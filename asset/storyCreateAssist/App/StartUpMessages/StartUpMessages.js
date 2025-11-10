import React, {useState} from "react";
import styles from './StartUpMessages.module.css'
import Compose from "../Compose";
import {v4 as uuidv4} from 'uuid'
import {useThreadContext} from "../../Context/ThreadProvider";
import {useQueryState} from "nuqs";

export default function StartUpMessages() {
  const {threadsData, messagesData} = useThreadContext();
  const {createThread} = threadsData;
  const {setMessages, saveMessages, createStory} = messagesData;
  const [text, setText] = useState('');
  const [threadId, setThreadId] = useQueryState('id');

  const createStoryHandler = () => {
    if (!text) {
      return;
    }

    createThread({
      id: threadId,
      messages: [],
      text,
    }).then(newThread => {
      const humanMessage = {
        id: uuidv4(),
        message: 'Создать историю',
        type: 'human',
      };
      setMessages(prevMessages => [...prevMessages, humanMessage]);
      saveMessages(threadId, humanMessage);

      createStory(threadId, text);
    });
  }

  return (
    <div className={styles.contentInner}>
      <div className={styles.messages}>
        <div className={styles.messagesInner}>
          <div style={{marginBottom: '32px', width: '70%'}}>
            <div
              style={{display: 'grid', gridTemplateColumns: 'repeat(2,minmax(0,1fr))', gap: '16px', width: '100%'}}>
              <div className={styles.actionItem} onClick={createStoryHandler}>
                <h3 className={styles.actionItemTitle}>Создать историю</h3>
              </div>
            </div>
          </div>
          <Compose setTextHandler={setText} startUp={true}/>
        </div>
      </div>
    </div>
  );
}
