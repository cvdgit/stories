function TableOfContents() {

  const dialog = new SimpleModal({
    id: 'tableOfContentsModal',
    title: 'Оглавление',
    size: 'xl'
  });

  function createSlideElem({id, data, title, slideNumber}, inGroup) {
    /*
    <div style="display: flex; flex-direction: row; justify-content: space-between; gap: 10px; align-items: center">
        <input style="flex: 1" class="slide-name" type="text" value="${title}" />
    </div>
     */
    return $(`<div data-slide-id="${id}" class="table-of-contents-slide reveal-slide-init ${inGroup ? 'in-group' : ''}">
    <div style="display: flex; flex-direction: row; justify-content: space-between; gap: 10px; align-items: center">
        #${slideNumber}
        <button title="Удалить слайд из группы" class="remove-slide" type="button"><i class="glyphicon glyphicon-trash"></i></button>
    </div>
    <div style="position: relative; width: 100%; aspect-ratio: 16 / 9; border: 1px solid #666">
        <div class="thumb-reveal reveal"><div class="slides">${data}</div></div>
    </div>
</div>`);
  }

  function initDeck(elem) {
    const deck = new Reveal(elem, {
      embedded: true,
      width: 1280,
      height: 720,
      margin: 0.1,
      transition: 'slide',
      transitionSpeed: 'default',
      backgroundTransition: 'fade',
      center: false,
      controls: false,
      controlsLayout: 'bottom-right',
      controlsBackArrows: 'faded',
      controlsTutorial: false,
      progress: false,
      keyboard: false,
      history: false,
      mouseWheel: false,
      showNotes: false,
      shuffle: false,
      loop: false,
      hashOneBasedIndex: true,
      rtl: false,
      help: false,
      dependencies: [],
      touch: false
    });
    deck.initialize();
  }

  function Payload(data) {

    const rawData = structuredClone(data);
    rawData.title = rawData.title || '';
    rawData.groups = rawData.groups || [];

    this.getTitle = () => rawData.title;
    this.isDisableNav = () => rawData.isDisableNav || false;
    this.getGroups = () => rawData.groups;
    this.addGroup = group => this.getGroups().push(group);
    this.createGroup = (id, name, slides = [], cards = []) => ({id, name, slides, cards});
    this.createGroupCard = (id, name) => ({id, name});
    this.getPayload = () => {
       return {
         ...rawData,
         groups: rawData.groups
           .filter(g => g.slides.length > 0)
           .map(g => ({
             ...g,
             slides: g.slides.map(s => {
               delete s.data;
               return s;
             })
           }))
       }
    };
    this.insertEmptyAfterGroup = afterId => {
      const group = this.createGroup(uuidv4(), 'Слайды', [], [this.createGroupCard(uuidv4(), 'Новая карточка')]);
      if (afterId) {
        const index = this.getGroups().findIndex(f => f.id === afterId);
        this.getGroups().splice(index + 1, 0, group);
        return group;
      }
      this.getGroups().unshift(group);
      return group;
    }
    this.insertEmptyAfterCard = (groupId, afterCardId) => {
      const card = this.createGroupCard(uuidv4(), 'Новая карточка');
      const group = this.getGroup(groupId);
      if (afterCardId) {
        const index = group.cards.findIndex(c => c.id === afterCardId);
        group.cards.splice(index + 1, 0, card);
        return card;
      }
      group.cards.unshift(card);
      return card;
    }
    this.getGroup = (id) => this.getGroups().find(g => g.id === id);

    this.appendSlideToGroup = (groupId, slide) => {
      const group = this.getGroup(groupId);
      group.slides.push(slide);
    };

    this.moveSlide = (groupId, slide) => {
      const slideGroup = this.getGroups().find(g => g.slides.find(s => Number(s.id) === Number(slide.id)));
      slideGroup.slides = slideGroup.slides.filter(s => Number(s.id) !== Number(slide.id));
      const group = this.getGroup(groupId);
      group.slides.push(slide);
    };

    this.findSlideInGroups = (id) => {
      const group = this.getGroups()
        .find(g => g.slides.find(s => Number(s.id) === Number(id)));
      if (group) {
        return group.slides.find(s => Number(s.id) === Number(id));
      }
    }

    this.removeSlideFromGroup = (id) => {
      const slideGroup = this.getGroups().find(g => g.slides.find(s => Number(s.id) === Number(id)));
      slideGroup.slides = slideGroup.slides.filter(s => Number(s.id) !== Number(id));
    }

    this.removeGroup = (id) => {
      rawData.groups = rawData.groups.filter(g => g.id !== id);
    }

    this.removeGroupCard = (groupId, cardId) => {
      const group = this.getGroup(groupId);
      group.cards = group.cards.filter(c => c.id !== cardId);
      group.slides = group.slides.filter(s => s.cardId !== cardId);
    }

    this.updateTitle = (title) => {
      rawData.title = title;
    }

    this.setDisableNav = disableNav => rawData.disableNav = disableNav;

    this.updateGroupName = (id, name) => {
      const group = this.getGroup(id);
      group.name = name;
    }

    /*this.updateSlideName = (id, name) => {
      const slide = this.findSlideInGroups(id);
      slide.title = name;
    }*/
    this.updateGroupCardName = (groupId, cardId, name) => {
      const group = this.getGroup(groupId);
      const card = group.cards.find(c => c.id === cardId);
      card.name = name;
    }

    this.updateGroupCardImage = (groupId, cardId, image) => {
      const group = this.getGroup(groupId);
      const card = group.cards.find(c => c.id === cardId);
      card.image = image;
      return card;
    }
  }

  function createGroupCardElement({id, name, image}, slides, findSlide) {
    const $element = $(`
<div data-group-card-id="${id}" class="table-of-contents-group-card">
    <h4 class="card-header"></h4>
    <div class="table-of-contents-group-slides"></div>
</div>
`);

    slides.map(({id}) => {
      const slide = findSlide(id);
      if (!slide) {
        return;
      }
      const {data, slideNumber} = slide;
      const slideElem = createSlideElem({id, data, slideNumber}, true);
      $element.find('.table-of-contents-group-slides').append(slideElem);
    });

    $element.find('.card-header').append(
      drawCardHeader({id, name, image})
    );

    return $element;
  }

  function drawCardHeader({id, name, image}) {
    const element = document.createElement('div');
    element.classList.add('card-header-inner');

    element.innerHTML = `<input type="text" class="form-control group-card-name" value="${name}">`;

    if (image) {
      const figure = document.createElement('div');
      figure.style.position = 'relative';
      figure.style.width = '150px';
      figure.style.textAlign = 'center';
      figure.innerHTML = `<p style="font-size: 14px">Картинка карточки</p><figure class="card-image" style="background-image: url(${image});"></figure>`;
      element.prepend(figure);
      const deleteElement = document.createElement('a');
      deleteElement.classList.add('card-image-delete')
      deleteElement.href = '';
      deleteElement.innerText = 'Удалить';
      deleteElement.style.fontSize = '14px';
      deleteElement.style.color = 'rgba(220, 53, 69, 1)';
      figure.append(deleteElement);
    } else {
      const uploader = document.createElement('button');
      uploader.type = 'button';
      uploader.classList.add('table-of-contents-card-img-btn');
      uploader.innerHTML = `
<label for="image${id}">
<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="image" class="table-of-contents-card-img-icon" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M0 96C0 60.7 28.7 32 64 32H448c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6h96 32H424c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"></path></svg>
</label>
<input class="table-of-contents-card-img-file" accept="image/png, image/gif, image/jpeg, image/svg+xml, image/bmp, image/tiff"
                     id="image${id}" type="file" style="display: none"/>
      `;
      element.prepend(uploader);
    }

    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.title = 'Удалить карточку';
    removeBtn.classList.add('remove-group-card');
    removeBtn.innerHTML = '<i class="glyphicon glyphicon-trash"></i>'

    const fragment = document.createDocumentFragment();
    fragment.appendChild(element);
    fragment.appendChild(removeBtn);
    return fragment;
  }

  function createGroupElement({id, name, slides, cards}, allSlides, canRemove) {
    const $element = $(`
<div data-group-id="${id}" class="table-of-contents-group">
<h4 style="display: flex; flex-direction: row; justify-content: space-between; align-items: center;margin-bottom: 20px;">
<input type="text" class="form-control group-name" value="${name}" />
${canRemove ? `<button title="Удалить группу" class="remove-group" type="button"><i class="glyphicon glyphicon-trash"></i></button>` : ''}
</h4>
<h4>Карточки:</h4>
<div class="table-of-contents-cards"></div>
</div>
`);

    $element.find('.table-of-contents-cards').append(`<div class="fragment-item" style="border: 0 none; min-height: 30px">
    <div style="position: relative">
        <div class="fragment-create">
            <button type="button" class="fragment-create-btn create-group-card">+ новая карточка</button>
        </div>
    </div>
</div>
`);

    (cards || []).map(({id, name, image}) => {
      const $card = createGroupCardElement(
        {id, name, image},
        slides.filter(s => s.cardId === id),
        slideId => allSlides.find(s => Number(s.id) === Number(slideId))
      );
      $card
        .append(`<div class="fragment-create"><button type="button" class="fragment-create-btn create-group-card">+ новая карточка</button></div>`);
      $element.find('.table-of-contents-cards').append($card);
    });

    return $element;
  }

  function initGroupSortable(element, payloadManager, findSlide) {
    element.sortable({
      group: 'table-of-contents-group-slides',
      connectWith: '.table-of-contents-group-slides',
      placeholder: 'ui-state-highlight',
      receive: ({target, toElement}) => {
        const $element = $(toElement).parents('[data-slide-id]:eq(0)');
        const slideId = $element.attr('data-slide-id');
        const groupId = $(target).parents('[data-group-id]').attr('data-group-id');
        const cardId = $(target).parents('[data-group-card-id]').attr('data-group-card-id');

        let slide = payloadManager.findSlideInGroups(slideId);
        if (!slide) {
          const rawSlide = findSlide(Number(slideId));
          slide = {
            id: rawSlide.id,
            title: `Слайд ${rawSlide.slideNumber}`,
            slideNumber: rawSlide.slideNumber,
            data: rawSlide.data,
            cardId,
          }
          payloadManager.appendSlideToGroup(groupId, slide);
          return
        }
        slide.cardId = cardId;
        payloadManager.moveSlide(groupId, slide);
      },
      remove: ({toElement}) => {
        const id = $(toElement).parents('[data-slide-id]:eq(0)').attr('data-slide-id');
      }
    }).disableSelection();
  }

  function drawGroups($contentItemList, payloadManager, allSlides) {

    $contentItemList.empty();
    $contentItemList.append(`<div class="fragment-item" style="border: 0 none; min-height: 30px">
    <div style="position: relative">
        <div class="fragment-create">
            <button type="button" class="fragment-create-btn create-group">+ новая группа</button>
        </div>
    </div>
</div>
`);
    payloadManager.getGroups().map(group =>  {
      const $fragmentElem = createGroupElement(
        group,
        allSlides,
        payloadManager.getGroups().length > 1
      );
      $contentItemList.append($fragmentElem);
      $fragmentElem.append(`<div class="fragment-create"><button type="button" class="fragment-create-btn create-group">+ новая группа</button></div>`);
    });

    initGroupSortable(
      $contentItemList.find('.table-of-contents-group-slides'),
      payloadManager,
      (id) => allSlides.find(s => Number(s.id) === Number(id))
    );
  }

  function drawAllSlidesGroup($list, payloadManager, allSlides) {

    $list.empty();

    const inGroupSlideIds = payloadManager.getGroups()
      .map(g => g.slides.map(s => Number(s.id)))
      .flat();

    allSlides
      .filter(s => !inGroupSlideIds.includes(Number(s.id)))
      .map(({id, slideNumber, data}) => {
        const slideElem = createSlideElem(
          {id, data, title: `Слайд ${slideNumber}`, slideNumber},
          false
        );
        $list.append(slideElem);
      });

    $list.sortable({
      connectWith: '.table-of-contents-group-slides',
      placeholder: 'ui-state-highlight',
      receive: ({target, toElement}) => {
        console.log('receive');
      },
      remove: ({toElement}) => {
        const $element = $(toElement).parents('[data-slide-id]:eq(0)');
        $element.addClass('in-group');
      }
    }).disableSelection();
  }

  this.show = (data, allSlides, updateHandler) => {

    const payload = new Payload(data);

    const $body = $(`<div style="display: flex; flex-direction: row; gap: 20px; width: 100%; height: 100%; max-height: 100%;">
    <div id="col-left" style="width: 300px">
        <div style="display: flex; flex-direction: column; gap: 10px; max-height: 100%">
            <h4 class="h4" style="margin-top: 0">Слайды истории</h4>
            <div class="table-of-contents-all-slides"></div>
        </div>
    </div>
    <div id="col-right" style="flex: 1; display: flex; flex-direction: column">
        <div style="display: flex; flex-direction: row; justify-content: space-between; gap: 20px; align-items: center">
            <div style="display: flex; flex-direction: column; flex: 1">
              <div class="form-group" style="flex: 1">
                  <label for="tableOfContentsTitle">Название</label>
                  <input
                          id="tableOfContentsTitle"
                          style="max-width: 80%"
                          class="table-of-contents-title form-control"
                          type="text"
                          value="${payload.getTitle() || 'Оглавление'}"
                  />
              </div>
              <label for="disableNav">
              Отключить панель навигации <input ${payload.isDisableNav() ? 'checked' : ''} class="disable-nav" type="checkbox" id="disableNav">
</label>
            </div>
            <button class="btn btn-primary table-of-contents-save" type="button">Сохранить</button>
        </div>
        <div style="margin: 20px 0; display: flex; flex-direction: column; justify-content: space-between; overflow: hidden; gap: 10px; max-height: 100%">
            <h4 class="h4" style="margin: 0">Группы слайдов</h4>
            <div class="table-of-contents-groups" style="overflow-y: auto; flex: 1"></div>
        </div>
    </div>
</div>`);

    if (payload.getGroups().length === 0) {
      payload.addGroup(
        payload.createGroup(
          uuidv4(), 'Новая группа', [], [
            payload.createGroupCard(uuidv4(), 'Слайды'),
          ])
      );
    }

    drawGroups($body.find('.table-of-contents-groups'), payload, allSlides);

    drawAllSlidesGroup($body.find('.table-of-contents-all-slides'), payload, allSlides);

    function initSlideDecks() {
      $body.find('.reveal-slide-init').each((i, el) => {
        setTimeout(() => {
          initDeck(el.querySelector('.reveal'));
          el.classList.remove('reveal-slide-init');
        }, 1);
      });
    }

    initSlideDecks();

    $body.find('.table-of-contents-title').on('input', ({target}) => {
      payload.updateTitle(target.value);
    });

    $body.on('click', '.create-group', ({target}) => {

      const $target = $(target);
      const id = $target.parents('[data-group-id]').attr('data-group-id');

      const group = payload.insertEmptyAfterGroup(id);

      const $groupElem = createGroupElement(
        group,
        allSlides,
        payload.getGroups().length > 1
      );
      $groupElem.append(`<div class="fragment-create"><button type="button" class="fragment-create-btn create-group">+ новая группа</button></div>`);

      initGroupSortable(
        $groupElem.find('.table-of-contents-group-slides'),
        payload,
        (id) => allSlides.find(s => Number(s.id) === Number(id))
      );

      if (id) {
        $target.parents('.table-of-contents-group').after($groupElem);
        return;
      }

      $target.parents('.fragment-item').before(`<div class="fragment-item" style="border: 0 none"><div class="fragment-create"><button type="button" class="fragment-create-btn create-group">+ новая группа</button></div></div>`);
      $target.parents('.fragment-item').before($groupElem);
      $target.parents('.fragment-item').remove();
    });

    $body.on('click', '.create-group-card', ({target}) => {
      const $target = $(target);
      const groupId = $target.parents('[data-group-id]').attr('data-group-id');
      const cardId = $target.parents('[data-group-card-id]').attr('data-group-card-id');

      const card = payload.insertEmptyAfterCard(groupId, cardId);

      const $card = createGroupCardElement(
        card,
        [],
        slideId => allSlides.find(s => Number(s.id) === Number(slideId))
      );
      $card.append(`<div class="fragment-create"><button type="button" class="fragment-create-btn create-group-card">+ новая карточка</button></div>`);

      initGroupSortable(
        $card.find('.table-of-contents-group-slides'),
        payload,
        (id) => allSlides.find(s => Number(s.id) === Number(id))
      );

      if (cardId) {
        $target.parents('.table-of-contents-group-card').after($card);
        return;
      }

      $target.parents('.fragment-item').before(`<div class="fragment-item" style="border: 0 none; min-height: 30px"><div style="position: relative"><div class="fragment-create"><button type="button" class="fragment-create-btn create-group-card">+ новая карточка</button></div></div></div>`);
      $target.parents('.fragment-item').before($card);
      $target.parents('.fragment-item').remove();
    });

    $body.on('click', '.remove-slide', ({target}) => {
      const $element = $(target).parents('[data-slide-id]:eq(0)');
      const id = $element.attr('data-slide-id');
      payload.removeSlideFromGroup(id);
      $element.remove();
      drawAllSlidesGroup($body.find('.table-of-contents-all-slides'), payload, allSlides);

      initSlideDecks();
    });

    $body.on('click', '.remove-group', ({target}) => {
      const $element = $(target).parents('[data-group-id]:eq(0)');
      if ($element.find('.table-of-contents-group-slides > .table-of-contents-slide').length) {
        if (!confirm('Подтверждаете?')) {
          return;
        }
      }

      const id = $element.attr('data-group-id');
      payload.removeGroup(id);
      $element.remove();

      drawGroups($body.find('.table-of-contents-groups'), payload, allSlides);
      drawAllSlidesGroup($body.find('.table-of-contents-all-slides'), payload, allSlides);

      initSlideDecks();
    });

    $body.on('click', '.remove-group-card', ({target}) => {

      const $element = $(target).parents('[data-group-card-id]:eq(0)');
      if ($element.find('.table-of-contents-group-slides > .table-of-contents-slide').length) {
        if (!confirm('Подтверждаете?')) {
          return;
        }
      }

      const groupId = $(target).parents('[data-group-id]:eq(0)').attr('data-group-id');
      const id = $element.attr('data-group-card-id');
      payload.removeGroupCard(groupId, id);
      $element.remove();

      drawGroups($body.find('.table-of-contents-groups'), payload, allSlides);
      drawAllSlidesGroup($body.find('.table-of-contents-all-slides'), payload, allSlides);

      initSlideDecks();
    });

    $body.on('input', '.group-name', ({target}) => {
      const $element = $(target).parents('[data-group-id]:eq(0)');
      const id = $element.attr('data-group-id');
      payload.updateGroupName(id, target.value);
    });

    $body.on('input', '.group-card-name', ({target}) => {
      const $target = $(target);
      const groupId = $target.parents('[data-group-id]:eq(0)').attr('data-group-id');
      const cardId = $target.parents('[data-group-card-id]:eq(0)').attr('data-group-card-id');
      payload.updateGroupCardName(groupId, cardId, target.value);
    });

    $body.on('change', '.disable-nav', ({target}) => {
      payload.setDisableNav(target.checked);
    });

    $body.on('change', '.table-of-contents-card-img-file', async ({target}) => {
      const $target = $(target);
      const groupId = $target.parents('[data-group-id]').attr('data-group-id');
      const cardId = $target.parents('[data-group-card-id]').attr('data-group-card-id');
      const file = target.files[0];

      const formData = new FormData();
      formData.append('card_id', cardId);
      formData.append('image', file);

      const response = await window.Api.postForm('/admin/index.php?r=editor/table-of-contents/card-image', formData);
      if (response && response.success) {
        const {thumbnail} = response;
        const card = payload.updateGroupCardImage(groupId, cardId, thumbnail);
        updateHandler(payload.getPayload());
        $(`[data-group-card-id='${card.id}']`)
          .find('.card-header')
          .empty()
          .append(
            drawCardHeader(card)
          );
      }
    });

    $body.on('click', '.card-image-delete', (e) => {
      e.preventDefault();

      if (!confirm('Подтверждаете?')) {
        return;
      }

      const $target = $(e.target);
      const groupId = $target.parents('[data-group-id]').attr('data-group-id');
      const cardId = $target.parents('[data-group-card-id]').attr('data-group-card-id');

      const card = payload.updateGroupCardImage(groupId, cardId, null);
      updateHandler(payload.getPayload());

      $(`[data-group-card-id='${card.id}']`)
        .find('.card-header')
        .empty()
        .append(
          drawCardHeader(card)
        );
    });

    $body.find('.table-of-contents-save').on('click', () => {
      updateHandler(payload.getPayload());
      dialog.hide();
    });

    dialog.show({body: $body});
  }
}
