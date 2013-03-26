jQuery(function(){

	// initialise plugins
		jQuery('.sf-menu').superfish({
			//useClick: true
		});
		jQuery("#tabs").tabs();

		jQuery('ul.columns li:last-child').addClass('last');

	//portfolio style
	jQuery('.data').hover(function(){
		link = jQuery(this).find('.con');
		link.animate({top: '50%', opacity: '1'},200);
	},
	function(){
		link.animate({top: '-50px', opacity: '0'},200);
	}
	);

	// hide #back-top first
	jQuery("#back-top").hide();

	// fade in #back-top
	jQuery(function () {
		jQuery(window).scroll(function () {
			if (jQuery(this).scrollTop() > 100) {
				jQuery('#back-top').fadeIn();
			} else {
				jQuery('#back-top').fadeOut();
			}
		});

		// scroll body to 0px on click
		jQuery('#back-top a').click(function () {
			jQuery('body,html').animate({
				scrollTop: 0
			}, 800);
			return false;
		});
	});

	//function to stay menu top when scroll down
		var menu = jQuery('.main-nav'),
		pos = menu.offset();
		
		jQuery(window).scroll(function(){
			if(jQuery(this).scrollTop() > pos.top+menu.height() && menu.hasClass('default')){
				menu.fadeOut('fast', function(){
					jQuery(this).removeClass('default').addClass('fixed').fadeIn('fast');
				});
			} else if(jQuery(this).scrollTop() <= pos.top && menu.hasClass('fixed')){
				menu.fadeOut('fast', function(){
					jQuery(this).removeClass('fixed').addClass('default').fadeIn('fast');
				});
			}
		});
});