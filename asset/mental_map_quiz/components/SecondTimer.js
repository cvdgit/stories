function formatSecondsToHMS(totalSeconds) {
  const hours = Math.floor(totalSeconds / 3600);
  const minutes = Math.floor((totalSeconds % 3600) / 60);
  const seconds = totalSeconds % 60;

  const formattedHours = String(hours).padStart(2, '0');
  const formattedMinutes = String(minutes).padStart(2, '0');
  const formattedSeconds = String(seconds).padStart(2, '0');

  return `${formattedHours}:${formattedMinutes}:${formattedSeconds}`;
}

/**
 * @constructor
 */
function SecondTimer() {

  let intervalId;
  let timer = 0;

  return {
    /**
     * @param {HTMLElement} element
     */
    start(element) {
      timer = 0;
      element.innerText = formatSecondsToHMS(timer);
      intervalId = setInterval(() => {
        element.innerText = formatSecondsToHMS(++timer);
      }, 1000);
    },
    stop() {
      if (intervalId) {
        clearInterval(intervalId);
      }
    },
    getTimerSeconds() {
      return timer;
    }
  }
}

export default SecondTimer;
