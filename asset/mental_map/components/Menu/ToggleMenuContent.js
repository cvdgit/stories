import React from "react";

function ToggleMenuContent({classNames, children}) {
  return (
    <div className={`menu__content ${classNames}`}>
      <div className="menu__listWrapper">
        <ul className="menu__list">
          {children}
        </ul>
      </div>
    </div>
  );
}

export default ToggleMenuContent;
