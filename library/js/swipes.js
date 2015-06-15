jQuery(document).ready(function($) {
	if ( $('.gesture').hasClass( 'iphone' ) ) {
		var next = $('.nav-next a').attr('href');
	    var prev = $('.nav-previous a').attr('href');

		$(document).on('swipeleft swiperight', function(event) {
			if ( event.type == 'swipeleft' ) {
		        window.location = next;
			} 
			if ( event.type == 'swiperight' ) {
		        window.location = prev;
			}
		});
	}
});