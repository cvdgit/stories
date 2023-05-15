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
  dialog.show();
};

export default passTestRegionCorrect;
