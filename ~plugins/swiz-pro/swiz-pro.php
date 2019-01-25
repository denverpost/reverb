<?php
/*
Plugin Name: SWIZ Pro
Plugin URI: http://www.highervisual.com
Description: SWIZ Pro is the future of content marketing. Add powerful, custom-styled and responsive quizzes and contests to your site. Increase engagement and shares while building your mailing list!
Version: 0.37
Author: Higher Visual and Webonise Lab 
Author URI: http://www.highervisual.com
Text Domain: swiz-pro
Domain Path: /languages
*/
require_once (ABSPATH . "wp-load.php");
require_once( ABSPATH . "wp-includes/pluggable.php" );

define('WPPROQUIZ_VERSION', '0.37');

define('WPPROQUIZ_DEV', false);

define('WPPROQUIZ_PATH', dirname(__FILE__));
define('WPPROQUIZ_URL', plugins_url('', __FILE__));
define('WPPROQUIZ_FILE', __FILE__);
define('WPPROQUIZ_PPATH', dirname(plugin_basename(__FILE__)));
define('WPPROQUIZ_PLUGIN_PATH', WPPROQUIZ_PATH . '/plugins/');

$uploadDir = wp_upload_dir();

define('WPPROQUIZ_CAPTCHA_DIR', $uploadDir['basedir'] . '/wp_pro_quiz_captcha');
define('WPPROQUIZ_CAPTCHA_URL', $uploadDir['baseurl'] . '/wp_pro_quiz_captcha');

//OG data
define('OG_TAG_TYPE', 'website');
define('DEFAULT_OG_TAG_TITLE', 'SWIZ Pro');
define('DEFAULT_OG_TAG_URL', ( is_ssl() ? 'https' : 'http' ) . "://$_SERVER[HTTP_HOST]");
define('DEFAULT_OG_TAG_DESC', ' SWIZ Pro is the world\'s most powerful sweepstakes and quiz tool offered by Denver Post Media');

//Social share
define('TWITTER_CARD', 'summary');
define('FACEBOOK_SHARE_URL', 'https://www.facebook.com/dialog/feed');
define('TWITTER_SHARE_URL', 'https://twitter.com/intent/tweet');
define('LINKEDIN_SHARE_URL', 'https://www.linkedin.com/shareArticle');
define('FACEBOOK_APP_ID', '135916837210510'); // SWIZ Pro
define('BUCKETLIST_TAGS', '#knowCOsummer');

//General
define('DEFAULT_PC_GALLERY_IMG', '6');
define('TIMEZONE', '-7');
define('WPPROQUIZ_DEFAULT_DB_VERSION', '25');
define('ADMIN_TEXTAREA_ROWS', '10');

//Css
define('FREECHOICE_QUESTION_CLASS', 'swizpro_freeChoice');
define('PHOTOCONTEST_CLASS', 'swizpro_photoContest');
define('THIS_THAT_CLASS', 'swizpro_thisOrThatQuiz');
define('CUSTOM_CSS_FILENAME', 'custom-css.php');
define('SMALL_CHECKBOX_CSS', 'small-checkboxes.css');
define('MEDIUM_CHECKBOX_CSS', 'medium-checkboxes.css');
define('LARGE_CHECKBOX_CSS', 'large-checkboxes.css');
define('DENVERPOST_CSS', 'denverpost.css');
define('THEKNOW_CSS', 'theknow.css');
define('WPPROQUIZ_CUSTOM_STYLE_PATH', WPPROQUIZ_PATH.'/css/custom_styles');
define('WPPROQUIZ_CUSTOM_STYLE_URL', WPPROQUIZ_URL.'/css/custom_styles');

//Content text
define('DEFAULT_QUESTION_TAB_MSG', 'Before adding questions, please add a quiz title and type, complete the Content tab, then save to create the quiz.');
define('DEFAULT_PC_ENTIRY_TAB_MSG', 'Entries will appear after you complete the Content tab, and publish the contest.');
define('GIVEAWAY_CONTEST_TEXT', 'Giveaway Contest');
define('GIVEAWAY_CONTEST_VALUE', 'giveaway-contest');
define('PHOTO_CONTEST_TEXT', 'Photo Contest');
define('PHOTO_CONTEST_VALUE', 'photo-contest');
define('CHALLENGE_QUIZ_TEXT', 'Challenge Quiz');
define('CHALLENGE_QUIZ_VALUE', 'challenge-quiz');
define('THIS_OR_THAT_QUIZ_TEXT', 'This or That Quiz');
define('THIS_OR_THAT_QUIZ_VALUE', 'this-that-quiz');
define('BUCKETLIST_TEXT', 'Colorado Bucket List');
define('BUCKETLIST_VALUE', 'bucketlist');
define('DEFAULT_PC_LEFT_HEADER_TITLE', 'Date');
define('DEFAULT_PC_CENTER_HEADER_TITLE', 'Prize');
define('DEFAULT_PC_RIGHT_HEADER_TITLE', 'Winner');
define('DEFAULT_CUSTOM_FORM_HEADER', '');
define('DEFAULT_PRIVACY_POLICY_TEXT', 'Privacy Policy');
define('DEFAULT_TERMS_CONDITIONS_TEXT', 'Terms &amp; Conditions');
define('DEFAULT_CONTEST_RULES_TEXT', 'Contest Rules');

// Image
define('WPPROQUIZ_IMAGE_PATH', WPPROQUIZ_URL.'/img');
define('WPPROQUIZ_DP_IMAGE', WPPROQUIZ_IMAGE_PATH.'/denverpost.jpeg');
define('WPPROQUIZ_DP_COVER_IMAGE', WPPROQUIZ_IMAGE_PATH.'/swiz-pro-image.png');
define('WPPROQUIZ_DPM_LOGO', WPPROQUIZ_IMAGE_PATH.'/swiz-pro-logo.png');
define('SWIZPRO_LOGO', WPPROQUIZ_IMAGE_PATH.'/swizProLogo.png');
define('ADMIN_PC_IMAGE_HEIGHT', '100');
define('ADMIN_PC_IMAGE_WIDTH', '150');
define('DEFAULT_CUSTOM_THUMBNAIL_WIDTH', '250');
define('DEFAULT_CUSTOM_THUMBNAIL_HEIGHT', '250');
define('VALID_IMAGE_EXT', '.png|.jpg|.gif|.jpeg|.PNG|.JPG|.GIF|.JPEG');

// iframe
define('IFRAME_PAGE_TITLE', 'swiz-pro');

spl_autoload_register('wpProQuiz_autoload');

register_activation_hook(__FILE__, array('WpProQuiz_Helper_Upgrade', 'upgrade'));

add_action('plugins_loaded', 'wpProQuiz_pluginLoaded');

if (is_admin()) {
    new WpProQuiz_Controller_Admin();
} else {
    if(isset($_GET['wp-pro-quiz'])) {
        $wp_rewrite = new wp_rewrite;
        if ( !defined('WP_POST_REVISIONS') )
            define('WP_POST_REVISIONS', true);
        fetchIframePage();
    }
    new WpProQuiz_Controller_Front();
}


function fetchIframePage(){
    $iframePageTitle = IFRAME_PAGE_TITLE;
    $iframePageContent = '[WpProQuiz' . ' ' . $_GET['wp-pro-quiz'] . ']';
    $pageExsistCheck = get_page_by_title($iframePageTitle);
    $slug = sanitize_title_with_dashes($iframePageTitle);
    $iframePageUrl =  DEFAULT_OG_TAG_URL . "/" . $slug;
    if(!isset($pageExsistCheck->ID)){
        $iframePage = array(
            'post_type' => 'page',
            'post_title' => $iframePageTitle,
            'post_content' => $iframePageContent,
            'post_status' => 'publish',
            'post_author' => 1,
            'post_name' => $slug,
        );
        $pageId = wp_insert_post($iframePage);
        update_post_meta( $pageId, '_wp_page_template', 'template-full-width.php');
    } else {
        $pageId = $pageExsistCheck->ID;
        $iframePage = array(
            'ID' => $pageId,
            'post_status' => 'publish',   //Added incase this page is moved to trash
            'post_content' => $iframePageContent,
        );
        wp_update_post( $iframePage );
    }
    echo "<script>window.location.replace('".$iframePageUrl."')</script>";
}

function wpProQuiz_autoload($class)
{
    $c = explode('_', $class);

    if ($c === false || count($c) != 3 || $c[0] !== 'WpProQuiz') {
        return;
    }

    switch ($c[1]) {
        case 'View':
            $dir = 'view';
            break;
        case 'Model':
            $dir = 'model';
            break;
        case 'Helper':
            $dir = 'helper';
            break;
        case 'Controller':
            $dir = 'controller';
            break;
        case 'Plugin':
            $dir = 'plugin';
            break;
        default:
            return;
    }

    $classPath = WPPROQUIZ_PATH . '/lib/' . $dir . '/' . $class . '.php';

    if (file_exists($classPath)) {
        /** @noinspection PhpIncludeInspection */
        include_once $classPath;
    }
}

function wpProQuiz_pluginLoaded()
{
	if(!get_option('wpProQuiz_thumbnail_size_w')){
		add_option('wpProQuiz_thumbnail_size_w', DEFAULT_CUSTOM_THUMBNAIL_WIDTH);
	}

	if(!get_option('wpProQuiz_thumbnail_size_h')){
		add_option('wpProQuiz_thumbnail_size_h', DEFAULT_CUSTOM_THUMBNAIL_HEIGHT);
	}

	add_image_size('swiz-pro-thumbnail', 
		get_option('wpProQuiz_thumbnail_size_w'),get_option('wpProQuiz_thumbnail_size_h'),1);

    load_plugin_textdomain('wp-pro-quiz', false, WPPROQUIZ_PPATH . '/languages');

    if (get_option('wpProQuiz_version') !== WPPROQUIZ_VERSION) {
        WpProQuiz_Helper_Upgrade::upgrade();
    }
}

function wpProQuiz_achievementsV3()
{
    if (function_exists('achievements')) {
        achievements()->extensions->wp_pro_quiz = new WpProQuiz_Plugin_BpAchievementsV3();

        do_action('wpProQuiz_achievementsV3');
    }
}

add_action('dpa_ready', 'wpProQuiz_achievementsV3');

/*
 * Admin Css enqueued
 *
 */
function loadAdminCustomScripts()
{
    wp_enqueue_style('jquery-ui',
        'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css');
        

    wp_enqueue_style('quizme_admin_front_style',
        plugins_url('css/quizme-admin.css', WPPROQUIZ_FILE),
        array(),
        WPPROQUIZ_VERSION
    );

    wp_enqueue_style('quizme_admin_front_responsive_style',
        plugins_url('css/quizme-admin-responsive.css', WPPROQUIZ_FILE),
        array(),
        WPPROQUIZ_VERSION
    );
}

add_action('admin_enqueue_scripts','loadAdminCustomScripts');

// To set the visual mode as default 
add_filter( 'wp_default_editor', create_function('', 'return "tinymce";'));

add_action('admin_enqueue_scripts','loadAdminCustomScripts');

