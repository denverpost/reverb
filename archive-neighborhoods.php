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
                        'paged' => get_query_var( 'paged' ),
                        );
                    
                    global $wp_query; 
                    $wp_query = new WP_Query( $args ); ?>

                    <?php if ( $wp_query->have_posts() ) : ?>

                        <?php reactor_loop_before(); ?>
                
                            <header class="page-header">
                                <h1 class="archive-title neighborhood-header"><span>Neighborhoods</span></h1>
                            </header>
                    
                    <?php reactor_loop_before(); ?>
                        
                        <?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
                            
                            <?php reactor_post_before(); ?>

                            <?php get_template_part('post-formats/format', 'catpage'); ?>

                            <?php reactor_post_after(); ?>

                        <?php endwhile; // end of the post loop ?>

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