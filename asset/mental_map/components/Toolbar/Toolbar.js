import React, {useState} from "react";
import TextDialog from "./TextDialog";
import SettingsDialog from "./SettingsDialog";

export default function Toolbar({title, mentalMapId, schedules, setFormattedMapText, isTreeView}) {
  const [textDialogOpen, setTextDialogOpen] = useState(false)
  const [settingsDialogOpen, setSettingsDialogOpen] = useState(false)
  const [textFragmentCount, setTextFragmentCount] = useState(0)

  const returnUrl = window.mentalMapReturnUrl || '/'

  return (
    <div>
      <div className="app-header">
        <div className="app-header__menu-btn">
          <a className="app-header__menu-close" href={returnUrl}>Назад</a>
        </div>
        <div className="app-header__title">{title}</div>
        <div className="app-header__btn-group">
          <button onClick={() => {
            setTextDialogOpen(true)
          }} className="button button--default button--header-done"
                  type="button">Текст {textFragmentCount > 0 && (<span> ({textFragmentCount})</span>)}
          </button>
          <button onClick={() => {
            setSettingsDialogOpen(true)
          }} className="button button--default button--header-done"
                  type="button">Настройки
          </button>
        </div>
      </div>

      <TextDialog
        setTextFragmentCount={setTextFragmentCount}
        mentalMapId={mentalMapId}
        open={textDialogOpen} setOpen={setTextDialogOpen}
        setFormattedMapTextGlobal={setFormattedMapText}
      />
      <SettingsDialog
        schedules={schedules}
        mentalMapId={mentalMapId}
        open={settingsDialogOpen}
        setOpen={setSettingsDialogOpen}
      />
    </div>
  )
}
