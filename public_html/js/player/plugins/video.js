
function WikidsVideoPlayer(elemID, options) {
    "use strict";

    console.log('WikidsVideoPlayer');

    if (options.mute) {
        options.volume = 0;
    }

    var player,
        done = false;

    var controls = ['play', 'current-time', 'mute', 'volume'];
    if (options.showControls) {
        controls = ['play', 'progress', 'current-time', 'mute', 'volume', 'captions', 'settings', 'pip', 'airplay', 'fullscreen'];
    }

    player = new Plyr('#' + elemID, {
        autoplay: true,
        controls: controls,
        clickToPlay: false,
        keyboard: false
    });

    player.on("ready", function(event) {
        console.log('player.ready');
        player.play();
        player.speed = options.speed;
        player.currentTime = options.seekTo;
        player.volume = options.volume;
    });

    var pauseTimeoutID;

    player.on("statechange", function(event) {
        if (event.detail.code === 1 && !done) {
            var timeout = options.duration - (player.currentTime - options.seekTo);
            if (timeout > 0) {
                console.log('statechange', player.currentTime, timeout);
                pauseTimeoutID = setTimeout(pauseVideo, timeout * 1000);
            }
        }
    });

    player.on("pause", function() {
        if (pauseTimeoutID) {
            clearTimeout(pauseTimeoutID);
        }
    });

    player.on("end", function() {
        console.log('stop');
    });

    player.on("play", function() {
        if (done) {
            player.currentTime = parseFloat(options.seekTo);
            done = false;
        }
    });

    function pauseVideo() {
        console.log('pauseVideo');
        player.pause();
        done = true;
        if (inTransition()) {
            backToStory();
        }
        else {
            if (options.toNextSlide) {
                setTimeout(function () {
                    WikidsPlayer.right();
                }, 1500);
            }
        }
    }

    function inTransition() {
        return window["TransitionSlide"] && TransitionSlide.getInTransition();
    }

    function backToStory() {
        TransitionSlide.backToStory(function () {
            if (window['WikidsSeeAlso'] && WikidsSeeAlso.autoplay()) {
                setTimeout(RevealAudioSlideshow.playCurrentAudio, 1000);
            }
        });
    }

    return player;
}


var WikidsVideo = (function() {
    "use strict";

    function getCurrentSlide() {
        return Reveal.getCurrentSlide();
    }

    var loaded = [];
    var config = Reveal.getConfig().video;
    var player;

    function createPlayer(currentSlide) {
        console.log("createPlayer");

        currentSlide = currentSlide || getCurrentSlide();
        if (!currentSlide) {
            console.log("createPlayer.noCurrentSlide");
            return;
        }

        if (loaded[$(currentSlide).attr('data-id')]) {
            console.log("createPlayer.loaded");
            return;
        }

        var elem = $("div.wikids-video-player", currentSlide);
        var elemID = "video" + new Date().getTime();
        elem.attr("id", elemID);

        if (elem.length) {

            var options = {
                videoID: elem.attr("data-video-id"),
                seekTo: parseFloat(elem.attr("data-seek-to") || 0),
                duration: parseInt(elem.attr("data-video-duration")),
                mute: elem.attr("data-mute") === "true",
                toNextSlide: elem.attr("data-to-next-slide") === "true",
                speed: parseInt(elem.attr("data-speed") || 1),
                volume: parseFloat(elem.attr("data-volume") || 0.8),
                showControls: config.showControls || false
            };

            elem.addClass("plyr__video-embed");
            elem.attr("data-plyr-provider", "youtube");
            elem.attr("data-plyr-embed-id", options.videoID);

            loaded[$(currentSlide).attr('data-id')] = true;
            player = WikidsVideoPlayer(elemID, options);
        }
    }

    Reveal.addEventListener("slidechanged", function(event) {
        console.log("Reveal.slidechanged [video.js]");
        createPlayer(event.currentSlide);
    });

    Reveal.addEventListener("ready", function(event) {
        console.log("Reveal.ready [video.js]");
        createPlayer(event.currentSlide);
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
        },
        "reset": function() {
            loaded = [];
        }
    };
})();
