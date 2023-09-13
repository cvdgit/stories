import {createImageWrapper, createMap, createRegionsMap} from "../Region";

function getScale(props) {
  let scale = props.scale || 1;
  if (window['Reveal']) {
    scale = Reveal.getScale();
  }
  return scale;
}

const AnswerManager = function() {
  const answers = [];
  return {
    add(answer) {
      if (answers.indexOf(answer) === -1) {
        answers.push(answer);
      }
    },
    getAnswers() {
      return answers;
    },
    resetAnswers() {
      answers.length = 0;
    }
  }
};

const PassTestRegionContent = (regionMapName, imageParams, regions, props, callback) => {

  const answers = [];

  const correctRegions = regions.map(region => region.id);

  const $wrapper = createImageWrapper(imageParams, regionMapName);

  const answerManager = new AnswerManager();

  const checkValueIsCorrect = (correctValues, userValues) => {
    return correctValues.every(correctValue => {
      return userValues.some(value => {
        return correctValue === value;
      });
    });
  }

  const $map = createMap(regionMapName)
    .appendTo($wrapper.find('.question-region-inner'));

  const point = {
    x: 0,
    y: 0
  };

  $map
    .on('mousedown', 'area', function(e) {
      point.x = e.clientX;
      point.y = e.clientY;
    })
    .on('mouseup', 'area', function(e) {
      if (e.clientX !== point.x || e.clientY !== point.y) {
        return;
      }

      const elem = $(e.target).parent().parent()[0];
      const zoom = window.regionZoom.getTransform().scale;
      //const zoom = getScale(props);

      const clientRect = elem.getBoundingClientRect();
      let x, y;

      //if (zoom > 1) {
      //x = e.offsetX / zoom;
      //y = e.offsetY / zoom;
      //} else {
      x = (e.clientX - clientRect.left) / zoom;
      y = (e.clientY - clientRect.top) / zoom;
      //}

      const rect = {x: x - 10, y: y - 10};

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
        .appendTo($wrapper.find('.question-region-inner'));

      const $target = $(e.target);
      const regionId = $target.attr('data-answer-id');
      if (regionId) {
        answerManager.add(regionId);
      } else {
        answerManager.add('no_correct_' + new Date().getTime());
      }

      if (answerManager.getAnswers().length === correctRegions.length) {
        //setTimeout(function() {
          $wrapper.find('span.answer-point').remove();
          callback(checkValueIsCorrect(correctRegions, answerManager.getAnswers()), answerManager.getAnswers());
          answerManager.resetAnswers();
        //}, 500);
      }
    });

  createRegionsMap(regions, $map, true, imageParams.width, imageParams.height);

  return $wrapper;
};

export default PassTestRegionContent;
