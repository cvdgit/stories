
var WikidsSeeAlso = window.WikidsSeeAlso || (function() {
    "use strict";

    var config = Reveal.getConfig().seeAlso;

    function seeAlsoStories() {
        $.get(config.action).done(function(data) {
            $(Reveal.getCurrentSlide()).empty().append(data.html);
            Reveal.sync();
            setTimeout(showAutoPlayOverlay, 1000);
        });
    }

    function hideAutoPlayOverlay() {
        $(".autoplay-overlay", Reveal.getCurrentSlide()).fadeOut();
    }

    function showAutoPlayOverlay() {

        var $story = $(".story-item:eq(0)", Reveal.getCurrentSlide()),
            storyTitle = $("h3.story-item-name", $story).text(),
            storyLink = $("a", $story).attr("href");

        var second = 10;
        var $timer = $("<span/>")
            .addClass("autoplay-timer")
            .html('<i class="autoplay-timer-title">Следующая история: <a href="' + storyLink + '">' + storyTitle + '</a></i><br><i class="autoplay-timer-second">' + second + '</i><br><button>Отмена</button>');

        $("button", $timer).on("click", function() {
            clearInterval(timeout);
            hideAutoPlayOverlay();
        });

        $(".autoplay-overlay", Reveal.getCurrentSlide())
            .on('click', function() {
                clearInterval(timeout);
                hideAutoPlayOverlay();
            })
            .append($timer)
            .fadeIn();

        var timeout = setInterval(function() {
            second--;
            $(".autoplay-timer-second", $timer).text(second);
            if (second <= 1) {
                clearInterval(timeout);
                location.href = storyLink;
            }
        }, 1000);
    }

    Reveal.addEventListener("slidechanged", function(event) {
        if (Reveal.isLastSlide()) {
            seeAlsoStories();
        }
    });

})();
