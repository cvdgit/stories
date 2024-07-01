import React, {useEffect} from "react";
import {useMentalMap} from "../../App/App";

export default function ImageUploader({file, imageItem, uploadCompleteHandler}) {
  const {state} = useMentalMap()

  useEffect(() => {

    if (!file) {
      return;
    }

    const formData = new FormData();
    formData.append('mental_map_id', state.id);
    formData.append('type', 'image')
    formData.append('image', file);
    formData.append('image_item_id', imageItem.id)

    fetch('/admin/index.php?r=mental-map/image', {
      method: 'POST',
      body: formData,
    })
      .then((res) => res.json())
      .then((response) => {
        uploadCompleteHandler(response);
      })
      .catch((err) => console.error(err));
  }, []);

  return (
    <div className="upload-progress__wrapper">
      <div className="upload-progress">
        <svg className="upload-progress__pie" width="21px" height="21px" focusable="false">
          <circle className="upload-progress__border" cx="10.5" cy="10.5" r="8.5"></circle>
          <circle className="upload-progress__progress" cx="10.5" cy="10.5" r="8.5" strokeDasharray="53.407"
                  strokeDashoffset="50.73665"></circle>
        </svg>
        <div className="upload-progress__text">Загрузка изображения...</div>
      </div>
      <a style={{display: "none"}} className="upload-action-link" href="#">Отменить</a>
    </div>
  )
}
