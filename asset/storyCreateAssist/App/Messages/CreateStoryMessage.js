import React, {useState} from "react";
import styles from "./Messages.module.css";

export default function CreateStoryMessage({message}) {
  const [showText, setShowText] = useState(false);
  const toggleTextHandler = () => {
    setShowText(!showText);
  };
  return (
    <div className={styles.message}>
      <div style={{cursor: 'pointer'}} onClick={toggleTextHandler}>
        {message.message}
      </div>
      {showText && <div style={{width: '100%', maxHeight: '100px', overflowY: 'auto', position: 'relative', whiteSpace: 'pre-wrap'}}>{message.metadata?.text}</div>}
    </div>
  );
}
