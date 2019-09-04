
var WikidsLinks = window.WikidsLinks || (function() {
    "use strict";

    var loaded = false,
        config = Reveal.getConfig().linksConfig;

    var root,
        links = [];

    function getCurrentSlide() {
        return Reveal.getCurrentSlide();
    }

    function getCurrentSlideID() {
        return $(getCurrentSlide()).attr("data-id");
    }

    function initRootElement() {
        if (root && $(root, getCurrentSlide()).length) {
            $(root, getCurrentSlide()).remove();
        }
        root = $("<div/>")
            .addClass("wikids-slide-links");
        $("<h4/>")
            .text("Интересные ссылки")
            .appendTo(root);
        $("<div/>")
            .addClass("wikids-slide-links-links")
            .appendTo(root);
        $("<a/>")
            .addClass("hide")
            .attr("href", "#")
            .addClass("wikids-slide-links-more")
            .text("показать еще")
            .on("click", showMoreLinks)
            .appendTo(root);
        root.appendTo(getCurrentSlide());
    }

    function showLinks() {
        if (loaded) {
            return;
        }
        loaded = true;
        var currentSlideID = getCurrentSlideID();
        links = config.links.filter(function(link) {
            return link.slideID === currentSlideID;
        });
        if (links.length) {
            initRootElement();
            drawLinks(links);
        }
    }

    function toggleLinksBlock() {
        var block = $(this).parent();
        block.toggleClass("wikids-slide-links-mini");
        block.find("a").toggleClass("hide");
    }

    function showMoreLinks() {
        var block = $(this).parent();
        block.find("a").removeClass("hide");
        block.toggleClass("wikids-slide-links-more-on");
        if (block.hasClass("wikids-slide-links-more-on")) {
            $(this).text("скрыть");
        }
        else {
            drawLinks(links);
        }
        return false;
    }

    function drawLinks(links) {
        var container = $(".wikids-slide-links-links", root).empty(),
            linkNumber = 0,
            showMoreLink;
        links.forEach(function(link) {
            var button = $("<a/>")
                .attr("href", link.href)
                .attr("target", "_blank")
                .addClass("wikids-slide-links-link")
                .text(link.title)
                .appendTo(container);
            if (linkNumber >= 3) {
                showMoreLink = true;
                button.addClass("hide");
            }
            else {
                linkNumber++;
            }
        });
        if (showMoreLink) {
            $(".wikids-slide-links-more", root)
                .removeClass("hide")
                .text("показать еще (" + (links.length - linkNumber) + ")");
        }
    }

    Reveal.addEventListener("ready", function(event) {
        showLinks();
    });

    Reveal.addEventListener("slidechanged", function(event) {
        loaded = false;
        showLinks();
    });

})();
