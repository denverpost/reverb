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

	$( document ).on( "pageinit", ".gesture", function() {
		$.event.special.swipe.scrollSupressionThreshold = (screen.availWidth) / 60;
		$.event.special.swipe.horizontalDistanceThreshold = (screen.availWidth) / 60;
		$.event.special.swipe.verticalDistanceThreshold = (screen.availHeight) / 13;
		$.event.special.swipe.durationThreshold = 1000;
		var $page = $(this),
		    page = "#" + $page.attr( "id" ), 
		    next = $('.nav-next a').attr('href'), 
		    prev = $('.nav-previous a').attr('href'); 

		if ( next ) {  
		    $page.on( "swipeleft", function() { 
		        $.mobile.changePage( next , {transition: "slide"}); 
		    }); 
		} 

		if ( prev ) { 
		    $page.on( "swiperight", function() { 
		        $.mobile.changePage( prev, { transition: "slide" , reverse: true} ); 
		    }); 
		}
	});
}); 
