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
	wp_reset_postdata();
    $layout =  reactor_option('', '1c', '_template_layout'); ?>
    
    <?php // if front page has one sidebar and the sidebar is active
    if ( is_active_sidebar('sidebar-frontpage') ) : ?>
    
    <?php reactor_sidebar_before(); ?>
    
        <div id="sidebar-frontpage" class="sidebar" role="complementary">
            <?php dynamic_sidebar('sidebar-frontpage'); ?>
        </div><!-- #sidebar-frontpage -->
        
    <?php // else show an alert
    else : if ( '1c' != $layout ) : ?>
    
        <div id="sidebar-frontpage" class="sidebar" role="complementary">
            <div class="alert-box secondary"><p>Add some widgets to this area!</p></div>
        </div><!-- #sidebar --> 
    
    <?php reactor_sidebar_after(); ?>
    
    <?php endif; endif; ?>