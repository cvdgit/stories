import SlidesConfig from "../SlidesConfig";
import SlidesPlayer from "../SlidesPlayer";

export default () => {
  return {

    id: 'actions',

    init(deck) {

      $(".reveal > .slides")
        .on("click", "img[data-action=1]", executeAction);

      const slidesPlayer = new SlidesPlayer(deck);

      function executeAction() {

        const storyId = $(this).attr("data-action-story");
        const slideId = $(this).attr("data-action-slide");
        const backToNextSlide = ($(this).attr("data-backtonextslide") === "1");

        slidesPlayer.goToSlide(storyId, slideId, backToNextSlide);
      }
    }
  }
}
