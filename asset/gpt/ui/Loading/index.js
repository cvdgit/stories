import React from 'react';
import "./Loading.css";

export function Loading(props) {
  const {text, type, color} = props

  return (
    <div className="loading">
      <div className={`loading-inner ${type}`}>
        <div className="loading-line">
          {[1, 2, 3, 4].map(item => <div key={item} className={`loading-bar bar-${item}`} style={{backgroundColor: color}}/>)}
        </div>
        {text && <div className="text">{text}</div>}
      </div>
    </div>
  )
}

Loading.defaultProps = {
  type: 'circle', // normal|circle
  text: null
}
