<?php
/**
 * Reverb Child Theme Functions
 *
 * @package Reactor
 * @author Anthony Wilhelm (@awshout / anthonywilhelm.com)
 * @version 1.1.0
 * @since 1.0.0
 * @copyright Copyright (c) 2013, Anthony Wilhelm
 * @license GNU General Public License v2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 */

/* -------------------------------------------------------
 You can add your custom functions below
-------------------------------------------------------- */


/**
 * Child Theme Features
 * The following function will allow you to remove features included with Reactor
 *
 * Remove the comment slashes (//) next to the functions
 * For add_theme_support, remove values from arrays to disable parts of the feature
 * remove_theme_support will disable the feature entirely
 * Reference the functions.php file in Reactor for add_theme_support functions
 */
add_action('after_setup_theme', 'reactor_child_theme_setup', 11);


function reactor_child_theme_setup() {

    /* Support for menus */
    // remove_theme_support('reactor-menus');
    // add_theme_support(
    //  'reactor-menus',
    //  array('top-bar-l', 'top-bar-r', 'main-menu', 'side-menu', 'footer-links')
    // );
    
    /* Support for sidebars
    Note: this doesn't change layout options */
    remove_theme_support('reactor-sidebars');
    add_theme_support(
       'reactor-sidebars',
       array( 'primary', 'front-upper', 'front-mobile', 'front-lower', )
    );
    
    /* Support for layouts
    Note: this doesn't remove sidebars */
    remove_theme_support('reactor-layouts');
    // add_theme_support(
    //  'reactor-layouts',
    //  array('1c', '2c-l', '2c-r', '3c-l', '3c-r', '3c-c')
    // );
    
    /* Support for custom post types */
    remove_theme_support('reactor-post-types');
    // add_theme_support(
    //  'reactor-post-types',
    //  array('slides', 'portfolio')
    // );
    
    /* Support for page templates */
    remove_theme_support('reactor-page-templates');
    add_theme_support(
       'reactor-page-templates',
        array( 'front-page' )
    );
    
    /* Remove support for background options in customizer */
     remove_theme_support('reactor-backgrounds');
    
    /* Remove support for font options in customizer */
    // remove_theme_support('reactor-fonts');
    
    /* Remove support for custom login options in customizer */
    // remove_theme_support('reactor-custom-login');
    
    /* Remove support for breadcrumbs function */
     remove_theme_support('reactor-breadcrumbs');
    
    /* Remove support for page links function */
    // remove_theme_support('reactor-page-links');
    
    /* Remove support for page meta function */
    // remove_theme_support('reactor-post-meta');
    
    /* Remove support for taxonomy subnav function */
    remove_theme_support('reactor-taxonomy-subnav');
    
    /* Remove support for shortcodes */
    // remove_theme_support('reactor-shortcodes');
    
    /* Remove support for tumblog icons */
     remove_theme_support('reactor-tumblog-icons');
    
    /* Remove support for other langauges */
    // remove_theme_support('reactor-translation');
    remove_theme_support('post-formats');       
}

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

// Add contact methods fields to user profile
function modify_contact_methods($profile_fields) {

    // Add new fields
    $profile_fields['publication'] = 'Publication';
    $profile_fields['display_title'] = 'Title (i.e.: \'Editor\'; leave blank to not display)';
    $profile_fields['instagram'] = 'Instagram username';
    $profile_fields['email_public'] = 'Public E-mail address (displayed on site)';

    return $profile_fields;
}
add_filter('user_contactmethods', 'modify_contact_methods');

// Add options to user interface for profile display
function extra_profile_fields( $user ) { ?>
    <h3>Author Information Display</h3>
    <table class="form-table">
        <tbody>
            <tr>
                <th>
                    <label for="list_author_single">Show author bio on article pages</label>
                </th>
                <td>
                    <input type="checkbox" name="list_author_single" id="list_author_single" value="true" <?php if ( esc_attr( get_the_author_meta('list_author_single', $user->ID) ) == true ) echo 'checked'; ?> />
                </td>
            </tr>
            <tr>
                <th>
                    <label for="list_author_about">Show author on About page</label>
                </th>
                <td>
                    <input type="checkbox" name="list_author_about" id="list_author_about" value="true" <?php if ( esc_attr( get_the_author_meta('list_author_about', $user->ID) ) == true ) echo 'checked'; ?> />
                </td>
            </tr>
            <tr>
                <th>
                    <label for="display_author_as">List author on About Page as</label>
                </th>
            <td>
                <?php 
                //get dropdown saved value
                $selected = get_the_author_meta( 'display_author_as', $user->ID ); 
                ?>
                <select name="display_author_as" id="display_author_as">
                    <option value="editor" <?php echo ($selected == "editor")?  'selected="selected"' : '' ?>>Editor</option>
                    <option value="writer" <?php echo ($selected == "writer")?  'selected="selected"' : '' ?>>Writer</option>
                    <option value="photographer" <?php echo ($selected == "photographer")?  'selected="selected"' : '' ?>>Photographer</option>
                <span class="description">Simple text field</span>
            </td>
        </tr>
        </tbody>
    </table>
    <br />
<?php }
add_action( 'edit_user_profile', 'extra_profile_fields' );
add_action( 'show_user_profile', 'extra_profile_fields' );

function save_extra_profile_fields( $user_id ) {
    if ( !current_user_can( 'edit_user', $user_id ) )
        return false;
    if (!isset($_POST['list_author_about'])) $_POST['list_author_about'] = false;
    update_user_meta( $user_id, 'list_author_about', $_POST['list_author_about'] );
    if (!isset($_POST['list_author_single'])) $_POST['list_author_single'] = false;
    update_user_meta( $user_id, 'list_author_single', $_POST['list_author_single'] );
    if (!isset($_POST['display_author_as'])) $_POST['display_author_as'] = false;
    update_user_meta( $user_id, 'display_author_as', $_POST['display_author_as'] );
}
add_action( 'personal_options_update', 'save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'save_extra_profile_fields' );

// Disables automatic Wordpress core updates:
define( 'WP_AUTO_UPDATE_CORE', false );

//This function intelligently trims a body of text to a certain number of words, but will not break a sentence.
if ( ! function_exists( 'smart_trim' ) ) {
    function smart_trim($instring, $truncation) {
        //remove shortcodes (and thereby images and embeds)
        $instring = strip_shortcodes( $instring );
        //a little regex kills datelines
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

class follow_us_on_widget extends WP_Widget
{
    public function __construct()
    {
            parent::__construct(
                'follow_us_on_widget',
                __('Follow Us On [social media]', 'follow_us_on_widget'),
                array('description' => __('Command readers to follow us on various mission-critical social networks!', 'follow_us_on_widget'), )
            );
    }

    public function widget($args, $instance)
    {
        // List of icons linked to various social networks' Intent pages
        echo '<div id="sidebar-followus" class="widget widget_followus">
                <h4 class="widget-title">Follow Us</h4>
                <ul>
                    <li class="followus"><a href="http://twitter.com/thknwco" title="Follow The Know on Twitter"><img src="' . get_stylesheet_directory_uri() . '/images/social-twitter.png" alt="Follow The Know on Twitter" /></a></li>
                    <li class="followus"><a href="http://facebook.com/denverentertain" title="Like The Know on Facebook"><img src="' . get_stylesheet_directory_uri() . '/images/social-facebook.png" alt="Like The Know on Facebook" /></a></li>
                    <li class="followus"><a href="http://instagram.com/thknwco" title="Follow The Know on Instagram"><img src="' . get_stylesheet_directory_uri() . '/images/social-instagram.png" alt="Follow The Know on Instagram" /></a></li>
                    <li class="followus"><a href="' . get_bloginfo( 'url' ) . '/feed/" title="Follow The Know via RSS"><img src="' . get_stylesheet_directory_uri() . '/images/social-rss.png" alt="Follow The Know via RSS" /></a></li>
                    <div class="clear"></div>
                </ul>
            </div>';
    }
}
function register_follow_us_on_widget() { register_widget('follow_us_on_widget'); }
add_action( 'widgets_init', 'register_follow_us_on_widget' );

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

// Get a venue when the custom field matches a venue taxonomy slug
function tkno_get_venue_from_slug($venue_slug) {
    $args = array(
        'post_type'     => 'venues',
        'meta_query'    => array(
            array(
                'key'   => 'venue_slug',
                'value' => $venue_slug,
                'compare' => 'LIKE',
                'adp_disable' => true
                )
            ),
        'posts_limits'    => 1
        );
    $query = new WP_Query( $args );
    $venues = $query->get_posts();
    return $venues[0];
}

// Get an acceptable top-level category name and ID, or slug, for classes and labels
function tkno_get_top_category_slug($return_slug=false,$cat_id=false) {
    global $post;
    $curr_cat = ( $cat_id ) ? get_category_parents( $cat_id, false, '/', true ) : get_the_category_list( '/' , 'multiple', $post->ID );
    $valid_cats = array('music','food','drink','things-to-do','arts');
    $curr_cat = explode( '/', $curr_cat );
    $return_cat = array();
    foreach ( $curr_cat as $current ) {
        $current = sanitize_title( strtolower( $current ) );
        if ( in_array( $current, $valid_cats ) ) {
            $return_cat['slug'] = $current;
            if ( $return_slug ) { 
                return $current;
            }
            break;
        }
    }
    if ( ! empty( $return_cat['slug'] ) ) { 
        $cat_for_name = get_category_by_slug( $return_cat['slug'] );
        $return_cat['cat_name'] = $cat_for_name->name;
        $return_cat['term_id'] = $cat_for_name->term_id;
        return (object) $return_cat;
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
    } else if ( is_category() ) {
        $id = get_query_var( 'cat' );
        $cat = get_category( (int)$id );
        if ( $cat->category_parent > 0 ) {
            $cat = get_category( (int)$cat->category_parent );
        }
        $category = $cat->slug;
    } else if ( is_single() ) {
        $cats = tkno_get_top_category_slug();
        $category = $cats->slug;
    }
    if ( $category ) {
        switch ( $category ) {
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
    return array( $kv, $tax );
}

// Create a simple widget for one-click newsletter signup
class newsletter_signup_widget extends WP_Widget {
    public function __construct() {
            parent::__construct(
                'newsletter_signup_widget',
                __('Newsletter Signup', 'newsletter_signup_widget'),
                array('description' => __('Come on, sign up for a newsletter. All the cool kids are doing it.', 'newsletter_signup_widget'), )
            );
    }

    public function form( $instance ) {
        //Check if limit_days exists, if its null, put "new limit_days" for use in the form
        if ( isset( $instance[ 'newletter_text' ] ) ) {
            $newletter_text = $instance[ 'newletter_text' ];
        }
        else {
            $newletter_text = __( 'Sign up for our <em>Now You Know</em> emails to get breaking entertainment news and weekend plans sent right to your inbox.', 'wpb_widget_domain' );
        } ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'newletter_text' ); ?>"><?php _e( 'Descriptive text (displayed above email form):' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'newletter_text' ); ?>" name="<?php echo $this->get_field_name( 'newletter_text' ); ?>" type="text" value="<?php echo esc_attr( $newletter_text ); ?>" />
        </p>
    <?php }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance[ 'newletter_text' ] = ( ! empty( $new_instance[ 'newletter_text' ] ) ) ? trim( wp_kses( $new_instance[ 'newletter_text' ] ) ) : 'Sign up for our <em>Now You Know</em> emails to get breaking entertainment news and weekend plans sent right to your inbox.';
        return $instance;
    }

    public function widget($args, $instance) {
        $newletter_text = $instance[ 'newletter_text' ];
        global $wp;
        $current_url = home_url(add_query_arg(array(),$wp->request));
        // The signup form for the email
        echo '<div id="sidebar-newsletter" class="widget widget_newsletter">
                <h4 class="widget-title">Get Our Newsletter</h4>
                <p>' . $newletter_text . '</p>
                <form action="http://www.denverpostplus.com/app/mailer/" method="post" name="reverbmail">
                    <div class="row collapse mx-form">
                        <div class="large-9 small-9 columns">
                            <input type="hidden" name="keebler" value="goof111" />
                            <input type="hidden" name="goof111" value="TRUE" />
                            <input type="hidden" name="redirect" value="' . $current_url . '" />
                            <input type="hidden" name="id" value="autoadd" />
                            <input type="hidden" name="which" value="theknow" />
                            <input type="text" name="name_first" value="Humans: Do Not Use" style="display:none;" />
                            <input required placeholder="Email Address" type="text" name="email_address" maxlength="50" value="" />
                        </div>
                        <div class="large-3 small-3 columns end">
                            <input class="button prefix" type="submit" id="newslettersubmit" value="Sign up">
                        </div>
                    </div>
                </form>
            </div>';
    }
}
function register_newsletter_signup_widget() { register_widget('newsletter_signup_widget'); }
add_action( 'widgets_init', 'register_newsletter_signup_widget' );

// Create a simple widget for one-click newsletter signup
class newstip_submit_widget extends WP_Widget {
    public function __construct() {
            parent::__construct(
                'newstip_submit_widget',
                __('Newstip Submit', 'newstip_submit_widget'),
                array('description' => __('Todd was warned about news tip submissions.', 'newstip_submit_widget'), )
            );
    }

    public function widget($args, $instance) {
        global $wp;
        $current_url = home_url(add_query_arg(array(),$wp->request));
        // The submit form for the newstip
        echo '<div id="sidebar-newstip" class="widget widget_newstip">
                <h4 class="widget-title">Send Us A Tip</h4>
                <form action="http://www.denverpostplus.com/app/mailer/" method="post" name="tipmail">
                    <div class="row collapse mx-form">
                        <textarea name="comments" rows="4" cols="30"></textarea>
                        <input type="hidden" name="keebler" value="goof111" />
                        <input type="hidden" name="goof111" value="TRUE" />
                        <input type="hidden" name="redirect" value="' . $current_url . '" />
                        <input type="hidden" name="id" value="newstip" />
                        <input type="text" name="name_first" value="Humans: Do Not Use" style="display:none;" />
                        <p>If you would like a reply, include your email:</p>
                        <div class="large-9 small-9 columns">
                            <input type="text" name="email_address" value="" maxlength="50" />
                        </div>
                        <div class="large-3 small-3 columns end">
                            <input class="button prefix" type="submit" id="newstipsubmit" value="Send tip">
                        </div>
                        <div class="clear"></div>
                    </div>
                </form>
            </div>';
    }
}
function register_newstip_submit_widget() { register_widget('newstip_submit_widget'); }
add_action( 'widgets_init', 'register_newstip_submit_widget' );

class sidebar_tagline_widget extends WP_Widget {
    public function __construct() {
            parent::__construct(
                'sidebar_tagline_widget',
                __('DP Logo + Tagline', 'sidebar_tagline_widget'),
                array('description' => __('If they don\'t know we are affiliated with The Denver Post, will they even care?', 'sidebar_tagline_widget'), )
            );
    }

    public function form( $instance ) {
        //Check if limit_days exists, if its null, put "new limit_days" for use in the form
        if ( isset( $instance[ 'tagline_text' ] ) ) {
            $tagline_text = $instance[ 'tagline_text' ];
        }
        else {
            $tagline_text = __( 'What to do, where to be and what to see, from', 'wpb_widget_domain' );
        } ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'tagline_text' ); ?>"><?php _e( 'Tagline text (will be followed by "The Denver Post" logo):' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'tagline_text' ); ?>" name="<?php echo $this->get_field_name( 'tagline_text' ); ?>" type="text" value="<?php echo esc_attr( $tagline_text ); ?>" />
        </p>
    <?php }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance[ 'tagline_text' ] = ( ! empty( $new_instance[ 'tagline_text' ] ) ) ? trim( strip_tags( $new_instance[ 'tagline_text' ] ) ) : 'What to do, where to be and what to see, from';
        return $instance;
    }

    public function widget($args, $instance) {
        $tagline_text = $instance[ 'tagline_text' ];
        // Display a fixed tagline and The Denver Post logo
        echo '<div id="sidebar-tagline" class="widget widget_tagline">
                <p>' . $tagline_text . ' <img src="'.get_bloginfo('stylesheet_directory').'/images/dp-logo-blk.png" /></p>
            </div>';
    }
}
function register_sidebar_tagline_widget() { register_widget('sidebar_tagline_widget'); }
add_action( 'widgets_init', 'register_sidebar_tagline_widget' );

// Calendar widget
class tkno_calendar_widget extends WP_Widget
{
    public function __construct()
    {
            parent::__construct(
                'tkno_calendar_widget',
                __('The Know Calendar', 'tkno_calendar_widget'),
                array('description' => __('Put an adaptive (by parent category) The Know calendar widget in a sidebar', 'tkno_calendar_widget'), )
            );
    }

    public function widget( $args, $instance ) {

        function tkno_cal_category() {
            $category = FALSE;
            $calcat = '8354';
            if ( is_home() || is_front_page() ) {
                $calcat = '8354';
            } else if ( is_category() ) {
                $id = get_query_var( 'cat' );
                $cat = get_category( (int)$id );
                $category = $cat->slug;
            } else if ( is_single() ) {
                $category = tkno_get_top_category_slug(true);
            }
            if ( $category ) {
                switch ( $category ) {
                    case 'music':
                        $calcat = '8347';
                        break;
                    case 'arts':
                        $calcat = '8350';
                        break;
                    case 'things-to-do':
                        $calcat = '8351';
                        break;
                    case 'food':
                        $calcat = '8352';
                        break;
                    case 'drink':
                        $calcat = '8353';
                        break;
                    default:
                        $calcat = '8354';
                }
            }
            return $calcat;
        }
        echo '<div id="sidebar-calendar" class="widget widget_cal">
                <div data-cswidget="' . tkno_cal_category() . '"></div>
                <script type="text/javascript" async defer src="//portal.CitySpark.com/js/widget.min.js"></script>
                </div>';
    }
}
function register_calendar_widget() { register_widget('tkno_calendar_widget'); }
add_action( 'widgets_init', 'register_calendar_widget' );

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
                            googletag.defineSlot(\'/8013/denverpost.com/TheKnow' . $ad_tax[1] . '\', [[300,250],[300,600]], \'cube1_reverb\').setTargeting(\'pos\',[\'Cube1_RRail_ATF\']).setTargeting(\'kv\', \'' . $ad_tax[0] . '\').addService(googletag.pubads());
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
                            googletag.defineSlot(\'/8013/denverpost.com/TheKnow' . $ad_tax[1] . '\', [300,250], \'cube1_reverb\').setTargeting(\'pos\',[\'Cube1_RRail_ATF\']).setTargeting(\'kv\', \'' . $ad_tax[0] . '\').addService(googletag.pubads());
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
                    googletag.defineSlot(\'/8013/denverpost.com/TheKnow' . $ad_tax[1] . '\', [300,250], \'cube2_reverb\').setTargeting(\'pos\',[\'Cube2_RRail_mid\']).setTargeting(\'kv\', \'' . $ad_tax[0] . '\').addService(googletag.pubads());
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
 *
 * DESTROY ALL COMMENTS ARRRRR!
 * 
 */

// Disable support for comments and trackbacks in post types
function tkno_disable_comments_post_types_support() {
    $post_types = get_post_types();
    foreach ($post_types as $post_type) {
        if(post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}
add_action('admin_init', 'tkno_disable_comments_post_types_support');

// Close comments on the front-end
function tkno_disable_comments_status() {
    return false;
}
add_filter('comments_open', 'tkno_disable_comments_status', 20, 2);
add_filter('pings_open', 'tkno_disable_comments_status', 20, 2);

// Hide existing comments
function tkno_disable_comments_hide_existing_comments($comments) {
    $comments = array();
    return $comments;
}
add_filter('comments_array', 'tkno_disable_comments_hide_existing_comments', 10, 2);

// Remove comments page in menu
function tkno_disable_comments_admin_menu() {
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'tkno_disable_comments_admin_menu');

// Redirect any user trying to access comments page
function tkno_disable_comments_admin_menu_redirect() {
    global $pagenow;
    if ($pagenow === 'edit-comments.php') {
        wp_redirect(admin_url()); exit;
    }
}
add_action('admin_init', 'tkno_disable_comments_admin_menu_redirect');

// Remove comments metabox from dashboard
function tkno_disable_comments_dashboard() {
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}
add_action('admin_init', 'tkno_disable_comments_dashboard');

// Remove comments links from admin bar
function tkno_disable_comments_admin_bar() {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
}
add_action('init', 'tkno_disable_comments_admin_bar');

/**
 * Add a Venue taxonomy for venues (to tie into a Venue post format)
 */
function tkno_register_venue_taxonomy() {
    $labels = array(
        'name'                           => 'Venues',
        'singular_name'                  => 'Venue',
        'search_items'                   => 'Search Venues',
        'all_items'                      => 'All Venues',
        'edit_item'                      => 'Edit Venue',
        'update_item'                    => 'Update Venue',
        'add_new_item'                   => 'Add New Venue',
        'new_item_name'                  => 'New Venue Name',
        'menu_name'                      => 'Venues',
        'view_item'                      => 'View Venues',
        'popular_items'                  => 'Popular Venues',
        'separate_items_with_commas'     => 'Separate venues with commas',
        'add_or_remove_items'            => 'Add or remove venues',
        'choose_from_most_used'          => 'Choose from the most used venues',
        'not_found'                      => 'No venues found'
    );
    register_taxonomy(
        'venue',
        array('post'),
        array(
            'label' => __( 'Venue' ),
            'hierarchical' => false,
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => false,
            'show_ui' => true,
            'show_in_nav_menus' => false,
            'show_tagcloud' => false,
            'show_admin_column' => false,
            'rewrite' => array( 'slug' => 'venue','with_front' => false),
        )
    );
}
add_action( 'init', 'tkno_register_venue_taxonomy' );

/**
 * Add a Venue Page post type to tie in to the taxonomy
 */
function tkno_register_venue_page_posttype() {
    $labels = array(
        'name'               => _x( 'Venue Pages', 'post type general name' ),
        'singular_name'      => _x( 'Venue Page', 'post type singular name' ),
        'add_new'            => _x( 'Add New', 'venue' ),
        'add_new_item'       => __( 'Add New Venue Page' ),
        'edit_item'          => __( 'Edit Venue Page' ),
        'new_item'           => __( 'New Venue Page' ),
        'all_items'          => __( 'All Venue Pages' ),
        'view_item'          => __( 'View Venue Pages' ),
        'search_items'       => __( 'Search Venue Pages' ),
        'not_found'          => __( 'No venue pages found' ),
        'not_found_in_trash' => __( 'No venue pages found in the Trash' ), 
        'parent_item_colon'  => '',
        'menu_name'          => 'Venue Pages'
    );
    $args = array(
        'labels'        => $labels,
        'description'   => 'Venue Pages feature a single venue and pull in related items based on the Venue taxonomy',
        'public'        => true,
        'publicly_queryable' => true,
        'show_ui'       => true,
        'menu_position' => 5,
        'capability_type' => 'post',
        'query_var'     => true,
        'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attibutes', 'revisions', 'author', 'custom-fields', ),
        'rewrite' => array( 'slug' => 'venues','with_front' => false),
        'has_archive'   => true,
    );
    register_post_type( 'venues', $args );
}
add_action( 'init', 'tkno_register_venue_page_posttype' );

/**
 * Custom iunteraction messages for Venue Page post type
 */
function tkno_venue_page_messages( $messages ) {
    global $post, $post_ID;
    $messages['venues'] = array(
        0 => '', 
        1 => sprintf( __('Venue page updated. <a href="%s">View venue page</a>'), esc_url( get_permalink($post_ID) ) ),
        2 => __('Custom field updated.'),
        3 => __('Custom field deleted.'),
        4 => __('Venue page updated.'),
        5 => isset($_GET['revision']) ? sprintf( __('Venue page restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
        6 => sprintf( __('Venue page published. <a href="%s">View venue page</a>'), esc_url( get_permalink($post_ID) ) ),
        7 => __('Venue page saved.'),
        8 => sprintf( __('Venue page submitted. <a target="_blank" href="%s">Preview venue page</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
        9 => sprintf( __('Venue page scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview venue page</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
        10 => sprintf( __('Venue page draft updated. <a target="_blank" href="%s">Preview venue page</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    );
    return $messages;
}
add_filter( 'post_updated_messages', 'tkno_venue_page_messages' );

/**
 * Contextual help for venue pages
 */
function tkno_venue_page_contextual_help( $contextual_help, $screen_id, $screen ) { 
  if ( 'venues' == $screen->id ) {

    $contextual_help = '<h2>Venue pages</h2>
    <p>Venue pages show details about a particular venue, and tie in recent posts assigned to that venue. You can see a list of them on this page in reverse chronological order - the latest one we added is first.</p> 
    <p>You can view/edit the details of each venue page by clicking on its name, or you can perform bulk actions using the dropdown menu and selecting multiple items.</p>';

  } elseif ( 'edit-venue_page' == $screen->id ) {

    $contextual_help = '<h2>Editing venue pages</h2>
    <p>This page allows you to view/modify venue pages. Please make sure to fill out the available boxes with the appropriate details and <strong>not</strong> add these details to the venue description.</p>';

  }
  return $contextual_help;
}
add_action( 'contextual_help', 'tkno_venue_page_contextual_help', 10, 3 );

// Popular widget
class tkno_popular_widget extends WP_Widget
{
    public function __construct()
    {
            parent::__construct(
                'tkno_popular_widget',
                __('The Know Popular widget', 'tkno_popular_widget'),
                array('description' => __('Put a The Know popular posts widget in the sidebar', 'tkno_popular_widget'), )
            );
    }

    public function form( $instance ) {
        //Check if limit_days exists, if its null, put "new limit_days" for use in the form
        if ( isset( $instance[ 'limit_days' ] ) ) {
            $limit_days = $instance[ 'limit_days' ];
        }
        else {
            $limit_days = __( '0', 'wpb_widget_domain' );
        } ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'limit_days' ); ?>"><?php _e( 'Display popular posts from the last __ days (0 for no limit):' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'limit_days' ); ?>" name="<?php echo $this->get_field_name( 'limit_days' ); ?>" type="text" value="<?php echo esc_attr( $limit_days ); ?>" />
        </p>
    <?php }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance[ 'limit_days' ] = ( ! empty( $new_instance[ 'limit_days' ] ) ) ? (int)strip_tags( $new_instance[ 'limit_days' ] ) : 0;
        return $instance;
    }

    public function widget( $args, $instance ) {
        $limit_days = $instance[ 'limit_days' ];
        $limit_days = ( $limit_days != 0 ) ? (int)$limit_days : 10000;
        if ( function_exists( 'stats_get_csv' ) ) {
            echo '<div id="sidebar-popular" class="widget widget_pop">
                    <h4 class="widget-title">Popular</h4>';
            $top_posts = stats_get_csv( 'postviews', 'days=7&limit=200' );
            if ( count( $top_posts ) > 0 ) {
                echo '<ul>';
                $i=1;
                foreach ($top_posts as $p) {
                    $post = get_post( $p[ 'post_id' ] );
                    $post_date = strtotime( $post->post_date );
                    $today_date = time();
                    if ( $i <= 5 && ( $today_date - $post_date ) <= 60*60*24*(int)$limit_days && ( get_post_type( $p['post_id'] ) != 'page' ) ) { ?>
                        <li class="clearfix"><span class="pop_num"><?php echo $i; ?></span><a href="<?php echo $p['post_permalink']; ?>"><?php echo $p['post_title']; ?></a><div class="clear"></div></li>
                        <?php
                        $i++;
                    }
                }
                echo '</ul>
                    </div>';
            }
        } else {
            ?> <!-- Sorry, there are no Popular posts to display! --><?php
        }
        if ( term_exists( 'dont-miss', 'post_tag' ) ) {
            $dm_tag = get_term_by( 'slug', 'dont-miss', 'post_tag' );
            remove_all_filters('posts_orderby'); // disable Post Types Order ordering for this query
            $args = array(
                'post_type'         => 'post',
                'tag_id'            => $dm_tag->term_id,
                'posts_per_page'    => '5',
                'orderby'           => 'rand',
                'adp_disable'       => true,
                );
            $dm_query = new WP_Query( $args );
            $i=1;
            if ( $dm_query->have_posts() ) {
                echo '<div id="sidebar-dontmiss" class="widget widget_dontmiss">
                    <h4 class="widget-title">Don\'t Miss</h4>
                    <ul>';
                while ( $dm_query->have_posts() ) : $dm_query->the_post();
                    if ( $i <= 5 ) { ?>
                        <li class="clearfix"><span class="pop_num"><?php echo $i; ?></span><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><div class="clear"></div></li>
                    <?php $i++;
                    }
                endwhile;
                echo '</ul>
                    </div>';
            } else {
                ?> <!-- Sorry, there are no Don't Miss posts at this time! --><?php
            }
        }
    }
}
function register_popular_widget() { register_widget('tkno_popular_widget'); }
add_action( 'widgets_init', 'register_popular_widget' );

function tkno_get_primary_category() {
    
    global $post;
    
    $primaryCat = new WPSEO_Primary_Term( 'category', $post->ID );
    $primaryCat = $primaryCat->get_primary_term();
    $primaryCat = get_cat_name($primaryCat);

    $categories = get_the_category( $post->ID );
    $return_cat = Array();

    foreach( $categories as $category ) {
       $defaultCat = $category->name;
       $defaultCatLink = get_category_link( $category->term_id );
    }

    if ( $primaryCat !== "" ) {
       $cat = new WPSEO_Primary_Term('category', $post->ID);
       $cat = $cat->get_primary_term();

       $return_cat['name'] = get_cat_name($cat);
       $return_cat['url'] = get_category_link($cat);

    } else {
       $return_cat['name'] = $defaultCat;
       $return_cat['url'] = $defaultCatLink;
    }

    return (object) $return_cat;
}

/**
 * in_article_related_shortcode
 * @return html list inserted in content based on tag
 */
function in_article_related_shortcode(){
    $related = '';
    if ( is_single() && function_exists( 'yarpp_related' ) ) { 
        global $post;
        $related .= yarpp_related( array( 
            'post_type'         => array('post'),
            'show_pass_post'    => false,
            'exclude'           => array(),
            'recent'            => '2 month',
            'weight'            => array(
                'tax'   => array(
                    'post_tag' => 2,
                    'venue'   => 1
                )
            ),
            'threshold'         => 2,
            'template'          => 'yarpp-template-inarticle.php',
            'limit'             => 5,
            'order'             => 'score DESC'
            ),
        $post->ID,
        false);
    }
    return $related;
}
add_shortcode('related', 'in_article_related_shortcode');

function related_shortcode_button() {
    echo '<a href="javascript:void(0);" id="insert-related-shortcode" class="button">Insert Related</a>';
}
add_action('media_buttons', 'related_shortcode_button',15);

function tkno_admin_enqueue($hook) {
    if ( 'post.php' != $hook ) {
        return;
    }
    wp_enqueue_script( 'rvadmin-js', get_stylesheet_directory_uri() . '/library/js/rv-admin.js' );
}
add_action( 'admin_enqueue_scripts', 'tkno_admin_enqueue' );

/**
 * Attempt to de-dupe the homepage results
 */
function tkno_exclude_duplicates( &$query ) {
    if ( ! is_front_page() || $query->get('adp_disable') ) return;
    global $adp_posts;
    if ( empty( $query->post__not_in ) ) {
        $query->set( 'post__not_in', $adp_posts );
    }
}
add_action( 'parse_query', 'tkno_exclude_duplicates' );

function tkno_log_posts( $posts, $query ) {
    $adp_posts = array(); 
    if ( ! is_front_page() ) return $posts;
    global $adp_posts;
    foreach ( $posts as $i => $post ) {
        $adp_posts[] = $post->ID;
    }
    return $posts;
}
add_filter( 'the_posts', 'tkno_log_posts', 10, 2 );

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
 * Removes "smart" characters from word processors and replaces them with the correct hmtl safe characters
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
