
function WikidsVideoPlayer(elemID, videoID, seekTo, duration, mute) {
    "use strict";

    seekTo = seekTo || 0;
    duration = duration || 0;

    var player,
        done = false;

    player = new Plyr('#' + elemID, {
        controls: []
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

    function pauseVideo() {
        player.pause();
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

    var loaded = false;

    function createPlayer() {
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

            WikidsVideoPlayer(elemID, videoID, seekTo, duration, mute);
        }
        loaded = false;
    }

    Reveal.addEventListener("slidechanged", function(event) {
        console.log("slidechanged");
        if (!loaded) {
            console.log("createPlayer");
            loaded = true;
            createPlayer();
        }
    });

    Reveal.addEventListener("ready", function(event) {
        console.log("ready");
        if (!loaded) {
            console.log("createPlayer");
            loaded = true;
            createPlayer();
        }
    });

    return {
        "createPlayer": createPlayer
    };
})();
