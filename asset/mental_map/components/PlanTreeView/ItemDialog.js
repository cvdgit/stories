import React, {useEffect, useRef, useState} from 'react';
import Dialog from "../Dialog";
import {createWordsFormText, getTextBySelections} from "../Selection";
import {CSSTransition} from "react-transition-group";
import ContentEditable from "react-contenteditable";

export default function ItemDialog({open, setOpen, currentNode, currentTitle, currentDescription, setCurrentDescription, hideHandler}) {
  console.log('ItemDialog render')

  const ref = useRef()
  const [selectionMode, setSelectionMode] = useState(false)
  const selectionRef = useRef()
  const textRef = useRef()
  const [currentWords, setCurrentWords] = useState([])
  const [title, setTitle] = useState(currentTitle || '')
  const [description, setDescription] = useState(currentDescription || '')

  const emitChange = (e) => {
    setDescription(e.target.value)
    setCurrentDescription(e.target.value)
  }

  useEffect(() => {
    if (!currentWords.length) {
      return
    }
    setDescription(getTextBySelections(currentWords))
    setCurrentDescription(getTextBySelections(currentWords))
  }, [JSON.stringify(currentWords)]);

  return (
    <CSSTransition
      in={open}
      nodeRef={ref}
      timeout={200}
      classNames="dialog"
      unmountOnExit
    >
      <Dialog nodeRef={ref} hideHandler={() => {
        hideHandler({
          title,
          description: currentDescription
        })
        setOpen(false)
      }}>
        {currentNode && (<div>
            <div style={{display: 'flex', flexDirection: 'row'}}>
              <div style={{flex: '1', display: 'flex', flexDirection: 'column'}}>

                <div style={{margin: '20px 0'}}>
                  <input className="textarea" style={{minHeight: 'auto'}} type="text" value={title} onChange={e => {
                    setTitle(e.target.value)
                  }}/>
                </div>

                <div style={{marginBottom: '10px'}}>
                  <button onClick={() => {
                    setDescription(getTextBySelections(currentWords))
                    setCurrentDescription(getTextBySelections(currentWords))
                    setSelectionMode(false)
                  }}
                          className={`button button--default ${selectionMode ? 'button--outline' : 'button--header-done'} `}
                          type="button">Редактировать
                  </button>
                  <button onClick={() => {
                    setCurrentWords(createWordsFormText(currentDescription))
                    setSelectionMode(true)
                  }}
                          className={`button button--default ${selectionMode ? 'button--header-done' : 'button--outline'}`}
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
                    {currentWords.map((word, i) => {
                      const {type} = word
                      if (type === 'break') {
                        return (<div key={i} className="line-sep"></div>)
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
                  <ContentEditable
                    innerRef={textRef}
                    html={description}
                    onChange={emitChange}
                    tagName="div"
                    className="textarea"
                    style={{
                      borderStyle: 'solid',
                      maxHeight: '20rem',
                      overflowY: 'auto'
                    }}
                  />
                )}
              </div>
            </div>
          </div>
        )}
      </Dialog>
    </CSSTransition>
  )
}
