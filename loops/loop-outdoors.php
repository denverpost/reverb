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
			'post_type'           => 'post',
			'cat'                 => $current_cat,
			'posts_per_page'      => 3,
			'offset'			  => 1
			);
        $outdoorpage_query = new WP_Query( $args ); ?>

	    <?php if ( $outdoorpage_query->have_posts() ) : ?>
        
        <?php reactor_loop_before(); ?>
            
            <?php while ( $outdoorpage_query->have_posts() ) : $outdoorpage_query->the_post(); ?>
            	
                <?php reactor_post_before(); ?>
                    
                    <?php get_template_part('post-formats/format', 'frontpage'); // Frontpage format for each post 
                    $address = get_post_meta( $post->ID, '_location_street_address', true );
                    $latitude = get_post_meta( $post->ID, '_location_latitude', true );
                    $longitude = get_post_meta( $post->ID, '_location_longitude', true );
                    if ( $address && $latitude && $longitude ) {
                        $medium_img_url = ( $post->ID ) ? wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium') : false;
                        $img_div = ( $medium_img_url && strlen( $medium_img_url[0] ) >= 1 ) ? '<div class="cat-thumbnail"><div class="cat-imgholder"></div><a href="' . get_permalink( $post->ID ) . '"><div class="cat-img" style="background-image:url(\\\'' . $medium_img_url[0] . '\\\');"></div></a></div>' : '';
                        $map_display .= do_shortcode('[leaflet-marker zoom=11 lat=' . $latitude . ' lng=' . $longitude . ']<h3><a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a></h3><p>' . $address . '</p>' . $img_div . '[/leaflet-marker]' );
                    }
                    ?>
                
                <?php reactor_post_after(); ?>

            <?php endwhile; // end of the loop ?>

        <?php reactor_loop_after(); ?>
                
        <?php // if no posts are found
		else : reactor_loop_else(); ?>

        <?php endif; // end have_posts() check

        set_query_var("cat",$current_cat); ?>