import React, {useEffect, useRef, useState} from "react";
import {useImages, useMentalMap} from "../App/App";
import Images from "./Sidebar/Images";
import './Editor.css'
import UploadImageMap from "./ImageMap/UploadImageMap";
import UploadingImageMap from "./ImageMap/UploadingImageMap";
import api from "../../Api";
import SvgImageMap from "./SvgImageMap/SvgImageMap";

export default function Editor() {
  console.log('editor')
  const {state, dispatch} = useMentalMap()
  const {state: images, dispatch: imagesDispatch} = useImages()
  const [uploading, setUploading] = useState(false)
  const [file, setFile] = useState(null)
  const [imageMap, setImageMap] = useState(state.map)
  const [newImages, setNewImages] = useState([])
  const isFirstRender = useRef(true)

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
    if (isFirstRender.current) {
      isFirstRender.current = false;
      return;
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
                <div className="quiz-authoring__main"
                     style={{display: 'flex', flexDirection: 'column', justifyContent: 'space-between'}}>
                  {uploading
                    ? <UploadingImageMap file={file} uploadCompleteHandler={uploadCompleteHandler}/>
                    : state.map.url
                      ? <SvgImageMap mapImage={state.map} newImages={newImages} setNewImages={setNewImages}/>
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
