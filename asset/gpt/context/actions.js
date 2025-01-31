import {fetchStream} from "../openai";
import {uuidv4} from "../utils";


export default function action(state, dispatch) {
  const setState = (payload = {}) => dispatch({
    type: "SET_STATE",
    payload: {...payload},
  });

  const saveConversations = async (action, state) => {
    const response = await fetch(`/admin/index.php?r=gpt/chat/conversations`, {
      method: "POST",
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        action,
        payload: state
      }),
    });
    console.log(await response.json());
  }

  const saveMessages = async (action, state) => {
    const response = await fetch(`/admin/index.php?r=gpt/chat/messages`, {
      method: "POST",
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        action,
        payload: state
      }),
    });
    console.log(await response.json());
  }

  return {

    setState,

    clearTyping() {
      console.log("clear");
      setState({typingMessage: {}});
    },

    async sendMessage() {

      const {typingMessage, options, chat, is, currentChat} = state;

      if (typingMessage?.content) {

        const newMessage = {
          ...typingMessage,
          sentTime: Date.now(),
        };

        const messages = [...chat[currentChat].messages, newMessage];

        let newChat = [...chat];
        newChat.splice(currentChat, 1, {...chat[currentChat], messages});

        setState({
          is: {...is, thinking: true},
          typingMessage: {},
          chat: newChat,
        });

        saveMessages("new", {...newMessage, conversation_id: chat[currentChat].id});

        const controller = new AbortController();

        try {
          const messId = uuidv4()
          const res = await fetchStream({
            messages: messages.map((item) => {
              const {sentTime, id, ...rest} = item;
              return {...rest};
            }),
            options: options.openai,
            signal: controller.signal,
            onMessage(content) {

              newChat.splice(currentChat, 1, {
                ...chat[currentChat],
                messages: [
                  ...messages,
                  {
                    content,
                    role: "assistant",
                    sentTime: Date.now(),
                    id: messId,
                  },
                ],
              });

              setState({
                is: {...is, thinking: content.length},
                chat: newChat,
              });
            },
            onEnd(content) {
              setState({
                is: {...is, thinking: false},
              });
              saveMessages("new", {
                content,
                role: "assistant",
                sentTime: Date.now(),
                id: messId,
                conversation_id: chat[currentChat].id
              })
            },
            onError(res) {
              console.log(res);
              const {error} = res || {};
              if (error) {
                newChat.splice(currentChat, 1, {
                  ...chat[currentChat],
                  error,
                });
                setState({
                  chat: newChat,
                  is: {...is, thinking: false},
                });
              }
            },
          });
          console.log(res);
        } catch (error) {
          console.log(error);
        }
      }
    },

    newChat() {
      const {chat} = state;
      const newChat = {
        title: "Новый разговор",
        id: uuidv4(),
        messages: [],
        ct: Date.now(),
        icon: [1, "files"],
      };
      const chatList = [
        ...chat,
        newChat,
      ];
      setState({chat: chatList, currentChat: chatList.length - 1});

      saveConversations("new", newChat);
    },

    modifyChat(arg, index) {
      const chat = [...state.chat];
      chat.splice(index, 1, {...chat[index], ...arg});
      setState({chat, currentEditor: null});

      saveConversations("modify", chat[index]);
    },

    editChat(index, title) {
      const chat = [...state.chat];
      chat.splice(index, 1, [...chat[index], title]);
      setState({
        chat,
      });
    },

    removeChat(index) {
      const chat = [...state.chat];
      const elem = chat.splice(index, 1);
      const payload =
        state.currentChat === index
          ? {chat, currentChat: index - 1}
          : {chat};
      setState({
        ...payload,
      });

      saveConversations("remove", {id: elem[0].id});
    },

    setMessage(content) {
      const typingMessage =
        content === ""
          ? {}
          : {
            role: "user",
            content,
            id: uuidv4(),
          };
      setState({is: {...state.is, typing: true}, typingMessage});
    },

    clearMessage() {
      const chat = [...state.chat];
      chat[state.currentChat].messages = [];
      setState({
        chat,
      });
    },

    removeMessage(index) {
      const messages = [...state.chat[state.currentChat].messages];
      const chat = [...state.chat];
      const removedMessage = messages.splice(index, 1);
      chat[state.currentChat].messages = messages;
      setState({
        chat,
      });
      console.log(messages, chat, removedMessage)
      saveMessages("remove", {
        id: removedMessage[0].id,
        conversation_id: chat[state.currentChat].id
      });
    },

    setOptions({type, data = {}}) {
      console.log(type, data);
      let options = {...state.options};
      options[type] = {...options[type], ...data};
      setState({options});
    },

    setIs(arg) {
      const {is} = state;
      setState({is: {...is, ...arg}});
    },

    currentList() {
      return state.chat[state.currentChat];
    },

    stopResponse() {
      setState({
        is: {...state.is, thinking: false},
      });
    },
  };
}
