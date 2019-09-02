
var WikidsLinks = window.WikidsLinks || (function() {
    "use strict";

    var loaded = false,
        config = Reveal.getConfig().linksConfig;



    function getCurrentSlideID() {
        return $(Reveal.getCurrentSlide()).attr("data-id");
    }

    function showLinks() {
        if (loaded) {
            return;
        }
        loaded = true;
        var currentSlideID = getCurrentSlideID();
        var links = config.links.filter(function(link) {
            return link.slideID === currentSlideID;
        });
        if (links.length) {
            drawLinks(links);
        }
    }

    function toggleLinksBlock() {
        var block = $(this).parent();
        block.toggleClass("wikids-slide-links-mini");
        block.find("a").toggleClass("hide");
    }

    function drawLinks(links) {
        if ($(".wikids-slide-links", Reveal.getCurrentSlide()).length) {
            $(".wikids-slide-links", Reveal.getCurrentSlide()).remove();
        }
        var root = $("<div/>")
            .addClass("wikids-slide-links");
        $("<h4/>")
            .text("Интересные ссылки")
            .attr("title", "Свернуть")
            .on("click", toggleLinksBlock)
            .appendTo(root);
        links.forEach(function(link) {
            var button = $("<a/>")
                .attr("href", link.href)
                .attr("target", "_blank")
                .text(link.title)
                .appendTo(root);
        });
        root.appendTo(Reveal.getCurrentSlide());
    }

    Reveal.addEventListener("ready", function(event) {
        showLinks();
    });

    Reveal.addEventListener("slidechanged", function(event) {
        loaded = false;
        showLinks();
    });

})();
