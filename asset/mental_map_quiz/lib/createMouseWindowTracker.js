export default function createMouseWindowTracker() {
  let isMouseInsideWindow = true;
  let isFullscreenWindow = checkFullscreen();

  const listeners = {
    leave: [],
    enter: [],
    fullscreen: [],
    windowed: []
  };

  function emit(eventName, value) {
    for (const callback of listeners[eventName]) {
      callback(value);
    }
  }

  function checkFullscreen() {
    const threshold = 8;
    return (
      Math.abs(window.screenX) <= threshold &&
      Math.abs(window.screenY) <= threshold &&
      Math.abs(window.outerWidth - screen.availWidth) <= threshold &&
      Math.abs(window.outerHeight - screen.availHeight) <= threshold
    );
  }

  function updateFullscreenState() {
    const newState = checkFullscreen();

    if (newState !== isFullscreenWindow) {
      isFullscreenWindow = newState;

      emit(isFullscreenWindow ? "fullscreen" : "windowed");
    }
  }

  function handleMouseOut(event) {
    if (!event.relatedTarget && !event.toElement) {
      if (isMouseInsideWindow) {
        isMouseInsideWindow = false;
        emit("leave");
      }
    }
  }

  function handleMouseEnter() {
    if (!isMouseInsideWindow) {
      isMouseInsideWindow = true;
      emit("enter");
    }
  }

  function handleVisibilityChange() {
    if (document.visibilityState !== "visible") {
      if (isMouseInsideWindow) {
        isMouseInsideWindow = false;
        emit("leave");
      }
    }
  }

  function handleBlur() {
    if (isMouseInsideWindow) {
      isMouseInsideWindow = false;
      emit("leave");
    }
  }

  function handleResize() {
    updateFullscreenState();
  }

  document.addEventListener("mouseout", handleMouseOut);
  window.addEventListener("mouseenter", handleMouseEnter);

  document.addEventListener("visibilitychange", handleVisibilityChange);
  window.addEventListener("blur", handleBlur);

  window.addEventListener("resize", handleResize);

  return {
    on(eventName, callback) {
      if (listeners[eventName]) {
        listeners[eventName].push(callback);
      }
    },

    off(eventName, callback) {
      if (listeners[eventName]) {
        listeners[eventName] =
          listeners[eventName].filter(fn => fn !== callback);
      }
    },

    isInside() {
      return isMouseInsideWindow;
    },

    isFullscreen() {
      return isFullscreenWindow;
    },

    destroy() {
      document.removeEventListener("mouseout", handleMouseOut);
      window.removeEventListener("mouseenter", handleMouseEnter);

      document.removeEventListener(
        "visibilitychange",
        handleVisibilityChange
      );

      window.removeEventListener("blur", handleBlur);
      window.removeEventListener("resize", handleResize);

      for (const key in listeners) {
        listeners[key] = [];
      }
    }
  };
}
