import React from 'react';
import imageUrl from '../../images/wikids.png'
import "./Avatar.css";

export const Avatar = (props) => {
  const {src, altText, className, size, circle} = props
  return (
    <div className={`avatar ${circle && "avatar__circle"} ${className}`} style={{width: size, height: size}}>
      <img src={src || imageUrl} alt={altText}/>
    </div>
  );
};

Avatar.defaultProps = {
  src: null,
  altText: 'User Avatar',
  circle: true,
};
