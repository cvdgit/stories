(function () {

  const container = document.querySelector("#unity-container");
  const canvas = document.querySelector("#unity-canvas");

  function unityShowBanner(msg, type) {
    function updateBannerVisibility() {
      warningBanner.style.display = warningBanner.children.length ? 'block' : 'none';
    }

    var div = document.createElement('div');
    div.innerHTML = msg;
    warningBanner.appendChild(div);
    if (type == 'error') div.style = 'background: red; padding: 10px;';
    else {
      if (type == 'warning') div.style = 'background: yellow; padding: 10px;';
      setTimeout(function () {
        warningBanner.removeChild(div);
        updateBannerVisibility();
      }, 5000);
    }
    updateBannerVisibility();
  }

  const buildUrl = "/game/Build";
  // var loaderUrl = buildUrl + "/TestServerColor.loader.js";
  const config = {
    dataUrl: buildUrl + "/TestServerColor.data.unityweb",
    frameworkUrl: buildUrl + "/TestServerColor.framework.js.unityweb",
    codeUrl: buildUrl + "/TestServerColor.wasm.unityweb",
    streamingAssetsUrl: "StreamingAssets",
    companyName: "DefaultCompany",
    productName: "TestAuthorization",
    productVersion: "0.1",
    showBanner: () => console.log("Show Banner"),
    color: "#0000FF",
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

  function progressCallback(progress) {
    //progressBarFull.style.width = 100 * progress + "%";
  }

  createUnityInstance(canvas, config, progressCallback)
    .then((unityInstance) => {

      unityInstance.SendMessage('JavaScriptHook', 'HexToColor', config.color);

    }).catch((message) => {
    alert(message);
  });
})()
