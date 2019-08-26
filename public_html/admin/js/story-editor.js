
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
        deleteBlockAction: "",
        deleteSlideAction: "",
        currentSlidesAction: "",
        slideVisibleAction: "",
        createSlideAction: "",
        slidesAction: ""
    };

    var currentSlideIndex,
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
                        .data("block-id", block.id);
                    elem.on("click", function (e) {
                        e.preventDefault();
                        setActiveBlock(elem);
                    });
                    elem.appendTo($list);
                });
                setActiveBlock($list.find("a").get(0));
            }
            else {
                $("#slide-block-params").hide();
            }
        });
    }

    function setActiveBlock(elem) {
        $("a", $list).removeClass("active");
        $(elem).addClass("active");
        loadBlockForm($(elem).data("block-id"));
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
        var promise = $.ajax({
            "url": config.deleteBlockAction + "&slide_id=" + currentSlideID + "&block_id=" + blockID,
            "type": "GET",
            "dataType": "json"
        });
        promise.done(function() {
            loadSlide(currentSlideID, true);
        });
    }

    function setActiveSlide(slideID) {
        currentSlideID = slideID;
        $("[data-slide-id]", $previewContainer).each(function() {
            $(this).removeClass("active");
        });
        $("[data-slide-id=" + slideID + "]", $previewContainer).addClass("active");
        setSlideUrl();
    }

    function loadSlide(slideID, loadBlocks) {
        loadBlocks = loadBlocks || false;
        currentSlideID = slideID;
        send(slideID)
            .done(function(data) {
                setActiveSlide(data.id);
                changeSlideVisibleIcon(data.status);
                $(".slides", $editor).empty().append(data.data);
                Reveal.sync();
                Reveal.slide(0);
                if (loadBlocks) {
                    loadSlideBlocks();
                }
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
        return "/" + currentSlideID;
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

    editor.changeStory = function(obj) {
        var $slides = $("#story-link-slides");
        $slides.empty();
        var promise = $.ajax({
            "url": editor.getConfigValue("slidesAction") + "&story_id=" + $(obj).val(),
            "type": "GET",
            "dataType": "json"
        });
        promise.done(function(data) {
            data.forEach(function(slide) {
                $("<option />")
                    .val(slide.id)
                    .text("Слайд" + slide.slideNumber)
                    .appendTo($slides);
            });
        });
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