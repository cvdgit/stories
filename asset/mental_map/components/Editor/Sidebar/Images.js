import React, {useRef, useState} from 'react'
import './Image.css'
import {Draggable, Droppable} from "react-beautiful-dnd";
import Image from "./Image";
import SliderMenu from "./SliderMenu";
import {useImages} from "../../App/App";
import { v4 as uuidv4 } from 'uuid'

export default function Images({addImageHandler}) {
  const {state: images, dispatch: imagesDispatch} = useImages()
  const ref = useRef()
  const [files, setFiles] = useState(new Map())

  const fileChangeHandler = (e) => {
    if (!e.target.files) {
      return;
    }

    const files = new Map();
    Array.from(e.target.files).map(file => {

      const id = uuidv4();
      files.set(id, file);

      const item = {
        id,
        url: null,
        width: null,
        height: null,
        fileName: null
      }

      imagesDispatch({
        type: 'add_image',
        payload: item
      });
    });

    setFiles(files);
  }

  const getFile = (id) => {
    const file = files.get(id);
    files.delete(id);
    return file;
  }

  const uploadCompleteHandler = (imageItem, payload) => {
    const item = {
      id: imageItem.id,
      url: payload.url,
      width: payload.width,
      height: payload.height
    }
    imagesDispatch({
      type: 'update_image_item',
      payload: item
    })
  }

  return (

    <div className="sidebar-list">
      <div className="sidebar-list__title">
        <span>Изображения</span>
      </div>
      {images.length
        ? (
          <div className="block-edit-gallery">
            <Droppable ignoreContainerClipping={false} isDropDisabled={true} droppableId="image-list">
              {(droppableProvided, droppableSnapshot) => (
                <div ref={droppableProvided.innerRef} style={{display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '2rem'}}>
                  {images.map((imageItem, index) => (
                    <Draggable key={index} draggableId={`image-${index}`} index={index}>
                      {(provided, snapshot) => {
                        return (
                          <div ref={provided.innerRef} {...provided.draggableProps} {...provided.dragHandleProps}>
                            <Image imageItem={imageItem} file={getFile(imageItem.id)} uploadCompleteHandler={(response) => uploadCompleteHandler(imageItem, response)} />
                          </div>
                        )
                      }}
                    </Draggable>
                  ))}
                  {droppableProvided.placeholder}
                </div>
              )}
            </Droppable>
          </div>
        )
        : (<div className="sidebar-list__empty-message">
          Нажмите ниже, чтобы добавить изображения.
        </div>)}
      <div className="sidebar-list__footer">
        <div className="block-edit-field-upload brand--ui">
          <div className="block-edit-field-upload__inner">
            <button onClick={() => ref.current.click()} className="menu__trigger button button--outline" tabIndex="0">
              Добавить изображения
            </button>
            <input ref={ref} multiple={true} onChange={fileChangeHandler}
                   accept="image/png, image/jpeg"
                   type="file" style={{display: "none"}}/>
          </div>
        </div>
      </div>
    </div>
  )
}
