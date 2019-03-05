
var StoryEditor = (function() {

	var $editor = $('#story-editor');
	
	var config = {
		storyID: ''
	}

	var currentSlideIndex;

	function initialize(params) {
		config = params;
	}

    function send(index) {
    	var part = [
    		'story_id=' + config.storyID,
    		'slide_index=' + index
    	];
        return $.ajax({
            url: '/admin/index.php?r=editor/get-slide-by-index&' + part.join('&'),
            type: 'GET',
            dataType: 'json'
        });
    }

    function init() {
    	Reveal.initialize({
			width: 1280,
			height: 720,
			margin: 0.01,
			//minScale: 1,
			//maxScale: 0.6,
			center: true
    	});
    }

	function loadSlide(index) {
		var $el = $('#' + config.textFieldID);
		send(index)
			.done(function(data) {
				
				$('.slides', $editor).empty().append(data.html);
				$el.val(data.story.text);
				
				Reveal.sync();
				Reveal.slide(0);

				currentSlideIndex = index;
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

	    var formData = $form.serialize();
	    
	    $.ajax({
	        url: $form.attr("action"),
	        type: $form.attr("method"),
	        data: formData,
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

	return {
		initialize: initialize,
		loadSlide: loadSlide,
		onBeforeSubmit: onBeforeSubmit,
		getCurrentSlideIndex: function() {
			return currentSlideIndex;
		}
	}
})();
