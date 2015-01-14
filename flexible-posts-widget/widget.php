<?php
/**
 * Flexible Posts Widget: Default widget template
 */

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

echo $before_widget;

if ( !empty($title) )
	echo $before_title . $title . $after_title;

if( $flexible_posts->have_posts() ):
	$catquery = get_term_by('slug',$flexible_posts->query['tax_query'][0]['terms'][0],'category');
?>
	<ul class="dpe-flexible-posts">
	<?php while( $flexible_posts->have_posts() ) : $flexible_posts->the_post(); global $post; ?>
		<li id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<a href="<?php echo the_permalink(); ?>">
				<?php
					if( $thumbnail == true ) {
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
				<?php } ?>
				<h4 class="title cat-<?php echo $catquery->slug; ?>"><span class="category-title"><a href="<?php echo get_category_link(intval($catquery->name)); ?>" title="<?php echo $catquery->name; ?>"><?php echo $catquery->name; ?></a></span><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>
			</a>
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
