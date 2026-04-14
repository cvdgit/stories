import {removePunctuation, stripTags} from "./common";
import md5 from 'blueimp-md5'

function FragmentState(mentalMapId) {

  function resetText(text) {
    text = stripTags(text);
    text = removePunctuation(text);
    text = text.replaceAll(/  +/g, ' ');
    return text.toLowerCase().trim();
  }

  return {
    get(fragmentId, fragmentText) {

      const fragments = localStorage.getItem(mentalMapId);
      if (!fragments) {
        return;
      }

      const fragment = JSON.parse(fragments).find(f => f.id === fragmentId);
      if (!fragment) {
        return;
      }

      const hash = md5(resetText(fragmentText));
      if (fragment.hash === hash) {
        return fragment.words;
      }

      localStorage.setItem(mentalMapId, JSON.stringify(fragments.filter(f => f.id !== fragmentId)));
    },
    set(fragmentId, fragmentText, words) {
      const fragments = JSON.parse(localStorage.getItem(mentalMapId) || '[]');
      let fragment = fragments.find(f => f.id === fragmentId);
      if (!fragment) {
        fragment = {id: fragmentId};
        fragments.push(fragment);
      }
      fragment.hash = md5(resetText(fragmentText));
      fragment.words = words;
      localStorage.setItem(mentalMapId, JSON.stringify(fragments));
    }
  }
}

export default FragmentState;
