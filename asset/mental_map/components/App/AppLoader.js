import React from "react";

function AppLoader(props) {
  return (
    <div className="wikids-longread-loader">
      <div className="wikids-longread-loader__container">
        <div className="longread-loader">
          <div className="longread-loader__spinner"></div>
          <div className="longread-loader__text">{props.text}</div>
        </div>
      </div>
    </div>
  );
}

export default AppLoader;
