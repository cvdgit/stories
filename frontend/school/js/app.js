import "../scss/main.scss";
import "bootstrap/js/dist/collapse";
import "bootstrap/js/dist/dropdown";
import "bootstrap/js/dist/tab";
import "bootstrap/js/dist/modal";
import $ from 'jquery';

$(".categories li").click(function(e) {
    e.preventDefault();
    
    const isActive = $(this).hasClass('active');

    $('.categories li a.nav-link').removeClass('active');

    $('.categories li').removeClass('active');
    $(this).addClass('active');

    $('.categories').toggleClass('expanded');

    if (!isActive) {
        $(this).tab('show');
    }
});