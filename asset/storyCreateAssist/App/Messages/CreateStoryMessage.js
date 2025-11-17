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
      {showText && <div style={{maxHeight: '100px', overflowY: 'auto', position: 'relative'}}>{message.metadata?.text}</div>}
    </div>
  );
}
