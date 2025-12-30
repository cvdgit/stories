import React, {useRef, useState} from "react";
import styles from './StartUpMessages.module.css'
import Compose from "../Compose";
import {v4 as uuidv4} from 'uuid'
import {useThreadContext} from "../../Context/ThreadProvider";
import {useQueryState} from "nuqs";
import {CSSTransition} from "react-transition-group";
import TextEditorDialog from "../Editor/TextEditorDialog";
import TrainerSettingsDialog from "./TrainerSettingsDialog";

const mentalMaps = [
  {title: 'Ментальная карта', type: 'mental-map', create: true},
  {title: 'Ментальная карта (четные пропуски)', type: 'mental-map-even-fragments', create: true},
  {title: 'Ментальная карта (нечетные пропуски)', type: 'mental-map-odd-fragments', create: true},
  {title: 'Ментальная карта (план)', type: 'mental-map-plan', create: true},
  {title: 'План с накоплением', type: 'mental-map-plan-accumulation', create: true},
  {title: 'Пересказ', type: 'retelling', create: true},
];

export default function StartUpMessages() {
  const [settings, setSettings] = useState(mentalMaps);
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
  const [settingsOpen, setSettingsOpen] = useState(false);
  const settingsRef = useRef(null);

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
    const repetitionTrainer = settings.filter(s => s.create);
    if (!repetitionTrainer.length) {
      alert('Не выбрано содежимое истории');
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

      createRepetitionTrainerStory(threadId, text, repetitionTrainer);
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
              <div className={styles.actionItem} style={{display: 'flex', flexDirection: 'row', alignItems: 'center', gap: '10px'}}>
                <h3 onClick={createRepetitionTrainerStoryHandler} className={styles.actionItemTitle} style={{overflow: 'hidden', flex: '1 1 0%', textOverflow: 'ellipsis', whiteSpace: 'nowrap'}}>Создать историю с речевым тренажером</h3>
                <button title="Настройки речевого тренажера" className={styles.trashBtn} type="button" onClick={() => setSettingsOpen(true)}>
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="1.5"
                       stroke="currentColor" className={styles.trashSvg}>
                    <path strokeLinecap="round" strokeLinejoin="round"
                          d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z"/>
                    <path strokeLinecap="round" strokeLinejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>
          {text.length > 0 && <div style={{
            marginBottom: '20px',
            width: '70%',
            display: 'grid',
            gridTemplateColumns: 'repeat(2,minmax(0,1fr))',
            gap: '16px'
          }}>
            <div></div>
            <div className={styles.actionItem} style={{border: '0 none', backgroundColor: 'inherit', boxShadow: 'none'}}
                 onClick={() => setOpen(true)}>
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
        <CSSTransition
          in={settingsOpen}
          nodeRef={settingsRef}
          timeout={200}
          classNames="dialog"
          unmountOnExit
        >
          <TrainerSettingsDialog
            ref={settingsRef}
            mentalMaps={settings}
            saveSettings={setSettings}
            dialogProps={{addClassName: 'item-dialog'}}
            open={settingsOpen}
            setOpen={setSettingsOpen}
          />
        </CSSTransition>
      </div>
    </div>
  );
}
