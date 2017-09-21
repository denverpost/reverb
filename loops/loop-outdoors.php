<?php
/**
 * The loop for displaying posts on the front page template
 *
 * @package Reactor
 * @subpackage loops
 * @since 1.0.0
 */

		if ( get_query_var('paged') ) {
			$paged = get_query_var('paged');
		} elseif ( get_query_var('page') ) {
			$paged = get_query_var('page');
		} else {
			$paged = 1;
		}
        $args = array( 
			'post_type'           => 'post',
			'cat'                 => get_query_var( 'cat' ),
			'posts_per_page'      => 3,
			'paged'               => $paged
			);
		
        $outdoorpage_query = new WP_Query( $args ); ?>
              
	    <?php if ( $outdoorpage_query->have_posts() ) : $i=0; ?>
        
        <?php reactor_loop_before(); ?>
            
            <?php while ( $outdoorpage_query->have_posts() ) : $outdoorpage_query->the_post(); $i++; ?>
            	
                <?php reactor_post_before(); ?>
                    
                    <?php get_template_part('post-formats/format', 'frontpage'); // Frontpage format for each post ?>
                
                <?php reactor_post_after(); ?>

            <?php endwhile; // end of the loop ?>

        <?php reactor_loop_after(); ?>
                
        <?php // if no posts are found
		else : reactor_loop_else(); ?>

        <?php endif; // end have_posts() check ?>