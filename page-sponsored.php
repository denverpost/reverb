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
                <div class="<?php reactor_columns(12,12,12); ?>">
                                
                    <div id="nativo_author"><!-- @Author --></div>
					<div id="nativo_authorlogo"><!-- @Authorlogo --></div>
					<div id="nativo_title"><!-- @Title --></div>
					<div id="nativo_headline"><!-- @Headline --></div>
					<div id="nativo_content"><!-- @Content --></div>
					<!-- Right Rail Container -->
					<div id="rr-width-container"></div>

                </div><!-- .columns -->
                
            </div><!-- .row -->
        </div><!-- #content -->
        
	</div><!-- #primary -->

<?php get_footer(); ?>