
var WikidsSeeAlso = window.WikidsSeeAlso || (function() {
    "use strict";

    var config = Reveal.getConfig().seeAlso;

    function seeAlsoStories() {
        $.get(config.action).done(function(data) {
            $(Reveal.getCurrentSlide()).empty().append(data.html);
            Reveal.sync();
        });
    }

    Reveal.addEventListener("slidechanged", function(event) {
        if (Reveal.isLastSlide()) {
            seeAlsoStories();
        }
    });

})();
