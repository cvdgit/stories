import React, { forwardRef } from 'react';
import "./Icon.css";

export const Icon = forwardRef((props, ref) => {
  const { type, children, className, onClick, ...rest } = props
  const handleClick = (event) => {
    onClick && onClick();
    event.stopPropagation();
  }
  return <i ref={ref} {...rest} onClick={handleClick} className={`icon ico ico-${type} ${className}`} >{children}</i>
})
