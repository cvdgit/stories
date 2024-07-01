import React from "react";

function ToggleMenuItem({icon, title, clickHandler, children}) {
  return (
    <li onClick={clickHandler} className="menu__item" role="menuitem" tabIndex="-1">
      {children ? children : (
        <>
          {icon && (<span className="menu__icon">{icon}</span>)}
          <span>{title}</span>
        </>
      )}
    </li>
  )
}

export default ToggleMenuItem;
