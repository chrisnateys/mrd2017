require(["jquery"], function($){
	if (jQuery('button.button').length) {
		jQuery('button.deduct').click(function(){
			var oldValue = jQuery('#qty').val();
			// Don't let #qty get lower than 0
			if (oldValue > 0) {
				var newVal = parseInt(oldValue - 1);
				
			} else {
				newVal = 0;
			}
			jQuery('#qty').val(newVal);
			return false;
		});
		jQuery('button.add').click(function(){
			var oldValue = parseInt(jQuery('#qty').val());
			var newVal = parseInt(oldValue + 1);
			jQuery('#qty').val(newVal);
			return false;
		});
	}
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