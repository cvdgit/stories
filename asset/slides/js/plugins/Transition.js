import SlidesConfig from "../SlidesConfig";

let stack = [];
let inTransitionStory = false;
let currentStoryId = null;

export default () => {
  return {

    deck: null,
    id: 'transition',

    init(deck) {

      this.deck = deck;

      stack = [];
      inTransitionStory = false;

      const slidesConfig = new SlidesConfig();
      this.config = slidesConfig.get(this.id);

      currentStoryId = this.config.story_id;

      const action = (e) => {

        const story_id = $(e.target).data("storyId");
        const slide_id = $(e.target).data("slides");
        const backToNextSlide = ($(e.target).attr("data-backtonextslide") === "1");
        const slide_index = this.deck.getIndices().h;

        this.goToSlide(story_id, slide_id, backToNextSlide);
      }

      $(".reveal > .slides").on("click", "button[data-story-id]", action);
    },

    backToStory(callback) {

      if (stack.length > 0) {

        const state = stack.shift();

        $(".reveal .slides")
          .empty()
          .html(
            $('<img/>')
              .attr('src', '/img/loading.gif')
              .css('marginTop', '22%')
          );

        const getStoryData = (id) => {
          return $.ajax({
            "url": this.config.action + "/" + id,
            "type": "GET",
            "dataType": "json"
          });
        }

        const syncReveal = (data, slide_index) => {

          $(".reveal .slides")
            .empty()
            .append(data);

          if (this.deck.hasPlugin('background')) {
            const backgroundPlugin = this.deck.getPlugin('background');
            backgroundPlugin.initBackground();
          }

          this.deck.sync();
          this.deck.slide(slide_index);
        }

        getStoryData(state.story_id)
          .done(function(data) {

            syncReveal(data.html, state.slide_index);

            currentStoryId = state.story_id;
            inTransitionStory = false;

            if (window["WikidsVideo"]) {
              WikidsVideo.pauseLastPlayer();
              WikidsVideo.reset();
              if (state.slide_index === 0) {
                WikidsVideo.createPlayer();
              }
            }

            //if (WikidsPlayer.isTestSlide()) {
            //    WikidsStoryTest.restore();
            //}

            if (typeof callback === 'function') {
              callback();
            }
          });
      }
    },

    goToSlide(storyID, slideID, backToNextSlide) {

      backToNextSlide = backToNextSlide || false;

      var slide_index = this.deck.getIndices().h;

      var promise = $.ajax({
        "url": this.config.getSlideAction + "?story_id=" + storyID + "&slide_id=" + slideID,
        "type": "GET",
        "dataType": "json"
      });

      promise.done((data) => {

        inTransitionStory = true;

        $(".reveal .slides")
          .empty()
          .append(data.html);

        if (window["StoryBackground"]) {
          StoryBackground.init();
        }

        this.deck.sync();
        this.deck.slide(0);

        if (slide_index === 0 && window["WikidsVideo"]) {
          // Если переход происходит с первого (0) слайда, то событие slidechanged не генерится
          WikidsVideo.reset();
          WikidsVideo.createPlayer();
        }

        stack.unshift({
          "story_id": currentStoryId,
          "slide_index": backToNextSlide ? slide_index : slide_index + 1,
          "slide_id": slideID
        });

        currentStoryId = storyID;
      });
    },

    getInTransition() {
      return inTransitionStory;
    },

    hasTransitionInSlide() {
      const slide = this.deck.getCurrentSlide();
      return $("div[data-block-type=transition] button", slide).length > 0;
    },

    autoGoToTransition() {
      const slide = this.deck.getCurrentSlide();
      $("div[data-block-type=transition] button", slide).each(() => {
        this.goToSlide($(this).attr("data-story-id"), $(this).attr("data-slides"));
        return false;
      });
    }
  }
};
