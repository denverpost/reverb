<?php
/**
 * Flexible Posts Widget: Default widget template
 */

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

$didthumb = false;

echo $before_widget;

if( $flexible_posts->have_posts() ):
	$catquery = get_term_by('id',$flexible_posts->query['tax_query'][0]['terms'][0],'category');

if ( ! function_exists('tkno_get_acceptable_parent') ) {
	function tkno_get_acceptable_parent($catquery) {
		$cat_parents = get_category_parents( $catquery->term_id, false, '/' );
		$valid_cats = array('music','food','drink','things-to-do','arts');
		$cat_parents = explode( '/', $cat_parents );
		foreach ( $cat_parents as $current ) {
			$current = sanitize_title_with_dashes( $current );
			if ( in_array( $current, $valid_cats ) ) {
			    return $current;
			}
		}
	}
}

$cat_parent = tkno_get_acceptable_parent($catquery);
$cat_display = ( ! empty( $title ) ) ? $title : $catquery->name;

echo $before_title . '<span class="fpe-widget-title category-' . $catquery->slug . ' category-' . $cat_parent . '"><a href="' . get_category_link( intval( $catquery->term_id ) ) . '">' . $cat_display . '</a></span>' . $after_title;
?>
	<ul class="dpe-flexible-posts">
	<?php while( $flexible_posts->have_posts() ) : $flexible_posts->the_post(); global $post; ?>
		<li id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php
					if( $thumbnail == true && !$didthumb ) {
						// If the post has a feature image, show it
						if( has_post_thumbnail() ) { 
							$medium_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium'); ?>
							<div class="cat-thumbnail">
								<div class="cat-imgholder"></div>
								<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
									<div class="cat-img" style="background-image:url('<?php echo $medium_image_url[0]; ?>');"></div>
								</a>
							</div>
					<?php } ?>
				<?php $didthumb = true;
				} ?>
				<h4 class="title"><a href="<?php the_permalink(); ?>" rel="bookmark" class="title-link" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>
		</li>
	<?php endwhile; ?>
	</ul><!-- .dpe-flexible-posts -->
<?php else: // We have no posts ?>
	<div class="dpe-flexible-posts no-posts">
		<p><?php _e( 'No post found', 'flexible-posts-widget' ); ?></p>
	</div>
<?php	
endif; // End have_posts()
	
echo $after_widget;