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

  Reveal.addEventListener('ready', () => {

    const $overlay = $(
      `<div class="screen-recording-overlay">
          <p style="font-size: 24px; line-height: 30px">Перед прохождением истории нужно разрешить запись экрана</p>
          <p class="screen-recording-error-wrap" style="font-size: 24px; line-height: 30px; color: #dc3545; display: none">
            <strong class="screen-recording-error"></strong>
          </p>
          <button class="start-screen-recording btn" type="button">
            Начать запись экрана <img alt="..." style="width: 20px; display: none" src="/img/loading.gif" />
          </button>
          <div class="stop-screen-recording-wrap" style="display: none">
            <span>Идет запись... </span>
            <span class="screen-recorder-timer">00:00:00</span>
            <button class="stop-screen-recording btn btn-sm" type="button">
              Стоп <img alt="..." style="width: 20px; display: none" src="/img/loading.gif" />
            </button>
          </div>
      </div>`
    )

    let dataChunks = [];
    let mediaRecorder;

    const socket = io(config.ws_host);

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
