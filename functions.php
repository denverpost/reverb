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
    remove_theme_support('reactor-menus');
    add_theme_support(
        'reactor-menus',
        array('top-bar-l', 'top-bar-outdoors', 'top-bar-r', 'main-menu', 'side-menu', 'footer-links')
    );
    
    /* Support for sidebars
    Note: this doesn't change layout options */
    remove_theme_support('reactor-sidebars');
    add_theme_support(
       'reactor-sidebars',
       array( 'primary', 'outdoors', 'front-upper', 'front-mobile', 'front-lower', 'neighborhood-upper', 'neighborhood-lower', 'outdoor-upper', 'outdoor-middle', 'category-sponsor', 'sidebar-sponsored' )
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

/**
 * INCLUDES Sub-files for post types and taxonomies, widgets and whatnot
**/
// A variety of overrides and tweaks for various Wordpress and plugin behaviors
require_once( __DIR__ . '/includes/overrides.php');
// Venues taxonomy and post type, related admin and sidebar widgets, etc.
require_once( __DIR__ . '/includes/venues.php');
// Neighborhoods taxonomy and post type, related admin and sidebar widgets, etc.
require_once( __DIR__ . '/includes/neighborhoods.php');
// A day in post type
require_once( __DIR__ . '/includes/quicktrip.php');
// Locations taxonomy and post type, related admin and sidebar widgets, etc.
require_once( __DIR__ . '/includes/locations.php');
// Sidebar widgets and related pieces
require_once( __DIR__ . '/includes/widgets.php');
// Ad-related widgets, etc.
//require_once( __DIR__ . '/includes/advertising.php');
// Expanded RSS widget for DP feed
require_once( __DIR__ . '/includes/rss-widget.php');
// Location-based news from RSS widget
require_once( __DIR__ . '/includes/neighborhood-rss-widget.php');
// Location-based news from RSS widget
require_once( __DIR__ . '/includes/snow-widget.php');

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

// Fix page vs. paged variable confusion for single post queries
add_action( 'template_redirect', function() {
    if ( is_singular( 'venues' ) || is_singular( 'neighborhoods' ) ) {
        global $wp_query;
        $page = ( int ) $wp_query->get( 'page' );
        if ( $page > 1 ) {
            // convert 'page' to 'paged'
            $wp_query->set( 'page', 1 );
            $wp_query->set( 'paged', $page );
        }
        // prevent redirect
        remove_action( 'template_redirect', 'redirect_canonical' );
    }
}, 0 );

// Get an acceptable top-level category name and ID, or slug, for classes and labels
function tkno_get_top_category_slug( $return_slug=false, $cat_id=false ) {
    global $post;
    $curr_cat = ( $cat_id ) ? get_category_parents( $cat_id, false, '/', true ) : get_the_category_list( '/' , 'multiple', $post->ID );
    $valid_cats = ( is_outdoors() ) ? array( 'spring', 'summer', 'fall', 'winter', 'trips', 'outdoors' ) : array( 'music', 'food', 'drink', 'things-to-do', 'arts' );
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

function tkno_get_primary_category( $input_id=false ) {
    
    global $post;

    $primary_id = ( $input_id == false ) ? $post->ID : $input_id;

    $primaryCat = '';
    if ( class_exists( 'WPSEO_Primary_Term' ) ) {
    
        $primaryCat = new WPSEO_Primary_Term( 'category', $primary_id );
        $primaryCat = $primaryCat->get_primary_term();
        $primaryCat = get_cat_name($primaryCat);
    }

    $return_cat = Array();

    if ( $primaryCat !== '' ) {
       $cat = new WPSEO_Primary_Term('category', $primary_id);
       $cat = $cat->get_primary_term();

       $return_cat['name'] = get_cat_name($cat);
       $return_cat['url'] = get_category_link($cat);

    } else {
        $categories = get_the_category( $primary_id );
        foreach( $categories as $category ) {
           $defaultCat = $category->name;
           $defaultCatLink = get_category_link( $category->term_id );
        }
        $return_cat['name'] = $defaultCat;
        $return_cat['url'] = $defaultCatLink;
    }

    return (object) $return_cat;
}

/**
 * in_article_related_shortcode
 * @return html list inserted in content
 */
function in_article_related_shortcode( $atts=array() ){
    $related = '';
    $template = ( isset( $atts['wide'] ) && $atts['wide'] == 'true') ? 'yarpp-template-inarticle-fullwidth.php' : 'yarpp-template-inarticle.php';
    $require_venue = ( isset( $atts['venue'] ) && $atts['venue'] == 'true') ? true : false;
    $month = ( isset( $atts['months'] ) && is_int( $atts['months'] ) ) ? $atts['months'] . ' month' : '2 month';
    $sort_order = ( isset( $atts['sort'] ) && $atts['sort'] == 'date' ) ? 'date DESC' : 'score DESC';
    $venue_require = array( 'venue' => 1 );
    if ( is_single() && function_exists( 'yarpp_related' ) ) { 
        global $post;
        $related .= yarpp_related( array( 
            'post_type'         => array('post'),
            'show_pass_post'    => false,
            'past_only'         => false,
            'exclude'           => array(),
            'recent'            => $month,
            'weight'            => array(
                'title'             => 1,
                'tax'               => array(
                    'post_tag'          => 4,
                    'venue'             => 5,
                    'category'          => 2
                )
            ),
            'require_tax'       => ( $require_venue ) ? $venue_require : array(),
            'threshold'         => 3,
            'template'          => $template,
            'limit'             => 5,
            'order'             => $sort_order
            ),
        $post->ID,
        false);
    }
    wp_reset_postdata();
    return $related;
}
add_shortcode('related', 'in_article_related_shortcode');

function related_shortcode_button() {
    echo '<a href="javascript:void(0);" id="insert-related-shortcode" class="button">Insert Related</a>';
}
add_action('media_buttons', 'related_shortcode_button',15);

/**
 * Shortcode to add a widget teasing the Bucket List
 * @return html aside inserted in content
 */
function in_article_bucketlist_shortcode(){
    $bucketlist = '<aside class="article-bucketlist">';
    /* WINTER
        $bucketlist .= '<h3>#knowCOwinter</h3>';
        $bucketlist .= '<a href="//theknow.denverpost.com/2017/11/19/ultimate-colorado-winter-bucket-list/167078/" rel="bookmark"><img src="//theknow.denverpost.com/wp-content/uploads/2017/11/50Things-Winter-1.jpg" /></a>';
        $bucketlist .= '<p><a href="//theknow.denverpost.com/2017/11/19/ultimate-colorado-winter-bucket-list/167078/">This story features a bucket-list experience &mdash; check out our complete Colorado Winter Bucket List!</a></p>';
        */
        $bucketlist .= '<h3>#knowCOsummer18</h3>';
        $bucketlist .= '<a href="//theknow.denverpost.com/2017/05/22/colorado-ultimate-summer-bucket-list/144739/" rel="bookmark"><img src="//theknow.denverpost.com/wp-content/uploads/2017/05/100things.jpg" /></a>';
        $bucketlist .= '<p><a href="//theknow.denverpost.com/2017/05/22/colorado-ultimate-summer-bucket-list/144739/">This story features a bucket-list experience &mdash; check out our complete Colorado Summer Bucket List!</a></p>';
        $bucketlist .= '<div class="clear"></div>';
    $bucketlist .= '</aside>';
    return $bucketlist;
}
add_shortcode('bucketlist', 'in_article_bucketlist_shortcode');

function bucketlist_shortcode_button() {
    echo '<a href="javascript:void(0);" id="insert-bucketlist-shortcode" class="button">Insert Bucket List</a>';
}
add_action('media_buttons', 'bucketlist_shortcode_button',15);

function tkno_admin_enqueue($hook) {
    wp_enqueue_style( 'admin_css', get_stylesheet_directory_uri() . '/admin-style.css', false, '1.0.0' );
    if ( 'post.php' != $hook ) {
        return;
    }
    wp_enqueue_script( 'rvadmin-js', get_stylesheet_directory_uri() . '/library/js/rv-admin.js' );
}
add_action( 'admin_enqueue_scripts', 'tkno_admin_enqueue' );

function is_location() {
    /* is it an locations-related page? */
    return ( is_post_type_archive( 'location' ) || ( is_single() && get_post_type() == 'location' ) ) ? true : false;
}

function is_outdoors() {
    /* is it an outdoors-related page? */
    $outdoors = false;
    global $post;
    $current_id = ( is_single() ) ? $post->ID : get_query_var('cat');
    $outdoor_parent = get_category_by_slug( 'outdoors' );
    if ( is_category() && ( $current_id == $outdoor_parent->term_id || cat_is_ancestor_of( $outdoor_parent->term_id, $current_id ) ) ) {
        $outdoors = true;
    } else if ( is_single() ) {
        $categories = wp_get_post_categories( $current_id );
        foreach ( $categories as $category ) {
            if ( $category == $outdoor_parent->term_id ) {
                $outdoors = true;
                break;
            }
        }
    }
    return $outdoors;
}

// Is this Outdoors, or one of the "parent" children of Outdoors
function is_outdoor_home() {
    if ( is_outdoors() && is_category() ) {
        $cat = get_queried_object();
        $children = get_terms( $cat->taxonomy, array(
            'parent'    => $cat->term_id,
            'hide_empty' => false
        ) );
        if ( 0 == $cat->category_parent ) {
            return true;
        } elseif ( $children ) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
add_filter( 'category_template', 'outdoors_category_templates' );

// Different templates for Outdoors and its subcategories
function outdoors_category_templates( $template ) {
    if ( is_outdoors() ) {
        $cat = get_queried_object();
        $children = get_terms( $cat->taxonomy, array(
            'parent'    => $cat->term_id,
            'hide_empty' => false
        ) );
        if ( 0 == $cat->category_parent ) {
            $template = locate_template( 'category-outdoors.php' );
        } elseif ( $children ) {
            $template = locate_template( 'category-outdoorchild.php' );
        }
    }
    return $template;
}
add_filter( 'category_template', 'outdoors_category_templates' );

// adlayer inside article ad insertion
//Insert ads after second paragraph of single post content. 
function ad_insert_post_ads( $content ) {
	
	//adding check for one off articles *for now
	$id = get_the_ID();
	
	if ($id == 201246) {
	//do nothing for a special reason
	}else{
	 
    	$ad_code = '<div style="text-align:center;"><div id="div-gpt-ad-Cube_Article" class="dfp-ad dfp-Cube_Article" data-ad-unit="Cube_Article">';

	$ad_code .= '<script type="text/javascript">';
	$ad_code .= 'if ( "undefined" !== typeof googletag ) {
			googletag.cmd.push( function() { googletag.display("div-gpt-ad-Cube_Article"); } );
		}';
	$ad_code .= '</script>';
	$ad_code .= '</div></div>';
	$ad_code .= "<div></div>";
 
    if ( is_single() && ! is_admin() ) {
        return ad_insert_after_paragraph( $ad_code, 2, $content );
    }

	}
     
    return $content;
}
  
// Parent Function that makes the magic happen
function ad_insert_after_paragraph( $insertion, $paragraph_id, $content ) {
    $closing_p = '</p>';
    $paragraphs = explode( $closing_p, $content );
    foreach ($paragraphs as $index => $paragraph) {
 
        if ( trim( $paragraph ) ) {
            $paragraphs[$index] .= $closing_p;
        }
        
        //check for at least 5 paragraphs before displaying inline ad cube
        $minParagraphCount = 5;
 		if ((count($paragraphs)-1) >= $minParagraphCount) {
			if ( $paragraph_id == $index + 1 ) {
				$paragraphs[$index] .= $insertion;
			}
		}
    }
     
    return implode( '', $paragraphs );
}
add_filter( 'the_content', 'ad_insert_post_ads' );

function my_acf_init() {
	acf_update_setting('google_api_key', '##git removed');
}

add_action('acf/init', 'my_acf_init');

add_role( "adwidgets", "Trade Widgets", array(
   'read'            => true, // Allows a user to read
   'edit_theme_options' => true, //allows user to access widgets and menus
   'upload_files' => true, //upload files
   ));
//Allow trade widget role to Add Media
if ( current_user_can('adwidgets') && !current_user_can('upload_files') )
add_action('admin_init', 'allow_contributor_uploads');

function allow_contributor_uploads() {
$contributor = get_role('adwidgets');
$contributor->add_cap('upload_files');
}
