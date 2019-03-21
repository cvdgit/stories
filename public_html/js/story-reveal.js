
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
					WikidsPlayer.toggleFullscreen();
					var $el = $(this).find('i');
					$el.removeClass('fa-arrows-alt')
					   .removeClass('fa-expand-arrows-alt');
					WikidsPlayer.inFullscreen() ? $el.addClass('fa-arrows-alt') : $el.addClass('fa-expand-arrows-alt');
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
	}
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





/*
function changeRevealContainerHeight() {
	var $container = $('.reveal-container');
	if (storyInFullscreen()) {
		$container.css('height', '100%');
	}
	else {
		var containerWidth = $container[0].offsetWidth;
		$container.css('height', (containerWidth * 0.5) + 'px');
	}
}

window.onresize = changeRevealContainerHeight;

$(function() {
	$(window).resize();
})
*/
