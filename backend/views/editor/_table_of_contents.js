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
  }

  function createGroupCardElement({id, name}, slides, findSlide) {
    const $element = $(`<div data-group-card-id="${id}" class="table-of-contents-group-card">
<h4 style="display: flex; flex-direction: row; justify-content: space-between; align-items: center">
<input type="text" class="form-control group-card-name" value="${name}">
<button title="Удалить карточку" class="remove-group-card" type="button"><i class="glyphicon glyphicon-trash"></i></button>
</h4>
<div class="table-of-contents-group-slides" style="display: grid; padding: 10px; min-height: 140px; background-color: #eee; gap: 20px; grid-template-columns: 1fr 1fr 1fr 1fr; width: 100%;"></div>
    </div>`);

    slides.map(({id}) => {
      const slide = findSlide(id);
      if (!slide) {
        return;
      }
      const {data, slideNumber} = slide;
      const slideElem = createSlideElem({id, data, slideNumber}, true);
      $element.find('.table-of-contents-group-slides').append(slideElem);
    });

    return $element;
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

    (cards || []).map(({id, name}) => {
      const $card = createGroupCardElement(
        {id, name},
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
    <div id="col-left" style="width: 300px; overflow-y: auto">
        <div style="display: flex; flex-direction: column; gap: 10px">
            <h4 class="h4" style="margin-top: 0">Слайды истории</h4>
            <div class="table-of-contents-all-slides"
                 style="display: flex; flex-direction: column; padding: 10px; min-height: 140px; background-color: #eee; gap: 20px; width: 100%;"></div>
        </div>
    </div>
    <div id="col-right" style="flex: 1; overflow-y: auto">
        <div class="form-group">
            <label for="tableOfContentsTitle">Название</label>
            <input
                    id="tableOfContentsTitle"
                    style="max-width: 50%"
                    class="table-of-contents-title form-control"
                    type="text"
                    value="${payload.getTitle() || 'Оглавление'}"
            />
        </div>
        <div style="margin: 20px 0; display: flex; flex-direction: column; gap: 10px">
            <h4 class="h4" style="margin: 0">Группы слайдов</h4>
            <div class="table-of-contents-groups"></div>
        </div>
        <div style="padding: 20px 0; display: flex; flex-direction: row; justify-content: end">
            <button class="btn btn-primary table-of-contents-save" type="button">Сохранить</button>
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

    $body.find('.table-of-contents-save').on('click', () => {
      updateHandler(payload.getPayload());
      dialog.hide();
    });

    dialog.on('show', () => {
      /*const left = document.getElementById('col-left');
      const right = document.getElementById('col-right');
      const leftWrapper = document.getElementById('leftWrapper');
      const scrollContainer = $('#tableOfContentsModal')[0];
      console.log(scrollContainer)
      let topOffset = 0;
      const syncHeight = () => {
        left.style.height = right.offsetHeight + 'px';
      };

      const observer = new ResizeObserver(updateAll);
      observer.observe(right);

      window.addEventListener('resize', syncHeight);

      function updatePosition() {
        const containerRect = scrollContainer.getBoundingClientRect();
        const wrapperRect = leftWrapper.getBoundingClientRect();
        const rightRect = right.getBoundingClientRect();

        const scrollTop = scrollContainer.scrollTop;

        const wrapperTop =
          wrapperRect.top - containerRect.top + scrollTop;

        const wrapperBottom = wrapperTop + right.offsetHeight;

        const currentTop = scrollTop;

        if (
          currentTop > wrapperTop &&
          currentTop + left.offsetHeight < wrapperBottom
        ) {
          // "sticky"
          left.style.position = 'absolute';
          left.style.top = (currentTop - wrapperTop) + 'px';
          left.style.left = '0';
          left.style.width = '100%';
        } else if (currentTop + left.offsetHeight >= wrapperBottom) {
          // стоп внизу
          left.style.position = 'absolute';
          left.style.top = (right.offsetHeight - left.offsetHeight) + 'px';
        } else {
          // исходное состояние
          left.style.position = 'relative';
        }
        console.log(scrollTop)
      }

      function updateAll() {
        syncHeight();
        updatePosition();
      }

      updateAll();

      $('#tableOfContentsModal').on('scroll', updatePosition);*/
    })

    dialog.show({body: $body});
  }
}
