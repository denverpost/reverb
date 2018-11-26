<?php
/**
 * Created by PhpStorm.
 * User: cbrubaker
 * Date: 10/18/18
 * Time: 11:21 AM
 */


<section>
<h1><?php _e( 'posts', 'neighborhood' ); ?></h1>
<?php get_template_part('loop'); ?>
<?php
$args = array('post_type' => 'neighborhood', 'posts_per_page' => 3);
$query = new WP_Query($args);
while($query -> have_posts()) : $query -> the_post();
?>
<h2><?php the_title(); ?></h2>
<p>Meta: <?php the_meta(); ?></p>
<p>Excerpt: <?php the_excerpt(); ?></p>
<p>what_to_put_here_to_get_taxonomies_values????</p>
<?php endwhile; ?>

<?php get_template_part('pagination'); ?>
</section>

<?PHP
/**
* Get taxonomies terms links.
*
* @see get_object_taxonomies()
*/
function wpdocs_custom_taxonomies_terms_links() {
// Get post by post ID.
if ( ! $post = get_post() ) {
return '';
}

// Get post type by post.
$post_type = $post->post_type;

// Get post type taxonomies.
$taxonomies = get_object_taxonomies( $post_type, 'objects' );

$out = array();

foreach ( $taxonomies as $taxonomy_slug => $taxonomy ){

// Get the terms related to post.
$terms = get_the_terms( $post->ID, $taxonomy_slug );

if ( ! empty( $terms ) ) {
$out[] = "<h2>" . $taxonomy->label . "</h2>\n<ul>";
    foreach ( $terms as $term ) {
    $out[] = sprintf( '<li><a href="%1$s">%2$s</a></li>',
    esc_url( get_term_link( $term->slug, $taxonomy_slug ) ),
    esc_html( $term->name )
    );
    }
    $out[] = "\n</ul>\n";
}
}
return implode( '', $out );
}
?>