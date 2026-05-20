import SlidesConfig from "../SlidesConfig";

export default function SpeakSlideText() {
  const id = 'speak-slide-text';
  const config = new SlidesConfig().get(id);

  return {
    instance: null,
    id,
    init(deck) {

      this.instance = new window.SpeakSlideText(deck, config);

      deck.addEventListener('slidechanged', () => {
        this.instance.init();
      });
      deck.addEventListener('ready', ({indexh, indexv}) => {
        if (Number(indexh) > 0 || Number(indexv) > 0) {
          return;
        }
        this.instance.init();
      });
    },
    canNext() {
      if (!this.instance) {
        return;
      }
      return this.instance.canNext();
    }
  }
}
