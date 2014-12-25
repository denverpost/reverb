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
  
        <div class="row">
          	<div class="<?php reactor_columns( 12 ); ?>">
                <?php // slider function passing category from options
				reactor_slider( array(
					'category' => $slider_category,
					'slider_id' => 'slider-front-page',
					'data_options' => array(
						'animation' => '\'fade\'',
						'pause_on_hover' => 'false',
						),
					) ); ?>
            </div><!-- .columns -->
        </div><!-- .row -->
  
        <div id="content" role="main">
        	<div class="row">

                <div class="large-9 medium-12 small-12 columns">

                    <div class="row">

                        <div class="large-9 large-push-3 medium-9 medium-push-3 small-12 columns">
                        
                        <?php reactor_inner_content_before(); ?>
                                
                            <?php get_template_part('loops/loop', 'frontpage'); ?>
                            
                        <?php reactor_inner_content_after(); ?>
                        
                        </div><!-- .columns -->

                        <div class="large-3 large-pull-9 medium-3 medium-pull-9 columns">

                            <?php get_sidebar('frontpagetwo'); ?>

                        </div><!-- .columns -->

                    </div>

                </div>

                <div class="large-3 medium-12 small-12 columns">

    				<?php get_sidebar('frontpage'); ?>
                </div><!-- .columns -->
            </div><!-- .row -->
        </div><!-- #content -->
        
        <?php reactor_content_after(); ?>
        
	</div><!-- #primary -->

<?php get_footer(); ?>
