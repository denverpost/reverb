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
    function theSwipe() {
    	var hammerElem = $('body.gesture');
		var url = false;
		hammerElem.on("swiperight", function () {
			url = $('.nav-next a').attr('href');
			if ( url ) {
			    window.location = url;
			}
		});
		hammerElem.on("swipeleft", function () {
			url = $('.nav-previous a').attr('href');
			if ( url ) {
			    window.location = url;
			}
		});
    }
});
