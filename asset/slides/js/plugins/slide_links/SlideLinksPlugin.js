import SlidesConfig from "../../SlidesConfig";
import SlidesPlayer from "../../SlidesPlayer";
import SlideLinks from "./SlideLinks";

export default function SlideLinksPlugin() {
  return {

    id: 'slide_links',

    init(deck) {

      const slidesConfig = new SlidesConfig();
      const config = slidesConfig.get(this.id);

      const slidesPlayer = new SlidesPlayer(deck);
      const slideLinks = new SlideLinks(config);

      deck.addEventListener("slidechanged", function() {
        slideLinks.processLinks(slidesPlayer.getCurrentSlide());
      });

      deck.addEventListener("ready", function() {
        slideLinks.processLinks(slidesPlayer.getCurrentSlide());
      });
    }
  }
}
