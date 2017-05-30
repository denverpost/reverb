<?php
/**
 * Scripts
 * WordPress will add these scripts to the theme
 *
 * @package Reactor
 * @author Anthony Wilhelm (@awshout / anthonywilhelm.com)
 * @since 1.0.0
 * @link http://codex.wordpress.org/Function_Reference/wp_register_script
 * @link http://codex.wordpress.org/Function_Reference/wp_enqueue_script
 * @see wp_register_script
 * @see wp_enqueue_script
 * @license GNU General Public License v2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 */

/**
 * Reactor Scripts
 *
 * @since 1.0.0
 */
add_action('wp_enqueue_scripts', 'reactor_register_scripts', 1);
add_action('wp_enqueue_scripts', 'reactor_enqueue_scripts');
 
function reactor_register_scripts() {
	// register scripts
	wp_register_script('modernizr-js', get_template_directory_uri() . '/library/js/vendor/custom.modernizr.js', array(), false, true);
	wp_register_script('jqueryui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js', array('jquery'), false, true);
	wp_register_script('foundation-js', get_template_directory_uri() . '/library/js/foundation.min.js', array('jquery'), false, true);
	wp_register_script('reactor-js', get_template_directory_uri() . '/library/js/reactor.js', array(), false, true);
	//Added DJS
	wp_register_script('jquery-inview', get_stylesheet_directory_uri() . '/library/js/jquery.inview.min.js', array('jquery'), false, true);
	wp_register_script('jquerymobile', get_stylesheet_directory_uri() . '/library/js/jquery.mobile.custom.min.js', array('minit','jquery'), false, true);
	wp_register_script('rvfunctions-js', get_stylesheet_directory_uri() . '/library/js/rv-functions.js', array(), false, true);
	wp_register_script('cityspark', '//portal.CitySpark.com/js/widget.min.js', array(), false, true);
	wp_register_script('gads-js', '//www.googletagservices.com/tag/js/gpt.js', array(), false, false);
	wp_register_script('adnexus', 'https://secure.adnxs.com/seg?add=2836221&t=1', array('jquery'), false, true);
	wp_register_script('swipes-js', get_stylesheet_directory_uri() . '/library/js/swipes.js', array('jquerymobile','jquery'), false, true);
	wp_register_script('ndn-js', '//launch.newsinc.com/js/embed.js', array(), false, true);
}

function reactor_enqueue_scripts() {
	if ( !is_admin() ) { 
		// enqueue scripts
		wp_enqueue_script('jquery');
		//wp_enqueue_script('zepto-js');
		wp_enqueue_script('modernizr-js');
		wp_enqueue_script('jqueryui');
		wp_enqueue_script('foundation-js');
		wp_enqueue_script('reactor-js');
		//Added DJS
		wp_enqueue_script('jquery-inview');
		wp_enqueue_script('rvfunctions-js');
		//enqueue on single only
		if ( is_single() ) {
			wp_enqueue_script('jquerymobile');
			wp_enqueue_script('swipes-js');
		}
		wp_enqueue_script('gads-js');
		wp_enqueue_script('adnexus');
		wp_enqueue_script('ndn-js');
		wp_enqueue_script('cityspark');
	}
}