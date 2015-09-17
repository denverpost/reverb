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
    //confiure Outbrainvariables
    var outbrainurl = '<?php echo get_permalink(); ?>';
</script>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-60685156-1', 'auto');
  ga('send', 'pageview');
</script>

</head>

<body <?php body_class( 'gesture' ); ?>>

<div id="omniture" style="display:none">
    <script type="text/javascript">var s_account="mngireverb"</script>
    <script type="text/javascript" src="http://extras.mnginteractive.com/live/omniture/sccore.js"></script>
    <script type="text/javascript">
        s.trackExternalLinks = false
        s.pageName = document.title
        s.channel = "Reverb"
        s.prop1 = "D=g"
        s.prop2 = "Reverb/?"
        s.prop3 = "Reverb/?/?"
        s.prop4 = "Reverb/?/?/?"
        s.prop5 = "Reverb/?/?/?/" + document.title
        var s_code=s.t();if(s_code)document.write(s_code)
    </script>
    <noscript><img src="http://denverpost.112.2O7.net/b/ss/denverpost/1/H.17--NS/0" height="1" width="1" border="0" alt="" /></noscript>
</div>

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
                    googletag.defineSlot('/8013/heyreverb.com/', [970,30], 'sbb_reverb').setTargeting('pos',['sbb']).setTargeting('kv', 'reverb').addService(googletag.pubads());
                    googletag.pubads().enableSyncRendering();
                    googletag.enableServices();
                    googletag.display('sbb_reverb');
                }
                </script>
            </div>
            <!-- end DFP Premium Ad Tag -->
        </div>

        <div id="main" class="wrapper">
