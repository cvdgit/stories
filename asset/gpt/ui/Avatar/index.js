import React from 'react';
import imageUrl from '../../images/wikids.png'
import "./Avatar.css";

export const Avatar = ({className, size, src = null, altText = 'User Avatar', circle = true}) => {
  return (
    <div className={`avatar ${circle && "avatar__circle"} ${className}`} style={{width: size, height: size}}>
      <img src={src || imageUrl} alt={altText}/>
    </div>
  );
};
