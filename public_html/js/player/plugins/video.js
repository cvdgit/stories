
function WikidsVideoPlayer(elemID, options) {
    "use strict";

    console.log('WikidsVideoPlayer');

    if (options.mute) {
        options.volume = 0;
    }

    var player,
        done = false;

    //var controls = ['play', 'current-time', 'mute', 'volume'];
    var controls = [];
    if (options.showControls) {
        controls = ['play', 'progress', 'current-time', 'mute', 'volume', 'captions', 'settings', 'pip', 'airplay', 'fullscreen'];
    }

    player = new Plyr('#' + elemID, {
        autoplay: options.duration > 0,
        controls: controls,
        clickToPlay: true,
        keyboard: false
    });

    var sourceIsYouTube = options.source === 1,
        sourceIsFile = options.source === 2;

    player.on("ready", function(event) {
      if (options.duration > 0) {
        player.play();
      }
        player.speed = options.speed;
        if (options.seekTo > 0) {
            player.currentTime = options.seekTo;
        }
        player.volume = options.volume;
    });

    if (sourceIsFile) {
        player.once('canplay', function (event) {
            if (options.seekTo > 0) {
                player.currentTime = options.seekTo;
            }
        });
    }

    var pauseTimeoutID = null;

    if (sourceIsFile) {
        player.on('playing', function (event) {
            if (!pauseTimeoutID) { //  && options.duration > 0
                console.log('PLAYING');
                var timeout = options.duration - (player.currentTime - options.seekTo);
                pauseTimeoutID = setTimeout(pauseVideo, timeout * 1000);
            }
        });
    }
    else {
        var playTimeout;
        //if (options.duration > 0) {
            player.on("statechange", function (event) {
                if (event.detail.code === 1 && !done) {
                    playTimeout = setInterval(function () {
                        if (!pauseTimeoutID) {
                            console.log('STATECHANGE');
                            //console.log('currentTime', player.currentTime);
                            var timeout = options.duration - (player.currentTime - options.seekTo);
                            //console.log('duration', options.duration);
                            //console.log('timeout', timeout * 1000);
                            pauseTimeoutID = setTimeout(pauseVideo, timeout * 1000);
                        }
                    }, 100);
                } else {
                    clearInterval(playTimeout);
                }
            });
        //}
    }

    player.on("pause", function() {
        //console.log('PAUSE');
        if (pauseTimeoutID) {
            //console.log('CLEAR TIMEOUT ' + options.videoID, pauseTimeoutID);
            clearTimeout(pauseTimeoutID);
            pauseTimeoutID = null;
        }
    });

    player.on("end", function() {
        //console.log('END');
    });

    player.on("play", function() {
        if (done) {
            player.currentTime = parseFloat(options.seekTo);
            done = false;
        }
    });

    function pauseVideo() {
        //console.log('PAUSE VIDEO');
        player.pause();
        done = true;
        if (inTransition()) {
            console.log('backToStory');
            backToStory();
        }
        else {
            if (options.toNextSlide) {
                console.log('toNextSlide');
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

    console.log('WikidsVideo');

    function getCurrentSlide() {
        return Reveal.getCurrentSlide();
    }

    var loaded = [];
    var config = Reveal.getConfig().video;
    var player;
    var players = [];

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
        if (elem.length) {

            var options = {
                videoID: elem.attr("data-video-id"),
                seekTo: parseFloat(elem.attr("data-seek-to") || 0),
                duration: parseInt(elem.attr("data-video-duration") || 0),
                mute: elem.attr("data-mute") === "true",
                toNextSlide: elem.attr("data-to-next-slide") === "true",
                speed: parseInt(elem.attr("data-speed") || 1),
                volume: parseFloat(elem.attr("data-volume") || 0.8),
                showControls: config.showControls || false,
                source: parseInt(elem.attr('data-source'))
            };

            var sourceIsYouTube = options.source === 1,
                sourceIsFile = options.source === 2;

            var elemID = "video" + new Date().getTime();

            if (sourceIsFile) {
                var $video = $('<video/>', {
                    id: elemID,
                    controls: true,
                    src: options.videoID,
                    type: 'video/mp4'
                });
                elem.replaceWith($video);
            }
            else {
                elem.attr("id", elemID);
                elem.addClass("plyr__video-embed");
                elem.attr("data-plyr-provider", "youtube");
                elem.attr("data-plyr-embed-id", options.videoID);
            }

            loaded[$(currentSlide).attr('data-id')] = true;
            player = WikidsVideoPlayer(elemID, options);
            players.push(player);
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
        },
        "destroyPlayers": function() {
            players.forEach(function(player) {
                player.destroy();
            });
        },
        "pauseLastPlayer": function() {
            if (players.length === 0) {
                return;
            }
            var player = players.pop();
            if (player !== undefined) {
                player.pause();
            }
        }
    };
})();
