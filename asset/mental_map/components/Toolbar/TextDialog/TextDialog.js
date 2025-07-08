import React, {useEffect, useRef, useState} from "react";
import {CSSTransition} from "react-transition-group";
import Dialog from "../../Dialog";
import {useMentalMap} from "../../App/App";
import api from "../../../Api";
import {formatTextWithLineNumbers} from "../../../Lib";

export default function TextDialog({open, setOpen, setTextFragmentCount, setFormattedMapTextGlobal}) {
  const textDialogRef = useRef();
  const {state, dispatch} = useMentalMap()
  const [formattedMapText, setFormattedMapText] = useState([])
  const [mapText, setMapText] = useState(state.text.toString())
  const firstUpdate = useRef(true)

  useEffect(() => {
    setFormattedMapText(formatTextWithLineNumbers(mapText))
  }, [mapText]);

  useEffect(() => {
    if (firstUpdate.current) {
      firstUpdate.current = false;
      return;
    }
    const timeoutId = setTimeout(() => api
      .post('/admin/index.php?r=mental-map/update-map-text', {
        payload: {
          id: state.id,
          text: mapText
        }
      }), 500);
    return () => clearTimeout(timeoutId);
  }, [mapText]);

  return (
    <CSSTransition
      in={open}
      nodeRef={textDialogRef}
      timeout={200}
      classNames="dialog"
      unmountOnExit
    >
      <Dialog nodeRef={textDialogRef} hideHandler={() => {
        setTextFragmentCount((formattedMapText || []).length)
        setFormattedMapTextGlobal(formattedMapText)
        setOpen(false)
      }}>
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
        <div style={{display: 'flex', flexDirection: 'row', gap: '20px', maxHeight: '500px'}}>
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
