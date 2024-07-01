import React, {useId} from "react";

export default function UploadImageMap({imageUploadHandler}) {
  const id = useId()

  const fileChangeHandler = (e) => {
    if (!e.target.files) {
      return;
    }
    imageUploadHandler(e.target.files[0]);
  }

  return (
    <div>
      <h4>Ментальная карта не загружена</h4>
      <div>
      <label htmlFor={id + "-image-upload"} className="button">
                <span>
                  <span className="menu__icon">
                    <svg viewBox="0 0 15 15" width="15" height="15" className="i"
                         focusable="false"><title>Upload</title><desc>Upload icon</desc><polygon
                      points="6.52941176 3.52941176 6.52941176 9.70588235 8.29411765 9.70588235 8.29411765 3.52941176 10.4117647 3.52941176 7.41176471 0 4.41176471 3.52941176"></polygon><path
                      d="M14.0625,14.1176471 L0.9375,14.1176471 C0.375,14.1176471 0,13.7647059 0,13.2352941 L0,5.29411765 C0,4.76470588 0.375,4.41176471 0.9375,4.41176471 L3.75,4.41176471 L3.75,6.17647059 L1.875,6.17647059 L1.875,12.3529412 L13.125,12.3529412 L13.125,6.17647059 L11.25,6.17647059 L11.25,4.41176471 L14.0625,4.41176471 C14.625,4.41176471 15,4.76470588 15,5.29411765 L15,13.2352941 C15,13.7647059 14.625,14.1176471 14.0625,14.1176471"></path></svg>
                  </span>Загрузить изображение
                </span>
      </label>
      <input onChange={fileChangeHandler}
             accept="image/png, image/jpeg"
             id={id + "-image-upload"} type="file" style={{display: "none"}}/>
      </div>
    </div>
  )
}
