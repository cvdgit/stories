export default function createMouseWindowTracker() {
  let isMouseInsideWindow = true;

  const listeners = {
    leave: [],
    enter: []
  };

  function emit(eventName) {
    for (const callback of listeners[eventName]) {
      callback();
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

  document.addEventListener("mouseout", handleMouseOut);
  window.addEventListener("mouseenter", handleMouseEnter);

  document.addEventListener("visibilitychange", handleVisibilityChange);
  window.addEventListener("blur", handleBlur);

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

    destroy() {
      document.removeEventListener("mouseout", handleMouseOut);
      window.removeEventListener("mouseenter", handleMouseEnter);

      document.removeEventListener(
        "visibilitychange",
        handleVisibilityChange
      );

      window.removeEventListener("blur", handleBlur);

      listeners.leave = [];
      listeners.enter = [];
    }
  };
}
