import React, {useEffect, useRef, useState, forwardRef} from 'react';
import Dialog from "../Dialog";
import {createWordsFormText, getTextBySelections} from "../Selection";
import ContentEditable from "react-contenteditable";

const ItemDialog = forwardRef(function ItemDialog(props, ref) {
  const {
    setOpen,
    currentNode,
    markInit,
    currentTitle,
    currentDescription,
    setCurrentDescription,
    hideHandler,
    setMarkedItems
  } = props

  const [selectionMode, setSelectionMode] = useState(false)
  const selectionRef = useRef()
  const textRef = useRef()
  const [currentWords, setCurrentWords] = useState([])
  const [title, setTitle] = useState('')
  const [description, setDescription] = useState('')
  const [mark, setMark] = useState(false)

  const emitChange = (e) => {
    setDescription(e.target.value)
    setCurrentDescription(e.target.value)
    setMark(false)
  }

  useEffect(() => {
    if (currentNode) {
      setTitle(currentNode.title)
      setDescription(currentNode.description)
      setMark(markInit)
    }
  }, [JSON.stringify(currentNode)]);

  useEffect(() => {
    if (!currentWords.length) {
      return
    }
    setDescription(getTextBySelections(currentWords))
    setCurrentDescription(getTextBySelections(currentWords))
  }, [JSON.stringify(currentWords)]);

  return (
    <Dialog nodeRef={ref} hideHandler={() => {
      hideHandler({
        title,
        description: currentDescription
      })
      setOpen(false)
    }} addContentClassName="item-content">
      {currentNode && (<div style={{flex: '1', display: 'flex', flexDirection: 'column'}}>
          <div style={{display: 'flex', height: '100%'}}>
            <div style={{display: 'flex', flexDirection: 'column', width: '100%', justifyContent: 'space-between'}}>
              <div>
                <div style={{margin: '20px 0'}}>
                  <input className="textarea" style={{minHeight: 'auto'}} type="text" value={title} onChange={e => {
                    setTitle(e.target.value)
                    setMark(false)
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
              </div>
              <div style={{flex: '1'}}>
                {selectionMode ? (
                  <div
                    ref={selectionRef}
                    className="textarea"
                    style={{
                      borderStyle: 'solid',
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
                      overflowY: 'auto'
                    }}
                  />
                )}
              </div>
            </div>
          </div>
          {mark && <div className="dialog-action" style={{paddingTop: '1rem'}}>
            <button onClick={() => {
              setMarkedItems(prevState => {
                return [...prevState].filter(id => id !== currentNode.id)
              })
              setOpen(false)
            }} className="button button--default button--outline" type="button">Закрыть и снять выделение
            </button>
          </div>}
        </div>
      )}
    </Dialog>
  )
})

export default ItemDialog;
