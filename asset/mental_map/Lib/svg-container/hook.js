import { Svg, Element } from "@svgdotjs/svg.js";
import "@svgdotjs/svg.draw.js"
import "@svgdotjs/svg.select.js"
import "@svgdotjs/svg.resize.js"
import "@svgdotjs/svg.draggable.js"
import "@svgdotjs/svg.panzoom.js"
import { useState, useRef, useEffect, useLayoutEffect } from 'react';

export const useSvgContainer = () => {
  const [handles, setHandles] = useState();
  return { setHandles, svgContainer: handles };
};

export const useSvg = (
  container,
  effect,
  deps
) => {
  const callbackRef = useRef(effect);

  useLayoutEffect(() => {
    callbackRef.current = effect;
  }, [effect]);

  useEffect(() => {
    let objs = [];
    let current = callbackRef.current;
    if (current && container) objs = current(container.svg) || [];
    return () => objs.forEach((obj) => obj.remove());
  }, [...deps, container]);
};

export const svgUpdate = (container, effect) => () => effect(container?.svg);

export const useSvgWithCleanup = (
  container,
  effect,
  deps
) => {
  const callbackRef = useRef(effect);

  useLayoutEffect(() => {
    callbackRef.current = effect;
  }, [effect]);

  useEffect(() => {
    let current = callbackRef.current;
    let ret;
    if (current && container) ret = current(container.svg);
    return () => {
      if (container) {
        if (ret) ret(container.svg);
        else container.svg.clear();
      }
    };
  }, [...deps, container]);
};
