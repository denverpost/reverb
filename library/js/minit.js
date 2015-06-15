jQuery(document).on('mobileinit',function($){
	$.event.special.swipe.scrollSupressionThreshold = (screen.availWidth) / 60;
	$.event.special.swipe.horizontalDistanceThreshold = (screen.availWidth) / 60;
	$.event.special.swipe.verticalDistanceThreshold = (screen.availHeight) / 13;
	$.event.special.swipe.durationThreshold = 1000;
});