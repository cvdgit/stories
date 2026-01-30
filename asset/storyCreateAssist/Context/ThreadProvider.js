import {useThreads} from "../Hooks/useThreads";
import React, {createContext, useCallback, useContext, useEffect, useMemo, useRef, useState} from "react";
import {useQueryState} from "nuqs";
import {v4 as uuidv4} from 'uuid';
import api from "../Api";
import {fetchEventSource} from "@microsoft/fetch-event-source";
import {applyPatch} from "fast-json-patch";
import {createWordItem, getTextBySelections, hideWordsEven, hideWordsOdd} from "../../mental_map_quiz/words";

async function streamMessage(url, payload, onMessage, onEnd, onError) {
  let streamedResponse = {};
  let accumulatedMessage = '';
  await fetchEventSource(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Accept: 'text/event-stream',
      'X-CSRF-Token': document.querySelector('meta[name=csrf-token]').getAttribute('content')
    },
    body: JSON.stringify(payload),
    openWhenHidden: true,
    onerror(err) {
      console.error(err);
      onError && onError(err);
    },
    onmessage(msg) {
      if (msg.event === "end") {
        console.log("end")
        onEnd && onEnd(accumulatedMessage)
        return;
      }
      if (msg.event === "data" && msg.data) {
        const chunk = JSON.parse(msg.data);
        streamedResponse = applyPatch(
          streamedResponse,
          chunk.ops,
        ).newDocument;

        if (Array.isArray(streamedResponse?.streamed_output)) {
          accumulatedMessage = streamedResponse.streamed_output.join("");
        }

        onMessage(accumulatedMessage);
      }
    },
    onclose: () => {
      console.log('close')
    }
  });
}

const ThreadContext = createContext(undefined);

function processOutputAsJson(output) {
  let json = null
  try {
    json = JSON.parse(output.replace(/```json\n?|```/g, ''))
  } catch (ex) {
    console.log(ex.message)
  }
  return json
}

let saveTimeoutId;

export function ThreadProvider({children}) {
  const {
    isUserThreadsLoading,
    userThreads,
    getThreadById,
    deleteThread,
    createThread,
    getCurrentThreadTitle,
    setThreadTitle
  } = useThreads('123abc');
  const [isStreaming, setIsStreaming] = useState(false);
  const [messages, setMessages] = useState([]);
  const [_threadId, setThreadId] = useQueryState('id');
  const needSaveMessages = useRef(false);

  useEffect(() => {
    if (!_threadId) {
      setThreadId(uuidv4());
    }
  }, [_threadId]);

  const switchSelectedThread = useCallback((thread) => {
    setThreadId(thread.id);
    //streamStateRef.current = null;
    setIsStreaming(false);

    if (!thread.messages) {
      setMessages([]);
      return;
    }

    setMessages(thread.messages);
  }, [setThreadId]);

  useEffect(() => {
    if (!needSaveMessages.current) {
      return;
    }
    if (saveTimeoutId) {
      clearTimeout(saveTimeoutId);
    }

    needSaveMessages.current = false;
    saveTimeoutId = setTimeout(() => api.post('/admin/index.php?r=story-ai/save-thread', {
      id: _threadId,
      messages,
      title: 'Без имени'
    }, {'X-CSRF-Token': document.querySelector('meta[name=csrf-token]').getAttribute('content')}), 500);
    return () => clearTimeout(saveTimeoutId);
  }, [messages]);

  const saveMessages = (threadId) => {
    needSaveMessages.current = true;
  }

  const createRepetitionTrainerStoryFromText = async (threadId, id, payload) => {
    const data = {
      title: payload.title,
      fragments: payload.fragments.map(f => {
        const lines = []
        lines.push(`<p><h2>${f.fragmentTitle}</h2></p>`)
        f.sentences.map(s => {
          // <strong>${s.sentenceTitle}</strong><br/>
          lines.push(`<p>${s.sentenceText}</p>`)
        })
        return {
          id: f.id,
          text: lines.join('')
        };
      }),
      threadId,
      view: 'slide-repetition-trainer'
    }
    return await api.post('/admin/index.php?r=story-ai/create-story-handler', data, {
      'X-CSRF-Token': document.querySelector('meta[name=csrf-token]').getAttribute('content')
    })
  }

  const createStoryFromText = async (threadId, id, payload) => {
    const data = {
      title: payload.title,
      fragments: payload.fragments.map(f => ({id: f.id, text: f.fragmentText})),
      threadId
    }
    return await api.post('/admin/index.php?r=story-ai/create-story-handler', data, {
      'X-CSRF-Token': document.querySelector('meta[name=csrf-token]').getAttribute('content')
    })
  }

  const createStory = async (threadId, text) => {

    const messageId = uuidv4();
    setMessages(prevMessages => [...prevMessages, {
      id: messageId,
      message: '',
      type: 'ai-story',
      status: 'idle'
    }]);

    setIsStreaming(true);

    await streamMessage(
      '/admin/index.php?r=gpt/story/create',
      {threadId, text},
      (message) => {
        saveMessages(threadId);
        setMessages(prevMessages => prevMessages.map(m => {
          if (m.id === messageId) {
            return {...m, message};
          }
          return m;
        }));
      },
      async (accumulatedMessage) => {
        setIsStreaming(false);
        const json = processOutputAsJson(accumulatedMessage);

        saveMessages(threadId);

        setMessages(prevMessages => prevMessages.map(m => {
          if (m.id === messageId) {
            return {...m, status: 'done'};
          }
          return m;
        }));

        const payload = {...json, fragments: json.fragments.map(f => ({...f, id: uuidv4()}))};
        const storyMessageId = uuidv4();
        setMessages(prevMessages => [...prevMessages, {
          id: storyMessageId,
          message: 'Создание истории...',
          type: 'story',
          metadata: {payload},
        }]);

        const storyResponse = await createStoryFromText(threadId, storyMessageId, payload);
        if (!storyResponse.success) {
          saveMessages(threadId);
          setMessages(prevMessages => prevMessages.map(m => {
            if (m.id === storyMessageId) {
              return {...m, message: storyResponse.message};
            }
            return m;
          }));
          return;
        }

        saveMessages(threadId);
        setMessages(prevMessages => prevMessages.map(m => {
          if (m.id === storyMessageId) {
            return {...m, metadata: {...m.metadata, story: storyResponse.story}};
          }
          return m;
        }));

        setThreadTitle(threadId, storyResponse.story.title);

        await createReadingTrainer(threadId, {payload, story: storyResponse.story});
      },
      () => {
        setIsStreaming(false);
      }
    );
  };

  const createRepetitionTrainer = async (threadId, metadata) => {
    const messageId = uuidv4();
    setMessages(prevMessages => [...prevMessages, {
      id: messageId,
      message: '',
      type: 'repetition_trainer',
      metadata: {}
    }]);
    saveMessages(threadId);

    const {payload: json, story, repetitionTrainer} = metadata;
    const {id: storyId, slideMap} = story;

    const slideState = slideMap.map(({slideId}) => ({slideId, status: 'process'}))

    saveMessages(threadId);
    setMessages(prevMessages => prevMessages.map(m => {
      if (m.id === messageId) {
        return {...m, metadata: {...m.metadata, slides: slideState, storyId}};
      }
      return m;
    }));

    const slideRequests = [];
    slideMap.map(({fragmentId, slideId}) => {

      const contents = [...repetitionTrainer].map(({title, type, required}) => ({title, type, required, fragments: []}));

      const slideFragment = json.fragments.find(({id}) => id === fragmentId);
      const textFragments = [];
      const fragments = slideFragment.sentences.map(({sentenceText, sentenceTitle}) => {
        const fragmentId = uuidv4();
        textFragments.push(sentenceText);
        return {
          id: fragmentId,
          sentenceText,
          sentenceTitle,
          words: createWordItem(sentenceText, fragmentId).words
        }
      });

      for (let i = 0; i < contents.length; i++) {
        const type = contents[i].type
        switch (type) {
          case 'mental-map':
            structuredClone(fragments).map(f => contents[i].fragments.push({
              id: f.id,
              title: getTextBySelections(f.words)
            }))
            break;
          case 'mental-map-even-fragments':
            structuredClone(fragments).map(f => contents[i].fragments.push({
              id: f.id,
              title: hideWordsEven(f.words)
            }))
            break;
          case 'mental-map-odd-fragments':
            structuredClone(fragments).map(f => contents[i].fragments.push({
              id: f.id,
              title: hideWordsOdd(f.words)
            }))
            break;
          case 'mental-map-plan':
            structuredClone(fragments).map(({id, sentenceText, sentenceTitle}) => contents[i].fragments.push({
              id,
              title: sentenceTitle,
              description: sentenceText
            }))
            break;
          case 'mental-map-plan-accumulation':
            structuredClone(fragments).map(({id, sentenceText, sentenceTitle}) => contents[i].fragments.push({
              id,
              title: sentenceTitle,
              description: sentenceText
            }))
            break;
        }
      }

      const data = {
        storyId,
        slideId,
        contents,
        text: textFragments.join("\r\n")
      };

      slideRequests.push(
        api.post('/admin/index.php?r=story-ai/create-slide-content-handler', data, {
          'X-CSRF-Token': document.querySelector('meta[name=csrf-token]').getAttribute('content')
        }).then(response => {

          saveMessages(threadId);
          setMessages(prevMessages => prevMessages.map(m => {
            if (m.id === messageId) {
              return {
                ...m, metadata: {
                  ...m.metadata, slides: m.metadata.slides.map(s => {
                    if (s.slideId === response.slideId) {
                      return {...s, status: 'done'};
                    }
                    return s;
                  })
                }
              };
            }
            return m;
          }));

        })
      )
    })

    Promise.all(slideRequests).then(() => {
      const planPayload = {
        storyId,
        fragments: json.fragments.map(f => {
          return {
            id: f.id,
            title: f.fragmentTitle,
            description: f.sentences.map(({sentenceText}) => sentenceText).join("\r\n")
          };
        })
      }
      api.post('/admin/index.php?r=story-ai/create-final-mental-map-handler', planPayload, {
        'X-CSRF-Token': document.querySelector('meta[name=csrf-token]').getAttribute('content')
      });
    })
  }

  const deleteRepetitionTrainer = async (threadId, messageId, storyId) => {
    const response = await api.post('/admin/index.php?r=story-ai/remove-trainer', {storyId}, {
      'X-CSRF-Token': document.querySelector('meta[name=csrf-token]').getAttribute('content')
    })
    if (response.success) {
      saveMessages(threadId);
      setMessages(prevMessages => prevMessages.filter(m => m.id !== messageId));
    }
  }

  const createStoryByFragments = async (threadId, fragments) => {

    saveMessages(threadId);

    const payload = {
      title: 'Название истории',
      fragments: fragments.map(fragmentText => ({fragmentText, id: uuidv4()}))
    };

    const storyMessageId = uuidv4();
    setMessages(prevMessages => [...prevMessages, {
      id: storyMessageId,
      message: 'Создание истории...',
      type: 'story',
      metadata: {payload},
    }]);

    const storyResponse = await createStoryFromText(threadId, storyMessageId, payload);
    if (!storyResponse.success) {
      saveMessages(threadId);
      setMessages(prevMessages => prevMessages.map(m => {
        if (m.id === storyMessageId) {
          return {...m, message: storyResponse.message};
        }
        return m;
      }));
      return;
    }

    saveMessages(threadId);
    setMessages(prevMessages => prevMessages.map(m => {
      if (m.id === storyMessageId) {
        return {...m, metadata: {...m.metadata, story: storyResponse.story}};
      }
      return m;
    }));

    setThreadTitle(threadId, storyResponse.story.title);

    await createReadingTrainer(threadId, {payload, story: storyResponse.story});
  }

  const createRepetitionTrainerStory = async (threadId, text, repetitionTrainer) => {
    const messageId = uuidv4();
    setMessages(prevMessages => [...prevMessages, {
      id: messageId,
      message: '',
      type: 'ai',
    }]);
    try {

      setIsStreaming(true);
      await streamMessage(
        '/admin/index.php?r=gpt/story/create-for-trainer',
        {threadId, text},
        (message) => {
          saveMessages(threadId);
          setMessages(prevMessages => prevMessages.map(m => {
            if (m.id === messageId) {
              return {...m, message};
            }
            return m;
          }));
        },
        async (accumulatedMessage) => {
          setIsStreaming(false);
          const json = JSON.parse(accumulatedMessage);

          saveMessages(threadId);

          const payload = {...json, fragments: json.fragments.map(f => ({...f, id: uuidv4()}))};
          const storyMessageId = uuidv4();
          setMessages(prevMessages => [...prevMessages, {
            id: storyMessageId,
            message: 'Создание истории...',
            type: 'story',
            metadata: {payload},
          }]);

          const storyResponse = await createRepetitionTrainerStoryFromText(threadId, storyMessageId, payload);
          if (!storyResponse.success) {
            saveMessages(threadId);
            setMessages(prevMessages => prevMessages.map(m => {
              if (m.id === storyMessageId) {
                return {...m, message: storyResponse.message};
              }
              return m;
            }));
            return;
          }

          saveMessages(threadId);
          setMessages(prevMessages => prevMessages.map(m => {
            if (m.id === storyMessageId) {
              return {...m, metadata: {...m.metadata, story: storyResponse.story}};
            }
            return m;
          }));

          setThreadTitle(threadId, storyResponse.story.title);

          await createRepetitionTrainer(threadId, {
            payload,
            story: storyResponse.story,
            repetitionTrainer
          });
        },
        () => {
          setIsStreaming(false);
        }
      );

    } catch (err) {
      console.error(err);
      setIsStreaming(false);
    } finally {}
  }

  const createReadingTrainer = async (threadId, metadata) => {

    const messageId = uuidv4();

    setMessages(prevMessages => [...prevMessages, {
      id: messageId,
      message: '',
      type: 'reading_trainer',
      metadata: {}
    }]);
    saveMessages(threadId);

    const {payload: json, story} = metadata;
    const {id: storyId, slideMap} = story;

    const slideState = slideMap.map(({slideId}) => ({slideId, status: 'process'}));

    saveMessages(threadId);
    setMessages(prevMessages => prevMessages.map(m => {
      if (m.id === messageId) {
        return {...m, metadata: {...m.metadata, slides: slideState, storyId}};
      }
      return m;
    }));

    const requests = [];
    slideMap.map(({fragmentId, slideId}) => {

      const contents = {title: 'Ментальная карта', type: 'mental-map', fragments: []};

      const slideFragment = json.fragments.find(({id}) => id === fragmentId);

      requests.push(
        streamMessage(
          '/admin/index.php?r=gpt/mental-map/text-fragments',
          {text: slideFragment.fragmentText},
          () => {
          },
          (fragmentsJsonText) => {

            let fragments = []
            try {
              fragments = processOutputAsJson(fragmentsJsonText).map(text => {
                const fragmentId = uuidv4();
                return {
                  id: fragmentId,
                  title: text,
                }
              });
            } catch (ex) {
              throw new Error(ex.message);
            }

            const data = {
              storyId,
              slideId,
              mentalMap: {...contents, fragments},
              text: slideFragment.fragmentText
            };
            api.post('/admin/index.php?r=story-ai/create-slide-reading-handler', data, {
              'X-CSRF-Token': document.querySelector('meta[name=csrf-token]').getAttribute('content')
            }).then(response => {

              saveMessages(threadId);
              setMessages(prevMessages => prevMessages.map(m => {
                if (m.id === messageId) {
                  return {
                    ...m, metadata: {
                      ...m.metadata, slides: m.metadata.slides.map(s => {
                        if (s.slideId === response.slideId) {
                          return {...s, status: 'done'};
                        }
                        return s;
                      })
                    }
                  };
                }
                return m;
              }));
            });
          }
        )
      )
    })

    Promise
      .all(requests)
      .then(values => {
        console.log('all', values);
      })
  }

  const contextValue = useMemo(() => ({
    threadsData: {
      isUserThreadsLoading,
      userThreads,
      getThreadById,
      deleteThread,
      createThread,
      getCurrentThreadTitle
    },
    messagesData: {
      isStreaming,
      messages,
      setMessages,
      createStory,
      createStoryByFragments,
      createRepetitionTrainerStory,
      createRepetitionTrainer,
      deleteRepetitionTrainer,
      createReadingTrainer,
      switchSelectedThread,
      saveMessages,
    }
  }), [
    isUserThreadsLoading,
    userThreads,
    getThreadById,
    deleteThread,
    createThread,
    getCurrentThreadTitle,
    isStreaming,
    messages,
    setMessages,
    saveMessages,
    createStory,
    createStoryByFragments,
    createRepetitionTrainerStory,
    createRepetitionTrainer,
    deleteRepetitionTrainer,
    createReadingTrainer,
    switchSelectedThread
  ]);

  return (
    <ThreadContext.Provider value={contextValue}>
      {children}
    </ThreadContext.Provider>
  );
}

export function useThreadContext() {
  const context = useContext(ThreadContext);
  if (context === undefined) {
    throw new Error('useThreadContext must be used within a ThreadProvider');
  }
  return context;
}
