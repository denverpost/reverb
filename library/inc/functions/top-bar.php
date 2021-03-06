<?php 
/**
 * Top Bar Function
 * output code for the Foundation top bar structure
 * 
 * @package Reactor
 * @author Anthony Wilhelm (@awshout / anthonywilhelm.com)
 * @since 1.0.0
 * @license GNU General Public License v2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 */

if ( !function_exists('reactor_top_bar') ) {
	function reactor_top_bar( $args = '' ) {

		$defaults = array(
			'title'      => get_bloginfo('name'),
			'title_url'  => home_url(),
			'menu_name'  => '',
			'left_menu'  => 'reactor_top_bar_l',
			'right_menu' => 'reactor_top_bar_r',
			'fixed'      => false,
			'contained'  => true,
			'sticky'     => false,
			'search'	 => false,
		);
		$args = wp_parse_args( $args, $defaults );
		$args = apply_filters( 'reactor_top_bar_args', $args );

		$args['right_menu'] = ( $args['search'] ) ? 'reactor_topbar_search' : $args['right_menu'];

		$outdoors = ( is_outdoors() || is_location() ) ? true : false;
		
        // Here's where we override the left menu if it's an Outdoors section
		$args['left_menu'] = ( $outdoors && $args['left_menu'] == 'reactor_top_bar_l' ) ? 'reactor_top_bar_outdoors' : $args['left_menu'];

		/* call functions to create right and left menus in the top bar. defaults to the registered menus for top bar */
        $left_menu = ( ( $args['left_menu'] && is_callable( $args['left_menu'] ) ) ) ? call_user_func( $args['left_menu'], (array) $args ) : '';
        $right_menu = ( ( $args['right_menu'] && is_callable( $args['right_menu'] ) ) ) ? call_user_func( $args['right_menu'], (array) $args ) : '';
		
		// assemble classes for top bar
		$classes = array(); $output = '';
		$classes[] = ( $args['fixed'] ) ? 'fixed' : '';
		$classes[] = ( $args['contained'] ) ? 'contain-to-grid' : '';
		$classes[] = ( $args['sticky'] ) ? 'sticky' : '';
		$classes = array_filter( $classes );
		$classes = implode( ' ', array_map( 'esc_attr', $classes ) );
		$stickyattrib = ( $args['sticky'] ) ? 'sticky_on: all;' : '';
		
		$page_logo = ( is_post_type_archive( 'neighborhoods' ) || ( is_single() && get_post_type() == 'neighborhoods' ) ) ? '/images/to-do-neighborhoods-logo.png' : '/images/to-do-denver-logo.png';
		$page_logo = ( $outdoors ) ? '/images/to-do-outdoors-logo.png' : $page_logo;
		$title_link_url = ( $outdoors ) ? get_category_link( get_cat_ID( 'Outdoors' ) ) : $args['title_url'];

		// start top bar output
		if ( has_nav_menu('top-bar-l') || has_nav_menu('top-bar-r') ) {
			$output .= '<div class="top-bar-container ' . $classes . '">';
				$output .= '<nav class="top-bar" data-topbar data-options="is_hover:true; scrolltop:false; custom_back_text:true; back_text:&laquo; Back; mobile_show_parent_link: true;' . $stickyattrib . '">';
					$output .= '<ul class="title-area">';
						$output .= '<li class="name">';
							$output .= '<p><a href="' . $title_link_url .'"><img src="' . get_stylesheet_directory_uri() . $page_logo .'" alt="' . get_bloginfo('name') . ' logo" /></a></p>';
						$output .= '</li>';
						$output .= '<li class="toggle-topbar menu-icon"><a href="#"><span>' . $args['menu_name'] . '</span></a></li>';
					$output .= '</ul>';
					$output .= '<section class="top-bar-section">';
						$output .= $left_menu;
						$output .= $right_menu;
					$output .= '</section>';
				$output .= '</nav>';
			$output .= '</div>';
			
		echo apply_filters('reactor_top_bar', $output, $args);	
	    }
	}
}

/**
 * Function to use search form in top bar
 * this chould be used as the callback for top bar menus
 *
 * @since 1.0.0
 */
if(!function_exists('reactor_topbar_search')) {
	function reactor_topbar_search( $args = '' ) {
	
		$defaults = array(
			'side' => 'right',
		 );
		$args = wp_parse_args( $args, $defaults );
		$args = apply_filters( 'reactor_top_bar_args', $args );
		
		$output  = '<ul class="' . $args['side'] . '"><li class="has-form" id="searchformbox">';
		$output .= '<form role="search" method="get" id="searchform" action="' . home_url() . '"><div class="row collapse">';
		$output .= '<div class="large-10 small-10 columns">';
		$output .= '<input type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="' . esc_attr__('Search', 'reactor') . '" />';
		$output .= '</div>';
		$output .= '<div class="large-2 small-2 end columns">';
		$output .= '<input class="button prefix" type="submit" id="searchsubmit" value="' . esc_attr__('Search', 'reactor') . '" style="background-image:url(\'' . get_stylesheet_directory_uri() . '/images/icon-search.png\');" />';
		$output .= '</div>';
		$output .= '</div></form>';	
		$output .= '</li></ul>';
		
		return apply_filters('reactor_search_form', $output);
	}
}