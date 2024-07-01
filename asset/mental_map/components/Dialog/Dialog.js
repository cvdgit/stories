import React from "react";
import './Dialog.css'

function Dialog({children, nodeRef, hideHandler}) {
  return (
    <div ref={nodeRef} className="dialog export-dialog-modal">
      <div onClick={hideHandler} className="dialog__overlay"></div>
      <div className="dialog__content">
        <div className="dialog__close">
          <i className="icon icon-remove"></i>
        </div>
        <div className="export-dialog__content">
          {children}
          <div className="dialog-action">
            <button onClick={hideHandler} className="button button--default button--outline" type="button">Закрыть</button>
          </div>
        </div>
      </div>
    </div>
  );
}

export default Dialog;
