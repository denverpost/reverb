<?php
/**
 * The template for displaying posts by tag
 *
 * @package Reactor
 * @subpackge Templates
 * @since 1.0.0
 */
?>

<?php
// If there is a venue page already for this venue, let's redirect there instead of this boring listing

$slug = get_term_by( 'name', single_term_title( "", false ), 'venue' )->slug;
$nei_page = tkno_get_venue_from_slug( $slug );

if ( get_post_type( $nei_page ) == 'venues' ) {
    $nei_page_url = get_permalink( $nei_page );
    header("HTTP/1.1 301 Moved Permanently"); 
    header("Location: {$nei_page_url}");
}


?>

<?php get_header(); ?>

	<div id="primary" class="site-content">
    
    	<?php reactor_content_before(); ?>
    
        <div id="content" role="main">
        	<div class="row">
                <div class="<?php reactor_columns(); ?>">
                
                <?php reactor_inner_content_before(); ?>
                
				<?php if ( have_posts() ) : ?>
                    <header class="archive-header venue-header">
                        <h1 <?php post_class('archive-title'); ?>><a href="javascript:void(0);" class="noclick">Stories from <?php echo single_term_title('', false); ?></a></h1>
                    </header><!-- .archive-header -->
                <?php else: // end have_posts() check ?>
                    <header class="archive-header venue-header">
                        <h1 <?php post_class('archive-title'); ?>><a href="javascript:void(0);" class="noclick">Stories from <?php echo single_term_title('', false); ?></a></h1>
                    </header><!-- .archive-header -->
                        <?php $terms = get_terms( array(
                            'taxonomy' => 'venue',
                            'orderby' => 'count',
                            'order' => 'DESC',
                            'hide_empty' => false,
                        ) ); ?>
                        <h2 class="no_neighborhood">No stories from there yet! Try one of these other venues?</h2>
                        <div class="no_neighborhood_list">
                            <ul class="inline-list">
                                <?php foreach ($terms as $term) { ?>
                                    <li><a href="<?php echo get_term_link( $term ); ?>"><?php echo $term->name; ?></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                <?php endif; // end have_posts() check ?> 
                
				<?php // get the loop
				get_template_part('loops/loop', 'venue'); ?>
                
                <?php reactor_inner_content_after(); ?>
                
                </div><!-- .columns -->
                
                <?php get_sidebar(); ?>
                
            </div><!-- .row -->
        </div><!-- #content -->
        
        <?php reactor_content_after(); ?>
        
	</div><!-- #primary -->

<?php get_footer(); ?>