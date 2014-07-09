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
    
    <?php // if front page has two sidebars and second sidear is active
    if ( is_active_sidebar('sidebar-frontpage-2') ) : ?>
    
    <?php reactor_sidebar_before(); ?>
    
        <div id="sidebar-frontpage-2" class="sidebar" role="complementary">
            <?php dynamic_sidebar('sidebar-frontpage-2'); ?>
        </div><!-- #sidebar-frontpage-2 -->
        
    <?php // else show an alert
    else : if ( '3c-l' == $layout || '3c-r' == $layout || '3c-c' == $layout ) : ?>
    
        <div id="sidebar-frontpage-2" class="sidebar" role="complementary">
            <div class="alert-box secondary"><p>Add some widgets to this area!</p></div>
        </div><!-- #sidebar-2 -->
    
    <?php reactor_sidebar_after(); ?>
    
    <?php endif; endif; ?>