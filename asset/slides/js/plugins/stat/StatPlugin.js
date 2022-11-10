import SlidesConfig from "../../SlidesConfig";
import Stat from "./Stat";

export default () => {

  const stack = [];

  return {

    id: 'stat',

    init(deck) {

      const slidesConfig = new SlidesConfig();
      const config = slidesConfig.get(this.id);

      const stat = new Stat(config);

      deck.addEventListener('slidechanged', (event) => {
        if (!$(event.previousSlide).hasClass('next-story')) {
          const promise = stat.slideChangeEvent(event);
          if (promise) {
            stack.push(promise);
          }
        }
      });
    },

    getStack() {
      return stack;
    }
  }
}
