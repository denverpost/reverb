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

<?php $GLOBALS['dfmcat'] = '';
  $GLOBALS['dfmby'] = '';
  $GLOBALS['dfmid'] = '';
  $GLOBALS['dfmthetitle'] = ( is_single() || is_page() ) ? html_entity_decode(get_the_title(), ENT_QUOTES) : wp_title('|', false, '');
  $GLOBALS['dfmcat'] = array('','');
if ( is_category() ) {
    $category = get_the_category();
    $GLOBALS['dfmcat'][0] = $category[0]->cat_name;
} else if ( is_single() ) {
    //$category = get_the_category();
    $post_terms = wp_get_object_terms( $post->ID, 'category', array( 'fields' => 'ids' ) );
    $separator = ', ';
    $term_ids = implode( ',' , $post_terms );
    $category = get_categories(array('hierarchical'=>true,'taxonomy'=>'category','order'=>'desc','number'=>1,'include'=>$term_ids));
    $GLOBALS['dfmcat'][0] = ( ( $category[0]->category_parent != ( '' || null) ) ? get_cat_name($category[0]->category_parent) : $category[0]->cat_name );
    $GLOBALS['dfmcat'][1] = ( ( $category[0]->category_parent != ( '' || null) ) ? $category[0]->cat_name : '');
    $GLOBALS['dfmid'] = $post->ID;
    $GLOBALS['dfmby'] = get_the_author_meta('display_name', $post->post_author);
} else if ( is_tag() ) {
    $GLOBALS['dfmcat'][0] = single_tag_title( '', false );
} else if ( is_front_page() ) {
    $GLOBALS['dfmcat'][0] = 'Home';
} else if ( is_page() ) {
    $GLOBALS['dfmcat'][0] = get_the_title();
} ?>

<script type="text/javascript">
//Configure DFM Variables
    dfm.api("data","siteId",          "Reverb"); //Assign a siteid for your property
    dfm.api("data","siteName",        "Reverb"); //Assign user-friendly name for site
    dfm.api("data","siteDomain",      "heyreverb.com"); //Domain of the News.com, WordPress, Blog
    dfm.api("data","actualDomain",    "www.heyreverb.com");//Full URl of site
    dfm.api("data","localCommonUrl",  "");
    dfm.api("data","localAssetsUrl",  "");
    dfm.api("data","contentId",       "<?php echo $GLOBALS['dfmid']; ?>");
    dfm.api("data","sectionName",     "<?php echo $GLOBALS['dfmcat'][0]; ?>"); //Omniture section/category name
    dfm.api("data","pageId",          "");
    dfm.api("data","pageUrl",         "http://www.heyreverb.com/"); //Full URl of site
    dfm.api("data","pageVanityUrl",   "http://www.heyreverb.com/"); //Full URl of site
    dfm.api("data","pageTitle",       "<?php echo $GLOBALS['dfmthetitle']; ?>");
    dfm.api("data","pageType",        "");
    dfm.api("data","abstract",        "<?php echo $twitter_desc; ?>");
    dfm.api("data","keywords",        "<?php echo substr($GLOBALS['rel_art'], 2); ?>");
    dfm.api("data","title",           "<?php echo $GLOBALS['dfmthetitle']; ?>");
    dfm.api("data","sectionId",       "<?php echo $GLOBALS['dfmcat'][1] != '' ? $GLOBALS['dfmcat'][1] : $GLOBALS['dfmcat'][0]; ?>");
    dfm.api("data","slug",            "<?php echo ( is_single() ? $post->post_name : '' ); ?>");
    dfm.api("data","byline",          "<?php echo $GLOBALS['dfmby']; ?>");
    dfm.api("data","NetworkID",       "8013");
    dfm.api("data","Taxonomy1",       "<?php echo $GLOBALS['dfmcat'][0]; ?>");
    dfm.api("data","Taxonomy2",       "<?php echo $GLOBALS['dfmcat'][1] != '' ? $GLOBALS['dfmcat'][1] : ''; ?>");
    dfm.api("data","Taxonomy3",       "");
    dfm.api("data","Taxonomy4",       "");
    dfm.api("data","kv",              "News");
    dfm.api("data","omnitureAccount", "mngireverb"); //Omniture s_account.
</script>

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

    ga('create', 'UA-42541613-24', 'heyreverb.com');
    ga('send', 'pageview');
</script>

</head>

<body <?php body_class(); ?>>

<div id="omniture">
<!-- SiteCatalyst code version: H.17 Copyright 1997-2005 Omniture, Inc. More info available at http://www.omniture.com -->
  <script type="text/javascript"><!--//
          /* Specify the Report Suite ID(s) to track here */
          var s_account= dfm.api("data","omnitureAccount");
    //--></script>
  <!-- Replaced SiteCatalystCode_H_22_1.js with SiteCatalystCode_H_22_1_NC.js -->
  <script type="text/javascript" src='http://extras.mnginteractive.com/live/js/omniture/SiteCatalystCode_H_22_1_NC.js'></script>
  <script type="text/javascript" src='http://extras.mnginteractive.com/live/js/omniture/OmniUserObjAndHelper.js'></script>
  <script type="text/javascript">

          //Local Variables
          var contentId = dfm.api("data","contentId");
          var PaperBrand = getBrand2(s_account);
          var PageName = dfm.api("data","pageTitle");
          var SectionName = dfm.api("data","sectionName");

      if (contentId != "" && contentId != null && SectionName == "") {
        SectionName = dfm.api("data","sectionId");
      }
      var FriendlyName = SectionName;
          var ArticleTitle = "";
      if (contentId != "" && contentId != null) {
        ArticleTitle = dfm.api("data","pageTitle");
        FriendlyName = FriendlyName + " / "  +  ArticleTitle;
      }
          var domainName = getDomainName();
          userObj = new omniObj();
          userObj.load();
          userObj.update();
          userObj.save();
          /* You may give each page an identifying name, server, and channel on the next lines. */
          s.pageName=FriendlyName;
          s.channel=SectionName; // Same as prop1
          s.server="HEYREVERB";
          s.pageType=""; // Error pages ONLY
          s.prop1="Reverb";
          beanprop2 = ("" != "") && ("" != null) ? "" : "?";
          var escbeanprop2 = escape(beanprop2);
          var unescbeanprop2 = unescape(escbeanprop2);
          var articleId = dfm.api("data","contentId");        
          beanprop3 = ("" != "") && ("" != null) ? "" : "?";
          beanprop4 = ("" != "") && ("" != null) ? "" : "?";
          if(articleId != "" && articleId != null){
              beanprop5 = ("" != "") && ("" != null) ? "" : articleId +"_" +ArticleTitle;
          } else {
              beanprop5 = "?" ;
          }
            s.prop2='D=ch+' + "\"/" + unescbeanprop2 + "\"";
            s.prop3='D=ch+' + "\"/" + unescbeanprop2 + "/" + beanprop3 + "\"";
            s.prop4='D=ch+' + "\"/" + unescbeanprop2 + "/"+ beanprop3 + "/"+ beanprop4 + "\""; // Sub section 3  
            s.prop5='D=ch+' + "\"/" + unescbeanprop2 + "/" + beanprop3 + "/" + beanprop4 + "/" + beanprop5 + "\""; // Sub section 4
          s.prop6=dfm.api("data","Taxonomy1"); // Global - Section  TODO
          s.prop7=dfm.api("data","Taxonomy2"); // Global - Sub section 1  TODO
          s.prop8=dfm.api("data","Taxonomy3"); // Global - Sub section 2  TODO
          var sourceVal = ""; // Source of request, i.e. RSS, flash, etc...
          if(articleId != '' && articleId != null){
              if(s.prop9 == '' || s.prop9 == null){
                s.prop9 = sourceVal;
              }
          }
          s.prop10=""; // Reserved for RSS
          if(articleId != '' && articleId != null){
              s.prop11=ArticleTitle=="null"?"":'D="'+domainName+' / "+v26+" / "+c50';
          }
          s.prop12=dfm.api("data","byline");
          s.prop13=""; // Reserved for article
          s.prop14=""; // Reserved for article
          s.prop15=""; // Reserved for article
          s.prop16=""; // Search
          s.prop17=""; // Search
          s.prop18=""; // Search
          s.prop19=""; // Search
          s.prop20=""; // Reserved for Search
          s.prop21=""; // Reserved for Search
          s.prop22=""; // Reserved for Search
          s.prop23=""; // Reserved for Search
          s.prop24=""; // Reserved for Search
          s.prop25=""; // Reserved for Search
          s.prop26=""; // 3rd Party Vendors
          s.prop27=""; // OneSpot
          s.prop28=""; // Blank
          if(s.prop29 == null || s.prop29 == ''){
              s.prop29="";
          }
          s.prop30=""; // Form Analysis Plugin
          s.prop31=""; // Blank
          s.prop32=""; // Blank
          s.prop33=ArticleTitle=="null"?'D=c40+" / "+c43':'D=c40+" / "+c50+" / "+c9';
          s.prop34=ArticleTitle=="null"?'D=c40+" / "+c43':'D=c40+" / "+c43+" / "+c50';
          s.prop35=ArticleTitle=="null"?'D=v18=" / "+c40+" / "+c43':'D=v18+" / "+c40+" / "+c50';
          s.prop36=isCampaign(getCampaignValue("EADID")+getCampaignValue("CREF"), PaperBrand, PageName, ArticleTitle); // Campaign Tracking Code  + Paper Brand + Page Name
          s.prop37=isCampaign(getCampaignValue("IADID")+getCampaignValue("SOURCE"), PaperBrand, PageName, ArticleTitle); // Affiliate ID + Paper Brand + Page Name
          s.prop38=isCampaign(getCampaignValue("PARTNERID"), PaperBrand, PageName,ArticleTitle); // Internal Referral ID + Paper Brand + Page Name
          s.prop39="";                                                             // Search Engine + Keywords + Paper Brand + Page Name (populated by functions.js)
          s.prop40=PaperBrand;                                                     // Paper Brand
          s.prop41="";                                                             // Blank
          s.prop42="";
          s.prop43=SectionName; //
          s.prop44=ArticleTitle=="null"?"":'D=c40+" / "+c43+" / "+v26';
          s.prop45=ArticleTitle=="null"?"":'D=c40+" / "+c43+" / "+c12';
          s.prop46=getArticleHelperPage(domainName,"22133898",location.href,ArticleTitle); // ArticleID + Special Page Name + Article Title
          s.prop47=""; // Search
          s.prop48=""; // Blank
          s.prop49=""; // Blank
          s.prop50=ArticleTitle;
          /* E-commerce Variables */
          s.campaign=getCiQueryString("EADID")+getCiQueryString("CREF");          //External Campaign - ?EADID=id
          s.state="";
          s.zip="";
          s.events=getEvents(ArticleTitle, s.events);
          s.products="";
          s.purchaseID="";
          s.eVar1=getCiQueryString("PARTNERID");                                  // Internal Campaign - ?PID=id
          s.eVar2=getCiQueryString("IADID")+getCiQueryString("SOURCE");           // Affiliate ID - ?IADID=id
          s.eVar3=getBrandOnChange(PaperBrand);                                             //Paper Brand
          s.eVar4="D=pageName";
          s.eVar5="";
          s.eVar6="";
          s.eVar7="";
          s.eVar8="";
          s.eVar9="";
          s.eVar10="";
          s.eVar11="";
          s.eVar12="";
          s.eVar13="";
          s.eVar14=userObj.fPage|userObj.conPage|userObj.loginConPage?userObj.vType:''; //Visitory Type
          s.eVar15=userObj.fPage|userObj.userIdChange?userObj.userId:'';                     // User ID
          s.eVar16=userObj.conPage?userObj.rType:'';
          s.eVar17="";
          s.eVar18=getUserType();
          s.eVar19=userObj.fPage|userObj.conPage|userObj.aaPage?userObj.regStatus:''; //Registration Status
          s.eVar20=""; // Search
          s.eVar21="";
          s.eVar22="";
          s.eVar23="";                                                      // Refinements
          s.eVar24="D=c43";
          s.eVar25="";
          s.eVar26=articleId;  // article ID
        s.eVar50=getCiQueryString("AADID");
  </script>
  <script type="text/javascript" src='http://extras.mnginteractive.com/live/js/omniture/functions.js'></script>
  <script type="text/javascript"><!--//
        var s_code=s.t();if(s_code)document.write(s_code);
    //--></script>
  <script type="text/javascript"><!--
        if(navigator.appVersion.indexOf('MSIE')>=0)document.write(unescape('%3C')+'\!-'+'-');
    //--></script>
    <noscript><img src="http://NeBnr.112.2O7.net/b/ss/NeBnr/1/H.17--NS/0" height="0" width="0" alt="" style="margin:0;padding:0;" /></noscript>
    <!-- End SiteCatalyst code version: H.17 -->
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
                    googletag.defineSlot('/8013/denverpost.com/Entertainment', [970,30], 'sbb_reverb').setTargeting('pos',['sbb']).setTargeting('kv', 'reverb').addService(googletag.pubads());
                    googletag.pubads().enableSyncRendering();
                    googletag.enableServices();
                    googletag.display('sbb_reverb');
                }
                </script>
            </div>
            <!-- end DFP Premium Ad Tag -->
        </div>

        <div id="main" class="wrapper">
