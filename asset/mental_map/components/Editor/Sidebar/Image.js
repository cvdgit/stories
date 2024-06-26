import React from 'react'
import ImageUploader from "./ImageUploader";

export default function Image({imageItem, file, uploadCompleteHandler,}) {
  return (
    <div className="block-edit-field-drag-item block-edit-field-drag-item--large">
      <div className="block-edit-field-drag-item__container">
        <div className="1block-edit-field-image__row">
          {imageItem.url ? (
            <>
              <div className="block-edit-field-image__image-container">
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
