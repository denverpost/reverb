<?php

// Get an acceptable top-level category name and ID, or slug, for classes and labels
function tkno_get_ad_cat_slug($cat_id=false) {
    global $post;
    $curr_cat = get_the_category_list( '/' , 'multiple', $post->ID );
    $valid_cats = array('movies-and-tv','museums-and-galleries','stage','audio-video','musicnews','music-photos','reverb-features','music-reviews','family-friendly','outdoors','photos','dining-news','restaurant-reviews','bars-and-clubs','beer');
    $curr_cat = explode( '/', $curr_cat );
    $return_cat = array();
    foreach ( $curr_cat as $current ) {
        $current = sanitize_title( strtolower( $current ) );
        if ( in_array( $current, $valid_cats ) ) {
            $return_cat['slug'] = $current;
            break;
        }
    }
    if ( ! empty( $return_cat['slug'] ) ) { 
        $cat_for_name = get_category_by_slug( $return_cat['slug'] );
        $return_cat['cat_name'] = $cat_for_name->name;
        $return_cat['term_id'] = $cat_for_name->term_id;
        return (object) $return_cat;
    } else if ( empty( $return_cat['slug'] ) && tkno_get_top_category_slug( $cat_id ) != false ) {
        return tkno_get_top_category_slug( $cat_id );
    } else {
        return false;
    }
}

function tkno_get_ad_value() {
    $category = FALSE;
    $kv = 'theknow';
    $tax = '';
    if ( is_home() || is_front_page() ) {
        $kv = 'theknow';
    } else if ( is_page_template( 'page-templates/calendar.php' ) ) {
        $category = 'calendar';
    } else if ( is_category() ) {
        $id = get_query_var( 'cat' );
        $cat = get_category( (int)$id );
        $category = $cat->slug;
    } else if ( is_single() && get_post_type() != 'venues' && get_post_type() != 'neighborhoods' && get_post_type() != 'location' ) {
        $cats = tkno_get_ad_cat_slug();
        $category = $cats->slug;
    } else if ( get_post_type() == 'venues' ) {
        $category = 'venues';
    } else if ( get_post_type() == 'neighborhoods' ) {
        $category = 'neighborhoods';
    } else if ( get_post_type() == 'location' ) {
        $category = 'location';
    }
    if ( $category ) {
        switch ( $category ) {
            case 'calendar':
                $kv = 'calendar';
                $tax = '/Play/Event-calendar';
                break;
            case 'venues':
                $kv = 'venues';
                $tax = '/Venues';
                break;
            case 'neighborhoods':
                $kv = 'neighborhoods';
                $tax = '/Neighborhoods';
                break;
            case 'location':
                $kv = 'location';
                $tax = '/Location';
                break;
            case 'movies-and-tv':
                $kv = 'movies-and-tv';
                $tax = '/See/Movies-and-tv';
                break;
            case 'museums-galleries':
                $kv = 'museums-galleries';
                $tax = '/See/Museums-and-galleries';
                break;
            case 'stage':
                $kv = 'stage';
                $tax = '/See/Stage';
                break;
            case 'audio-video':
                $kv = 'audio-video';
                $tax = '/Hear/Audio-video';
                break;
            case 'musicnews':
                $kv = 'musicnews';
                $tax = '/Hear/Music-news';
                break;
            case 'music-photos':
                $kv = 'music-photos';
                $tax = '/Hear/Music-photos';
                break;
            case 'reverb-features':
                $kv = 'reverb-features';
                $tax = '/Hear/Reverb-features';
                break;
            case 'music-reviews':
                $kv = 'music-reviews';
                $tax = '/Hear/Reviews';
                break;
            case 'family-friendly':
                $kv = 'family-friendly';
                $tax = '/Play/Family-friendly';
                break;
            case 'outdoors':
                $kv = 'outdoors';
                $tax = '/Play/Outdoors';
                break;
            case 'photos':
                $kv = 'photos';
                $tax = '/Play/Photos';
                break;
            case 'dining-news':
                $kv = 'dining-news';
                $tax = '/Eat/Dining-news';
                break;
            case 'restaurant-reviews':
                $kv = 'restaurant-reviews';
                $tax = '/Eat/Restaurant-reviews';
                break;
            case 'bars-and-clubs':
                $kv = 'bars-and-clubs';
                $tax = '/Drink/Bars-and-clubs';
                break;
            case 'beer':
                $kv = 'beer';
                $tax = '/Drink/Beer';
                break;
            case 'drink':
                $kv = 'drink';
                $tax = '/Drink';
                break;
            case 'food':
                $kv = 'eat';
                $tax = '/Eat';
                break;
            case 'music':
                $kv = 'hear';
                $tax = '/Hear';
                break;
            case 'things-to-do':
                $kv = 'play';
                $tax = '/Play';
                break;
            case 'arts':
                $kv = 'see';
                $tax = '/See';
                break;
            case 'photos':
                $kv = 'photos';
                $tax = '/Photos';
                break;
            default:
                $kv = 'theknow';
                $tax = '';
        }
    }
    if ( is_single() && has_tag( 'top-chef-in-colorado' ) ) {
        $kv = 'Top-Chef-in-Colorado';
    }
    return array( $kv, $tax );
}

// Set up a section vs. article targetting response for ad tags
function tkno_get_ad_target_page() {
    if ( is_single() ) {
        return '.setTargeting("page",["article"])';
    } else if ( is_category() || is_tag() || is_tax( 'venues' ) ) {
        return '.setTargeting("page",["section"])';
    } else {
        return '';
    }
}

class sidebar_ad_widget_top_cube extends WP_Widget
{
    public function __construct()
    {
            parent::__construct(
                'sidebar_ad_widget_top_cube',
                __('Sidebar Ad - Top-of-rail Cube', 'sidebar_ad_widget_top_cube'),
                array('description' => __('Big ads are a key component of the online browsing experience. Designed to be used at the top of the right rail.', 'sidebar_ad_widget_top_cube'), )
            );
    }

    public function widget($args, $instance)
    {
        // It's a big ad.
        $ad_tax = tkno_get_ad_value();
        echo '
            <!-- ##ADPLACEMENT## -->
            <div id="cube1_reverb_wrap" class="widget ad_wrap">
                <div>
                    <script>
                        if ( window.innerWidth > 540 ) {
                            googletag.defineSlot(\'/8013/denverpost.com/TheKnow' . $ad_tax[1] . '\', [[300,250],[300,600]], \'cube1_reverb\').setTargeting(\'pos\',[\'Cube1_RRail_ATF\']).setTargeting(\'kv\', \'' . $ad_tax[0] . '\')' . tkno_get_ad_target_page() . '.addService(googletag.pubads());
                            googletag.pubads().enableSyncRendering();
                            googletag.enableServices();
                            googletag.display(\'cube1_reverb\');
                        }
                    </script>
                </div>
            </div>';
    }
}
function register_ad_widget_large_cube() { register_widget('sidebar_ad_widget_top_cube'); }
add_action( 'widgets_init', 'register_ad_widget_large_cube' );

class mobile_sidebar_ad_widget_top_cube extends WP_Widget
{
    public function __construct()
    {
            parent::__construct(
                'mobile_sidebar_ad_widget_top_cube',
                __('Sidebar Ad - Mobile-Only Top Cube', 'mobile_sidebar_ad_widget_top_cube'),
                array('description' => __('Big ads are a key component of the online browsing experience. Place above first Flexible Posts Widget for mobile-only display.', 'mobile_sidebar_ad_widget_top_cube'), )
            );
    }

    public function widget($args, $instance)
    {
        // It's a big ad.
        $ad_tax = tkno_get_ad_value();
        echo '
            <!-- ##ADPLACEMENT## -->
            <div id="cube1_reverb_wrap" class="widget ad_wrap">
                <div>
                    <script>
                        if ( window.innerWidth <= 540 ) {
                            googletag.defineSlot(\'/8013/denverpost.com/TheKnow' . $ad_tax[1] . '\', [300,250], \'cube1_reverb\').setTargeting(\'pos\',[\'Cube1_RRail_ATF\']).setTargeting(\'kv\', \'' . $ad_tax[0] . '\')' . tkno_get_ad_target_page() . '.addService(googletag.pubads());
                            googletag.pubads().enableSyncRendering();
                            googletag.enableServices();
                            googletag.display(\'cube1_reverb\');
                        }
                    </script>
                </div>
            </div>';
    }
}
function register_mobile_ad_widget_large_cube() { register_widget('mobile_sidebar_ad_widget_top_cube'); }
add_action( 'widgets_init', 'register_mobile_ad_widget_large_cube' );

class sidebar_ad_widget_cube extends WP_Widget
{
    public function __construct()
    {
            parent::__construct(
                'sidebar_ad_widget_cube',
                __('Sidebar Ad - Secondary Cube', 'sidebar_ad_widget_cube'),
                array('description' => __('Ads are a key component of the online browsing experience. Use for ad positions below the top of the right rail.', 'sidebar_ad_widget_cube'), )
            );
    }

    public function widget($args, $instance)
    {
        // It's an ad.
        $ad_tax = tkno_get_ad_value();
        echo '
            <!-- ##ADPLACEMENT## -->
            <div id="cube2_reverb_wrap" class="widget ad_wrap">
                <div>
                    <script>
                    googletag.defineSlot(\'/8013/denverpost.com/TheKnow' . $ad_tax[1] . '\', [300,250], \'cube2_reverb\').setTargeting(\'pos\',[\'Cube2_RRail_mid\']).setTargeting(\'kv\', \'' . $ad_tax[0] . '\')' . tkno_get_ad_target_page() . '.addService(googletag.pubads());
                    googletag.pubads().enableSyncRendering();
                    googletag.enableServices();
                    googletag.display(\'cube2_reverb\');
                    </script>
                </div>
            </div>';
    }
}
function register_ad_widget_cube() { register_widget('sidebar_ad_widget_cube'); }
add_action( 'widgets_init', 'register_ad_widget_cube' );