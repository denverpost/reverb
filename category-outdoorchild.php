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

                    <div class="article_wrapper_top">
                        <?php get_template_part('loops/loop', 'outdoortop'); ?>
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

                    <div class="large-12 medium-6 small-12" id="sidebar-outdoormiddle">

                        <?php tkno_outdoor_children_below(); ?>

                    </div><!-- .columns -->

                </div><!-- .columns -->

				<?php get_sidebar('outdoors'); ?>
                
            </div><!-- .row -->
        </div><!-- #content -->
        
        <?php reactor_content_after(); ?>
        
	</div><!-- #primary -->

<?php get_footer(); ?>
