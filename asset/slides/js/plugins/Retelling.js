import SlidesConfig from "../SlidesConfig";
import SlidesPlayer from "../SlidesPlayer";

let stack = [];
let inTest = false;
let currentStoryId = null;

let readySlides = [];

export default function Retelling() {
  return {

    id: 'retelling',
    deck: null,
    config: {},


    init(deck) {
      this.deck = deck

      const slidesConfig = new SlidesConfig()
      this.config = slidesConfig.get(this.id)

      currentStoryId = this.config.story_id
      stack = [];
      const container = $('.reveal > .slides')

      const init = () => {

        const elem = $('div.retelling-block', deck.getCurrentSlide())
        if (!elem.length) {
          return
        }

        const retellingId = elem.attr('data-retelling-id')
        if (!retellingId) {
          throw new Error('Retelling id not found')
        }

        const params = elem.data()
        params.story_id = this.config.story_id
        params.studentId = this.config.student_id

        const slidesPLayer = new SlidesPlayer(deck)

        const retelling = retellingBuilder.create(elem[0], deck, {
          init: async () => {
            const response = await fetch(`/retelling/init`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
              },
              body: JSON.stringify({
                story_id: params.story_id,
                slide_id: $(deck.getCurrentSlide()).attr('data-id'),
                id: retellingId
              })
            })

            const json = await response.json()
            return {...json}
          },
          ...params
        }, $(deck.getCurrentSlide()).attr('data-id'), MicrophoneChecker)
        retelling.run()
      }

      readySlides = []

      function initRetelling() {
        const currentSlideID = $(deck.getCurrentSlide()).attr('data-id');
        if (readySlides[currentSlideID] && !$(deck.getCurrentSlide()).find('.retelling-block').is(':empty')) {
          return;
        }
        readySlides[currentSlideID] = true;
        init()
      }

      deck.addEventListener('slidechanged', initRetelling)
      deck.addEventListener('ready', initRetelling)
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

    /*isQuestionSlide() {
      return true;
    },

    inTest() {
      return inTest;
    }*/
  }
}
