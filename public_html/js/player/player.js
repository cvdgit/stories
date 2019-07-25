
var Wikids = (function() {

    function getRevealConfig()
    {
        return WikidsRevealConfig;
    }

    return {
        getRevealConfig: getRevealConfig
    };
})()

var WikidsStoryFeedback = (function() {

    var config = Wikids.getRevealConfig().feedbackConfig;

    function send(data) {
        return $.ajax({
            url: config.action,
            type: 'POST',
            dataType: 'json',
            data: data
        });
    }

    function sendFeedback() {
        var data = {
            'slide_number': (Reveal.getIndices().h + 1)
        }
        send(data)
            .done(function(response) {
                if (response.success) {
                    alert('Спасибо!');
                }
            });
    }

    return {
        sendFeedback: sendFeedback
    }
})();

var WikidsPlayer = (function(document, $) {

    var $playerContainer = $('.story-container');

    function enterFullscreen()
    {
        var element = $playerContainer[0];
        var requestMethod = element.requestFullscreen ||
            element.webkitRequestFullscreen ||
            element.webkitRequestFullScreen ||
            element.mozRequestFullScreen ||
            element.msRequestFullscreen;
        if (requestMethod) {
            requestMethod.apply(element);
        }
    }

    function closeFullscreen()
    {
        var element = document;
        var requestMethod = element.exitFullscreen ||
            element.exitFullScreen ||
            element.mozCancelFullScreen ||
            element.webkitExitFullscreen ||
            element.webkitCancelFullScreen ||
            element.msExitFullscreen;
        if (requestMethod) {
            requestMethod.apply(element);
        }
    }

    function inFullscreen()
    {
        //return (window.screenTop && window.screenY);
        return ($(":fullscreen").length > 0);
    }

    function toggleFullscreen()
    {
        if (inFullscreen()) {
            closeFullscreen();
        } else {
            enterFullscreen();
        }
    }

    return {
        "toggleFullscreen": toggleFullscreen,
        "inFullscreen": inFullscreen,
        "left": function() {
            if (Reveal.isFirstSlide()) {
                TransitionSlide.backToStory();
            }
            else {
                Reveal.prev();
            }
        },
        "right": function() {
            if (Reveal.isLastSlide()) {
                TransitionSlide.backToStory();
            }
            else {
                Reveal.next();
            }
        }
    };
})(document, $);

function onSlideMouseDown(e) {
    e = e || window.event;
    if ($(e.target).hasClass("story-controls")
        || $(e.target).parents(".story-controls").length
        || $(e.target).hasClass("btn"))  {
        return;
    }
    switch (e.which) {
        case 1: WikidsPlayer.right(); break;
        case 3: WikidsPlayer.left(); break;
    }
}

if (!$.browser.mobile) {
    Reveal.addEventListener("mousedown", onSlideMouseDown, false);
}

window.addEventListener("orientationchange", function() {
    if (window.orientation === 90) {
        WikidsPlayer.toggleFullscreen();
    }
}, false);
