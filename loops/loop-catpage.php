<?php
/**
 * The loop for displaying posts on the category page template
 *
 * @package reverb
 * @subpackage loops
 * @since 0.1
 */
?>

<?php // the get options
$number_posts = 25;
    
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => $number_posts,
        'cat' => get_cat_id( single_cat_title("",false) ),
        'paged' => get_query_var( 'paged' ),
        'orderby' => 'date',
        'order' => 'DESC',
        );
    
    global $wp_query; 
    $wp_query = new WP_Query( $args ); ?>

    <?php if ( $wp_query->have_posts() ) : ?>
    
    <?php reactor_loop_before(); ?>
    	
        <?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>

            <?php echo ( $wp_query->current_post == 0 ) ? '<div class="cat-main">' : ''; ?>
                    <?php echo ( $wp_query->current_post == 1 ) ? '<div class="row cat-top"><div class="large-6 medium-6 small-12 columns cat-main">' : ''; ?>
                    <?php echo ( $wp_query->current_post == 2 ) ? '<div class="large-6 medium-6 small-12 columns cat-main">' : ''; ?>
			
            			<?php reactor_post_before(); ?>

                        <?php get_template_part('post-formats/format', 'catpage'); ?>

                        <?php reactor_post_after(); ?>

                <?php echo ( $wp_query->current_post == 0 || ( $wp_query->current_post == 1 && $wp_query->post_count > 2 ) ) ? '</div><!-- #end row-single -->' : ''; ?>

            <?php echo ( $wp_query->current_post == 2 || ( $wp_query->current_post == 1 && $wp_query->post_count == 2 ) ) ? '</div></div><!-- #end row-double -->' : ''; ?>

        <?php endwhile; // end of the post loop ?>

	<?php endif; ?>
        
    <?php reactor_loop_after(); ?>