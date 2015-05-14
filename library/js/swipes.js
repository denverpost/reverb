jQuery(document).ready(function($) {
    function hammerSwipe() {
		var hammerTime = $('body.gesture').hammer();
		hammerTime.on("drag", function ( evnt ) {
			var url = false;
			if ( evnt.direction == 'right' ) {
			    url = jQuery('.nav-next a').attr('href');
			}		
			if ( evnt.direction == 'left' ) {
			    url = jQuery('.nav-previous a').attr('href');
			}
			if ( url ) {
			    window.location = url;
			}
		});
    }
    if ( $('.gesture').hasClass( 'iphone' ) ) {
	    hammerSwipe();
	}
});
