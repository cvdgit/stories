
var StoryRevealStatistics = window.StoryRevealStatistics || (function() {

	function makeSessionID() {
		var text = "";
		var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		for (var i = 0; i < 32; i++) {
			text += possible.charAt(Math.floor(Math.random() * possible.length));
		}
		return text;
	}

	var config = Reveal.getConfig().statisticsConfig;
	
	var start_time_ts = 0,
	    session = makeSessionID();

	function send(data) {
		$.ajax({
			url: config.action,
			type: 'POST',
			dataType: 'json',
			data: data
		});
	}

	function toUnixTS(ts) {
		return (new Date(ts).getTime() / 1000).toFixed(0);
	}

	function getStatistics(ev) {
		var stat = {};
		if (ev.previousSlide) {
			stat = {
				story_id: config.story_id,
				slide_number: ev.indexh,
				begin_time: start_time_ts,
				end_time: toUnixTS(ev.timeStamp),
				chars: ev.previousSlide.innerText.split(' ').length,
				session: session,
				slide_id: $(ev.previousSlide).attr("data-id"),
				study_task_id: config.study_task_id,
				last_slide: Reveal.isLastSlide() ? 1 : 0
			};
		}
		return stat;
	}

	function sendStatistics(ev) {
		var data = getStatistics(ev);
		if (Object.keys(data).length) {
			send(data);
			start_time_ts = data.end_time;
		}
	}

	function slideChangeEvent(ev) {
		var ts = toUnixTS(ev.timeStamp);
		if (ts > 0 && ev.indexh > 0) {
			sendStatistics(ev);
		}
	}
	Reveal.addEventListener('slidechanged', slideChangeEvent);

})();
