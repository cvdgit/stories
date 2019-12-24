
var StoryEditor = (function() {
    "use strict";

    var $editor = $('#story-editor');
    var $previewContainer = $('#preview-container');

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
        currentSlideID;

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

    function init() {
        console.log("StoryEditor.init");
        Reveal.initialize(WikidsRevealConfig);
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
                    elem.appendTo($list);
                });
                setActiveBlock($list.find("a").attr("data-block-id"));
            }
            else {
                $("#slide-block-params").hide();
            }
        });
    }

    function selectActiveBlock(blockID) {
        $(".reveal .slides div[data-block-id]").removeClass("wikids-active-block");
        $(".reveal .slides").find("div[data-block-id=" + blockID + "]").addClass("wikids-active-block");
    }

    function setActiveBlock(blockID) {
        $("a", $list).removeClass("active");
        $("a[data-block-id=" + blockID + "]", $list).addClass("active");
        selectActiveBlock(blockID);
        loadBlockForm(blockID);
    }

    function loadBlockForm(blockID) {
        var promise = $.ajax({
            "url": config.getBlockFormAction + "&slide_id=" + currentSlideID + "&block_id=" + blockID,
            "type": "GET",
            "dataType": "json"
        });
        var $formContainer = $("#form-container");
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
        promise.done(function() {
            loadSlide(currentSlideID, true);
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

    function loadSlide(slideID, loadBlocks) {

        loadBlocks = loadBlocks || false;
        currentSlideID = slideID;

        var click = {
            x: 0,
            y: 0
        };

        send(slideID)
            .done(function(data) {
                setActiveSlide(data);
                changeSlideVisibleIcon(data.status);
                updateLinkCounter(data.blockNumber);
                $(".slides", $editor).empty().append(data.data);
                Reveal.sync();
                Reveal.slide(0);
                $(".sl-block", ".reveal").draggable({
                    start: function(event) {
                        setActiveBlock($(event.target).attr("data-block-id"));
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
                        $("input.editor-top").val(Math.round(ui.position.top) + "px");
                        $("input.editor-left").val(Math.round(ui.position.left) + "px");
                    }
                });
                if (loadBlocks) {
                    loadSlideBlocks();
                }
                WikidsVideo.createPlayer();
            })
            .fail(function(data) {
                $editor.text(data);
            });
    }

    function deleteSlide() {
        if (!confirm("Удалить слайд?")) {
            return;
        }
        var promise = $.ajax({
            "url": config.deleteSlideAction + "&slide_id=" + currentSlideID,
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
        return slide.isLink ? '<i class="glyphicon glyphicon-link"></i>' : slide.isQuestion ? '<i class="glyphicon glyphicon-question-sign"></i>' : '#';
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
                    $("<a/>")
                        .attr("href", "#")
                        .addClass("list-group-item")
                        .attr("data-slide-id", slide.id)
                        .attr("data-link-slide-id", slide.linkSlideID)
                        .html(slideIcon(slide) + " слайд " + slide.slideNumber)
                        .on("click", function () {
                            loadSlide(slide.id, true);
                            return false;
                        })
                        .appendTo($container);
                });
                loadSlide(activeSlideID, true);
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

    init();

    function previewContainerSetHeight() {
        var height = parseInt($('.story-container').css('height')) - 40;
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

    function toggleSlideVisible() {
        var promise = $.ajax({
            "url": config.slideVisibleAction + "&slide_id=" + currentSlideID,
            "type": "GET",
            "dataType": "json"
        });
        promise.done(function(data) {
            if (data && data.success) {
                changeSlideVisibleIcon(data.status);
            }
        });
    }

    var $slideVisibleControl = $("#slide-visible");
    function changeSlideVisibleIcon(status) {
        var $icon = $("i", $slideVisibleControl);
        $icon
            .removeClass("glyphicon-eye-open")
            .removeClass("glyphicon-eye-close");
        if (status === 1) {
            $icon.addClass("glyphicon-eye-open");
        }
        else {
            $icon.addClass("glyphicon-eye-close");
        }
    }

    function slideSourceModal(url) {
        $("#slide-source-modal").modal({"remote": url + "&slide_id=" + currentSlideID});
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
        "toggleSlideVisible": toggleSlideVisible,
        "createSlide": createSlide,
        "slideSourceModal": slideSourceModal,
        "getConfigValue": function(value) {
            return config[value];
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
                        .text("Слайд" + slide.slideNumber);
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
(function(editor, $, console) {
    "use strict";

    var config = {
        addImagesAction: ""
    };

    var $modal = $("#slide-images-modal");

    editor.initImagesModule = function(params) {
        config = params;
    };

    editor.slideImagesModal = function() {
        $modal.modal("show");
    };

    editor.changeImageStory = function(obj) {
        var $images = $("#story-images-list");
        $images.empty();
        var promise = $.ajax({
            "url": editor.getConfigValue("storyImagesAction") + "&story_id=" + $(obj).val(),
            "type": "GET",
            "dataType": "json"
        });
        promise.done(function(data) {
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
    };

    editor.addImages = function(image) {
        var promise = $.ajax({
            "url": config.addImagesAction + "&slide_id=" + editor.getCurrentSlideID() + "&image=" + image,
            "type": "GET",
            "dataType": "json"
        });
        promise.done(function(data) {
            if (data && data.success) {
                $modal.modal("hide");
                editor.loadSlide(editor.getCurrentSlideID(), true);
            }
        });
    };

})(StoryEditor, jQuery, console);

/** Collections */
(function(editor, $, console) {
    "use strict";

    var config = {
        addImagesAction: ""
    };

    var $modal = $("#slide-collections-modal");

    $modal.on('show.bs.modal', function() {
        var $select = $('#collections-select', this);
        $select.empty();
        $select.append($('<option/>').val('').text('Выбрать коллекцию'));
        getCollections().done(function(data) {
            if (data && data.results) {
                data.results.forEach(function(collection) {
                    $select.append($('<option/>').val(collection.id).text(collection.title));
                });
            }
        });
    });

    editor.initCollectionsModule = function(params) {
        config = params;
    };

    editor.slideCollectionsModal = function() {
        $modal.modal("show");
    };

    function getCollections() {
        return $.get('/admin/index.php?r=yandex/boards');
    }

    editor.changeCollection = function(obj) {
        var $images = $("#story-images-list", $modal);
        $images.empty();
        var promise = $.ajax({
            "url": "/admin/index.php?r=yandex/cards" + "&board_id=" + $(obj).val(),
            "type": "GET",
            "dataType": "json"
        });
        promise.done(function(data) {
            if (data && data.results) {
                data.results.forEach(function (card) {
                    var img = $("<img/>").attr("src", card.content[0].source.url);
                    $images.append('<div class="col-xs-6 col-md-3"><a href="#" class="thumbnail">' + img.prop("outerHTML") + '</a></div>');
                });
            }
            else {
                $images.append('<div class="col-md-12">Изображения в итории не найдены</div>');
            }
        });
    };

    editor.addCollectionImage = function(image) {
        var promise = $.ajax({
            "url": "/admin/index.php?r=editor/image/set&&slide_id=" + editor.getCurrentSlideID() + "&url=" + image,
            "type": "GET",
            "dataType": "json"
        });
        promise.done(function(data) {
            if (data && data.success) {
                $modal.modal("hide");
                editor.loadSlide(editor.getCurrentSlideID(), true);
            }
        });
    };

})(StoryEditor, jQuery, console);