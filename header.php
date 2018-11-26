<?php
/**
 * The template for displaying the header
 *
 * @package Reactor
 * @subpackge Templates
 * @since 1.0.0
 */?><!DOCTYPE html>
<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if ( IE 7 )&!( IEMobile )]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if ( IE 8 )&!( IEMobile )]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->

<head>

<!-- WordPress head -->
<?php wp_head(); ?>
<!-- end WordPress head -->
<?php reactor_head();
$outdoor_class = ( is_outdoors() || is_location() ) ? ' is-outdoors' : '';
$outdoor_page_class = ( is_outdoor_home() ) ? ' outdoorhome' : ''; ?>

<script>
    var gptadslots = [];
    var googletag = googletag || {cmd:[]};
</script>

</head>

<body <?php body_class( 'gesture' . $outdoor_class . $outdoor_page_class ); ?>>

    <?php reactor_body_inside(); ?>

    <div id="page" class="hfeed site"> 
    
        <?php reactor_header_before(); ?>
    
        <header id="header" class="site-header" role="banner">
                    
                    <?php reactor_header_inside(); ?>

        </header><!-- #header -->
        
        <?php reactor_header_after(); ?>

        <?php if ( ! is_page_template( 'page-sponsored.php' ) ) { ?>
        <div class="adElement clearfloat" id="adPosition1" style="clear:both;text-align:center;">
            
            <!-- begin top leaderboard and interstitial -->
            	 <div class="header-banners">
				<div id="div-gpt-ad-interstitial" class="dfp-ad dfp-interstitial" data-ad-unit="interstitial">
					<script type="text/javascript">
						if ( "undefined" !== typeof googletag ) {
							googletag.cmd.push( function() { googletag.display("div-gpt-ad-interstitial"); } );
						}
					</script>
				</div>				<div id="div-gpt-ad-top_leaderboard" class="dfp-ad dfp-top_leaderboard" data-ad-unit="top_leaderboard">
					<script type="text/javascript">
						if ( "undefined" !== typeof googletag ) {
							googletag.cmd.push( function() { googletag.display("div-gpt-ad-top_leaderboard"); } );
						}
					</script>
				</div>			
			 </div>
            <!-- end top leaderboard and interstitial -->
            
        </div>
        <?php } ?>

        <div id="main" class="wrapper">
