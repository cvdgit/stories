import Plyr from "plyr";
import "plyr/dist/plyr.css";

function WikidsVideoPlayer(elemID, options, slidesPlayer) {

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
    autoplay: true,
    controls: controls,
    clickToPlay: true,
    keyboard: false
  });

  var sourceIsYouTube = options.source === 1,
    sourceIsFile = options.source === 2;

  player.on("ready", function(event) {
    player.play();
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
            console.log('currentTime', player.currentTime);
            var timeout = options.duration - (player.currentTime - options.seekTo);
            console.log('duration', options.duration);
            console.log('timeout', timeout * 1000);
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
    console.log('PAUSE');
    if (pauseTimeoutID) {
      console.log('CLEAR TIMEOUT ' + options.videoID, pauseTimeoutID);
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
    console.log('PAUSE VIDEO');
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
          slidesPlayer.right();
        }, 1500);
      }
    }
  }

  function inTransition() {
    return slidesPlayer.inTransition();
  }

  function backToStory() {
    slidesPlayer.backToStory(function () {
      //if (window['WikidsSeeAlso'] && WikidsSeeAlso.autoplay()) {
      //  setTimeout(RevealAudioSlideshow.playCurrentAudio, 1000);
      //}
    });
  }

  return player;
}

let loaded = [];
let players = [];
let player = null;

export default function Video(slidesPlayer, config) {

  loaded = [];

  return {

    createPlayer(currentSlide) {

      console.debug("createPlayer");

      if (!currentSlide) {
        console.debug("createPlayer.noCurrentSlide");
        return;
      }

      if (loaded[$(currentSlide).attr('data-id')]) {
        console.debug("createPlayer.loaded");
        return;
      }

      const elem = $("div.wikids-video-player", currentSlide);
      if (!elem.length) {
        console.debug("createPlayer.noVideo");
        return;
      }

      const options = {
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

      const sourceIsYouTube = options.source === 1,
        sourceIsFile = options.source === 2;

      const elemID = "video" + new Date().getTime();

      if (sourceIsFile) {

        const $video = $('<video/>', {
          id: elemID,
          controls: true,
          src: options.videoID,
          type: 'video/mp4'
        });

        elem.replaceWith($video);
      } else {

        elem.attr("id", elemID);
        elem.addClass("plyr__video-embed");
        elem.attr("data-plyr-provider", "youtube");
        elem.attr("data-plyr-embed-id", options.videoID);
      }

      loaded[$(currentSlide).attr('data-id')] = true;

      player = WikidsVideoPlayer(elemID, options, slidesPlayer);
      players.push(player);
    }
  }
};
