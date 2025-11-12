import React from "react";
import styles from "./Messages.module.css";

export default function StoryMessage({message, haveRepetitionTrainer, createReadingTrainer, threadId}) {
  const {metadata} = message;

  return (
    <div className={styles.message}>
      <div style={{display: 'flex', flexDirection: 'column', rowGap: '10px'}}>
        {!metadata.story &&
          <div style={{marginBottom: '20px', maxHeight: '100px', overflowY: 'auto'}}>{message.message}</div>}
        {metadata.story && <>
          <h3>История:</h3>
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
