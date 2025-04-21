import React, {useRef, useState} from 'react'
import './Image.css'
import Image from "./Image";
import {useImages, useMentalMap} from "../../App/App";
import {v4 as uuidv4} from 'uuid'
import api from "../../../Api";

function shuffle(array) {
  let counter = array.length;
  // While there are elements in the array
  while (counter > 0) {
    // Pick a random index
    const index = Math.floor(Math.random() * counter);
    // Decrease counter by 1
    counter--;
    // And swap the last element with it
    const temp = array[counter];
    array[counter] = array[index];
    array[index] = temp;
  }
  return array;
}

export default function Images({setNewImages}) {
  const {state, dispatch} = useMentalMap()
  const {state: images, dispatch: imagesDispatch} = useImages()
  const ref = useRef()
  const [files, setFiles] = useState(new Map())
  const [selectedImages, setSelectedImages] = useState([])

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

  const moveSelectedImageHandler = () => {

    const texts = (state.text || '')
      .split("\n\n")
      .filter(p => p !== '')

    const imagesToAdd = [...selectedImages]

    if (imagesToAdd.length === 0) {
      let imgNum = state.map.images.length
      if (imgNum < texts.length) {
        const shuffleImages = shuffle([...images])
        while (imgNum < texts.length) {
          imagesToAdd.push(shuffleImages.pop())
          imgNum++
        }
      }
    }

    const updatedList = [...images].filter(i => {
      return !(imagesToAdd.find(si => si.id === i.id) !== undefined)
    })

    imagesDispatch({
      type: 'update_images',
      payload: updatedList
    })

    let left = 10
    let top = 10
    let w = left
    let imgNum = state.map.images.length
    const ids = []
    imagesToAdd.map((si, index) => {

      w = w + si.width + 50
      if (index > 0) {
        left = left + si.width + 50
        if (w >= state.map.width) {
          left = 10
          top = top + si.height + 70
          w = 10
        }
      }

      dispatch({
        type: 'add_image_to_mental_map',
        payload: {
          ...si,
          left,
          top,
          text: texts[imgNum] ?? ''
        }
      })

      ids.push(si.id)

      imgNum++
    })

    setNewImages(ids)
    setSelectedImages([])
  }

  const checkBoxHandler = (imageItem, value) => {
    setSelectedImages((prevItems) => {
      if (value) {
        return [...prevItems, imageItem]
      }
      return prevItems.filter(i => i.id !== imageItem.id)
    })
  }

  const deleteImageHandler = (imageId) => {
    imagesDispatch({
      type: 'delete_image',
      imageId
    })
    api.post('/admin/index.php?r=mental-map/delete-image', {
      payload: {
        id: state.id,
        imageId,
      }
    })
  }

  return (
    <div className="sidebar-list">
      <div className="sidebar-list__title">
        <span>Изображения</span>
        <button onClick={moveSelectedImageHandler} type="button"
                className="button button--default button--header-done">+ выбранные
        </button>
      </div>
      {images.length
        ? (
          <div className="block-edit-gallery">
            <div
              style={{display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem'}}>
              {images.map((imageItem, index) => (
                <Image
                  key={index}
                  checked={!!selectedImages.find(i => i.id === imageItem.id)}
                  imageItem={imageItem}
                  file={getFile(imageItem.id)}
                  uploadCompleteHandler={(response) => uploadCompleteHandler(imageItem, response)}
                  checkBoxHandler={checkBoxHandler}
                  deleteImageHandler={deleteImageHandler}
                />
              ))}
            </div>
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
