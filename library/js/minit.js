jQuery(document).on('mobileinit',function(){
	jQuery.event.special.swipe.scrollSupressionThreshold = (screen.availWidth) * .60;
	jQuery.event.special.swipe.horizontalDistanceThreshold = (screen.availWidth) * .60;
	jQuery.event.special.swipe.verticalDistanceThreshold = (screen.availHeight) * .10;
	jQuery.event.special.swipe.durationThreshold = 1000;
});