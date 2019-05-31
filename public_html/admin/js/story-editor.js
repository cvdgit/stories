
var StoryEditor = (function() {

    var $editor = $('#story-editor');
    var $previewContainer = $('#preview-container');

    var config = {
        storyID: "",
        getSlideBlocksAction: "",
        getBlockFormAction: "",
        createBlockAction: "",
        deleteBlockAction: ""
    };

    var currentSlideIndex;

    function initialize(params) {
        config = params;
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

    function loadSlide(index, loadBlocks) {

        loadBlocks = loadBlocks || false;
        currentSlideIndex = index;

        $("[data-slide-index]", $previewContainer).each(function() {
            $(this).removeClass("active");
        });
        $("[data-slide-index=" + index + "]", $previewContainer).addClass("active");

        send(index)
            .done(function(data) {

                $(".slides", $editor).empty().append(data.html);
                Reveal.sync();
                Reveal.slide(0);

                if (loadBlocks) {
                    loadSlideBlocks();
                }

                setSlideUrl();
            })
            .fail(function(data) {
                $editor.text(data);
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

    return {
        "initialize": initialize,
        "loadSlide": loadSlide,
        "onBeforeSubmit": onBeforeSubmit,
        "getCurrentSlideIndex": function() {
            return currentSlideIndex;
        },
        "readUrl": readUrl,
        "setSlideUrl": setSlideUrl,
        "createBlock": createBlock,
        "deleteBlock": deleteBlock
    };
})();
