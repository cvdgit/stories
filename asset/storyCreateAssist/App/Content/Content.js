import React from "react";
import styles from './Content.module.css';
import Messages from "../Messages";
import StartUpMessages from "../StartUpMessages";

export default function Content({messages}) {
  return (
    <div className={styles.content}>
      {messages.length
          ? <Messages messages={messages}/>
          : <StartUpMessages/>}
    </div>
  )
}
