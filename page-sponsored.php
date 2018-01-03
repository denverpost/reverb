<?php
/**
 * Template Name: Sponsored template
 *
 * The default template for displaying pages
 *
 * @package Reactor
 * @subpackge Templates
 * @since 1.0.0
 */
?>

<?php get_header(); ?>

	<div id="primary" class="site-content full-width">
    
        <div id="content" role="main">
		    <div class="row">
		        <div class="large-8 columns 2c-l">
		            <article class="post type-post status-publish format-standard has-post-thumbnail hentry single">
		                <div class="entry-body">
		                    <header class="entry-header">
		                        <h1 class="entry-title"><!-- @Title --></h1>
		                    </header><!-- .entry-header -->
		                    <div class="entry-content">
		                        <!-- @Content -->
		                    </div><!-- .entry-content -->
		                </div><!-- .entry-body -->
		            </article>
		        </div><!-- .columns -->
		        <div id="sidebar" class="sidebar large-4 medium-6 small-12 columns" role="complementary">
		        </div><!-- #sidebar -->
		    </div>
		    <!-- .row -->
		</div>
        
	</div><!-- #primary -->

<?php get_footer(); ?>