import React from 'react'
import ImageUploader from "./ImageUploader";

export default function Image({imageItem, file, uploadCompleteHandler, checkBoxHandler, checked, deleteImageHandler}) {
  return (
    <div className="block-edit-field-drag-item block-edit-field-drag-item--large">
      <div className="block-edit-field-drag-item__container">
        <div className="1block-edit-field-image__row" style={{padding: '0'}}>
          {imageItem.url ? (
            <>
              <div className="block-edit-field-image__image-container">
                <div style={{position: 'relative'}}>
                  <input
                    id={imageItem.id}
                    checked={checked}
                    onChange={(e) => {
                      checkBoxHandler(imageItem, e.target.checked)
                    }}
                    type="checkbox"
                    style={{
                      position: 'absolute',
                      bottom: '10px',
                      left: '50%',
                      transform: 'translate(-50%, 0)'
                    }}
                  />
                  <img onClick={() => {
                    checkBoxHandler(imageItem, !checked)
                  }} style={{width: `${imageItem.width}px`}} alt="" className="block-edit-field-image__image"
                       src={imageItem.url} draggable={false}/>
                </div>
                <button onClick={() => {
                  deleteImageHandler(imageItem.id)
                }} title="Удалить изображение" className="delete-btn" style={{marginTop: '4px'}}
                        type="button">&times;</button>
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
