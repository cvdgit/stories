// *************************************************************************//
// ! This is main JS file that contains custom scripts used in this template*/
// *************************************************************************//
/**
	Navigation File

	01. Carousel
	02. Calendar
	03. Back to top
	04. Mobile Menu
	05. Video Blog
	06. lazyYT
	07. List And Grid Product
	08. Slider Range
	09. Thumbnail Slider
	10. Quantity product
	11. Grid Product
	12. Filte/Sortable Product
	13. Vertival Slider
	14. Memu Resize
	15. Search
	16. Custom Select
	17. Ask Block
	18. Audio Player
	19. Basic Count Down to Date and Time
	20. Google map
 */

$(function() {
	"use strict";

	// **********************************************************************//
	// ! 01. Carousel
	// **********************************************************************//
	$('.base-slider').owlCarousel({
		loop: true,
		margin: 0,
		nav: false,
		dots: true,
		lazyLoad:true,
		item: 1,
		responsive:{
			0:{
				items:1
			},
			600:{
				items:1
			},
			1000:{
				items:1
			}
		}
	});
	$('.gallery').owlCarousel({
		loop: true,
		margin: 0,
		nav: true,
		dots: false,
		lazyLoad:true,
		item: 1,
		responsive:{
			0:{
				items:1
			},
			600:{
				items:1
			},
			1000:{
				items:1
			}
		}
	});
	$('.slider-product, .similar-products').owlCarousel({
		loop: true,
		margin: 30,
		nav: false,
		dots: true,
		lazyLoad:true,
		item: 4,
		responsive:{
			0:{
				items:1
			},
			600:{
				items:2
			},
			1000:{
				items:4
			}
		}
	});

	// **********************************************************************//
	// ! 02. Calendar
	// **********************************************************************//
	/*
	$( "#datepicker" ).datepicker({
		dayNamesMin: [ "mon", "tue", "wed", "thu", "fri", "sat", "sun" ]
	});
	*/


	// **********************************************************************//
	// ! 03. Back to top
	// **********************************************************************//
	var back_to_top = $('#back-to-top');
	if (back_to_top.length) {
		var scrollTrigger = 100,
		backToTop = function () {
				var scrollTop;
				scrollTop = $(window).scrollTop();
				if (scrollTop > scrollTrigger) {
					back_to_top.addClass('show');
				} else {
					back_to_top.removeClass('show');
				}
			};
		backToTop();
		$(window).on('scroll', function () {
			backToTop();
		});
		back_to_top.on('click', function (e) {
			e.preventDefault();
			$('html,body').animate({
				scrollTop: 0
			}, 700);
		});
	}


	// **********************************************************************//
	// ! 04. Mobile Menu
	// **********************************************************************//
	var menu = $('.menu');
	$('.menu-button').on('click', function(){
		menu.toggle();
		$(this).toggleClass('active');
		menu.parent().toggleClass('mobile-menu');
	});

	$('.mega-menu-top a, .children a').on('click', function(){
		$(this).parent().find('.mega-menu, .sub-menu').toggleClass('active');
	});

	// **********************************************************************//
	// ! 05. Video Blog
	// **********************************************************************//
	var picture = $('.wp-picture');

	picture.on('click', function(){
		$(this).parent('.video-post').find('.images-video').hide();
	});
	picture.on('click', function(){
		$(this).parent('.youtube-post').find('.images-video').hide();
	});


	// **********************************************************************//
	// ! 06. lazyYT
	// **********************************************************************//
	$('.lazyYT').lazyYT();


	// **********************************************************************//
	// ! 07. List And Grid Product
	// **********************************************************************//
	$('.switch span').click(function(){
		$(this).addClass("active").siblings().removeClass("active");
		$('.content-product .product').toggleClass('list-product');
		$('.content-product.grid').toggleClass('list-product-top');
		$(".filtr-container").toggleClass('list-product-width');
	});


	// **********************************************************************//
	// ! 08. Slider Range
	// **********************************************************************//
	/*
	var range =  $('#slider-range');

	range.slider({
		range: true,
		min: 0,
		max: 500,
		values: [ 75, 300 ],
		slide: function( event, ui ) {
			$( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
		}
	});
	$( "#amount" ).val( "$" + range.slider( "values", 0 ) + " - $" + range.slider( "values", 1 ) );
	*/

	// **********************************************************************//
	// ! 09. Thumbnail Slider
	// **********************************************************************//
	var sync1 = $(".product-slider-shop");
	var sync2 = $(".thumbnail");
	var slidesPerPage = 4;	//globaly define number of elements per page
	var syncedSecondary = true;

	sync1.owlCarousel({
		items : 1,
		slideSpeed : 2000,
		nav: false,
		autoplay: true,
		dots: false,
		loop: true,
		responsiveRefreshRate : 200
	}).on('changed.owl.carousel', syncPosition);

	sync2
	.on('initialized.owl.carousel', function () {
		sync2.find(".owl-item").eq(0).addClass("current");
	})
	.owlCarousel({
		items : slidesPerPage,
		dots: false,
		nav: false,
		margin: 10,
		smartSpeed: 200,
		slideSpeed : 500,
		slideBy: slidesPerPage, //alternatively you can slide by 1, this way the active slide will stick to the first item in the second carousel
		responsiveRefreshRate : 100
	}).on('changed.owl.carousel', syncPosition2);

	function syncPosition(el) {
		//if you set loop to false, you have to restore this next line
		//var current = el.item.index;

		//if you disable loop you have to comment this block
		var count = el.item.count-1;
		var current = Math.round(el.item.index - (el.item.count/2) - .5);
		if(current < 0) {
			current = count;
		}
		if(current > count) {
			current = 0;
		}

		//end block

		sync2
			.find(".owl-item")
			.removeClass("current")
			.eq(current)
			.addClass("current");
		var onscreen = sync2.find('.owl-item.active').length - 1;
		var start = sync2.find('.owl-item.active').first().index();
		var end = sync2.find('.owl-item.active').last().index();

		if (current > end) {
			sync2.data('owl.carousel').to(current, 100, true);
		}
		if (current < start) {
			sync2.data('owl.carousel').to(current - onscreen, 100, true);
		}
	}
	function syncPosition2(el) {
		if(syncedSecondary) {
			var number = el.item.index;
			sync1.data('owl.carousel').to(number, 100, true);
		}
	}
	sync2.on("click", ".owl-item", function(e){
		e.preventDefault();
		var number = $(this).index();
		sync1.data('owl.carousel').to(number, 300, true);
	});


	// **********************************************************************//
	// ! 10. Quantity product
	// **********************************************************************//
	var quantity = '.quantity';

	$(quantity).on('click', '.minus', function(){
		var $input = $(this).parent().find('input');
		var count = parseInt($input.val(),10) - 1;
		count = count < 1 ? 1 : count;
		$input.val(count);
		$input.change();
		return false;
	});
	$(quantity).on('click', '.plus', function(){
		var $input = $(this).parent().find('input');
		$input.val(parseInt($input.val(),10) + 1);
		$input.change();
		return false;
	});

	// **********************************************************************//
	// ! 11. Grid Product
	// **********************************************************************//
	$('.grid').masonry({
		itemSelector: '.grid-item'
	});

	// **********************************************************************//
	// ! 12.Filte/Sortable Product
	// **********************************************************************//
	function filtr_product(){
		$('.filtr-container').filterizr();
	}
	if ( document.getElementById('filtr_product') ) {
		filtr_product();
	}
	//Simple filter controls
	var simplefilter = $('.simplefilter li')
	simplefilter.click(function() {
		simplefilter.removeClass('active');
		$(this).addClass('active');
	});

	// **********************************************************************//
	// ! 13. Vertival Slider
	// **********************************************************************//
	$('.slider-for').slick({
		slidesToShow: 1,
		slidesToScroll: 1,
		arrows: false,
		fade: true,
		asNavFor: '.slider-nav'
	});
	$('.slider-nav').slick({
		slidesToShow: 3,
		slidesToScroll: 1,
		vertical: true,
		asNavFor: '.slider-for',
		dots: false,
		arrows: false,
		centerMode: false,
		focusOnSelect: true
	});

	// **********************************************************************//
	// ! 14. Memu Resize
	// **********************************************************************//
	if( $( window ).width() > 992 ) {
		var my_window = $(window);
		my_window.on('load', function(){

			my_window.resize(function() {
				memu_resize();
			});
			memu_resize();
			function memu_resize(){
				$('.mega-menu-top .mega-menu').each(function() {
					var extraBoxedOffset = 70;

					var li = $(this).parent();
					var liOffset = li.offset().left - extraBoxedOffset;
					var liOffsetTop = li.offset().top;
					var liWidth = $(this).parent().width();
					var dropdowntMarginLeft = liWidth/2;
					var dropdownWidth = $(this).outerWidth();
					var dropdowntLeft = liOffset - dropdownWidth/2;
					
					var left = 0;
					var top = $('header').outerHeight()/2.5;
					if(dropdowntLeft < 0) {
						left = liOffset - 10;
						dropdowntMarginLeft = 0;
					} else {
						left = dropdownWidth/2;
					}
					$(this).css({
						'top': top,
						'left': - left,
						'marginLeft': dropdowntMarginLeft
					});
				});
			}
		});
	}
	$(window).scroll(function () {
		var sc = $(window).scrollTop()
		if (sc > 140) {
			$("header").addClass("fixed")
		} else {
			$("header").removeClass("fixed")
		}
	});
	var heightHeader = $('header').outerHeight();
	$('.wrapper').css('padding-top', heightHeader);

	// **********************************************************************//
	// ! 15. Search
	// **********************************************************************//
	var close_block = $('.search');
	close_block.hide();

	$('.search-icon').on('click', function(){
		close_block.show();
	});
	$('.close').on('click', function(){
		close_block.hide();
	});
	$(document).mouseup(function (e) {
		var container = close_block;
		if (container.has(e.target).length === 0){
			container.hide();
		}
	});

	// **********************************************************************//
	// ! 16. Custom Select
	// **********************************************************************//
	$('.custom-select').each(function(){
		var $this = $(this), numberOfOptions = $(this).children('option').length;

		$this.addClass('select-hidden'); 
		$this.wrap('<div class="select"></div>');
		$this.after('<div class="select-styled"></div>');

		var $styledSelect = $this.next('div.select-styled');
		$styledSelect.text($this.children('option').eq(0).text());

		var $list = $('<ul />', {
			'class': 'select-options'
		}).insertAfter($styledSelect);

		for (var i = 0; i < numberOfOptions; i++) {
			$('<li />', {
				text: $this.children('option').eq(i).text(),
				rel: $this.children('option').eq(i).val()
			}).appendTo($list);
		}

		var $listItems = $list.children('li');

		$styledSelect.click(function(e) {
			e.stopPropagation();
			$('div.select-styled.active').not(this).each(function(){
				$(this).removeClass('active').next('ul.select-options').hide();
			});
			$(this).toggleClass('active').next('ul.select-options').toggle();
		});

		$listItems.click(function(e) {
			e.stopPropagation();
			$styledSelect.text($(this).text()).removeClass('active');
			$this.val($(this).attr('rel'));
			$list.hide();
		});

		$(document).click(function() {
			$styledSelect.removeClass('active');
			$list.hide();
		});
	});

	// **********************************************************************//
	// ! 17. Ask Block
	// **********************************************************************//
	$('.ask-block').click(function() {
		if(!$(this).parent('.ask-answer-block').hasClass('active')){
			$(this).parent('.ask-answer-block').find('.answer-block').fadeIn(100);
			$(this).parent('.ask-answer-block').find('.ask-plus').fadeOut(50);
			$(this).parent('.ask-answer-block').find('.ask-minus').fadeIn(50);
			$(this).parent('.ask-answer-block').addClass('active');
		}
		else{
			$(this).parent('.ask-answer-block').find('.answer-block').fadeOut(100);
			$(this).parent('.ask-answer-block').find('.ask-minus').fadeOut(50);
			$(this).parent('.ask-answer-block').find('.ask-plus').fadeIn(50);
			$(this).parent('.ask-answer-block').removeClass('active');
		}
	});

	$('.counter').counterUp({
		delay: 30,
		time: 2500
	});

});


// **********************************************************************//
// ! 18. Audio Player
// **********************************************************************//
function initProgressBar() {
	var player = document.getElementById('player');
	var length = player.duration
	var current_time = player.currentTime;

	// calculate total length of value
	var totalLength = calculateTotalValue(length)
	document.getElementById("end-time").innerHTML = totalLength;

	// calculate current value time
	var currentTime = calculateCurrentValue(current_time);
	document.getElementById("start-time").innerHTML = currentTime;

	var progressbar = document.getElementById('seek-obj');
		progressbar.value = (player.currentTime / player.duration);
		progressbar.addEventListener("click", seek);

	if (player.currentTime == player.duration) {
		document.getElementById('play-btn').className = "";
	}

	function seek(event) {
	var percent = event.offsetX / this.offsetWidth;
		player.currentTime = percent * player.duration;
		progressbar.value = percent / 100;
	}
};

function initPlayers(num) {

	for (var i = 0; i < num; i++) {
		(function() {

			// Variables
			// ----------------------------------------------------------
			// audio embed object
			var playerContainer = document.getElementById('player-container'),
				player = document.getElementById('player'),
				isPlaying = false,
				playBtn = document.getElementById('play-btn');

			// Controls Listeners
			// ----------------------------------------------------------
			if (playBtn != null) {
				playBtn.addEventListener('click', function() {
				  togglePlay()
				});
			}

			// Controls & Sounds Methods
			// ----------------------------------------------------------
			function togglePlay() {
				if (player.paused === false) {
					player.pause();
					isPlaying = false;
					document.getElementById('play-btn').className = "";
				} else {
					player.play();
					document.getElementById('play-btn').className = "pause";
					isPlaying = true;
				}
			}
		}());
	}
}
function calculateTotalValue(length) {
	var minutes = Math.floor(length / 60),
		seconds_int = length - minutes * 60,
		seconds_str = seconds_int.toString(),
		seconds = seconds_str.substr(0, 2),
		time = minutes + ':' + seconds
	return time;
}
function calculateCurrentValue(currentTime) {
	var current_hour = parseInt(currentTime / 3600) % 24,
		current_minute = parseInt(currentTime / 60) % 60,
		current_seconds_long = currentTime % 60,
		current_seconds = current_seconds_long.toFixed(),
		current_time = (current_minute < 10 ? "0" + current_minute : current_minute) + ":" + (current_seconds < 10 ? "0" + current_seconds : current_seconds);
	return current_time;
}
initPlayers(jQuery('#player-container').length);


// **********************************************************************//
// ! 19. Basic Count Down to Date and Time
// **********************************************************************//
/*
* Basic Count Down to Date and Time
* Author: @mrwigster / trulycode.com
*/
(function (e) {
	e.fn.countdown = function (t, n) {
	function i() {
		eventDate = Date.parse(r.date) / 1e3;
		currentDate = Math.floor(e.now() / 1e3);
		if (eventDate <= currentDate) {
			n.call(this);
			clearInterval(interval)
		}
		seconds = eventDate - currentDate;
		days = Math.floor(seconds / 86400);
		seconds -= days * 60 * 60 * 24;
		hours = Math.floor(seconds / 3600);
		seconds -= hours * 60 * 60;
		minutes = Math.floor(seconds / 60);
		seconds -= minutes * 60;
		days == 1 ? thisEl.find(".timeRefDays").text("day") : thisEl.find(".timeRefDays").text("days");
		hours == 1 ? thisEl.find(".timeRefHours").text("hour") : thisEl.find(".timeRefHours").text("hours");
		minutes == 1 ? thisEl.find(".timeRefMinutes").text("minute") : thisEl.find(".timeRefMinutes").text("minutes");
		seconds == 1 ? thisEl.find(".timeRefSeconds").text("second") : thisEl.find(".timeRefSeconds").text("seconds");
		if (r["format"] == "on") {
			days = String(days).length >= 2 ? days : "0" + days;
			hours = String(hours).length >= 2 ? hours : "0" + hours;
			minutes = String(minutes).length >= 2 ? minutes : "0" + minutes;
			seconds = String(seconds).length >= 2 ? seconds : "0" + seconds
		}
		if (!isNaN(eventDate)) {
			thisEl.find(".days").text(days);
			thisEl.find(".hours").text(hours);
			thisEl.find(".minutes").text(minutes);
			thisEl.find(".seconds").text(seconds)
		} else {
			alert("Invalid date. Example: 30 Tuesday 2013 15:50:00");
			clearInterval(interval)
		}
	}
	var thisEl = e(this);
	var r = {
		date: null,
		format: null
	};
	t && e.extend(r, t);
	i();
	interval = setInterval(i, 1e3)
	}
})(jQuery);
function e() {
	var e = new Date;
	e.setDate(e.getDate() + 60);
	dd = e.getDate();
	mm = e.getMonth() + 1;
	y = e.getFullYear();
	futureFormattedDate = mm + "/" + dd + "/" + y;
	return futureFormattedDate
}
$("#countdown").countdown({
	date: "30 May 2019 09:00:00", // Change this to your desired date to countdown to
	format: "on"
});
// **********************************************************************//
// ! 20. Google map
// **********************************************************************//
var map;
function initMap() {
map = new google.maps.Map(document.getElementById('map'), {
	zoom: 18,
	scrollwheel: false,
	center: new google.maps.LatLng(37.4286126, -122.1421517),
	mapTypeId: 'roadmap',
	styles:[
		  {
			"elementType": "geometry",
			"stylers": [
			  {
				"color": "#f5f5f5"
			  }
			]
		  },
		  {
			"elementType": "labels.icon",
			"stylers": [
			  {
				"visibility": "off"
			  }
			]
		  },
		  {
			"elementType": "labels.text.fill",
			"stylers": [
			  {
				"color": "#616161"
			  }
			]
		  },
		  {
			"elementType": "labels.text.stroke",
			"stylers": [
			  {
				"color": "#f5f5f5"
			  }
			]
		  },
		  {
			"featureType": "administrative.land_parcel",
			"elementType": "labels.text.fill",
			"stylers": [
			  {
				"color": "#bdbdbd"
			  }
			]
		  },
		  {
			"featureType": "poi",
			"elementType": "geometry",
			"stylers": [
			  {
				"color": "#eeeeee"
			  }
			]
		  },
		  {
			"featureType": "poi",
			"elementType": "labels.text.fill",
			"stylers": [
			  {
				"color": "#757575"
			  }
			]
		  },
		  {
			"featureType": "poi.park",
			"elementType": "geometry",
			"stylers": [
			  {
				"color": "#e5e5e5"
			  }
			]
		  },
		  {
			"featureType": "poi.park",
			"elementType": "labels.text.fill",
			"stylers": [
			  {
				"color": "#9e9e9e"
			  }
			]
		  },
		  {
			"featureType": "road",
			"elementType": "geometry",
			"stylers": [
			  {
				"color": "#ffffff"
			  }
			]
		  },
		  {
			"featureType": "road.arterial",
			"elementType": "labels.text.fill",
			"stylers": [
			  {
				"color": "#757575"
			  }
			]
		  },
		  {
			"featureType": "road.highway",
			"elementType": "geometry",
			"stylers": [
			  {
				"color": "#dadada"
			  }
			]
		  },
		  {
			"featureType": "road.highway",
			"elementType": "labels.text.fill",
			"stylers": [
			  {
				"color": "#616161"
			  }
			]
		  },
		  {
			"featureType": "road.local",
			"elementType": "labels.text.fill",
			"stylers": [
			  {
				"color": "#9e9e9e"
			  }
			]
		  },
		  {
			"featureType": "transit.line",
			"elementType": "geometry",
			"stylers": [
			  {
				"color": "#e5e5e5"
			  }
			]
		  },
		  {
			"featureType": "transit.station",
			"elementType": "geometry",
			"stylers": [
			  {
				"color": "#eeeeee"
			  }
			]
		  },
		  {
			"featureType": "water",
			"elementType": "geometry",
			"stylers": [
			  {
				"color": "#c9c9c9"
			  }
			]
		  },
		  {
			"featureType": "water",
			"elementType": "labels.text.fill",
			"stylers": [
			  {
				"color": "#9e9e9e"
			  }
			]
		  }
		]
	});

	var features = [{
		position: new google.maps.LatLng(37.4286126, -122.1421517),
		type: 'info'
	}];

	// Create markers.
	features.forEach(function(feature) {
		var marker = new google.maps.Marker({
			position: feature.position,
			icon: 'wp-content/themes/cinagro-html/assets/images/marker.png',
			map: map,
			title: 'Uluru (Ayers Rock)'
		});
	});

}