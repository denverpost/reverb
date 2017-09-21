<?php
/**
 * The sidebar template containing the front page widget area
 *
 * @package Reactor
 * @subpackge Templates
 * @since 1.0.0
 */
?>

	<?php // get the front page layout
	wp_reset_postdata(); ?>
    
    <?php // if front page has two sidebars and second sidear is active
    if ( is_active_sidebar('sidebar-outdoorupper') ) : ?>
    
    <?php reactor_sidebar_before(); ?>

        <div id="sidebar-outdooruppper" class="sidebar" role="complementary">
            <?php dynamic_sidebar('sidebar-outdoorupper'); ?>
        </div><!-- #sidebar-frontpage-2 -->
    
    <?php reactor_sidebar_after(); ?>

    <div class="clear"></div>
    
    <?php endif; ?>