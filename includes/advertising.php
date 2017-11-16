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
    $tax_neighborhood = $category = $outdoorcat = $outdoorkv = FALSE;
    $kv = 'theknow';
    $tax = '';
    $post_kv = false;
    if ( is_single() ) {
        global $post;
        $post_kv = get_post_meta($post->ID, '_post_kv', true);
    }
    if ( $post_kv ) {
        $kv = $post_kv;
        $category = false;
    } else if ( is_home() || is_front_page() ) {
        $kv = 'theknow';
    } else if ( is_page_template( 'page-templates/calendar.php' ) ) {
        $category = 'calendar';
    } else if ( is_outdoors() ) {
        if ( is_category() ) {
            $id = get_query_var( 'cat' );
            $cat = get_category( (int)$id );
            $childcat = $cat->slug;
            $cats = tkno_get_ad_cat_slug();
            $category = $cats->slug;
            $outdoorcat = ( $childcat == $category ) ? '/Outdoors' : '/Outdoors/' . ucfirst( $childcat );
            $outdoorkv = $childcat;
        } else {
            $cats = tkno_get_ad_cat_slug();
            $outdoorcat = ( $cats->slug == 'outdoors' ) ? '/Outdoors' : '/Outdoors/' . ucfirst( $cats->slug );
            $category = $outdoorkv = $cats->slug;
        }
        $category = 'outdoors';
    } else if ( is_category() && ! is_outdoors() ) {
        $id = get_query_var( 'cat' );
        $cat = get_category( (int)$id );
        $category = $cat->slug;
    } else if ( is_single() && ! is_outdoors() && get_post_type() != 'venues' && get_post_type() != 'neighborhoods' && get_post_type() != 'location' ) {
        $cats = tkno_get_ad_cat_slug();
        $category = $cats->slug;
    } else if ( get_post_type() == 'venues' ) {
        $category = 'venues';
    } else if ( is_post_type_archive( 'neighborhoods' ) ) {
        $category = 'neighborhood';
    } else if ( get_post_type() == 'neighborhoods' ) {
        $category = 'neighborhoods';
        global $post;
        $locality = $neighborhood = '';
        $neighborhood_slug = get_post_meta( $post->ID, '_neighborhood_slug', true );
        $neighborhood_child = get_term_by( 'slug', $neighborhood_slug, 'neighborhood' );
        $neighborhood_parent = get_term_topmost_parent( $neighborhood_child->term_id, $neighborhood_child->taxonomy );
        $neighborhood_child_meta = get_option( "neighborhood_$neighborhood_child->term_id" );
        $child_text = ( $neighborhood_child_meta ) ? ucfirst( str_replace( ' ', '-', $neighborhood_child_meta[ 'pretty_name_field' ] ) ) : ucfirst( str_replace( ' ', '-', $neighborhood_child->name ) );
        $neighborhood_parent_meta = get_option( "neighborhood_$neighborhood_parent->term_id" );
        $neighborhood_parent_pretty = ( $neighborhood_parent_meta ) ? ucfirst( str_replace( ' ', '-', $neighborhood_parent_meta[ 'pretty_name_field' ] ) ) : $neighborhood_parent->name;
        $parent_text = ucfirst( str_replace( ' ', '-', $neighborhood_parent_pretty ) );
        if ( $parent_text == $child_text ) {
            $tax_neighborhood = $parent_text;
        } else {
            $tax_neighborhood = $parent_text . '/' . $child_text;
        }
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
            case 'neighborhood':
                $kv = 'neighborhoods';
                $tax = '/Neighborhood';
                break;
            case 'neighborhoods':
                $kv = 'neighborhoods';
                $tax = '/Neighborhood/' . $tax_neighborhood;
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
                $kv = $outdoorkv;
                $tax = $outdoorcat;
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
    if ( is_single() && has_tag( 'top-chef-colorado' ) ) {
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

/**
 * Section-level sponsorship widget
 * @return html list inserted in widget
 */
class category_sponsor_widget extends WP_Widget {
    public function __construct() {
            parent::__construct(
                'category_sponsor_widget',
                __('Category sponsor', 'category_sponsor_widget'),
                array('description' => __('Displays "Powered by" with a logo image linked to a sponsor\'s site.', 'category_sponsor_widget'), )
            );
    }

    public function form( $instance ) {
        $defaults = array( 'sponsor_category' => __( '' ), 'sponsor_image_url' => __( '' ), 'sponsor_link_url' => __( '' ) );
        $instance = wp_parse_args( ( array ) $instance, $defaults ); ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'sponsor_category' ); ?>"><?php _e( 'Category to display sponsorship on:' ); ?></label> 
        <select id="<?php echo $this->get_field_id( 'sponsor_category' ); ?>" name="<?php echo $this->get_field_name( 'sponsor_category' ); ?>" class="widefat" style="width:100%;">
            <option <?php echo ( $instance[ 'sponsor_category' ] == '' ) ? 'selected="selected" ' : ''; ?> value="">&nbsp;</option>
            <?php foreach( get_terms( 'category' ) as $term) { 
                if ( $term->parent == 0 ): ?>
                <option <?php selected( $instance[ 'sponsor_category' ], $term->term_id ); ?> value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
                <?php endif; ?>
            <?php } ?>      
        </select>
        </p>
        <p>
        <label for="<?php echo $this->get_field_id( 'sponsor_image_url' ); ?>"><?php _e( 'URL of sponsor logo image:' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'sponsor_image_url' ); ?>" name="<?php echo $this->get_field_name( 'sponsor_image_url' ); ?>" type="text" value="<?php echo $instance[ 'sponsor_image_url' ]; ?>" />
        </p>
        <p>
        <label for="<?php echo $this->get_field_id( 'sponsor_link_url' ); ?>"><?php _e( 'URL to link logo to:' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'sponsor_link_url' ); ?>" name="<?php echo $this->get_field_name( 'sponsor_link_url' ); ?>" type="text" value="<?php echo $instance[ 'sponsor_link_url' ]; ?>" />
        </p>
    <?php }

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance[ 'sponsor_category' ] = ( ! empty( $new_instance[ 'sponsor_category' ] ) ) ? trim( strip_tags( $new_instance[ 'sponsor_category' ] ) ) : '';
        $instance[ 'sponsor_image_url' ] = ( ! empty( $new_instance[ 'sponsor_image_url' ] ) ) ? trim( strip_tags( $new_instance[ 'sponsor_image_url' ] ) ) : '';
        $instance[ 'sponsor_link_url' ] = ( ! empty( $new_instance[ 'sponsor_link_url' ] ) ) ? trim( strip_tags( $new_instance[ 'sponsor_link_url' ] ) ) : '';
        return $instance;
    }

    public function widget( $args, $instance ) {
        
        $sponsor_category = ( isset( $instance[ 'sponsor_category' ] ) && $instance[ 'sponsor_category' ] != '' ) ? $instance[ 'sponsor_category' ] : false;
        $sponsor_image_url = ( isset( $instance[ 'sponsor_image_url' ] ) && $instance[ 'sponsor_image_url' ] != '' ) ? $instance[ 'sponsor_image_url' ] : false;
        $sponsor_link_url = ( isset( $instance[ 'sponsor_link_url' ] ) && $instance[ 'sponsor_link_url' ] != '' ) ? $instance[ 'sponsor_link_url' ] : false;
        if ( is_category( $sponsor_category ) && $sponsor_image_url && $sponsor_link_url ) {
            ?>
            <div class="sponsor_category">
                <span>Powered by</span><a href="<?php echo $sponsor_link_url; ?>"><img src="<?php echo $sponsor_image_url; ?>" /></a>
            </div>
            <?php
        }
    }
}
function register_category_sponsor_widget() { register_widget('category_sponsor_widget'); }
add_action( 'widgets_init', 'register_category_sponsor_widget' );

/*** ad KV METABOX ***/
function post_kv_meta_box_setup() {
    add_action( 'add_meta_boxes', 'post_kv_meta_box_add' );
    add_action( 'save_post', 'post_kv_meta_box_save', 10, 2 );
}
add_action( 'load-post.php', 'post_kv_meta_box_setup' );
add_action( 'load-post-new.php', 'post_kv_meta_box_setup' );

function post_kv_meta_box_add() {
    add_meta_box(
        'ad_kv',      // Unique ID
        esc_html__( 'Override Ad KV', 'example' ),    // Title
        'post_kv_meta_box',   // Callback function
        array( 'venues', 'location', 'post', 'neighborhoods' ),       // Admin page (or post type)
        'side',         // Context
        'default'         // Priority
    );
}
// Render the metabox
function post_kv_meta_box( $post ) {
    // Noncename needed to verify where the data originated
    echo '<input type="hidden" name="post_kv_meta_nonce" id="post_kv_meta_nonce" value="' .
    wp_create_nonce( basename(__FILE__) ) . '" />';
    // Get the field data if it has already been entered
    $post_kv = get_post_meta($post->ID, '_post_kv', true);

    // Echo out the fields
    echo '<p><em>If set, this will override all other ad tag KV possibilities. String will be formatted to fit standard requirements (no spaces, first letter uppercase).</em></p>';
    echo '<p><label>KV Value:</label> <input type="text" style="wdith:100%;" name="_post_kv" id="_post_kv" value="' . $post_kv  . '" /></p>';
}

// Save the Metabox Data
function post_kv_meta_box_save( $post_id, $post ) {
    /* Verify the nonce before proceeding. */
    if ( ! isset( $_POST['post_kv_meta_nonce'] ) || ! wp_verify_nonce( $_POST['post_kv_meta_nonce'], basename( __FILE__ ) ) ) {
        return $post_id;
    }

    // Is the user allowed to edit the post or page?
    if ( ! current_user_can( 'edit_post', $post->ID ) )
        return $post_id;

    // OK, we're authenticated: we need to find and save the data
    // We'll put it into an array to make it easier to loop though.
    $post_kv = ucfirst( str_replace( ' ', '-', strtolower( $_POST['_post_kv'] ) ) );

    $post_kv_new_value = ( isset( $post_kv ) ) ? sanitize_text_field( $post_kv ) : '';
    $post_kv_meta_key = '_post_kv';
    $post_kv_meta_value = get_post_meta( $post_id, $post_kv_meta_key, true );
    if ( $post_kv_new_value && '' == $post_kv_meta_value )
        add_post_meta( $post_id, $post_kv_meta_key, $post_kv_new_value, true );
    elseif ( $post_kv_new_value && $post_kv_new_value != $post_kv_meta_value )
        update_post_meta( $post_id, $post_kv_meta_key, $post_kv_new_value );
    elseif ( '' == $post_kv_new_value && $post_kv_meta_value )
        delete_post_meta( $post_id, $post_kv_meta_key, $post_kv_meta_value );
}
/*** END METABOX ***/