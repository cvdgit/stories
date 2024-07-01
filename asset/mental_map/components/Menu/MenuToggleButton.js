import React from "react";

function MenuToggleButton({children, classNames, toggleHandler, ...props}) {
  return (
    <button {...props} onClick={toggleHandler} className={`menu__trigger ${classNames}`} tabIndex="0">{children}</button>
  );
}

export default MenuToggleButton;
