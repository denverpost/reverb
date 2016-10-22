<?php
/*
YARPP Template: Thumbnails
Description: Requires a theme which supports post thumbnails
Author: mitcho (Michael Yoshitaka Erlewine)
*/ ?>
<?php if ( have_posts() ): ?>
	<?php while ( have_posts() ) : the_post();

		$primary_category = tkno_get_primary_category();
	
		if ( has_post_thumbnail() ) {
			$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium' );
		}
	
		if ( isset( $large_image_url ) && strlen( $large_image_url[0] ) >= 1 ) { ?>
			<div <?php post_class('related-wrap'); ?>>
				<div class="related-post" style="background-image:url('<?php echo $large_image_url[0]; ?>');">
					<div class="front-imgholder"></div>
					<a href="<?php the_permalink(); ?>" rel="bookmark"></a>
				</div>
				<span>
					<a href="<?php echo get_category_link( intval( $primary_category->term_id ) ); ?>"><?php echo $primary_category->name; ?></a>
				</span>
				<h2>
					<a href="<?php the_permalink(); ?>" rel="bookmark""><?php the_title(); ?></a>
				</h2>
			</div>
		<?php } ?>
	<?php endwhile; ?>

	<div class="clear"></div>

<?php else: ?>

<?php endif; ?>