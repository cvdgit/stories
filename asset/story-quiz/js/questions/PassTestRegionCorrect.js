import InnerDialog from "../components/Dialog";
import {createImageWrapper, createMap, createRegionsMap} from "../Region";

function createSuccess(regionMapName, imageParams, regions) {
  const $wrapper = createImageWrapper(imageParams, regionMapName)
  const $map = createMap(regionMapName);
  $map.appendTo($wrapper);
  $wrapper.find('img').one('load', function(e) {
    $(this).maphilight({alwaysOn: true});
  });
  createRegionsMap(regions, $map);
  return $wrapper;
}

const passTestRegionCorrect = (container, regionMapName, imageParams, regions) => {
  const $content = createSuccess(regionMapName, imageParams, regions);
  const dialog = new InnerDialog(container, {title: 'Ответ неверный', content: $content});
  dialog.show((wrap) => {

    const imageElement = wrap.find('.question-region-inner img');
    const height = parseInt(imageElement.css('height'));

    let initialZoom = 0.5;
    if (height > 500) {
      initialZoom = 500 / height;
    } else {
      initialZoom = height / 500;
    }

    window.regionZoom = panzoom(wrap.find('.question-region-inner')[0], {
      maxZoom: 3,
      minZoom: 0.4,
      bounds: true,
      initialZoom,
      initialX: 0,
      initialY: 0
    });
  });
};

export default passTestRegionCorrect;
