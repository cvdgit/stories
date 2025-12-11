(function () {
  "use strict";

  const modal = new SimpleModal({
    id: 'gen-cover-modal',
    title: 'Генерация обложки',
    size: 'auto'
  });

  async function fetchStoryText(storyId) {
    if (!storyId) {
      throw new Error('No params')
    }
    const response = await fetch(`/admin/index.php?r=story/get-text&id=${storyId}`, {
      headers: {
        'X-CSRF-Token': $("meta[name=csrf-token]").attr('content'),
        'Content-Type': 'application/json',
      }
    });
    if (!response.ok) {
      throw new Error('Fetch story text error');
    }
    return await response.json();
  }

  async function fetchGenerateImage(prompt) {
    if (!prompt) {
      throw new Error('No params')
    }
    const response = await fetch(`/admin/index.php?r=gpt/image/generate`, {
      method: 'POST',
      headers: {
        'X-CSRF-Token': $("meta[name=csrf-token]").attr('content'),
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({prompt})
    });
    if (!response.ok) {
      throw new Error('Fetch story test error');
    }
    return await response.json();
  }

  async function fetchGenImages(storyId) {
    if (!storyId) {
      throw new Error('No params')
    }
    const response = await fetch(`/admin/index.php?r=story/gen-images&id=${storyId}`, {
      headers: {
        'X-CSRF-Token': $("meta[name=csrf-token]").attr('content'),
        'Content-Type': 'application/json',
      }
    });
    if (!response.ok) {
      throw new Error('Fetch story text error');
    }
    return await response.json();
  }

  async function generateImage(prompt) {
    const {success, data, message} = await fetchGenerateImage(prompt);
    if (!success) {
      throw new Error(`Generate Image request error: ${message}`);
    }

    const {status, images} = data;
    if (status !== 'ok') {
      throw new Error('Generate Image API request error: ' + JSON.stringify(data));
    }

    return images;
  }

  /**
   * @param {int} storyId
   * @param {HTMLDivElement} promptElement
   * @return {Promise<array>}
   */
  async function createCover(storyId, promptElement, userPrompt) {

    const {text} = await fetchStoryText(storyId);
    if (!text) {
      throw new Error('В истории нет текста');
    }

    let prompt = userPrompt;
    if (!prompt) {
      await window.sendStreamMessage(
        '/admin/index.php?r=gpt/story/for-cover-prompt',
        {text},
        (message) => promptElement.innerText = message,
        (message) => prompt = message
      );
    }

    if (!prompt) {
      throw new Error('no prompt');
    }

    return generateImage(prompt);
  }

  async function setCover(storyId, imageUrl) {
    const response = await fetch('/admin/index.php?r=story/set-cover', {
      method: 'post',
      headers: {
        'X-CSRF-Token': $("meta[name=csrf-token]").attr('content'),
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({storyId, imageUrl})
    });
    if (!response.ok) {
      throw new Error('Set story cover error');
    }
    return await response.json();
  }

  async function saveGenImage(storyId, imageUrl) {
    const response = await fetch('/admin/index.php?r=story/save-image', {
      method: 'post',
      headers: {
        'X-CSRF-Token': $("meta[name=csrf-token]").attr('content'),
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({storyId, imageUrl})
    });
    if (!response.ok) {
      throw new Error(`Save image error: ${response.statusText}`);
    }
    return await response.json();
  }

  function createImageItem({storyId, imageUrl, imageKey, active}) {
    const $imageItem = $(`<div class="gen-images-image-wrap">
    <div class="gen-image-image">
        <img style="width: 100px;" alt="..." src="/img/loading.gif"/>
    </div>
    <div class="gen-images-image-actions"></div>
</div>`);

    const image = new Image();
    image.src = imageUrl;
    image.onload = () => {
      $imageItem
        .find('.gen-image-image')
        .empty()
        .append(
          $(`<img class="gen-img" style="max-width: 100%;" alt="..." src="${image.src}"/>`)
        );
    }
    image.onerror = (error) =>
      $imageItem.find('.gen-image-image').text('Ошибка при загрузка изображения');

    const links = [
      {
        title: 'Сделать обложкой истории',
        classNames: 'gen-image-action',
        action: 'set-cover'
      },
      {
        title: 'Удалить',
        classNames: 'gen-image-action',
        action: 'del-image'
      }
    ];

    links.map(({title, classNames, action}) => {
      if (active && action === 'set-cover') {
        return;
      }
      $(`<a href="" class="${classNames}" data-gen-action="${action}">${title}</a>`)
        .appendTo($imageItem.find('.gen-images-image-actions'))
    })

    $imageItem.on('click', '[data-gen-action=set-cover]', e => {
      e.preventDefault();
      setCover(storyId, imageUrl).then(response => {
        if (response?.success) {
          location.reload();
        }
      });
    });

    $imageItem.on('click', '[data-gen-action=del-image]', e => {
      e.preventDefault();
      if (!confirm('Подтверждаете?')) {
        return;
      }

    });

    if (active) {
      $imageItem.append(
        `<div class="gen-image-active">Обложка истории</div>`
      )
    }

    return $imageItem;
  }

  function createBody() {
    return $(`<div class="gen-modal">
    <div class="gen-modal-content-wrap">
        <div class="gen-images"></div>
        <div class="gen-cover-img-container">
            <div class="gen-loader-wrap"><p>Генерация изображения...</p>
                <img style="width: 50px;" alt="..." src="/img/loading.gif"/>
            </div>
            <p class="gen-img-error-wrap" style="color: #dc3545; display: none"><strong class="gen-img-error"></strong>
            </p>
            <div class="gen-img-handler-wrap">
                Нет изображения
            </div>
        </div>
        <div class="gen-cover-container">
            <div class="gen-messages">
                <div class="gen-system-message-wrap">
                    <div class="gen-system-message"></div>
                </div>
            </div>
            <div class="gen-modal-composer-wrap">
                <form class="composeForm">
                    <div contenteditable="plaintext-only" class="textarea gen-create-prompt"
                         style="overflow-y: auto; max-height: 100px;min-height: 50px"></div>
                    <!--div style="flex-shrink: 0">
                        <button type="button" class=compose>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                        d="M3.714 3.048a.498.498 0 0 0-.683.627l2.843 7.627a2 2 0 0 1 0 1.396l-2.842 7.627a.498.498 0 0 0 .682.627l18-8.5a.5.5 0 0 0 0-.904z"></path>
                                <path d="M6 12h16"></path>
                            </svg>
                        </button>
                    </div-->
                </form>
            </div>
        </div>
    </div>
    <div class="gen-modal-actions">
        <div class="gen-create-image-wrap">
            <div class="gen-create-image-buttons">
                <button title="Сгенерировать изображения на основе промта" data-toggle="tooltip" type="button"
                        class="btn btn-primary gen-img-create-handler">Создать
                </button>
                или
                <a title="Сгенерировать изображения, соответствующее тексту истории" data-toggle="tooltip"
                   class="btn-link gen-img-handler" href="">Сгенерировать</a>
            </div>
            <button type="button" class="btn btn-primary set-cover">Сделать обложкой истории</button>
        </div>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
    </div>
</div>`);
  }

  $('.gen-cover').on('click', e => {
    e.preventDefault();

    const $target = $(e.target.closest('a.gen-cover'));
    const storyId = $target.attr('data-story-id');
    const storyCover = $target.attr('data-story-cover');

    const $body = createBody();

    $body.on('click', '.gen-img-handler', (e) => {
      e.preventDefault();

      $body.removeClass('gen-error')
        .addClass('gen-loading');

      const dialog = document.getElementById('gen-cover-modal');
      dialog.scrollTop = dialog.scrollHeight;

      const prompt = $body.find('.gen-system-message').text();

      createCover(storyId, $body.find('.gen-system-message')[0], prompt)
        .then(images => {
          saveGenImage(storyId, images[0].url).then(({url, key}) => {
            $body.removeClass('gen-loading gen-error');
            createImageItem({imageUrl: url, imageKey: key, storyId})
              .appendTo($body.find('.gen-images'));
          });
        })
        .catch(error => {
          $body.removeClass('gen-loading')
            .addClass('gen-error');
          $body.find('.gen-img-error').text(error);

          if ($body.find('.gen-system-message').length) {
            $body.find('.gen-system-message').attr('contenteditable', 'plaintext-only');
            $body.find('.gen-system-message-actions').show();
          }
        });
    });

    $body.on('click', '.gen-img-create-handler', (e) => {
      const prompt = $body.find('.gen-create-prompt').text();
      if (!prompt) {
        return;
      }
      $body.removeClass('gen-error')
        .addClass('gen-loading');

      $body.find('.gen-system-message').text(prompt);
      $body.find('.gen-create-prompt').text('');

      const dialog = document.getElementById('gen-cover-modal');
      dialog.scrollTop = dialog.scrollHeight;

      createCover(storyId, $body.find('.gen-system-message')[0], prompt)
        .then(images => {
          saveGenImage(storyId, images[0].url).then(({url, key}) => {
            $body.removeClass('gen-loading gen-error');
            createImageItem({imageUrl: url, imageKey: key, storyId})
              .appendTo($body.find('.gen-images'));
          });
        })
        .catch(error => {
          $body.removeClass('gen-loading')
            .addClass('gen-error');
          $body.find('.gen-img-error').text(error);

          if ($body.find('.gen-system-message').length) {
            $body.find('.gen-system-message').attr('contenteditable', 'plaintext-only');
            $body.find('.gen-system-message-actions').show();
          }
        });
    });

    $body.find('.set-cover').on('click', e => {
      const url = $body.find('.gen-img').attr('src');
      if (!url) {
        alert('no image');
        return;
      }
      setCover(storyId, url).then(response => {
        if (response?.success) {
          location.reload();
        }
      });
    });

    modal.on('show', async function () {

      const {success, images} = await fetchGenImages(storyId);
      images.map(({url, key}) => {
        createImageItem({
          imageUrl: url,
          imageKey: key,
          storyId,
          active: key === storyCover
        })
          .appendTo($body.find('.gen-images'));
      });

      $body.find('[data-toggle=tooltip]').tooltip({
        trigger: 'hover'
      });
    })

    modal.show({
      body: $body
    });
  })
})();
