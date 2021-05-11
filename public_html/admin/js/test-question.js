
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

var RegionTable = function() {

    var $table = $('<table class="table table-bordered">' +
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

    $table.on('click', '.delete-region', function(e) {
        e.preventDefault();
        if (!confirm('Удалить запись?')) {
            return;
        }
        var $row = $(this).parent().parent();
        var id = $row.attr('id');
        var answerID = $row.find('td:eq(2)').text();
        $row.remove();
        dispatchEvent('onDeleteRegion', {
            "id": id,
            "answerID": answerID
        });
    });

    $table.on('click', 'input[type=checkbox]', function() {
        $table.find('input[type=checkbox]').prop('checked', false);
        this.checked = true;
    });

    function create() {
        return $table;
    }

    function addRow(id, title, correct, answer_id) {
        $table.find('tr.empty-row').remove();
        var $deleteElement = $('<a/>')
            .attr('href', '#')
            .addClass('delete-region')
            .attr('title', 'Удалить область')
            .append($('<i/>').addClass('glyphicon glyphicon-trash'));
        var $checkBox = $('<input/>')
            .attr('type', 'checkbox')
            .prop('checked', correct);
        var $row = $('<tr/>')
            .attr('id', id)
            .append($('<td/>').append($checkBox))
            .append($('<td/>').text(title))
            .append($('<td/>').text(answer_id))
            .append($('<td/>').append($deleteElement));
        $table.find('tbody').append($row);
    }

    function getRows() {
        return $('tbody tr:not(.empty-row)', $table).map(function() {
            var elem = $(this);
            return {
                'id': elem.attr('id'),
                'title': elem.find('td:eq(1)').text(),
                'correct': elem.find('td:eq(0) input[type=checkbox]').prop('checked'),
                'answer_id': elem.find('td:eq(2)').text()
            }
        }).get();
    }

    function rowsCount() {
        return $('tbody tr:not(.empty-row)', $table).length;
    }

    function getCorrect() {
        return $table.find('input[type=checkbox]:checked').length === 0;
    }

    return {
        'create': create,
        'addRow': addRow,
        'addEventListener': function(type, listener, useCapture) {
            if ('addEventListener' in window) {
                document.addEventListener(type, listener, useCapture);
            }
        },
        'getRows': getRows,
        'rowsCount': rowsCount,
        'getCorrect': getCorrect
    }
}

var RegionQuestion = (function() {

    var table = new RegionTable(),
        selection = new RegionSelection($('#image-region'));

    selection.addEventListener('onAddRegion', function(args) {
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
})();