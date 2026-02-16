import styles from "./Compose.module.css";
import React from "react";
import TextareaAutosize from "react-textarea-autosize";

export default function Compose({setTextHandler, startUp, composeHandler}) {
  return (
    <form className={`${styles.composeForm} ${startUp ? '' : styles.composeFormMessages}`}>
      <TextareaAutosize onChange={e => setTextHandler(e.target.value)} name="input" className={styles.composeInput} rows="1"
                        placeholder="Текст истории"/>
      <div style={{flexShrink: '0'}}>
        <button type="button" className={styles.compose} onClick={composeHandler}>
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"
               className="lucide lucide-send-horizontal">
            <path
              d="M3.714 3.048a.498.498 0 0 0-.683.627l2.843 7.627a2 2 0 0 1 0 1.396l-2.842 7.627a.498.498 0 0 0 .682.627l18-8.5a.5.5 0 0 0 0-.904z"></path>
            <path d="M6 12h16"></path>
          </svg>
        </button>
      </div>
    </form>
  )
}
