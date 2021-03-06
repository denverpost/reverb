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
			<?php //the_time('F j, Y'); ?>
            </header><!-- .entry-header -->
    
            <?php if ( is_author() ) : ?>
            <div class="entry-content">
            </div>
            <?php elseif ( is_search() || is_archive() ) : // Only display Excerpts for Search ?>
            <div class="entry-summary">
                <?php the_excerpt(); ?>
            </div><!-- .entry-summary -->
            <?php elseif ( is_single() ) : ?>
            <div class="entry-content">
                <?php the_content(); ?>
                <?php wp_link_pages( array('before' => '<div class="page-links">' . __('Pages:', 'reactor'), 'after' => '</div>') ); ?>
            </div><!-- .entry-content -->
            <?php else : ?>
            <div class="entry-content">
                <?php the_content(); ?>
            </div><!-- .entry-content -->
            <?php endif; ?>
            
            <?php if ( !is_author() && !is_search() ) : ?>
                <footer class="entry-footer">
                	<?php reactor_post_footer(); ?>
                </footer><!-- .entry-footer -->
            <?php endif; ?>
        </div><!-- .entry-body -->
	</article><!-- #post -->