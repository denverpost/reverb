$j=jQuery.noConflict();

function scrollDownTo(whereToScroll, scrollOffset) {
    scrollOffset = typeof scrollOffset !== 'undefined' ? scrollOffset : 60;
    $j('html,body').animate({
        scrollTop: ($j(whereToScroll).offset().top - scrollOffset)
    }, 300);
    return false;
}

function checkListicle() {
  if ( $j('.listicle').length == 1 ) {
    $j('.listicle figure.wp-caption figcaption.wp-caption-text').each(function(){
      var node = $j(this).contents().filter(function () { 
            return this.nodeType == 3;
        }).first(),
      text = node.text(),
      first = text.slice(0, text.indexOf(" "));
    if (!node.length)
        return;
    node[0].nodeValue = text.slice(first.length);
    node.before('<span>' + first + '</span>');
    });
  }
}

$j(document).ready(function() {
  $j(window).load(function() {
    //dpLogoClick();
    checkListicle();
    boxes = $j('.frontpage-widget.widget_dpe_fp_widget');
    maxHeight = Math.max.apply(
    Math, boxes.map(function() {
      return $j(this).height();
    }).get());
    boxes.height(maxHeight);
  });
});