$j=jQuery.noConflict();

function insert_related_shortcode() {
  wp.media.editor.insert('[related]');
}

$j(document).ready(function(){
  $j('#insert-related-shortcode').click(insert_related_shortcode);
});
