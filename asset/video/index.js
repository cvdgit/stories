import Plyr from 'plyr'
import 'plyr/dist/plyr.css'

const urlParams = new URLSearchParams(location.search)

const options = {
  seekTo: Number(urlParams.get('t')) || 0,
  volume: 0.8,
  speed: 1,
  duration: Number(urlParams.get('d')) || 0
}

const controls = ['play', 'progress', 'current-time', 'mute', 'volume', 'captions', 'settings', 'pip', 'airplay', 'fullscreen']
const player = new Plyr('#player', {
  autoplay: false,
  controls: controls,
  clickToPlay: true,
  keyboard: true,
  captions: {
    active: true,
    language: 'en',
    update: true
  }
})

player.on('ready', (event) => {
  //player.play()
  player.speed = options.speed
  if (options.seekTo > 0) {
    player.currentTime = options.seekTo
  }
  player.volume = options.volume
})

player.once('canplay', (event) => {
  if (options.seekTo > 0) {
    player.currentTime = options.seekTo
  }
})

let pauseTimeoutID = null

player.on('playing', (event) => {
  if (options.duration > 0 && !pauseTimeoutID) {
    let timeout = options.duration - (player.currentTime - options.seekTo)
    pauseTimeoutID = setTimeout(pauseVideo, timeout * 1000);
  }
  if (options.seekTo > 0 && player.currentTime < options.seekTo) {
    player.currentTime = options.seekTo
  }
})

player.on('pause', () => {
  if (pauseTimeoutID) {
    clearTimeout(pauseTimeoutID)
    pauseTimeoutID = null
  }
})

let done = false

player.on('play', () => {
  if (done) {
    player.currentTime = parseFloat(options.seekTo)
    done = false
  }
});

function pauseVideo() {
  player.pause()
  done = true
}
