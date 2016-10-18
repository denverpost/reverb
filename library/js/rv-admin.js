$j=jQuery.noConflict();

console.log('loaded adminjs');
function insert_related_shortcode() {
  wp.media.editor.insert('[related]');
}

$j(document).ready(function(){
  $j('#insert-related-shortcode').click(insert_related_shortcode);
  console.log('loaded');
});
