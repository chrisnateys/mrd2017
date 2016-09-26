require(["jquery"], function($){
	jQuery('.nav-sections').on('swipeleft swiperight', function(e){
		e.stopPropagation();
		e.preventDefault();
	});
	jQuery('.footer-top h4').click(function(){
		if(jQuery(window).width() < 768) {
			jQuery(this).toggleClass('soHotRightNow');
			jQuery(this).next('ul').slideToggle();
		}
	});
	jQuery('.mobile-menu-button').click(function(){
		jQuery('.nav-sections-item-content').toggleClass('soHotRightNow');
	});
	jQuery('.menu-close').click(function(){
		jQuery('.nav-sections-item-content').toggleClass('soHotRightNow');
	});
});