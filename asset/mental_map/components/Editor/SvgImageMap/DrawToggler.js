import React, {useState} from "react";

export default function DrawToggler({onChangeHandler}) {
  const [activeValue, setActiveValue] = useState('move')

  const itemChange = (e) => {
    console.log('ch', e.target.value)
    setActiveValue(e.target.value)
    onChangeHandler(e.target.value)
  }

  const items = [
    (<label onClick={() => {
      onChangeHandler('move')
      setActiveValue('move')
    }} key={0} className={`btn btn-default ${activeValue === 'move' ? 'active' : ''}`} htmlFor="">
      <input onChange={itemChange} style={{margin: '4px 0 0'}} type="radio" name="shape" value="move" autoComplete="off" defaultChecked={activeValue === 'move'} />
      <i>
        <svg id="Move--Streamline-Carbon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" height="16" width="16">
          <desc></desc>
          <defs></defs>
          <title>move</title>
          <path d="m12.5 5.5 -0.705 0.705L13.085 7.5 8.5 7.5l0 -4.585 1.295 1.29L10.5 3.5l-2.5 -2.5 -2.5 2.5 0.705 0.705L7.5 2.915 7.5 7.5l-4.585 0 1.29 -1.295L3.5 5.5l-2.5 2.5 2.5 2.5 0.705 -0.705L2.915 8.5 7.5 8.5l0 4.585 -1.295 -1.29L5.5 12.5l2.5 2.5 2.5 -2.5 -0.705 -0.705L8.5 13.085 8.5 8.5l4.585 0 -1.29 1.295L12.5 10.5l2.5 -2.5 -2.5 -2.5z" fill="#000000" strokeWidth="0.5"></path>
          <path id="_Transparent_Rectangle_" d="M0 0h16v16H0Z" fill="none" strokeWidth="0.5"></path>
        </svg>
      </i>
    </label>),
    (<label onClick={() => {
      onChangeHandler('rect')
      setActiveValue('rect')
    }} key={1} className={`btn btn-default ${activeValue === 'rect' ? 'active' : ''}`} htmlFor="">
      <input onChange={itemChange} style={{margin: '4px 0 0'}} type="radio" name="shape" value="rect" autoComplete="off" defaultChecked={activeValue === 'rect'} />
      Нарисовать
    </label>)
  ]

  return (
    <div className="btn-group" onChange={itemChange} data-toggle="buttons">
      {items}
    </div>
  )
}
