<?php
/**
 * The sidebar template containing the neighborhood page lower widget area
 *
 * @package Reactor
 * @subpackge Templates
 * @since 1.0.0
 */
?>
    <?php // just the tiny, boring sidebar
    if ( is_active_sidebar('sidebar-categorysponsor') ) : ?>
    
    <?php reactor_sidebar_before(); ?>
        <?php dynamic_sidebar('sidebar-categorysponsor'); ?>    

    <div class="clear"></div>
    
    <?php endif; ?>