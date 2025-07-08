import React, {useState} from "react";

export default function DrawToggler({onChangeHandler, currentFragment, onDeleteHandler, onCopyHandler}) {
  const [activeValue, setActiveValue] = useState('move')

  const itemChange = (e) => {
    setActiveValue(e.target.value)
    onChangeHandler(e.target.value)
  }

  const items = [
    (<label onClick={() => {
      onChangeHandler('move')
      setActiveValue('move')
    }} key={0} className={`btn btn-default ${activeValue === 'move' ? 'active' : ''}`} htmlFor="">
      <input onChange={itemChange} style={{margin: '4px 0 0'}} type="radio" name="shape" value="move" autoComplete="off"
             defaultChecked={activeValue === 'move'}/>
      <i>
        <svg id="Move--Streamline-Carbon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" height="16" width="16">
          <desc></desc>
          <defs></defs>
          <title>move</title>
          <path
            d="m12.5 5.5 -0.705 0.705L13.085 7.5 8.5 7.5l0 -4.585 1.295 1.29L10.5 3.5l-2.5 -2.5 -2.5 2.5 0.705 0.705L7.5 2.915 7.5 7.5l-4.585 0 1.29 -1.295L3.5 5.5l-2.5 2.5 2.5 2.5 0.705 -0.705L2.915 8.5 7.5 8.5l0 4.585 -1.295 -1.29L5.5 12.5l2.5 2.5 2.5 -2.5 -0.705 -0.705L8.5 13.085 8.5 8.5l4.585 0 -1.29 1.295L12.5 10.5l2.5 -2.5 -2.5 -2.5z"
            fill="#000000" strokeWidth="0.5"></path>
          <path id="_Transparent_Rectangle_" d="M0 0h16v16H0Z" fill="none" strokeWidth="0.5"></path>
        </svg>
      </i>
    </label>),
    (<label onClick={() => {
      onChangeHandler('rect')
      setActiveValue('rect')
    }} key={1} className={`btn btn-default ${activeValue === 'rect' ? 'active' : ''}`} htmlFor="">
      <input onChange={itemChange} style={{margin: '4px 0 0'}} type="radio" name="shape" value="rect" autoComplete="off"
             defaultChecked={activeValue === 'rect'}/>
      Нарисовать
    </label>)
  ]

  return (
    <div className="btn-group" onChange={itemChange} data-toggle="buttons"
         style={{justifyContent: 'space-between', alignItems: 'center'}}>
      <div>{items}</div>
      {currentFragment && <div>
        <button onClick={onCopyHandler} type="button" style={{width: '30px', height: '30px', padding: '6px'}}>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="1.5"
               stroke="currentColor" className="size-6">
            <path strokeLinecap="round" strokeLinejoin="round"
                  d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75"/>
          </svg>
        </button>
        <button onClick={onDeleteHandler} type="button" style={{width: '30px', height: '30px', padding: '6px'}}>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="1.5"
               stroke="currentColor" className="size-6">
            <path strokeLinecap="round" strokeLinejoin="round"
                  d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
          </svg>
        </button>
      </div>}
    </div>
  )
}
