import SlidesConfig from "../SlidesConfig";
import SlidesPlayer from "../SlidesPlayer";

let stack = [];
let inTest = false;
let currentStoryId = null;

let readySlides = [];

export default () => {
  return {

    id: 'testing',
    deck: null,
    config: {},

    init(deck) {
      this.deck = deck;

      const slidesConfig = new SlidesConfig();
      this.config = slidesConfig.get(this.id);

      currentStoryId = this.config.story_id;

      stack = [];

      const container = $('.reveal > .slides');

      const storyTestResults = (event) => {

        const promise = $.ajax({
          "url": this.config.storeAction,
          "type": "GET",
          "dataType": "json",
          "data": {
            "test_id": event.testID,
            "correct_answers": event.correctAnswers
          }
        });
        promise.done(function(data) {

        });
      }

      const action = (elem) => {

        inTest = true;

        const test_id = $(elem).data("testId");
        const slide_index = this.deck.getIndices().h;

        const test = WikidsStoryTest.create(container[0], {
          'dataUrl': '/question/get',
          'dataParams': {'testId': test_id},
          'forSlide': true,
          'fromSlideId': $(deck.getCurrentSlide()).attr('data-id'),
          'deck': deck,
          init: () => {
            return $.ajax({
              "url": this.config.initAction + '?testId=' + test_id + '&studentId=' + this.config.student_id,
              "type": "GET",
              "dataType": "json"
            })
          },
          onInitialized: () => {

            test.addEventListener("finish", storyTestResults);
            test.addEventListener("backToStory", this.backToStory.bind(this));

            this.deck.sync();
            this.deck.slide(0);

            stack.unshift({"story_id": currentStoryId, "slide_index": slide_index});
          }
        });
        test.run();
      }

      $(".reveal > .slides").on("click", "button[data-test-id]", function() {
        action(this);
      });

      const initTesting = () => {

        const elem = $("div.new-questions", deck.getCurrentSlide());
        if (!elem.length) {
          return;
        }

        const params = elem.data();
        params.studentId = this.config.student_id;

        const slidesPLayer = new SlidesPlayer(deck);

        const test = WikidsStoryTest.create(elem[0], {
          'dataUrl': '/question/get',
          'dataParams': params,
          'forSlide': false,
          'required': params.testRequired,
          'deck': deck,
          init: function() {
            return $.getJSON('/question/init', params);
          },
          onInitialized: function() {
            test.addEventListener("finish", function () {
              slidesPLayer.right();
            });
            test.addEventListener("nextSlide", function () {
              slidesPLayer.right();
            });
          }
        });
        test.run();
      }

      readySlides = [];

      const initEducation = () => {

        const currentSlideID = $(deck.getCurrentSlide()).attr('data-id');

        if (readySlides[currentSlideID]) {
          return;
        }

        readySlides[currentSlideID] = true;

        initTesting();
      }

      deck.addEventListener("slidechanged", initEducation);
      deck.addEventListener("ready", initEducation);
    },

    backToStory() {

      const getStoryData = (id) => {
        return $.ajax({
          "url": this.config.storyBodyAction + "/" + id,
          "type": "GET",
          "dataType": "json"
        });
      }

      const syncReveal = (data, slide_index) => {

        $(".reveal > .slides")
          .empty()
          .append(data);

        if (window["StoryBackground"]) {
          StoryBackground.init();
        }

        this.deck.sync();
        this.deck.slide(slide_index);

        //console.log('sync');
      }

      if (stack.length > 0) {

        const state = stack.shift();

        inTest = false;

        getStoryData(state.story_id)
          .done((data) => {

            syncReveal(data.html, state.slide_index);

            currentStoryId = state.story_id;
          });
      }
    },

    isQuestionSlide() {
      return true;
    },

    inTest() {
      return inTest;
    }
  }
}
