$j=jQuery.noConflict();

function scrollDownTo(whereToScroll, scrollOffset) {
    scrollOffset = typeof scrollOffset !== 'undefined' ? scrollOffset : 60;
    $j('html,body').animate({
        scrollTop: ($j(whereToScroll).offset().top - scrollOffset)
    }, 300);
    return false;
}

$j(document).ready(function() {
  $j(window).load(function() {
    boxes = $j('.frontpage-widget.widget_dpe_fp_widget');
    maxHeight = Math.max.apply(
    Math, boxes.map(function() {
      return $j(this).height();
    }).get());
    boxes.height(maxHeight);
  });
});