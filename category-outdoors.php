<?php
/**
 * Template Name: Outdoors Page
 *
 * @package Reactor
 * @subpackage Page-Templates
 * @since 1.0.0
 */
?>

<?php get_header(); ?>

	<div id="primary" class="site-content">
    
    	<?php reactor_content_before(); ?>
  
        <div id="content" role="main">
        	<div class="row">

                <div class="large-8 medium-12 small-12 columns" id="outdoorsmain">

                    <?php
                    $query_cat = $wp_query->get_queried_object();
                    $class_header_category = ' category-' . tkno_get_top_category_slug( true, $query_cat->cat_id );
                    $class_category = 'archive-title  category-' . $query_cat->slug; ?>
                    <header class="archive-header<?php echo $class_header_category; ?>">
                        <h1 class="<?php echo $class_category; ?>""><a href="javascript:void(0);" class="noclick"><?php echo $query_cat->name; ?></a></h1>
                        <?php get_sidebar('categorysponsor'); ?>
                    </header><!-- .archive-header -->

                    <div class="article_wrapper_top">
                        <?php get_template_part('loops/loop', 'outdoortop'); ?>
                    </div>

                    <div class="neighborhood-map-form">
                        <div class="map-expander"></div>
                        <?php do_shortcode( '[leaflet-map zoomcontrol="1"]' ); ?>
                    </div>

                    <div class="large-12 medium-6 small-12" id="outdoor-upper">

                        <?php get_sidebar('outdoorupper'); ?>

                    </div><!-- .columns -->
                
                    <?php reactor_inner_content_before(); ?>
                
                    <div class="article_wrapper">
                        <?php get_template_part('loops/loop', 'outdoors'); ?>
                    </div>

                    <div class="clear"></div>
                        
                    <?php reactor_inner_content_after(); ?>

                    <div class="large-12 medium-6 small-12" id="outdoor-middle">

                        <?php get_sidebar('outdoormiddle'); ?>

                    </div><!-- .columns -->

                </div><!-- .columns -->

				<?php get_sidebar('outdoors'); ?>
                
            </div><!-- .row -->
        </div><!-- #content -->
        
        <?php reactor_content_after(); ?>
        
	</div><!-- #primary -->

<?php get_footer(); ?>
