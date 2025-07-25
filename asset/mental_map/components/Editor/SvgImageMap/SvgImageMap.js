import React, {useEffect, useRef, useState} from "react";
import './SvgImageMap.css'
import {useSvg, useSvgContainer} from "../../../Lib/svg-container/hook";
import {SvgContainer} from "../../../Lib/svg-container";
import {SVG} from "@svgdotjs/svg.js";
import ImageDialog from "../ImageDialog";
import {CSSTransition} from 'react-transition-group';
import {v4 as uuidv4} from 'uuid';
import {useMentalMap} from "../../App/App";
import DrawToggler from "./DrawToggler";

let currentShape
let svgZoom = 0.6

function findBgClassName(bg) {
  if (!bg || bg === '') {
    return '';
  }
  return `fragment-bg-${bg.toLowerCase()}`;
}

export default function SvgImageMap({mapImage, newImages, setNewImages}) {
  console.log('SvgImageMap render')

  const [open, setOpen] = useState(false)
  const [currentImageItem, setCurrentImageItem] = useState(null)
  const ref = useRef(null)
  const {state, dispatch} = useMentalMap()
  const [currentFragment, setCurrentFragment] = useState(null)
  const [drawMode, setDrawMode] = useState(false)

  const {setHandles, svgContainer} = useSvgContainer();

  useEffect(() => {
    const {width: mapImageWidth, height: mapImageHeight} = mapImage
    const {width, height} = document.getElementById('map-container').getBoundingClientRect()
    if (mapImageWidth > mapImageHeight) {
      svgZoom = width / mapImageWidth
    } else {
      svgZoom = height / mapImageHeight
    }
    svgZoom -= 0.05
  }, []);

  function attachShapeEvents(shape, item) {
    shape.on('dragend', e => {
      const {x: left, y: top} = e.detail.box
      dispatch({
        type: 'update_mental_map_images',
        imageId: item.id,
        payload: {left, top}
      })
    })

    shape.on('resize', e => {
      const {x: left, y: top, width, height} = e.detail.box
      dispatch({
        type: 'update_mental_map_images',
        imageId: item.id,
        payload: {left, top, width, height}
      })
    })

    shape.on('dblclick', () => {
      setCurrentImageItem(item)
      setOpen(true)
    })
  }

  function createImageShape(image, id) {
    return image.attr({'class': 'map-fragment map-image', 'data-id': id})
  }

  function createRectShape(rect, id, bg) {
    return rect.attr({'class': `map-fragment ${findBgClassName(bg)}`, 'data-id': id})
  }

  const onload = (svg, container) => {

    const wrapGroup = svg.group()
    wrapGroup.attr({
      id: 'schemeWrap',
      class: 'scheme-wrap'
    });

    const schemeImage = svg.image(mapImage.url, e => {
      schemeImage.size(mapImage.width, mapImage.height)
    })
    wrapGroup.add(schemeImage);

    (mapImage.images || []).map(i => {
      let shape
      if (i.url) {
        shape = createImageShape(wrapGroup.image(i.url), i.id)
        shape.size(i.width, i.height)
      } else {
        shape = createRectShape(wrapGroup.rect(i.width, i.height), i.id, i.bg)
      }
      shape.move(i.left, i.top)
      attachShapeEvents(shape, i)
    });
  }

  useSvg(svgContainer, svg => {
    console.log('use svg');

    svg
      .off('mousedown.map')
      .off('mouseup.map')

    if (drawMode) {
      svg.panZoom(false)
    } else {
      svg.panZoom({
        zoomFactor: 0.1,
        zoomMin: 0.25,
        zoomMax: 2,
        margins: {left: 10, top: 10, right: 10, bottom: 10}
      });
      svg.zoom(svgZoom, {x: 10, y: 10})
    }

    svg.off('zoom').on('zoom', (ev) => {
      const {level} = ev.detail
      svgZoom = level
    })

    const wrap = svg.find('#schemeWrap')

    svg
      .on('mousedown.map', e => {

        svg.find('.map-fragment').map(shape => shape.select(false).draggable(false))
        if (currentFragment) {
          setCurrentFragment(null)
        }

        if (currentShape) {
          currentShape
            .select(false)
            .draggable(false)
        }

        const target = SVG(e.target)
        if (target.type === 'rect' || target.type === 'circle' || target.type === 'polyline' || target.hasClass('map-fragment')) {
          target
            .select()
            .resize()
            .draggable()
          setCurrentFragment(target)
        } else {
          if (drawMode) {
            const shape = createRectShape(svg.rect(), uuidv4())
            shape.draw(e)
            currentShape = shape
          } else {
            setCurrentFragment(null)
          }
        }
      })
      .on('mouseup.map', e => {

        if (!currentShape) {
          return
        }
        currentShape.draw(e)
        if (currentShape.width() < 15 || currentShape.height() < 15) {
          currentShape.remove();
        } else {

          currentShape
            .select()
            .resize()
            .draggable()

          wrap.put(currentShape)

          const {x: left, y: top, width, height} = currentShape.attr()

          const el = {
            id: currentShape.attr('data-id'),
            left,
            top,
            width,
            height,
            text: ''
          }

          dispatch({
            type: 'add_image_to_mental_map',
            payload: el
          })

          currentShape.on('dblclick', e => {
            setCurrentImageItem(el)
            setOpen(true)
          })
        }
        currentShape = null
        setCurrentFragment(null)
      })

    return [];
  }, [drawMode])

  useSvg(svgContainer, svg => {
    const elems = []
    const wrap = svg.find('#schemeWrap')
    newImages.map(id => {
      const item = mapImage.images.find(i => i.id === id)
      let shape;
      if (item.url) {
        shape = createImageShape(wrap.image(item.url), item.id)
      } else {
        shape = createRectShape(wrap.rect(), item.id, item.bg)
      }
      shape.size(item.width, item.height)
      shape.move(item.left, item.top)
      attachShapeEvents(shape, item)
    })
    return elems
  }, [JSON.stringify(newImages)])

  const changeBgHandler = (imageId, bg) => {
    dispatch({
      type: 'update_mental_map_images',
      imageId,
      payload: {
        bg
      }
    })
    const wrap = svgContainer.svg.find('#schemeWrap')
    const el = wrap.findOne(`.map-fragment[data-id='${imageId}']`)
    el.attr({
      'class': `map-fragment ${findBgClassName(bg)}`
    })
  }

  const changeMakeTransparentHandler = (imageId, value) => {
    dispatch({
      type: 'update_mental_map_images',
      imageId,
      payload: {
        makeTransparent: value
      }
    })
  }

  return (
    <>
      <div style={{margin: '20px 0'}}>
        <DrawToggler
          currentFragment={currentFragment}
          onChangeHandler={(value) => setDrawMode(value !== 'move')}
          onDeleteHandler={(e) => {
            if (currentFragment && confirm('Подтверждаете удаление?')) {
              currentFragment
                .select(false)
                .draggable(false)
                .remove()
              dispatch({
                type: 'remove_image_from_mental_map',
                imageId: currentFragment.attr('data-id'),
              })
              setCurrentFragment(null)
            }
          }}
          onCopyHandler={(e) => {
            if (currentFragment) {
              const shape = SVG(currentFragment)
              const im = {...state.map.images.find(i => i.id === shape.attr('data-id'))}
              im.id = uuidv4()
              im.left += 20
              im.top += 20

              dispatch({
                type: 'add_image_to_mental_map',
                payload: im
              })

              setNewImages(prevState => {
                return [...prevState, im.id]
              })
            }
          }}
        />
      </div>
      <div style={{flex: '1'}}>
        <SvgContainer setHandles={setHandles} width='100%' height='100%' margin='0 auto' onload={onload}/>
      </div>
      <CSSTransition
        in={open}
        nodeRef={ref}
        timeout={200}
        classNames="dialog"
        unmountOnExit
      >
        <ImageDialog
          open={open}
          ref={ref}
          setOpen={setOpen}
          currentImageItem={mapImage.images.find(f => f.id === currentImageItem?.id)}
          changeBgHandler={changeBgHandler}
          changeMakeTransparentHandler={changeMakeTransparentHandler}
        />
      </CSSTransition>
    </>
  )
}
