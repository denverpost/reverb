<?php
/*
YARPP Template: Thumbnails
Description: Requires a theme which supports post thumbnails
Author: mitcho (Michael Yoshitaka Erlewine)
*/ ?>
<?php if ( have_posts() ): ?>
	<?php while ( have_posts() ) : the_post();
		$categories_list = '';
		$categories = get_the_category();
		end( $categories );
		foreach( $categories as $category ) {
			if ( strtolower( $category->slug) != 'uncategorized' && $category->category_parent == 0 ) {
				$categories_list = $category;
			}
		}

		if ( has_post_thumbnail() ) {
			$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium' );
		}
		if ( isset( $large_image_url ) && strlen( $large_image_url[0] ) >= 1 ) { ?>
			<div class="related-post" style="background-image:url( '<?php echo $large_image_url[0]; ?>' );">
				<div class="front-thumbnail">
					<div class="front-imgholder"></div>
					<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
						<div class="front-img" style="background-image:url('<?php echo $large_image_url[0]; ?>');"></div>
					</a>
				</div>
				<h2 class="entry-title">
					<span>
						<a href="<?php echo get_category_link( intval( $categories_list->term_id ) ); ?>"><?php echo $categories_list->cat_name; ?></a>
					</span>
					<?php the_title(); ?>
				</h2>
			</div>
		<?php } ?>
	<?php endwhile; ?>

<?php else: ?>

<?php endif; ?>
