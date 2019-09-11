
var loadedYT = false;
function onYouTubeIframeAPIReady() {
    "use strict";

    loadedYT = true;
    WikidsVideo.createPlayer();
}

function WikidsVideoPlayer(elemID, videoID, seekTo, duration) {
    "use strict";

    seekTo = seekTo || 0;
    duration = duration || 0;

    var player,
        done = false;
    player = new YT.Player(elemID, {
        height: '100%',
        width: '100%',
        videoId: videoID,
        events: {
            'onReady': function(event) {
                if (seekTo > 0) {
                    event.target.seekTo(seekTo);
                }
            },
            'onStateChange': function(event) {
                if (event.data === YT.PlayerState.PLAYING && !done) {
                    setTimeout(pauseVideo, duration * 1000);
                    done = true;
                }
            }
        }
    });

    function pauseVideo() {
        player.pauseVideo();
    }
}

var WikidsVideo = window.WikidsVideo || (function() {
    "use strict";

    function getCurrentSlide() {
        return Reveal.getCurrentSlide();
    }

    var tag = document.createElement('script');
    tag.src = "https://www.youtube.com/iframe_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    function createPlayer() {
        var elem = $("div.wikids-video-player", getCurrentSlide());
        var elemID = "video" + new Date().getTime();
        elem.attr("id", elemID);
        if (elem.length) {
            var videoID = elem.attr("data-video-id"),
                seekTo = elem.attr("data-seek-to"),
                duration = elem.attr("data-video-duration");
            WikidsVideoPlayer(elemID, videoID, seekTo, duration);
        }
    }

    Reveal.addEventListener("slidechanged", function(event) {
        if (loadedYT) {
            createPlayer();
       }
    });

    return {
        "createPlayer": createPlayer
    };
})();
