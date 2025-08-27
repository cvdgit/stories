import React, {useEffect, useRef, useState, forwardRef} from 'react';
import Dialog from "../Dialog";
import {createWordsFormText, getTextBySelections} from "../Selection";
import api from "../../Api";
import Editable from "../Editable";

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
  const [currentWords, setCurrentWords] = useState([])
  const [title, setTitle] = useState('')
  const [description, setDescription] = useState('')
  const [mark, setMark] = useState(false)
  const [prompts, setPrompts] = useState([])
  const [promptId, setPromptId] = useState('')

  const emitChange = (content) => {
    setMark(false)
    setTitle(content)
  }

  useEffect(() => {
    if (currentNode) {
      setTitle(currentNode.title)
      setMark(markInit)
      setPromptId(currentNode.promptId || '')
    }
  }, [JSON.stringify(currentNode)]);

  useEffect(() => {
    if (!currentWords.length) {
      return
    }
    setTitle(getTextBySelections(currentWords))
  }, [JSON.stringify(currentWords)]);

  useEffect(() => {
    if (!open) {
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
      })

  }, [open]);

  return (
    <Dialog nodeRef={ref} hideHandler={() => {
      hideHandler({
        title,
        promptId
      })
      setOpen(false)
    }} addContentClassName="item-content">
      {currentNode && (<div style={{flex: '1', display: 'flex', flexDirection: 'column', overflow: 'hidden', height: '100%'}}>
          <div style={{display: 'flex', height: '100%'}}>
            <div style={{display: 'flex', flexDirection: 'column', width: '100%', justifyContent: 'space-between'}}>
              <div>
                <div style={{margin: '20px 0'}}>
                  <div>
                    <label style={{paddingBottom: '4px', fontSize: '14px', display: 'block'}} htmlFor="">Проверочный промт:</label>
                    <select className="textarea" value={promptId} onChange={(e) => {
                      setPromptId(e.target.value)
                    }} style={{width: '100%', padding: '10px', minHeight: 'auto'}}>
                      <option value="">По умолчанию (значение из настроек)</option>
                      {prompts.map((p, i) => (
                        <option key={i} value={p.id}>{p.name}</option>
                        ))}
                    </select>
                  </div>
                </div>
                <div style={{marginBottom: '10px'}}>
                  <button onClick={() => {
                    if (!selectionMode) {
                      return
                    }
                    setTitle(getTextBySelections(currentWords))
                    setSelectionMode(false)
                  }}
                          className={`button button--default ${selectionMode ? 'button--outline' : 'button--header-done'} `}
                          type="button">Редактировать
                  </button>
                  <button onClick={() => {
                    if (selectionMode) {
                      return
                    }
                    setCurrentWords(createWordsFormText(title))
                    setSelectionMode(true)
                  }}
                          className={`button button--default ${selectionMode ? 'button--header-done' : 'button--outline'}`}
                          type="button">Выделить
                  </button>
                </div>
              </div>
              <div style={{flex: '1', overflowY: 'auto'}}>
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
                          onClick={(e) => {
                            if (e.ctrlKey) {
                              console.log('click')
                            }
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
                  <Editable content={title} changeHandler={emitChange} />
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
