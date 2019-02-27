
var StoryRevealFeedback = window.StoryRevealFeedback || (function() {

	var $element = $('.story-controls');
	var config = Reveal.getConfig().feedbackConfig;

	var $link = $('<a>').attr('href', '#').css({color: 'white'}).text('Нашли опечатку на слайде?');
	var $info = $('<span>').css({color: 'white', marginLeft: '10px'});
	var $linkWrapper = $('<div>').css({
	    	position: 'absolute',
	    	top: '17px',
	    	left: '20px'
	    });
	$link.on('click', function(e) {
		e.preventDefault();
		$.ajax({
			url: config.action,
			type: 'POST',
			dataType: 'json',
			data: {
				'slide_number': (Reveal.getIndices().h + 1)
			},
			success: function(response) {
				if (response.success) {
					$info.text('Спасибо!');
				}
			}
		});
	});

	Reveal.addEventListener('slidechanged', function(event) {
		$info.text('');
	});

	$link.appendTo($linkWrapper);
	$info.appendTo($linkWrapper);
	$linkWrapper.appendTo($element);
})();
