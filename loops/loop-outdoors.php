<?php
/**
 * The loop for displaying posts on the outdoors template
 *
 * @package Reactor
 * @subpackage loops
 * @since 1.0.0
 */

		$current_cat = get_query_var('cat');
        $args = array( 
			'post_type'           => array('post','quicktrip'),
			'cat'                 => $current_cat,
			'posts_per_page'      => 3,
			'offset'			  => 1
			);
        $outdoorpage_query = new WP_Query( $args ); ?>

	    <?php if ( $outdoorpage_query->have_posts() ) : ?>

            <?php while ( $outdoorpage_query->have_posts() ) : $outdoorpage_query->the_post(); ?>
            	
                <?php reactor_post_before(); ?>
                    
                    <?php get_template_part('post-formats/format', 'frontpage'); // Frontpage format for each post ?>
                
                <?php reactor_post_after(); ?>

            <?php endwhile; // end of the loop ?>

        <?php // if no posts are found
		else : reactor_loop_else(); ?>

        <?php endif; // end have_posts() check

        set_query_var("cat",$current_cat);
        wp_reset_query(); ?>