export const initState = {
  chat: [],
  options: {
    account: {
      name: "CHAT——AI",
      avatar: "",
    },
    general: {
      language: "English",
      theme: "light",
      command: "COMMAND_ENTER",
      size: "normal",
    },
    openai: {},
  },
  is: {
    typing: false,
    config: false,
    fullScreen: true,
    sidebar: true,
    inputting: false,
    thinking: false,
    apps: true,
  },
  typingMessage: {},
};
