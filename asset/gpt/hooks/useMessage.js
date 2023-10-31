import {useState, useEffect} from "react";
import {useGlobal} from "../context";

export function useMessage() {
  const {currentChat, chat, is} = useGlobal();
  const [message, setMessage] = useState({messages: []});
  useEffect(() => {
    if (chat.length) {
      setMessage(chat[currentChat]);
    }
  }, [chat, is?.thinking, currentChat]);
  return {message};
}
