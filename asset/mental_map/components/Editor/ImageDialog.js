import Dialog from "../Dialog";
import React, {useEffect, useId, useRef, useState} from "react";
import {createWordsFormText, getTextBySelections} from "../Selection";
import {useMentalMap} from "../App/App";

export default function ImageDialog({
                                      ref,
                                      setOpen,
                                      currentImageItem,
                                      changeBgHandler,
                                      changeMakeTransparentHandler
                                    }) {
  const textRef = useRef()
  const selectionRef = useRef()
  const [selectionMode, setSelectionMode] = useState(false)
  const [currentText, setCurrentText] = useState(currentImageItem.text)
  const [currentWords, setCurrentWords] = useState([])
  const [bg, setBg] = useState(currentImageItem.bg)
  const [makeTransparent, setMakeTransparent] = useState(Boolean(currentImageItem.makeTransparent))
  const {state, dispatch} = useMentalMap()
  const checkId = useId()

  useEffect(() => {
    if (!currentWords.length) {
      return
    }
    dispatch({
      type: 'update_mental_map_images',
      imageId: currentImageItem.id,
      payload: {
        text: getTextBySelections(currentWords)
      }
    })
  }, [JSON.stringify(currentWords)]);

  const emitChange = (e) => {
    dispatch({
      type: 'update_mental_map_images',
      imageId: currentImageItem.id,
      payload: {
        text: e.target.innerHTML
      }
    })
  }

  return (
    <Dialog nodeRef={ref} hideHandler={() => setOpen(false)}>
      {currentImageItem && (
        <div>
          <div style={{display: 'flex', flexDirection: 'row'}}>
            <div style={{marginRight: '20px'}}>
              <img src={currentImageItem.url} alt=""/>
            </div>
            <div style={{flex: '1', display: 'flex', flexDirection: 'column'}}>
              <div style={{marginBottom: '10px'}}>
                <button onClick={() => {
                  setCurrentText(getTextBySelections(currentWords))
                  setSelectionMode(false)
                }}
                        className={`button button--default ${selectionMode ? 'button--outline' : 'button--header-done'} `}
                        type="button">Редактировать
                </button>
                <button onClick={() => {
                  setCurrentText(textRef.current.innerHTML)
                  setCurrentWords(createWordsFormText(textRef.current.innerHTML))
                  setSelectionMode(true)
                }}
                        className={`button button--default ${selectionMode ? 'button--header-done' : 'button--outline'} `}
                        type="button">Выделить
                </button>
              </div>
              {selectionMode ? (
                <div
                  ref={selectionRef}
                  className="textarea"
                  style={{
                    borderStyle: 'solid',
                    maxHeight: '20rem',
                    overflowY: 'auto'
                  }}
                >
                  {currentWords.map(word => {
                    const {type} = word
                    if (type === 'break') {
                      return (<div key={word.id} className="line-sep"></div>)
                    }
                    return (
                      <span
                        key={word.id}
                        onClick={() => {
                          setCurrentWords(prevState => [...prevState].map(w => {
                              if (w.id === word.id) {
                                w.hidden = !w.hidden
                              }
                              return w
                            })
                          )
                        }}
                        className={`text-item-word ${word.hidden ? 'selected' : ''}`}
                      >{word.word}</span>
                    )
                  })}
                </div>
              ) : (
                <div
                  ref={textRef}
                  contentEditable="plaintext-only"
                  className="textarea"
                  dangerouslySetInnerHTML={{__html: currentText}}
                  onInput={emitChange}
                  onBlur={emitChange}
                  onKeyUp={emitChange}
                  onKeyDown={emitChange}
                  style={{
                    borderStyle: 'solid',
                    maxHeight: '20rem',
                    overflowY: 'auto'
                  }}
                ></div>
              )}
              <div style={{marginTop: '2rem'}}>
                    <textarea className="textarea" onChange={() => {
                    }} value={state.text} style={{minHeight: '300px'}}/>
              </div>
            </div>
          </div>
          {!currentImageItem.url && (
            <div style={{
              marginRight: '20px',
              display: 'flex',
              flexDirection: 'row',
              justifyContent: 'center',
              alignItems: 'center',
              padding: '10px 0'
            }}>
              <div>
                <span style={{marginRight: '10px'}}>Фон:</span>
                <select value={String(bg || '')} onChange={(e) => {
                  changeBgHandler(currentImageItem.id, e.target.value)
                  setBg(e.target.value)
                }} style={{padding: '10px'}}>
                  <option value="">Прозрачный</option>
                  <option value="blue">Blue</option>
                  <option value="green">Green</option>
                  <option value="red">Red</option>
                </select>
              </div>
              <div>
                <label htmlFor={checkId}>
                  <input
                    id={checkId}
                    onChange={(e) => {
                      changeMakeTransparentHandler(currentImageItem.id, e.target.checked)
                      setMakeTransparent(e.target.checked)
                    }}
                    checked={Boolean(makeTransparent)}
                    type="checkbox"
                  /> Прозрачный фон при верном ответе</label>
              </div>
            </div>
          )}
        </div>
      )}
    </Dialog>
  )
}
