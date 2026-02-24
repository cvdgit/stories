import React, {useState} from "react";
import styles from "./Messages.module.css";
import {useThreadContext} from "../../Context/ThreadProvider";

export default function ReadingTrainerMessage({message, deleteRepetitionTrainer, threadId}) {
  const {messagesData} = useThreadContext();
  const {createReadingTrainerSlide} = messagesData;
  const [isHovering, setIsHovering] = useState(false);
  const {metadata} = message;
  const {slides} = metadata;

  const handleMouseEnter = () => {
    setIsHovering(true);
  };

  const handleMouseLeave = () => {
    setIsHovering(false);
  };

  const removeHandler = () => {
    /*if (!confirm('Подтверждаете?')) {
      return;
    }
    deleteRepetitionTrainer(threadId, message.id, metadata.storyId);*/
  }

  const slideRequestHandler = async (slideId) => {
    try {
      await createReadingTrainerSlide(
        threadId,
        message.id,
        metadata.storyId,
        slideId
      );
    } catch (ex) {
      alert(ex.message);
    }
  }

  return (
    <div className={styles.message} style={{width: '100%'}}>
      <div style={{display: 'flex', flexDirection: 'column', rowGap: '10px', width: '100%'}}>
        <div className={styles.messageWrap} onMouseEnter={handleMouseEnter} onMouseLeave={handleMouseLeave}>
          <h3 style={{margin: '0'}}>
            <div style={{
              display: 'flex',
              height: '30px',
              flexDirection: 'row',
              justifyContent: 'space-between',
              alignItems: 'center'
            }}>
              Тренажер для чтения:
              {isHovering &&
                <button title="Удалить речевой тренажер" onClick={removeHandler} className={styles.trashBtn}
                        type="button">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                       stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"
                       className={styles.trashSvg}>
                    <path d="M3 6h18"></path>
                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                    <line x1="10" x2="10" y1="11" y2="17"></line>
                    <line x1="14" x2="14" y1="11" y2="17"></line>
                  </svg>
                </button>}
            </div>
          </h3>
          {slides.map(({slideId, status}, j) =>
            <div style={{display: 'flex', flexDirection: 'row', gap: '10px'}} key={j}>
              <div>Слайд #{j + 1}</div>
                {status === 'process' &&
                  <div style={{width: '30px'}}>
                    <svg fill="hsl(228, 97%, 42%)" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                      <circle cx="4" cy="12" r="3" opacity="1">
                        <animate id="spinner_qYjJ" begin="0;spinner_t4KZ.end-0.25s" attributeName="opacity" dur="0.75s"
                                 values="1;.2" fill="freeze"/>
                      </circle>
                      <circle cx="12" cy="12" r="3" opacity=".4">
                        <animate begin="spinner_qYjJ.begin+0.15s" attributeName="opacity" dur="0.75s" values="1;.2"
                                 fill="freeze"/>
                      </circle>
                      <circle cx="20" cy="12" r="3" opacity=".3">
                        <animate id="spinner_t4KZ" begin="spinner_qYjJ.begin+0.3s" attributeName="opacity" dur="0.75s"
                                 values="1;.2" fill="freeze"/>
                      </circle>
                    </svg>
                  </div>
                }
              {status === 'error' && <div style={{color: 'red'}}>Ошибка - <button onClick={() => slideRequestHandler(slideId)} type="button">Повторить</button></div>}
            </div>
          )}
        </div>
      </div>
    </div>
  )
}
