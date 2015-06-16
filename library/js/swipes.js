jQuery(document).ready(function($) {
	if ( $('.gesture').hasClass( 'iphone' ) ) {
		var next = $('.nav-next a').attr('href');
	    var prev = $('.nav-previous a').attr('href');

		$(document).on('swipeleft swiperight', function(event) {
			if ( event.type == 'swipeleft' && next.indexOf('http') ) {
		        window.location = next;
			} 
			if ( event.type == 'swiperight' && prev.indexOf('http') ) {
		        window.location = prev;
			}
		});
	}
});