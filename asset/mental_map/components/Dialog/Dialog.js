import React from "react";
import './Dialog.css'

function Dialog({children, nodeRef, hideHandler, ...props}) {
  return (
    <div ref={nodeRef} className="dialog export-dialog-modal">
      <div onClick={hideHandler} className="dialog__overlay"></div>
      <div className="dialog__content" {...props}>
        <div className="dialog__close" onClick={hideHandler}>
          <i className="icon icon-remove">&times;</i>
        </div>
        <div className="export-dialog__content">
          {children}
          <div className="dialog-action" style={{paddingTop: '1rem'}}>
            <button onClick={hideHandler} className="button button--default button--outline" type="button">Закрыть</button>
          </div>
        </div>
      </div>
    </div>
  );
}

export default Dialog;
