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
        );
    
    global $catpage_query; 
    $catpage_query = new WP_Query( $args ); ?>

    <?php if ( $catpage_query->have_posts() ) : ?>
    
    <?php reactor_loop_before(); ?>
    	
        <?php while ( $catpage_query->have_posts() ) : $catpage_query->the_post(); ?>
			
			<?php reactor_post_before(); ?>

            <?php get_template_part('post-formats/format', 'catpage'); ?>

            <?php reactor_post_after(); ?>

        <?php endwhile; // end of the post loop ?>

	<?php endif; ?>
        
    <?php reactor_loop_after(); ?>