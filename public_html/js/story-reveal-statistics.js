
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

	Reveal.addEventListener('ready', function(event) {
		start_time_ts = toUnixTS(event.timeStamp);
	});

	Reveal.addEventListener('slidechanged', function(event) {
		var tsA = start_time_ts,
		    tsB = toUnixTS(event.timeStamp);
		var data = {
			slide_number: event.indexh,
			begin_time: tsA,
			end_time: tsB,
			chars: event.currentSlide.innerText.length,
			session: session
		};
		send(data);
		start_time_ts = tsB;
	});

	return this;
})();