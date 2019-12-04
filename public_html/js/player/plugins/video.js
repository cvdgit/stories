
function WikidsVideoPlayer(elemID, videoID, seekTo, duration, mute, showControls) {
    "use strict";

    seekTo = seekTo || 0;
    duration = duration || 0;
    showControls = showControls || false;

    var player,
        done = false;

    var controls = [];
    if (showControls) {
        controls = ['play', 'progress', 'current-time', 'mute', 'volume', 'captions', 'settings', 'pip', 'airplay', 'fullscreen'];
    }

    player = new Plyr('#' + elemID, {
        controls: controls
    });

    player.on("ready", function(event) {
        player.play();
        player.currentTime = parseInt(seekTo);
        if (mute) {
            player.volume = 0;
        }
        else {
            player.volume = 0.8;
        }
    });

    player.on("statechange", function(event) {
        if (event.detail.code === 1 && !done) {
            setTimeout(pauseVideo, duration * 1000);
            done = true;
        }
    });

    player.on("pause", function() {
        if (TransitionSlide.getInTransition()) {
            TransitionSlide.backToStory(function() {
                setTimeout(RevealAudioSlideshow.playCurrentAudio, 1000);
            });
        }
    });

    function pauseVideo() {
        player.pause();
    }

}


var WikidsVideo = window.WikidsVideo || (function() {
    "use strict";

    function getCurrentSlide() {
        return Reveal.getCurrentSlide();
    }

    var loaded = false;

    var config = Reveal.getConfig().video;

    function createPlayer() {
        //console.log("createPlayer");
        loaded = false;
        var elem = $("div.wikids-video-player", getCurrentSlide());
        var elemID = "video" + new Date().getTime();
        elem.attr("id", elemID);
        if (elem.length) {
            var videoID = elem.attr("data-video-id"),
                seekTo = elem.attr("data-seek-to"),
                duration = elem.attr("data-video-duration"),
                mute = elem.attr("data-mute") === "true";

            elem.addClass("plyr__video-embed");
            elem.attr("data-plyr-provider", "youtube");
            elem.attr("data-plyr-embed-id", videoID);

            WikidsVideoPlayer(elemID, videoID, seekTo, duration, mute, config.showControls);
        }
    }

    Reveal.addEventListener("slidechanged", function() {
        //console.log("slidechanged");
        if (!loaded) {
            loaded = true;
            createPlayer();
        }
    });

    Reveal.addEventListener("ready", function() {
        //console.log("ready");
        if (!loaded) {
            loaded = true;
            createPlayer();
        }
    });

    return {
        "createPlayer": createPlayer,
        "showControls": function() {
            return config.showControls;
        }
    };
})();
