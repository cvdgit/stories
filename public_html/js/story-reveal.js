
function storyEnterFullscreen() {
	
	var element = $('.reveal-container')[0];
	
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


var WikidsStoryFeedback = (function() {

	var config = StoryRevealConfig.feedbackConfig;

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

var RevealConfig = {
	customcontrols: {
		controls: [
			{
				icon: 'far fa-comment',
				className: 'custom-feedback',
				title: 'Сообщить об опечатке на слайде',
				action: function() {
					WikidsStoryFeedback.sendFeedback();
				}
			},
			{
				icon: 'fas fa-arrows-alt',
				className: 'custom-fullscreen',
				title: 'Полноэкранный режим',
				action: function() {
					storyToggleFullscreen();
					var $el = $(this).find('i');
					$el.removeClass('fa-arrows-alt')
					   .removeClass('fa-expand-arrows-alt');
					storyInFullscreen() ? $el.addClass('fa-arrows-alt') : $el.addClass('fa-expand-arrows-alt');
				}
			},
			{
				icon: 'fas fa-chevron-left', 
				className: 'custom-navigate-left',
				title: 'Назад',
				action: function() {
					Reveal.prev();
				}
			},
			{
				icon: 'fas fa-chevron-right', 
				className: 'custom-navigate-right',
				title: 'Вперед',
				action: function() {
					Reveal.next();
				}
			}
		],
		controlsCallback: function(ev) {
			var $left = $('.custom-navigate-left', $('.reveal'));
			Reveal.getProgress() === 0 ? $left.attr('disabled', 'disabled') : $left.removeAttr('disabled');
			var $right = $('.custom-navigate-right', $('.reveal'));
			Reveal.getProgress() === 1 ? $right.attr('disabled', 'disabled') : $right.removeAttr('disabled');
		}
	},
    dependencies: [
    //    { src: 'js/reveal-plugins/markdown/marked.js', condition: function() { return !!document.querySelector( '[data-markdown]' ); } },
    //    { src: 'js/reveal-plugins/markdown/markdown.js', condition: function() { return !!document.querySelector( '[data-markdown]' ); } },
    //    { src: 'js/reveal-plugins/highlight/highlight.js', async: true, callback: function() { hljs.initHighlighting(); hljs.initHighlightingOnLoad(); } },
    //    { src: 'js/reveal-plugins/notes/notes.js', async: true, condition: function() { return !!document.body.classList; } },
    //    { src: 'js/reveal-plugins/zoom/zoom.js', async: true }
        {src: '/js/revealjs-customcontrols/customcontrols.js'},
        {src: '/js/revealjs-customcontrols/customcontrols.css'},
        {src: '/js/story-reveal-statistics.js'}
        // {src: '/js/story-reveal-feedback.js'}
    ]
};

$.extend(StoryRevealConfig, RevealConfig);


Reveal.initialize(StoryRevealConfig);

//function onSlideMouseDown(e) {
//	Reveal.next();
//}
//Reveal.addEventListener("mousedown", onSlideMouseDown, false);

/*
Reveal.addEventListener("mouseover", function() {
	console.log('123');
}, false);
*/


function changeRevealContainerHeight() {
	var $container = $('.reveal-container');
	if (storyInFullscreen()) {
		$container.css('height', '100%');
	}
	else {
		var containerWidth = $container[0].offsetWidth;
		$container .css('height', (containerWidth * 0.5) + 'px');
	}
}

window.onresize = changeRevealContainerHeight;

$(function() {
	$(window).resize();
})
