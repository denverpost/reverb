jQuery(document).ready(function($) {
	delete Hammer.defaults.cssProps.userSelect;
    function hammerSwipe() {
    	var hammerElem = $('body.gesture');
		var hammerTime = new Hammer(hammerElem.get(0));
		hammerTime.get('panleft').set({threshold:65});
		hammerTime.get('panright').set({threshold:65});
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
});
