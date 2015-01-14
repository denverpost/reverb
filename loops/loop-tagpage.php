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
        'tag_id' => get_term_by('name', single_tag_title("",false), 'post_tag' )->term_id,
        'paged' => get_query_var( 'paged' ),
        );
    
    global $wp_query; 
    $wp_query = new WP_Query( $args ); ?>

    <?php if ( $wp_query->have_posts() ) : ?>
    
    <?php reactor_loop_before(); ?>
    	
        <?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
			
			<?php reactor_post_before(); ?>

            <?php get_template_part('post-formats/format', 'catpage'); ?>

            <?php reactor_post_after(); ?>

        <?php endwhile; // end of the post loop ?>

	<?php endif; ?>
        
    <?php reactor_loop_after(); ?>