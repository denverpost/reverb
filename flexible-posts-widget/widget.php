<?php
/**
 * Flexible Posts Widget: Default widget template
 */

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

$didthumb = false;

echo $before_widget;

if ( ! function_exists('is_outdoors') ) {
	function is_outdoors() {
	    /* is it an outdoors-related page? */
	    $outdoors = false;
	    global $post;
	    $current_id = ( is_single() ) ? $post->ID : get_query_var('cat');
	    $outdoor_parent = get_category_by_slug( 'outdoors' );
	    if ( is_category() && ( $current_id == $outdoor_parent->term_id || cat_is_ancestor_of( $outdoor_parent->term_id, $current_id ) ) ) {
	        $outdoors = true;
	    } else if ( is_single() ) {
	        $categories = wp_get_post_categories( $current_id );
	        foreach ( $categories as $category ) {
	            if ( $category == $outdoor_parent->term_id ) {
	                $outdoors = true;
	                break;
	            }
	        }
	    }
	    return $outdoors;
	}
}

if( $flexible_posts->have_posts() ):
	$catquery = get_term_by('id',$flexible_posts->query['tax_query'][0]['terms'][0],'category');

if ( ! function_exists('tkno_get_acceptable_parent') ) {
	function tkno_get_acceptable_parent($catquery) {
		$cat_parents = get_category_parents( $catquery->term_id, false, '/' );
		$valid_cats = ( is_outdoors() ) ? array( 'spring', 'summer', 'fall', 'winter', 'trips' ) : array( 'music', 'food', 'drink', 'things-to-do', 'arts' );
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