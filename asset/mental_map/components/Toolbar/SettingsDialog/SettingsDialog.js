import React, {useEffect, useId, useRef, useState} from "react";
import {CSSTransition} from "react-transition-group";
import Dialog from "../../Dialog";
import api from "../../../Api";
import {useMentalMap} from "../../App/App";

export default function SettingsDialog({open, setOpen, mentalMapId, schedules}) {
  const settingsDialogRef = useRef();
  const checkId = useId();
  const {state, dispatch} = useMentalMap();
  const isFirstRender = useRef(true);
  const [settings, setSettings] = useState(state?.settings || {});

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
          <div>
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
        </div>
      </Dialog>
    </CSSTransition>
  )
}
