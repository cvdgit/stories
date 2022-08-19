
export default () => {

  const $playerContainer = $(".story-container");
  let fullScreenMode = false;

  function enterFullscreen() {

    const element = $playerContainer[0];
    const requestMethod = element.requestFullscreen ||
      element.webkitRequestFullscreen ||
      element.webkitRequestFullScreen ||
      element.mozRequestFullScreen ||
      element.msRequestFullscreen;

    if (requestMethod) {
      requestMethod.apply(element);
      fullScreenMode = true;
    }
  }

  function closeFullscreen() {

    const element = document;
    const requestMethod = element.exitFullscreen ||
      element.exitFullScreen ||
      element.mozCancelFullScreen ||
      element.webkitExitFullscreen ||
      element.webkitCancelFullScreen ||
      element.msExitFullscreen;

    if (requestMethod) {
      requestMethod.apply(element);
      fullScreenMode = false;
    }
  }

  return {

    toggleFullscreen() {
      if (this.inFullscreen()) {
        closeFullscreen();
      } else {
        enterFullscreen();
      }
    },

    inFullscreen() {
      return $(":fullscreen").length > 0;
    }

  }
}
