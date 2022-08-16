import SlidesConfig from "../../SlidesConfig";
import Video from "./Video";
import SlidesPlayer from "../../SlidesPlayer";

export default () => {
  return {

    id: 'video',

    init(deck) {

      const slidesConfig = new SlidesConfig();
      const config = slidesConfig.get(this.id);

      const slidesPlayer = new SlidesPlayer(deck);
      const video = new Video(slidesPlayer, config);

      deck.addEventListener('slidechanged', (event) => video.createPlayer(event.currentSlide));
      deck.addEventListener('ready', (event) => video.createPlayer(event.currentSlide));
    }
  }
}
