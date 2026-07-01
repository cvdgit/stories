import React, {useEffect, useRef, useState} from "react";
import {CSSTransition} from "react-transition-group";
import Dialog from "../../Dialog";
import {formatTextWithLineNumbers} from "../../../Lib";

export default function TextDialog({text, open, setOpen, successCallback, controls}) {
  const textDialogRef = useRef(null);
  const [formattedMapText, setFormattedMapText] = useState([])
  const [mapText, setMapText] = useState(text || '')

  useEffect(() => {
    setFormattedMapText(formatTextWithLineNumbers(mapText))
  }, [mapText]);

  const addFragmentsHandler = () => {
    if (formattedMapText.length === 0) {
      return
    }
    setOpen(false)
    successCallback(formattedMapText)
  }

  // (<button onClick={addFragmentsHandler} className="button button--default button--header-done">Добавить фрагменты</button>)
  return (
    <CSSTransition
      in={open}
      nodeRef={textDialogRef}
      timeout={200}
      classNames="dialog"
      unmountOnExit
    >
      <Dialog
        style={{width: '90%', height: '90%'}}
        nodeRef={textDialogRef}
        hideHandler={() => {
          setOpen(false)
        }}
        controls={
          controls.map((component, key) => component(key, formattedMapText))
        }
      >
        <h2
          className="dialog-heading"
          style={{display: 'flex', justifyContent: 'space-between', alignItems: 'center'}}
        >
          Текст
          <button
            type="button"
            onClick={() => {
              const paragraphs = (formattedMapText || [])
                .map(
                  p => p.replace(/\n/g, ' ')
                    .trim()
                    .replace(/\s+/g, ' ')
                )
              const text = paragraphs.join("\n\n")
              dispatch({
                type: 'update_mental_map_text',
                text,
              })
              setMapText(text)
            }}
            className="button button--default button--outline"
          >Форматировать</button></h2>
        <div style={{display: 'flex', flexDirection: 'row', gap: '20px', flex: '1', maxHeight: '100%', overflow: 'hidden'}}>
          <div style={{flex: '1'}}>
              <textarea className="textarea" style={{minHeight: '400px'}} placeholder="Текст" onChange={(e) => {
                setMapText(e.target.value)
              }} value={mapText}/>
          </div>
          <div style={{flex: '1'}}>
            <div className="textarea" style={{
              overflowY: 'auto',
              borderWidth: '1px',
              borderStyle: 'solid',
              borderColor: 'rgb(176 190 197 / 1)'
            }}>
              {(formattedMapText || []).map((p, i) => (
                <div className="text-line" key={i}>
                  <span>{i + 1}</span>
                  {p.split("\n").map((t, j) => (<p key={j}>{t}</p>))}
                </div>))}
            </div>
          </div>
        </div>
      </Dialog>
    </CSSTransition>
  )
}
