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
        );
    
    global $wp_query; 
    $wp_query = new WP_Query( $args ); ?>

    <?php if ( $wp_query->have_posts() ) : ?>
    
    <?php reactor_loop_before(); ?>

        <?php $i=0; ?>
    	
        <?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>

            <?php echo ( $i == 0 ) ? '<div class="cat-main">' : ''; ?>
                    <?php echo ( $i == 1 ) ? '<div class="row"><div class="large-6 medium-6 small-12 columns cat-second">' : ''; ?>
                    <?php echo ( $i == 2 ) ? '<div class="large-6 medium-6 small-12 columns cat-second">' : ''; ?>
			
            			<?php reactor_post_before(); ?>

                        <?php get_template_part('post-formats/format', 'catpage'); ?>

                        <?php reactor_post_after(); ?>

                <?php echo ( $i == 0 || $i == 1 ) ? '</div>' : ''; ?>

            <?php echo ( $i == 2 ) ? '</div></div>' : ''; ?>

            <?php $i++; ?>

        <?php endwhile; // end of the post loop ?>

	<?php endif; ?>
        
    <?php reactor_loop_after(); ?>