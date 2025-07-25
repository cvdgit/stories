import {CSSTransition} from "react-transition-group";
import Dialog from "../Dialog";
import React, {useEffect, useRef, useState} from "react";
import api from "../../Api";

export default function PromptsDialog({open, hideDialog, showPromptDialog, loadPrompts, promptsLoaded}) {
  const ref = useRef();
  const [prompts, setPrompts] = useState([])

  useEffect(() => {
    if (!open) {
      return
    }
    if (!loadPrompts) {
      return
    }

    async function fetchPrompts() {
      const response = await api.get('/admin/index.php?r=llm-prompt/get', {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      })
      return response
    }
    fetchPrompts()
      .then(response => {
        setPrompts(response.prompts)
        promptsLoaded()
      })

  }, [open, loadPrompts]);

  return (
    <CSSTransition
      in={open}
      nodeRef={ref}
      timeout={200}
      classNames="dialog"
      unmountOnExit
    >
      <Dialog nodeRef={ref} hideHandler={() => {
        hideDialog()
      }} style={{width: '60rem'}}>
        <h2 className="dialog-heading">Prompts</h2>
        <div style={{padding: '20px 0'}}>
          {prompts.length ? prompts.map((prompt, i) => (
            <div key={i} onClick={() => showPromptDialog(prompt)} style={{cursor: 'pointer'}}>
              <div>{prompt.name}</div>
            </div>
          )) : <div>No prompts</div>}
        </div>
        <div style={{padding: '20px 0'}}>
          <button onClick={() => {
            showPromptDialog()
          }} type="button"
                  className="button button--default button--header-done">+ Добавить
          </button>
        </div>
      </Dialog>
    </CSSTransition>
  )
}
