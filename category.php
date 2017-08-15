<?php
/**
 * The template for displaying posts by category
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
                    $query_cat = $wp_query->get_queried_object();
                    $class_category = 'archive-title category-' . tkno_get_top_category_slug( true, $query_cat->cat_id );
                    $class_header_category = ' category-' . tkno_get_top_category_slug( true, $query_cat->cat_id ); ?>
                    <header class="archive-header<?php echo $class_header_category; ?>">
                        <h1 <?php post_class( $class_category ); ?>><a href="javascript:void(0);" class="noclick"><?php echo $query_cat->name; ?></a></h1>
                        <?php get_sidebar('categorysponsor'); ?>
                    </header><!-- .archive-header -->
                <?php endif; // end have_posts() check ?> 
                
				<?php // get the loop
				get_template_part('loops/loop', 'catpage'); ?>
                
                <?php reactor_inner_content_after(); ?>
                
                </div><!-- .columns -->
                
                <?php get_sidebar(); ?>
                
            </div><!-- .row -->
        </div><!-- #content -->
        
        <?php reactor_content_after(); ?>
        
	</div><!-- #primary -->

<?php get_footer(); ?>