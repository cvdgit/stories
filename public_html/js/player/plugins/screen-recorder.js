(function () {
  const {screenRecorderConfig: config} = Reveal.getConfig();

  const session = uuidv4();

  Reveal.addEventListener('slidechanged', () => {
    console.log('screen recorder slidechanged');
  });

  function setError(error) {
    const wrap = document.querySelector('.screen-recording-error-wrap')
    if (!error) {
      wrap.style.display = 'none';
      wrap.querySelector('.screen-recording-error').innerText = '';
      return;
    }
    wrap.style.display = 'block';
    wrap.querySelector('.screen-recording-error').innerText = error;
  }

  function startCounter(secondCallback) {
    let timer = 0;
    return setInterval(() => {
      timer++;
      secondCallback(timer);
    }, 1000);
  }

  function formatSecondsToHMS(totalSeconds) {
    const hours = Math.floor(totalSeconds / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);
    const seconds = totalSeconds % 60;

    const formattedHours = String(hours).padStart(2, '0');
    const formattedMinutes = String(minutes).padStart(2, '0');
    const formattedSeconds = String(seconds).padStart(2, '0');

    return `${formattedHours}:${formattedMinutes}:${formattedSeconds}`;
  }

  function InnerDialog(container, title, content) {
    const html = `<div class="slide-hints-wrapper" style="background-color: white; position: absolute">
    <div class="retelling-dialog-inner">
        <div class="retelling-dialog-header">
            <h2>${title}</h2>
            <div class="header-actions">
                <button type="button" class="hints-close">&times;</button>
            </div>
        </div>
        <div id="dialog-body" class="retelling-dialog-body"></div>
    </div>
</div>
`
    this.showHandler = null
    this.hideHandler = null

    const hideDialog = () => {

      Reveal.configure({keyboard: true})

      if ($(container).find('.slide-hints-wrapper').length) {
        $(container)
          .find('.slide-hints-wrapper')
          .hide()
          .remove();
      }
      $('.reveal .story-controls').show()

      if (typeof this.hideHandler === "function") {
        this.hideHandler()
      }
    }

    this.show = () => {

      Reveal.configure({keyboard: false})

      const $element = $(html)

      $element.find("#dialog-body").append(content)
      $element.on("click", ".hints-close", hideDialog)

      $('.reveal .story-controls').hide()

      container
        .append($element)
        .find(".slide-hints-wrapper")
        .show();

      if (typeof this.showHandler === "function") {
        this.showHandler($element)
      }
    }

    this.hide = hideDialog;

    this.onShow = callback => {
      this.showHandler = callback
    }

    this.onHide = callback => {
      this.hideHandler = callback
    }
  }

  function resetWsStatus(elem) {
    [
      'ws-status-loading',
      'ws-status-connect',
      'ws-status-disconnect'
    ].map(className => $(elem).removeClass(className))
  }

  function wsStatusLoading(elem) {
    resetWsStatus(elem);
    $(elem).addClass('ws-status-loading');
  }

  function wsStatusConnected(elem) {
    resetWsStatus(elem);
    $(elem).addClass('ws-status-connect');
  }

  function wsStatusDisconnect(elem) {
    resetWsStatus(elem);
    $(elem).addClass('ws-status-disconnect');
  }

  Reveal.addEventListener('ready', () => {

    const $overlay = $(`<div class="screen-recording-overlay">
    <div class="ws-status ws-status-loading">
        <div class="ws-loading"><img alt="..." style="width: 20px" src="/img/loading.gif"/> подключение...</div>
        <div class="ws-connect" title="Подключено">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                 stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z"/>
            </svg>
        </div>
        <div class="ws-disconnect" title="Не удалось подключиться к серверу">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                 stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M11.412 15.655 9.75 21.75l3.745-4.012M9.257 13.5H3.75l2.659-2.849m2.048-2.194L14.25 2.25 12 10.5h8.25l-4.707 5.043M8.457 8.457 3 3m5.457 5.457 7.086 7.086m0 0L21 21"/>
            </svg>
            ошибка подключения
        </div>
    </div>
    <p style="font-size: 24px; line-height: 30px">Перед прохождением истории нужно разрешить запись экрана</p>
    <p class="screen-recording-error-wrap" style="font-size: 24px; line-height: 30px; color: #dc3545; display: none">
        <strong class="screen-recording-error"></strong>
    </p>
    <div class="start-screen-recording-wrap" style="display: flex; flex-direction: row; gap: 10px; align-items: center">
        <button class="start-screen-recording btn" type="button" disabled>
            Начать запись экрана <img alt="..." style="width: 20px; display: none" src="/img/loading.gif"/>
        </button>
        <a class="show-screen-records" href="">Записи</a>
    </div>
    <div class="stop-screen-recording-wrap" style="display: none">
        <span>Идет запись... </span>
        <span class="screen-recorder-timer">00:00:00</span>
        <button class="stop-screen-recording btn btn-sm" type="button">
            Стоп <img alt="..." style="width: 20px; display: none" src="/img/loading.gif"/>
        </button>
    </div>
</div>`)

    let dataChunks = [];
    let mediaRecorder;

    const socket = io(config.ws_host);

    socket.on("connect", (socket) => {
      $overlay
        .find('.start-screen-recording')
        .removeAttr('disabled');
      wsStatusConnected($overlay.find('.ws-status'));
    });

    socket.on("disconnect", (socket) => {
      $overlay
        .find('.start-screen-recording')
        .prop('disabled', true);
      wsStatusDisconnect($overlay.find('.ws-status'));
    });

    socket.on("connect_error", (socket) => {
      $overlay
        .find('.start-screen-recording')
        .prop('disabled', true);
      wsStatusDisconnect($overlay.find('.ws-status'));
    });

    async function fetchVideos({wsHost, storyId, userId}) {
      const response = await fetch(`${wsHost}/getScreenVideos/${storyId}/${userId}`)
      if (!response.ok) {
        return [];
      }
      return await response.json();
    }

    async function renderVideoList(container) {

      container
        .find('.videos-container')
        .html('<img style="width: 100px; height: 100px" src="/img/loading.gif" alt="">');

      const params = {
        wsHost: config.ws_host,
        storyId: config.story_id,
        userId: config.user_id
      };
      const files = await fetchVideos(params);
      if (files.length === 0) {
        container
          .find('.videos-container')
          .html('Нет записей экрана');
        return;
      }

      container.find('.videos-container').empty();
      container.find('.videos-container').append('<div class="sessions-wrap"></div>');

      const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
      };
      files.files.map(({session, videos}) => {
        const $session = $(`<div><h4>Сессия</h4><div class="videos-wrap"></div></div>`);
        videos.map(video => {
          const formattedDateTime = new Intl.DateTimeFormat('ru-RU', options)
            .format(new Date(video.time));
          $session
            .find('.videos-wrap')
            .append(
              `<div class="videos-video">
<div style="position: relative"><video controls style="max-width: 100%">
<source src="${config.ws_host}${video.url}" type="video/webm" />
</video>
${video.status === 'process' ? '<div class="videos-video-process"><img src="/img/loading.gif" alt="">В обработке...</div>' : ''}
</div>
<div style="font-size: 16px; padding: 4px 0">${formattedDateTime}</div>
</div>`
            );
        })
        container.find('.sessions-wrap').append($session);
      });
    }

    $overlay.find('.start-screen-recording').on('click', async ({target}) => {

      if ($(target).hasClass('pending')) {
        return;
      }

      $(target).addClass('pending');
      setError()

      let screenStream;

      const displayMediaOptions = {
        video: true
      };
      if (navigator.mediaDevices.getDisplayMedia) {
        try {
          screenStream = await navigator.mediaDevices.getDisplayMedia(displayMediaOptions)
        } catch (e) {

          setError(e)
          $(target).removeClass('pending')

          throw new Error('*** getDisplayMedia')
        }
      } else {
        throw new Error('*** getDisplayMedia not supported')
      }

      let voiceStream = 'unavailable';
      /*if (navigator.mediaDevices.getUserMedia) {
        if (screenStream) {
          try {
            voiceStream = await navigator.mediaDevices.getUserMedia({
              audio: true
            })
          } catch (e) {
            console.error('*** getUserMedia', e)
          } finally {
            //
          }
        }
      } else {
        console.warn('*** getUserMedia not supported')
      }*/

      if (!screenStream) {
        throw new Error('*** no screen stream')
      }

      $overlay.addClass('recording');
      $(target).removeClass('pending');

      const streamDataId = uuidv4();

      socket.emit('user:connected', {
        session,
        userId: config.user_id,
        storyId: config.story_id,
      });

      $overlay.find('.screen-recorder-timer').text('00:00:00');
      const timerHandler = startCounter(sec => {
        $overlay.find('.screen-recorder-timer').text(formatSecondsToHMS(sec))
      });

      let mediaStream
      if (voiceStream === 'unavailable') {
        mediaStream = screenStream
      } else {
        mediaStream = new MediaStream([
          ...screenStream.getVideoTracks(),
          ...voiceStream.getAudioTracks()
        ])
      }

      mediaRecorder = new MediaRecorder(mediaStream);
      mediaRecorder.ondataavailable = ({data}) => {
        socket.emit('screenData:start', {
          session,
          streamDataId,
          data
        })
      }
      mediaRecorder.onstop = () => {
        stopRecording({
          session,
          streamDataId,
          userId: config.user_id,
          storyId: config.story_id
        });
        clearInterval(timerHandler);
      };
      mediaRecorder.start(250);
    })

    $overlay.find('.stop-screen-recording').on('click', ({target}) => {
      const $target = $(target);

      if ($target.hasClass('pending')) {
        return;
      }
      $target.addClass('pending');

      mediaRecorder.stop();
    });

    $overlay.find('.show-screen-records').on('click', (e) => {
      e.preventDefault();

      const $content = $(`<div class="videos-container"></div>`);

      const dialog = new InnerDialog($overlay, `Записи экрана (<a style="font-weight: 600" class="videos-load" href="">Обновить список</a>)`, $content);
      dialog.onShow(async (container) => {

        await renderVideoList(container);

        container.find('.videos-load').on('click', async (e) => {
          e.preventDefault();
          await renderVideoList(container);
        })
      })
      dialog.show();
    })

    function stopRecording(payload) {
      socket.emit('screenData:end', payload);
      mediaRecorder = null;
      $overlay.removeClass('recording');
      $overlay.find('.stop-screen-recording').removeClass('pending');
    }

    $('#story-container').append($overlay);
  });
})
();
