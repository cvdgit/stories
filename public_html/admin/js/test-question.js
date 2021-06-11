
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

function RegionsSVG(id, imageFile, shapeType, regions) {
    'use strict';

    function Shapes() {
        var shapes = [];
        var index = 0;
        var currentShape = null;

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

        var that = this;
        var deleteCurrentShapeListener = function(e) {
            if (e.keyCode === 46) {
                if (currentShape) {
                    that.delete(currentShape.attr('id'));
                    that.resetCurrentShape().remove();
                }
            }
        };

        this.setCurrentShape = function(shape) {
            currentShape = shape;
            currentShape
                .selectize({rotationPoint: false})
                .resize()
                .draggable();
            document.addEventListener('keydown', deleteCurrentShapeListener);
        };
        this.resetCurrentShape = function() {
            if (currentShape === null) {
                return;
            }
            currentShape
                .selectize(false)
                .draggable(false);
            document.removeEventListener('keydown', deleteCurrentShapeListener);
            return currentShape;
        };
    }

    this.shapes = new Shapes();
    this.shapeType = shapeType;

    this.draw = new SVG(id).size(imageFile.width, imageFile.height)
    this.draw.image(imageFile.path);

    var that = this;

    function getDrawObject() {
        var options = {
            'stroke': '#1baee1',
            'stroke-width': 3,
            'fill-color': '#1baee1',
            'fill-opacity': 0.1,
        };
        switch (that.shapeType.getType()) {
            case 'polyline':
                return that.draw.polyline().attr(options);
            case 'circle':
                return that.draw.circle().attr(options);
            case 'rect':
                return that.draw.rect().attr(options);
        }
        return null;
    }

    regions.forEach(function(region) {
        region.type = region.type || 'rect';
        var options = {
            'stroke': '#1baee1',
            'stroke-width': 3,
            'fill-color': '#1baee1',
            'fill-opacity': 0.1,
        };
        var shape;
        switch (region.type) {
            case 'polyline':
                shape = that.draw.polyline(region.polyline);
                break;
            case 'rect':
                shape = that.draw.rect(region.rect.width, region.rect.height).move(region.rect.left, region.rect.top);
                break;
            case 'circle':
                shape = that.draw.circle(region.circle.r * 2).move(region.circle.x, region.circle.y);
                break;
        }
        shape.attr(options);
        shape.attr('data-answer-id', region.answer_id);
        that.shapes.add(shape);
    });


    var currentShape = null;

    function addEventListeners() {
        that.draw
            .on('mousedown', function(event) {
                console.log('mousedown');
                var target = SVG.adopt(event.target);
                that.shapes.resetCurrentShape();
                if (target.type === 'rect' || target.type === 'circle' || target.type === 'polyline') {
                    that.shapes.setCurrentShape(that.shapes.getByID(target.attr('id')));
                }
                else {
                    currentShape = getDrawObject();
                    currentShape.draw(event);
                }
            })
            .on('mousemove', function(event) {
                if (that.shapeType.isPolyline() && currentShape) {
                    console.log('mousemove');
                    currentShape.draw('point', event);
                }
            })
            .on('mouseup', function(event) {
                console.log('mouseup');

                if (currentShape === null) {
                    return;
                }

                if (that.shapeType.isPolyline()) {
                    currentShape.draw('stop', event);
                } else {
                    currentShape.draw(event);
                }

                if (currentShape.width() < 15 || currentShape.height() < 15) {
                    currentShape.remove();
                }
                else {
                    that.shapes.add(currentShape);
                    that.shapes.setCurrentShape(currentShape);
                }
                currentShape = null;
            });
    }

    addEventListeners();
}

RegionsSVG.prototype = {
    'getRegions': function() {
        var regions = [];
        this.shapes.all().forEach(function(shape) {
            var region = {
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
    },
    'deleteRegion': function(id) {
        this.shapes.getByID(id).remove();
    }
};

function extend(a, b) {
    for (var i in b) {
        a[i] = b[i];
    }
    return a;
}

function dispatchEvent(type, args) {
    var event = document.createEvent("HTMLEvents", 1, 2);
    event.initEvent(type, true, true);
    extend(event, args);
    document.dispatchEvent(event);
}

/*
var RegionSelection = function(element) {

    element.find('img')
        .on('dragstart', function() { return false; })
        .one('load', function(e) {
            element
                .off('mousedown.wikids')
                .on('mousedown.wikids', element, function(e) {
                    startDrawRect(e);
                });
        })
        .each(function() {
            if (this.complete || this.readyState === 'complete') {
                $(this).trigger('load');
            }
        });

    var drawingRect,
        selectedRect;

    var canvasOffsetLeft = 0,
        canvasOffsetTop = 0,
        drawStartX = 0,
        drawStartY = 0;

    function getScale() {
        return 1;
    }

    function pointX(x) {
        return (x - canvasOffsetLeft) / getScale();
    }

    function pointY(y) {
        return (y - canvasOffsetTop) / getScale();
    }

    function startDrawRect(e) {

        var offset = element.offset();
        canvasOffsetLeft = offset.left;
        canvasOffsetTop = offset.top;

        drawStartX = pointX(e.pageX);
        drawStartY = pointY(e.pageY);
        drawingRect = createRect(drawStartX, drawStartY, 0, 0);

        element.on('mousemove.wikids', drawRect);
        element.on('mouseup.wikids', endDrawRect);
    }

    function drawRect(e) {
        var currentX = pointX(e.pageX);
        var currentY = pointY(e.pageY);
        var position = calculateRectPos(drawStartX, drawStartY, currentX, currentY);
        drawingRect.css(position);
    }

    function endDrawRect(e) {
        var currentX = pointX(e.pageX);
        var currentY = pointY(e.pageY);
        var position = calculateRectPos(drawStartX, drawStartY, currentX, currentY);
        if (position.width < 10 || position.height < 10) {
            drawingRect.remove();
        }
        else {
            drawingRect.css(position);
            selectRect(drawingRect);
            dispatchEvent('onAddRegion', {
                "rect": position,
                "element": drawingRect,
            });
        }
        element.off('mousemove.wikids');
        element.off('mouseup.wikids');
    }

    var params = {};

    function createRect(x, y, w, h) {
        var rect = $('<div/>')
            .addClass('rect')
            .css({
                left: x,
                top: y,
                width: w,
                height: h
            });
        rect.on('click', function() {
            var $el = $(this);
            params = {
                left: parseInt($el.css('left')),
                top: parseInt($el.css('top')),
                width: parseFloat($el.css('width')),
                height: parseFloat($el.css('height'))
            };
        });
        rect.appendTo(element);
        return rect;
    }

    function selectRect(rect) {
        selectedRect && selectedRect.removeClass('selected');
        selectedRect = rect;
        selectedRect.addClass('selected');
    }

    function calculateRectPos(startX, startY, endX, endY) {
        var width = endX - startX;
        var height = endY - startY;
        var posX = startX;
        var posY = startY;
        if (width < 0) {
            width = Math.abs(width);
            posX -= width;
        }
        if (height < 0) {
            height = Math.abs(height);
            posY -= height;
        }
        return {
            left: posX,
            top: posY,
            width: width,
            height: height
        };
    }

    function addRect(id, position) {
        var rect = createRect(position.left, position.top, position.width, position.height);
        rect.attr('id', id);
    }

    function deleteRect(id) {
        element.find('#' + id).remove();
    }

    function getRectByID(id) {
        var $rect = element.find('#' + id);
        return {
            left: parseInt($rect.css('left')),
            top: parseInt($rect.css('top')),
            width: parseFloat($rect.css('width')),
            height: parseFloat($rect.css('height'))
        };
    }

    return {
        'addEventListener': function(type, listener, useCapture) {
            if ('addEventListener' in window) {
                document.addEventListener(type, listener, useCapture);
            }
        },
        'addRect': addRect,
        'deleteRect': deleteRect,
        'getRectByID': getRectByID
    }
};
*/

/*
var RegionQuestion = (function() {

    var table = new RegionTable();

    table.addEventListener('onAddRegion', function(args) {
        var item = {
            "id": 'region' + table.rowsCount().toString(),
            "title": 'Region ' + table.rowsCount().toString(),
            "rect": args.rect,
            "correct": table.getCorrect()
        };
        table.addRow(item.id, item.title, item.correct);
        args.element.attr('id', item.id);
    });

    table.addEventListener('onDeleteRegion', function(args) {
        selection.deleteRect(args.id);
    });

    function init(data) {

        data = data || [];
        if (typeof data === 'string') {
            data = JSON.parse(data);
        }
        data.forEach(function(item) {
            table.addRow(item.id, item.title, item.correct, item.answer_id);
            selection.addRect(item.id, item.rect);
        });

        $('#region-table').append(table.create());
    }

    function getRegionsJson() {
        var data = [];
        table.getRows().forEach(function(item) {
            item.rect = selection.getRectByID(item.id);
            data.push(item);
        });
        return JSON.stringify(data);
    }

    return {
        'init': init,
        'getRegionsJson': getRegionsJson,
        'addEventListener': table.addEventListener
    };
})();*/