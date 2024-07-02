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

  const returnUrl = window?.mentalMapReturnUrl || '/'

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
                      type="button">Текст
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
        <Dialog nodeRef={ref} hideHandler={() => setOpen(false)}>
          <h2 className="dialog-heading">Текст</h2>
          <div>
            <textarea className="textarea" style={{minHeight: '400px'}} placeholder="Текст" onChange={(e) => {
              dispatch({
                type: 'update_mental_map_text',
                text: e.target.value
              })
              setMapText(e.target.value)
            }} value={state.text} />
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
