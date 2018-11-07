$(document).ready(function($) {
	
	"use strict";

	var menu = $('.menu');
	$('.menu-button').on('click', function(){
		menu.toggle();
		$(this).toggleClass('active');
		menu.parent().toggleClass('mobile-menu');
	});
	
	$('.mega-menu-top a, .children a').on('click', function(){
		$(this).parent().find('.mega-menu, .sub-menu').toggleClass('active');
	});
	
	
	$('.switch span').on('click', function(){
		$(this).addClass("active").siblings().removeClass("active");
		$('.content-product .product').toggleClass('list-product');
		$('.content-product.grid').toggleClass('list-product-top');
		$(".filtr-container").toggleClass('list-product-width');
		});
		
	$('.grid').masonry({
		itemSelector: '.grid-item'
	});

	$(".box").each(function(){
        if( $(this).find(".background .background-image").length ) {
            $(this).css("background-color","transparent");
        }
    });

	$("[data-background-image]").each(function() {
        $(this).css("background-image", "url("+ $(this).attr("data-background-image") +")" );
    });

    $(".background-image").each(function() {
        $(this).css("background-image", "url("+ $(this).find("img").attr("src") +")" );
    });
	
});