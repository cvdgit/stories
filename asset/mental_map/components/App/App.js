import React, {createContext, useContext, useEffect, useId, useReducer, useRef, useState} from 'react';
import './App.css'
import AppLoader from "./AppLoader";
import Editor from "../Editor";
import api, {parseError} from "../../Api";
import MentalMapReducer from "../../Lib/MentalMapReducer";
import ImagesReducer from "../../Lib/ImagesReducer";
import Dialog from "../Dialog";
import {CSSTransition} from "react-transition-group";
import TreeView from "../TreeView";

export const MentalMapContext = createContext({});
export const ImagesContext = createContext({});
export const SchedulesContext = createContext({});

export default function App({mentalMapId}) {
  const [loading, setLoading] = useState(true)
  //const [mentalMap, setMentalMap] = useState({})
  const [error, setError] = useState(null)
  const [state, dispatch] = useReducer(MentalMapReducer, {})
  const [imagesState, imagesDispatch] = useReducer(ImagesReducer, {})
  const [schedules, setSchedules] = useState([])
  const ref = useRef()
  const [open, setOpen] = useState(false)
  const [mapText, setMapText] = useState(state.text)
  const firstUpdate = useRef(true)
  const settingsRef = useRef()
  const [settingsOpen, setSettingsOpen] = useState(false)
  const [textFragmentCount, setTextFragmentCount] = useState(0)
  const [formattedMapText, setFormattedMapText] = useState()
  const [settings, setSettings] = useState(state?.settings || {})
  const checkId = useId()
  const [isTreeView, setIsTreeView] = useState(false)

  useEffect(() => {
    api
      .get(`/admin/index.php?r=mental-map/get&id=${mentalMapId}`)
      .then((response) => {
        setLoading(false);
        //setMentalMap(response.course);
        dispatch({
          type: 'mental_map_loaded',
          mentalMap: response.mentalMap
        })
        imagesDispatch({
          type: 'images_loaded',
          images: response.images
        })
        setSchedules(response.schedules)
        setIsTreeView(Boolean(response?.mentalMap?.treeView))
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

  useEffect(() => {
    const timeoutId = setTimeout(() => api
      .post('/admin/index.php?r=mental-map/update-settings', {
        payload: {
          id: mentalMapId,
          settings: state?.settings || {}
        }
      }), 500);
    return () => clearTimeout(timeoutId);
  }, [JSON.stringify(state?.settings)]);

  useEffect(() => {
    setFormattedMapText(formatTextWithLineNumbers(state.text))
  }, [state.text]);

  const returnUrl = window?.mentalMapReturnUrl || '/'

  function formatTextWithLineNumbers(text) {
    return (text || '').split("\n\n").filter(p => p !== '')
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
                setSettingsOpen(true)
              }} className="button button--default button--header-done"
                      type="button">Настройки
              </button>
            </div>
          </div>
        </div>
      </div>
      {loading
        ? <AppLoader/>
        : (
          isTreeView
            ? <TreeView texts={formattedMapText}/>
            :
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
          <h2
            className="dialog-heading"
            style={{display: 'flex', justifyContent: 'space-between', alignItems: 'center'}}
          >
            Текст
            <button
              type="button"
              onClick={() => {
                //setFormattedMapText(prevState => [...prevState].map(p => p.replace(/\n/g, ' ').trim().replace(/\s+/g, ' ')))
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
                dispatch({
                  type: 'update_mental_map_text',
                  text: e.target.value
                })
                setMapText(e.target.value)
              }} value={state.text}/>
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

      <CSSTransition
        in={settingsOpen}
        nodeRef={settingsRef}
        timeout={200}
        classNames="dialog"
        unmountOnExit
      >
        <Dialog nodeRef={settingsRef} hideHandler={() => setSettingsOpen(false)} style={{width: '60rem'}}>
          <h2 className="dialog-heading">Настройки</h2>
          <div style={{padding: '20px 0'}}>
            <div style={{marginBlock: '20px'}}>
              <label htmlFor={checkId}>
                <input
                  id={checkId}
                  onChange={(e) => {
                    settings.imageFirst = !Boolean(state?.settings?.imageFirst)
                    setSettings(settings)
                    dispatch({
                      type: 'update_settings',
                      payload: settings
                    })
                  }}
                  checked={Boolean(state?.settings?.imageFirst)}
                  type="checkbox"
                /> При прохождении показывать изображение ментальной карты</label>
            </div>
            <div>
              <select value={String(state?.settings?.scheduleId)} onChange={(e) => {
                settings.scheduleId = e.target.value === '' ? null : Number(e.target.value)
                setSettings(settings)
                dispatch({
                  type: 'update_settings',
                  payload: settings
                })
              }} style={{width: '100%', padding: '10px'}}>
                <option value="">Расписание повторений</option>
                {schedules.map(s => (
                  <option key={s.id} value={s.id}>{s.name}</option>
                ))}
              </select>
            </div>
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
