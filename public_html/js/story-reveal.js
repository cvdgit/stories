
function storyEnterFullscreen() {
	var element = document.documentElement;
	var requestMethod = element.requestFullscreen ||
						element.webkitRequestFullscreen ||
						element.webkitRequestFullScreen ||
						element.mozRequestFullScreen ||
						element.msRequestFullscreen;
	if (requestMethod) {
		requestMethod.apply(element);
	}
}

function storyCloseFullscreen() {
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

function storyInFullscreen() {
	return (window.screenTop && window.screenY);
}

function storyToggleFullscreen() {
	if (storyInFullscreen()) {
		storyCloseFullscreen();
  	} else {
  		storyEnterFullscreen();
  	}
}

Reveal.initialize({
	
	width: 1920,
	height: 1080,

	//minScale: 1.0,
	//maxScale: 0.6,
	//margin: 0.1,

	transition: "none",
	backgroundTransition: "slide",
	center: true,
	controls: false,
	controlsLayout: 'bottom-right', // edges
	controlsBackArrows: 'faded',
	progress: true,
	history: true,
	mouseWheel: false,
	showNotes: true,
	slideNumber: true,
	autoSlide: false,
	autoSlideStoppable: true,
	shuffle: false,
	loop: false,
	hash: true,
	hashOneBasedIndex: true,
	customcontrols: {
		controls: [
			{
				icon: '<div class="custom-controls-arrow"><i class="fas fa-arrows-alt"></i></div>',
				action: function() {
					storyToggleFullscreen();
					var $el = $(this).find('i');
					$el.removeClass('fa-arrows-alt')
					   .removeClass('fa-expand-arrows-alt');
					storyInFullscreen() ? $el.addClass('fa-arrows-alt') : $el.addClass('fa-expand-arrows-alt');
				},
				className: 'custom-fullscreen'
			},
			{
				icon: '<div class="custom-controls-arrow"><i class="fas fa-chevron-left"></i></div>', 
				action: function() {
					Reveal.prev();
				},
				className: 'custom-navigate-left'
			},
			{
				icon: '<div class="custom-controls-arrow"><i class="fas fa-chevron-right"></i></div>', 
				action: function() {
					Reveal.next();
				},
				className: 'custom-navigate-right'
			}
		],
		controlsCallback: function(ev) {
			var $left = $('.custom-navigate-left', $('.reveal'));
			Reveal.getProgress() === 0 ? $left.attr('disabled', 'disabled') : $left.removeAttr('disabled');
			var $right = $('.custom-navigate-right', $('.reveal'));
			Reveal.getProgress() === 1 ? $right.attr('disabled', 'disabled') : $right.removeAttr('disabled');
		}
	},
	rtl: false,
    dependencies: [
    //    { src: 'js/reveal-plugins/markdown/marked.js', condition: function() { return !!document.querySelector( '[data-markdown]' ); } },
    //    { src: 'js/reveal-plugins/markdown/markdown.js', condition: function() { return !!document.querySelector( '[data-markdown]' ); } },
    //    { src: 'js/reveal-plugins/highlight/highlight.js', async: true, callback: function() { hljs.initHighlighting(); hljs.initHighlightingOnLoad(); } },
    //    { src: 'js/reveal-plugins/notes/notes.js', async: true, condition: function() { return !!document.body.classList; } },
    //    { src: 'js/reveal-plugins/zoom/zoom.js', async: true }
        {src: '/js/revealjs-customcontrols/customcontrols.js'},
        {src: '/js/story-reveal-statistics.js'}
    ]
});

function onSlideMouseDown(e) {
	Reveal.next();
}
Reveal.addEventListener("mousedown", onSlideMouseDown, false);

Reveal.addEventListener('ready', function(event) {
	console.log(Reveal.getQueryHash());
});

Reveal.addEventListener('slidechanged', function(event) {
console.log(window.location.hash);
});
