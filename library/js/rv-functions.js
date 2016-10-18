$j=jQuery.noConflict();

function scrollDownTo(whereToScroll, scrollOffset) {
    scrollOffset = typeof scrollOffset !== 'undefined' ? scrollOffset : 60;
    $j('html,body').animate({
        scrollTop: ($j(whereToScroll).offset().top - scrollOffset)
    }, 300);
    return false;
}

$j(document).ready(function(){
  $j('#insert-related-shortcode').click(insert_related_shortcode);
  console.log('loaded');
});

function insert_related_shortcode() {
  wp.media.editor.insert('[related]');
}

$j(document).ready(function() {
  $j(window).load(function() {
    //dpLogoClick();
    //searchOpen();
    boxes = $j('.frontpage-widget.widget_dpe_fp_widget');
    maxHeight = Math.max.apply(
    Math, boxes.map(function() {
      return $j(this).height();
    }).get());
    boxes.height(maxHeight);
  });
});