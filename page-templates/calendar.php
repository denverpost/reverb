<?php
/**
 * Template Name: Calendar
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
                <div class="<?php reactor_columns( array( 12, 12, 12 ) ); ?> primary">

                    <header class="archive-header">
                        <h1 class="archive-title post type-post status-publish format-standard hentry category-things-to-do"><a href="javascript:void(0);" class="noclick">Things to do</a></h1>
                    </header>

                   <script type="text/javascript" src="//portal.CitySpark.com/PortalScripts/TheKnow" > </script>
                
                </div><!-- .columns -->
                
            </div><!-- .row -->
        </div><!-- #content -->
        
        <?php reactor_content_after(); ?>
        
	</div><!-- #primary -->

<?php get_footer(); ?>