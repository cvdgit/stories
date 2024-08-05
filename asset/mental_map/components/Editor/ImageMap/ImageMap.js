import React, {useEffect, useRef, useState} from "react";
import {Droppable} from "react-beautiful-dnd";
import Moveable from "react-moveable";
import {useImages, useMentalMap} from "../../App/App";
import Dialog from "../../Dialog";
import {CSSTransition} from 'react-transition-group';
import {trimRanges, surroundRangeContents} from '../../../Lib/selection'
import PanZoom, {Element, PanZoomWithCover} from "@sasza/react-panzoom";

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

const ImageIndexViewable = {
  name: 'imageIndexViewable',
  props: ['index'],
  events: [],
  render(moveable, React) {
    const rect = moveable.getRect();

    // Add key (required)
    // Add class prefix moveable-(required)
    return <div key={"dimension-viewer"} className={"moveable-dimension"} style={{
      position: "absolute",
      left: `${rect.width / 2}px`,
      top: `${rect.height + 20}px`,
      background: "#4af",
      borderRadius: "2px",
      padding: "2px 4px",
      color: "white",
      fontSize: "13px",
      whiteSpace: "nowrap",
      fontWeight: "bold",
      willChange: "transform",
      transform: `translate(-50%, 0px)`,
    }}>
      {moveable.props.index}
    </div>;
  },
}

export default function ImageMap({mapImage}) {
  const [open, setOpen] = useState(false)
  const ref = useRef(null)
  const {state, dispatch} = useMentalMap()
  const {dispatch: imagesDispatch} = useImages()
  const [currentImageItem, setCurrentImageItem] = useState(null)
  const [currentText, setCurrentText] = useState(null)
  const textRef = useRef()
  const zoomRef = useRef()

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

    //resizeHandler()

    //window.addEventListener('resize', resizeHandler)

    //return () => window.removeEventListener('resize', resizeHandler)
  }, []);

  useEffect(() => {
    //zoomRef.current.reset()
  }, []);

  const insertFragmentHandler = () => {
    if (!window.getSelection) {
      throw new Error('window.getSelection error');
    }
    const selection = window.getSelection();
    if (selection.toString().length === 0) {
      throw new Error('Необходимо выделить фрагмент текста');
    }
    if (selection.isCollapsed) {
      throw new Error('selection is collapsed');
    }
    const selText = selection.toString();
    const skipTrim = (selText.length === 1) && (selText === ' ');
    if (!skipTrim) {
      trimRanges(selection);
    }

    const ranges = [];
    for (let i = 0, len = selection.rangeCount; i < len; ++i) {
      ranges.push(selection.getRangeAt(i));
    }
    selection.removeAllRanges();

    let i = ranges.length;

    while (i--) {
      const range = ranges[i];

      surroundRangeContents(range, (textNodes) => {

        const element = document.createElement('span')
        element.classList.add('target-text')
        textNodes[0].parentNode.insertBefore(element, textNodes[0]);

        let textContent = '';
        for (let i = 0, node; node = textNodes[i++];) {
          element.appendChild(node);
          textContent += node.nodeType === 3 ? node.textContent : node.outerHTML;
          element.appendChild(node);
        }

        //const id = dataWrapper.createFragment(generateUUID());
        //element.setAttribute('data-fragment-id', id);

        if (textNodes[0].textContent === ' ') {
          textNodes[0].textContent = '\u00A0';
        }

        /*dataWrapper.createFragmentItem(id, {
          id: generateUUID(),
          title: textContent,
          correct: true
        });*/
      });

      selection.addRange(range);
    }
  }

  const emitChange = (e) => {
    dispatch({
      type: 'update_mental_map_images',
      imageId: currentImageItem.id,
      payload: {
        text: e.target.innerHTML
      }
    })
    //setCurrentText(e.target.innerHTML)
  }

  return (
    <>
      <div>
        <button onClick={() => {
          state.map.images.map(i => {
            dispatch({
              type: 'remove_image_from_mental_map',
              imageId: i.id,
            })
            const im = {...i}
            im.text = ''
            delete im.left
            delete im.top
            imagesDispatch({
              type: 'add_image',
              payload: im
            })
          })
        }} type="button" style={{position: 'absolute', zIndex: '999'}}>Очистить
        </button>
      </div>

      <div id="mentalMapContainer" className="mental-map-container">
        <div style={{width: '100%', height: '100%', position: 'relative'}}>
          <div id="container" className="container" style={{
            aspectRatio: '16/9 auto',
            position: 'absolute',
            width: '100%',
            inset: '50% auto auto 50%',
            transform: 'translate(-50%, -50%)'
          }}>
            <PanZoom
              boundary={{left: 0, top: 0}}
              zoomInitial={0.5}
              ref={zoomRef}
              height={`${mapImage.height}px`}
              width={`${mapImage.width}px`}
              zoomMin={0.5}
              zoomMax={1.8}
              //onContainerChange={e => console.log('onContainerChange', e)}
              //onContainerClick={e => console.log('onContainerClick', e)}
              //onContainerPositionChange={e => console.log('onContainerPositionChange', e)}
              //onContainerZoomChange={e => console.log('onContainerZoomChange', e)}
              //onElementsChange={e => console.log('onElementsChange', e)}
            >
              <img draggable={false} src={mapImage.url} style={{height: '100%'}} alt=""/>
              {mapImage.images.map((image, i) => (
                <Element
                  resizedMinWidth={100}
                  resizedMaxWidth={300}
                  resizable={true}
                  key={`im-${i}`}
                  id={`image${i}`}
                  x={image.left}
                  y={image.top}
                  width={image.width}
                  onAfterResize={(e) => {
                    const elem = zoomRef.current.getElements()[e.id]
                    dispatch({
                      type: 'update_mental_map_images',
                      imageId: image.id,
                      payload: {
                        width: elem.node.current.offsetWidth,
                        height: elem.node.current.offsetHeight,
                        left: elem.position.x,
                        top: elem.position.y
                      }
                    })
                  }}
                  onMouseUp={e => {
                    const target = e.e.target
                    if (target.tagName === 'BUTTON') {
                      dispatch({
                        type: 'remove_image_from_mental_map',
                        imageId: image.id,
                      })
                      imagesDispatch({
                        type: 'add_image',
                        payload: {...image}
                      })
                      return
                    }
                    if (target.tagName !== 'IMG') {
                      return
                    }
                    const left = Math.floor(e.x)
                    const top = Math.floor(e.y)
                    if (image.left !== left || image.top !== top) {
                      dispatch({
                        type: 'update_mental_map_images',
                        imageId: image.id,
                        payload: {left, top}
                      })
                    } else {
                      setCurrentImageItem(image)
                      setCurrentText(image.text)
                      setOpen(true)
                    }
                  }}
                >
                  <img
                    style={{width: '100%', height: '100%', cursor: 'pointer'}}
                    className={`1mental-pic mental-pic-${i}`}
                    src={image.url}
                    alt=""
                  />
                  <div style={{marginTop: '4px', display: 'flex', flexDirection: 'row', justifyContent: 'space-around', position: 'absolute', width: '100%'}}>
                    <span style={{
                      fontWeight: '500',
                      width: '36px',
                      height: '36px',
                      fontSize: '26px',
                      lineHeight: '26px',
                      borderRadius: '6px',
                      background: 'rgb(68, 170, 255)',
                      display: 'flex',
                      alignItems: 'center',
                      justifyContent: 'center',
                      color: 'white'
                    }}>{i + 1}</span>
                    <button title="Удалить" type="button" style={{
                      background: '#dc3545',
                      width: '36px',
                      height: '36px',
                      fontSize: '30px',
                      lineHeight: '30px',
                      borderRadius: '6px',
                      color: 'white'
                    }}>&times;</button>
                  </div>
                </Element>
              ))}
            </PanZoom>
          </div>
        </div>
      </div>

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
                  <div style={{flex: '1', display: 'flex', flexDirection: 'column'}}>
                    <div style={{marginBottom: '10px'}}>
                      <button onClick={insertFragmentHandler} className="button button--default button--outline"
                              type="button">Выделить
                      </button>
                      <button onClick={() => {
                        textRef.current.textContent = textRef.current.innerText
                        dispatch({
                          type: 'update_mental_map_images',
                          imageId: currentImageItem.id,
                          payload: {
                            text: textRef.current.innerHTML
                          }
                        })
                      }} className="button button--default button--outline"
                              type="button">Очистить
                      </button>
                    </div>
                    <div
                      ref={textRef}
                      contentEditable="plaintext-only"
                      className="textarea"
                      dangerouslySetInnerHTML={{__html: currentText}}
                      onInput={emitChange}
                      onBlur={emitChange}
                      onKeyUp={emitChange}
                      onKeyDown={emitChange}
                      style={{
                        borderStyle: 'solid',
                        maxHeight: '20rem',
                        overflowY: 'auto'
                      }}
                    />
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
