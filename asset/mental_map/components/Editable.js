import React, {useRef} from "react";
import ContentEditablePlainText from "./ContentEditablePlainText";

export default function Editable({content, changeHandler}) {
  const ref = useRef();
  return (
    <ContentEditablePlainText
      ref={ref}
      tagName="div"
      className="textarea"
      spellCheck={true}
      style={{
        outline: 0,
        borderStyle: 'solid',
        overflowY: 'auto'
      }}
      dir="auto"
      onChange={e => changeHandler(e.target.value)}
      html={content}
      content={content}
      onPasteCapture={(e) => {
        e.preventDefault();
        e.target.innerText = e.clipboardData.getData("text/plain");
      }}
    />
  )
}
