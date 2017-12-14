<?php
/**
 * Template Name: Venue Pages
 * 
 * The template for displaying single venues
 *
 * @package Reactor
 * @subpackage Templates
 * @since 1.0.0
 */
?>

<?php get_header(); ?>

	<div id="primary" class="site-content">
    
    	<?php reactor_content_before(); ?>
    
        <div id="content" role="main">
        	<div class="row">
                <div class="<?php reactor_columns(); ?>">
                
                <?php reactor_inner_content_before(); ?>
                
					<?php // start the loop
                    while ( have_posts() ) : the_post(); ?>
                    
                    <?php reactor_post_before(); ?>
                        
					<?php get_template_part('post-formats/format', 'single'); ?>

                    <?php 
                    if ( ! has_shortcode( $post->post_content, 'locations' ) ) {
                        $loc_imgoverride = get_post_meta( $post->ID, '_loc_imgoverride', true );
                        $loc_address_override = get_post_meta( $post->ID, '_loc_address_override', true );
                        $latitude = get_post_meta( $post->ID, '_location_latitude', true );
                        $longitude = get_post_meta( $post->ID, '_location_longitude', true );
                        $address = ( isset( $loc_address_override ) && strlen( $loc_address_override ) >= 1 ) ? $loc_address_override : get_post_meta( $post->ID, '_location_street_address', true );
                        $medium_img_url = ( $loc_imgoverride != 'true' && has_post_thumbnail() ) ? wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium') : false;
                        $img_div = ( $medium_img_url && strlen( $medium_img_url[0]) >= 1) ? '<div class="cat-thumbnail"><div class="cat-imgholder"></div><a href="' . get_the_permalink() . '"><div class="cat-img" style="background-image:url(\\\'' . $medium_img_url[0] . '\\\');"></div></a></div>' : '';
                        $loc_map_icon = get_post_meta( $post->ID, '_loc_map_icon', true );
                        $marker_icon = ( isset( $loc_map_icon ) ) ? get_marker_icon($loc_map_icon) : '';
                        echo do_shortcode('[leaflet-marker zoom=11 lat=' . $latitude . ' lng=' . $longitude . $marker_icon . ']<h3><a href="' . get_the_permalink() . '">' . addslashes( get_the_title() ) . '</a></h3><p>' . $address . '</p>' . $img_div . '[/leaflet-marker]');
                    }
                    ?>

                    <?php reactor_post_after(); ?>
        
                    <?php endwhile; // end of the loop ?>
                    
                <?php reactor_inner_content_after(); ?>

                <?php global $post;
                    $location_address = get_post_meta($post->ID, '_location_street_address', true);
                    $venue_slug = get_post_meta($post->ID, '_venue_slug', true);
                    $venue_name = get_term_by( 'slug', $venue_slug, 'venue' ); ?>
                
                <div class="location-related">

                    <?php

                        $args = array(
                            'post_type' => 'location',
                            'posts_per_page' => 5,
                            'post__not_in' => array( $post->ID )
                            );
                        add_filter( 'posts_where' , 'location_posts_near' ); 
                        $location_query = new WP_Query( $args );
                        remove_filter( 'posts_where' , 'location_posts_near' ); ?>
                        
                        <?php if ( $location_query->have_posts() ) : ?>
                            
                        <h2 class="archive-title"><a class="noclick" href="javascript:void(0);">Nearby</a></h2>
                        
                            <?php while ( $location_query->have_posts() ) : $location_query->the_post(); ?>

                                <?php get_template_part('post-formats/format', 'location'); ?>

                            <?php endwhile;
                        
                        endif; ?>

                    </div>
                
                </div><!-- .columns -->
                
                <?php get_sidebar('outdoors'); ?>
                
            </div><!-- .row -->
        </div><!-- #content -->
        
        <?php reactor_content_after(); ?>
        
	</div><!-- #primary -->

<?php get_footer(); ?>