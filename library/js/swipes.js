function bringTheBar(text) {
	text = typeof text !== 'undefined' ? text : 'Swipe to navigate!';
	var fixedDiv = document.createElement('div');
	fixedDiv.id = 'message-slide-up';
	fixedDiv.style.position = 'fixed';
	fixedDiv.style.bottom = '0';
	fixedDiv.style.left = '0';
	fixedDiv.style.height = '36px;';
	fixedDiv.style.width = '100%';
	fixedDiv.style.background = 'linear-gradient(to bottom, rgba(0,0,0,0) 0%,rgba(4,4,4,0.5) 23%,rgba(19,19,19,1) 100%);';
	fixedDiv.style.color = '#FFFFFF';
	fixedDiv.style.fontSize = '18px';
	fixedDiv.style.fontFamily = '\'Open Sans\',\'PT Sans\',Arial,Helvetica,sans-serif';
	fixedDiv.style.textAlign = 'center';
	fixedDiv.innerHTML = '<span style="display:block;position:fixed;left:2%;bottom:9px;">&lt;</span>' + text +'<span style="display:block;position:fixed;right:2%;bottom:9px;">&gt;</span>';
	document.body.appendChild(fixedDiv);
}

jQuery(document).ready(function($) {
	// if ( $('.gesture').hasClass( 'iphone' ) ) {
		var next = $('.nav-next a').attr('href');
	    var prev = $('.nav-previous a').attr('href');
	    console.log('next: ' + next + ', prev: ' + prev);

		$(document).on('swipeleft swiperight', function(event) {
			if ( event.type == 'swipeleft' && next.indexOf('reverb') ) {
		        window.location = next;
			} 
			if ( event.type == 'swiperight' && prev.indexOf('reverb') ) {
		        window.location = prev;
			}
		});
	// }
});