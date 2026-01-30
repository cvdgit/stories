import React, {forwardRef} from "react";
import Dialog from "../Dialog";
import styles from "./TrainerSettingsDialog.module.css";

const TrainerSettingsDialog = forwardRef(({dialogProps, open, setOpen, mentalMaps, saveSettings}, ref) => {

  return <Dialog nodeRef={ref} hideHandler={() => setOpen(false)} addContentClassName="smallContent">
    <div style={{minWidth: '600px'}}>
      {mentalMaps.map((map, i) => <div key={i} className={styles.contentRow}>
        <label className={styles.rowLabel} htmlFor={map.type}>
          <input style={{zoom: '1.5'}} id={map.type} checked={map.create} onChange={e => {
            saveSettings(prevState => ([...prevState].map(m => {
              if (m.type === map.type) {
                return {...m, create: e.target.checked}
              }
              return m;
            })));
          }} type="checkbox"/> {map.title}
        </label>
        <label className={styles.rowLabel} htmlFor={`${map.type}-${i}`}>
          <input id={`${map.type}-${i}`} type="checkbox" checked={map.required} style={{zoom: '1.5'}} onChange={e => {
            saveSettings(prevState => ([...prevState].map(m => {
              if (m.type === map.type) {
                return {...m, required: e.target.checked}
              }
              return m;
            })));
          }} /> Обязательно
        </label>
      </div>)}
    </div>
  </Dialog>
});

export default TrainerSettingsDialog;
