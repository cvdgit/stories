(function () {

  const container = document.querySelector("#unity-container");
  const canvas = document.querySelector("#unity-canvas");
  const loadingBar = document.querySelector("#unity-loading-bar");
  const progressBarFull = document.querySelector("#unity-progress-bar-full");
  const fullscreenButton = document.querySelector("#unity-fullscreen-button");
  const warningBanner = document.querySelector("#unity-warning");

  function unityShowBanner(msg, type) {
    function updateBannerVisibility() {
      warningBanner.style.display = warningBanner.children.length ? 'block' : 'none';
    }

    const div = document.createElement('div');
    div.innerHTML = msg;
    warningBanner.appendChild(div);
    if (type === 'error') {
      div.style = 'background: red; padding: 10px;';
    } else {
      if (type === 'warning') {
        div.style = 'background: yellow; padding: 10px;';
      }
      setTimeout(function () {
        warningBanner.removeChild(div);
        updateBannerVisibility();
      }, 5000);
    }
    updateBannerVisibility();
  }

  const gameLoaderConfig = window?.gameLoaderConfig
  if (!gameLoaderConfig) {
    throw new Error("Game Loader config not found")
  }

  const {folder} = gameLoaderConfig

  const buildUrl = `${folder}/Build`;
  const loaderUrl = buildUrl + "/BildForDemo11.loader.js";
  const configJson = window?.gameConfig
  if (!configJson) {
    throw new Error("Game config not found")
  }

  const config = {
    dataUrl: `${buildUrl}/BildForDemo11.data.unityweb`,
    frameworkUrl: `${buildUrl}/BildForDemo11.framework.js.unityweb`,
    codeUrl: `${buildUrl}/BildForDemo11.wasm.unityweb`,
    streamingAssetsUrl: `${folder}/StreamingAssets`,
    companyName: "DefaultCompany",
    productName: "WikidsGame",
    productVersion: "0.1",
    showBanner: unityShowBanner,
  };

  if (/iPhone|iPad|iPod|Android/i.test(navigator.userAgent)) {
    var meta = document.createElement('meta');
    meta.name = 'viewport';
    meta.content = 'width=device-width, height=device-height, initial-scale=1.0, user-scalable=no, shrink-to-fit=yes';
    document.getElementsByTagName('head')[0].appendChild(meta);
    container.className = "unity-mobile";
    canvas.className = "unity-mobile";
  } else {
    canvas.style.width = "1280px";
    canvas.style.height = "720px";
  }

  loadingBar.style.display = "block";

  function progressCallback(progress) {
    progressBarFull.style.width = 100 * progress + "%";
  }

  const script = document.createElement("script");
  script.src = loaderUrl;
  script.onload = () => {
    createUnityInstance(canvas, config, progressCallback)
      .then((unityInstance) => {

        loadingBar.style.display = "none";
        fullscreenButton.onclick = () => {
          unityInstance.SetFullscreen(1);
        };

        unityInstance.SendMessage('JavaScriptHook', 'UpdateConfigJson', JSON.stringify(configJson));

      })
      .catch((message) => {
        alert(message);
      })
  };

  document.body.appendChild(script);
})()
