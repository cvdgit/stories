import SlidesConfig from "../../SlidesConfig";
import Stat from "./Stat";

export default function StatPlugin() {

  const id = 'stat';
  const stack = [];
  const slidesConfig = new SlidesConfig();
  const config = slidesConfig.get(id);
  const stat = new Stat(config);

  return {

    id,

    init(deck) {

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
    },

    sendStat({slideId}) {
      if (!slideId) {
        console.error('Mental Map stat error - no slide id');
        return;
      }
      stack.push(stat.sendStat({slideId}));
    }
  }
}
