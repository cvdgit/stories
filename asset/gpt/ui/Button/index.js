import React, { forwardRef } from 'react'
import PropTypes from 'prop-types'
import "./Button.scss";

export const Button = forwardRef((props, ref) => {
  const { className, style, size, ghost, block, icon, type, children, ...rest } = props
  return (
    <button className={`button ${size} ${type === "icon" ? "ico" : ""} ${type} ${ghost ? "ghost" : ""} ${block ? "block" : ""} ${className ?? ""}`} ref={ref} {...rest}>
      {icon && <i className={`ico ico-${icon}`} />}
      {children}
    </button>
  )
})

Button.defaultProps = {
  ghost: false,
  size: 'normal',
  icon: '',
  type: 'normal',
  block: false,
  style: {}
}

Button.propTypes = {
  ghost: PropTypes.bool,
  size: PropTypes.string,
  icon: PropTypes.string,
  block: PropTypes.bool,
  type: PropTypes.string
}
