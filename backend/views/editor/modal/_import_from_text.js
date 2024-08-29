(function () {

  function createPage(scale, content) {
    const $page = $('<div/>', {
      class: 'page',
      contenteditable: 'plaintext-only'
    })
    $page.css({
      width: '1280px',
      height: '720px'
    })
    if (scale === 1) {
      $page.css({
        zoom: '',
        left: '',
        top: '',
        bottom: '',
        right: '',
        transform: ''
      })
    } else {
      $page.css({
        zoom: '',
        left: '50%',
        top: '50%',
        bottom: 'auto',
        right: 'auto',
        transform: `translate(-50%, -50%) scale(${scale})`
      })
    }

    $page.text(content)

    return $page
  }

  function appendToLastPage(word, page) {
    if (!page) {
      page = document.getElementsByClassName('page')[document.getElementsByClassName('page').length - 1]
    }
    const pageText = page.innerHTML
    page.innerHTML += word + ' '
    if (page.offsetHeight < page.scrollHeight) {
      page.innerHTML = pageText
      return false
    } else {
      return true
    }
  }

  $('#import-from-text-modal').on('shown.bs.modal', (e) => {

    /*const $slideContainer = $(e.target).find('.slide-container')
    $slideContainer.empty()*/

    const $container = $(e.target).find('.main')
    const width = $container[0].offsetWidth
    const height = $container[0].offsetHeight
    let scale = Math.min(width / 1280, height / 720)

    //console.log(width, height, scale)

    /*$('.content').css({
      position: 'absolute',
      zoom: '',
      left: '50%',
      top: '50%',
      bottom: 'auto',
      right: 'auto',
      transform: `translate(-50%, -50%) scale(${scale})`
    })*/

    //   const editorElement = document.querySelector('.editor')
    //
    //   const contentElement = document.querySelector('.content')

    DocumentEditor.init(
      e.target.querySelector('.editor'),
      e.target.querySelector('.content'),
      [''],
      {
        zoom: scale
      }
    )

    /*const $firstPage = createPage(scale, '')
    $firstPage.on('paste', (e) => {
      e.preventDefault()

      let content
      if (window.clipboardData) {
        content = window.clipboardData.getData('Text');
        /!*if (window.getSelection) {
          var selObj = window.getSelection();
          var selRange = selObj.getRangeAt(0);
          selRange.deleteContents();
          selRange.insertNode(document.createTextNode(content));
        }*!/
      } else if (e.originalEvent.clipboardData) {
        content = (e.originalEvent || e).clipboardData.getData('text/plain');
        //document.execCommand('insertText', false, content);
      }

      const words = content.split(' ')
      for (let i = 0; i < words.length; i++) {
        const success = appendToLastPage(words[i])
        if (success === false) {
          $slideContainer.append($('<div/>', {class: 'page-wrap', contenteditable: 'true'}).append(createPage(scale, '')))
          appendToLastPage(words[i])
        }
      }
    })

    $slideContainer.append($('<div/>', {class: 'page-wrap', contenteditable: 'true'}).append($firstPage))

    $slideContainer.on('input', (e) => {})*/
  })

  async function createSlides(storyId, slideId, texts) {
    const response = await fetch(`/admin/index.php?r=editor/import-from-text&current_slide_id=${slideId}&story_id=${storyId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': yii.getCsrfToken(),
      },
      body: JSON.stringify({
        texts
      })
    });

    if (!response.ok) {
      const message = `Error: ${response.status}`;
      toastr.error(message);
      throw new Error(message);
    }

    return await response.json();
  }

  $('#import-from-text').on('click', () => {
    const texts = $('.content', '#import-from-text-modal')
      .find('.page').map((i, el) => el.innerText)
      .get()
      .filter(s => s.trim())
    if (texts.length === 0) {
      return
    }

    createSlides(StoryEditor.getConfigValue('storyID'), StoryEditor.getCurrentSlide().getID(), texts)
      .then(response => {
        if (response && response?.success) {
          StoryEditor.loadSlides(response?.slide_id)
          $('#import-from-text-modal').modal('hide')
        } else {
          alert('error')
        }
      })
  })

})();
