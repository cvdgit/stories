
const createImageWrapper = (imageParams, mapName) => {
  const $img = $('<img/>')
    .attr('src', imageParams.url)
    .attr('usemap', '#' + mapName)
    .css({
      //'position': 'absolute',
      //'left': 0,
      //'top': 0,
      'width': imageParams.width + 'px',
      'height': imageParams.height + 'px',
    });
  return $('<div/>')
    .addClass('question-region')
    .css({
      //'width': imageParams.width + 'px',
      //'height': imageParams.height + 'px',
      maxHeight: '500px',
      overflow: 'hidden'
      //'margin': '0 auto'
    })
    .append($('<div/>', {
      css: {
        //position: 'relative',
        //width: imageParams.width + 'px',
        //height: imageParams.height + 'px'
      }
    }).append($('<div/>', {class: 'question-region-inner'}).append($img)));
};

const createMap = (mapName) => {
  return $('<map/>', {'name': mapName});
}

const createArea = (shape, coords, id, addIncorrectArea) => {
  const attrs = {
    shape,
    coords
  };
  if (id) {
    attrs['data-answer-id'] = id;
  }
  if (!addIncorrectArea) {
    attrs['data-maphilight'] = '{"strokeColor":"99cd50","strokeWidth":5,"fillColor":"99cd50","fillOpacity":0.2}';
  }
  return $('<area/>', attrs);
}

const createRegionsMap = (regions, map, addIncorrectArea, width, height) => {

  addIncorrectArea = addIncorrectArea || false;

  regions.forEach(region => {

    let area;

    if (region.type === 'rect' || !region['type']) {
      const x = parseInt(region.rect.left);
      const y = parseInt(region.rect.top);
      area = createArea('rect', [x, y, parseInt(region.rect.width) + x, parseInt(region.rect.height) + y].join(','), region.id);
    }

    if (region.type === 'polyline') {
      const coords = [];
      region.polyline.forEach(point => coords.push(point.join(',')));
      area = createArea('poly', coords.join(','), region.id);
    }

    if (region.type === 'circle') {
      area = createArea('circle', [region.circle.cx, region.circle.cy, region.circle.r].join(','), region.id);
    }

    area.appendTo(map);
  });

  if (addIncorrectArea) {
    createArea('rect', [0, 0, width, height].join(',')).appendTo(map);
  }
};

export {createImageWrapper, createMap, createRegionsMap};
