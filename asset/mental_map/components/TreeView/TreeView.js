import React, {createContext, useEffect, useReducer, useRef, useState} from "react";
import {
  addNodeUnderParent,
  changeNodeAtPath,
  getNodeAtPath,
  insertNode, removeNodeAtPath,
  SortableTree
} from "@nosferatu500/react-sortable-tree";
import "./TreeView.css";
import api, {parseError} from "../../Api";
import TreeReducer from "../../Lib/TreeReducer";
import {v4 as uuidv4} from 'uuid'
import Dialog from "../Dialog";
import {CSSTransition} from "react-transition-group";
import {createWordsFormText, getTextBySelections} from "../Selection";

export const TreeContext = createContext({});

let sendSaveRequest = false

export default function TreeView({texts}) {
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)
  const [state, dispatch] = useReducer(TreeReducer, {treeData: []})
  const [open, setOpen] = useState(false)
  const ref = useRef(null)
  const [selectionMode, setSelectionMode] = useState(false)
  const [currentText, setCurrentText] = useState(null)
  const [currentWords, setCurrentWords] = useState([])
  const textRef = useRef()
  const selectionRef = useRef()
  const [currentNode, setCurrentNode] = useState(null)
  const [markedItems, setMarkedItems] = useState([])

  const getNodeKey = ({treeIndex}) => treeIndex;

  useEffect(() => {
    api
      .get(`/admin/index.php?r=mental-map/tree-init&id=${mentalMapId}`)
      .then((response) => {
        setLoading(false);
        dispatch({
          type: 'tree_loaded',
          tree: response.payload
        })
      })
      .catch(async (error) => setError(await parseError(error)))
  }, [])

  const treeContext = {state, dispatch}

  useEffect(() => {
    if (!sendSaveRequest) {
      sendSaveRequest = true
      return
    }
    const timeoutId = setTimeout(() => api
      .post('/admin/index.php?r=mental-map/tree-save', {
        payload: {
          id: mentalMapId,
          treeData: state.treeData
        }
      }), 500);
    return () => clearTimeout(timeoutId);
  }, [JSON.stringify(state.treeData)]);

  const createNodesFromTextHandler = () => {
    texts.map(t => dispatch({
      type: 'add_node',
      payload: {id: uuidv4(), title: t, description: t}
    }))
  }

  useEffect(() => {
    if (!currentWords.length) {
      return
    }
    dispatch({
      type: 'update_tree',
      treeData: currentNode.changeHandler(getTextBySelections(currentWords))
    })
  }, [JSON.stringify(currentWords)]);

  const emitChange = (e) => {
    dispatch({
      type: 'update_tree',
      treeData: currentNode.changeHandler(e.target.innerHTML)
    })
  }

  return (
    <div className="author-layout__content">
      <TreeContext.Provider value={treeContext}>
        <div className="tree-wrap">
          <div>
            <p>&nbsp;</p>
          </div>
          <div className="tree-inner">
            <SortableTree
              treeData={state.treeData}
              rowHeight={140}
              onChange={treeData => dispatch({type: 'update_tree', treeData})}
              generateNodeProps={({node, path}) => ({
                title: (
                  <div
                    style={{whiteSpace: "pre-wrap", overflow: "auto"}}
                    className={`node-title ${markedItems.includes(node.id) ? 'node-marked' : ''}`}
                    onClick={() => {
                      setCurrentNode({
                        ...node, changeHandler: (title) => {
                          if (node.title === title) {
                            return state.treeData
                          }
                          setMarkedItems(prevState => {
                            if (prevState.includes(node.id)) {
                              return [...prevState]
                            }
                            prevState.push(node.id)
                            return [...prevState]
                          })
                          return changeNodeAtPath({
                            treeData: state.treeData,
                            path,
                            getNodeKey,
                            newNode: {...node, title},
                          })
                        }
                      });
                      setSelectionMode(false)
                      setCurrentText(node.title)
                      setOpen(true)
                    }}
                    dangerouslySetInnerHTML={{__html: node.title}}
                  ></div>
                ),
                buttons: [
                  <button
                    style={{width: '30px', padding: '6px'}}
                    onClick={() => {
                      const treeData = addNodeUnderParent({
                        treeData: state.treeData,
                        parentKey: path[path.length - 1],
                        expandParent: true,
                        getNodeKey,
                        newNode: {
                          id: uuidv4(),
                          title: '',
                        },
                        addAsFirstChild: state.addAsFirstChild,
                      }).treeData
                      dispatch({
                        type: 'update_tree',
                        treeData,
                      })
                    }}
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="1.5"
                         stroke="currentColor" className="size-6">
                      <path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                  </button>,
                  <button
                    style={{width: '30px', padding: '6px'}}
                    onClick={() => {

                      const sibling = getNodeAtPath({
                        treeData: state.treeData,
                        path,
                        getNodeKey,
                        ignoreCollapsed: true,
                      })

                      const treeData = insertNode({
                        treeData: state.treeData,
                        depth: path.length - 1,
                        minimumTreeIndex: sibling.treeIndex + 1,
                        newNode: {...node, id: uuidv4(), children: []},
                        getNodeKey,
                        ignoreCollapsed: false,
                      }).treeData
                      dispatch({
                        type: 'update_tree',
                        treeData,
                      })
                    }}
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="1.5"
                         stroke="currentColor" className="size-6">
                      <path strokeLinecap="round" strokeLinejoin="round"
                            d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75"/>
                    </svg>
                  </button>,
                  <button
                    style={{width: '30px', padding: '6px'}}
                    onClick={() => {
                      const treeData = removeNodeAtPath({
                        treeData: state.treeData,
                        path,
                        getNodeKey,
                      })
                      dispatch({
                        type: 'update_tree',
                        treeData,
                      })
                    }}
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="1.5"
                         stroke="currentColor" className="size-6">
                      <path strokeLinecap="round" strokeLinejoin="round"
                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                    </svg>
                  </button>,
                ],
              })}
            />

            <div style={{display: 'flex', alignItems: 'center', justifyContent: 'center', padding: '10px'}}>
              <button
                className="button button--default button--header-done"
                onClick={() =>
                  dispatch({
                    type: 'add_node',
                    payload: {id: uuidv4(), title: ''}
                  })
                }
              >
                Добавить
              </button>
              {state.treeData.length === 0 && (
                <button onClick={createNodesFromTextHandler} className="button button--default button--header-done"
                        type="button">Создать из текста</button>
              )}
            </div>
          </div>
        </div>

        <div>
          <CSSTransition
            in={open}
            nodeRef={ref}
            timeout={200}
            classNames="dialog"
            unmountOnExit
          >
            <Dialog nodeRef={ref} hideHandler={() => setOpen(false)} addContentClassName="item-content">
              {currentNode && (<div style={{flex: '1', display: 'flex', flexDirection: 'column', overflow: 'hidden', height: '100%'}}>
                  <div style={{display: 'flex', height: '100%'}}>
                    <div style={{display: 'flex', flexDirection: 'column', width: '100%', justifyContent: 'space-between'}}>
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
                            overflowY: 'auto'
                          }}
                        ></div>
                      )}
                      </div>
                    </div>
                  </div>
                  {markedItems.includes(currentNode.id) && <div className="dialog-action" style={{paddingTop: '1rem'}}>
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
          </CSSTransition>
        </div>
      </TreeContext.Provider>
    </div>
  )
}
