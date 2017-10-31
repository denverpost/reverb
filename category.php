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
                    $parent_category = get_category( $query_cat->category_parent );
                    $class_category_outdoors = '';
                    $class_header_category = ' category-' . tkno_get_top_category_slug( true, $query_cat->cat_id );
                    if ( is_outdoors() && $parent_category->slug != 'outdoors' && tkno_get_top_category_slug( true, $query_cat->cat_id ) == 'outdoors' ) {
                        $class_category_outdoors = ' category-' . $parent_category->slug;
                    }
                    $class_category = 'archive-title  category-' . $query_cat->slug . $class_category_outdoors; ?>
                    <header class="archive-header<?php echo $class_header_category; ?>">
                        <h1 class="<?php echo $class_category; ?>"><a href="javascript:void(0);" class="noclick"><?php echo $query_cat->name; ?></a></h1>
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