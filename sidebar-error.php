<?php
/**
 * The sidebar template containing the main widget area
 *
 * @package Reactor
 * @subpackge Templates
 * @since 1.0.0
 */
?>
	<?php // get the page layout
	wp_reset_postdata(); 
	$default = reactor_option('page_layout', '2c-l');
	$layout = reactor_option('', $default, '_template_layout'); ?>
    
    <?php // if layout has one sidebar and the sidebar is active
    if ( is_active_sidebar('sidebar-error') ) : ?>
    
    <?php reactor_sidebar_before(); ?>
    
        <div id="sidebar-error" class="sidebar <?php reactor_columns( array(12,12,12), true, true, 1 ); ?>" role="complementary">
            <?php dynamic_sidebar('sidebar-error'); ?>
        </div><!-- #sidebar -->
        
    <?php // else show an alert
    else : if ( '1c' != $layout ) : ?>
    
        <div id="sidebar" class="sidebar <?php reactor_columns( '', true, true, 1 ); ?>" role="complementary">
            <div class="alert-box secondary"><p>Add some widgets to this area!</p></div>
        </div><!-- #sidebar --> 
        
    <?php reactor_sidebar_after(); ?>    
    
    <?php endif; endif; ?>
    
    