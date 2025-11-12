import React, {useState} from "react";
import styles from "./Messages.module.css";
import Compose from "../Compose";
import StoryMessage from "./StoryMessage";
import RepetitionTrainerMessage from "./RepetitionTrainerMessage";
import {useThreadContext} from "../../Context/ThreadProvider";
import {useQueryState} from "nuqs";
import ReadingTrainerMessage from "./ReadingTrainerMessage";

export default function Messages({messages}) {
  const {threadsData, messagesData} = useThreadContext();
  const {getCurrentThreadTitle} = threadsData;
  const {createRepetitionTrainer, deleteRepetitionTrainer, createReadingTrainer} = messagesData;
  const [text, setText] = useState('');
  const [threadId, setThreadId] = useQueryState('id');

  function renderMessage(message) {
    switch (message.type) {
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
      default: {
        return (
          <div className={styles.message}>
            <div style={{marginBottom: '20px', maxHeight: '100px', overflowY: 'auto'}}>{message.message}</div>
          </div>
        )
      }
    }
  }

  return (
    <div className={styles.container}>
      <div className={styles.messagesWrap}>
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
