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
  } = messagesData;
  const [text, setText] = useState('');
  const [threadId, setThreadId] = useQueryState('id');

  const [open, setOpen] = useState(false);
  const ref = useRef(null);

  const composeHandler = async () => {
    if (!text) {
      return;
    }

    const thread = await createThread({
      id: threadId,
      messages: [],
      text,
    });

    setMessages(prevMessages => [...prevMessages, {
      id: uuidv4(),
      message: text,
      type: 'raw-text',
      metadata: {text}
    }]);

    saveMessages(threadId);
  }

  return (
    <div className={styles.contentInner}>
      <div className={styles.messages}>
        <div className={styles.messagesInner}>
          <Compose setTextHandler={setText} startUp={true} composeHandler={composeHandler}/>
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
            createStoryFromEditorHandler={() => {}}
            createSpeechTrainerFromEditorHandler={() => {}}
            setOpen={setOpen}
          />
        </CSSTransition>
      </div>
    </div>
  );
}
