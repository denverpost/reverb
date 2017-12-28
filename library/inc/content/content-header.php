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
if ( is_home() || is_front_page() ) {
	$twitter_url 	= get_bloginfo( 'url' );
	$twitter_title 	= get_bloginfo( 'name' );
	$GLOBALS['dfmcat'][0] = 'Home';
} else if ( is_category() ) {
	$id 			= get_query_var( 'cat' );
    $twitter_url 	= get_category_link( $id );
    $twitter_title 	= get_cat_name( $id ) . ' - ' . get_bloginfo( 'name' );
    $GLOBALS['dfmcat'][0] = get_cat_name( $id );
} else if ( is_tag() ) {
	$tag_slug 		= get_query_var( 'tag' );
	$tag 			= get_term_by('slug', $tag_slug, 'post_tag');
    $twitter_url 	= get_tag_link( (int)$tag->term_id );
    $twitter_title 	= $tag->name . ' - ' . get_bloginfo( 'name' );
} else if ( is_post_type_archive( 'venues' ) ) {
	$GLOBALS['dfmcat'][0] = 'venues';
} else if ( is_post_type_archive( 'neighborhoods' ) ) {
	$GLOBALS['dfmcat'][0] = 'neighborhoods';
} else if ( is_post_type_archive( 'location' ) ) {
	$GLOBALS['dfmcat'][0] = 'location';
} else if ( is_singular() ) {
    $twitter_thumbs = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large');
    $twitter_url    = get_permalink();
    $twitter_title  = get_the_title();
    $temp_post 		= get_post($post->ID);
    $temp_auth 		= get_the_author_meta('twitter', $post->post_author);
    $temp_gplus 	= get_the_author_meta('googleplus', $post->post_author);
    $categories_list = ( tkno_get_top_category_slug() ) ? tkno_get_top_category_slug() : 'none';
    if ( get_post_type() == 'venues' ) {
    	$GLOBALS['dfmcat'][0] = 'venues';
    } else if ( get_post_type() == 'neighborhoods' ) {
    	$GLOBALS['dfmcat'][0] = 'neighborhoods';
    } else if ( get_post_type() == 'location' ) {
    	$GLOBALS['dfmcat'][0] = 'location';
    } else if ( $categories_list != 'none' ) {
    	$GLOBALS['dfmcat'][0] = $categories_list->cat_name;
    } else {
    	$GLOBALS['dfmcat'][0] = $categories_list;
    }
    $GLOBALS['dfmby'] = get_the_author_meta('display_name', $post->post_author);
}
$facebook_image = ( is_outdoors() ) ? '/images/facebook-share-outdoors.jpg' : '/images/facebooklogo600.jpg';
$twitter_thumb = ( ($twitter_thumbs != '') ? $twitter_thumbs[0] : get_stylesheet_directory_uri() . $facebook_image );
$twitter_user_id = ( ($temp_post != '') && is_single() ) ? $temp_post->post_author : '@thknwco';
$twitter_creator = ( ($temp_auth != '') && is_single() ) ? '@' . $temp_auth : '@thknwco';
echo ( ($temp_gplus != '') && is_single() ) ? '<link rel="author" href="' . $temp_gplus . '" />' : '<link rel="publisher" href="http://plus.google.com/100931264054788579031" />';
?>

<meta property="fb:pages" content="113250288696719">

<meta name="dcterms.audience" content="Global" />
<?php if ( is_page_template( 'page-sponsored.php' ) || stripos( get_query_var( 'post_mime_type' ), 'image' ) !== false ) {
		echo ( (get_post_meta(get_the_ID(), 'sponsored_link', true) != '') ? '<meta name="Googlebot-News" content="noindex,follow">' : '' ); ?>
		<meta name="robots" content="noindex,nofollow" />
<?php } else { ?>
		<meta name="robots" content="follow,follow" />		
<?php } ?>

<meta name="dcterms.rightsHolder" content="The Denver Post" />
<meta name="dcterms.rights" content="All content copyright The Denver Post or other copyright holders. All rights reserved." />
<meta name="dcterms.dateCopyrighted" content="<?php echo date_i18n('Y'); ?>" />

<meta name="news_keywords" content="colorado, reviews, music<?php
$GLOBALS['rel_art'] = '';
if (has_tag() ) {
    $posttags = get_the_tags();
    foreach($posttags as $tag) {
        $GLOBALS['rel_art'] .= ', ' . $tag->name;
    }
    echo $GLOBALS['rel_art'];
    } ?>" />
<meta name="keywords" content="colorado, outdoors, events, entertainment<?php
    echo $GLOBALS['rel_art'];
    ?>" />

<?php $favicon_uri = reactor_option('favicon_image') ? reactor_option('favicon_image') : get_stylesheet_directory_uri() . '/favicon.ico'; ?>
<link rel="shortcut icon" href="<?php echo $favicon_uri; ?>">
<link href='http://fonts.googleapis.com/css?family=Open+Sans|Arvo' rel='stylesheet' type='text/css'>

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
