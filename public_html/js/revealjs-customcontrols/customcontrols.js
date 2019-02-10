
var RevealCustomControls = window.RevealCustomControls || (function() {
	//
	var config = Reveal.getConfig().customcontrols;

	//var reveal = document.querySelector(".reveal");

	var $controls = $('<div/>');
	$controls.addClass('customcontrols');
	
	for (var $control, $icon, elem, i = 0; i < config.controls.length; i++ ) {

		elem = config.controls[i];
		$control = $('<button/>');
		if (elem.className && elem.className.length) {
			$control.addClass(elem.className);
		}
		$control.addClass('enabled');
		$control.append(elem.icon);
		$control.on('click', elem.action);

		$controls.append($control);
	}
	
	$('.story-controls').append($controls);

	Reveal.addEventListener('ready', function(event) {

		Reveal.getConfig().customcontrols.controlsCallback(event);
	});

	Reveal.addEventListener('slidechanged', function(event) {

		Reveal.getConfig().customcontrols.controlsCallback(event);
	});

	return this;

})();
