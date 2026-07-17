export default function createMouseWindowTracker() {
  const FULLSCREEN_THRESHOLD = 8;
  const ZOOM_BORDER_THRESHOLD = 24;
  const DPR_THRESHOLD = 0.01;

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

  function captureMetrics() {
    return {
      outerWidth: window.outerWidth,
      outerHeight: window.outerHeight,
      innerWidth: window.innerWidth,
      innerHeight: window.innerHeight,
      dpr: window.devicePixelRatio,
      viewportScale: window.visualViewport?.scale ?? 1,
      viewportWidth: window.visualViewport?.width ?? window.innerWidth,
      viewportHeight: window.visualViewport?.height ?? window.innerHeight
    };
  }

  function checkFullscreen() {
    return (
      Math.abs(window.screenX) <= FULLSCREEN_THRESHOLD &&
      Math.abs(window.screenY) <= FULLSCREEN_THRESHOLD &&
      Math.abs(window.innerWidth - screen.availWidth) <= FULLSCREEN_THRESHOLD &&
      Math.abs(window.outerHeight - screen.availHeight) <= FULLSCREEN_THRESHOLD
    );
  }

  function updateFullscreenState() {
    const newState = checkFullscreen();

    if (newState !== isFullscreenWindow) {
      isFullscreenWindow = newState;

      emit(isFullscreenWindow ? "fullscreen" : "windowed");
    }
  }

  /**
   * Максимально возможное определение изменения zoom страницы.
   * Возвращает:
   * {
   *    zoomed: boolean,
   *    confidence: "low" | "medium" | "high",
   *    reasons: []
   * }
   */
  function detectZoom() {
    const current = captureMetrics();

    const reasons = [];

    //
    // 1. visualViewport.scale
    // (если браузер начал поддерживать desktop zoom)
    //
    if (
      window.visualViewport &&
      Math.abs(current.viewportScale - 1) > 0.001
    ) {
      reasons.push("visualViewport.scale");
    }

    //
    // 2. devicePixelRatio изменился
    //
    if (
      Math.abs(current.dpr - initialMetrics.dpr) > DPR_THRESHOLD
    ) {
      reasons.push("devicePixelRatio");
    }

    //
    // 3. Изменилось соотношение рамок браузера
    //
    const initialBorder =
      initialMetrics.outerWidth - initialMetrics.innerWidth;

    const currentBorder =
      current.outerWidth - current.innerWidth;

    if (
      Math.abs(currentBorder - initialBorder) >
      ZOOM_BORDER_THRESHOLD
    ) {
      reasons.push("outer-inner-width");
    }

    //
    // 4. Окно занимает экран,
    // но viewport неожиданно меньше.
    //
    const outerFullscreen =
      Math.abs(window.outerWidth - screen.availWidth) <=
      FULLSCREEN_THRESHOLD &&
      Math.abs(window.outerHeight - screen.availHeight) <=
      FULLSCREEN_THRESHOLD;

    if (
      outerFullscreen &&
      Math.abs(window.innerWidth - screen.availWidth) >
      FULLSCREEN_THRESHOLD * 2
    ) {
      reasons.push("viewport-width");
    }

    //
    // Оценка достоверности
    //
    let confidence = "low";

    if (reasons.length >= 3) {
      confidence = "high";
    } else if (reasons.length === 2) {
      confidence = "medium";
    }

    return {
      zoomed: reasons.length >= 2,
      confidence,
      reasons
    };
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
    console.log('resize')
    updateFullscreenState();
  }

  window.addEventListener("resize", handleResize);

  return {

    initEvents() {
      document.addEventListener("mouseout", handleMouseOut);
      window.addEventListener("mouseenter", handleMouseEnter);
      document.addEventListener("visibilitychange", handleVisibilityChange);
      window.addEventListener("blur", handleBlur);
    },

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

    hasZoom() {
      return detectZoom().zoomed;
    },

    getZoomInfo() {
      return detectZoom();
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
