import SlidesConfig from "../../SlidesConfig";
import Stat from "./Stat";

export default () => {
  return {

    id: 'stat',

    init(deck) {

      const slidesConfig = new SlidesConfig();
      const config = slidesConfig.get(this.id);

      const stat = new Stat(config);

      deck.addEventListener('slidechanged', (event) => stat.slideChangeEvent(event));
    }
  }
}
