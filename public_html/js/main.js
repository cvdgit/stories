
$(function() {

	$('.modal').on('show.bs.modal', function () {
	    $('.modal').not($(this)).each(function () {
	        $(this).modal('hide');
	    });
	});

  const addCommentFocusClassName = 'add-comment-focus';
  $('.add-comment-placeholder textarea').on('focus', function() {
    var $element = $(this).parent().parent();
    if (!$element.hasClass(addCommentFocusClassName)) {
      $element.addClass(addCommentFocusClassName);
    }
  });
  $('.add-comment-close').on('click', function(e) {
    e.preventDefault();
    $(this).parent().parent().removeClass(addCommentFocusClassName).find('textarea').val('');
  });

});


/*

$(function() {
	"use strict";

	// ! 03. Back to top
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

	// ! 04. Mobile Menu
	var menu = $('.menu');
	$('.menu-button').on('click', function(){
		menu.toggle();
		$(this).toggleClass('active');
		menu.parent().toggleClass('mobile-menu');
	});

	$('.mega-menu-top a, .children a').on('click', function(){
		$(this).parent().find('.mega-menu, .sub-menu').toggleClass('active');
	});

	// ! 14. Memu Resize
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


	$(document).ready( function() {
    $(".file-upload input[type=file]").change(function(){
         var filename = $(this).val().replace(/.*\\/, "");
				 $("#filename").val(filename);
    });
});

$(document).ready(function() {
   
  $.uploadPreview({
    input_field: "#image-upload",   // По умолчанию: .image-upload
    preview_box: "#image-preview",  // По умолчанию: .image-preview
    label_field: "#image-label",    // По умолчанию: .image-label
    label_default: "Choose File",   // По умолчанию: Choose File
    label_selected: "Change File",  // По умолчанию: Change File
    no_label: false,                // По умолчанию: false
    success_callback: function() {
				var formData = new FormData($('#form-upload')[0]);
				// console.log('formData', formData);    
				$.ajax({
						type: 'post',
						url:'/file-avatar',
						data: formData,
						cache: false,
						contentType: false,
						processData: false,
						success: function(data) {
								
						}
				})  
				return false;
 
		}
  });
});

});

*/