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
                        
					<?php // get post format and display code for that format
                    if ( !get_post_format() ) : get_template_part('post-formats/format', 'single'); 
					else : get_template_part('post-formats/format', get_post_format() ); endif; ?>
                    
                    <?php reactor_post_after(); ?>
        
                    <?php endwhile; // end of the loop ?>
                    
                <?php reactor_inner_content_after(); ?>

                <?php global $post;
                    $venue_slug = get_post_meta($post->ID, '_venue_slug', true);
                    $venue_name = get_term_by( 'slug', $venue_slug, 'venue' ); ?>
                
                <div class="venue-related">
                    <h2 class="archive-title"><a class="noclick" href="javascript:void(0);">Recent stories featuring <?php echo $venue_name->name; ?></a></h2>

                    <?php

                        $args = array(
                            'post_type' => 'post',
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'venue',
                                    'field' => 'slug',
                                    'terms' => array( $venue_name->slug )
                                ),
                            ),
                            'posts_per_page' => 10,
                            'paged' => get_query_var( 'paged' )
                            );
                        $venue_query = new WP_Query( $args ); ?>

                        <?php if ( $venue_query->have_posts() ) :
                            
                            while ( $venue_query->have_posts() ) : $venue_query->the_post(); ?>

                                <?php get_template_part('post-formats/format', 'catpage'); ?>

                            <?php endwhile;

                            reactor_loop_after();
                        
                        endif; ?>

                    </div>
                
                </div><!-- .columns -->
                
                <?php get_sidebar(); ?>
                
            </div><!-- .row -->
        </div><!-- #content -->
        
        <?php reactor_content_after(); ?>
        
	</div><!-- #primary -->

<?php get_footer(); ?>