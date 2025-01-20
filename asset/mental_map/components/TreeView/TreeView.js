import React, {createContext, useEffect, useReducer, useState} from "react";
import {changeNodeAtPath, SortableTree} from "@nosferatu500/react-sortable-tree";
import TextareaAutosize from 'react-textarea-autosize';
import "./TreeView.css";
import {addNodeUnderParent, removeNodeAtPath} from "./tree";
import api, {parseError} from "../../Api";
import TreeReducer from "../../Lib/TreeReducer";
import {v4 as uuidv4} from 'uuid'

export const TreeContext = createContext({});

let sendSaveRequest = false

export default function TreeView({texts}) {
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)
  const [state, dispatch] = useReducer(TreeReducer, {treeData: []})

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
      payload: {id: uuidv4(), title: t}
    }))
  }

  return (
    <div className="author-layout__content">
      <TreeContext.Provider value={treeContext}>
        <div
          style={{paddingLeft: '3rem', paddingRight: '3rem', height: '100%', display: 'flex', flexDirection: 'column'}}>
          <div>
            <p>&nbsp;</p>
          </div>
          <div style={{flex: '1', width: '80%', margin: '0 auto', display: 'flex', flexDirection: 'column'}}>
            <SortableTree
              treeData={state.treeData}
              rowHeight={100}
              onChange={treeData => dispatch({type: 'update_tree', treeData})}
              generateNodeProps={({node, path}) => ({
                title: (
                  <TextareaAutosize
                    style={{width: '100%', resize: 'none'}}
                    onChange={
                      event => {
                        const title = event.target.value;
                        const data = changeNodeAtPath({
                          treeData: state.treeData,
                          path,
                          getNodeKey,
                          newNode: {...node, title},
                        })
                        dispatch({type: 'update_tree', treeData: data})
                      }
                    } value={node.title}/>
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
                <button onClick={createNodesFromTextHandler} className="button button--default button--header-done" type="button">Создать из текста</button>
              )}
            </div>
          </div>
        </div>
      </TreeContext.Provider>
    </div>
  )
}
