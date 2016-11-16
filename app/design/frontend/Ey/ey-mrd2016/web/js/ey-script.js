require(["jquery"], function($){
	if (jQuery('.hp-topbanner-carousel .products-grid .product-items').length){
		var owl = jQuery('.hp-topbanner-carousel .products-grid .product-items');
		owl.owlCarousel({
			responsive: {
                0: {
                    items: 2,
                    dots: true,
                    slideBy: 2
                },
                480: {
                    items: 2,
                    dots: true,
                    slideBy: 2
                },
                768: {
                	items: 3,
                	dots: false,
                	slideBy: 3
                }
            }
		});
		jQuery('.hp-topbanner-carousel .products-grid').append('<div class="hp-owlnav"><div class="hp-owlprev"></div><div class="hp-owlnext"></div></div>');
		jQuery('.hp-topbanner-carousel .hp-owlprev').click(function(){
			owl.trigger('prev.owl.carousel');
		});
		jQuery('.hp-topbanner-carousel .hp-owlnext').click(function(){
			owl.trigger('next.owl.carousel');
		});
	}
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
	jQuery(document).ready(function(){
		if (jQuery('.amasty-preorder-note').length) {
			jQuery('.item.product.product-item').each(function(index){
				jQuery(this).append('<div class="reserve-badge">Pre-Order</div>');
			});
		}
	});
});