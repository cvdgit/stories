import SlidesConfig from "../SlidesConfig";
import SlidesPlayer from "../SlidesPlayer";
import "./next_story.css";
import config from "reveal.js/js/config";

export default () => {
  return {

    id: 'next-story',

    init(deck) {

      const slidesPlayer = new SlidesPlayer(deck);

      const slidesConfig = new SlidesConfig();
      const config = this.config = slidesConfig.get(this.id);

      const createNextStory = (slide) => {
        return `
            <div class="sl-block" style="width: 1280px; height: 720px; left: 0; top: 0">
                <div class="sl-block-content">
                    <div class="next-story">
                        <h2 class="next-story__text">История пройдена!</h2>
                        <button type="button" class="btn next-story__button">Дальше</button>
                    </div>
                </div>
            </div>`;
      }

      const transitionPlugin = deck.getPlugin('transition');
      const testingPlugin = deck.getPlugin('testing');

      deck.addEventListener('slidechanged', event => {

        if (deck.isLastSlide() && !transitionPlugin.getInTransition() && !testingPlugin.inTest()) {

          const $slide = $(deck.getCurrentSlide());
          $slide[0].innerHTML = createNextStory();
          $slide.find('.next-story__button').on('click', function() {
            $.getJSON('/edu/default/get-next-story?story_id=' + config.story_id + '&program_id=' + config.program_id)
              .done(response => {
                if (response && response.success) {

                  location.href = response.url;
                }
              });
          });
        }
      });
    }
  }
}
