
function generateUUID() {
  var d = new Date().getTime();
  var d2 = ((typeof performance !== 'undefined') && performance.now && (performance.now() * 1000)) || 0;
  return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
    var r = Math.random() * 16;
    if (d > 0) {
      r = (d + r) % 16 | 0;
      d = Math.floor(d / 16);
    }
    else {
      r = (d2 + r) % 16 | 0;
      d2 = Math.floor(d2 / 16);
    }
    return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
  });
}

SVG.Element.prototype.draw.extend('line polyline polygon', {
    init:function(e){
        // When we draw a polygon, we immediately need 2 points.
        // One start-point and one point at the mouse-position
        this.set = new SVG.Set();
        var p = this.startPoint,
            arr = [
                [p.x, p.y],
                [p.x, p.y]
            ];
        this.el.plot(arr);
    },
    // The calc-function sets the position of the last point to the mouse-position (with offset ofc)
    calc:function (e) {
        var arr = this.el.array().valueOf();
        arr.pop();
        if (e) {
            var p = this.transformPoint(e.clientX, e.clientY);
            arr.push(this.snapToGrid([p.x, p.y]));
        }
        this.el.plot(arr);
    },
    point:function(e){
        if (this.el.type.indexOf('poly') > -1) {
            // Add the new Point to the point-array
            var p = this.transformPoint(e.clientX, e.clientY),
                arr = this.el.array().valueOf();
            arr.push(this.snapToGrid([p.x, p.y]));
            this.el.plot(arr);
            // Fire the `drawpoint`-event, which holds the coords of the new Point
            this.el.fire('drawpoint', {event:e, p:{x:p.x, y:p.y}, m:this.m});
            return;
        }
        // We are done, if the element is no polyline or polygon
        this.stop(e);
    },
    clean:function(){
        // Remove all circles
        this.set.each(function () {
            this.remove();
        });
        this.set.clear();
        delete this.set;
    },
});

/*
function RegionTable(selector, onDeleteCallback) {

    this.$table = $('<table class="table table-bordered">' +
        '<thead>' +
        '<tr>' +
        '<td>Верный</td>' +
        '<td>Область</td>' +
        '<td>Вопрос</td>' +
        '<td></td>' +
        '</tr>' +
        '</thead>' +
        '<tbody>' +
        '<tr class="empty-row"><td colspan="4">Выделите область на изображении</td></tr>' +
        '</tbody>' +
        '</table>');

    this.$table.on('click', '.delete-region', function(e) {
        e.preventDefault();
        if (!confirm('Удалить запись?')) {
            return;
        }
        var $row = $(this).parent().parent();
        var id = $row.attr('data-id');
        var answerID = $row.find('td:eq(2)').text();
        $row.remove();

        onDeleteCallback(id);
    });

    $(selector).append(this.create());
}

RegionTable.prototype = {
    'create': function create() {
        return this.$table;
    },
    'addRow': function addRow(id, title, correct, answer_id) {
        this.$table.find('tr.empty-row').remove();
        var $deleteElement = $('<a/>')
            .attr('href', '#')
            .addClass('delete-region')
            .attr('title', 'Удалить область')
            .append($('<i/>').addClass('glyphicon glyphicon-trash'));
        var $checkBox = $('<input/>')
            .attr('type', 'checkbox')
            .prop('checked', correct);
        var $row = $('<tr/>')
            .attr('data-id', id)
            .append($('<td/>').append($checkBox))
            .append($('<td/>').text(title))
            .append($('<td/>').text(answer_id))
            .append($('<td/>').append($deleteElement));
        this.$table.find('tbody').append($row);
    },
    'addEventListener': function(type, listener, useCapture) {
        if ('addEventListener' in window) {
            document.addEventListener(type, listener, useCapture);
        }
    },
    'getRows': function getRows() {
        return $('tbody tr:not(.empty-row)', this.$table).map(function() {
            var elem = $(this);
            return {
                'id': elem.attr('id'),
                'title': elem.find('td:eq(1)').text(),
                'correct': elem.find('td:eq(0) input[type=checkbox]').prop('checked'),
                'answer_id': elem.find('td:eq(2)').text()
            }
        }).get();
    },
    'rowsCount': function rowsCount() {
        return $('tbody tr:not(.empty-row)', this.$table).length;
    },
    'getCorrect': function getCorrect() {
        return this.$table.find('input[type=checkbox]:checked').length === 0;
    }
};
*/

function Shapes() {
  let shapes = [];
  let index = 0;
  let currentShape = null;

  this.add = function(shape) {
    shapes[index] = shape;
    index++;
  };
  this.delete = function(id) {
    shapes = shapes.filter(function(shape) {
      return shape.attr('id') !== id;
    });
  };
  this.getByID = function(id) {
    return shapes.filter(function(shape) {
      return shape.attr('id') === id;
    })[0];
  };
  this.getCurrentShape = function() {
    return currentShape;
  };
  this.all = function() {
    return shapes;
  };

  this.reset = () => {
    shapes = [];
  }

  const deleteCurrentShapeListener = (e) => {
    if (e.keyCode === 46) {
      if (currentShape) {
        this.delete(currentShape.attr('id'));
        this.resetCurrentShape().remove();
        currentShape = null;
      }
    }
  };

  this.setCurrentShape = function(shape) {
    currentShape = shape;
    //document.addEventListener('keydown', deleteCurrentShapeListener);
  };
  this.resetCurrentShape = function() {
    if (currentShape === null) {
      return;
    }
    //document.removeEventListener('keydown', deleteCurrentShapeListener);
    return currentShape;
  };
}

function RegionsSVG(id, {onDeleteHandler}) {
  //'use strict';

  this.shapes = new Shapes();

  this.markOptions = {
    'stroke': '#1baee1',
    'stroke-width': 3,
    'fill-color': '#1baee1',
    'fill-opacity': 0.1,
    'class': 'scheme-mark'
  };

  this.draw = new SVG(id);
  this.wrapper = this.draw.group();
  this.wrapper.attr({
    id: 'regionImageWrap',
    class: 'region-image-wrap'
  });

  this.removeDragEventListeners = () => {
    this.draw
      .off('mousedown')
      .off('mousemove')
      .off('mouseup');
  };

  this.addDragEventListeners = (type, drawShapeHandler, dragEndHandler) => {

    let currentShape = null;

    this.draw
      .on('mousedown', (event) => {
        console.log('mousedown');

        const shape = this.shapes.resetCurrentShape();
        if (shape) {
          shape
            .selectize(false)
            .draggable(false);
        }

        const target = SVG.adopt(event.target);
        if (target.type === 'rect' || target.type === 'circle' || target.type === 'polyline') {

          const foundShape = this.shapes.getByID(target.attr('id'));
          this.shapes.setCurrentShape(foundShape);

          foundShape
            .selectize({rotationPoint: false})
            .resize()
            .draggable();

        } else {
          currentShape = drawShapeHandler();
          currentShape.draw(event);
        }
      })
      .on('mousemove', (event) => {
        if (type === 'polyline' && currentShape) {
          currentShape.draw('point', event);
        }
      })
      .on('mouseup', (event) => {
        console.log('mouseup')

        if (currentShape === null) {
          return;
        }

        if (type === 'polyline') {
          currentShape.draw('stop', event);
        } else {
          currentShape.draw(event);
        }

        if (currentShape.width() < 15 || currentShape.height() < 15) {
          currentShape.remove();
        } else {
          this.shapes.add(currentShape);
          this.shapes.setCurrentShape(currentShape);
          currentShape
            .selectize({rotationPoint: false})
            .resize()
            .draggable();

          if (typeof dragEndHandler === 'function') {
            dragEndHandler(currentShape.attr());
          }
        }
        currentShape = null;
      });
  };

  document.addEventListener('keydown', (e) => {
    if (e.code === 'Delete') {
      const shape = this.shapes.getCurrentShape();
      if (shape) {
        this.shapes.delete(shape.attr('id'));
        shape
          .selectize(false)
          .draggable(false)
          .remove();
        this.shapes.setCurrentShape(null);
        if (typeof onDeleteHandler === 'function') {
          onDeleteHandler(shape.attr('data-answer-id'));
        }
      }
    }
  });
}

RegionsSVG.prototype.loadImage = function(path, width, height, initRegions, onImageLoaded) {

  this.draw.size(width, height);

  const image = this.wrapper.image(path);

  if (onImageLoaded) {
    image.loaded(onImageLoaded);
  }

  this.shapes.reset();

  initRegions.map(region => {
    let shape;
    switch (region.type || 'rect') {
      case 'polyline':
        shape = this.wrapper.polyline(region.polyline);
        break;
      case 'rect':
        shape = this.wrapper.rect(region.rect.width, region.rect.height).move(region.rect.left, region.rect.top);
        break;
      case 'circle':
        shape = this.wrapper.circle(region.circle.r * 2).move(region.circle.x, region.circle.y);
        break;
    }
    shape.attr(this.markOptions);
    shape.attr('data-answer-id', region.answer_id);
    this.shapes.add(shape);
  });
}

RegionsSVG.prototype.getRegions = function() {
  const regions = [];
  this.shapes.all().map(shape => {
    const region = {
      'id': shape.attr('id'),
      'title': shape.attr('id'),
      'type': shape.type,
      'correct': true,
      'answer_id': shape.attr('data-answer_id')
    };
    switch (shape.type) {
      case 'polyline':
        region['polyline'] = shape.array().value;
        break;
      case 'rect':
        region['rect'] = {
          'left': shape.attr('x'),
          'top': shape.attr('y'),
          'width': shape.attr('width'),
          'height': shape.attr('height')
        };
        break;
      case 'circle':
        region['circle'] = {
          'cx': shape.cx(),
          'cy': shape.cy(),
          'r': shape.attr('r'),
          'x': shape.x(),
          'y': shape.y()
        };
        break;
    }
    regions.push(region);
  });
  return regions;
}

RegionsSVG.prototype.deleteRegion = function(id) {
  this.shapes.getByID(id).remove();
}

RegionsSVG.prototype.drawRect = function({attrsHandler, drawEndHandler}) {
  this.removeDragEventListeners();
  this.addDragEventListeners('rect',() => {
    let rectAttrs = {...this.markOptions};
    if (typeof attrsHandler === 'function') {
      rectAttrs = {...this.markOptions, ...attrsHandler()};
    }
    return this.wrapper.rect().attr(rectAttrs);
  }, drawEndHandler);
}

RegionsSVG.prototype.drawCircle = function() {
  this.removeDragEventListeners();
  this.addDragEventListeners('circle', () => this.wrapper.circle().attr(this.markOptions));
};

RegionsSVG.prototype.drawPolyline = function() {
  this.removeDragEventListeners();
  this.addDragEventListeners('polyline',() => this.wrapper.polyline().attr(this.markOptions));
}

RegionsSVG.prototype.setDraggableMode = function (mode = true) {
  this.removeDragEventListeners();
  if (mode) {
    this.draw
      .on('mousedown', (event) => {

        const shape = this.shapes.resetCurrentShape();
        if (shape) {
          shape
            .selectize(false)
            .draggable(false);
        }

        const target = SVG.adopt(event.target);
        if (target.type === 'rect' || target.type === 'circle' || target.type === 'polyline') {

          const foundShape = this.shapes.getByID(target.attr('id'));
          this.shapes.setCurrentShape(foundShape);

          foundShape
            .selectize({rotationPoint: false})
            .resize()
            .draggable();

        }
      });
  }
}

RegionsSVG.prototype.resetSelectize = function() {
  this.shapes.all().map(shape => {
    shape.selectize(false);
  });
}
