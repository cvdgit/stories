import React from 'react'
import {useGlobal} from "../../context";
import "./ChatError.css";

export function ChatError() {
  const { currentChat, chat } = useGlobal();
  const chatError = chat[currentChat]?.error || {}
  return (
    <div className="chat-error">
      {chatError.code}<br />
      {chatError.message}<br />
      {chatError.type}<br />
      {chatError.param}<br />
    </div>
  )
}
