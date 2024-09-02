import React, {useEffect, useRef, useState} from "react";
import {useImages, useMentalMap} from "../../App/App";
import Dialog from "../../Dialog";
import {CSSTransition} from 'react-transition-group';
import {trimRanges, surroundRangeContents} from '../../../Lib/selection'
import PanZoom, {Element} from "@sasza/react-panzoom";
import {v4 as uuidv4} from "uuid";

let scale = 1

function decodeHtml(html) {
  const txt = document.createElement("textarea");
  txt.innerHTML = html;
  return txt.value;
}

function processImageText(text) {
  const textFragments = new Map();
  const reg = new RegExp(`<span[^>]*>(.*?)<\\/span>`, 'gm');
  const imageText = decodeHtml((text || '').replace(/&nbsp;/g, ' ')).replace(reg, (match, p1) => {
    const id = uuidv4()
    textFragments.set(`${id}`, `${p1.trim()}`)
    return `{${id}}`
  })
  return {
    imageText,
    textFragments
  }
}

function createWordsFormText(text) {
  const {imageText, textFragments} = processImageText(text)
  console.log(imageText)
  const paragraphs = imageText.split('\n')
  const words = paragraphs.map(p => {
    if (p === '') {
      return [{type: 'break'}]
    }
    const words = p.split(' ').map(word => {
      if (word.indexOf('{') > -1) {
        const id = word.toString().replace(/[^\w\-]+/gmui, '')
        if (textFragments.has(id)) {
          const reg = new RegExp(`{${id}}`)
          word = word.replace(reg, textFragments.get(id))
          return word.split(' ').map(w => ({id: uuidv4(), word: w, type: 'word', hidden: true, target: true}))
        }
      }
      return [{id: uuidv4(), word, type: 'word', hidden: false}]
    })
    return [...(words.flat()), {type: 'break'}]
  }).flat()
  return words
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
  const [selectionMode, setSelectionMode] = useState(false)
  const [currentWords, setCurrentWords] = useState([])
  const selectionRef = useRef()

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
    if (!currentWords.length) {
      return
    }

    dispatch({
      type: 'update_mental_map_images',
      imageId: currentImageItem.id,
      payload: {
        text: getTextBySelections()
      }
    })

  }, [JSON.stringify(currentWords)]);

  function getTextBySelections() {
    let text = ''
    currentWords.map(word => {
      if (word.type === 'break') {
        text += "\n"
      } else {
        text += (word.hidden ? `<span class="target-text">${word.word}</span>` : word.word) + ' '
      }
    })
    return text.trim()
  }

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
        }} type="button" style={{position: 'absolute', zIndex: '999', right: '0'}}>Очистить
        </button>
      </div>

      <div id="mentalMapContainer" className="mental-map-container">
        <div style={{width: '100%', height: '100%', position: 'relative'}}>
          <div id="container" className="container" style={{
            //aspectRatio: '16/9 auto',
            //position: 'absolute',
            width: '100%',
            height: '100%',
            //inset: '50% auto auto 50%',
            //transform: 'translate(-50%, -50%)'
          }}>
            <PanZoom
              boundary={{left: 0, top: 0}}
              zoomInitial={0.5}
              ref={zoomRef}
              height={`${mapImage.height}px`}
              width={`${mapImage.width}px`}
              //style={{transformOrigin: '50% 50%'}}
              transformOrigin='50% 50%'
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
                      setSelectionMode(false)
                    }
                  }}
                >
                  <img
                    style={{width: '100%', height: '100%', cursor: 'pointer'}}
                    className={`1mental-pic mental-pic-${i}`}
                    src={image.url}
                    alt=""
                  />
                  <div style={{
                    marginTop: '4px',
                    display: 'flex',
                    flexDirection: 'row',
                    justifyContent: 'space-around',
                    position: 'absolute',
                    width: '100%'
                  }}>
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
                      <button onClick={() => {
                        setCurrentText(getTextBySelections())
                        setSelectionMode(false)
                      }}
                              className={`button button--default ${selectionMode ? 'button--outline' : 'button--header-done'} `}
                              type="button">Редактировать
                      </button>
                      <button onClick={() => {
                        setCurrentText(textRef.current.innerHTML)
                        setCurrentWords(createWordsFormText(textRef.current.innerHTML))
                        setSelectionMode(true)
                      }}
                              className={`button button--default ${selectionMode ? 'button--header-done' : 'button--outline'} `}
                              type="button">Выделить
                      </button>
                    </div>
                    {selectionMode ? (
                      <div
                        ref={selectionRef}
                        className="textarea"
                        style={{
                          borderStyle: 'solid',
                          maxHeight: '20rem',
                          overflowY: 'auto'
                        }}
                      >
                        {currentWords.map(word => {
                          const {type} = word
                          if (type === 'break') {
                            return (<div key={word.id} className="line-sep"></div>)
                          }
                          return (
                            <span
                              key={word.id}
                              onClick={() => {
                                setCurrentWords(prevState => [...prevState].map(w => {
                                    if (w.id === word.id) {
                                      w.hidden = !w.hidden
                                    }
                                    return w
                                  })
                                )
                              }}
                              className={`text-item-word ${word.hidden ? 'selected' : ''}`}
                            >{word.word}</span>
                          )
                        })}
                      </div>
                    ) : (
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
                      ></div>
                    )}
                    <div style={{marginTop: '2rem'}}>
                    <textarea className="textarea" onChange={() => {
                    }} value={state.text} style={{minHeight: '300px'}}/>
                    </div>
                  </div>
                </div>
              </div>
            )}
          </Dialog>
        </CSSTransition>
      </div>
    </>
  )
}
