import React, { forwardRef, useState } from 'react'
import Proptypes from 'prop-types'
import { Button } from '../Button';
import "./Textarea.css";

export const Textarea = forwardRef((props, ref) => {
  const {
    onChange,
    placeholder,
    className,
    showClear,
    disable,
    children,
    rows,
    maxHeight,
    value,
    defaultValue,
    transparent,
    onClear,
    ...rest
  } = props;
  const [content, setContent] = useState(value);
  const [height, setHeight] = useState('auto')

  function handleChange(event) {
    setHeight('auto');
    setHeight(`${event.target.scrollHeight}px`);
    setContent(event.target.value);
    onChange && onChange(event.target.value);
  }

  function handleClear() {
    console.log("handleClear")
    onChange && onChange("");
    onClear && onClear();
    setContent("");
  }

  const handleKeyPress = (event) => {
    if (event.key === "Enter") {
      event.preventDefault();
    }
    if (event.shiftKey && event.key === "Enter") {
      setContent(content + "\n");
      event.preventDefault();
    }
  }

  return (
    <div className={`textarea-box ${className}`}>
      <div className="textarea-box__inner">
        <textarea
          ref={ref}
          rows={rows}
          style={{ height }}
          onChange={handleChange}
          placeholder={placeholder}
          onKeyDown={handleKeyPress}
          className={`textarea ${transparent && "textarea__transparent"}`}
          value={content}
          {...rest}
        />
      </div>
      {showClear && <Button className="clear" type="icon" onClick={handleClear} icon="cancel" />}
    </div>
  );
});

Textarea.defaultProps = {
  showClear: false,
  disable: false,
  defaultValue: '',
  maxHeight: 200,
  placeholder: '',
  rows: '1',
  transparent: false,
  value: ''
};

Textarea.propTypes = {
  showClear: Proptypes.bool,
  transparent: Proptypes.bool,
  onClear: Proptypes.func,
  className: Proptypes.string,
  onChange: Proptypes.func,
  disable: Proptypes.bool,
  placeholder: Proptypes.string,
  maxHeight: Proptypes.number,
  rows: Proptypes.string,
}
