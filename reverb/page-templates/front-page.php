<?php
/**
 * Template Name: Front Page
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

                <div class="large-12 medium-12 small-12 columns" id="frontupper">
                    <?php get_sidebar('frontupper'); ?>
                </div>

                <div class="large-8 medium-12 small-12 columns" id="frontpagemain">
                
                <?php reactor_inner_content_before(); ?>
                        
                <?php get_template_part('loops/loop', 'frontpage'); ?>

                <div class="clear"></div>
                    
                <?php reactor_inner_content_after(); ?>

                <?php get_sidebar('frontlower'); ?>
                
                </div><!-- .columns -->

				<?php get_sidebar('primary'); ?>
                
            </div><!-- .row -->
        </div><!-- #content -->
        
        <?php reactor_content_after(); ?>
        
	</div><!-- #primary -->

<?php get_footer(); ?>
