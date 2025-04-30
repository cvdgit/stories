import SlidesConfig from "../SlidesConfig";
import SlidesPlayer from "../SlidesPlayer";

let stack = [];
let inTest = false;
let currentStoryId = null;

let readySlides = [];

export default function MentalMap() {
  return {

    id: 'mental_map',
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

        const elem = $('div.mental-map', deck.getCurrentSlide())
        if (!elem.length) {
          return
        }

        const mentalMapId = elem.attr('data-mental-map-id')
        if (!mentalMapId) {
          throw new Error('Mental map id not found')
        }

        const params = elem.data()
        params.story_id = this.config.story_id
        params.studentId = this.config.student_id

        const slidesPLayer = new SlidesPlayer(deck)

        const mentalMap = window.mentalMapBuilder.create(elem[0], deck, {
          init: async () => {
            const response = await fetch(`/mental-map/init`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
              },
              body: JSON.stringify({
                story_id: params.story_id,
                id: mentalMapId
              })
            })

            const json = await response.json()
            return {...json}
          },
          ...params
        }, $(deck.getCurrentSlide()).attr('data-id'))
        mentalMap.run()
      }

      readySlides = []

      function initMentalMap() {
        const currentSlideID = $(deck.getCurrentSlide()).attr('data-id');
        if (readySlides[currentSlideID] && !$(deck.getCurrentSlide()).find('.mental-map').is(':empty')) {
          return;
        }
        readySlides[currentSlideID] = true;
        init()
      }

      deck.addEventListener('slidechanged', initMentalMap)
      deck.addEventListener('ready', initMentalMap)
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
