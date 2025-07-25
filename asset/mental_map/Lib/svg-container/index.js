import React, { useRef, useEffect } from 'react';
import { SVG } from "@svgdotjs/svg.js";

const SvgContainer = (props) => {
  console.log('SvgContainer')
  const wrapper = useRef(null);

  useEffect(() => {
    if (wrapper && wrapper.current) {
      if (wrapper.current.children.length === 0) {
        let svg = SVG()
          .addTo(wrapper.current)
          .size(wrapper.current.offsetWidth, wrapper.current.offsetHeight)
          .attr({'tabindex': '0'})
          .viewbox(0, 0, wrapper.current.offsetWidth - 10, wrapper.current.offsetHeight - 10);
        props.setHandles({ svg, container: wrapper.current });
        props.onload?.(svg, wrapper.current);
      }
    }
  }, [wrapper]);

  const style = {outline: '0', position: 'relative', backgroundColor: '#bbb'};
  if (props.margin) style.margin = props.margin;
  if (props.height) style.height = props.height;
  if (props.width) style.width = props.width;

  return <div id="map-container" tabIndex="0" ref={wrapper} style={style}></div>;
}

export { SvgContainer };
