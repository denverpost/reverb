function bringTheBar(text) {
	text = typeof text !== 'undefined' ? text : 'Swipe to navigate!';
	var fixedDiv = document.createElement('div');
	fixedDiv.id = 'message-slide-up';
	fixedDiv.style.position = 'fixed';
	fixedDiv.style.bottom = '0';
	fixedDiv.style.left = '0';
	fixedDiv.style.height = '60px;';
	fixedDiv.style.width = '100%';
	fixedDiv.style.background = 'linear-gradient(to bottom, rgba(0,0,0,0) 0%,rgba(4,4,4,0.5) 23%,rgba(19,19,19,1) 100%);';
	fixedDiv.style.color = '#FFFFFF';
	fixedDiv.style.fontFamily = '\'Open Sans\',\'PT Sans\',Arial,Helvetica,sans-serif';
}

jQuery(document).ready(function($) {
	// if ( $('.gesture').hasClass( 'iphone' ) ) {
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
	// }
});