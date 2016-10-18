<?php
/*
YARPP Template: Thumbnails
Description: Requires a theme which supports post thumbnails
Author: mitcho (Michael Yoshitaka Erlewine)
*/ ?>
<?php if ( have_posts() ): ?>
	<?php while ( have_posts() ) : the_post();
		if ( has_post_thumbnail() ) {
			$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium' );
		}
		if ( isset( $large_image_url ) && strlen( $large_image_url[0] ) >= 1 ) { ?>
			<div class="related-post">
				<div class="front-imgholder"></div>
				<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
					<div class="front-img" style="background-image:url('<?php echo $large_image_url[0]; ?>');"></div>
				</a>
				<h2 <?php post_class('entry-title'); ?>>
					<?php the_title(); ?>
				</h2>
			</div>
		<?php } ?>
	<?php endwhile; ?>

	<div class="clear"></div>

<?php else: ?>

<?php endif; ?>
