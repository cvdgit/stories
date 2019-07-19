
StoryEditor = (function() {

    var $editor = $('#story-editor');
    var $previewContainer = $('#preview-container');

    var config = {
        storyID: "",
        getSlideBlocksAction: "",
        getBlockFormAction: "",
        createBlockAction: "",
        deleteBlockAction: "",
        deleteSlideAction: "",
        slidesAction: "",
        slideVisibleAction: ""
    };

    var currentSlideIndex;

    function initialize(params) {
        config = params;
        loadSlides(readUrl() || -1);
    }

    function send(index) {
        var part = [
            'slide_index=' + index
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
            "url": config.getSlideBlocksAction + "&slide_index=" + currentSlideIndex,
            "type": "GET",
            "dataType": "json"
        });
        promise.done(function(data) {
            $list.empty();
            data.forEach(function(block) {
                var elem = $("<a>")
                    .attr("href", "#")
                    .addClass("list-group-item")
                    .text(block.type)
                    .data("block-id", block.id);
                elem.on("click", function(e) {
                    e.preventDefault();
                    setActiveBlock(elem);
                });
                elem.appendTo($list);
            });
            setActiveBlock($list.find("a").get(0));
        });
    }

    function setActiveBlock(elem) {
        $("a", $list).removeClass("active");
        $(elem).addClass("active");
        loadBlockForm($(elem).data("block-id"));
    }

    function loadBlockForm(blockID) {
        var promise = $.ajax({
            "url": config.getBlockFormAction + "&slide_index=" + currentSlideIndex + "&block_id=" + blockID,
            "type": "GET",
            "dataType": "json"
        });
        var $formContainer = $("#form-container");
        promise.done(function(data) {
            $formContainer.html(data);
        });
    }

    function createBlock(type) {
        var promise = $.ajax({
            "url": config.createBlockAction + "&slide_index=" + currentSlideIndex + "&block_type=" + type,
            "type": "GET",
            "dataType": "json"
        });
        promise.done(function() {
            loadSlide(currentSlideIndex, true);
        });
    }

    function deleteBlock(blockID) {
        var promise = $.ajax({
            "url": config.deleteBlockAction + "&slide_index=" + currentSlideIndex + "&block_id=" + blockID,
            "type": "GET",
            "dataType": "json"
        });
        promise.done(function() {
            loadSlide(currentSlideIndex, true);
        });
    }

    function setActiveSlide(number) {
        currentSlideIndex = number;
        $("[data-slide-index]", $previewContainer).each(function() {
            $(this).removeClass("active");
        });
        $("[data-slide-index=" + number + "]", $previewContainer).addClass("active");
        setSlideUrl();
    }

    function loadSlide(index, loadBlocks) {
        loadBlocks = loadBlocks || false;
        currentSlideIndex = index;
        send(index)
            .done(function(data) {
                setActiveSlide(data.number);
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

    function deleteSlide(index) {
        if (!confirm("Удалить слайд?")) {
            return;
        }
        var promise = $.ajax({
            "url": config.deleteSlideAction + "&slide_index=" + index,
            "type": "GET",
            "dataType": "json"
        });
        promise.done(function(data) {
            if (data && data.success) {
                loadSlides(index === currentSlideIndex ? -1 : currentSlideIndex);
            }
        });
    }

    function loadSlides(activeSlideIndex) {
        var $container = $("#preview-container");
        $container.empty();
        var promise = $.ajax({
            "url": config.slidesAction,
            "type": "GET",
            "dataType": "json"
        });
        promise.done(function(data) {
            data.forEach(function(slide) {
                var elem = $("<div/>");
                var slideIndex = slide.slideNumber;
                elem.addClass("img-thumbnail preview-container-item");
                elem.attr("data-slide-index", slideIndex);
                $("<a/>")
                    .attr("href", "#")
                    .text("Слайд " + slideIndex)
                    .on("click", function() {
                        loadSlide(slideIndex, true);
                        return false;
                    })
                    .appendTo(elem);
                $("<a/>")
                    .attr("href", "#")
                    .attr("title", "Удалить слайд")
                    .html("&times;")
                    .addClass("remove-slide")
                    .appendTo(elem);
                elem.appendTo($container);
            });
            loadSlide(activeSlideIndex, true);
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
                loadSlide(currentSlideIndex);
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
        return "/" + currentSlideIndex;
    }

    function setSlideUrl() {
        window.location.hash = locationHash();
    }

    function toggleSlideVisible() {
        var promise = $.ajax({
            "url": config.slideVisibleAction + "&slide_index=" + currentSlideIndex,
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

    return {
        "initialize": initialize,
        "loadSlides": loadSlides,
        "loadSlide": loadSlide,
        "onBeforeSubmit": onBeforeSubmit,
        "getCurrentSlideIndex": function() {
            return currentSlideIndex;
        },
        "readUrl": readUrl,
        "setSlideUrl": setSlideUrl,
        "createBlock": createBlock,
        "deleteBlock": deleteBlock,
        "deleteSlide": deleteSlide,
        "toggleSlideVisible": toggleSlideVisible
    };
})();
