import React, {useRef, useState} from "react";
import styles from "../Messages.module.css";
import {MarkdownText} from "../../../components/MarkdownText";
import markdownit from "markdown-it";
import markdownAstToTree, {findFirstHeader} from "../../../lib/markdownAstToTree";
import {useThreadContext} from "../../../Context/ThreadProvider";
import storyPayloadBuilder from "./storyPayloadBuilder";
import TrainerSettingsDialog from "../../StartUpMessages/TrainerSettingsDialog";
import {CSSTransition} from "react-transition-group";

const mentalMaps = [
  {title: 'Ментальная карта', type: 'mental-map', create: true, required: false},
  {title: 'Ментальная карта (четные пропуски)', type: 'mental-map-even-fragments', create: true, required: false},
  {title: 'Ментальная карта (нечетные пропуски)', type: 'mental-map-odd-fragments', create: true, required: false},
  {title: 'Ментальная карта (план)', type: 'mental-map-plan', create: true, required: false},
  {title: 'План с накоплением', type: 'mental-map-plan-accumulation', create: true, required: false},
  {title: 'Пересказ', type: 'retelling', create: true, required: false},
];

export default function RawTextMessage({message, threadId}) {
  const {messagesData} = useThreadContext();
  const {
    createStoryForReading,
    createStoryForSpeechTrainer,
    setMessages,
    splitTextForReading,
    splitTextForSpeechTrainer,
  } = messagesData;
  const {metadata, id: messageId} = message;
  const [settingsOpen, setSettingsOpen] = useState(false);
  const settingsRef = useRef(null);
  const [settings, setSettings] = useState(mentalMaps);

  const createStoryForReadingHandler = async () => {
    const {markdown} = metadata;
    const md = markdownit();
    const ast = md.parse(markdown);
    const tree = markdownAstToTree(ast);

    setMessages(prevMessages => prevMessages.map(m => {
      if (m.id === messageId) {
        return {...m, withStory: true};
      }
      return m;
    }));

    await createStoryForReading(
      threadId,
      findFirstHeader(tree) || 'Название истории',
      storyPayloadBuilder(tree)
    );
  };

  const createStoryForSpeechTrainerHandler = async () => {

    const {markdown} = metadata;
    const md = markdownit();
    const ast = md.parse(markdown);
    const tree = markdownAstToTree(ast);

    const repetitionTrainer = settings.filter(s => s.create);
    if (!repetitionTrainer.length) {
      alert('Не выбрано содержимое истории');
      return;
    }

    setMessages(prevMessages => prevMessages.map(m => {
      if (m.id === messageId) {
        return {...m, withStory: true};
      }
      return m;
    }));

    await createStoryForSpeechTrainer(
      threadId,
      findFirstHeader(tree) || 'Название истории',
      storyPayloadBuilder(tree),
      repetitionTrainer
    );
  };

  const breakUpTextForReadingHandler = async ({id, metadata}) => {
    await splitTextForReading(threadId, metadata.text, id);
  }

  const breakUpTextForSpeechTrainerHandler = async ({id, metadata}) => {
    await splitTextForSpeechTrainer(threadId, metadata.text, id);
  }

  const breakUpTextMarkdown = async () => {
    const {text: markdown} = metadata;
    setMessages(prevMessages => prevMessages.map(m => {
      if (m.id === messageId) {
        return {...m, divided: true, status: 'done', metadata: {...m.metadata, markdown}};
      }
      return m;
    }));
  };

  return <div className={styles.message}>
    <div style={{display: 'flex', flexDirection: 'column', rowGap: '10px'}}>
      <div>
        <MarkdownText>{message.message}</MarkdownText>
      </div>
      <div>
        <div style={{marginBottom: '10px'}}>
          {(!message.divided && message.status !== 'idle') && <div
            style={{display: 'grid', gridTemplateColumns: 'repeat(2,minmax(0,1fr))', gap: '16px', width: '100%'}}>
            <div className={styles.actionItem} onClick={() => breakUpTextForReadingHandler(message)}>
              <h3 className={styles.actionItemTitle}>Разбить для чтения</h3>
            </div>
            <div className={styles.actionItem} onClick={() => breakUpTextForSpeechTrainerHandler(message)}>
              <h3 className={styles.actionItemTitle}>Разбить для речевого тренажера</h3>
            </div>
            <div className={styles.actionItem} onClick={breakUpTextMarkdown}>
              <h3 className={styles.actionItemTitle}>Разбить Markdown</h3>
            </div>
          </div>}

          {(message.divided && !message.withStory) && <div style={{display: 'flex', gap: '20px'}}>
            <div className={styles.actionItem} onClick={createStoryForReadingHandler}>
              <h3 className={styles.actionItemTitle}>Создать историю для чтения</h3>
            </div>
            <div className={styles.actionItem} style={{
              display: 'flex',
              flexDirection: 'row',
              alignItems: 'center',
              gap: '10px',
              paddingRight: '10px'
            }}>
              <h3 onClick={createStoryForSpeechTrainerHandler} className={styles.actionItemTitle}
                  style={{overflow: 'hidden', flex: '1 1 0%', textOverflow: 'ellipsis', whiteSpace: 'nowrap'}}>Создать
                историю с речевом тренажером</h3>
              <button title="Настройки речевого тренажера" className={styles.settingsBtn} type="button"
                      onClick={() => setSettingsOpen(true)}>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="1.5"
                     stroke="currentColor" className={styles.settingsSvg}>
                  <path strokeLinecap="round" strokeLinejoin="round"
                        d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z"/>
                  <path strokeLinecap="round" strokeLinejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                </svg>
              </button>

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
          </div>}
        </div>
      </div>
    </div>
  </div>
}
