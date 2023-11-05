
let loaded = false;

export default function Links(config) {

  loaded = false;

  let root;
  let links = [];

  const initRootElement = (currentSlide) => {

    if (root && $(root, currentSlide).length) {
      $(root, currentSlide).remove();
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

    root.appendTo(currentSlide);
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

  return {

    showLinks(currentSlide, currentSlideId, unsetLoaded = false) {

      if (unsetLoaded) {
        loaded = false;
      }

      if (loaded) {
        return;
      }

      loaded = true;

      links = config.links.filter(function(link) {
        return parseInt(link.slideID) === parseInt(currentSlideId);
      });

      if (links.length) {
        initRootElement(currentSlide);
        drawLinks(links);
      }
    }
  };
};
