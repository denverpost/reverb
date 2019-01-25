<?php
/**
 * Template Name: Quick Trip
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
                    
                    <?php //reactor_post_before(); ?>

                        <h2 class="entry-title">
                            <?php the_title(); ?>
                        </h2>
					<?php  /* // get post format and display code for that format
                    if ( !get_post_format() ) : get_template_part('post-formats/format', 'single'); 
					else : get_template_part('post-formats/format', get_post_format() ); endif; */?>
                    
                    <?php reactor_post_after(); ?>
        
                    <?php endwhile; // end of the loop ?>
                    
                <?php reactor_inner_content_after(); ?>

                
                </div><!-- .columns -->
                
                <?php get_sidebar(); ?>
                
            </div><!-- .row -->
        </div><!-- #content -->
        
        <?php reactor_content_after(); ?>
        
	</div><!-- #primary -->

<?php get_footer(); ?>