

var Wikids = (function() {

	"use strict"

	function getRevealConfig()
	{
		return StoryRevealConfig;
	}

	return {
		getRevealConfig: getRevealConfig
	};
})()

var WikidsStoryFeedback = (function() {

	var config = Wikids.getRevealConfig().feedbackConfig;

    function send(data) {
        return $.ajax({
			url: config.action,
			type: 'POST',
			dataType: 'json',
			data: data
        });
    }

	function sendFeedback() {
		var data = {
			'slide_number': (Reveal.getIndices().h + 1)
		}
		send(data)
		  	.done(function(response) {
				if (response.success) {
					alert('Спасибо!');
				}
			});
	}

	return {
		sendFeedback: sendFeedback
	}
})();

var WikidsPlayer = (function() {

	var $playerContainer = $('.reveal-container');

	function enterFullscreen()
	{
		var element = $playerContainer[0];
		var requestMethod = element.requestFullscreen ||
							element.webkitRequestFullscreen ||
							element.webkitRequestFullScreen ||
							element.mozRequestFullScreen ||
							element.msRequestFullscreen;
		if (requestMethod) {
			requestMethod.apply(element);
		}
	}

	function closeFullscreen()
	{
		var element = document;
		var requestMethod = element.exitFullscreen ||
		                    element.exitFullScreen ||
							element.mozCancelFullScreen ||
							element.webkitCancelFullscreen ||
							element.webkitCancelFullScreen ||
							element.msExitFullscreen;
		if (requestMethod) {
			requestMethod.apply(element);
		}
	}

	function inFullscreen()
	{
		return (window.screenTop && window.screenY);
	}

	function toggleFullscreen()
	{
		if (inFullscreen()) {
			closeFullscreen();
	  	} else {
	  		enterFullscreen();
	  	}
	}

	/*
	function changePlayerContainerHeight()
	{
		if (inFullscreen()) {
			$playerContainer.css('height', '100%');
		}
		else {
			var containerWidth = $playerContainer[0].offsetWidth;
			$playerContainer.css('height', (containerWidth * 0.5) + 'px');
		}
	}

	window.onresize = changePlayerContainerHeight;
	$(function() {
		$(window).resize();
	});
	*/

	return {
		toggleFullscreen: toggleFullscreen,
		inFullscreen: inFullscreen
	}
})()