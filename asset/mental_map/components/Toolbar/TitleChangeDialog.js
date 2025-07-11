import React, {useEffect, useRef, useState} from 'react';
import Dialog from "../Dialog";
import {CSSTransition} from "react-transition-group";
import api from "../../Api";
export default function TitleChangeDialog({open, setOpen, mentalMapId, currentTitle, setCurrentTitle}) {
  const ref = useRef();
  const [title, setTitle] = useState(currentTitle || '')

  useEffect(() => {
    if (currentTitle === title) {
      return;
    }
    const timeoutId = setTimeout(() => api
      .post('/admin/index.php?r=mental-map/update-map-title', {
        payload: {
          id: mentalMapId,
          title
        }
      }), 500);
    return () => clearTimeout(timeoutId);
  }, [title]);

  return (
    <CSSTransition
      in={open}
      nodeRef={ref}
      timeout={200}
      classNames="dialog"
      unmountOnExit
    >
      <Dialog nodeRef={ref} hideHandler={() => {
        if (title !== currentTitle) {
          setCurrentTitle(title)
        }
        setOpen(false)
      }} style={{width: '60rem'}}>
        <h2 className="dialog-heading">Редактировать название ментальной карты</h2>
        <div style={{padding: '20px 0'}}>
          <input
            className="textarea"
            style={{minHeight: 'auto'}}
            type="text"
            value={title}
            onChange={e => {
              setTitle(e.target.value)
            }}
          />
        </div>
      </Dialog>
    </CSSTransition>
  )
}
