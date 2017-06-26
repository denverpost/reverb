<?php
/**
 * Template Name: Neighborhood Pages
 * 
 * The template for displaying single neighborhoods
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
                
					<?php global $post;
                    // start the loop
                    while ( have_posts() ) : the_post(); ?>
                    
                    <?php reactor_post_before(); ?>
                        
					<?php // get post format and display code for that format
                    if ( !get_post_format() ) : get_template_part('post-formats/format', 'single'); 
					else : get_template_part('post-formats/format', get_post_format() ); endif; ?>
                    
                    <?php reactor_post_after(); ?>
        
                    <?php endwhile; // end of the loop ?>
                    
                <?php reactor_inner_content_after(); ?>

                <?php get_sidebar('neighborhoodlower'); ?>

                <?php global $post;
                    $neighborhood_slug = get_post_meta( $post->ID, 'neighborhood_slug', true );
                    $neighborhood_name = get_term_by( 'slug', $neighborhood_slug, 'neighborhood' ); ?>
                
                <div class="neighborhood-related">
                    <h2 class="archive-title"><a class="noclick" href="javascript:void(0);">Recent stories from <?php echo $neighborhood_name->name; ?></a></h2>

                    <?php

                        $args = array(
                            'post_type' => 'post',
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'neighborhood',
                                    'field' => 'slug',
                                    'terms' => array( $neighborhood_name->slug )
                                ),
                            ),
                            'posts_per_page' => 10,
                            );
                        
                        $neighborhood_query = new WP_Query( $args ); ?>

                        <?php if ( $neighborhood_query->have_posts() ) :
                            
                            while ( $neighborhood_query->have_posts() ) : $neighborhood_query->the_post(); ?>

                                <?php get_template_part('post-formats/format', 'catpage'); ?>

                            <?php endwhile;
                        
                        endif; ?>

                    </div>
                
                </div><!-- .columns -->
                
                <?php get_sidebar(); ?>
                
            </div><!-- .row -->
        </div><!-- #content -->
        
        <?php reactor_content_after(); ?>
        
	</div><!-- #primary -->

<?php get_footer(); ?>