<?php
/**
 * The template for displaying search results
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
                
                <?php reactor_inner_content_before(); ?>
                
			<?php if ( have_posts() ) :
				$results_total = $wp_query->found_posts;
				if ( $paged == 0 ) {
					$results_first = 1;
					$results_last = $wp_query->query_vars['posts_per_page'];
				} else {
					$results_first = 1 + ($wp_query->query_vars['posts_per_page'] * ( $paged - 1 ) );
					if ( $wp_query->post_count == $wp_query->query_vars['posts_per_page'] ) {
						$results_last = $wp_query->query_vars['posts_per_page'] * ( $paged );
					} else {
						$results_last = ( $wp_query->query_vars['posts_per_page'] * ( $paged - 1 ) ) + $wp_query->post_count;
					}
				}
				$entries = ( $wp_query->post_count == 1 ) ? 'entry' : 'entries';
				?>
			
				<?php reactor_loop_before(); ?>
				
                	        <header class="page-header">
                        	<h1 class="page-title"><span class="searchresults">Showing <?php echo $entries; ?> <strong><?php echo $results_first; ?></strong> <?php if ( $wp_query->post_count != 1 && $paged != 0 ): ?> to <strong><?php echo $results_last; ?></strong> <?php endif; ?>(of <?php echo $results_total; ?>) for</span> <?php echo get_search_query(); ?></h1>
                    		</header> 

				<?php // start the loop
				while ( have_posts() ) : the_post(); ?>
				
					<?php // get post format and display template for that format
					if ( !get_post_format() ) : get_template_part('post-formats/format', 'catpage');
					else : get_template_part('post-formats/catpage', get_post_format()); endif; ?>
					
				<?php endwhile; ?>
				
				<?php reactor_loop_after(); ?>
				
				<?php // if no posts are found
				else : reactor_loop_else(); ?>
				
			<?php endif; // end have_posts() check ?> 
                
                <?php reactor_inner_content_after(); ?>
                
                </div><!-- .columns -->
                
                <?php get_sidebar(); ?>
                
            </div><!-- .row -->
        </div><!-- #content -->
        
        <?php reactor_content_after(); ?>
        
	</div><!-- #primary -->

<?php get_footer(); ?>
