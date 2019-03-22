
var RevealCustomControls = window.RevealCustomControls || (function() {
	//
	var config = Reveal.getConfig().customcontrols;

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

	return this;

})();
