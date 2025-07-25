import React, {useState} from "react";
import TextDialog from "./TextDialog";
import SettingsDialog from "./SettingsDialog";
import TitleChangeDialog from "./TitleChangeDialog";
import Prompts from "./Prompts";

export default function Toolbar({currentTitle, mentalMapId, schedules, setFormattedMapText}) {
  const [textDialogOpen, setTextDialogOpen] = useState(false)
  const [settingsDialogOpen, setSettingsDialogOpen] = useState(false)
  const [titleDialogOpen, setTitleDialogOpen] = useState(false)
  const [textFragmentCount, setTextFragmentCount] = useState(0)
  const [title, setTitle] = useState(currentTitle || '')
  const [promptsDialogOpen, setPromptsDialogOpen] = useState(false)

  const returnUrl = window.mentalMapReturnUrl || '/'

  return (
    <div>
      <div className="app-header">
        <div className="app-header__menu-btn">
          <a className="app-header__menu-close" href={returnUrl}>Назад</a>
        </div>
        <div className="app-header__title">
          <a
            href=""
            onClick={(e) => {
              e.preventDefault();
              setTitleDialogOpen(true);
            }}
            style={{
              color: 'black',
              display: 'flex',
              alignItems: 'center'
            }}
          >
            {title}
            <svg style={{marginLeft: '10px'}} width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="1.5"
                 stroke="currentColor" className="size-6">
              <path strokeLinecap="round" strokeLinejoin="round"
                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
            </svg>
          </a>
        </div>
        <div className="app-header__btn-group">
          <button onClick={() => {
            setPromptsDialogOpen(true)
          }} className="button button--default button--header-done"
                  type="button">Prompts
          </button>
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
      <TitleChangeDialog
        mentalMapId={mentalMapId}
        open={titleDialogOpen}
        setOpen={setTitleDialogOpen}
        currentTitle={title}
        setCurrentTitle={setTitle}
      />
      <Prompts promptsDialogOpen={promptsDialogOpen} setPromptsDialogOpen={setPromptsDialogOpen} />
    </div>
  )
}
