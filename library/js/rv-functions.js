$j=jQuery.noConflict();

function scrollDownTo(whereToScroll, scrollOffset) {
    scrollOffset = typeof scrollOffset !== 'undefined' ? scrollOffset : 60;
    $j('html,body').animate({
        scrollTop: ($j(whereToScroll).offset().top - scrollOffset)
    }, 300);
    return false;
}

var disqus_shortname = 'dpreverb'; // required: replace example with your forum shortname
jQuery.ajax({
  type: 'GET',
  url: '//dpreverb.disqus.com/count.js',
  dataType: 'script',
  cache: false
});

function showDisqusComments() {
	var disqus_shortname = 'dpreverb';
	$j.ajax({
        type: "GET",
        url: "http://" + disqus_shortname + ".disqus.com/embed.js",
        dataType: "script",
        cache: true
    });
    $j('.showdisqus').fadeOut();
    scrollDownTo('#disqus_thread');
}

//Disqus button reveal
$j(document).ready(function() {
  $j(document).foundation({
        equalizer: {
            equalize_on_stack: true
        }
    });
	var checkHash = location.hash;
	if (checkHash == '#disqus_thread' || checkHash == '#comments') {
		showDisqusComments();
	}
	$j('.showdisqus').on('click', function(){
		showDisqusComments();
	});
	$j('.commentsNameLink').on('click', function(){
		showDisqusComments();
	});
});