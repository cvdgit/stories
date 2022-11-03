import {_extends} from "../common";
import '../../../../node_modules/maphilight/jquery.maphilight';

const RegionQuestion = function(test) {

  this.test = test;

  var answers = [];
  this.addAnswer = function(answer) {
    if (answers.indexOf(answer) === -1) {
      answers.push(answer);
    }
  };
  this.getAnswers = function() {
    return answers;
  };
  this.resetAnswers = function() {
    answers = [];
  }

  this.getAnswerByRegion = function(questionAnswers, region) {
    return questionAnswers.filter(function(answer) {
      return answer.region_id === region;
    });
  };

  this.createImageWrapper = function(imageParams, mapName) {
    var $img = $('<img/>')
      .attr('src', imageParams.image)
      .attr('usemap', '#' + mapName)
      .css({'position': 'absolute', 'left': 0, 'top': 0, 'width': '100%', 'height': '100%'});
    return $('<div/>')
      .addClass('question-region')
      .css({'width': imageParams.imageWidth + 'px', 'height': imageParams.imageHeight + 'px', 'position': 'relative', 'margin': '0 auto'})
      .append($img);
  }

  this.createMap = function(mapName) {
    return $('<map/>', {'name': mapName});
  }

  this.createRegionsMap = function(regions, map, addIncorrectArea, width, height) {

    addIncorrectArea = addIncorrectArea || false;

    function createArea(shape, coords, id) {
      var attrs = {
        'shape': shape,
        'coords': coords
      };
      if (id) {
        attrs['data-answer-id'] = id;
      }
      if (!addIncorrectArea) {
        attrs['data-maphilight'] = '{"strokeColor":"99cd50","strokeWidth":5,"fillColor":"99cd50","fillOpacity":0.2}';
      }
      return $('<area/>', attrs);
    }

    regions.forEach(function(region) {
      var area;
      if (region.type === 'rect' || !region['type']) {
        var x = parseInt(region.rect.left),
          y = parseInt(region.rect.top);
        area = createArea('rect', [x, y, parseInt(region.rect.width) + x, parseInt(region.rect.height) + y].join(','), region.id);
      }
      if (region.type === 'polyline') {
        var coords = [];
        region.polyline.forEach(function(point) {
          coords.push(point.join(','));
        });
        area = createArea('poly', coords.join(','), region.id);
      }
      if (region.type === 'circle') {
        area = createArea('circle', [region.circle.cx, region.circle.cy, region.circle.r].join(','), region.id)
      }
      area.appendTo(map);
    });

    if (addIncorrectArea) {
      createArea('rect', [0, 0, width, height].join(',')).appendTo(map);
    }
  }
}

RegionQuestion.prototype.create = function(question, questionAnswers, props = {}) {
  var params = question.params;
  var regionMapName = 'regions-' + question.id;
  var that = this;
  var $wrapper = this.createImageWrapper(params, regionMapName)
    .on('click', function(e) {
      function getScale() {
        let scale = props.scale || 1;
        if (window['Reveal']) {
          scale = Reveal.getScale();
        }
        return scale;
      }
      var elem = $(e.target).parent()[0];
      var zoom = getScale();
      console.log(zoom);
      var clientRect = elem.getBoundingClientRect();
      var x, y;
      if (zoom > 1) {
        x = e.offsetX / zoom;
        y = e.offsetY / zoom;
      }
      else {
        x = (e.clientX - clientRect.left) / zoom;
        y = (e.clientY - clientRect.top) / zoom;
      }
      var rect = {x: x - 10, y: y - 10};
      $('<span/>')
        .addClass('answer-point')
        .css({
          'position': 'absolute',
          'left': rect.x,
          'top': rect.y,
          'shape-outside': 'circle()',
          'clip-path': 'circle()',
          'background-color': '#3c763d',
          'width': '20px',
          'height': '20px',
          'pointer-events': 'none'
        })
        .appendTo(this);
    });
  var $map = this.createMap(regionMapName)
    .appendTo($wrapper)
    .on('click', 'area', function() {
      var target = $(this);
      var regionID = target.attr('data-answer-id');
      if (regionID) {
        var answer = that.getAnswerByRegion(questionAnswers, regionID);
        that.addAnswer(answer[0].id);
      }
      else {
        that.addAnswer('no_correct_' + new Date().getTime());
      }
      if (that.getAnswers().length === parseInt(question.correct_number)) {
        setTimeout(function() {
          that.test.nextQuestion(that.getAnswers());
          that.resetAnswers();
          $wrapper.find('span.answer-point').remove();
        }, 500);
      }
    });
  this.createRegionsMap(params.regions, $map, true, params.imageWidth, params.imageHeight);
  return $wrapper;
};

RegionQuestion.prototype.createSuccess = function(question) {
  var params = question.params;
  var regionMapName = 'correct-regions-' + question.id;
  var $wrapper = this.createImageWrapper(params, regionMapName);
  var $map = this.createMap(regionMapName).appendTo($wrapper);

  $wrapper.find('img').one('load', function(e) {
    $(this).maphilight({alwaysOn: true});
  });

  this.createRegionsMap(params.regions, $map);
  return $wrapper;
};

_extends(RegionQuestion, {
  pluginName: 'regionQuestion'
});

export default RegionQuestion;
