
var loadedYT = false;
function onYouTubeIframeAPIReady() {
    "use strict";

    loadedYT = true;
    WikidsVideo.createPlayer();
}

var WikidsVideo = window.WikidsVideo || (function() {
    "use strict";

    var loaded = false,
        player;

    function getCurrentSlide() {
        return Reveal.getCurrentSlide();
    }

    var tag = document.createElement('script');
    tag.src = "https://www.youtube.com/iframe_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    function createPlayer() {
        if (loaded) {
            return;
        }
        loaded = true;

        var elem = $(".wikids-video-player", getCurrentSlide());
        var elemID = "video" + new Date().getTime();
        elem.attr("id", elemID);

        if (elem.length) {
            var videoID = elem.attr("data-video-id"),
                seekTo = elem.attr("data-seek-to");
            player = new YT.Player(elemID, {
                height: '100%',
                width: '100%',
                videoId: videoID,
                playerVars: {
/*                    'controls': 0,
                    'disablekb': 1,
                    'iv_load_policy': 3,
                    'modestbranding': 1,
                    'rel': 0,
                    'showinfo': 0*/
                },
                events: {
                    'onReady': function(event) {
                        if (seekTo) {
                            event.target.seekTo(seekTo);
                        }
                    },
                    'onStateChange': function(event) {
                        if (event.data === YT.PlayerState.PLAYING) {
                            event.target.pauseVideo();
                        }
                    }
                }
            });

        }
    }

    Reveal.addEventListener("ready", function(event) {
        //createPlayer();
    });

    Reveal.addEventListener("slidechanged", function(event) {
        if (loadedYT) {
            loaded = false;
            createPlayer();
        }
    });

    return {
        "createPlayer": createPlayer
    }
})();
