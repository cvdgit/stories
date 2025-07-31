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
          {!isTreeView && <div style={{marginBottom: '20px'}}>
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
          <div style={{marginBottom: '20px'}}>
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
          <div style={{marginBottom: '20px'}}>
            <select value={String(state.settings?.threshold || '80')} onChange={(e) => {
              settings.threshold = e.target.value === '' ? null : Number(e.target.value)
              setSettings(settings)
              dispatch({
                type: 'update_settings',
                payload: settings
              })
            }} style={{width: '100%', padding: '10px'}}>
              <option value="">Выберите</option>
              <option value="95">95</option>
              <option value="80">80</option>
              <option value="60">60</option>
              <option value="40">40</option>
              <option value="20">20</option>
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
