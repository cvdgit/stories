import React, {useEffect, useRef, useState} from "react";
import {Droppable} from "react-beautiful-dnd";
import Moveable from "react-moveable";
import {useImages, useMentalMap} from "../../App/App";
import Dialog from "../../Dialog";
import {CSSTransition} from 'react-transition-group';

let scale = 1

const Editable = {
  name: "editable",
  props: [
    'removeHandler'
  ],
  events: [],
  render(moveable, React) {
    const rect = moveable.getRect();
    const {pos2} = moveable.state;

    // Add key (required)
    // Add class prefix moveable-(required)
    const EditableViewer = moveable.useCSS("div", `
        {
            position: absolute;
            left: 0px;
            top: 0px;
            will-change: transform;
            transform-origin: 0px 0px;
        }
        .custom-button {
            width: 24px;
            height: 24px;
            margin-bottom: 4px;
            background: #dc3545;
            border-radius: 4px;
            appearance: none;
            border: 0;
            color: white;
            font-weight: bold;
        }
            `);
    return <EditableViewer key={"editable-viewer"} className={"moveable-editable"} style={{
      transform: `translate(${pos2[0]}px, ${pos2[1]}px) rotate(${rect.rotation}deg) translate(10px)`,
    }}>
      <button className="custom-button" onClick={() => {
        moveable.props.removeHandler()
        //moveable.state.target.parentElement.remove()
      }}> -
      </button>
    </EditableViewer>;
  },
};

export default function ImageMap({mapImage}) {
  const [open, setOpen] = useState(false)
  const ref = useRef(null)
  const {state, dispatch} = useMentalMap()
  const {dispatch: imagesDispatch} = useImages()
  const [currentImageItem, setCurrentImageItem] = useState(null)
  const [currentText, setCurrentText] = useState(null)

  useEffect(() => {

    const resizeHandler = () => {
      const container = document.getElementById('mentalMapContainer')
      const elem = document.getElementById('container')

      const width = container.offsetWidth
      const height = container.offsetHeight

      scale = Math.min(width / 1280, height / 720)

      elem.style.width = '1280px'
      elem.style.height = '720px'

      if (scale === 1) {
        elem.style.zoom = '';
        elem.style.left = '';
        elem.style.top = '';
        elem.style.bottom = '';
        elem.style.right = '';
        elem.style.transform = '';
      } else {
        elem.style.zoom = '';
        elem.style.left = '50%';
        elem.style.top = '50%';
        elem.style.bottom = 'auto';
        elem.style.right = 'auto';
        elem.style.transform = `translate(-50%, -50%) scale(${scale})`
      }
    }

    resizeHandler()

    window.addEventListener('resize', resizeHandler)

    return () => window.removeEventListener('resize', resizeHandler)
  }, []);

  return (
    <>
      <Droppable droppableId="image">
        {(droppableProvided, droppableSnapshot) => (
          <div id="mentalMapContainer" ref={droppableProvided.innerRef}
               className="mental-map-container">
            <div id="container" className="container" style={{position: 'absolute', width: '100%', height: '100%'}}>
              <div style={{position: 'relative', width: '100%', height: '100%'}}>
                <img draggable={false} src={mapImage.url} style={{height: '100%'}} alt=""/>
                {mapImage.images.map((image, i) => (
                  <div key={`im-${i}`}>
                    <img
                      style={{
                        left: '0',
                        top: '0',
                        transform: `translate(${Math.abs(image.left)}px, ${Math.abs(image.top)}px)`,
                        width: `${image.width}px`,
                        height: `${image.height}px`
                      }}
                      className={`mental-pic mental-pic-${i}`}
                      src={image.url}
                      onClick={() => {
                        setCurrentImageItem(image)
                        setCurrentText(image.text)
                        setOpen(true)
                      }}
                      alt=""/>
                    <Moveable
                      target={`.mental-pic-${i}`}
                      draggable={true}
                      resizable={true}
                      keepRatio={true}
                      ables={[Editable]}
                      props={{
                        editable: true,
                        removeHandler: () => {
                          dispatch({
                            type: 'remove_image_from_mental_map',
                            imageId: image.id,
                          })
                          imagesDispatch({
                            type: 'add_image',
                            payload: {...image}
                          })
                        }
                      }}
                      //throttleResize={0}
                      //throttleDrag={1}
                      //scalable={true}
                      snappable={true}
                      //origin={true}
                      bounds={{
                        top: 0,
                        left: 0,
                        right: 0,
                        bottom: 0,
                        position: 'css'
                      }}
                      onResizeStart={e => {
                        e.setFixedDirection([0, 0]);
                      }}
                      onDrag={e => {
                        e.target.style.transform = e.transform
                        dispatch({
                          type: 'update_mental_map_images',
                          imageId: image.id,
                          payload: {
                            left: parseInt(e.translate[0]),
                            top: parseInt(e.translate[1])
                          }
                        })
                      }}
                      onBeforeResize={e => {
                        const val = window.getComputedStyle(e.target, null)
                        let values = val.getPropertyValue('transform').split('(')[1]
                        values = values.split(')')[0]
                        values = values.split(',')
                          .map(s => s.trim())
                          .map(s => parseInt(s))
                        dispatch({
                          type: 'update_mental_map_images',
                          imageId: image.id,
                          payload: {
                            width: parseInt(e.target.style.width),
                            height: parseInt(e.target.style.height),
                            left: values[4],
                            top: values[5]
                          }
                        })
                      }}
                      onResize={e => {
                        e.target.style.cssText += `width: ${e.width}px; height: ${e.height}px`;
                        e.target.style.transform = e.drag.transform;
                      }}
                    />
                  </div>
                ))}
              </div>
              {droppableProvided.placeholder}
            </div>
          </div>)}
      </Droppable>
      <div>
        <CSSTransition
          in={open}
          nodeRef={ref}
          timeout={200}
          classNames="dialog"
          unmountOnExit
        >
          <Dialog nodeRef={ref} hideHandler={() => setOpen(false)}>
            {currentImageItem && (
              <div>
                <div style={{display: 'flex', flexDirection: 'row'}}>
                  <div style={{marginRight: '20px'}}>
                    <img src={currentImageItem.url} alt=""/>
                  </div>
                  <div style={{flex: '1'}}>
                    <textarea placeholder="Текст" className="textarea" onChange={(e) => {
                      dispatch({
                        type: 'update_mental_map_images',
                        imageId: currentImageItem.id,
                        payload: {
                          text: e.target.value
                        }
                      })
                      setCurrentText(e.target.value)
                    }} value={currentText}/>
                  </div>
                </div>
                <div style={{marginTop: '2rem'}}>
                  <textarea className="textarea" onChange={() => {
                  }} value={state.text} style={{minHeight: '300px'}}/>
                </div>
              </div>
            )}
          </Dialog>
        </CSSTransition>
      </div>
    </>
  )
}
