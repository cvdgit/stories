
function DataModifier(action) {

    this.timeout = undefined;
    this.data = {};

    function formatValues(values) {
        var result = [];
        for (var prop in values) {
            if (values.hasOwnProperty(prop)) {
                result.push($.extend({'key': prop}, values[prop]));
            }
        }
        return result;
    }

    this.startTimeout = function() {
        if (this.timeout !== undefined) {
            clearTimeout(this.timeout);
        }
        if (!this.data) {
            return;
        }
        var that = this;
        this.timeout = setTimeout(function() {
            action(formatValues(that.data));
            that.data = {};
        }, 2000);
    }
}

DataModifier.prototype.add = function(key, values) {
    this.data[key] = $.extend(true, this.data[key], values);
    this.startTimeout();
};

DataModifier.prototype.clearTimeout = function() {
    if (this.timeout === undefined) {
        return;
    }
    clearTimeout(this.timeout);
}

var StoryEditor = (function() {
    "use strict";

    var $editor = $('#story-editor');
    var $previewContainer = $('#preview-container');
    var $formContainer = $("#form-container");

    $editor.on('click', 'div.sl-block', function(e) {
        var currentBlockID = $(this).attr('data-block-id');
        setActiveBlock(currentBlockID, currentBlockID === activeBlockID);
    });

    $editor.on({
        mouseenter: function(e) {
            var $wrapper = $('<div/>', {'class': 'sl-block-transform sl-block-transform-hover'})
                .append($('<div/>', {'class': 'sl-block-border'}));
            $(e.target).parents('div.sl-block').append($wrapper);
        },
        mouseleave: function(e) {
            $(e.target).parents('div.sl-block').find('div.sl-block-transform-hover').remove();
        }
    }, 'div.sl-block:not(.wikids-active-block)');

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

    var currentSlideIndex = 0,
        currentSlideID,
        activeBlockID = null;

    function initialize(params) {
        config = params;
        loadSlides(readUrl() || -1);
    }

    function send(slideID) {
        var part = [
            'slide_id=' + slideID
        ];
        return $.ajax({
            url: config.getSlideAction + '&' + part.join('&'),
            type: 'GET',
            dataType: 'json'
        });
    }

    var $list = $("#slide-block-list");

    function loadSlideBlocks() {
        var promise = $.ajax({
            "url": config.getSlideBlocksAction + "&slide_id=" + currentSlideID,
            "type": "GET",
            "dataType": "json"
        });
        $("#slide-blocks").show();
        promise.done(function(data) {
            $list.empty();
            if (data.length > 0) {
                data.forEach(function (block) {
                    var elem = $("<a>")
                        .attr("href", "#")
                        .addClass("list-group-item")
                        .text(block.type)
                        .attr("data-block-id", block.id);
                    elem.on("click", function (e) {
                        e.preventDefault();
                        setActiveBlock(block.id);
                    });
                    var deleteElem = $('<span/>')
                        .addClass('glyphicon glyphicon-trash')
                        .css({'float': 'right', 'color': 'red', 'fontWeight': '500'})
                        .attr({'title': 'Удалить блок'});
                    deleteElem.on('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        deleteBlock(block.id);
                    });
                    elem.append(deleteElem);
                    elem.appendTo($list);
                });
                //setActiveBlock(activeBlockID || $list.find("a").attr("data-block-id"));
            }
            else {
                $("#slide-block-params").hide();
            }
        });
    }

    function selectActiveBlock(blockID) {
        $(".reveal .slides div[data-block-id]").removeClass("wikids-active-block");
        $(".reveal .slides div.sl-block").find('.sl-block-transform').remove();
        var $wrapper = $('<div/>', {'class': 'sl-block-transform'})
            .append($('<div/>', {'class': 'sl-block-border-active'}));
        $(".reveal .slides").find("div[data-block-id=" + blockID + "]")
            .addClass("wikids-active-block")
            .append($wrapper);
    }

    function setActiveBlock(blockID, doNotLoadForm) {
        activeBlockID = blockID;
        doNotLoadForm = doNotLoadForm || false;
        $("a", $list).removeClass("active");
        $("a[data-block-id=" + blockID + "]", $list).addClass("active");
        selectActiveBlock(blockID);
        if (!doNotLoadForm) {
            loadBlockForm(blockID);
        }
    }

    function loadBlockForm(blockID) {
        var promise = $.ajax({
            "url": config.getBlockFormAction + "&slide_id=" + currentSlideID + "&block_id=" + blockID,
            "type": "GET",
            "dataType": "json"
        });
        $("#slide-block-params").show();
        promise.done(function(data) {
            $formContainer.html(data);
        });
    }

    function createBlock(type) {
        var promise = $.ajax({
            "url": config.createBlockAction + "&slide_id=" + currentSlideID + "&block_type=" + type,
            "type": "GET",
            "dataType": "json"
        });
        promise.done(function(response) {
            if (response && response.success) {
                activeBlockID = response.block_id;
                loadSlide(currentSlideID, true);
            }
        });
    }

    function createSlide() {
        $.getJSON(config.createSlideAction, {"current_slide_id": currentSlideID})
            .done(function(data) {
                loadSlides(data.id);
            });
    }

    function deleteBlock(blockID) {
        if (!confirm("Удалить блок?")) {
            return;
        }
        var promise = $.ajax({
            "url": config.deleteBlockAction + "&slide_id=" + currentSlideID + "&block_id=" + blockID,
            "type": "GET",
            "dataType": "json"
        });
        promise.done(function() {
            var deleteCurrentBlock = (blockID === activeBlockID);
            if (deleteCurrentBlock) {
                activeBlockID = null;
            }
            loadSlide(currentSlideID, true);
        });
    }

    function setActiveSlide(slide) {
        currentSlideID = slide.id;
        currentSlideIndex = slide.number - 1;
        $("[data-slide-id]", $previewContainer).each(function() {
            $(this).removeClass("active");
        });
        $("[data-slide-id=" + slide.id + "]", $previewContainer).addClass("active");
        setSlideUrl();
    }

    function updateLinkCounter(count) {
        $("#slide-links").text("Ссылки" + (count > 0 ? " (" + count + ")" : ""));
    }

    function updateBlock(data) {
        $.ajax({
            url: '/admin/index.php?r=editor/blocks/save',
            type: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            processData: false
        }).done(function(response) {
            if (response && response.success) {
                toastr.success('Изменения успешно сохранены');
            }
        });
    }
    var modifier = new DataModifier(updateBlock);

    function loadSlide(slideID, loadBlocks) {

        loadBlocks = loadBlocks || false;
        currentSlideID = slideID;
        var click = {
            x: 0,
            y: 0
        };

        EditorActions.reset();

        function getContainment($box, $drag, space) {
            var x1 = $box.offset().left + space;
            var y1 = $box.offset().top + space;
            var x2 = $box.offset().left + $box.width() - $drag.width() - space;
            var y2 = $box.offset().top + $box.height() - $drag.height() - space;
            return [x1, y1, x2, y2];
        }

        send(slideID)
            .done(function(data) {

                setActiveSlide(data);
                updateLinkCounter(data.blockNumber);

                $(".slides", $editor).empty().append(data.data);

/*                if (activeBlockID !== null && !loadBlocks) {
                    setActiveBlock(activeBlockID, true);
                }*/

                $('section', '#story-editor')
                    .css({'height': '720px', 'width': '1280px'})
                    .attr('id', 'slide-container');

                $(".sl-block", ".reveal").draggable({
                    start: function(event) {
                        var blockID = $(event.target).attr("data-block-id");
                        setActiveBlock(blockID, blockID === activeBlockID);
                        click.x = event.clientX;
                        click.y = event.clientY;
                    },
                    drag: function(event, ui) {
                        var zoom = Reveal.getScale();
                        var original = ui.originalPosition;
                        ui.position = {
                            left: (event.clientX - click.x + original.left) / zoom,
                            top:  (event.clientY - click.y + original.top ) / zoom
                        };
                    },
                    stop: function(event, ui) {
                        setFormTop(Math.round(ui.position.top) + "px", $(event.target));
                        setFormLeft(Math.round(ui.position.left) + "px", $(event.target));
                    }
                });
                $(".sl-block", ".reveal").resizable({
                    handles: 'all',
                    aspectRatio: true,
                    start: function(event) {
                        setActiveBlock($(event.target).attr("data-block-id"));
                    },
                    resize: function(event, ui) {
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
                    },
                    stop: function(event, ui) {
                        setFormWidth(Math.round(ui.size.width) + "px", $(event.target));
                        setFormHeight(Math.round(ui.size.height) + "px", $(event.target));
                    }
                });
                Reveal.sync();
                Reveal.slide(0);
                if (loadBlocks) {
                    loadSlideBlocks();
                }
                WikidsVideo.reset();
                WikidsVideo.createPlayer();
            })
            .fail(function(data) {
                $editor.text(data);
            });
    }

    function deleteSlide(slideID) {
        if (!confirm("Удалить слайд?")) {
            return;
        }
        var promise = $.ajax({
            "url": config.deleteSlideAction + "&slide_id=" + slideID,
            "type": "GET",
            "dataType": "json"
        });
        promise.done(function(data) {
            if (data && data.success) {
                loadSlides(-1);
            }
        });
    }

    function slideIcon(slide) {
        return slide.isLink ?
            '<i class="glyphicon glyphicon-link"></i>' :
            slide.isQuestion ?
                '<i class="glyphicon glyphicon-question-sign"></i>' :
                '#';
    }

    function loadSlides(activeSlideID) {
        var $container = $("#preview-container");
        $container.empty();
        var promise = $.ajax({
            "url": config.currentSlidesAction,
            "type": "GET",
            "dataType": "json"
        });
        promise.done(function(data) {
            if (data.length > 0) {
                data.forEach(function (slide) {

                    var $element = $("<a/>")
                        .attr("href", "#")
                        .addClass("list-group-item")
                        .attr("data-slide-id", slide.id)
                        .attr("data-link-slide-id", slide.linkSlideID)
                        .html(slideIcon(slide) + " слайд " + slide.slideNumber)
                        .on("click", function () {
                            activeBlockID = null;
                            currentSlideID = null;
                            loadSlide(slide.id, true);
                            return false;
                        });

                    $('<span/>')
                        .addClass('delete-slide glyphicon glyphicon-trash')
                        .attr({'title': 'Удалить слайд'})
                        .on('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            deleteSlide(slide.id);
                        })
                        .appendTo($element);

                    $('<span/>')
                        .addClass('toggle-slide-visible glyphicon glyphicon-eye-' + (slide.isHidden ? 'close' : 'open'))
                        .attr('title', (slide.isHidden ? 'Показать' : 'Скрыть') + ' слайд')
                        .on('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            var that = this;
                            toggleSlideVisible(slide.id)
                                .done(function(data) {
                                    if (data && data.success) {
                                        changeSlideVisibleIcon($(that), data.status);
                                    }
                                });
                        })
                        .appendTo($element);

                    $element.appendTo($container);
                });
                loadSlide(activeSlideID, true);
                $container.sortable();
            }
            else {
                $("<span/>").text("Нет слайдов").appendTo($container);
                $("#slide-blocks").hide();
                $("#slide-block-params").hide();
            }
        });
    }

    function onBeforeSubmit() {
        var $form = $(this),
            button = $("button[type=submit]", $form);
        button.button("loading");

        modifier.clearTimeout();

        $.ajax({
            url: $form.attr("action"),
            type: $form.attr("method"),
            data: new FormData($form[0]),
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                loadSlide(currentSlideID);
            },
            error: function(data) {
                console.log(data);
            }
        }).always(function() {
            button.button("reset");
        });
    }

    //init();

    function previewContainerSetHeight() {
        var height = parseInt($('.story-container').css('height'));
        $previewContainer.css('height', height + 'px');
    }

    previewContainerSetHeight();
    window.addEventListener('resize', previewContainerSetHeight);

    function getQueryHash() {
        var query = {};
        location.search.replace( /[A-Z0-9]+?=([\w\.%-]*)/gi, function(a) {
            query[ a.split( '=' ).shift() ] = a.split( '=' ).pop();
        } );
        for( var i in query ) {
            var value = query[ i ];
            query[ i ] = deserialize( unescape( value ) );
        }
        return query;
    }

    function readUrl() {
        var hash = window.location.hash;
        var bits = hash.slice( 2 ).split( '/' ),
            name = hash.replace( /#|\//gi, '' );
        return name;
    }

    function locationHash() {
        return "#slide=" + currentSlideIndex;
    }

    function setSlideUrl() {
        window.location.hash = locationHash();
    }

    function toggleSlideVisible(slideID) {
        slideID = slideID || currentSlideID;
        return $.ajax({
            "url": config.slideVisibleAction + "&slide_id=" + slideID,
            "type": "GET",
            "dataType": "json"
        });
    }

    function changeSlideVisibleIcon(element, status) {
        element.removeClass('glyphicon-eye-open glyphicon-eye-close');
        if (status === 1) {
            element.addClass('glyphicon-eye-open');
        }
        else {
            element.addClass('glyphicon-eye-close');
        }
    }

    function slideSourceModal(url) {
        $("#slide-source-modal").modal({"remote": url + "&slide_id=" + currentSlideID});
    }

    function getConfigValue(value) {
        return config[value];
    }

    function saveSlidesOrder() {
        var formData = new FormData();
        formData.append('SlidesOrder[story_id]', config.storyID);
        $('#preview-container a').each(function(i) {
            formData.append('SlidesOrder[slides][' + i + ']', $(this).attr('data-slide-id'));
            formData.append('SlidesOrder[order][' + i + ']', ++i);
        });
        var promise = $.ajax({
            'url': '/admin/index.php?r=slide/save-order',
            'type': 'POST',
            'data': formData,
            cache: false,
            contentType: false,
            processData: false
        });
        promise.done(function(data) {
            if (data) {
                if (data.success) {
                    toastr.success('Порядок слайдов успешно изменен');
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

    function findBlockElement(blockID) {
        return $editor.find('section > div.sl-block[data-block-id=' + blockID + ']');
    }

    function setBlockAlign(align, blockID) {
        var element = findBlockElement(blockID === undefined ? activeBlockID : blockID);
        switch (align) {
            case 'left':
                setFormLeft('0px', element);
                break;
            case 'right':
                setFormLeft(1280 - parseInt(element.css('width')) + 'px', element);
                break;
            case 'top':
                setFormTop('0px', element);
                break;
            case 'bottom':
                setFormTop(720 - parseInt(element.css('height')) + 'px', element);
                break;
            case 'horizontal_center':
                setFormLeft((1280 - parseInt(element.css('width'))) / 2 + 'px', element);
                break;
            case 'vertical_center':
                setFormTop((720 - parseInt(element.css('height'))) / 2 + 'px', element);
                break;
            case 'slide_center':
                setFormLeft((1280 - parseInt(element.css('width'))) / 2 + 'px', element);
                setFormTop((720 - parseInt(element.css('height'))) / 2 + 'px', element);
                break;
        }
    }

    function getBlockForm() {
        return $formContainer.find('form:eq(0)');
    }

    function getModifyKey(blockElement) {
        return currentSlideID + ':' + blockElement.attr('data-block-id');
    }

    function setFormLeft(value, element) {
        getBlockForm().find('input.editor-left').val(value);
        if (element) {
            element.css('left', value);
            modifier.add(getModifyKey(element), {'left': value});
        }
    }

    function setFormTop(value, element) {
        getBlockForm().find('input.editor-top').val(value);
        if (element) {
            element.css('top', value);
            modifier.add(getModifyKey(element), {'top': value});
        }
    }

    function setFormWidth(value, element) {
        getBlockForm().find('input.editor-width').val(value);
        if (element) {
            element.css('width', value);
            modifier.add(getModifyKey(element), {'width': value});
        }
    }

    function setFormHeight(value, element) {
        getBlockForm().find('input.editor-height').val(value);
        if (element) {
            element.css('height', value);
            modifier.add(getModifyKey(element), {'height': value});
        }
    }

    return {
        "initialize": initialize,
        "loadSlides": loadSlides,
        "loadSlide": loadSlide,
        "onBeforeSubmit": onBeforeSubmit,
        "getCurrentSlideIndex": function() {
            return currentSlideIndex;
        },
        "getCurrentSlideID": function() {
            return currentSlideID;
        },
        "readUrl": readUrl,
        "setSlideUrl": setSlideUrl,
        "createBlock": createBlock,
        "deleteBlock": deleteBlock,
        "deleteSlide": deleteSlide,
        "createSlide": createSlide,
        "slideSourceModal": slideSourceModal,
        "getConfigValue": getConfigValue,
        "getStoryID": function() {
            return getConfigValue('storyID');
        },
        "saveSlidesOrder": saveSlidesOrder,
        "setBlockAlignLeft": function() {
            setBlockAlign('left');
        },
        "setBlockAlignRight": function() {
            setBlockAlign('right');
        },
        "setBlockAlignTop": function() {
            setBlockAlign('top');
        },
        "setBlockAlignBottom": function() {
            setBlockAlign('bottom');
        },
        "setBlockAlignHorizontalCenter": function() {
            setBlockAlign('horizontal_center');
        },
        "setBlockAlignVerticalCenter": function() {
            setBlockAlign('vertical_center');
        },
        "setBlockAlignSlideCenter": function() {
            setBlockAlign('slide_center');
        },
        "stretchToSlide": function() {
            if (activeBlockID === null) {
                return;
            }
            var element = findBlockElement(activeBlockID);
            setFormLeft('0px', element);
            setFormTop('0px', element);
            setFormWidth('1280px', element);
            setFormHeight('720px', element);
        },
        "stretchToWidth": function() {

        },
        "stretchToHeight": function() {

        }
    };
})();



(function(editor, $, console) {
    "use strict";

    editor.createSlideLink = function() {
        $("#slide-link-modal").modal("show");
    };

    editor.changeStory = function(obj, listID, defaultValue) {
        var $slides = $("#" + listID);
        $slides.empty();
        var storyID = $(obj).val();
        if (storyID) {
            var promise = $.ajax({
                "url": editor.getConfigValue("slidesAction") + "&story_id=" + storyID,
                "type": "GET",
                "dataType": "json"
            });
            promise.done(function (data) {
                data.forEach(function (slide) {
                    var $option = $("<option />")
                        .val(slide.id)
                        .text("Слайд " + slide.slideNumber + (slide.isHidden ? ' (скрытый)' : ''));
                    if (slide.id === defaultValue) {
                        $option.attr("selected", "selected");
                    }
                    $option.appendTo($slides);
                });
            });
        }
    };

    editor.link = function() {
        var promise = $.ajax({
            "url": editor.getConfigValue("createSlideLinkAction") + "&link_slide_id=" + $("#story-link-slides").val() + '&current_slide_id=' + editor.getCurrentSlideID(),
            "type": "GET",
            "dataType": "json"
        });
        promise.done(function(data) {
            if (data.success) {
                editor.loadSlides(data.id);
            }
            else {
                toastr.error(data.error);
            }
            $("#slide-link-modal").modal("hide");
        });
    };

})(StoryEditor, jQuery, console);

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
})(StoryEditor, jQuery, console);

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

})(StoryEditor, jQuery, console);

(function(editor, $, console) {
    "use strict";

    editor.copySlide = function() {
        $.getJSON(editor.getConfigValue("copySlideAction"), {
            "slide_id": editor.getCurrentSlideID()
        }).done(function(data) {
            editor.loadSlides(data.id);
        });
    };

})(StoryEditor, jQuery, console);

/** Blocks */
(function(editor, $, console) {
    "use strict";

    editor.newCreateBlock = function() {
        $.getJSON(editor.getConfigValue("newCreateBlockAction"), {
            "slide_id": editor.getCurrentSlideID()
        }).done(function(data) {
            console.log(data);
            //editor.loadSlide(editor.getCurrentSlideID(), true);
        });
    };

})(StoryEditor, jQuery, console);

/** Youtube block */
(function(editor, $, console) {
    "use strict";

    editor.createVideoBlock = function() {

    };

})(StoryEditor, jQuery, console);

/** Images */
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

/** Collections */
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

/** Cropper */
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

    /** Обрезать и сохранить изображение */
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

    /** Сохранение изображения без обрезки */
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

/** Story Images */
(function(editor, $, console) {
    "use strict";

    var $modal = $("#story-images-modal");

    function elementWrapper() {
        return $('<div class="media">' +
                    '<div class="media-left"></div>' +
                    '<div class="media-body">' +
                        '<a href="#" class="add-backup-image">Добавить резервное изображение</a>' +
                        '<br>' +
                        '<a href="#" class="backup-images">Резервные изображения <span></span></a>' +
                        '<br>' +
                        '<a href="#" class="delete-image">Удалить из истории</a>' +
                    '</div>' +
                '</div>');
    }

    function backupImages(imageID) {
        editor.slideCollectionsBackupModal(imageID);
    }

    function deleteImageFromStory(imageID, slideID, blockID, elem) {
        return $.ajax({
            "url": "/admin/index.php?r=editor/image/delete-from-story&image_id=" + imageID + '&slide_id=' + slideID + '&block_id=' + blockID,
            "type": "GET",
            "dataType": "json"
        });
    }

    $modal.on('show.bs.modal', function() {
        var $imagesList = $(".story-images-list", this);
        $imagesList.empty();
        var promise = $.ajax({
            "url": "/admin/index.php?r=editor/image/get-images&story_id=" + editor.getConfigValue('storyID'),
            "type": "GET",
            "dataType": "json"
        });
        promise.done(function(data) {
            if (data && data.success) {
                if (!data.result.length) {
                }
                else {
                    data.result.forEach(function(image) {
                        var $img = $('<img/>')
                            .attr("src", "/image/view?id=" + image.hash)
                            .attr("width", 200);
                        elementWrapper()
                            .find(".media-left").append($img).end()
                            .find(".add-backup-image").on("click", function(e) {
                                e.preventDefault();
                                backupImages(image.id);
                            }).end()
                            .find(".backup-images").attr("href", "/admin/index.php?r=editor/image/update&id=" + image.id).end()
                            .find(".backup-images span").text(" (" + image.link_image_count + ")").end()
                            .find(".delete-image").on("click", function(e) {
                                e.preventDefault();
                                if (!confirm("Удалить изображение?")) {
                                    return;
                                }
                                var $link = $(this);
                                deleteImageFromStory(image.id, image.slide_id, image.block_id)
                                    .done(function(data) {
                                        if (data && data.success) {
                                            $link.parent().parent().remove();
                                            editor.loadSlide(editor.getCurrentSlideID(), true);
                                        }
                                    });
                            }).end()
                            .appendTo($imagesList);
                    });
                }
            }
        });
    });

})(StoryEditor, jQuery, console);

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
})(jQuery, StoryEditor);

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
})(StoryEditor, jQuery, console);

var ImageFromFile = (function() {
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
})();

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
})(StoryEditor);

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
})(EditorImageUploader, StoryEditor, ImageFromFile);

var StoryImageFromUrl = (function() {

})();

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
});

