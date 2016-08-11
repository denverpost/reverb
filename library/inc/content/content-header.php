<?php
/**
 * Header Content
 * hook in the content for header.php
 *
 * @package Reactor
 * @author Anthony Wilhelm (@awshout / anthonywilhelm.com)
 * @since 1.0.0
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 * @license GNU General Public License v2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 */

/**
 * Site meta, title, and favicon
 * in header.php
 * 
 * @since 1.0.0
 */
function reactor_do_reactor_head() { ?>
<meta charset="<?php bloginfo('charset'); ?>" />
<title><?php wp_title('|', true, 'right'); ?></title>

<!-- google chrome frame for ie -->
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
   
<!-- mobile meta -->
<meta name="HandheldFriendly" content="True">
<meta name="MobileOptimized" content="320">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

<?php

function convert_smart_quotes($string)  { 
    $search = array('&lsquo;','&rsquo;','&ldquo;','&rdquo;');
    $replace = array('&#039;','&#039;','&#034;','&#034;');
    return str_replace($search, $replace, $string); 
}

global $post;

//Twitter Cards
$twitter_thumbs = '';
$temp_post = '';
$temp_auth = '';
$temp_gplus = '';
$twitter_desc = get_bloginfo( 'description' );
if ( is_home() || is_front_page() ) {
	$twitter_url 	= get_bloginfo( 'url' );
	$twitter_title 	= get_bloginfo( 'name' );
	$GLOBALS['dfmcat'][0] = 'Home';
} else if ( is_category() ) {
	$id 			= get_query_var( 'cat' );
    $twitter_desc_temp = category_description( $id );
    $twitter_desc 	= ( strlen( $twitter_desc_temp ) > 0 ) ? strip_tags( category_description( $id ) ) : $twitter_desc;
    $twitter_url 	= get_category_link( $id );
    $twitter_title 	= get_cat_name( $id ) . ' - ' . get_bloginfo( 'name' );
    $GLOBALS['dfmcat'][0] = get_cat_name( $id );
} else if ( is_tag() ) {
	$tag_slug 		= get_query_var( 'tag' );
	$tag 			= get_term_by('slug', $tag_slug, 'post_tag');
    $twitter_desc 	= 'Articles tagged '. $tag->name . ' - ' . get_bloginfo( 'description' );
    $twitter_url 	= get_tag_link( (int)$tag->term_id );
    $twitter_title 	= $tag->name . ' - ' . get_bloginfo( 'name' );
} else if ( is_singular() ) {
    $twitter_thumbs = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large');
    $twitter_desc   = strip_tags(get_the_excerpt());
    $twitter_desc   = convert_smart_quotes(htmlentities($twitter_desc, ENT_QUOTES, 'UTF-8'));
    $twitter_url    = get_permalink();
    $twitter_title  = get_the_title();
    $temp_post 		= get_post($post->ID);
    $temp_auth 		= get_the_author_meta('twitter', $post->post_author);
    $temp_gplus 	= get_the_author_meta('googleplus', $post->post_author);
    $GLOBALS['dfmcat'][0] = ( ( $category[0]->category_parent != ( '' || null) ) ? get_cat_name($category[0]->category_parent) : $category[0]->cat_name );
    $GLOBALS['dfmcat'][1] = ( ( $category[0]->category_parent != ( '' || null) ) ? $category[0]->cat_name : '');
    $GLOBALS['dfmid'] = $post->ID;
    $GLOBALS['dfmby'] = get_the_author_meta('display_name', $post->post_author);
}
$twitter_thumb = ( ($twitter_thumbs != '') ? $twitter_thumbs[0] : get_stylesheet_directory_uri() . '/images/facebooklogo600.jpg' );
$twitter_user_id = ( ($temp_post != '') && is_single() ) ? $temp_post->post_author : '@RVRB';
$twitter_creator = ( ($temp_auth != '') && is_single() ) ? '@' . $temp_auth : '@RVRB';
echo ( ($temp_gplus != '') && is_single() ) ? '<link rel="author" href="' . $temp_gplus . '" />' : '<link rel="publisher" href="http://plus.google.com/100931264054788579031" />';
?>

<meta name="twitter:card" content="<?php echo ( is_single() ) ? 'summary_large_image' : 'summary'; ?>" />
<meta name="twitter:site" content="@RVRB" />
<meta name="twitter:creator" content="<?php echo $twitter_creator; ?>" />
<meta name="twitter:url" content="<?php echo $twitter_url; ?>" />
<meta name="twitter:title" content="<?php echo $twitter_title; ?>" />
<meta name="twitter:description" content="<?php echo $twitter_desc; ?>" />
<meta name="twitter:image:src" content="<?php echo $twitter_thumb; ?>" />
<meta name="twitter:domain" content="heyreverb.com" />

<meta property="fb:app_id" content="682160958485333"/>
<meta property="og:title" content="<?php echo $twitter_title; ?>" />
<meta property="og:type" content="<?php echo ( is_single() ) ? 'article' : 'blog'; ?>" />
<meta property="og:url" content="<?php echo $twitter_url; ?>" />
<meta property="og:image" content="<?php echo $twitter_thumb; ?>" />
<meta property="og:site_name" content="<?php bloginfo('name') ?>" />
<meta property="og:description" content="<?php echo $twitter_desc; ?>" />
<meta property="article:publisher" content="http://www.facebook.com/heyreverb" />

<?php if ( !is_singular() ) { ?>
  <!-- <meta http-equiv="refresh" content="1800"> -->
<?php } ?>
<meta name="dcterms.audience" content="Global" />
<?php echo ( (get_post_meta(get_the_ID(), 'sponsored_link', true) != '') ? '<meta name="Googlebot-News" content="noindex,follow">' : '' ); ?>
<meta name="robots" content="follow, all" />

<meta name="dcterms.rightsHolder" content="The Denver Post" />
<meta name="dcterms.rights" content="All content copyright The Denver Post or other copyrighth holders. All rights reserved." />
<meta name="dcterms.dateCopyrighted" content="<?php echo date_i18n('Y'); ?>" />

<meta name="description" content="<?php echo $twitter_desc; ?>" />
<meta name="news_keywords" content="colorado, reviews, music<?php
$GLOBALS['rel_art'] = '';
if (has_tag() ) {
    $posttags = get_the_tags();
    foreach($posttags as $tag) {
        $GLOBALS['rel_art'] .= ', ' . $tag->name;
    }
    echo $GLOBALS['rel_art'];
    } ?>" />
<meta name="keywords" content="colorado, reviews, music<?php
    echo $GLOBALS['rel_art'];
    ?>" />

<?php $favicon_uri = reactor_option('favicon_image') ? reactor_option('favicon_image') : get_stylesheet_directory_uri() . '/favicon.ico'; ?>
<link rel="shortcut icon" href="<?php echo $favicon_uri; ?>">
<link href='http://fonts.googleapis.com/css?family=Open+Sans:600|Candal|Josefin+Sans:400,400i,700,700i|Arvo' rel='stylesheet' type='text/css'>

<?php 
}
add_action('wp_head', 'reactor_do_reactor_head', 1);

/**
 * Top bar
 * in header.php
 * 
 * @since 1.0.0
 */
function reactor_do_top_bar() {
	if ( has_nav_menu('top-bar-l') || has_nav_menu('top-bar-r') ) {
		$topbar_args = array(
			'title'     => reactor_option('topbar_title', get_bloginfo('name')),
			'title_url' => reactor_option('topbar_title_url', home_url()),
			'fixed'     => reactor_option('topbar_fixed', 0),
			'sticky'    => reactor_option('topbar_sticky', 0),
			'contained' => reactor_option('topbar_contain', 1),
			'search'    => reactor_option('topbar_search', 0),
		);
		reactor_top_bar( $topbar_args );
	}
}
add_action('reactor_header_after', 'reactor_do_top_bar', 1);

/**
 * Site title, tagline, logo, and nav bar
 * in header.php
 * 
 * @since 1.0.0
 */
function reactor_do_title_logo() { ?>
	<div class="inner-header">
		<div class="row" style="background-image:url('<?php echo get_stylesheet_directory_uri(); ?>/images/background.jpg');">
                <div class="site-logo">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
						<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/site-logo.png" alt="<?php echo esc_attr( get_bloginfo('name', 'display') ); ?> logo">
						<?php if ( is_front_page() ) { ?>
						<h1 style="display:none;"><?php echo get_bloginfo('name') . ' - ' . get_bloginfo('description'); ?></h1>
						<?php } ?>
					</a>
				</div><!-- .site-logo -->
				<div class="site-social right">
					<a href="http://twitter.com/rvrb" class="twitter" style="background-image:url('<?php echo get_stylesheet_directory_uri(); ?>/images/social-icons.png');"><div class="social-space"></div></a>
					<a href="http://www.facebook.com/heyreverb" class="facebook" style="background-image:url('<?php echo get_stylesheet_directory_uri(); ?>/images/social-icons.png');"><div class="social-space"></div></a>
					<a href="http://instagram.com/heyreverb" class="instagram" style="background-image:url('<?php echo get_stylesheet_directory_uri(); ?>/images/social-icons.png');"><div class="social-space"></div></a>
					<a href="<?php echo get_site_url(); ?>/feed/" class="rss" style="background-image:url('<?php echo get_stylesheet_directory_uri(); ?>/images/social-icons.png');"><div class="social-space"></div></a>
				</div>
		</div><!-- .row -->
	</div><!-- .inner-header -->  
<?php 
}
//add_action('reactor_header_inside', 'reactor_do_title_logo', 1);

/**
 * Nav bar and mobile nav button
 * in header.php
 * 
 * @since 1.0.0
 */
function reactor_do_nav_bar() { 
	if ( has_nav_menu('main-menu') ) {
		$nav_class = ( reactor_option('mobile_menu', 1) ) ? 'class="hide-for-small" ' : ''; ?>
		<div class="main-nav">
			<nav id="menu" <?php echo $nav_class; ?>role="navigation">
				<div class="section-container horizontal-nav" data-section="horizontal-nav" data-options="one_up:false;">
					<?php reactor_main_menu(); ?>
				</div>
			</nav>
		</div><!-- .main-nav -->
		
	<?php	
	if ( reactor_option('mobile_menu', 1) ) { ?>       
		<div id="mobile-menu-button" class="show-for-small">
			<button class="secondary button" id="mobileMenuButton" href="#mobile-menu">
				<span class="mobile-menu-icon"></span>
				<span class="mobile-menu-icon"></span>
				<span class="mobile-menu-icon"></span>
			</button>
		</div><!-- #mobile-menu-button -->             
	<?php }
	}
}
add_action('reactor_header_inside', 'reactor_do_nav_bar', 2);

/**
 * Mobile nav
 * in header.php
 * 
 * @since 1.0.0
 */
function reactor_do_mobile_nav() {
	if ( reactor_option('mobile_menu', 1) && has_nav_menu('main-menu') ) { ?> 
		<nav id="mobile-menu" class="show-for-small" role="navigation">
			<div class="section-container accordion" data-section="accordion" data-options="one_up:false">
				<?php reactor_main_menu(); ?>
			</div>
		</nav>
<?php }
}
add_action('reactor_header_after', 'reactor_do_mobile_nav', 1);
?>
