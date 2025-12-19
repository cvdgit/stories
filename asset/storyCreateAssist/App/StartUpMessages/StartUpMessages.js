import React, {useRef, useState} from "react";
import styles from './StartUpMessages.module.css'
import Compose from "../Compose";
import {v4 as uuidv4} from 'uuid'
import {useThreadContext} from "../../Context/ThreadProvider";
import {useQueryState} from "nuqs";
import {CSSTransition} from "react-transition-group";
import TextEditorDialog from "../Editor/TextEditorDialog";

export default function StartUpMessages() {
  const {threadsData, messagesData} = useThreadContext();
  const {createThread} = threadsData;
  const {
    setMessages,
    saveMessages,
    createStory,
    createRepetitionTrainerStory,
    createStoryByFragments
  } = messagesData;
  const [text, setText] = useState('');
  const [threadId, setThreadId] = useQueryState('id');
  const [open, setOpen] = useState(false);
  const ref = useRef(null);

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
        message: 'Создать историю для чтения',
        type: 'human_create_story',
        metadata: {text}
      };
      setMessages(prevMessages => [...prevMessages, humanMessage]);
      saveMessages(threadId, humanMessage);

      createStory(threadId, text);
    });
  }

  const createStoryFromEditorHandler = fragments => {
    if (!fragments.length) {
      return;
    }

    createThread({
      id: threadId,
      messages: [],
      text: fragments.join('\n\n'),
    }).then(newThread => {
      const humanMessage = {
        id: uuidv4(),
        message: 'Создать историю для чтения',
        type: 'human_create_story',
        metadata: {text}
      };
      setMessages(prevMessages => [...prevMessages, humanMessage]);
      saveMessages(threadId, humanMessage);

      createStoryByFragments(threadId, fragments);
    });
  }

  const createRepetitionTrainerStoryHandler = () => {
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
        message: 'Создать историю с речевым тренажером',
        type: 'human_create_story',
        metadata: {text}
      };
      setMessages(prevMessages => [...prevMessages, humanMessage]);
      saveMessages(threadId, humanMessage);

      createRepetitionTrainerStory(threadId, text);
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
                <h3 className={styles.actionItemTitle}>Создать историю для чтения</h3>
              </div>
              <div className={styles.actionItem} onClick={createRepetitionTrainerStoryHandler}>
                <h3 className={styles.actionItemTitle}>Создать историю с речевым тренажером</h3>
              </div>
            </div>
          </div>
          {text.length > 0 && <div style={{marginBottom: '20px', width: '70%', display: 'grid', gridTemplateColumns: 'repeat(2,minmax(0,1fr))', gap: '16px'}}>
            <div></div>
            <div className={styles.actionItem} style={{border: '0 none', backgroundColor: 'inherit', boxShadow: 'none'}} onClick={() => setOpen(true)}>
              <h3 className={styles.actionItemTitle} style={{textAlign: 'right'}}>Редактор текста</h3>
            </div>
          </div>}
          <Compose setTextHandler={setText} startUp={true}/>
        </div>
      </div>

      <div>
        <CSSTransition
          in={open}
          nodeRef={ref}
          timeout={200}
          classNames="dialog"
          unmountOnExit
        >
          <TextEditorDialog
            ref={ref}
            dialogProps={{addClassName: 'item-dialog'}}
            open={open}
            text={text}
            createStoryFromEditorHandler={createStoryFromEditorHandler}
            setOpen={setOpen}
          />
        </CSSTransition>
      </div>
    </div>
  );
}
