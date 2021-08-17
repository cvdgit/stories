
;(function($) {
    var slice = [].slice;
    $.whenAll = function(array) {
        var
            resolveValues = arguments.length == 1 && $.isArray(array)
                ? array
                : slice.call(arguments)
            ,length = resolveValues.length
            ,remaining = length
            ,deferred = $.Deferred()
            ,i = 0
            ,failed = 0
            ,rejectContexts = Array(length)
            ,rejectValues = Array(length)
            ,resolveContexts = Array(length)
            ,value
        ;
        function updateFunc (index, contexts, values) {
            return function() {
                !(values === resolveValues) && failed++;
                deferred.notifyWith(
                    contexts[index] = this
                    ,values[index] = slice.call(arguments)
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
            }
            else {
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
        }).done(function(response) {
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
        timeout = setTimeout(function() {
            var deferreds = [];
            Object.keys(that.data).forEach(function(key) {
                deferreds.push(action(that.data[key], key).done(function() {
                    that.slideManager.updateSlideData(key, that.editorData[key]);
                    delete that.data[key];
                }));
            });
            $.whenAll(deferreds).then(function() {
                that.saved = true;
                changesSaved();
            });
        }, 1000);
    }

    this.change = function() {
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
        items.forEach(function(item) {
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
        'attach': function(selector, options, items) {
            return $(selector)
                .popover(createOptions(options, items))
                .on('shown.bs.popover', function() {
                    var that = this;
                    items.forEach(function(item) {
                        $('[data-action-name=' + item.name + ']').on('click', function() {
                            item.click();
                            $(that).popover('hide');
                        });
                    });
                });
        },
        'detach': function(selector) {
            $(selector).popover('dispose');
        }
    };
}

function SlideManager(options) {

    this.options = options;
    this.$slidesList = $('#slides-list');

    function SlideWrapper(element, data) {
        this.element = element;
        this.data = data;
        this.getID = function() {
            return this.element.attr('data-id');
        };
        this.getElement = function() {
            return this.element;
        };
        this.getNumber = function() {
            return this.data.number;
        };
        this.delete = function() {
            this.element.remove();
            this.element = null;
        };
        this.setSlideView = function(view) {
            this.element.attr('data-slide-view', view);
        }
    }

    var currentSlide = null;
    this.setActiveSlide = function(element, data) {
        currentSlide = new SlideWrapper(element, data);
    }
    this.unsetActiveSlide = function() {
        currentSlide = null;
    }
    this.getActiveSlide = function() {
        return currentSlide;
    }
    this.getCurrentSlideID = function() {
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
    'saveSlidesOrder': function() {
        var formData = new FormData();
        formData.append('SlidesOrder[story_id]', this.options['story_id']);
        this.$slidesList.find('[data-slide-id]').each(function(i) {
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
    'toggleSlideVisible': function(hidden, id) {
        id = id || this.getCurrentSlideID();
        var $elem = this.$slidesList.find('div[data-slide-id=' + id + '].thumb-reveal-wrapper');
        if (hidden) {
            $elem.addClass('slide-hidden');
        }
        else {
            $elem.removeClass('slide-hidden');
        }
    },
    'loadSlidesList': function(toSetActiveSlideID, itemCallback) {

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
                .each(function() {
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
            'url': '/admin/index.php?r=editor/slides&story_id=' + this.options['story_id'],
            'type': 'GET',
            'dataType': 'json'
        }).done(function(data) {
            if (data.length === 0) {
                $('<div/>', {'class': 'no-slides'}).text('Нет слайдов').appendTo(that.$slidesList);
                return;
            }
            var $element;
            data.forEach(function(slide, i) {

                slide.data = modifySlide(slide.data);

                that.slides[slide.id] = slide;

                $element = $('<div/>', {
                    'class': 'thumb-reveal-wrapper' + (slide.isHidden ? ' slide-hidden' : ''),
                    'data-slide-id': slide.id
                })
                    .on('click', function() {
                        setActiveSlideItem(slide.id);
                        itemCallback(slide);
                        return false;
                    })
                    .append(
                        $('<div/>', {'class': 'thumb-reveal-inner'})
                            .append(
                                $('<div/>', {
                                    'class': 'thumb-reveal reveal',
                                    'css': {'width': '1280px', 'height': '720px', 'transform': 'scale(0.1298)'}
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
                $element.appendTo(that.$slidesList);
                that.decks[slide.id] = makeReveal($element.find('.reveal')[0]);
            });

            that.$slidesList.sortable({
                over: function(event, ui) {
                    var cl = ui.item.attr('class');
                    $('.ui-state-highlight').addClass(cl);
                },
                placeholder: 'ui-state-highlight',
                //handle: '.slide-move',
                update: function() {
                    that.saveSlidesOrder()
                        .done(function(data) {
                            if (data) {
                                if (data.success) {
                                    toastr.success('Порядок слайдов успешно изменен');
                                    var i = 1;
                                    that.$slidesList.find('div.thumb-reveal-wrapper > .thumb-reveal-options > .slide-number').each(function() {
                                        $(this).text(i);
                                        i++;
                                    });
                                }
                                else {
                                    toastr.error(JSON.stringify(data.errors));
                                }
                            }
                            else {
                                toastr.error('Неизвестная ошибка');
                            }
                        })
                        .fail(function(data) {
                            toastr.error(data.responseJSON.message);
                        });
                }
            }).disableSelection();

            if (toSetActiveSlideID) {
                that.$slidesList.find('div[data-slide-id=' + toSetActiveSlideID + '].thumb-reveal-wrapper').click();
            }
            else {
                that.$slidesList.find('div.thumb-reveal-wrapper:eq(0)').click();
            }
        });
    },
    'loadSlide': function(slideID) {
        return $.getJSON('/admin/index.php', {
            'r': 'editor/load-slide',
            'story_id': this.options['story_id'],
            'slide_id': slideID
        });
    },
    'createSlide': function() {
        return $.getJSON('/admin/index.php', {
            'r': 'editor/slide/create',
            'story_id': this.options['story_id'],
            'current_slide_id': this.getCurrentSlideID()
        });
    },
    'deleteSlide': function() {
        return $.getJSON('/admin/index.php', {
            'r': 'editor/slide/delete',
            'slide_id': this.getCurrentSlideID()
        });
    },
    'copySlide': function() {

        return $.getJSON('/admin/index.php', {
            'r': 'editor/slide/copy',
            'slide_id': this.getCurrentSlideID()
        });
    },
    'toggleVisible': function() {
        return $.getJSON('/admin/index.php', {
            'r': 'editor/slide/toggle-visible',
            'slide_id': this.getCurrentSlideID()
        });
    },
    'updateSlideData': function(id, data) {
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

    this.setLeft = function(element, left) {
        css(element,'left', left);
    };
    this.setTop = function(element, top) {
        css(element, 'top', top);
    };
    this.setWidth = function(element, width) {
        css(element, 'width', width);
    };
    this.setHeight = function(element, height) {
        css(element, 'height', height);
    };
    this.change = function() {
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

    this.left = function(element) {
        setBlockAlign(element, 'left');
    };
    this.right = function(element) {
        setBlockAlign(element, 'right');
    };
    this.top = function(element) {
        setBlockAlign(element, 'top');
    }
    this.bottom = function(element) {
        setBlockAlign(element, 'bottom');
    };
    this.horizontalCenter = function(element) {
        setBlockAlign(element, 'horizontal_center');
    };
    this.verticalCenter = function(element) {
        setBlockAlign(element, 'vertical_center');
    };
    this.slideCenter = function(element) {
        setBlockAlign(element, 'slide_center');
    };
}

function BlockToolbar(options) {
    this.container = $('.blocks-sidebars');
    this.options = options;
}
BlockToolbar.prototype = {
    'create': function() {
        this.toolbar = this.options.createToolbar();
        this.container.find('.blocks-sidebar.visible').removeClass('visible');
        this.container.append(this.toolbar.addClass('visible'));
        var that = this;
        this.container.find('[data-toolbar-action]').each(function() {
            var $elem = $(this);
            $elem.on('click', that.options.actions[$elem.attr('data-toolbar-action')]);
        });
        this.options.onCreate();
    },
    'remove': function() {
        if (!this.toolbar) {
            return;
        }
        this.toolbar.find('[data-toolbar-action]').each(function() {
            $(this).off('click');
        });
        this.toolbar.remove();
        this.container.find('.blocks-sidebar').addClass('visible');
        this.options.onRemove();
    },
    'show': function() {
        this.container.find('.blocks-sidebar').removeClass('hide');
    },
    'hide': function() {
        this.container.find('.blocks-sidebar').addClass('hide');
    }
}

function ContentCleaner(editor) {

    this.editor = editor;

    this.cleanSlideContent = function(save) {
        save = save || false;
        var data = this.editor.find('section').clone();
        $('#save-container').empty().append(data);

        var section = $('#save-container').find('section');
        var attributes = $.map(section[0].attributes, function(item) {
            return item.name;
        });
        $.each(attributes, function(i, item) {
            if ($.inArray(item, ['data-id', 'data-slide-view', 'data-audio-src']) === -1) {
                section.removeAttr(item);
            }
        });

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
            data.find('.sl-block').each(function() {
                var $elem = $(this);
                blockAttrNames.forEach(function(blockAttrName) {
                    $elem.removeAttr(blockAttrName);
                });
                if ($elem.find('.sl-block-placeholder').length) {
                    $elem.find('.sl-block-placeholder').remove();
                }
            });
            data.find('div.new-questions').text('');
            data.find('div.wikids-video-player').text('');
        }
        return data[0].outerHTML;
    }

    this.cleanSlideBlock = function(block) {
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

    this.generate = function() {
        return generateId(10);
    };
}

var StoryEditor = (function() {
    "use strict";

    var $editor = $('#story-editor');

    $editor.on('mousedown', function(e) {
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
        }
        else {
            unsetActiveBlock();
        }
    });

    var imageMoved = false;
    var imageOffset = [0, 0];
    var imageMousePosition = {};

    $editor
        .on('mouseup', function(e) {
            if (imageMoved) {
                blockModifier.change();
            }
            imageMoved = false;
            var $block = $(e.target).parents('div.sl-block:eq(0)');
            $block.removeClass('is-panning');
        });
    $(document).on('mousemove', function(e) {
        if (imageMoved) {
            imageMousePosition = {
                x : e.clientX,
                y : e.clientY
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
        mousedown: function(e) {
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
        mouseenter: function(e) {
            var $wrapper = $('<div/>', {'class': 'sl-block-transform sl-block-transform-hover'})
                .append($('<div/>', {'class': 'sl-block-border'}));
            var $block = $(e.target);
            if (!$block.hasClass('sl-block')) {
                $block = $(e.target).parents('div.sl-block');
            }
            $block.append($wrapper);
        },
        mouseleave: function(e) {
            $(".reveal .slides div.sl-block:not(.wikids-active-block)")
                .find('.sl-block-transform').remove();
        }
    }, 'div.sl-block:not(.wikids-active-block)');

    $editor.on({
        mouseenter: function(e) {
            var $block = $(e.target);
            if (!$block.hasClass('sl-block')) {
                $block = $(e.target).parents('div.sl-block');
            }
            if ($block.attr('data-block-type') === 'image' && blockManager.isCropped($block)) {
                $block.append(createImageBlockControls());
            }
        },
        mouseleave: function() {
            $(".reveal .slides div.sl-block")
                .find('.sl-block-image-controls').remove();
        }
    }, 'div.sl-block');

    $editor.on('dblclick', function(e) {
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
                forcePasteAsPlainText: true,
                disableNativeSpellChecker: false,
                toolbarGroups: [
                    {name: 'styles', groups: ['styles']},
                    {name: 'colors', groups: ['colors']},
                    {name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
                    {name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph']},
                    {name: 'links', groups: ['links']},
                    {name: 'insert', groups: ['insert']}
                ],
                // Font
                removeButtons: 'About,Maximize,ShowBlocks,BGColor,Styles,Image,Flash,Table,Smiley,SpecialChar,PageBreak,Iframe,Anchor,BidiLtr,BidiRtl,Language,Source,Save,NewPage,ExportPdf,Preview,Print,Templates,Cut,Copy,Paste,PasteText,PasteFromWord,Undo,Redo,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Underline,Subscript,Superscript,CopyFormatting,CreateDiv,Indent,Outdent'
            });

            var contentIsChanged = false;
            ed.on('blur', function() {
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
            });

            ed.on('change', function() {
                contentIsChanged = true;
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
        blockIDGenerator;

    /**
     * Инициализация редактора и всех компонентов
     * @param params
     */
    function initialize(params) {

        config = params;

        slideMenu = new SlideMenu($editor);

        slidesManager = new SlideManager({'story_id': params['storyID']});
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

            function createToolbarItemGroup(actions) {
                var $group = $('<li/>', {'class': 'blocks-sidebar-item group'});
                actions.forEach(function(action) {
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

            $list.append(createToolbarItem('Положение', 'align-center','align'));
            $list.append(createToolbarItem('Удалить', 'trash', 'delete'));

            if (!activeBlock.isPlaceholder()) {
                $list.append(createToolbarItem('Копировать', 'duplicate', 'duplicate'));
            }

            return $('<div/>', {'class': 'blocks-sidebar'}).append($list);
        }

        var editorPopover = new EditorPopover();
        blockToolbar = new BlockToolbar({
            createToolbar,
            'onCreate': function() {
                editorPopover.attach('li[data-toolbar-action=align]', {'placement': 'left'},[
                    {'name': 'left', 'title': 'По левому краю', 'click': function() { blockAlignment.left(blockManager.getActive().getElement()); }},
                    {'name': 'right', 'title': 'По правому краю', 'click': function() { blockAlignment.right(blockManager.getActive().getElement()); }},
                    {'name': 'top', 'title': 'По верху', 'click': function() { blockAlignment.top(blockManager.getActive().getElement()); }},
                    {'name': 'bottom', 'title': 'По низу', 'click': function() { blockAlignment.bottom(blockManager.getActive().getElement()); }},
                    {'name': 'horizontal_center', 'title': 'По центру (горизонтально)', 'click': function() { blockAlignment.horizontalCenter(blockManager.getActive().getElement()); }},
                    {'name': 'vertical_center', 'title': 'По центру (вертикально)', 'click': function() { blockAlignment.verticalCenter(blockManager.getActive().getElement()); }},
                    {'name': 'slide_center', 'title': 'По центру слайда', 'click': function() { blockAlignment.slideCenter(blockManager.getActive().getElement()); }}
                ]);
            },
            'onRemove': function() {
                editorPopover.detach('li[data-toolbar-action=align]');
            },
            'actions': {
                'stretch': function() {
                    stretchToSlide();
                },
                'delete': function() {
                    if (!confirm('Удалить блок?')) {
                        return;
                    }
                    deleteBlockAction();
                },
                'duplicate': function() {
                    copyBlockAction();
                },
                'edit': function() {
                    config.onBlockUpdate(blockManager.getActive(), getUpdateBlockUrl(), contentCleaner.cleanSlideBlock(blockManager.getActive().getElement()));
                },
                'natural-size': function() {
                    naturalSize();
                },
                'replace': function() {
                    config.onImageReplace(blockManager.getActive().getID());
                },
                'select-image': function() {
                    config.onImageReplace(blockManager.getActive().getID());
                }
            }
        });

        loadSlides().done(function() {
            config.onReady();
        });

        config.onInit();
    }

    function makeDraggable(element) {
        var containmentArea = $editor.find('section');
        var config = {
            start: function(event) {
                setActiveBlock($(event.target));
            },
            drag: function(event, ui) {
                var zoom = Reveal.getScale();
                var contWidth = containmentArea.width(),
                    contHeight = containmentArea.height();
                ui.position.left = Math.max(0, Math.min(ui.position.left / zoom , contWidth - ui.helper.width()));
                ui.position.top = Math.max(0, Math.min(ui.position.top  / zoom,  contHeight- ui.helper.height()));
            },
            stop: function(event, ui) {
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
                }
                else {
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
            }
            else {
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
        var config = {};
        if (block.typeIsText()) {
            config = {
                handles: 'e, w',
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
        $editor.find("div[data-block-id]").each(function() {
            var $block = $(this);
            $block.removeClass("wikids-active-block");
            if ($block.data('ui-resizable')) {
                $block.resizable('destroy');
            }
        })
        $editor.find('div.sl-block .sl-block-transform').remove();
    }

    var blockManager = (function(editor) {

        function BlockWrapper(element) {
            this.element = element;
        }
        BlockWrapper.prototype = {
            'getType': function() {
                return this.element.attr('data-block-type');
            },
            'getID': function() {
                return this.element.attr('data-block-id');
            },
            'delete': function() {
                dispatchEvent('onBlockDelete', {'block': this});
                this.element.remove();
                this.element = null;
            },
            'typeIsImage': function() {
                return this.getType() === 'image';
            },
            'isPlaceholder': function() {
                return (this.typeIsImage() && !this.getElement().find('img').length);
            },
            'typeIsVideo': function() {
                return this.getType() === 'video' || this.getType() === 'videofile';
            },
            'typeIsHtml': function() {
                return this.getType() === 'html';
            },
            'typeIsText': function() {
                return this.getType() === 'text';
            },
            'getElement': function() {
                return this.element;
            },
            'getWidth': function() {
                return parseInt(this.element.css('width'));
            },
            'getHeight': function() {
                return parseInt(this.element.css('height'));
            }
        }

        var activeBlock = null;

        function extend(a, b) {
            for (var i in b) {
                a[i] = b[i];
            }
            return a;
        }

        function dispatchEvent(type, args) {
            var event = document.createEvent("HTMLEvents", 1, 2);
            event.initEvent(type, true, true);
            extend(event, args);
            editor[0].dispatchEvent(event);
        }

        return {
            'find': function(id) {
                var element = editor.find('section > div.sl-block[data-block-id=' + id + ']');
                return new BlockWrapper(element);
            },
            'append': function(element) {
                editor.find('section').append(element);
                var block = new BlockWrapper(element);
                dispatchEvent('onBlockCreate', {'block': block});
                return block;
            },
            'replace': function(element, placeholderBlockID) {
                var placeholder = this.find(placeholderBlockID).getElement();
                placeholder.replaceWith(element);
                var block = new BlockWrapper(element);
                dispatchEvent('onBlockCreate', {'block': block});
                return block;
            },
            'setActive': function(element) {
                activeBlock = new BlockWrapper(element);
            },
            'getActive': function() {
                return activeBlock;
            },
            'unsetActive': function() {
                activeBlock = null;
            },
            'deleteBlock': function(slideID, data) {
                return $.ajax({
                    url: '/admin/index.php?r=editor/block/delete&slide_id=' + slideID,
                    type: 'POST',
                    data: data,
                    contentType: 'text/html; charset=utf-8',
                    dataType: 'json',
                    processData: false
                });
            },
            'addEventListener': function(type, listener, useCapture) {
                if ('addEventListener' in window) {
                    editor[0].addEventListener(type, listener, useCapture);
                }
            },
            'replaceBlockImage': function(blockID, imageProps) {
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
            'isCropped': function(blockElement) {
                var $img = blockElement.find('img');
                if (!$img.length) {
                    return false;
                }
                return $img.attr('data-crop-x') !== undefined
                       || $img.attr('data-crop-y') !== undefined
                       || $img.attr('data-crop-width') !== undefined
                       || $img.attr('data-crop-height') !== undefined;
            },
            'updateImageAttributes': function(imageElement, imageProps, blockProps) {
                imageElement
                    .attr({
                        'src': imageProps.url,
                        'data-natural-width': imageProps.natural_width,
                        'data-natural-height': imageProps.natural_height
                    });
                this.updateImageSize(imageElement, imageProps.width, imageProps.height)

                var calcInitPosition = function(sizeA, sizeB) {
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
            'updateImagePosition': function(imageElement, left, top) {
                imageElement
                    .css({
                        'left': left + 'px',
                        'top': top + 'px'
                    });
            },
            'updateImageSize': function(imageElement, width, height) {
                imageElement
                    .css({
                        'width': width + 'px',
                        'height': height + 'px'
                    });
            },
            'updateCropImageAttributes': function(imageElement, imageProps, blockProps) {
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

    blockManager.addEventListener('onBlockDelete', function(e) {
        if (e.block.typeIsHtml()) {
            // При удалении блока с тестом, изменить тип слайда с new-question на slide
            slidesManager.getActiveSlide().setSlideView('slide');
        }
    });

    blockManager.addEventListener('onBlockCreate', function(e) {
        if (e.block.typeIsHtml()) {
            // При создании блока с тестом, изменить тип слайда с на new-question
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
        slidesManager.createSlide().done(function(data) {
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
        if (block.typeIsImage() || block.typeIsVideo() || block.typeIsHtml()) {
            blockManager.deleteBlock(slidesManager.getCurrentSlideID(), contentCleaner.cleanSlideBlock(block.getElement()))
                .done(function(response) {
                    deleteBlock(block);
                });
        }
        else {
            deleteBlock(block);
        }
    }

    function copyBlock(block, id) {
        var copyBlock = $(contentCleaner.cleanSlideBlock(block.getElement()));
        copyBlock.css({'left': (50 + parseInt(copyBlock.css('left'))) + 'px', 'top': (50 + parseInt(copyBlock.css('top'))) + 'px'});
        copyBlock.attr('data-block-id', id);
        appendBlock(copyBlock);
        blockModifier.change();
    }

    function copyBlockAction(blockID) {
        var block = blockID ? blockManager.find(blockID) : blockManager.getActive();
        var copyBlockID = blockIDGenerator.generate();
        if (block.typeIsImage() || block.typeIsVideo() || block.typeIsHtml()) {
            $.ajax({
                url: '/admin/index.php?r=editor/block/copy&slide_id=' + slidesManager.getCurrentSlideID() + '&block_id=' + copyBlockID,
                type: 'POST',
                data: contentCleaner.cleanSlideBlock(block.getElement()),
                contentType: 'text/html; charset=utf-8',
                dataType: 'json',
                processData: false
            }).done(function(response) {
                copyBlock(block, copyBlockID);
            });
        }
        else {
            copyBlock(block, copyBlockID);
        }
    }

    /**
     * Загрузка слайда
     * @param slideID
     */
    function loadSlide(slideID) {
        return slidesManager.loadSlide(slideID).done(function(data) {
            $('.slides', $editor).html(data.data);
            Reveal.sync();
            Reveal.slide(0);
            slidesManager.setActiveSlide($editor.find('section'), data);
            slideMenu.init(data);
            makeDraggable($editor.find('.sl-block'));
        }).fail(function(data) {
            $editor.text(JSON.stringify(data));
        });
    }

    function loadSlideWithData(slideData) {
        $('.slides', $editor).html(slideData.data);
        Reveal.sync();
        Reveal.slide(0);
        slidesManager.setActiveSlide($editor.find('section'), slideData);
        slideMenu.init(slideData);
        makeDraggable($editor.find('.sl-block'));
    }

    /**
     * Загрузка списка слайдов
     * @param toSetActiveSlideID
     */
    function loadSlides(toSetActiveSlideID) {
        return slidesManager.loadSlidesList(toSetActiveSlideID, function(slideData) {
            unsetActiveBlock();
            loadSlideWithData(slideData);
        }).done(function(data) {
            if (data.length) {
                blockToolbar.show();
                slideMenu.show();
            }
            else {
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
        return slidesManager.deleteSlide().done(function(data) {
            if (data && data.success) {
                deleteSlide();
                loadSlides();
            }
        });
    }

    function copySlideAction() {
        slidesManager.copySlide().done(function(data) {
            if (data && data.success) {
                loadSlides(data.id);
            }
        }).fail(function() {
            toastr.error('Не удалось скопировать слайд');
        });
    }

    /*function previewContainerSetHeight() {
        var height = parseInt($('.story-container').css('height'));
        $previewContainer.css('height', height + 'px');
    }
    previewContainerSetHeight();*/

    window.addEventListener('resize', function() {
        slideMenu.setPosition();
    });

    /*function getQueryHash() {
        var query = {};
        location.search.replace( /[A-Z0-9]+?=([\w\.%-]*)/gi, function(a) {
            query[ a.split( '=' ).shift() ] = a.split( '=' ).pop();
        } );
        for( var i in query ) {
            var value = query[ i ];
            query[ i ] = deserialize( unescape( value ) );
        }
        return query;
    }*/

    /*function readUrl() {
        var hash = window.location.hash;
        var bits = hash.slice( 2 ).split( '/' ),
            name = hash.replace( /#|\//gi, '' );
        return name;
    }*/

    /*function locationHash() {
        return "#slide=" + currentSlideIndex;
    }*/

    /*function setSlideUrl() {
        window.location.hash = locationHash();
    }*/

    function SlideMenu($ed) {
        "use strict";

        this.$slideMenu = $('.slide-menu');
        this.$slideMenuList = this.$slideMenu.find('ul');

        this.init = function(slideData) {
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

        this.setPosition = function() {
            var slidesRect = $ed.find('.slides')[0].getBoundingClientRect();
            var height = $ed.height(),
                slidesHeight = slidesRect.height,
                top = ((height - slidesHeight) / 2) - this.$slideMenu.height() + 'px';
            var width = $ed.width(),
                slidesWidth = slidesRect.width,
                w = width - slidesWidth;
            if (w < 0) {
                w = slidesWidth;
            }
            else {
                w = w / 2;
            }
            var left = slidesWidth + w - this.$slideMenu.width() + 1 + 'px';
            this.$slideMenu.css({'left': left, 'top': top});
        }

        this.getActionElement = function(name) {
            return this.$slideMenuList.find('li[data-slide-action=' + name + ']');
        }

        this.slideVisibleToggle = function(element, visible) {
            element.data('visible', visible);
            const OPEN = 'glyphicon-eye-open';
            const CLOSE = 'glyphicon-eye-close';
            var $el = element.find('span');
            visible === 1 && $el.removeClass(CLOSE).addClass(OPEN) && element.removeClass('set-in');
            visible === 2 && $el.removeClass(OPEN).addClass(CLOSE) && element.addClass('set-in');
        }

        this.slideVisibleToggleAction = function(visible) {
            this.slideVisibleToggle(this.getActionElement('visible'), visible);
        }

        this.hide = function() {
            this.$slideMenu.addClass('hide');
        }
        this.show = function() {
            this.$slideMenu.removeClass('hide');
        }
    }

    function slideVisibleToggleAction() {
        return slidesManager.toggleVisible().done(function(data) {
            if (data && data.success) {
                slideMenu.slideVisibleToggleAction(data.status);
                slidesManager.toggleSlideVisible(data.status === 2)
            }
            else {
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
                }
                else {
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
        }
        else {
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
        }
        else {
            block = blockManager.append(blockHtml);
        }
        makeDraggable(blockHtml);
        return block;
    }

    function createBlock(blockHtml, placeholderBlockID) {
        var block = appendBlock(blockHtml, placeholderBlockID);
        if (block.typeIsVideo() || block.typeIsHtml()) {
            stretchToSlide(block.getElement());
        }
        else {
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

    function createEmptyBlock(type) {
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

        $('<div/>', {
            'class': 'slide-paragraph',
            'html': '<p>Введите текст</p>'
        })
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


        'getCreateBlockUrl': function(blockType) {
            return '/admin/index.php?r=editor/form-create&slide_id=' + slidesManager.getCurrentSlideID() + '&block_type=' + blockType;
        },
        'createSlideBlock': createBlock,
        'updateSlideBlock': updateBlock,
        "getNormalizedSlideContent": function() {
            return contentCleaner.cleanSlideContent(true);
        },

        'getStoryID': function() {
            return getConfigValue('storyID');
        },
        'getCurrentSlideID': function() {
            return slidesManager.getCurrentSlideID();
        },

        loadSlides,
        loadSlide,

        'getSlidePreviewUrl': function() {
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
        'findBlockByID': function(id) {
            return blockManager.find(id);
        },
        'replaceBlockImage': function(blockID, imageProps) {
            blockManager.replaceBlockImage(blockID, imageProps);
        },

        createImagePlaceholder
    };
})();

/*
(function(editor, $, console) {
    "use strict";

    var $modal = $("#slide-question-modal");

    editor.createSlideQuestion = function() {
        $modal.modal("show");
    };

    editor.addQuestion = function() {
        var questionID = $("#story-question-list").val();
        if (!questionID) {
            $modal.modal("hide");
            return;
        }
        $.getJSON(editor.getConfigValue("createSlideQuestionAction"), {
            "question_id": questionID,
            "current_slide_id": editor.getCurrentSlideID()
        }).done(function(data) {
            editor.loadSlides(data.id);
        });
        $modal.modal("hide");
    };
})(StoryEditor, jQuery, console);*/

/*
(function(editor, $, console) {
    "use strict";

    editor.createQuestions = function(params, callback) {
        params = params || {};
        params.story_id = editor.getStoryID();
        params.after_slide_id = editor.getCurrentSlideID();
        $.getJSON(editor.getConfigValue("createNewSlideQuestionAction"), params).done(function(data) {
            if (typeof callback === 'function') {
                callback();
            }
            editor.loadSlides(data.id);
        });
    };

})(StoryEditor, jQuery, console);*/

/** Blocks */
/*(function(editor, $, console) {
    "use strict";

    editor.newCreateBlock = function() {
        $.getJSON(editor.getConfigValue("newCreateBlockAction"), {
            "slide_id": editor.getCurrentSlideID()
        }).done(function(data) {
            console.log(data);
            //editor.loadSlide(editor.getCurrentSlideID(), true);
        });
    };

})(StoryEditor, jQuery, console);*/

/** Images */
/*
var ImageFromStory = (function(editor, $, console) {
    "use strict";

    var config = {
        addImagesAction: ""
    };

    function extend(a, b) {
        for (var i in b) {
            a[i] = b[i];
        }
        return a;
    }

    function dispatchEvent(type, args) {
        var event = document.createEvent("HTMLEvents", 1, 2);
        event.initEvent(type, true, true);
        extend(event, args);
        document.dispatchEvent(event);
    }

    function changeImageStory(storyID) {
        var promise = $.ajax({
            "url": editor.getConfigValue("storyImagesAction") + "&story_id=" + storyID,
            "type": "GET",
            "dataType": "json"
        });
        promise.done(function(data) {
            dispatchEvent('onChangeStory', {
                'data': data
            });
        });
    }

    function addImages(image) {
        return $.ajax({
            "url": config.addImagesAction + "&slide_id=" + editor.getCurrentSlideID() + "&image=" + image,
            "type": "GET",
            "dataType": "json"
        });
    }

    return {
        'init': function(params) {
            config = params;
        },
        'changeImageStory': changeImageStory,
        'addImages': addImages,
        'addEventListener': function(type, listener, useCapture) {
            if ('addEventListener' in window) {
                document.addEventListener(type, listener, useCapture);
            }
        },
    };
})(StoryEditor, jQuery, console);
*/

/** Collections */
/*
(function(editor, $, console) {
    "use strict";

    var config = {
        setImageAction: "",
        accounts: []
    };

    var $modal = $("#slide-collections-modal");

    var currentPageNumber,
        currentCollectionID,
        currentAccount;

    var $collectionList = $("#collection-list", $modal);
    var $cardList = $("#collection-card-list", $modal);

    var mode = "",
        backupImage = "";

    function drawCollectionCards(collectionID, listID, account) {

        currentCollectionID = collectionID;
        account = account || currentAccount;

        var $tabCardList = $(".collection_card_list", "#" + listID);
        $tabCardList.empty();
        var promise = $.ajax({
            "url": "/admin/index.php?r=yandex/cards" + "&board_id=" + collectionID  + "&account=" + account,
            "type": "GET",
            "dataType": "json"
        });
        promise.done(function(data) {
            if (data && data.results) {
                data.results.forEach(function (card) {
                    var img = $("<img/>").attr("src", card.content[0].source.url);
                    var $item = $('<div class="col-xs-6 col-md-3"><a href="#" class="thumbnail">' + img.prop("outerHTML") + '</a></div>');
                    $item
                        .find("a.thumbnail")
                        .on("click", function(e) {
                            e.preventDefault();
                            addImage(card.content[0].source.url, card.content[0].content.url, account, card.board.id, card.board.title);
                        });
                    $tabCardList.append($item);
                });
            }
            else {
                $tabCardList.append('<div class="col-md-12">Изображения в итории не найдены</div>');
            }
        });
        promise.fail(function(data) {
            var response = data.responseJSON;
            toastr.error(response.message, response.name);
        });
    }

    function drawCollections(collections) {
        var $tabCollectionList = $(".collection_list", "#yandex-collection");
        $tabCollectionList.empty();
        collections.forEach(function(collection) {
            $("<a/>")
                .attr("href", "#")
                .html('<span class="label label-lg label-primary">' + collection.title + '</span> ')
                .css("font-size", "1.7rem")
                .on("click", function (e) {
                    e.preventDefault();
                    drawCollectionCards(collection.id, 'yandex-collection');
                })
                .appendTo($tabCollectionList);
        });
    }

    function drawPagination(reload) {
        reload = reload || false;
        var $pageList = $("#collection-page-list");
        if (reload) {
            $pageList.empty();
        }
        $(".collection_list", "#yandex-collection").empty();
        getCollections()
            .done(function(data) {
                if (data && data.results) {
                    if (!$pageList.find('li').length) {
                        var pageCount = Math.ceil(data.count / 100),
                            pages = [];
                        for (var i = 1; i <= pageCount; i++) {
                            pages.push(i);
                        }
                        pages.forEach(function (page) {
                            $('<li/>')
                                .addClass(page === currentPageNumber ? 'active' : '')
                                .append($('<a/>')
                                    .attr("href", "#")
                                    .text(page)
                                    .on("click", function (e) {
                                        e.preventDefault();
                                        var $link = $(this);
                                        $("li", $pageList).removeClass('active');
                                        $link.parent().addClass('active');
                                        $collectionList.empty();
                                        $cardList.empty();
                                        getCollections(page).done(function (data) {
                                            if (data && data.results) {
                                                drawCollections(data.results, 'yandex-collection');
                                            }
                                        });
                                    }))
                                .appendTo($pageList);
                        });
                        drawCollections(data.results, 'yandex-collection');
                    }
                }
            })
            .fail(function(data) {
                var response = data.responseJSON;
                toastr.error(response.message, response.name);
            });
    }

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var $tab = $(e.target);
        if ($tab.attr("href").substr(1) === 'yandex-collection') {
            if (!currentAccount) {
                currentAccount = config.accounts[0];
            }
            drawPagination();
        }
    });

    $modal.on('show.bs.modal', function() {

        backupImage = $(this).data("backupImage");

        var $tabCollectionList = $(".collection_list", "#story-collection");
        $tabCollectionList.empty();
        var promise = $.ajax({
            "url": "/admin/index.php?r=editor/image/get-used-collections&story_id=" + editor.getConfigValue('storyID'),
            "type": "GET",
            "dataType": "json"
        });
        promise.done(function(data) {
            if (data && data.success) {
                if (!data.result.length) {
                    $("<p/>")
                        .text("В историю не добавлено изображений из коллекций")
                        .appendTo($tabCollectionList);
                }
                else {
                    data.result.forEach(function (collection) {
                        $("<a/>")
                            .attr("href", "#")
                            .html('<span class="label label-lg label-primary">' + collection.collection_name + '</span> ')
                            .css("font-size", "1.7rem")
                            .on("click", function (e) {
                                e.preventDefault();
                                drawCollectionCards(collection.collection_id, 'story-collection', collection.collection_account);
                            })
                            .appendTo($tabCollectionList);
                    });
                }
            }
        });
    });

    editor.initCollectionsModule = function(params) {
        config = params;
    };

    $("a[data-account]", $modal).on("click", function(e) {
        e.preventDefault();
        currentAccount = $(this).attr("data-account");
        drawPagination(true);
    });

    editor.slideCollectionsModal = function() {
        mode = "";
        $modal
            .data("backupImage", "")
            .modal("show");
    };

    editor.slideCollectionsBackupModal = function(imageID) {
        mode = "backup";
        $modal
            .data("backupImage", imageID)
            .modal("show");
    };

    function getCollections(page) {
        page = page || 1;
        currentPageNumber = page;
        return $.get('/admin/index.php?r=yandex/boards&page=' + page + '&account=' + currentAccount);
    }

    function addImage(source_url, content_url, collection_account, collection_id, collection_name) {
        if (mode === "backup") {
            addBackupImage(source_url, content_url, collection_account, collection_id, collection_name);
        }
        else {
            addCollectionImage(source_url, content_url, collection_account, collection_id, collection_name);
        }
    }

    function addCollectionImage(source_url, content_url, collection_account, collection_id, collection_name) {
        var promise = $.ajax({
            "url": "/admin/index.php?r=editor/image/set",
            "type": "POST",
            "data": {
                "slide_id": editor.getCurrentSlideID(),
                "collection_account": collection_account,
                "collection_id": collection_id,
                "collection_name": collection_name,
                "content_url": content_url,
                "source_url": source_url
            },
            "dataType": "json"
        });
        promise.done(function(data) {
            if (data && data.success) {
                $modal.modal("hide");
                data.image['what'] = 'collection';
                ImageCropper.showModal(data.image);
            }
        });
    }

    function addBackupImage(source_url, content_url, collection_account, collection_id, collection_name) {
        var promise = $.ajax({
            "url": "/admin/index.php?r=editor/image/backup&image_id=" + backupImage,
            "type": "POST",
            "data": {
                "slide_id": editor.getCurrentSlideID(),
                "collection_account": collection_account,
                "collection_id": collection_id,
                "collection_name": collection_name,
                "content_url": content_url,
                "source_url": source_url
            },
            "dataType": "json"
        });
        promise.done(function(data) {
            if (data && data.success) {
                $modal.modal("hide");
            }
        });
    }

})(StoryEditor, jQuery, console);
*/

/** Cropper */
/*
var ImageCropper = (function(editor, $) {

    var $modal = $('#image-crop-modal'),
        sourceImage = {},
        aspectRatio = NaN;

    function showModal(image, ratio) {
        sourceImage = image;
        aspectRatio = ratio || EditorImage.aspectRatio();
        $modal.modal('show');
    }

    var cropper;

    $modal.on('shown.bs.modal', function() {

        var $img = $('<img/>').attr('src', sourceImage.url);
        $img.appendTo($('#crop-image-container', this));

        var options = {
            aspectRatio: aspectRatio,
            dragMode: 'none',
            background: false,
            zoomOnWheel: false,
            zoomOnTouch: false,
        };
        cropper = new Cropper($img[0], options);
    });

    $modal.on('hide.bs.modal', function() {
        if (cropper) {
            cropper.destroy();
        }
        $('#crop-image-container', this).find('img').remove();
    });

    // Обрезать и сохранить изображение
    function crop() {
        cropper.getCroppedCanvas().toBlob(function (blob) {
            var formData = new FormData(),
                params = EditorImage.getParams();
            formData.append('slide_id', editor.getCurrentSlideID());
            formData.append('croppedImage', blob);
            formData.append('croppedImageID', sourceImage.id);
            formData.append('what', sourceImage.what);
            formData.append('left', params.left);
            formData.append('top', params.top);
            formData.append('width', params.width);
            formData.append('height', params.height);
            $.ajax('/admin/index.php?r=editor/image/cropper-save', {
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function () {
                    editor.loadSlide(editor.getCurrentSlideID(), true);
                    $modal.modal('hide');
                },
                error: function () {
                    console.log('Upload error');
                }
            });
        });
    }

    // Сохранение изображения без обрезки
    function save() {

        var formData = new FormData();
        formData.append('slide_id', editor.getCurrentSlideID());
        formData.append('imagePath', sourceImage.url);
        formData.append('imageID', sourceImage.id);
        formData.append('what', sourceImage.what);

        $.ajax('/admin/index.php?r=editor/image/save', {
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function () {
                editor.loadSlide(editor.getCurrentSlideID(), true);
                $modal.modal('hide');
            },
            error: function () {
                console.log('Upload error');
            }
        });
    }

    return {
        'showModal': showModal,
        'crop': crop,
        'save': save
    };
})(StoryEditor, jQuery);
*/

/*
var EditorImage = (function($, editor) {

    var element,
        drawingRect,
        selectedRect;

    var canvasOffsetLeft = 0,
        canvasOffsetTop = 0,
        drawStartX = 0,
        drawStartY = 0;

    function getScale() {
        return Reveal.getScale();
    }

    function pointX(x) {
        return (x - canvasOffsetLeft) / getScale();
    }

    function pointY(y) {
        return (y - canvasOffsetTop) / getScale();
    }

    function startDrawRect(e) {

        var offset = element.offset();
        canvasOffsetLeft = offset.left;
        canvasOffsetTop = offset.top;

        drawStartX = pointX(e.pageX);
        drawStartY = pointY(e.pageY);
        drawingRect = createRect(drawStartX, drawStartY, 0, 0);

        element.on('mousemove.wikids', drawRect);
        element.on('mouseup.wikids', endDrawRect);
    }

    function drawRect(e) {
        var currentX = pointX(e.pageX);
        var currentY = pointY(e.pageY);
        var position = calculateRectPos(drawStartX, drawStartY, currentX, currentY);
        drawingRect.css(position);
    }

    function endDrawRect(e) {

        var currentX = pointX(e.pageX);
        var currentY = pointY(e.pageY);
        var position = calculateRectPos(drawStartX, drawStartY, currentX, currentY);
        if (position.width < 10 || position.height < 10) {
            drawingRect.remove();
        }
        else {
            drawingRect.css(position);
            selectRect(drawingRect);
        }
        element.off('mousemove.wikids');
        element.off('mouseup.wikids');
    }

    var params = {};

    function createRect(x, y, w, h) {
        var rect = $('<div/>')
            .addClass('rect')
            .css({
                left: x,
                top: y,
                width: w,
                height: h
            });
        rect.on('click', function() {
            var $el = $(this);
            params = {
                left: parseInt($el.css('left')),
                top: parseInt($el.css('top')),
                width: parseFloat($el.css('width')),
                height: parseFloat($el.css('height'))
            };
            EditorImageDialog.show();
        });
        rect.appendTo(element);
        return rect;
    }

    function selectRect(rect) {
        selectedRect && selectedRect.removeClass('selected');
        selectedRect = rect;
        selectedRect.addClass('selected');
    }

    function calculateRectPos(startX, startY, endX, endY) {
        var width = endX - startX;
        var height = endY - startY;
        var posX = startX;
        var posY = startY;
        if (width < 0) {
            width = Math.abs(width);
            posX -= width;
        }
        if (height < 0) {
            height = Math.abs(height);
            posY -= height;
        }
        return {
            left: posX,
            top: posY,
            width: width,
            height: height
        };
    }

    function init(el) {
        element = el;
        $(document)
            .off('mousedown.wikids')
            .on('mousedown.wikids', element, function(e) {
                var target = e.target;
                if (target.tagName !== 'SECTION') {
                    return;
                }

                $('div.rect', element).remove();

                startDrawRect(e);
            });
    }

    function destroy() {
        $(document).off('mousedown.wikids');
        $('div.rect', element).remove();
    }

    return {
        'init': init,
        'getParams': function() {
            return params;
        },
        'aspectRatio': function() {
            return (params.width / params.height).toFixed(2);
        },
        'destroy': destroy
    };
})(jQuery, StoryEditor);*/

/*
var EditorImageDialog = (function(editor, $, console) {
    "use strict";

    var dialog = $('#create-image-modal');

    $('#image-from-file', dialog).on('click', function() {
        dialog.modal('hide');
        ImageFromFile.show();
    });

    $('#image-from-story', dialog).on('click', function() {
        dialog.modal('hide');
        ImageFromStoryDialog.show();
    });

    $('#image-from-collection', dialog).on('click', function() {
        dialog.modal('hide');
        editor.slideCollectionsModal();
    });

    $('#image-from-url', dialog).on('click', function() {
        dialog.modal('hide');
        imageFromUrlDialog.show();
    });

    return {
        'show': function() {
            dialog.modal('show');
        },
        'hide': function() {
            dialog.modal('hide');
        }
    };
})(StoryEditor, jQuery, console);*/

/*var ImageFromFile = (function() {
    "use strict";

    var dialog = $('#image-from-file-modal');

    dialog.on('show.bs.modal', function() {
        $('form', this).trigger('reset');
    });

    return {
        'show': function() {
            dialog.modal('show');
        },
        'hide': function() {
            dialog.modal('hide');
        }
    };
})();*/

/*
var ImageFromStoryDialog = (function(module, $, editor) {
    "use strict";

    var dialog = $('#image-from-story-modal');

    var $images = $('#story-images-list', dialog);
    module.addEventListener('onChangeStory', function(event) {
        var data = event.data;
        $images.empty();
        if (data && data.length) {
            data.forEach(function (image) {
                var img = $("<img/>").attr("src", image);
                $images.append('<div class="col-xs-6 col-md-3"><a href="#" class="thumbnail">' + img.prop("outerHTML") + '</a></div>');
            });
        }
        else {
            $images.append('<div class="col-md-12">Изображения в итории не найдены</div>');
        }
    });

    $("#story-images-list", dialog).on("click", "a.thumbnail", function(e) {
        e.preventDefault();

        dialog.modal('hide');
        var imageSrc = $("img", this).attr("src");

        var image = {
            'url': imageSrc,
            'id': imageSrc.split('\\').pop().split('/').pop(),
            'what': 'story'
        };
        ImageCropper.showModal(image);
    });

    return {
        'show': function() {
            dialog.modal('show');
        },
        'hide': function() {
            dialog.modal('hide');
        }
    };
})(ImageFromStory, jQuery, StoryEditor);
*/

/*
var EditorImageUploader = (function(editor) {
    "use strict";

    function uploadImageHandler(form) {
        return $.ajax({
            url: form.attr("action"),
            type: form.attr("method"),
            data: new FormData(form[0]),
            cache: false,
            contentType: false,
            processData: false
        });
    }

    return {
        'uploadImageHandler': uploadImageHandler
    };
})(StoryEditor);*/

/*
var SlideImageUploader = (function(uploader, editor, dialog) {
    "use strict";

    function uploadHandler(form) {
        uploader.uploadImageHandler(form).done(function(data) {
            if (data && data.success) {
                dialog.hide();
                data.image['what'] = 'file';
                ImageCropper.showModal(data.image);
            }
        });
    }

    return {
        'uploadHandler': uploadHandler
    };
})(EditorImageUploader, StoryEditor);
*/

/*var StoryImageFromUrl = (function() {

})();*/


/*
function StoryDialog(selector, options) {
    "use strict";

    this.dialog = $(selector);
    this.options = options || {};
    this.dialog.on('show.bs.modal', options.onShow);
    this.dialog.on('shown.bs.modal', options.onShown);

    var that = this;
    this.dialog.find('form').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this),
            $form = $(this);
        options.submit(that, formData, $form.attr('method'), $form.attr('action'));
    });
}

StoryDialog.prototype.show = function() {
    "use strict";

    this.dialog.modal('show');
};

StoryDialog.prototype.hide = function() {
    "use strict";

    this.dialog.modal('hide');
};
*/

/*
var imageFromUrlDialog = new StoryDialog('#image-from-url-modal', {
    'onShow': function() {
        $('input[type=text]:eq(0)', this).val('');
    },
    'onShown': function() {
        $('input[type=text]:eq(0)', this).focus();
    },
    'submit': function(dialog, formData, method, action) {
        var promise = $.ajax({
            'url': action,
            'type': method,
            'data': formData,
            'cache': false,
            'contentType': false,
            'processData': false
        });
        promise.done(function(data) {
            if (data) {
                if (data.success) {
                    dialog.hide();
                    data.image['what'] = 'collection';
                    ImageCropper.showModal(data.image);
                }
                else {
                    toastr.error(JSON.stringify(data.errors));
                }
            }
            else {
                toastr.error('Неизвестная ошибка');
            }
        });
        promise.fail(function(data) {
            toastr.error(data.responseJSON.message);
        });
    }
});*/

