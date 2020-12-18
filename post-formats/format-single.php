<?php
/**
 * The template for displaying post content
 *
 * @package Reactor
 * @subpackage Post-Formats
 * @since 1.0.0
 */
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <div class="entry-body">
            
            <header class="entry-header">
            	<?php reactor_post_header(); ?>
            </header><!-- .entry-header -->
    
            <?php if ( get_post_type() == 'neighborhoods' ) {
                get_sidebar('neighborhoodupper');
            } ?>

            <div class="entry-content">
                <?php the_content(); ?>
                <?php wp_link_pages( array('before' => '<div class="page-links">' . __('Pages:', 'reactor'), 'after' => '</div>') ); ?>
                <?php if ( get_post_type() == 'location' && ! has_shortcode( $post->post_content, 'locations' ) ) { ?>
                    <div class="neighborhood-map-form">
                        <div class="map-expander"></div>
                        <?php echo do_shortcode('[leaflet-map zoom=11]'); ?>
                    </div>
                <?php } ?>
            </div><!-- .entry-content --> 
    
            <footer class="entry-footer">
            	<?php reactor_post_footer(); ?>
            </footer><!-- .entry-footer -->
        </div><!-- .entry-body -->
	</article><!-- #post -->