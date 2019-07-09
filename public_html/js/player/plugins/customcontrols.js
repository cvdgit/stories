
var RevealCustomControls = window.RevealCustomControls || (function() {
	//
	var config = Reveal.getConfig().customControls;

	//var reveal = document.querySelector(".reveal");

	var $controls = $('<div/>');
	$controls.addClass('customcontrols');

	$.each(config.controls, function(i, control) {
		var $button = $('<button/>'),
			$icon = $('<i/>').addClass(control.icon);
		$button
		    .addClass('enabled')
		    .addClass(control.className)
		    .attr('title', control.title)
		    .on('click', control.action)
		    .append($icon.wrap('<div class="controls-arrow"></div>'))
		    .appendTo($controls);
	});

	$('.story-controls').append($controls);

	var $controlsWrapper = $('<div/>');
	$controlsWrapper.addClass('story-controls');

	$controlsWrapper.append($controls);
	$controlsWrapper.appendTo('.reveal');

	var callback = Reveal.getConfig().customcontrols;
	if (typeof callback === 'function') {
		Reveal.addEventListener('ready', callback);
		Reveal.addEventListener('slidechanged', callback);
	}

	/*var timer;
	Reveal.addEventListener("mousemove", function(e) {
		e = e || window.event;
		$('.story-controls').fadeIn().addClass('show');
		try {
			clearTimeout(timer);
		} catch (e) {}
		timer = setTimeout(function () {
			if (!$(e.target).hasClass('story-controls') && !$(e.target).parents('.story-controls').length) {
				$('.story-controls').fadeOut().removeClass('show');
			}
		}, 3000);
	}, false);*/

	return this;
})();
