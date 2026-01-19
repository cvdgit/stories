;(function ($) {
  var slice = [].slice;
  $.whenAll = function (array) {
    var
      resolveValues = arguments.length == 1 && $.isArray(array)
        ? array
        : slice.call(arguments)
      , length = resolveValues.length
      , remaining = length
      , deferred = $.Deferred()
      , i = 0
      , failed = 0
      , rejectContexts = Array(length)
      , rejectValues = Array(length)
      , resolveContexts = Array(length)
      , value
    ;

    function updateFunc(index, contexts, values) {
      return function () {
        !(values === resolveValues) && failed++;
        deferred.notifyWith(
          contexts[index] = this
          , values[index] = slice.call(arguments)
        );
        if (!(--remaining)) {
          deferred[(!failed ? 'resolve' : 'reject') + 'With'](contexts, values);
        }
      };
    }

    for (; i < length; i++) {
      if ((value = resolveValues[i]) && $.isFunction(value.promise)) {
        value.promise()
          .done(updateFunc(i, resolveContexts, resolveValues))
          .fail(updateFunc(i, rejectContexts, rejectValues))
        ;
      } else {
        deferred.notifyWith(this, value);
        --remaining;
      }
    }
    if (!remaining) {
      deferred.resolveWith(resolveContexts, resolveValues);
    }
    return deferred.promise();
  };
})(jQuery);

function DataModifier(slideManager, cleaner) {

  this.slideManager = slideManager;
  this.cleaner = cleaner;

  this.saved = true;
  this.data = {};
  this.editorData = {};

  var indicator = $('#save-data');

  function haveChanges() {
    indicator.find('i')
      .removeClass('glyphicon-ok')
      .addClass('glyphicon-floppy-disk')
  }

  function changesSaved() {
    indicator.find('i')
      .removeClass('glyphicon-floppy-disk')
      .addClass('glyphicon-ok')
  }

  var that = this;

  function action(content, key) {
    return $.ajax({
      url: '/admin/index.php?r=editor/slide/save',
      type: 'POST',
      data: content,
      contentType: 'text/html; charset=utf-8',
      dataType: 'json',
      processData: false
    }).done(function (response) {
      if (response && response.success) {
        toastr.success('Изменения успешно сохранены');
      }
    });
  }

  var timeout = undefined;

  function startTimeout() {
    if (timeout !== undefined) {
      clearTimeout(timeout);
    }
    timeout = setTimeout(function () {
      var deferreds = [];
      Object.keys(that.data).forEach(function (key) {
        deferreds.push(action(that.data[key], key).done(function () {
          that.slideManager.updateSlideData(key, that.editorData[key]);
          delete that.data[key];
        }));
      });
      $.whenAll(deferreds).then(function () {
        that.saved = true;
        changesSaved();
      });
    }, 1000);
  }

  this.change = function () {
    this.saved = false;
    this.data[slideManager.getCurrentSlideID()] = cleaner.cleanSlideContent(true);
    this.editorData[slideManager.getCurrentSlideID()] = cleaner.cleanSlideContent();
    startTimeout();
    haveChanges();
  }
}

function EditorPopover() {

  this.popoverConfig = {
    'content': '',
    'html': true,
    'sanitize': false
  };

  function itemsTemplate(items) {
    return $('<div/>', {'class': 'prompt-wrapper'}).append(items);
  }

  function itemTemplate(item) {
    return $('<div/>', {'class': 'prompt-item'})
      .attr('data-action-name', item.name)
      .append(
        $('<div/>', {'class': 'prompt-item-inner'})
          .append($('<span/>', {'class': 'title'}).text(item.title))
      );
  }

  function createContent(items) {
    var fragment = $(document.createDocumentFragment());
    items.forEach(function (item) {
      fragment.append(itemTemplate(item));
    });
    return itemsTemplate(fragment);
  }

  var that = this;

  function createOptions(options, items) {
    options = $.extend(that.popoverConfig, options);
    options.content = createContent(items)[0].outerHTML;
    return options;
  }

  return {
    'attach': function (selector, options, items) {
      return $(selector)
        .popover(createOptions(options, items))
        .on('shown.bs.popover', function () {
          var that = this;
          items.forEach(function (item) {
            $('[data-action-name=' + item.name + ']').on('click', function () {
              item.click();
              $(that).popover('hide');
            });
          });
        });
    },
    'detach': function (selector) {
      $(selector).popover('dispose');
    }
  };
}

function SlideManager(options) {

  this.options = options;
  this.lessonId = this.options['lesson_id'] === null ? '' : this.options['lesson_id'];
  this.storyId = this.options['story_id'] === null ? '' : this.options['story_id'];

  this.$slidesList = $('#slides-list');

  function SlideWrapper(element, data) {
    this.element = element;
    this.data = data;
    this.getID = function () {
      return this.element.attr('data-id');
    };
    this.getElement = function () {
      return this.element;
    };
    this.getNumber = function () {
      return this.data.number;
    };
    this.delete = function () {
      this.element.remove();
      this.element = null;
    };
    this.setSlideView = function (view) {
      this.element.attr('data-slide-view', view);
    };
    this.getSlideView = () => this.element.attr('data-slide-view')
    this.isSlideLink = function () {
      return this.data.isLink;
    };
  }

  /** @var {SlideWrapper|null} */
  let currentSlide = null;
  this.setActiveSlide = function (element, data) {
    currentSlide = new SlideWrapper(element, data);
  }
  this.unsetActiveSlide = function () {
    currentSlide = null;
  }
  this.getActiveSlide = function () {
    return currentSlide;
  }
  this.getCurrentSlideID = function () {
    if (currentSlide === null) {
      return;
    }
    return currentSlide.getID();
  }

  this.modeIcons = {
    'hidden': {'class': 'glyphicon glyphicon-eye-close', 'title': 'Слайд скрыт'},
    'test': {'class': 'glyphicon glyphicon-question-sign', 'title': 'Слайд с тестом'},
    'link': {'class': 'glyphicon glyphicon-link', 'title': 'Ссылка на слайд'}
  }
  this.decks = [];
  this.slides = [];
}

SlideManager.prototype = {
  'saveSlidesOrder': function () {
    var formData = new FormData();
    formData.append('SlidesOrder[story_id]', this.storyId);
    formData.append('SlidesOrder[lesson_id]', this.lessonId);
    this.$slidesList.find('[data-slide-id]').each(function (i) {
      formData.append('SlidesOrder[slides][' + i + ']', $(this).attr('data-slide-id'));
      formData.append('SlidesOrder[order][' + i + ']', (++i).toString());
    });
    return $.ajax({
      'url': '/admin/index.php?r=editor/slide/save-order',
      'type': 'POST',
      'data': formData,
      cache: false,
      contentType: false,
      processData: false
    });
  },
  'toggleSlideVisible': function (hidden, id) {
    id = id || this.getCurrentSlideID();
    var $elem = this.$slidesList.find('div[data-slide-id=' + id + '].thumb-reveal-wrapper');
    if (hidden) {
      $elem.addClass('slide-hidden');
    } else {
      $elem.removeClass('slide-hidden');
    }
  },
  'loadSlidesList': function (toSetActiveSlideID, itemCallback) {

    this.$slidesList.empty();
    if (this.$slidesList.data('ui-sortable')) {
      this.$slidesList.sortable('destroy');
    }

    var that = this;

    function setActiveSlideItem(slideID) {
      that.$slidesList.find('[data-slide-id]').removeClass('active');
      that.$slidesList.find('[data-slide-id=' + slideID + ']').addClass("active");
    }

    function makeReveal(elem) {
      var deck = new Reveal(elem, {
        embedded: true
      });
      deck.initialize({
        'width': 1280,
        'height': 720,
        'margin': 0.01,
        'transition': 'none',
        'disableLayout': true,
        'controls': false,
        'progress': false,
        'slideNumber': false
      });
      return deck;
    }

    function modifySlide(data) {
      var $data = $(data);
      $data.find('div[data-block-type=image].sl-block')
        .each(function () {
          if (!$(this).find('img').length) {
            $('<div/>', {'class': 'sl-block-overlay sl-block-placeholder'})
              .appendTo($(this).find('div.sl-block-content'));
          }
        });
      return $data[0].outerHTML;
    }

    this.decks = [];
    this.slides = [];

    return $.ajax({
      'url': this.options['endpoint'],
      'type': 'GET',
      'dataType': 'json'
    }).done(function (data) {
      if (data.length === 0) {
        $('<div/>', {'class': 'no-slides'}).text('Нет слайдов').appendTo(that.$slidesList);
        return;
      }
      var $element;
      data.forEach(function (slide, i) {

        slide.data = modifySlide(slide.data);

        that.slides[slide.id] = slide;

        $element = $('<div/>', {
          'class': 'thumb-reveal-wrapper' + (slide.isHidden ? ' slide-hidden' : ''),
          'data-slide-id': slide.id
        })
          .on('click', function () {
            setActiveSlideItem(slide.id);
            itemCallback(slide);
            return false;
          })
          .append(
            $('<div/>', {'class': 'thumb-reveal-inner'})
              .append(
                $('<div/>', {
                  'class': 'thumb-reveal reveal',
                  'css': {'width': '1280px', 'height': '720px', 'transform': 'scale(0.1228)'}
                })
                  .append(
                    $('<div/>', {'class': 'slides'})
                      .append(slide.data)
                  )
              )
          )
          .append(
            $('<div/>', {'class': 'thumb-reveal-options'})
              .append($('<div/>', {
                'class': 'option slide-number',
                'text': slide.slideNumber
              }))
          )
          .append(
            $('<div/>', {'class': 'thumb-reveal-visible'})
              .append($('<div/>', {
                'class': 'option slide-visible',
                'html': '<i class="glyphicon glyphicon-eye-close"></i>',
                'title': 'Слайд скрыт'
              }))
          );

        if (slide.isLink) {
          $element.append(
            $('<div/>', {'class': 'thumb-reveal-is-link'})
              .append($('<div/>', {
                'class': 'option slide-is-link',
                'html': '<i class="glyphicon glyphicon-share-alt"></i>',
                'title': 'Ссылка на слайд'
              }))
          );
        }

        $element.appendTo(that.$slidesList);
        that.decks[slide.id] = makeReveal($element.find('.reveal')[0]);
      });

      that.$slidesList.sortable({
        over: function (event, ui) {
          var cl = ui.item.attr('class');
          $('.ui-state-highlight').addClass(cl);
        },
        placeholder: 'ui-state-highlight',
        //handle: '.slide-move',
        update: function () {
          that.saveSlidesOrder()
            .done(function (data) {
              if (data) {
                if (data.success) {
                  toastr.success('Порядок слайдов успешно изменен');
                  var i = 1;
                  that.$slidesList.find('div.thumb-reveal-wrapper > .thumb-reveal-options > .slide-number').each(function () {
                    $(this).text(i);
                    i++;
                  });
                } else {
                  toastr.error(JSON.stringify(data.errors));
                }
              } else {
                toastr.error('Неизвестная ошибка');
              }
            })
            .fail(function (data) {
              toastr.error(data.responseJSON.message);
            });
        }
      }).disableSelection();

      const $firstSlide = that.$slidesList.find('div.thumb-reveal-wrapper:eq(0)')
      if (toSetActiveSlideID) {
        const $toActiveSlide = that.$slidesList
          .find('div[data-slide-id=' + toSetActiveSlideID + '].thumb-reveal-wrapper')
        if ($toActiveSlide.length) {
          $toActiveSlide.click();
        } else {
          $firstSlide.click();
        }
      } else {
        $firstSlide.click();
      }

      if (that.decks.length > 0) {
        $.whenAll(that.decks).done(function () {
          if (toSetActiveSlideID) {
            const el = that.$slidesList
              .find('div[data-slide-id=' + toSetActiveSlideID + '].thumb-reveal-wrapper');
            if (el.length) {
              const rect = $('.slides-actions')[0].getBoundingClientRect();
              let top = el.offset().top - (rect.height + rect.top);
              if (top < 0) {
                top = 0;
              }
              that.$slidesList.animate({
                scrollTop: top
              }, 'fast');
            }
          }
        });
      }
    });
  },
  'loadSlide': function (slideID) {
    return $.getJSON('/admin/index.php', {
      'r': 'editor/load-slide',
      'story_id': this.options['story_id'],
      'slide_id': slideID
    });
  },
  'createSlide': function () {
    return $.getJSON('/admin/index.php', {
      'r': 'editor/slide/create',
      'story_id': this.options['story_id'],
      'lesson_id': this.options['lesson_id'],
      'current_slide_id': this.getCurrentSlideID()
    });
  },
  'deleteSlide': function () {
    return $.getJSON('/admin/index.php', {
      'r': 'editor/slide/delete',
      'slide_id': this.getCurrentSlideID()
    });
  },
  'copySlide': function () {
    return $.getJSON('/admin/index.php', {
      'r': 'editor/slide/copy',
      'slide_id': this.getCurrentSlideID(),
      'lesson_id': this.options['lesson_id'],
    });
  },
  'toggleVisible': function () {
    return $.getJSON('/admin/index.php', {
      'r': 'editor/slide/toggle-visible',
      'slide_id': this.getCurrentSlideID()
    });
  },
  'updateSlideData': function (id, data) {
    this.slides[id].data = data;
    this.$slidesList
      .find('div[data-slide-id=' + id + '].thumb-reveal-wrapper .reveal .slides')
      .html(data);
    this.decks[id].sync();
  }
}

function BlockModifier(modifier) {

  this.modifier = modifier;
  var that = this;

  function css(element, name, value) {
    element.css(name, value);
    that.change();
  }

  this.setLeft = function (element, left) {
    css(element, 'left', left);
  };
  this.setTop = function (element, top) {
    css(element, 'top', top);
  };
  this.setWidth = function (element, width) {
    css(element, 'width', width);
  };
  this.setHeight = function (element, height) {
    css(element, 'height', height);
  };
  this.change = function () {
    this.modifier.change();
  };
}

function BlockAlignment(modifier) {

  var slideWidth = 1280,
    slideHeight = 720;

  function setBlockAlign(element, align) {
    switch (align) {
      case 'left':
        modifier.setLeft(element, '0px');
        break;
      case 'right':
        modifier.setLeft(element, slideWidth - parseInt(element.css('width')) + 'px');
        break;
      case 'top':
        modifier.setTop(element, '0px');
        break;
      case 'bottom':
        modifier.setTop(element, slideHeight - parseInt(element.css('height')) + 'px');
        break;
      case 'horizontal_center':
        modifier.setLeft(element, (slideWidth - parseInt(element.css('width'))) / 2 + 'px');
        break;
      case 'vertical_center':
        modifier.setTop(element, (slideHeight - parseInt(element.css('height'))) / 2 + 'px');
        break;
      case 'slide_center':
        modifier.setLeft(element, (slideWidth - parseInt(element.css('width'))) / 2 + 'px');
        modifier.setTop(element, (slideHeight - parseInt(element.css('height'))) / 2 + 'px');
        break;
    }
  }

  this.left = function (element) {
    setBlockAlign(element, 'left');
  };
  this.right = function (element) {
    setBlockAlign(element, 'right');
  };
  this.top = function (element) {
    setBlockAlign(element, 'top');
  }
  this.bottom = function (element) {
    setBlockAlign(element, 'bottom');
  };
  this.horizontalCenter = function (element) {
    setBlockAlign(element, 'horizontal_center');
  };
  this.verticalCenter = function (element) {
    setBlockAlign(element, 'vertical_center');
  };
  this.slideCenter = function (element) {
    setBlockAlign(element, 'slide_center');
  };
}

function BlockToolbar(options) {
  this.container = $('.blocks-sidebars');
  this.options = options;
}

BlockToolbar.prototype = {
  'create': function () {
    this.toolbar = this.options.createToolbar();
    this.container.find('.blocks-sidebar.visible').removeClass('visible');
    this.container.append(this.toolbar.addClass('visible'));
    var that = this;
    this.container.find('[data-toolbar-action]').each(function () {
      var $elem = $(this);
      $elem.on('click', that.options.actions[$elem.attr('data-toolbar-action')]);
    });
    this.options.onCreate();
  },
  'remove': function () {
    if (!this.toolbar) {
      return;
    }
    this.toolbar.find('[data-toolbar-action]').each(function () {
      $(this).off('click');
    });
    this.toolbar.remove();
    this.container.find('.blocks-sidebar').addClass('visible');
    this.options.onRemove();
  },
  'show': function () {
    this.container.find('.blocks-sidebar').removeClass('hide');
  },
  'hide': function () {
    this.container.find('.blocks-sidebar').addClass('hide');
  }
}

function ContentCleaner(editor) {

  this.editor = editor;

  this.cleanSlideContent = function (save) {
    save = save || false;
    var data = this.editor.find('section').clone();
    $('#save-container').empty().append(data);

    var section = $('#save-container').find('section');
    var attributes = $.map(section[0].attributes, function (item) {
      return item.name;
    });
    $.each(attributes, function (i, item) {
      if ($.inArray(item, ['data-id', 'data-slide-view', 'data-audio-src']) === -1) {
        section.removeAttr(item);
      }
    });

    data.find('.table-of-contents-inner').remove();
    data.find('.sl-block-transform').remove();
    data.find('.sl-block-image-controls').remove();
    data.find('.ui-resizable-handle').remove();
    data.find('.sl-block.wikids-active-block').removeClass('wikids-active-block');
    data.find('.sl-block').removeClass('ui-draggable ui-draggable-handle ui-draggable-dragging ui-resizable');

    if (save) {
      data.find('img').each(function () {
        var $elem = $(this);
        var src = $elem.attr('src');
        $elem.removeAttr('src');
        src = src.replace(/[&|\?]+t=[0-9\.]+/g, '');
        $elem.attr('data-src', src);
        $elem.removeAttr('data-lazy-loaded');
      });

      var blockAttrNames = ['data-image-id'];
      data.find('.sl-block').each(function () {
        var $elem = $(this);
        blockAttrNames.forEach(function (blockAttrName) {
          $elem.removeAttr(blockAttrName);
        });
        if ($elem.find('.sl-block-placeholder').length) {
          $elem.find('.sl-block-placeholder').remove();
        }
      });
      data.find('div.new-questions').text('');
      data.find('div.wikids-video-player').text('');
      data.find('div.mental-map').text('')
    }
    return data[0].outerHTML;
  }

  this.cleanSlideBlock = function (block) {
    var cloneBlock = block.clone();
    $('#save-container').empty().append(cloneBlock);
    cloneBlock.find('.sl-block-transform').remove();
    cloneBlock.find('.sl-block-image-controls').remove();
    cloneBlock.find('.ui-resizable-handle').remove();
    cloneBlock.removeClass('wikids-active-block ui-draggable ui-draggable-handle ui-resizable');
    return cloneBlock[0].outerHTML;
  }
}

function BlockID() {

  function dec2hex(dec) {
    return dec.toString(16).padStart(2, '0');
  }

  function generateId(len) {
    var arr = new Uint8Array((len || 40) / 2);
    window.crypto.getRandomValues(arr);
    return Array.from(arr, dec2hex).join('');
  }

  this.generate = function () {
    return generateId(10);
  };
}

function uuidv4() {
  let d = new Date().getTime();
  let d2 = ((typeof performance !== 'undefined') && performance.now && (performance.now() * 1000)) || 0;
  return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
    let r = Math.random() * 16;
    if (d > 0) {
      r = (d + r) % 16 | 0;
      d = Math.floor(d / 16);
    }
    else {
      r = (d2 + r) % 16 | 0;
      d2 = Math.floor(d2 / 16);
    }
    return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
  });
}

function UrlManager() {
  this.location = window.location;
}

UrlManager.prototype.getSlideId = function () {
  var id = this.location.hash.replace(/#|\//gi, '')
  if (id === '') {
    id = undefined;
  }
  return id;
}
UrlManager.prototype.setSlideId = function (id) {
  this.location.hash = '#' + id;
}

const StoryEditor = (function () {
  "use strict";

  var $editor = $('#story-editor');

  $editor.on('mousedown', function (e) {
    var $target = $(e.target);
    if ($target.hasClass('sl-block') || $target.parents('.sl-block').length) {
      var $block = $(e.target);
      if (!$block.hasClass('sl-block')) {
        $block = $(e.target).parents('div.sl-block');
      }
      if (blockManager.getActive() && $block.data('blockId') !== blockManager.getActive().getID()) {
        unsetActiveBlock();
      }
      setActiveBlock($block);
    } else {
      unsetActiveBlock();
    }
  });

  var imageMoved = false;
  var imageOffset = [0, 0];
  var imageMousePosition = {};

  $editor
    .on('mouseup', function (e) {
      if (imageMoved) {
        blockModifier.change();
      }
      imageMoved = false;
      var $block = $(e.target).parents('div.sl-block:eq(0)');
      $block.removeClass('is-panning');
    });
  $(document).on('mousemove', function (e) {
    if (imageMoved) {
      imageMousePosition = {
        x: e.clientX,
        y: e.clientY
      };
      var $blockElement = blockManager.getActive().getElement();
      var $img = $blockElement.find('img');

      var imgLeft = parseInt($img.css('left')),
        imgTop = parseInt($img.css('top'));
      var imgWidth = parseInt($img.css('width')),
        imgHeight = parseInt($img.css('height'));

      var left = imgLeft + (imageMousePosition.x - imageOffset[0] - $img[0].offsetLeft);
      if (left > 0) {
        left = 0;
      }
      var maxLeft = -(parseInt($img.css('width')) - parseInt($blockElement.css('width')));
      if (left < maxLeft) {
        left = maxLeft;
      }
      $img.css('left', left + 'px');
      $img.attr('data-crop-x', (Math.abs(left) / imgWidth).toFixed(6));

      var top = imgTop + (imageMousePosition.y - imageOffset[1] - $img[0].offsetTop);
      if (top > 0) {
        top = 0;
      }
      var maxTop = -(parseInt($img.css('height')) - parseInt($blockElement.css('height')));
      if (top < maxTop) {
        top = maxTop;
      }
      $img.css('top', top + 'px');
      $img.attr('data-crop-y', (Math.abs(top) / imgHeight).toFixed(6));
    }
  });

  $editor.on({
    mousedown: function (e) {
      imageMoved = true;
      var $block = $(e.target).parents('div.sl-block:eq(0)');
      $block.addClass('is-panning');
      var $img = $block.find('img');
      imageOffset = [
        e.clientX - $img[0].offsetLeft,
        e.clientY - $img[0].offsetTop
      ];
    }
  }, '.sl-block-image-control');

  $editor[0].addEventListener('paste', e => {
    e.preventDefault();
    const availableTypes = e.clipboardData.types;

    if (availableTypes.includes('text/html')) {
      const html = e.clipboardData.getData('text/html');
      console.log(html);
    } else if (availableTypes.includes('text/plain')) {
      const text = e.clipboardData.getData('text/plain');
      if (text) {
        appendBlock(
          createEmptyBlock('text', text.replace(/(\r\n|\n|\r)/g, "<br>"))
        );
        blockModifier.change();
      }
    }

    /*if (e.clipboardData.files.length > 0) {
      console.log("Pasted content includes files.");
      Array.from(e.clipboardData.files).forEach(file => {
        if (file.type.startsWith('image/')) {
          console.log("Pasted file is an image:", file.name);
        }
      });
    }*/
  });

  function createImageBlockControls() {
    return $('<div/>', {'class': 'sl-block-image-controls'})
      .append(
        $('<div/>', {
          'class': 'sl-block-image-control button glyphicon glyphicon-move image-move',
          'title': 'Кликни и двигай'
        })
      );
  }

  $editor.on({
    mouseenter: function (e) {
      var $wrapper = $('<div/>', {'class': 'sl-block-transform sl-block-transform-hover'})
        .append($('<div/>', {'class': 'sl-block-border'}));
      var $block = $(e.target);
      if (!$block.hasClass('sl-block')) {
        $block = $(e.target).parents('div.sl-block');
      }
      $block.append($wrapper);
    },
    mouseleave: function (e) {
      $(".reveal .slides div.sl-block:not(.wikids-active-block)")
        .find('.sl-block-transform').remove();
    }
  }, 'div.sl-block:not(.wikids-active-block)');

  $editor.on({
    mouseenter: function (e) {
      var $block = $(e.target);
      if (!$block.hasClass('sl-block')) {
        $block = $(e.target).parents('div.sl-block');
      }
      if ($block.attr('data-block-type') === 'image' && blockManager.isCropped($block)) {
        $block.append(createImageBlockControls());
      }
    },
    mouseleave: function () {
      $(".reveal .slides div.sl-block")
        .find('.sl-block-image-controls').remove();
    }
  }, 'div.sl-block');

  $editor.on('dblclick', function (e) {
    var $target = $(e.target);
    if ($target.hasClass('sl-block') || $target.parents('.sl-block').length) {

      var block = blockManager.getActive();
      if (!block) {
        e.preventDefault();
        return;
      }
      if (!block.typeIsText()) {
        e.preventDefault();
        return;
      }

      var blockElement = block.getElement(),
        elem = blockElement.find('.slide-paragraph');

      if (blockElement.hasClass('is-editing')) {
        e.preventDefault();
        return;
      }

      $(blockElement).addClass('is-editing');

      if ($(blockElement).data('ui-draggable')) {
        $(blockElement).draggable('destroy');
      }

      if ($(blockElement).data('ui-resizable')) {
        $(blockElement).resizable('destroy');
      }

      elem.prop('contenteditable', true);

      var ed = CKEDITOR.inline(elem[0], {
        removePlugins: 'showborders,pastefromword',
        extraPlugins: 'font,justify,horizontalrule,colorbutton',
        format_tags: 'p;h2;h3;pre',
        startupFocus: true,
        //forcePasteAsPlainText: true,
        disableNativeSpellChecker: false,
        extraAllowedContent : 'span(*)[*]{*}',
        toolbarGroups: [
          {name: 'styles', groups: ['styles']},
          {name: 'colors', groups: ['colors']},
          {name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
          {name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph']},
          {name: 'links', groups: ['links']},
          {name: 'insert', groups: ['insert']}
        ],
        // Font
        removeButtons: 'About,Maximize,ShowBlocks,BGColor,Styles,Image,Flash,Table,Smiley,SpecialChar,PageBreak,Iframe,Anchor,BidiLtr,BidiRtl,Language,Source,Save,NewPage,ExportPdf,Preview,Print,Templates,Cut,Copy,Paste,PasteText,PasteFromWord,Undo,Redo,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Underline,CopyFormatting,CreateDiv,Indent,Outdent'
      });

      elem.data('editorName', ed.name);

      var contentIsChanged = false;
      /*            ed.on('blur', function() {
                      if (!this.getData().length) {
                          this.setData('<p>Введите текст</p>');
                      }
                      this.destroy();
                      elem.prop('contenteditable', false);
                      $(blockElement).removeClass('is-editing');
                      if (contentIsChanged) {
                          blockModifier.change();
                          contentIsChanged = false;
                      }
                      makeDraggable($(blockElement));
                  });*/

      ed.on('change', function () {
        contentIsChanged = true;
        elem.data('contentIsChanged', true);
      });
    }
  });

  var config = {
    storyID: "",
    getSlideAction: "",
    getSlideBlocksAction: "",
    getBlockFormAction: "",
    createBlockAction: "",
    newCreateBlockAction: "",
    deleteBlockAction: "",
    deleteSlideAction: "",
    currentSlidesAction: "",
    slideVisibleAction: "",
    createSlideAction: "",
    slidesAction: "",
    storyImagesAction: ""
  };

  var slidesManager,
    slideMenu,
    blockModifier,
    blockAlignment,
    blockToolbar,
    contentCleaner,
    blockIDGenerator,
    urlManager;

  /**
   * Инициализация редактора и всех компонентов
   * @param params
   */
  function initialize(params) {

    config = params;

    slideMenu = new SlideMenu($editor);

    slidesManager = new SlideManager({
      'story_id': params['storyID'],
      'endpoint': params['slidesEndpoint'],
      'lesson_id': params['lessonID']
    });
    contentCleaner = new ContentCleaner($editor);
    blockModifier = new BlockModifier(new DataModifier(slidesManager, contentCleaner));
    blockAlignment = new BlockAlignment(blockModifier);
    blockIDGenerator = new BlockID();

    function createToolbar() {

      function createToolbarItem(title, icon, action) {
        return $('<li/>', {'class': 'blocks-sidebar-item', 'data-toolbar-action': action})
          .append($('<span/>', {'class': 'glyphicon glyphicon-' + icon + ' icon'}))
          .append($('<span/>', {'class': 'text', 'text': title}));
      }

      function createToolbarItemWithImage(title, imageSrc, action) {
        return $('<li/>', {'class': 'blocks-sidebar-item', 'data-toolbar-action': action})
          .append($('<img/>', {'src': imageSrc, css: {width: '28px'}}))
          .append($('<span/>', {'class': 'text', 'text': title}));
      }

      function createToolbarItemGroup(actions) {
        var $group = $('<li/>', {'class': 'blocks-sidebar-item group'});
        actions.forEach(function (action) {
          $group.append(
            $('<span/>', {'class': 'group-item', 'data-toolbar-action': action.action})
              .append($('<span/>', {'class': 'text', 'text': action.title}))
          )
        })
        return $group;
      }

      var $list = $('<ul/>');
      var activeBlock = blockManager.getActive();

      if (!activeBlock.typeIsText() && !activeBlock.isPlaceholder()) {
        $list.append(createToolbarItem('Изменить', 'pencil', 'edit'));
      }

      if (activeBlock.isPlaceholder()) {
        $list.append(createToolbarItem('Выбрать', 'picture', 'select-image'));
      }

      if (activeBlock.typeIsImage() || activeBlock.typeIsVideo() || activeBlock.typeIsHtml()) {
        if (activeBlock.typeIsImage() && !activeBlock.isPlaceholder()) {
          $list.append(createToolbarItem('Заменить', 'circle-arrow-down', 'replace'));
        }
        $list.append(createToolbarItem('Растянуть', 'resize-full', 'stretch'));
      }

      if (activeBlock.typeIsImage() && !activeBlock.isPlaceholder()) {
        $list.append(createToolbarItemGroup([{'title': '1:1', 'action': 'natural-size'}]));
      }

      $list.append(createToolbarItem('Положение', 'align-center', 'align'));
      $list.append(createToolbarItem('Удалить', 'trash', 'delete'));

      //if (!activeBlock.isPlaceholder()) {
      $list.append(createToolbarItem('Копировать', 'duplicate', 'duplicate'));
      //}

      if (activeBlock.typeIsText()) {
        $list.append(createToolbarItemWithImage('Нейросеть', '/img/chatgpt-icon.png', 'text-gpt-actions'));
      }

      if (activeBlock.typeIsMentalMap() && slidesManager.getActiveSlide().getSlideView() === 'mental-map') {
        $list.append(createToolbarItemWithImage('Ментальная карта с вопросами', '/img/mental-map.png', 'mental-map-questions'));
      }

      return $('<div/>', {'class': 'blocks-sidebar'}).append($list);
    }

    var editorPopover = new EditorPopover();
    blockToolbar = new BlockToolbar({
      createToolbar,
      'onCreate': function () {
        editorPopover.attach('li[data-toolbar-action=align]', {'placement': 'left'}, [
          {
            'name': 'left', 'title': 'По левому краю', 'click': function () {
              blockAlignment.left(blockManager.getActive().getElement());
            }
          },
          {
            'name': 'right', 'title': 'По правому краю', 'click': function () {
              blockAlignment.right(blockManager.getActive().getElement());
            }
          },
          {
            'name': 'top', 'title': 'По верху', 'click': function () {
              blockAlignment.top(blockManager.getActive().getElement());
            }
          },
          {
            'name': 'bottom', 'title': 'По низу', 'click': function () {
              blockAlignment.bottom(blockManager.getActive().getElement());
            }
          },
          {
            'name': 'horizontal_center', 'title': 'По центру (горизонтально)', 'click': function () {
              blockAlignment.horizontalCenter(blockManager.getActive().getElement());
            }
          },
          {
            'name': 'vertical_center', 'title': 'По центру (вертикально)', 'click': function () {
              blockAlignment.verticalCenter(blockManager.getActive().getElement());
            }
          },
          {
            'name': 'slide_center', 'title': 'По центру слайда', 'click': function () {
              blockAlignment.slideCenter(blockManager.getActive().getElement());
            }
          }
        ]);
        editorPopover.attach('li[data-toolbar-action=text-gpt-actions]', {placement: 'left'}, [
          {
            name: 'gpt-rewrite',
            title: 'Переписать текст',
            click: () => params.gptRewriteHandler(blockManager.getActive(), blockModifier)
          },
          {
            name: 'gpt-speech-trainer',
            title: 'Речевой тренажер',
            click: () => params.gptSpeechTrainer(blockManager.getActive(), blockModifier)
          },
        ])
      },
      'onRemove': function () {
        editorPopover.detach('li[data-toolbar-action=align]');
        editorPopover.detach('li[data-toolbar-action=text-gpt-actions]');
      },
      'actions': {
        'stretch': function () {
          stretchToSlide();
        },
        'delete': function () {
          if (!confirm('Удалить блок?')) {
            return;
          }
          deleteBlockAction();
        },
        'duplicate': function () {
          copyBlockAction();
        },
        'edit': function () {
          config.onBlockUpdate(blockManager.getActive(), getUpdateBlockUrl(), contentCleaner.cleanSlideBlock(blockManager.getActive().getElement()));
        },
        'natural-size': function () {
          naturalSize();
        },
        'replace': function () {
          config.onImageReplace(blockManager.getActive().getID());
        },
        'select-image': function () {
          config.onImageReplace(blockManager.getActive().getID());
        },
        'mental-map-questions': () => {
          if (typeof params.mentalMapQuestionsHandler === 'function') {
            params.mentalMapQuestionsHandler(blockManager.getActive(), blockModifier)
          }
        }
      }
    });

    urlManager = new UrlManager();
    loadSlides(urlManager.getSlideId()).done(function () {
      config.onReady();
    });

    config.onInit();
  }

  function makeDraggable(element) {

    const blockId = $(element).attr('data-block-id');
    if (blockId) {
      const block = blockManager.find(blockId);
      if (block && block.typeIsTableOfContents()) {
        return;
      }
    }

    var containmentArea = $editor.find('section');
    var config = {
      start: function (event) {
        setActiveBlock($(event.target));
      },
      drag: function (event, ui) {
        var zoom = Reveal.getScale();
        var contWidth = containmentArea.width(),
          contHeight = containmentArea.height();
        ui.position.left = Math.max(0, Math.min(ui.position.left / zoom, contWidth - ui.helper.width()));
        ui.position.top = Math.max(0, Math.min(ui.position.top / zoom, contHeight - ui.helper.height()));
      },
      stop: function (event, ui) {
        blockModifier.setTop($(event.target), Math.round(ui.position.top) + "px");
        blockModifier.setLeft($(event.target), Math.round(ui.position.left) + "px");
      },
      grid: [5, 5],
      snap: true,
      snapMode: "outer",
      snapTolerance: 4,
      cancel: '.sl-block-image-controls'
    };
    element.draggable(config);
  }

  function makeResizable(element, config) {

    function resizeHandler(event, ui) {
      var zoomScale = Reveal.getScale();
      var opl = ui.originalPosition.left, opt = ui.originalPosition.top,
        pl = ui.position.left, pt = ui.position.top,
        osl = ui.originalSize.width, ost = ui.originalSize.height,
        sl = ui.size.width, st = ui.size.height;
      ui.size.width = osl + (sl - osl) / zoomScale;
      if (pl + osl !== opl + osl) { //left side
        ui.position.left = opl + (osl - ui.size.width);
      }
      ui.size.height = ost + (st - ost) / zoomScale;
      if (pt + ost !== opt + ost) { //top side
        ui.position.top = opt + (ost - ui.size.height);
      }

      var $element = $(event.target);
      if (blockManager.isCropped($element)) {
        var $img = $element.find('img');
        var widthOffset = ui.size.width - img.width;
        var heightOffset = ui.size.height - img.height;
        var imageHeight = img.height + (widthOffset * img.ratioH);
        var imageWidth = img.width + (heightOffset * img.ratioW);
        if (imageHeight > imageWidth) {
          blockManager.updateImageSize($img, ui.size.width, imageHeight);
        } else {
          blockManager.updateImageSize($img, imageWidth, ui.size.height);
        }
        var left = img.left * (imageWidth / img.width);
        var top = img.top * (imageHeight / img.height);
        blockManager.updateImagePosition($element.find('img'), left, top);
      }
    }

    var img = {};

    function startHandler(event, ui) {
      var $element = $(event.target);
      if (blockManager.isCropped($element)) {
        var $img = $element.find('img');
        img.width = parseInt($img.css('width'));
        img.height = parseInt($img.css('height'));
        img.ratioW = (img.width / img.height).toFixed(6);
        img.ratioH = (img.height / img.width).toFixed(6);
        img.left = parseInt($img.css('left'));
        img.top = parseInt($img.css('top'));
      }
    }

    function stopHandler(event, ui) {
      var $element = $(event.target);
      var blockType = $element.attr('data-block-type');
      blockModifier.setLeft($element, Math.round(ui.position.left) + "px");
      blockModifier.setTop($element, Math.round(ui.position.top) + "px");
      blockModifier.setWidth($element, Math.round(ui.size.width) + "px");
      if (blockType === 'text') {
        blockModifier.setHeight($element, 'auto');
      } else {
        blockModifier.setHeight($element, Math.round(ui.size.height) + "px");
      }
    }

    var resizableOptions = {
      resize: resizeHandler,
      start: startHandler,
      stop: stopHandler,
      grid: [5, 5],
      snap: true,
      snapMode: "outer",
      snapTolerance: 4,
      handles: 'all',
      aspectRatio: true
    };
    var conf = $.extend(resizableOptions, config);
    element.resizable(conf);
  }

  function makeResizableBlock(block) {
    if (block.typeIsTableOfContents()) {
      return;
    }
    var config = {};
    if (block.typeIsText()) {
      config = {
        handles: 'e, w',
        aspectRatio: false,
      };
    }
    if (block.typeIsVideo()) {
      config = {
        handles: 'all',
        aspectRatio: false,
      };
    }
    if (block.isPlaceholder()) {
      config = {
        handles: 'all',
        aspectRatio: false
      };
    }
    makeResizable(block.getElement(), config);
  }

  function selectActiveBlock(blockID) {
    $editor.find("div[data-block-id]").removeClass("wikids-active-block");
    $editor.find("div.sl-block").find('.sl-block-transform').remove();
    var block = blockManager.find(blockID);
    if ($.inArray(block.getType(), ['transition', 'test']) === -1) {
      makeResizableBlock(block);
    }
    var $wrapper = $('<div/>', {'class': 'sl-block-transform'}).append($('<div/>', {'class': 'sl-block-border-active'}));
    block.getElement().addClass("wikids-active-block").append($wrapper);
  }

  function unselectActiveBlock() {
    var $editingBlock = $editor.find('div[data-block-id].is-editing')
    if ($editingBlock.length) {
      var elem = $editingBlock.find('div[contenteditable]');
      var editorName = elem.data('editorName');
      for (var instance in CKEDITOR.instances) {

        if (instance === editorName) {

          var editor = CKEDITOR.instances[instance];

          if (!editor.getData().length) {
            editor.setData('<p>Введите текст</p>');
          }
          editor.destroy();

          elem.prop('contenteditable', false);
          $editingBlock.removeClass('is-editing');

          var contentIsChanged = elem.data('contentIsChanged');
          if (contentIsChanged) {
            blockModifier.change();
            elem.removeData('contentIsChanged');
          }
          makeDraggable($editingBlock);
        }
      }
    }
    $editor.find("div[data-block-id]").each(function () {
      var $block = $(this);
      $block.removeClass("wikids-active-block");
      if ($block.data('ui-resizable')) {
        $block.resizable('destroy');
      }
      /*if ($block.data('blockType') === 'text') {

          for (var instance in CKEDITOR.instances) {

              var editor = CKEDITOR.instances[instance];

              if (!editor.getData().length) {
                  editor.setData('<p>Введите текст</p>');
              }
              editor.destroy();

              var elem = $block.find('div.slide-paragraph');
              console.log($block);
              elem.prop('contenteditable', false);
              $block.removeClass('is-editing');

              var contentIsChanged = elem.data('contentIsChanged');
              if (contentIsChanged) {
                  blockModifier.change();
                  elem.removeData('contentIsChanged');
              }
              makeDraggable($block);
          }
      }*/
    })
    $editor.find('div.sl-block .sl-block-transform').remove();
  }

  var blockManager = (function (editor) {

    function BlockWrapper(element) {
      this.element = element;
    }

    BlockWrapper.prototype = {
      'getType': function () {
        return this.element.attr('data-block-type');
      },
      'getID': function () {
        return this.element.attr('data-block-id');
      },
      'delete': function () {
        dispatchEvent('onBlockDelete', {'block': this});
        this.element.remove();
        this.element = null;
      },
      'typeIsImage': function () {
        return this.getType() === 'image';
      },
      'isPlaceholder': function () {
        return (this.typeIsImage() && !this.getElement().find('img').length);
      },
      typeIsTableOfContents() {
        return this.getType() === 'table-of-contents';
      },
      'typeIsVideo': function () {
        return this.getType() === 'video' || this.getType() === 'videofile';
      },
      'typeIsHtml': function () {
        return this.getType() === 'html';
      },
      'typeIsTest': function () {
        return this.getType() === 'test';
      },
      'typeIsText': function () {
        return this.getType() === 'text';
      },
      typeIsMentalMap() {
        return this.getType() === 'mental_map'
      },
      typeIsMentalMapQuestions() {
        return this.typeIsMentalMap() && slidesManager.getActiveSlide().getSlideView() === 'mental-map-questions'
      },
      typeIsRetelling() {
        return this.getType() === 'retelling'
      },
      'getElement': function () {
        return this.element;
      },
      'getWidth': function () {
        return parseInt(this.element.css('width'));
      },
      'getHeight': function () {
        return parseInt(this.element.css('height'));
      }
    }

    var activeBlock = null;

    function extend(a, b) {
      for (let i in b) {
        a[i] = b[i];
      }
      return a;
    }

    function dispatchEvent(type, args) {
      const event = document.createEvent("HTMLEvents", 1, 2);
      event.initEvent(type, true, true);
      extend(event, args);
      editor[0].dispatchEvent(event);
    }

    return {
      'find': function (id) {
        const element = editor.find('section > div.sl-block[data-block-id=' + id + ']');
        return new BlockWrapper(element);
      },
      'append': function (element) {
        editor.find('section').append(element);
        var block = new BlockWrapper(element);
        dispatchEvent('onBlockCreate', {'block': block});
        return block;
      },
      'replace': function (element, placeholderBlockID) {
        var placeholder = this.find(placeholderBlockID).getElement();
        placeholder.replaceWith(element);
        var block = new BlockWrapper(element);
        dispatchEvent('onBlockCreate', {'block': block});
        return block;
      },
      'setActive': function (element) {
        activeBlock = new BlockWrapper(element);
      },
      'getActive': function () {
        return activeBlock;
      },
      'unsetActive': function () {
        activeBlock = null;
      },
      'deleteBlock': function (slideID, data) {
        return $.ajax({
          url: '/admin/index.php?r=editor/block/delete&slide_id=' + slideID,
          type: 'POST',
          data: data,
          contentType: 'text/html; charset=utf-8',
          dataType: 'json',
          processData: false
        });
      },
      'addEventListener': function (type, listener, useCapture) {
        if ('addEventListener' in window) {
          editor[0].addEventListener(type, listener, useCapture);
        }
      },
      'replaceBlockImage': function (blockID, imageProps) {
        var block = this.find(blockID),
          blockElement = block.getElement();
        blockElement.attr('data-image-id', imageProps.id);
        var isPlaceholder = false;
        if (!blockElement.find('img').length) {
          isPlaceholder = true;
          blockElement.find('div.sl-block-content')
            .empty()
            .append($('<img/>'));
        }
        this.updateImageAttributes(blockElement.find('img'), imageProps, {
          'width': block.getWidth(),
          'height': block.getHeight()
        });
        blockModifier.change();
        if (isPlaceholder) {
          blockToolbar.remove();
          blockToolbar.create();
        }
      },
      'isCropped': function (blockElement) {
        var $img = blockElement.find('img');
        if (!$img.length) {
          return false;
        }
        return $img.attr('data-crop-x') !== undefined
          || $img.attr('data-crop-y') !== undefined
          || $img.attr('data-crop-width') !== undefined
          || $img.attr('data-crop-height') !== undefined;
      },
      'updateImageAttributes': function (imageElement, imageProps, blockProps) {
        imageElement
          .attr({
            'src': imageProps.url,
            'data-natural-width': imageProps.natural_width,
            'data-natural-height': imageProps.natural_height
          });
        this.updateImageSize(imageElement, imageProps.width, imageProps.height)

        var calcInitPosition = function (sizeA, sizeB) {
          var pos = 0;
          if (sizeA < sizeB) {
            pos = (sizeB - sizeA) / 2;
            pos = -Math.abs(pos);
          }
          return pos;
        }
        this.updateImagePosition(imageElement, calcInitPosition(blockProps.width, imageProps.width), calcInitPosition(blockProps.height, imageProps.height));

        this.updateCropImageAttributes(imageElement, imageProps, blockProps);
      },
      'updateImagePosition': function (imageElement, left, top) {
        imageElement
          .css({
            'left': left + 'px',
            'top': top + 'px'
          });
      },
      'updateImageSize': function (imageElement, width, height) {
        imageElement
          .css({
            'width': width + 'px',
            'height': height + 'px'
          });
      },
      'updateCropImageAttributes': function (imageElement, imageProps, blockProps) {
        var imageLeft = Math.abs(parseInt(imageElement.css('left')));
        var imageTop = Math.abs(parseInt(imageElement.css('top')));
        imageElement
          .attr({
            'data-crop-x': (imageLeft / imageProps.width).toFixed(6),
            'data-crop-y': (imageTop / imageProps.height).toFixed(6),
            'data-crop-width': (blockProps.width / imageProps.width).toFixed(6),
            'data-crop-height': (blockProps.height / imageProps.height).toFixed(6)
          });
      }
    }
  }($editor));

  blockManager.addEventListener('onBlockDelete', function (e) {
    if (e.block.typeIsHtml()) {
      // При удалении блока с тестом, изменить тип слайда с new-question на slide
      slidesManager.getActiveSlide().setSlideView('slide');
    }
  });

  blockManager.addEventListener('onBlockCreate', function (e) {
    if (e.block.typeIsHtml()) {
      // При создании блока с тестом, изменить тип слайда на new-question
      slidesManager.getActiveSlide().setSlideView('new-question');
    }
    setActiveBlock(e.block.getElement());
  });

  function setActiveBlock(element) {
    if (blockManager.getActive()) {
      if (element.data('blockId') === blockManager.getActive().getID()) {
        return;
      } else {
        unsetActiveBlock();
      }
    }
    blockManager.setActive(element);
    blockToolbar.create();
    selectActiveBlock(blockManager.getActive().getID());
  }

  function unsetActiveBlock() {
    unselectActiveBlock();
    blockManager.unsetActive();
    blockToolbar.remove();
  }

  function createSlideAction() {
    slidesManager.createSlide().done(function (data) {
      toastr.success('Слайд успешно создан');
      loadSlides(data.id);
    });
  }

  function deleteBlock(block) {
    unsetActiveBlock();
    block.delete();
    blockModifier.change();
  }

  function deleteBlockAction(blockID) {
    var block = blockID ? blockManager.find(blockID) : blockManager.getActive();
    if (block.typeIsImage() || block.typeIsVideo() || block.typeIsHtml() || block.typeIsTest()) {
      blockManager.deleteBlock(slidesManager.getCurrentSlideID(), contentCleaner.cleanSlideBlock(block.getElement()))
        .done(function (response) {
          deleteBlock(block);
        });
    } else {
      deleteBlock(block);
    }
  }

  function copyBlock(block, id) {
    var copyBlock = $(contentCleaner.cleanSlideBlock(block.getElement()));
    copyBlock.css({
      'left': (55 + parseInt(copyBlock.css('left'))) + 'px',
      'top': (55 + parseInt(copyBlock.css('top'))) + 'px'
    });
    copyBlock.attr('data-block-id', id);
    appendBlock(copyBlock);
    blockModifier.change();
  }

  function copyBlockAction(blockID) {
    var block = blockID ? blockManager.find(blockID) : blockManager.getActive();
    var copyBlockID = blockIDGenerator.generate();
    if ((block.typeIsImage() && !block.isPlaceholder()) || block.typeIsVideo() || block.typeIsHtml()) {
      $.ajax({
        url: '/admin/index.php?r=editor/block/copy&slide_id=' + slidesManager.getCurrentSlideID() + '&block_id=' + copyBlockID,
        type: 'POST',
        data: contentCleaner.cleanSlideBlock(block.getElement()),
        contentType: 'text/html; charset=utf-8',
        dataType: 'json',
        processData: false
      }).done(function (response) {
        copyBlock(block, copyBlockID);
      });
    } else {
      copyBlock(block, copyBlockID);
    }
  }

  /**
   * Загрузка слайда
   * @param slideID
   */
  function loadSlide(slideID) {
    return slidesManager.loadSlide(slideID).done(function (data) {
      $('.slides', $editor).html(data.data);
      Reveal.sync();
      Reveal.slide(0);

      if (typeof config.onSlideLoad === 'function') {
        config.onSlideLoad(
          $editor.find('.slides section.present')
        );
      }

      slidesManager.setActiveSlide($editor.find('section'), data);
      slideMenu.init(data);
      makeDraggable($editor.find('.sl-block'));
    }).fail(function (data) {
      $editor.text(JSON.stringify(data));
    });
  }

  function loadSlideWithData(slideData) {
    $('.slides', $editor).html(slideData.data);
    Reveal.sync();
    Reveal.slide(0);

    if (typeof config.onSlideLoad === 'function') {
      config.onSlideLoad(
        $editor.find('.slides section.present')
      );
    }

    slidesManager.setActiveSlide($editor.find('section'), slideData);
    if (slideData.isLink) {
      $editor.find('section')
        .attr('title', 'Перейти к исходному слайду')
        .on('click', () => window.open(slideData.linkUrl, '_blank'))
        .addClass('is-link')

      blockToolbar.hide();
      //slideMenu.hide();
    } else {
      blockToolbar.show();
      slideMenu.show();
    }
    slideMenu.init(slideData);
    makeDraggable($editor.find('.sl-block'));
  }

  /**
   * Загрузка списка слайдов
   * @param toSetActiveSlideID
   */
  function loadSlides(toSetActiveSlideID) {
    return slidesManager.loadSlidesList(toSetActiveSlideID, function (slideData) {
      unsetActiveBlock();
      loadSlideWithData(slideData);
      urlManager.setSlideId(slideData.id);
    }).done(function (data) {
      if (data.length) {
        if (slidesManager.getActiveSlide().isSlideLink()) {
          blockToolbar.hide();
        } else {
          blockToolbar.show();
        }
        slideMenu.show();
      } else {
        blockToolbar.hide();
        slideMenu.hide();
      }
    });
  }

  function deleteSlide() {
    var slide = slidesManager.getActiveSlide();
    slide.delete();
    slidesManager.unsetActiveSlide();
  }

  function deleteSlideAction() {
    return slidesManager.deleteSlide().done(function (data) {
      if (data && data.success) {
        deleteSlide();
        loadSlides(data?.slide_id);
      }
    });
  }

  function copySlideAction() {
    slidesManager.copySlide().done(function (data) {
      if (data && data.success) {
        loadSlides(data.id);
      }
    }).fail(function () {
      toastr.error('Не удалось скопировать слайд');
    });
  }

  /*function previewContainerSetHeight() {
      var height = parseInt($('.story-container').css('height'));
      $previewContainer.css('height', height + 'px');
  }
  previewContainerSetHeight();*/

  window.addEventListener('resize', function () {
    if (slideMenu) {
      slideMenu.setPosition();
    }
  });

  function SlideMenu($ed) {
    "use strict";

    this.$slideMenu = $('.slide-menu');
    this.$slideMenuList = this.$slideMenu.find('ul');

    this.init = function (slideData) {
      this.setPosition();
      this.$slideMenu.show();
      this.slideVisibleToggleAction(slideData.status);
      setLinksIn(slideData.haveLinks);
      setRelationsIn(slideData.haveNeoRelations);
    }

    var that = this;

    function setLinksIn(haveLinks) {
      var element = that.getActionElement('links');
      haveLinks
        ? element.addClass('set-in')
        : element.removeClass('set-in');
    }

    function setRelationsIn(haveNeoRelations) {
      var element = that.getActionElement('relation');
      haveNeoRelations
        ? element.addClass('set-in')
        : element.removeClass('set-in');
    }

    this.setPosition = function () {
      var slidesRect = $ed.find('.slides')[0].getBoundingClientRect();
      var height = $ed.height(),
        slidesHeight = slidesRect.height,
        top = ((height - slidesHeight) / 2) - this.$slideMenu.height() + 'px';
      var width = $ed.width(),
        slidesWidth = slidesRect.width,
        w = width - slidesWidth;
      if (w < 0) {
        w = slidesWidth;
      } else {
        w = w / 2;
      }
      var left = slidesWidth + w - this.$slideMenu.width() + 1 + 'px';
      this.$slideMenu.css({'left': left, 'top': top});
    }

    this.getActionElement = function (name) {
      return this.$slideMenuList.find('li[data-slide-action=' + name + ']');
    }

    this.slideVisibleToggle = function (element, visible) {
      element.data('visible', visible);
      const OPEN = 'glyphicon-eye-open';
      const CLOSE = 'glyphicon-eye-close';
      var $el = element.find('span');
      visible === 1 && $el.removeClass(CLOSE).addClass(OPEN) && element.removeClass('set-in');
      visible === 2 && $el.removeClass(OPEN).addClass(CLOSE) && element.addClass('set-in');
    }

    this.slideVisibleToggleAction = function (visible) {
      this.slideVisibleToggle(this.getActionElement('visible'), visible);
    }

    this.hide = function () {
      this.$slideMenu.addClass('hide');
    }
    this.show = function () {
      this.$slideMenu.removeClass('hide');
    }
  }

  function slideVisibleToggleAction() {
    return slidesManager.toggleVisible().done(function (data) {
      if (data && data.success) {
        slideMenu.slideVisibleToggleAction(data.status);
        slidesManager.toggleSlideVisible(data.status === 2)
      } else {
        toastr.error('slideVisibleToggleAction error');
      }
    });
  }

  function getConfigValue(value) {
    return config[value];
  }

  function stretchToSlide(element) {
    element = element || blockManager.getActive().getElement();
    if (element === null) {
      return;
    }
    blockModifier.setLeft(element, '0px');
    blockModifier.setTop(element, '0px');
    blockModifier.setWidth(element, '1280px');
    blockModifier.setHeight(element, '720px');
  }

  function naturalSize() {
    var element = blockManager.getActive().getElement();
    if (element === null) {
      return;
    }
    var $img = element.find('img');
    if (blockManager.isCropped(element)) {

      function getImageOptions(blockProps, imageProps) {
        var imageRatioH = (imageProps.height / imageProps.width);
        var widthOffset = blockProps.width - imageProps.width;
        var imageHeight = imageProps.height + (widthOffset * imageRatioH);
        var imageRatioW = (imageProps.width / imageProps.height);
        var heightOffset = blockProps.height - imageProps.height;
        var imageWidth = imageProps.width + (heightOffset * imageRatioW);
        var width, height;
        if (imageHeight > imageWidth) {
          width = blockProps.width;
          height = imageHeight;
        } else {
          width = imageWidth;
          height = blockProps.height;
        }
        var left = imageProps.left * (imageWidth / imageProps.width);
        if (left > 0) {
          left = 0;
        }
        var maxLeft = -(imageWidth - blockProps.width);
        if (left < maxLeft) {
          left = maxLeft;
        }
        return {
          width,
          height,
          left,
          top: imageProps.top * (imageHeight / imageProps.height)
        }
      }

      var height = parseInt($img.attr('data-natural-height'));
      var ratio = parseFloat(element.css('width')) / parseFloat(element.css('height'));
      var width = (height * ratio).toFixed(6);

      blockModifier.setWidth(element, width + "px");
      blockModifier.setHeight(element, height + "px");

      var blockProps = {width, height};
      var imageProps = {
        width: parseInt($img.attr('data-natural-width')),
        height: parseInt($img.attr('data-natural-height')),
        left: parseFloat($img.css('left')),
        top: parseFloat($img.css('top'))
      };
      var imageOptions = getImageOptions(blockProps, imageProps);

      blockManager.updateImageSize($img, imageOptions.width, imageOptions.height);
      blockManager.updateImagePosition($img, imageOptions.left, imageOptions.top);
    } else {
      blockModifier.setWidth(element, $img.attr('data-natural-width') + 'px');
      blockModifier.setHeight(element, $img.attr('data-natural-height') + 'px');
    }
    blockAlignment.slideCenter(element);
  }

  function appendBlock(blockHtml, placeholderBlockID) {
    if (!(blockHtml instanceof jQuery)) {
      blockHtml = $(blockHtml);
    }
    var block;
    if (placeholderBlockID) {
      block = blockManager.replace(blockHtml, placeholderBlockID);
    } else {
      block = blockManager.append(blockHtml);
    }
    makeDraggable(blockHtml);
    return block;
  }

  function createBlock(blockHtml, placeholderBlockID) {
    const block = appendBlock(blockHtml, placeholderBlockID);
    if (block.typeIsVideo() || block.typeIsHtml() || block.typeIsMentalMap()) {
      stretchToSlide(block.getElement());
    } else {
      blockAlignment.slideCenter(block.getElement());
    }
    return block.getID();
  }

  function updateBlock(blockID, blockHtml) {
    var block = blockManager.find(blockID);
    unsetActiveBlock();
    block.delete();
    appendBlock(blockHtml);
  }

  function getUpdateBlockUrl() {
    return config.getBlockFormAction + "&slide_id=" + slidesManager.getCurrentSlideID() + "&block_id=" + blockManager.getActive().getID();
  }

  function createEmptyBlock(type, defaultText = 'Введите текст') {
    var block = $('<div/>', {
      'class': 'sl-block',
      'data-block-id': blockIDGenerator.generate(),
      'data-block-type': type,
      'css': {'width': '290px', 'height': 'auto'}
    });
    var blockContent = $('<div/>', {
      'class': 'sl-block-content',
      'data-placeholder': 'div',
      'data-placeholder-text': 'Text',
      'css': {'z-index': 12, 'text-align': 'left'}
    });

    $(`<div class="slide-paragraph"><p>${defaultText}</p></div>`)
      .appendTo(blockContent);

    blockContent.appendTo(block);

    return block[0].outerHTML;
  }

  function createImagePlaceholder() {
    var block = $('<div/>', {
      'class': 'sl-block',
      'data-block-id': blockIDGenerator.generate(),
      'data-block-type': 'image',
      'css': {'width': '800px', 'height': '600px'}
    });
    var blockContent = $('<div/>', {
      'class': 'sl-block-content',
      'css': {'z-index': 11}
    });
    var placeholder = $('<div/>', {
      'class': 'sl-block-overlay sl-block-placeholder',
      'css': {'z-index': 11}
    });
    return block.append(blockContent.append(placeholder))[0].outerHTML;
  }

  return {
    initialize,

    "deleteBlock": deleteBlock,

    'deleteSlide': deleteSlideAction,
    'createSlide': createSlideAction,
    'copySlide': copySlideAction,
    'slideVisibleToggle': slideVisibleToggleAction,

    "getConfigValue": getConfigValue,


    'getCreateBlockUrl': function (blockType) {
      return '/admin/index.php?r=editor/form-create&slide_id=' + slidesManager.getCurrentSlideID() + '&block_type=' + blockType;
    },
    'createSlideBlock': createBlock,
    'updateSlideBlock': updateBlock,
    "getNormalizedSlideContent": function () {
      return contentCleaner.cleanSlideContent(true);
    },

    'getStoryID': function () {
      return getConfigValue('storyID');
    },
    'getCurrentSlideID': function () {
      return slidesManager.getCurrentSlideID();
    },
    'getCurrentSlide': function () {
      return slidesManager.getActiveSlide();
    },

    loadSlides,
    loadSlide,

    'getSlidePreviewUrl': function () {
      var url = getConfigValue('storyUrl') + '#/';
      var activeSlide = slidesManager.getActiveSlide();
      if (activeSlide) {
        var number = parseInt(activeSlide.getNumber());
        if (number > 1) {
          url += number;
        }
      }
      return url;
    },

    createEmptyBlock,
    'findBlockByID': function (id) {
      return blockManager.find(id);
    },
    'replaceBlockImage': function (blockID, imageProps) {
      blockManager.replaceBlockImage(blockID, imageProps);
    },

    createImagePlaceholder,

    createMentalMapBlock() {
      const block = $('<div/>', {
        class: 'sl-block',
        'data-block-id': blockIDGenerator.generate(),
        'data-block-type': 'mental_map',
        css: {width: '1280px', height: '720px', left: 0, top: 0}
      })
      const blockContent = $('<div/>', {
        class: 'sl-block-content',
        'data-placeholder': 'div',
        'data-placeholder-text': 'Text',
        css: {'z-index': 12, 'text-align': 'left'}
      })

      const id = uuidv4()
      $('<div/>', {
        class: 'mental-map',
        'data-mental-map-id': id,
        html: `<a href="/admin/index.php?r=mental-map/editor&id=${id}">Ментальная карта</a>`
      })
        .appendTo(blockContent)

      blockContent.appendTo(block)

      return block[0].outerHTML;
    },

    createTableOfContentsBlock() {
      const block = $('<div/>', {
        class: 'sl-block',
        'data-block-id': blockIDGenerator.generate(),
        'data-block-type': 'table-of-contents',
        css: {width: '1280px', height: '720px', left: 0, top: 0}
      });
      const blockContent = $('<div/>', {
        class: 'sl-block-content',
        'data-placeholder': 'div',
        'data-placeholder-text': 'Text',
        css: {'z-index': 12, 'text-align': 'left'}
      });

      const id = uuidv4();
      $(
        `<div class="table-of-contents">
    <script class="table-of-contents-payload" type="application/json">{
        "title": "Оглавление",
        "groups": []
    }</script>
</div>`
      ).appendTo(blockContent);

      blockContent.appendTo(block);

      return block[0].outerHTML;
    },

    change() {
      blockModifier.change();
    },

    slides() {
      const slides = [];
      slidesManager.slides.forEach(s => slides.push(s));
      return [...slides]
        .filter(s => !s.haveTableOfContents)
        .filter(s => !s.isHidden)
        .sort((a, b) => a.slideNumber - b.slideNumber);
    }
  };
})();
