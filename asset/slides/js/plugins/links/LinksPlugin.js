import SlidesConfig from "../../SlidesConfig";
import SlidesPlayer from "../../SlidesPlayer";
import Links from "./Links";

export default function LinksPlugin() {
  return {

    id: 'links',

    init(deck) {

      const slidesConfig = new SlidesConfig();
      const config = slidesConfig.get(this.id);

      const slidesPlayer = new SlidesPlayer(deck);
      const links = new Links(config);

      deck.addEventListener("ready", function(event) {
        links.showLinks(slidesPlayer.getCurrentSlide(), slidesPlayer.getCurrentSlideId());
      });

      deck.addEventListener("slidechanged", function(event) {
        links.showLinks(slidesPlayer.getCurrentSlide(), slidesPlayer.getCurrentSlideId(), true);
      });
    }
  }
}
