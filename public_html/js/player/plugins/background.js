
var StoryBackground = (function () {
    "use strict";

    var config = Reveal.getConfig().backgroundConfig;

    var backgroundColorMap = {
        "dark": "#000000",
        "light": "#ffffff"
    };

    var backgroundStorageItemName = "story_background";

    var background = localStorage.getItem(backgroundStorageItemName) || "dark";
    if (background !== "dark") {
        setBackgroundColor(background);
    }

    function switchBackground() {
        var color = "";
        if ($(".reveal").hasClass("has-dark-background")) {
            color = "light";
        } else {
            color = "dark";
        }
        setBackgroundColor(color);
        setBackgroundStorageItem(color);
        Reveal.sync();
    }

    function setBackgroundColor(color) {
        color = backgroundColorMap[color] || backgroundColorMap["dark"];
        $(".slides section").attr("data-background-color", color);
    }

    function setBackgroundStorageItem(color) {
        localStorage.setItem(backgroundStorageItemName, color);
    }

    return {
        "switchBackground": switchBackground
    };
})();