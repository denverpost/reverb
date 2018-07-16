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
            <!-- begin DFP Premium Ad Tag -->
            <div>
                <script type='text/javascript'>
                <?php $ad_tax = tkno_get_ad_value(); ?>
                if ( document.getElementById("adPosition1").offsetWidth >= 970 ) {
                    document.write('<style type="text/css">#adPosition1 { margin:15px auto 0; }</style>');
                    googletag.defineSlot('/8013/denverpost.com/TheKnow<?php echo $ad_tax[1]; ?>', [[728,90],[970,250],[970,30]], 'top_leaderboard').setTargeting('pos',['top_leaderboard']).setTargeting('kv', ['<?php echo implode('\',\'',array_filter($ad_tax[0])) ?>'])<?php echo tkno_get_ad_target_page(); ?>.addService(googletag.pubads());
                    googletag.pubads().enableSyncRendering();
                    googletag.enableServices();
                    googletag.display('top_leaderboard');
                } else if ( document.getElementById("adPosition1").offsetWidth >= 728 ) {
                    document.write('<style type="text/css">#adPosition1 { margin:15px auto 0; }</style>');
                    googletag.defineSlot('/8013/denverpost.com/TheKnow<?php echo $ad_tax[1]; ?>', [728,90], 'top_leaderboard').setTargeting('pos',['top_leaderboard']).setTargeting('kv', ['<?php echo implode('\',\'',array_filter($ad_tax[0])) ?>'])<?php echo tkno_get_ad_target_page(); ?>.addService(googletag.pubads());
                    googletag.pubads().enableSyncRendering();
                    googletag.enableServices();
                    googletag.display('top_leaderboard');
                } else {
                    document.write('<style type="text/css">#adPosition1 { margin:10px auto 0; }</style>');
                    googletag.defineSlot('/8013/denverpost.com/TheKnow<?php echo $ad_tax[1]; ?>', [[300,50],[320,50],[320,100]], 'top_leaderboard').setTargeting('pos',['top_leaderboard']).setTargeting('kv', ['<?php echo implode('\',\'',array_filter($ad_tax[0])) ?>'])<?php echo tkno_get_ad_target_page(); ?>.addService(googletag.pubads());
                    googletag.pubads().enableSyncRendering();
                    googletag.enableServices();
                    googletag.display('top_leaderboard');
                }
                </script>
            </div>
            <!-- end DFP Premium Ad Tag -->
        </div>
        <?php } ?>

        <div id="main" class="wrapper">
