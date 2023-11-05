
function SlidesPlayer(deck) {

  return {

    left() {

      if (deck.isFirstSlide()) {

        if (deck.hasPlugin('transition')) {
          const transition = deck.getPlugin('transition');
          transition.backToStory();
        }

      }
      else {

        deck.prev();
      }
    },

    right() {
      if (deck.isLastSlide()) {

        if (deck.hasPlugin('transition')) {
          const transition = deck.getPlugin('transition');
          transition.backToStory();
        }

      }
      else {

        if (deck.hasPlugin('testing')) {

          const elem = $(deck.getCurrentSlide()).find('div.new-questions');

          if (elem.length) {

            const test = elem[0]['_wikids_test'];

            if (test !== undefined) {
              const canNext = test.canNext();
              if (!canNext) {
                return;
              }
            }
          }
        }

        deck.next();
      }
    },

    getCurrentSlide() {
      return deck.getCurrentSlide();
    },

    getCurrentSlideId() {
      return $(this.getCurrentSlide()).attr('data-id');
    },

    inTransition() {
      if (deck.hasPlugin('transition')) {
        const transition = deck.getPlugin('transition');
        return transition.getInTransition();
      }
      return false;
    },

    backToStory() {
      if (deck.hasPlugin('transition')) {
        const transition = deck.getPlugin('transition');
        transition.backToStory();
      }
    },

    goToSlide(storyId, slideId, backToNextSlide) {
      if (deck.hasPlugin('transition')) {
        const transition = deck.getPlugin('transition');
        transition.goToSlide(storyId, slideId, backToNextSlide);
      }
    }
  }
}

export default SlidesPlayer;
