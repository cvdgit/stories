import React from 'react'
import ImageUploader from "./ImageUploader";

export default function Image({imageItem, file, uploadCompleteHandler, checkBoxHandler, checked}) {
  console.log('ch', checked)
  return (
    <div className="block-edit-field-drag-item block-edit-field-drag-item--large">
      <div className="block-edit-field-drag-item__container">
        <div className="block-edit-field-image__row">
          {imageItem.url ? (
            <>
              <div className="block-edit-field-image__image-container">
                <input id={imageItem.id} checked={checked} onChange={checkBoxHandler} type="checkbox"/>
                <img style={{width: `${imageItem.width}px`}} alt="" className="block-edit-field-image__image"
                     src={imageItem.url}/>
              </div>
            </>
          ) : <ImageUploader file={file} imageItem={imageItem} uploadCompleteHandler={(response) => {
            uploadCompleteHandler(response, imageItem);
          }}/>
          }
        </div>
      </div>
    </div>
  )
}
