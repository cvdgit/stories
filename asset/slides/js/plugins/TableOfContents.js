import SlidesConfig from "../SlidesConfig";

export default function TableOfContents() {
  const PluginId = 'table-of-contents';
  const config = new SlidesConfig().get(PluginId);

  const instance = window.TableOfContents;

  return {
    id: PluginId,
    init(deck) {
      deck.addEventListener('slidechanged', () => {
        instance.initDeckEvent(deck, config);
      });
      deck.addEventListener('ready', ({indexh, indexv}) => {
        if (Number(indexh) > 0 || Number(indexv) > 0) {
          return;
        }
        instance.initDeckEvent(deck, config);
      });
    },
  }
}
