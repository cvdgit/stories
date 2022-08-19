import SlidesPlayer from "../SlidesPlayer";

export default () => {
  return {

    deck: null,
    id: 'background',

    backgroundColorMap: {
      "dark": "#000000",
      "light": "#ffffff"
    },

    backgroundStorageItemName: "story_background",

    init(deck) {

      this.deck = deck;
    },

    initBackground() {

      const init = () => {
        console.log('StoryBackground.init');

        /*var background = localStorage.getItem(backgroundStorageItemName) || "dark";
        if (WikidsPlayer.isTestSlide()) {*/

        setBackgroundColor('light');

        /*}
        else {
            if (background !== "dark") {
                setBackgroundColor(background);
            }
        }*/

        this.deck.sync();
      }

      const switchBackground = () => {

        let color = "";

        if ($(".reveal").hasClass("has-dark-background")) {
          color = "light";
        } else {
          color = "dark";
        }

        setBackgroundColor(color);

        setBackgroundStorageItem(color);

        this.deck.sync();
      }

      const setBackgroundColor = (color) => {
        console.log("setBackgroundColor", color);

        color = this.backgroundColorMap[color] || this.backgroundColorMap["dark"];

        $(".slides section").attr("data-background-color", color);
      }

      const setBackgroundStorageItem = (color) => {
        localStorage.setItem(this.backgroundStorageItemName, color);
      }

      init();
    }
  }
}
