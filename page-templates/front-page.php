<?php
/**
 * Template Name: Front Page
 *
 * @package Reactor
 * @subpackge Page-Templates
 * @since 1.0.0
 */
?>

<?php // get the options
$slider_category = reactor_option('frontpage_slider_category', ''); ?>

<?php get_header(); ?>

	<div id="primary" class="site-content">
    
    	<?php reactor_content_before(); ?>
  
        <div id="content" role="main">
        	<div class="row">

                <div class="large-6 large-push-2 medium-12 small-12 columns" id="frontpagemain">
                
                <?php reactor_inner_content_before(); ?>
                        
                    <?php get_template_part('loops/loop', 'frontpage'); ?>
                    
                <?php reactor_inner_content_after(); ?>
                
                </div><!-- .columns -->

                <div class="large-2 large-pull-6 medium-6 small-12 columns" id="frontpageleft">

                    <?php get_sidebar('frontpagetwo'); ?>

                </div><!-- .columns -->

				<?php get_sidebar('primary'); ?>
                
            </div><!-- .row -->
        </div><!-- #content -->
        
        <?php reactor_content_after(); ?>
        
	</div><!-- #primary -->

<?php get_footer(); ?>
