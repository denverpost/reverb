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
<?php reactor_head(); ?>

<script type="text/javascript">
    //configure Chartbeat variables
    var _sf_startpt=(new Date()).getTime();
</script>

</head>

<body <?php body_class( 'gesture' ); ?>>

	<?php reactor_body_inside(); ?>

    <div id="page" class="hfeed site"> 
    
        <?php reactor_header_before(); ?>
    
        <header id="header" class="site-header" role="banner">
                    
                    <?php reactor_header_inside(); ?>

        </header><!-- #header -->
        
        <?php reactor_header_after(); ?>

        <div class="adElement clearfloat" id="adPosition1" style="clear:both;text-align:center;">
            <!-- begin DFP Premium Ad Tag -->
            <div id='sbb_reverb'>
                <script type='text/javascript'>
                if ( document.getElementById("adPosition1").offsetWidth >= 970 ) {
                    document.write('<style type="text/css">#adPosition1 { margin:15px auto 0; }</style>');
                    googletag.defineSlot('/8013/heyreverb.com', [970,30], 'sbb_reverb').setTargeting('pos',['sbb']).setTargeting('kv', 'reverb').addService(googletag.pubads());
                    googletag.pubads().enableSyncRendering();
                    googletag.enableServices();
                    googletag.display('sbb_reverb');
                }
                </script>
            </div>
            <!-- end DFP Premium Ad Tag -->
        </div>

        <div id="main" class="wrapper">
