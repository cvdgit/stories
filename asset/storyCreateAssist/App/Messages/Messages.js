import React, {useEffect, useRef, useState} from "react";
import styles from "./Messages.module.css";
import Compose from "../Compose";
import StoryMessage from "./StoryMessage";
import RepetitionTrainerMessage from "./RepetitionTrainerMessage";
import {useThreadContext} from "../../Context/ThreadProvider";
import {useQueryState} from "nuqs";
import ReadingTrainerMessage from "./ReadingTrainerMessage";
import CreateStoryMessage from "./CreateStoryMessage";
import StoryFragmentsMessage from "./StoryFragmentsMessage";
import ReadingStoryFragmentsMessage from "./ReadingStoryFragmentsMessage";
import RawTextMessage from "./MessageType/RawTextMessage";

export default function Messages({messages}) {
  const {threadsData, messagesData} = useThreadContext();
  const {getCurrentThreadTitle} = threadsData;
  const {
    createRepetitionTrainer,
    deleteRepetitionTrainer,
    createReadingTrainer,
    setMessages,
    saveMessages,
  } = messagesData;
  const [text, setText] = useState('');
  const [threadId, setThreadId] = useQueryState('id');
  const ref = useRef(null);
  const messagesRef = useRef(null);

  useEffect(() => {
    if (!messagesRef.current) {
      return;
    }
    messagesRef.current.scrollTop = messagesRef.current.scrollHeight
  }, [messages])

  function renderMessage(message) {
    switch (message.type) {
      case 'raw-text': {
        return <RawTextMessage
          threadId={threadId}
          message={message}
          speechTrainer
        />;
      }

      case 'story': {
        return <StoryMessage
          haveRepetitionTrainer={messages.find(m => m.type === 'repetition_trainer')}
          message={message}
          createReadingTrainer={createReadingTrainer}
          threadId={threadId}
        />
      }
      case 'repetition_trainer': {
        return <RepetitionTrainerMessage
          message={message}
          deleteRepetitionTrainer={deleteRepetitionTrainer}
          threadId={threadId}
        />
      }
      case 'reading_trainer': {
        return <ReadingTrainerMessage
          message={message}
          deleteRepetitionTrainer={deleteRepetitionTrainer}
          threadId={threadId}
        />
      }
      case 'human_create_story': {
        return <CreateStoryMessage message={message} />
      }
      case 'story_as_tree': {
        return <StoryFragmentsMessage message={message} threadId={threadId} />
      }
      case 'reading-story-as-fragments': {
        return <ReadingStoryFragmentsMessage message={message} />
      }
      default: {
        return (
          <div className={styles.message}>
            <div ref={ref} style={{maxHeight: '100px', overflowY: 'auto', position: 'relative', whiteSpace: 'pre-wrap'}}>
              {message.message}
            </div>
            {message.status === 'idle' && <div style={{color: 'oklch(0.6 0 0)', fontSize: '12px', lineHeight: '16px', display: 'flex', flexDirection: 'row', gap: '10px', alignItems: 'center'}}>
              Генерация ответа
              <svg style={{width: '32px'}} fill="hsl(228, 97%, 42%)" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <circle cx="4" cy="12" r="3" opacity="1">
                  <animate id="spinner_qYjJ" begin="0;spinner_t4KZ.end-0.25s" attributeName="opacity" dur="0.75s"
                           values="1;.2" fill="freeze"/>
                </circle>
                <circle cx="12" cy="12" r="3" opacity=".4">
                  <animate begin="spinner_qYjJ.begin+0.15s" attributeName="opacity" dur="0.75s" values="1;.2"
                           fill="freeze"/>
                </circle>
                <circle cx="20" cy="12" r="3" opacity=".3">
                  <animate id="spinner_t4KZ" begin="spinner_qYjJ.begin+0.3s" attributeName="opacity" dur="0.75s"
                           values="1;.2" fill="freeze"/>
                </circle>
              </svg>
            </div>}
          </div>
        )
      }
    }
  }

  return (
    <div className={styles.container}>
      <div className={styles.messagesWrap} ref={messagesRef}>
        <div className={styles.messagesInner}>
          <div className={styles.messagesHeader}>
            <div className={styles.header}>
              <p style={{margin: '8px 0', whiteSpace: 'pre-line'}}>{getCurrentThreadTitle(threadId)}</p>
            </div>
          </div>
          {messages.map((message, i) => (
            <div key={i} className={styles.messagesContainer}>
              {renderMessage(message)}
            </div>
          ))}
        </div>
      </div>
      <Compose setTextHandler={setText}/>
    </div>
  )
}
