import React, {useState} from "react";
import styles from "./Messages.module.css";

export default function StoryMessage({message, haveContents, deleteStoryHandler, threadId}) {
  const [isHovering, setIsHovering] = useState(false);
  const {metadata} = message;

  const handleMouseEnter = () => {
    setIsHovering(true);
  };

  const handleMouseLeave = () => {
    setIsHovering(false);
  };

  const removeHandler = () => {
    if (!confirm('Подтверждаете?')) {
      return;
    }
    deleteStoryHandler(threadId, message.id, metadata.story.id);
  }

  return (
    <div className={styles.message}>
      <div onMouseEnter={handleMouseEnter} onMouseLeave={handleMouseLeave} style={{display: 'flex', flexDirection: 'column', rowGap: '10px'}}>
        {!metadata.story &&
          <div style={{marginBottom: '20px', maxHeight: '100px', overflowY: 'auto'}}>{message.message}</div>}
        {metadata.story && <>
          {!haveContents
            ? <h3 style={{margin: '0'}}>
              <div style={{
                display: 'flex',
                height: '30px',
                flexDirection: 'row',
                justifyContent: 'space-between',
                alignItems: 'center'
              }}>
                История:
                <div>
                {message.error && <span style={{color: 'rgb(239 68 68 / 1)'}}>{message.errorText}</span>}
                {isHovering &&
                  <button title="Удалить историю" onClick={removeHandler} className={styles.trashBtn}
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
              </div>
            </h3>
            : <h3>История:</h3>
          }
          <div style={{display: 'flex', flexDirection: 'row', columnGap: '10px'}}>
            <div>
              <img width="100" src={metadata.story.cover} alt="cover"/>
            </div>
            <div>
              <a style={{textDecoration: 'none', color: 'rgb(255 255 255 / 1)', lineHeight: '28px'}}
                 href={metadata.story.viewUrl}
                 target="_blank">{metadata.story.title}</a>
            </div>
          </div>
        </>}
      </div>
    </div>
  );
}
