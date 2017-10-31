<?php

// Fix for unix timestamps createing non-timezone-adjusted times in Google results
function tkno_fix_c_time_format( $date, $format, $timestamp, $gmt ) {
    if ( 'c' == $format )
        $date = date_i18n( DATE_ISO8601, $timestamp, $gmt );
    return $date;
}
add_filter( 'date_i18n', 'tkno_fix_c_time_format', 10, 4 );

// add a favicon to the site
function blog_favicon() {
    echo '<link rel="shortcut icon" type="image/x-icon" href="'.get_bloginfo('stylesheet_directory').'/favicon.ico" />' . "\n";
}
add_action('wp_head', 'blog_favicon');
add_action('admin_head', 'blog_favicon');

// Hide the Wordpress admin bar for everyone
function my_function_admin_bar(){ return false; }
add_filter( 'show_admin_bar' , 'my_function_admin_bar');

/* ----- [ Display Co-Authors In RSS ] ----- */
function coauthors_in_rss( $the_author ) {
    if ( is_feed() && function_exists( 'coauthors' ) ) {
        return coauthors( null, null, null, null, false );
    } else {
        return $the_author;
    }
}
add_filter( 'the_author', 'coauthors_in_rss' );

// Disable those annoying pingbacks from our own posts
function disable_self_trackback( &$links ) {
  foreach ( $links as $l => $link )
        if ( 0 === strpos( $link, get_option( 'home' ) ) )
            unset($links[$l]);
}
add_action( 'pre_ping', 'disable_self_trackback' );

// Disables automatic Wordpress core updates:
define( 'WP_AUTO_UPDATE_CORE', false );

//This function intelligently trims a body of text to a certain number of words, but will not break a sentence.
if ( ! function_exists( 'smart_trim' ) ) {
    function smart_trim($instring, $truncation) {
        //remove shortcodes (and thereby images and embeds)
        $instring = strip_shortcodes( $instring );
        //a little regex kills scripts
        $instring = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $instring);
        //a little more regex kills datelines
        $instring = preg_replace("/\A((([A-Z ]+)\\,\s?([a-zA-Z ]+)\\.?)|[A-Z]+)\s?(&#8211;|&#8212;?)\s?/u", "", $instring);
        //replace closing paragraph tags with a space to avoid collisions after punctuation
        $instring = str_replace("</p>", " ", $instring);
        //strip the HTML tags and then kill the entities
        $string = html_entity_decode( strip_tags($instring), ENT_QUOTES, 'UTF-8');

        $matches = preg_split("/\s+/", $string);
        $count = count($matches);

        if($count > $truncation) {
        //Grab the last word; we need to determine if
        //it is the end of the sentence or not
        $last_word = strip_tags($matches[$truncation-1]);
        $lw_count = strlen($last_word);

        //The last word in our truncation has a sentence ender
        if($last_word[$lw_count-1] == "." || $last_word[$lw_count-1] == "?" || $last_word[$lw_count-1] == "!") {
            for($i=$truncation;$i<$count;$i++) {
            unset($matches[$i]);
            }

        //The last word in our truncation doesn't have a sentence ender, find the next one
        } else {
            //Check each word following the last word until
            //we determine a sentence's ending
            $ending_found = false;
            for($i=($truncation);$i<$count;$i++) {
            if($ending_found != true) {
                $len = strlen(strip_tags($matches[$i]));
                if($matches[$i][$len-1] == "." || $matches[$i][$len-1] == "?" || $matches[$i][$len-1] == "!") {
                //Test to see if the next word starts with a capital
                if( isset($matches[$i+1][0]) && $matches[$i+1][0] == strtoupper($matches[$i+1][0])) {
                    $ending_found = true;
                }
                }
            } else {
                    unset($matches[$i]);
            }
            }
        }
        $body = implode(' ', $matches);
        return $body;
        } else {
        return $string;
        }
    }
}

/**
 * Include posts from authors in the search results where
 * either their display name or user login matches the query string
 *
 * @author danielbachhuber
 */
function tkno_filter_authors_search( $posts_search ) {

    // Don't modify the query at all if we're not on the search template
    // or if the LIKE is empty
    if ( !is_search() || empty( $posts_search ) )
        return $posts_search;

    global $wpdb;
    // Get all of the users of the blog and see if the search query matches either
    // the display name or the user login
    add_filter( 'pre_user_query', 'tkno_filter_user_query' );
    $search = sanitize_text_field( get_query_var( 's' ) );
    $args = array(
        'count_total' => false,
        'search' => sprintf( '*%s*', $search ),
        'search_fields' => array(
            'display_name',
            'user_login',
        ),
        'fields' => 'ID',
    );
    $matching_users = get_users( $args );
    remove_filter( 'pre_user_query', 'tkno_filter_user_query' );
    // Don't modify the query if there aren't any matching users
    if ( empty( $matching_users ) )
        return $posts_search;
    // Take a slightly different approach than core where we want all of the posts from these authors
    $posts_search = str_replace( ')))', ")) OR ( {$wpdb->posts}.post_author IN (" . implode( ',', array_map( 'absint', $matching_users ) ) . ")))", $posts_search );
    return $posts_search;
}
add_filter( 'posts_search', 'tkno_filter_authors_search' );

/**
 * Modify get_users() to search display_name instead of user_nicename
 */
function tkno_filter_user_query( &$user_query ) {

    if ( is_object( $user_query ) )
        $user_query->query_where = str_replace( "user_nicename LIKE", "display_name LIKE", $user_query->query_where );
    return $user_query;
}

// allow script tags in editor
function tkno_allow_script_tags( $allowedposttags ) {
    if ( !current_user_can( 'publish_posts' ) )
        return $allowedposttags;
    $allowedposttags['script'] = array(
        'src' => true,
        'async' => true,
        'defer' => true,
    );
    $allowedposttags['iframe'] = array(
        'align' => true,
        'width' => true,
        'height' => true,
        'frameborder' => true,
        'name' => true,
        'src' => true,
        'id' => true,
        'class' => true,
        'style' => true,
        'scrolling' => true,
        'marginwidth' => true,
        'marginheight' => true,
        'seamless' => true
    );
    return $allowedposttags;
}
add_filter('wp_kses_allowed_html','tkno_allow_script_tags', 1, 1);

// allow HTML5 data- atributes for NDN videos
function tkno_filter_allowed_html($allowed, $context){
    if (is_array($context)) {
        return $allowed;
    }
    if ($context === 'post') {
        $allowed['div']['data-config-widget-id'] = true;
        $allowed['div']['data-config-widget-pb'] = true;
        $allowed['div']['data-config-type'] = true;
        $allowed['div']['data-config-tracking-group'] = true;
        $allowed['div']['data-config-playlist-id'] = true;
        $allowed['div']['data-config-video-id'] = true;
        $allowed['div']['data-config-site-section'] = true;
        $allowed['div']['data-config-width'] = true;
        $allowed['div']['data-config-height'] = true;
    }
    return $allowed;
}
add_filter('wp_kses_allowed_html', 'tkno_filter_allowed_html', 10, 2);

function tkno_add_excerpts_to_pages() {
    add_post_type_support( 'page', 'excerpt' );
}
add_action( 'init', 'tkno_add_excerpts_to_pages' );

/**
 * Widget Custom Classes
 */
function tkno_widget_form_extend( $instance, $widget ) {
    if ( !isset($instance['classes']) )
    $instance['classes'] = null;
    $row = "<p>\n";
    $row .= "\t<label for='widget-{$widget->id_base}-{$widget->number}-classes'>Class:\n";
    $row .= "\t<input type='text' name='widget-{$widget->id_base}[{$widget->number}][classes]' id='widget-{$widget->id_base}-{$widget->number}-classes' class='widefat' value='{$instance['classes']}'/>\n";
    $row .= "</label>\n";
    $row .= "</p>\n";
    echo $row;
    return $instance;
}
add_filter('widget_form_callback', 'tkno_widget_form_extend', 10, 2);

function tkno_widget_update( $instance, $new_instance ) {
    $instance['classes'] = $new_instance['classes'];
        return $instance;
    }
add_filter( 'widget_update_callback', 'tkno_widget_update', 10, 2 );

function tkno_dynamic_sidebar_params( $params ) {
    global $wp_registered_widgets;
    $widget_id     = $params[0]['widget_id'];
    $widget_obj    = $wp_registered_widgets[$widget_id];
    $widget_opt    = get_option($widget_obj['callback'][0]->option_name);
    $widget_num    = $widget_obj['params'][0]['number'];
    if ( isset($widget_opt[$widget_num]['classes']) && !empty($widget_opt[$widget_num]['classes']) )
        $params[0]['before_widget'] = preg_replace( '/class="/', "class=\"{$widget_opt[$widget_num]['classes']} ", $params[0]['before_widget'], 1 );
    return $params;
}
add_filter( 'dynamic_sidebar_params', 'tkno_dynamic_sidebar_params' );

// Disable both Twitter Cards and OG tags
add_filter( 'jetpack_enable_open_graph', '__return_false', 99 );

// Disable only the Twitter Cards
add_filter( 'jetpack_disable_twitter_cards', '__return_true', 99 );

// Add body classes for mobile destection for swiping stuff
function browser_body_class($classes) {
    global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;
 
    if($is_lynx) $classes[] = 'lynx';
    elseif($is_gecko) $classes[] = 'gecko';
    elseif($is_opera) $classes[] = 'opera';
    elseif($is_NS4) $classes[] = 'ns4';
    elseif($is_safari) $classes[] = 'safari';
    elseif($is_chrome) $classes[] = 'chrome';
    elseif($is_IE) $classes[] = 'ie';
    else $classes[] = 'unknown';
    if($is_iphone) $classes[] = 'iphone';
    return $classes;
}
add_filter( 'body_class', 'browser_body_class' );

/**
 * Attempt to de-dupe the homepage results
 */
function tkno_exclude_duplicates( &$query ) {
    if ( ( ! is_front_page() && ! is_outdoor_home() ) || $query->get('adp_disable') ) return;
    global $adp_posts;
    if ( empty( $query->post__not_in ) ) {
        $query->set( 'post__not_in', $adp_posts );
    }
}
add_action( 'parse_query', 'tkno_exclude_duplicates' );

function tkno_log_posts( $posts ) {
    $adp_posts = array(); 
    if ( ! is_front_page() && ! is_outdoor_home() ) return $posts;
    global $adp_posts;
    foreach ( $posts as $i => $post ) {
        $adp_posts[] = $post->ID;
    }
    return $posts;
}
add_filter( 'the_posts', 'tkno_log_posts', 10, 1 );

/*
Plugin Name: Default to GD
Plugin URI: http://wordpress.org/extend/plugins/default-to-gd
Description: Sets GD as default WP_Image_Editor class.
Author: Mike Schroder
Version: 1.0
Author URI: http://www.getsource.net/
*/
function ms_image_editor_default_to_gd( $editors ) {
    $gd_editor = 'WP_Image_Editor_GD';
    $editors = array_diff( $editors, array( $gd_editor ) );
    array_unshift( $editors, $gd_editor );
    return $editors;
}
add_filter( 'wp_image_editors', 'ms_image_editor_default_to_gd' );

/**
 * Removes "smart" characters from word processors and replaces them with the correct html safe characters
 * @param: sting $str - The string to be fixed
 * @return: cleaned string
 */
function replace_smart_chars( $str ) {
       
        // Replace the smart quotes that cause question marks to appear
        $str = str_replace(
                array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
                array("'", "'", '"', '"', '-', '--', '...'), $str);
       
        // Replace the smart quotes that cause question marks to appear
        $str = str_replace(
                array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)),
                array("'", "'", '"', '"', '-', '--', '...'), $str);
       
        // Replace special chars (tm) (c) (r)
        $str = str_replace(
                array('™', '©', '®'),
                array('&trade;', '&copy;', '&reg;'), $str);
       
        // Return the fixed string
        return $str;
}

// Add filters to modify the content before saving to the database
add_filter( 'content_save_pre', 'replace_smart_chars' );
add_filter( 'title_save_pre',   'replace_smart_chars' );

// Hide the Wordpress SEO canonical for posts that already have one from Autoblog
function tkno_wpseo_canonical_override( $canonical ) {
    global $post;
    if ( is_singular() && get_post_meta( $post->ID, 'original_guid' ) ) {
        $meta_canonical = get_post_meta( $post->ID, 'original_guid' );
        $canonical = $meta_canonical[0];
    }
    return $canonical;
}
add_filter( 'wpseo_canonical', 'tkno_wpseo_canonical_override' );

// Use the Headline title set in WP for OpenGraph tags instead of the SEO title
function tkno_wpseo_og_title_override( $title ) {
    if ( is_singular() && $post = get_queried_object() ) {
        if ( $_title = get_the_title() )
            $title = $_title;
    }

    return $title;
}
add_filter( 'wpseo_opengraph_title', 'tkno_wpseo_og_title_override' );

function tkno_wpseo_hide_metaboxes(){
    remove_meta_box('wpseo_meta', 'location', 'normal');
    remove_meta_box('wpseo_meta', 'venues', 'normal');
    remove_meta_box('wpseo_meta', 'neighborhoods', 'normal');
}
add_action( 'add_meta_boxes', 'tkno_wpseo_hide_metaboxes',11 );

// Increase Custom Field Limit
function tkno_customfield_limit_increase( $limit ) {
    $limit = 100;
    return $limit;
}
add_filter( 'postmeta_form_limit' , 'tkno_customfield_limit_increase' );

/**
 * dequeue WP Email, Contact Form 7 and Gallery Slideshow scripts when not necessary
 */
function tkno_dequeue_scripts() {
    if( is_singular() ) {
        $post = get_post();
        if( ! has_shortcode( $post->post_content, 'gallery' ) ) {
            wp_dequeue_script( 'cycle2' );
            wp_dequeue_script( 'cycle2_center' );
            wp_dequeue_script( 'cycle2_carousel' );
            wp_dequeue_script( 'gss_js' );
            wp_dequeue_script( 'gss_custom_js' );
            wp_dequeue_style( 'gss_css' );
        }
        if( ! has_shortcode( $post->post_content, 'contact-form-7' ) ) {
            wp_dequeue_script( 'contact-form-7' );
            wp_dequeue_style( 'contact-form-7' );
        }
    }
    wp_dequeue_style( 'wp-email' );
}
add_action( 'wp_enqueue_scripts', 'tkno_dequeue_scripts', 99 );

/**
 * Remove jquery migrate and move jquery to footer
 */ 
function tkno_remove_jquery_migrate( &$scripts)
{
    if(!is_admin())
    {
        $scripts->remove( 'jquery');
        $scripts->add( 'jquery', false, array( 'jquery-core' ), '1.10.2' );
    }
}
add_filter( 'wp_default_scripts', 'tkno_remove_jquery_migrate' );

/**
 * deregister stupid wP emoji BS
 */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

/**
* Add theme support for Responsive Videos.
*/
function jetpackme_responsive_videos_setup() {
    add_theme_support( 'jetpack-responsive-videos' );
}
add_action( 'after_setup_theme', 'jetpackme_responsive_videos_setup' );

/**
 * deregister unused Jetpack CSS
 */ 
function tkno_remove_all_jp_css() {
  wp_deregister_style( 'AtD_style' ); // After the Deadline
  wp_deregister_style( 'jetpack_likes' ); // Likes
  wp_deregister_style( 'jetpack_related-posts' ); //Related Posts
  wp_deregister_style( 'jetpack-carousel' ); // Carousel
  wp_deregister_style( 'the-neverending-homepage' ); // Infinite Scroll
  wp_deregister_style( 'infinity-twentyten' ); // Infinite Scroll - Twentyten Theme
  wp_deregister_style( 'infinity-twentyeleven' ); // Infinite Scroll - Twentyeleven Theme
  wp_deregister_style( 'infinity-twentytwelve' ); // Infinite Scroll - Twentytwelve Theme
  wp_deregister_style( 'noticons' ); // Notes
  wp_deregister_style( 'post-by-email' ); // Post by Email
  wp_deregister_style( 'publicize' ); // Publicize
  wp_deregister_style( 'sharedaddy' ); // Sharedaddy
  wp_deregister_style( 'sharing' ); // Sharedaddy Sharing
  wp_deregister_style( 'stats_reports_css' ); // Stats
  wp_deregister_style( 'jetpack-widgets' ); // Widgets
  wp_deregister_style( 'jetpack-slideshow' ); // Slideshows
  wp_deregister_style( 'presentations' ); // Presentation shortcode
  wp_deregister_style( 'tiled-gallery' ); // Tiled Galleries
  wp_deregister_style( 'widget-conditions' ); // Widget Visibility
  wp_deregister_style( 'jetpack_display_posts_widget' ); // Display Posts Widget
  wp_deregister_style( 'gravatar-profile-widget' ); // Gravatar Widget
  wp_deregister_style( 'widget-grid-and-list' ); // Top Posts widget
}
if ( ! is_admin() ) {
    add_filter( 'jetpack_implode_frontend_css', '__return_false' );
    add_action('wp_print_styles', 'tkno_remove_all_jp_css' );
}