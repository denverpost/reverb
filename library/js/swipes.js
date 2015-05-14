jQuery(document).ready(function($) {
	delete Hammer.defaults.cssProps.userSelect;
    function hammerSwipe() {
    	var hammerElem = $('body.gesture');
		var hammerTime = new Hammer(hammerElem.get(0));
		hammerTime.on("drag", function ( evnt ) {
			var url = false;
			console.log(evnt.direction);
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
	hammerSwipe();
});
