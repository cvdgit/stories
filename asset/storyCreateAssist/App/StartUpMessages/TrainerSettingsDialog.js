import React, {forwardRef} from "react";
import Dialog from "../Dialog";

const TrainerSettingsDialog = forwardRef(({dialogProps, open, setOpen, mentalMaps, saveSettings}, ref) => {

  return <Dialog nodeRef={ref} hideHandler={() => setOpen(false)} addContentClassName="smallContent">
    <div>
      {mentalMaps.map((map, i) => <div key={i}>
        <label style={{display: 'flex', flexDirection: 'row', alignItems: 'center', gap: '10px'}} htmlFor={map.type}>
          <input style={{zoom: '1.5'}} id={map.type} checked={map.create} onChange={e => {
            saveSettings(prevState => ([...prevState].map(m => {
              if (m.type === map.type) {
                return {...m, create: e.target.checked}
              }
              return m;
            })));
          }} type="checkbox"/> {map.title}
        </label>
      </div>)}
    </div>
  </Dialog>
});

export default TrainerSettingsDialog;
