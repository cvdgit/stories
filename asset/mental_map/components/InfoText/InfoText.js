import React, {useRef, useState} from 'react';
import Editor from './Editor';
import Quill from 'quill';
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";

const Delta = Quill.import('delta');

export default function InfoText({defaultText, changeTextHandler}) {
  const quillRef = useRef(null);
console.log(defaultText);
  return (
    <div style={{margin: '20px'}}>
      <h3 className="dialog-heading" style={{fontSize: '2.2rem'}}>Закрепленный текст</h3>
      <Editor
        ref={quillRef}
        defaultValue={defaultText}
        onTextChange={changeTextHandler}
      />
    </div>
  );
}
