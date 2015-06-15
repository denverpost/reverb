/* jQuery(document).ready(function($) {
	delete Hammer.defaults.cssProps.userSelect;
    function hammerSwipe() {
    	var hammerElem = $('body.gesture');
		var hammerTime = new Hammer(hammerElem.get(0));
		hammerTime.get('pan').set({threshold:100});
		var url = false;
		hammerTime.on("panright", function ( evnt ) {
			console.log(evnt.direction);
			url = jQuery('.nav-next a').attr('href');
			if ( url ) {
			    window.location = url;
			}
		});
		hammerTime.on("panleft", function ( evnt ) {
			console.log(evnt.direction);
			url = jQuery('.nav-previous a').attr('href');
			if ( url ) {
			    window.location = url;
			}
		});
    }
    if ( $('.gesture').hasClass( 'iphone' ) ) {
	    hammerSwipe();
	}
}); */

jQuery(document).ready(function($) {
	console.log('got here');
	$.event.special.swipe.scrollSupressionThreshold = (screen.availWidth) / 60;
	$.event.special.swipe.horizontalDistanceThreshold = (screen.availWidth) / 60;
	$.event.special.swipe.verticalDistanceThreshold = (screen.availHeight) / 13;
	$.event.special.swipe.durationThreshold = 1000;
	var next = $('.nav-next a').attr('href'), 
    var prev = $('.nav-previous a').attr('href');

	$(document).on('swipeleft swiperight', function(event) {
		if ( event.type == 'swipeleft' ) {
	        $.mobile.changePage( next , {transition: "slide"});
		} 
		if ( event.type == 'swiperight' ) {
	        $.mobile.changePage( prev, { transition: "slide" , reverse: true} );
		}
	});
}); 
