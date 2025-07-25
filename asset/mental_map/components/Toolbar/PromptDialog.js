import {CSSTransition} from "react-transition-group";
import Dialog from "../Dialog";
import React, {useEffect, useRef, useState} from "react";
import api from "../../Api";

export default function PromptDialog({open, hideDialog, saveHandler, currentPrompt}) {
  const ref = useRef();
  const [name, setName] = useState('')
  const [promptText, setPromptText] = useState('')

  const controls = [
    (<button key={0} onClick={async () => {

      if (!name || !promptText) {
        alert('Необходимо заполнить поля')
        return
      }

      if (currentPrompt) {
        const response = await api.post('/admin/index.php?r=llm-prompt/update', {
          id: currentPrompt.id,
          name,
          'prompt': promptText
        }, {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        })
      } else {
        const response = await api.post('/admin/index.php?r=llm-prompt/create', {
          name,
          'prompt': promptText
        }, {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        })
      }

      saveHandler()
      hideDialog()
    }} type="button" className="button button--default button--header-done">Сохранить</button>)
  ]

  useEffect(() => {
    setName(currentPrompt ? currentPrompt.name : '')
    setPromptText(currentPrompt ? currentPrompt.prompt : '')
  }, [JSON.stringify(currentPrompt)]);

  return (
    <CSSTransition
      in={open}
      nodeRef={ref}
      timeout={200}
      classNames="dialog"
      unmountOnExit
    >
      <Dialog controls={controls} nodeRef={ref} hideHandler={() => {
        hideDialog()
      }} style={{width: '60rem'}}>
        <h2 className="dialog-heading">{currentPrompt ? 'Prompt' : 'New prompt'}</h2>
        <div style={{padding: '20px 0'}}>
          <input className="textarea" style={{minHeight: 'auto'}} type="text" value={name} onChange={e => setName(e.target.value)}/>
        </div>
        <div style={{padding: '20px 0'}}>
          <textarea className="textarea" style={{minHeight: '400px'}} placeholder="Текст" onChange={(e) => {
            setPromptText(e.target.value)
          }} value={promptText}/>
        </div>
      </Dialog>
    </CSSTransition>
  )
}
