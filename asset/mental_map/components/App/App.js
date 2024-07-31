import React, {createContext, useContext, useEffect, useReducer, useRef, useState} from 'react';
import './App.css'
import AppLoader from "./AppLoader";
import Editor from "../Editor";
import api, {parseError} from "../../Api";
import MentalMapReducer from "../../Lib/MentalMapReducer";
import ImagesReducer from "../../Lib/ImagesReducer";
import Dialog from "../Dialog";
import {CSSTransition} from "react-transition-group";

export const MentalMapContext = createContext({});
export const ImagesContext = createContext({});

export default function App({mentalMapId}) {
  const [loading, setLoading] = useState(true)
  const [mentalMap, setMentalMap] = useState({})
  const [error, setError] = useState(null)
  const [state, dispatch] = useReducer(MentalMapReducer, {})
  const [imagesState, imagesDispatch] = useReducer(ImagesReducer, {})
  const ref = useRef()
  const [open, setOpen] = useState(false)
  const [mapText, setMapText] = useState(state.text)
  const firstUpdate = useRef(true)
  const imagesRef = useRef()
  const [imagesOpen, setImagesOpen] = useState(false)
  const [textFragmentCount, setTextFragmentCount] = useState(0)
  const [formattedMapText, setFormattedMapText] = useState()

  useEffect(() => {
    api
      .get(`/admin/index.php?r=mental-map/get&id=${mentalMapId}`)
      .then((response) => {
        setLoading(false);
        setMentalMap(response.course);
        dispatch({
          type: 'mental_map_loaded',
          mentalMap: response.mentalMap
        })
        imagesDispatch({
          type: 'images_loaded',
          images: response.images
        })
      })
      .catch(async (error) => setError(await parseError(error)))
  }, [])

  const mentalMapContext = {state, dispatch}
  const imagesContext = {state: imagesState, dispatch: imagesDispatch}

  useEffect(() => {
    if (firstUpdate.current) {
      firstUpdate.current = false;
      return;
    }
    const timeoutId = setTimeout(() => api
      .post('/admin/index.php?r=mental-map/update-map-text', {
        payload: {
          id: state.id,
          text: state.text
        }
      }), 500);
    return () => clearTimeout(timeoutId);
  }, [mapText]);

  useEffect(() => {
    setFormattedMapText(formatTextWithLineNumbers(state.text))
  }, [state.text]);

  const returnUrl = window?.mentalMapReturnUrl || '/'

  function formatTextWithLineNumbers(text) {
    return (text || '').split("\n").filter(p => p !== '')
  }

  return (
    <div>
      <div>
        <div>
          <div className="app-header">
            <div className="app-header__menu-btn">
              <a className="app-header__menu-close" href={returnUrl}>Назад</a>
            </div>
            <div className="app-header__title">{state.name}</div>
            <div className="app-header__btn-group">
              <button onClick={() => {
                setOpen(true)
              }} className="button button--default button--header-done"
                      type="button">Текст {textFragmentCount > 0 && (<span> ({textFragmentCount})</span>)}
              </button>
              <button onClick={() => {
                setImagesOpen(true)
              }} className="button button--default button--header-done"
                      type="button">Изображения
              </button>
            </div>
          </div>
        </div>
      </div>
      {loading
        ? <AppLoader/>
        : (
          <MentalMapContext.Provider value={mentalMapContext}>
            <ImagesContext.Provider value={imagesContext}>
              <Editor/>
            </ImagesContext.Provider>
          </MentalMapContext.Provider>
        )
      }

      <CSSTransition
        in={open}
        nodeRef={ref}
        timeout={200}
        classNames="dialog"
        unmountOnExit
      >
        <Dialog nodeRef={ref} hideHandler={() => {
          setTextFragmentCount((formattedMapText || []).length)
          setOpen(false)
        }}>
          <h2 className="dialog-heading">Текст</h2>
          <div style={{display: 'flex', flexDirection: 'row', gap: '20px', maxHeight: '500px'}}>
            <div style={{flex: '1'}}>
              <textarea className="textarea" style={{minHeight: '400px'}} placeholder="Текст" onChange={(e) => {
                dispatch({
                  type: 'update_mental_map_text',
                  text: e.target.value
                })
                setMapText(e.target.value)
              }} value={state.text}/>
            </div>
            <div style={{flex: '1'}}>
              <div className="textarea" style={{overflowY: 'auto', borderWidth: '1px', borderStyle: 'solid', borderColor: 'rgb(176 190 197 / 1)'}}>
                {(formattedMapText || []).map((p, i) => (<div className="text-line" key={i}><span>{i + 1}</span>{p}</div>))}
              </div>
            </div>
          </div>
        </Dialog>
      </CSSTransition>

      <CSSTransition
        in={imagesOpen}
        nodeRef={imagesRef}
        timeout={200}
        classNames="dialog"
        unmountOnExit
      >
        <Dialog nodeRef={imagesRef} hideHandler={() => setImagesOpen(false)}>
          <h2 className="dialog-heading">Изображения</h2>
          <div className="all-images-list">
            {state.map?.images.map((image, i) => (
              <div className="all-images-list-item" key={i}>
                <div style={{marginRight: '10px'}}>
                  <img style={{maxWidth: '100px'}} src={image.url} alt="img"/>
                </div>
                <div style={{width: '100%'}}>
                  <div
                    className="textarea"
                    style={{
                      border: '1px #808080 solid',
                      maxHeight: '100px',
                      overflowY: 'auto'
                    }}
                    contentEditable="plaintext-only"
                    dangerouslySetInnerHTML={{__html: image.text}}
                  ></div>
                </div>
              </div>
            ))}
          </div>
        </Dialog>
      </CSSTransition>
    </div>
  )
}

export function useMentalMap() {
  return useContext(MentalMapContext);
}

export function useImages() {
  return useContext(ImagesContext);
}
