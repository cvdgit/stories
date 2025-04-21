import React, {useEffect, useRef, useState} from "react";
import {useImages, useMentalMap} from "../App/App";
import Images from "./Sidebar/Images";
import './Editor.css'
import ImageMap from "./ImageMap";
import UploadImageMap from "./ImageMap/UploadImageMap";
import UploadingImageMap from "./ImageMap/UploadingImageMap";
import api from "../../Api";
import SvgImageMap from "./SvgImageMap/SvgImageMap";

export default function Editor() {
  console.log('editor')
  const {state, dispatch} = useMentalMap()
  const {state: images, dispatch: imagesDispatch} = useImages()
  const mouseLocationRef = useRef({x: 0, y: 0})
  const [uploading, setUploading] = useState(false)
  const [file, setFile] = useState(null)
  const [imageMap, setImageMap] = useState(state.map)
  const [newImages, setNewImages] = useState([])

  const logMousePosition = e => {
    mouseLocationRef.current.x = e.clientX;
    mouseLocationRef.current.y = e.clientY;
  }

  useEffect(() => {
    window.addEventListener("mousemove", logMousePosition);
  }, [])

  const dragEndHandler = (item) => {

    if (!item.destination) return;

    if (item.destination.droppableId !== 'image') {
      return
    }

    const updatedList = Array.from(images)
    const [reorderedItem] = updatedList.splice(item.source.index, 1)

    const el = document.getElementById('container')
    const rect = el.getBoundingClientRect()
    let {x, y} = mouseLocationRef.current;
    x = x - rect.x
    y = y - rect.y

    imagesDispatch({
      type: 'update_images',
      payload: updatedList
    })

    dispatch({
      type: 'add_image_to_mental_map',
      payload: {
        ...reorderedItem,
        left: parseInt(x.toString()),
        top: parseInt(y.toString()),
        text: ''
      }
    })
  }

  const imageUploadHandler = (file) => {
    setUploading(true);
    setFile(file);
  }

  const uploadCompleteHandler = (payload) => {
    setUploading(false);
    setFile(null);
    dispatch({
      type: 'upload_mental_map_image',
      payload: {
        url: payload.url,
        width: payload.width,
        height: payload.height
      }
    })
  };

  useEffect(() => {
    if (JSON.stringify(state.map) === JSON.stringify(imageMap)) {
      //return
    }
    const timeoutId = setTimeout(() => api
      .post('/admin/index.php?r=mental-map/update-map', {
        payload: {
          id: state.id,
          map: state.map
        }
      }), 500);
    return () => clearTimeout(timeoutId);
  }, [state.map]);

  return (
    <div>

        <div className="author-layout author-layout--quiz">
          <div className="author-layout__sidebar author-layout__sidebar--scroll">
            <Images setNewImages={setNewImages}/>
          </div>
          <div className="author-layout__container">
            <div className="author-layout__content">
              <div className="quiz-authoring">
                <div className="quiz-authoring__row">
                  <div className="quiz-authoring__main">
                    {uploading
                      ? <UploadingImageMap file={file} uploadCompleteHandler={uploadCompleteHandler}/>
                      : state.map.url
                        ? <SvgImageMap mapImage={state.map} newImages={newImages}/>
                        : <UploadImageMap imageUploadHandler={imageUploadHandler}/>
                    }
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

    </div>
  )
}
