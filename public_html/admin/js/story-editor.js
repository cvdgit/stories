
var StoryEditor = (function() {

	var $editor = $('#story-editor');
	var $previewContainer = $('#preview-container');
	
	var config = {
		storyID: ''
	}

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

	function loadSlide(index) {

		currentSlideIndex = index;
		window.location.hash = locationHash();

		$('[data-slide-index]', $previewContainer).each(function() {
			$(this).removeClass('active');
		});
		$('[data-slide-index=' + index + ']', $previewContainer).addClass('active');

		var $el = $('#' + config.textFieldID);

		$('#' + config.fileFieldID).val('');

		send(index)
			.done(function(data) {
				
				$('.slides', $editor).empty().append(data.html);
				
				$el.val(data.story.text);
				$('#' + config.textSizeFieldID).val(data.story.text_size);
				
				Reveal.sync();
				Reveal.slide(0);
			})
			.fail(function(data) {
				$editor.text(data);
			});
	}

	function onBeforeSubmit() {
		
		var $form = $(this),
	        button = $('button[type=submit]', $form);
	    
	    var $input = $('input#form_slide_index', $form);
	    if (!$input.length) {
	    	$input = $('<input/>').attr({type: 'hidden', id: 'form_slide_index', name: 'SlideEditorForm[slide_index]'});
	    }

	    $input
	      .val(currentSlideIndex)
	      .appendTo($form);

	    button.button('loading');

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
	    	button.button('reset');
	    });
	}

	init();

	function previewContainerSetHeight() {
		var height = (window.innerHeight - 100) + 'px';
		$previewContainer.css('height', height);
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
		return '/' + currentSlideIndex;
	}

	return {
		initialize: initialize,
		loadSlide: loadSlide,
		onBeforeSubmit: onBeforeSubmit,
		getCurrentSlideIndex: function() {
			return currentSlideIndex;
		},
		readUrl: readUrl
	}
})();
