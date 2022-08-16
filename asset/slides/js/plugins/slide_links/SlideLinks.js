
export default (config) => {

  return {

    processLinks(elem) {
      $(elem)
        .find('div.sl-block[data-block-type=text]')
        .find('a')
        .each(function() {
          var linkElement = $(this);
          var href = linkElement.attr('href');
          var site = config.site.replace(/https?:\/\//, '');
          var regex = new RegExp(site + '([a-z0-9\\-]+)#\\/(\\d+)', 'i');
          if (regex.test(href)) {
            var matches = href.match(regex);
            linkElement
              .off('click')
              .on('click', function(e) {
                e.preventDefault();

                var storyAlias = matches[1];
                var slideNumber = matches[2];

                var $hintWrapper = $('<div/>', {'class': 'slide-hints-wrapper'});
                var $hintBackground = $('<div/>', {'class': 'slide-hints-background'});
                var $hintInner = $('<div/>', {'class': 'slide-hints-inner'});
                var $hint = $('<div/>', {'class': 'slide-hints'});

                $hintBackground.appendTo($hintWrapper);
                $hintInner.appendTo($hintWrapper);

                $('<header/>', {'class': 'slide-hints-header'})
                  .append(
                    $('<div/>', {'class': 'header-actions'})
                      .append(
                        $('<button/>', {
                          'class': 'hints-close',
                          'html': '&times;'
                        })
                          .on('click', function() {
                            $hintWrapper.hide();
                            $(this).remove();
                            $('.reveal .story-controls').show();
                          })
                      )
                  )
                  .appendTo($hintInner);

                $('<iframe>', {
                  'src': '/question-hints/view-slide?alias=' + storyAlias + '&number=' + slideNumber,
                  'frameborder': 0,
                  'scrolling': 'no',
                  'width': '100%',
                  'height': '100%'
                }).appendTo($hint);

                $hint.appendTo($hintInner);

                $('.reveal .story-controls').hide();
                $('.reveal .slides section.present').append($hintWrapper);
              });
          }
        });
    }
  }
};
