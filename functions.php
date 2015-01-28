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
	// 	'reactor-menus',
	// 	array('top-bar-l', 'top-bar-r', 'main-menu', 'side-menu', 'footer-links')
	// );
	
	/* Support for sidebars
	Note: this doesn't change layout options */
	remove_theme_support('reactor-sidebars');
	add_theme_support(
	   'reactor-sidebars',
	   array('primary', 'secondary', 'front-secondary', 'footer', 'error')
	);
	
	/* Support for layouts
	Note: this doesn't remove sidebars */
	// remove_theme_support('reactor-layouts');
	// add_theme_support(
	// 	'reactor-layouts',
	// 	array('1c', '2c-l', '2c-r', '3c-l', '3c-r', '3c-c')
	// );
	
	/* Support for custom post types */
	remove_theme_support('reactor-post-types');
	// add_theme_support(
	// 	'reactor-post-types',
	// 	array('slides', 'portfolio')
	// );
	
	/* Support for page templates */
	remove_theme_support('reactor-page-templates');
	add_theme_support(
	   'reactor-page-templates',
	 	array('front-page')
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
	// remove_theme_support('reactor-taxonomy-subnav');
	
	/* Remove support for shortcodes */
	// remove_theme_support('reactor-shortcodes');
	
	/* Remove support for tumblog icons */
	 remove_theme_support('reactor-tumblog-icons');
	
	/* Remove support for other langauges */
	// remove_theme_support('reactor-translation');
		
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

// Function to add featured image in RSS feeds
function featured_image_in_rss($content)
{
    // Global $post variable
    global $post;
    // Check if the post has a featured image
    if (has_post_thumbnail($post->ID))
    {
        $content = get_the_post_thumbnail($post->ID, 'full', array('style' => 'margin-bottom:10px;')) . $content;
    }
    return $content;
}
// Add the filter for RSS feeds Excerpt
add_filter('the_excerpt_rss', 'featured_image_in_rss');
//Add the filter for RSS feed content
add_filter('the_content_feed', 'featured_image_in_rss');

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
                        if($matches[$i+1][0] == strtoupper($matches[$i+1][0])) {
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

// Exclude Pages from search
function SearchFilter($query) {
    if ($query->is_search) {
        $query->set('post_type', 'post');
    }
    return $query;
}
add_filter('pre_get_posts','SearchFilter');

/**
 * Include posts from authors in the search results where
 * either their display name or user login matches the query string
 *
 * @author danielbachhuber
 */
add_filter( 'posts_search', 'db_filter_authors_search' );
function db_filter_authors_search( $posts_search ) {

    // Don't modify the query at all if we're not on the search template
    // or if the LIKE is empty
    if ( !is_search() || empty( $posts_search ) )
        return $posts_search;

    global $wpdb;
    // Get all of the users of the blog and see if the search query matches either
    // the display name or the user login
    add_filter( 'pre_user_query', 'db_filter_user_query' );
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
    remove_filter( 'pre_user_query', 'db_filter_user_query' );
    // Don't modify the query if there aren't any matching users
    if ( empty( $matching_users ) )
        return $posts_search;
    // Take a slightly different approach than core where we want all of the posts from these authors
    $posts_search = str_replace( ')))', ")) OR ( {$wpdb->posts}.post_author IN (" . implode( ',', array_map( 'absint', $matching_users ) ) . ")))", $posts_search );
    return $posts_search;
}
/**
 * Modify get_users() to search display_name instead of user_nicename
 */
function db_filter_user_query( &$user_query ) {

    if ( is_object( $user_query ) )
        $user_query->query_where = str_replace( "user_nicename LIKE", "display_name LIKE", $user_query->query_where );
    return $user_query;
}

// Attempts to permanently disable the Visual Editor for all users, all the time.
add_filter( 'user_can_richedit', '__return_false', 50 );

// Create a simple widget for one-click newsletter signup
class newsletter_signup_widget extends WP_Widget {
    public function __construct()
    {
            parent::__construct(
                'newsletter_signup_widget',
                __('Newsletter Signup', 'newsletter_signup_widget'),
                array('description' => __('Come on, sign up for a newsletter. All the cool kids are doing it.', 'newsletter_signup_widget'), )
            );
    }

    public function widget($args, $instance)
    {
        // List of icons linked to various social networks' Intent pages
        echo '<div id="sidebar-newsletter" class="widget widget_newsletter">
                <h4 class="widget-title">Get Mixtape Newsletters</h4>
                <form action="http://www.denverpostplus.com/app/mailer/" method="post" name="reverbmail">
                    <div class="row collapse mx-form">
                        <div class="large-9 small-9 columns">
                            <input type="hidden" name="keebler" value="goof111" />
                            <input type="hidden" name="goof111" value="TRUE" />
                            <input type="hidden" name="redirect" value="' . get_permalink() . '" />
                            <input type="hidden" name="id" value="autoadd" />
                            <input type="hidden" name="which" value="reverb" />
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

// allows using Disqus on development deployments
function childtheme_disqus_development() {
?>
  <script type="text/javascript">
  // see http://docs.disqus.com/help/83/
  var disqus_developer = 1; // developer mode is on
  </script>
<?php }

// only enable this if the server is a .dev domain name
if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== FALSE)
  add_action('wp_head', 'childtheme_disqus_development', 100);

function rvrb_add_excerpts_to_pages() {
    add_post_type_support( 'page', 'excerpt' );
}
add_action( 'init', 'rvrb_add_excerpts_to_pages' );

/* an ad that can be pulled in my the front-page loop */
function rvrb_infinite_ad_widget($iteration) {
    echo '
        <!-- ##ADPLACEMENT## -->
        <div id="cube' . $iteration . '_reverb" style="margin:0 auto;text-align:center;">
        <script>
        if ( $(window).width() >= 300 && $(window).width() < 480 ) {
            googletag.defineSlot(\'/8013/denverpost.com/Entertainment\', [300,250], \'cube1_reverb\').setTargeting(\'pos\',[\'Cube1_RRail_ATF\']).setTargeting(\'kv\', \'reverb\').addService(googletag.pubads());
            googletag.pubads().enableSyncRendering();
            googletag.enableServices();
        }
        </script>';
}

/**
 * Add theme support for infinity scroll
 */
function rvrb_infinite_scroll_render() {
    echo '<h1>RENDERING!</h1>';
    get_template_part('loops/loop', 'frontpage');
}

function rvrb_infinite_scroll_init() {
    add_theme_support( 'infinite-scroll', array(
        'container'         => 'frontpagemain',
        'render'            => 'rvrb_infinite_scroll_render',
        'footer'            => false,
        'posts_per_page'    => 10,
        'footer_widget'     => false,
        'type'              => 'scroll',
    ) );
}
add_action( 'after_setup_theme', 'rvrb_infinite_scroll_init', 20 );



/* EXPERIMENTAL SLIDESHOW PRO SUPPORT STUFF */

define('THEME_JS', get_bloginfo('stylesheet_directory') . '/js/', true);

function generate_thumb($thumb){ 
// By Matt Leyba and Jason Armour
if (isset($thumb))
    /*---------------Start Director Setup ------------------------*/
    // Include DirectorAPI class file
    include('http://www.heyreverb.com/wp-content/plugins/ssp-slideshow/classes/DirectorPHP.php');
    $director = new Director('hosted-9c9cf54218f185433472b1e031a9b8c3', 'reverb.slideshowpro.com');
    //echo('Connected!');
    
     if ($thumb != "") {
        // Separate our comma separated list $thumb into an array
        $thumbnaildata = explode(",", $thumb);
    }   
    
    # When your application is live, it is a good idea to enable caching.
    # You need to provide a string specific to this page and a time limit 
    # for the cache. Note that in most cases, Director will be able to ping
    # back to clear the cache for you after a change is made, so don't be 
    # afraid to set the time limit to a high number.
    $director->cache->set('10122010', '+60 minutes');
    
    # We can also request the album preview at a certain size
    #$director->format->preview(array('width' => "0", 'height' => '0', 'crop' => 0, 'quality' => 85, 'sharpening' => 1));
    $artlicleBig = array('name' => 'artlicleBig', 'width' => "610", 'height' => '384', 'crop' => 1, 'quality' => 80, 'sharpening' => 1);
    $artlicleMed = array('name' => 'artlicleMed', 'width' => "282", 'height' => '178', 'crop' => 1, 'quality' => 80, 'sharpening' => 1);
    $artlicleSm = array('name' => 'artlicleSm', 'width' => "150", 'height' => '150', 'crop' => 1, 'quality' => 80, 'sharpening' => 1);
    $artlicleTh = array('name' => 'artlicleTh', 'width' => "150", 'height' => '150', 'crop' => 1, 'quality' => 80, 'sharpening' => 1);
    $homeSlider = array('name' => 'homeSlider', 'width' => "960", 'height' => '400', 'crop' => 1, 'quality' => 95, 'sharpening' => 1);
    $director->format->add($artlicleBig);
    $director->format->add($artlicleMed);
    $director->format->add($artlicleSm);
    $director->format->add($artlicleTh);
    $director->format->add($homeSlider);
    /*-----------End Director Setup -----------------------------*/  

    // Check to see if user wanted to use a specific image or if we should use the first one as a default
    $x = $thumbnaildata[1] -1;
    if ($x == -1) {
        $x = 0;
    }

    $album = $director->album->get($thumbnaildata[0]);
    $caption = $album->contents->content[$x]->caption;
    $album_name = $album->name;
    
    /*my array of formats*/
    $imginfo = Array (
        'articleBig_url' => $album->contents->content[$x]->artlicleBig->url ."\" width=\"" . $album->contents->content[$x]->artlicleBig->width . "\" height=\"" . $album->contents->content[$x]->artlicleBig->height . "\" alt=\"" . $album_name . "\" /><div class=\"wp-caption\" style=\"margin-right:4px;width:610px;\"><p class=\"wp-caption-text\">".$caption."</p></div>",
        'articleMed_url' =>  $album->contents->content[$x]->artlicleMed->url ."\" width=\"" . $album->contents->content[$x]->artlicleMed->width . "\" height=\"" . $album->contents->content[$x]->artlicleMed->height . "\" alt=\"" . $album_name . "\" /><div class=\"wp-caption\" style=\"margin-right:4px;width:282px;\"><p class=\"wp-caption-text\">".$caption."</p></div>",
        'articleSm_url' =>  $album->contents->content[$x]->artlicleSm->url ."\" width=\"" . $album->contents->content[$x]->artlicleSm->width . "\" height=\"" . $album->contents->content[$x]->artlicleSm->height . "\" alt=\"" . $album_name . "\" />",
        'articleTh_url' =>  $album->contents->content[$x]->artlicleTh->url ."\" width=\"" . $album->contents->content[$x]->artlicleTh->width . "\" height=\"" . $album->contents->content[$x]->artlicleTh->height . "\" alt=\"" . $album_name . "\" />",
        'homeSlider_url' =>  $album->contents->content[$x]->homeSlider->url );
    return $imginfo;
}

add_action('wp_print_scripts', 'enqueueMyScripts');
function enqueueMyScripts(){
if ( is_singular() ) {
    wp_enqueue_script('galleriffic', THEME_JS .'jquery.galleriffic.js', array('jquery'));
    wp_enqueue_script('history', THEME_JS .'jquery.history.js', array('jquery'));
    wp_enqueue_script('opaicty', THEME_JS .'jquery.opacityrollover.js', array('jquery'));
    wp_enqueue_script('gallerifficinit', THEME_JS .'init_slideshow.js', array('galleriffic'), '', true);
    }
}

// comma seperated category list for ad tags
function deeez_cats($category){
$comma_holder = "";
$cat_string = "";
    foreach ($category as $categorysingle) {
    $cat_string .= $comma_holder . '"' . $categorysingle->name . '"';
    $comma_holder = ",";
    }
    return $cat_string;
}

// comma seperated category list for omniture tags
function deeez_cats2($category){
$space_holder = "";
$cat_string = "";
    foreach ($category as $categorysingle) {
    $cat_string .= $space_holder . $categorysingle->name;
    $space_holder = "_";
    }
    return $cat_string;
}

function getCatVal($num){
    $temp=get_the_category();
    $count=count($temp);// Getting the total number of categories the post is filed in.
    for($i=0;$i<$num&&$i<$count;$i++){
        //Formatting our output.
        $cat_string.=$temp[$i]->cat_name;
        if($i!=$num-1&&$i+1<$count)
        //Adding a ',' if it's not the last category.
        //You can add your own separator here.
        $cat_string.=', ';
    }
    echo $cat_string;
}

?>