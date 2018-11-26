<?php
/**
 * The template for displaying neighborhood archive pages
 *
 * @package Reactor
 * @subpackge Templates
 * @since 1.0.0
 */
?>

<?php get_header(); ?>

	<div id="primary" class="site-content">
    
    	<?php reactor_content_before(); ?>
    
        <div id="content" role="main">
        	<div class="row">
                <div class="<?php reactor_columns(); ?>">

                <?php $args = array(
                        'post_type' => 'neighborhoods',
                        'posts_per_page' => 25,
                        'paged' => get_query_var( 'paged' )
                        );
                    
                    global $wp_query; 
                    $wp_query = new WP_Query( $args ); ?>

                    <?php if ( $wp_query->have_posts() ) : ?>

                        <?php reactor_loop_before(); ?>
                
                            <header class="page-header">
                                <h1 class="archive-title neighborhood-header"><span>Neighborhoods</span></h1>
                            </header>
                    
                    <?php reactor_loop_before(); ?>

                        <div class="neighborhood-map-form">
                            <div class="map-expander"></div>
                            <?php echo do_shortcode('[leaflet-map]'); ?>
                        </div>
                        
                        <ul class="large-block-grid-3 small-block-grid-2">

                        <?php while ( $wp_query->have_posts() ) : $wp_query->the_post();

                        $neighborhood_slug = get_post_meta( $post->ID, '_neighborhood_slug', true );
                        $neighborhood_tax = get_term_by( 'slug', $neighborhood_slug, 'neighborhood' );
                        if ( isset($neighborhood_tax->parent) && $neighborhood_tax->parent == 0):
                        ?>
                            
                            <li>

                            <?php reactor_post_before(); ?>

                            <?php get_template_part('post-formats/format', 'neighborhoods'); ?>

                            <?php

                                $neighborhood_slug = get_post_meta( $post->ID, '_neighborhood_slug', true );
                                $map_shape_file = get_stylesheet_directory() . '/geojson/' . $neighborhood_slug . '.json';
                                $map_shape_file_url = get_stylesheet_directory_uri() . '/geojson/' . $neighborhood_slug . '.json';

                                if ( file_exists( $map_shape_file ) ) {
                                    $marker_text = '<h3>' . get_the_title() . '</h3><p><a href=\"' . get_the_permalink() . '\">Check out the neighborhood</a></p>';
                                    echo do_shortcode('[leaflet-geojson src="' . $map_shape_file_url . '" fitbounds=true]' . $marker_text . '[/leaflet-geojson]');
                                }

                            ?>

                            <?php reactor_post_after(); ?>

                            </li>

                        <?php endif;

                        endwhile; // end of the post loop ?>

                        </ul>

                    <?php endif; ?>
                        
                    <?php reactor_loop_after(); ?>
                
                <?php reactor_inner_content_after(); ?>
                
                </div><!-- .columns -->
                
                <?php get_sidebar(); ?>
                
            </div><!-- .row -->
        </div><!-- #content -->
        
        <?php reactor_content_after(); ?>
        
	</div><!-- #primary -->

<?php get_footer(); ?>
