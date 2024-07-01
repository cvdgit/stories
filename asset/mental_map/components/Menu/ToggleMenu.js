import React from "react";
import "./ToggleMenu.css"

function ToggleMenu({toggle, classNames, children}) {

  return (
    <div className={"menu" + (classNames ? " " + classNames : "") + (toggle ? " menu--active" : "")}>
      {children}
    </div>
  );
}

export default ToggleMenu;
