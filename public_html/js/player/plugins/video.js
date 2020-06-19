
function WikidsVideoPlayer(elemID, videoID, seekTo, duration, mute, speed, showControls, volume) {
    "use strict";

    seekTo = seekTo || 0;
    duration = duration || 0;
    speed = speed || 1;
    showControls = showControls || false;
    volume = volume || 0.8;

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
        player.speed = parseInt(speed);
        player.currentTime = parseFloat(seekTo);
        player.volume = parseFloat(volume);
    });

    player.on("statechange", function(event) {
        if (event.detail.code === 1 && !done) {
            setTimeout(pauseVideo, duration * 1000);
            done = true;
        }
    });

    player.on("pause", function() {
        if (window["TransitionSlide"] && TransitionSlide.getInTransition()) {
            TransitionSlide.backToStory(function() {
                if (window['WikidsSeeAlso'] && WikidsSeeAlso.autoplay()) {
                    setTimeout(RevealAudioSlideshow.playCurrentAudio, 1000);
                }
            });
        }
    });

    function pauseVideo() {
        player.pause();
    }

    return player;
}


var WikidsVideo = window.WikidsVideo || (function() {
    "use strict";

    function getCurrentSlide() {
        return Reveal.getCurrentSlide();
    }

    var loaded = false;
    var config = Reveal.getConfig().video;
    var player;

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
                mute = elem.attr("data-mute") === "true",
                speed = elem.attr("data-speed"),
                volume = elem.attr("data-volume");

            elem.addClass("plyr__video-embed");
            elem.attr("data-plyr-provider", "youtube");
            elem.attr("data-plyr-embed-id", videoID);

            player = WikidsVideoPlayer(elemID, videoID, seekTo, duration, mute, speed, config.showControls, volume);
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
        },
        "setBeginVideo": function(el) {
            $(el).parent().parent().find("input[type=text]").val(player.currentTime);
        },
        "setEndVideo": function(el, beginElementID) {
            var beginTime = parseFloat($("#" + beginElementID).val());
            $(el).parent().parent().find("input[type=text]").val(player.currentTime - beginTime);
        }
    };
})();
