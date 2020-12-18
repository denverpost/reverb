$j=jQuery.noConflict();

function insert_related_shortcode() {
  wp.media.editor.insert('[related]');
}

$j(document).ready(function(){
  $j('#insert-related-shortcode').click(insert_related_shortcode);
});

function insert_bucketlist_shortcode() {
  wp.media.editor.insert('[bucketlist]');
}

$j(document).ready(function(){
  $j('#insert-bucketlist-shortcode').click(insert_bucketlist_shortcode);
});