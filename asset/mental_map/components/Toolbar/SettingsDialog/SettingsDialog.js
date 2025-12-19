import React, {useEffect, useId, useRef, useState} from "react";
import {CSSTransition} from "react-transition-group";
import Dialog from "../../Dialog";
import api from "../../../Api";
import {useMentalMap} from "../../App/App";

export default function SettingsDialog({open, setOpen, mentalMapId, schedules}) {
  const settingsDialogRef = useRef();
  const checkId = useId();
  const planCheckId = useId();
  const tooltipCheckId = useId();
  const textCheckId = useId();
  const {state, dispatch} = useMentalMap();
  const isFirstRender = useRef(true);
  const [settings, setSettings] = useState(state?.settings || {});
  const scheduleElemId = useId()
  const thresholdElemId = useId()
  const promptElemId = useId()
  const recognitionElemId = useId();
  const [promptId, setPromptId] = useState(state.settings?.promptId || '')
  const [recognitionLang, setRecognitionLang] = useState(state.settings?.recognitionLang || '');
  const [prompts, setPrompts] = useState([])

  const isTreeView = Boolean(state.treeView)

  useEffect(() => {
    if (isFirstRender.current) {
      isFirstRender.current = false;
      return;
    }
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
    if (!open) {
      return
    }
    /*if (!loadPrompts) {
      return
    }*/

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
        //promptsLoaded()
      })

  }, [open/*, loadPrompts*/]);

  return (
    <CSSTransition
      in={open}
      nodeRef={settingsDialogRef}
      timeout={200}
      classNames="dialog"
      unmountOnExit
    >
      <Dialog nodeRef={settingsDialogRef} hideHandler={() => setOpen(false)} style={{width: '60rem'}}>
        <h2 className="dialog-heading">Настройки</h2>
        <div style={{paddingTop: '20px'}}>
          {!isTreeView && <div style={{marginBottom: '10px'}}>
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
          </div>}
          {isTreeView && <div style={{marginBottom: '20px'}}>
            <label htmlFor={planCheckId}>
              <input
                id={planCheckId}
                onChange={(e) => {
                  settings.planTreeView = !Boolean(state?.settings?.planTreeView)
                  setSettings(settings)
                  dispatch({
                    type: 'update_settings',
                    payload: settings
                  })
                }}
                checked={Boolean(state?.settings?.planTreeView)}
                type="checkbox"
              /> Ментальная карта в виде плана</label>
          </div>}
          <div style={{marginBottom: '10px'}}>
            <label style={{display: 'block', marginBottom: '2px'}} htmlFor={scheduleElemId}>Расписание повторений:</label>
            <select id={scheduleElemId} value={String(state?.settings?.scheduleId)} onChange={(e) => {
              settings.scheduleId = e.target.value === '' ? null : Number(e.target.value)
              setSettings(settings)
              dispatch({
                type: 'update_settings',
                payload: settings
              })
            }} style={{width: '100%', padding: '10px'}}>
              <option value="">Выберите</option>
              {schedules.map(s => (
                <option key={s.id} value={s.id}>{s.name}</option>
              ))}
            </select>
          </div>
          <div style={{marginBottom: '10px'}}>

            <label htmlFor={thresholdElemId} style={{display: 'block', marginBottom: '2px'}}>Точность пересказа:</label>
            <select id={thresholdElemId} value={String(state.settings?.threshold || '80')} onChange={(e) => {
              settings.threshold = e.target.value === '' ? null : Number(e.target.value)
              setSettings(settings)
              dispatch({
                type: 'update_settings',
                payload: settings
              })
            }} style={{width: '100%', padding: '10px'}}>
              <option value="">Выберите</option>
              <option value="95">95</option>
              <option value="90">90</option>
              <option value="85">85</option>
              <option value="80">80</option>
              <option value="60">60</option>
              <option value="40">40</option>
              <option value="20">20</option>
            </select>
          </div>

          <div style={{marginBottom: '20px'}}>
            <label htmlFor={promptElemId} style={{display: 'block', marginBottom: '2px'}}>Проверочный промт:</label>
            <select id={promptElemId} value={promptId} onChange={(e) => {
              setPromptId(e.target.value)
              settings.promptId = e.target.value === '' ? null : String(e.target.value)
              setSettings(settings)
              dispatch({
                type: 'update_settings',
                payload: settings
              })
            }} style={{width: '100%', padding: '10px'}}>
              <option value="">По умолчанию</option>
              {prompts.map((p, i) => (
                <option key={i} value={p.id}>{p.name}</option>
              ))}
            </select>
          </div>

          <div style={{marginBottom: '20px'}}>
            <label htmlFor={recognitionElemId} style={{display: 'block', marginBottom: '2px'}}>Язык распознавания:</label>
            <select id={recognitionElemId} value={recognitionLang} onChange={(e) => {
              setRecognitionLang(e.target.value);
              settings.recognitionLang = e.target.value === '' ? null : String(e.target.value);
              setSettings(settings);
              dispatch({
                type: 'update_settings',
                payload: settings
              });
            }} style={{width: '100%', padding: '10px'}}>
              <option value="">По умолчанию</option>
              <option value="ru-RU">Русский</option>
              <option value="en-US">Английский</option>
            </select>
          </div>

          {!isTreeView && <div style={{marginBottom: '20px'}}>
            <label htmlFor={tooltipCheckId}>
              <input
                id={tooltipCheckId}
                onChange={(e) => {
                  settings.hideTooltip = !Boolean(state.settings?.hideTooltip)
                  setSettings(settings)
                  dispatch({
                    type: 'update_settings',
                    payload: settings
                  })
                }}
                checked={Boolean(state.settings?.hideTooltip)}
                type="checkbox"
              /> Скрывать подсказки на изображении</label>
          </div>}

          {!isTreeView && <div style={{marginBottom: '20px'}}>
            <label htmlFor={textCheckId}>
              <input
                id={textCheckId}
                onChange={(e) => {
                  settings.hideText = !Boolean(state.settings?.hideText)
                  setSettings(settings)
                  dispatch({
                    type: 'update_settings',
                    payload: settings
                  })
                }}
                checked={Boolean(state.settings?.hideText)}
                type="checkbox"
              /> Скрывать текст при проговаривании</label>
          </div>}
        </div>
      </Dialog>
    </CSSTransition>
  )
}
