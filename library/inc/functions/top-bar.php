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

if ( !function_exists('reactor_top_bar_social') ) {
	function reactor_top_bar_social() {
		if ( is_singular() ) {
			$social_dropdown = '';
			$text = html_entity_decode(get_the_title());
			if ( has_post_thumbnail() ) {
				$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large');
			} else {
				$image = get_stylesheet_directory_uri() . '/images/logo-large.png';
			}
			$desc = ( get_the_excerpt() != '' ? get_the_excerpt() : get_bloginfo('description') );
			$social_dropdown_link = '<a href="javascript:void(0);" aria-controls="socialdrop" aria-expanded="false" data-dropdown="topbarsocialdrop"><span class="fi-share social-dropdown-link"></span></a>';
			$social_dropdown .= '<ul id="topbarsocialdrop" class="tiny content f-dropdown" data-dropdown-content>';
			//Twitter button
			$social_dropdown .= sprintf(
			    '<li class="dropdown-social pm-twitter"><a href="javascript:void(0)" onclick="javascript:window.open(\'http://twitter.com/share?text=%1$s&amp;url=%2$s&amp;via=%3$s\', \'twitwin\', \'left=20,top=20,width=500,height=500,toolbar=1,resizable=1\');"><span class="fi-social-twitter">Twitter</span></a></li>',
			    urlencode(html_entity_decode($text, ENT_COMPAT, 'UTF-8') . ':'),
			    rawurlencode( get_permalink() ),
			    'rvrb'
			);
			//Facebook share
			$social_dropdown .= sprintf(
			    '<li class="dropdown-social pm-facebook"><a href="javascript:void(0)" onclick="javascript:window.open(\'http://www.facebook.com/sharer/sharer.php?s=100&amp;p[url]=%1$s&amp;p[images][0]=%2$s&amp;p[title]=%3$s&amp;p[summary]=%4$s\', \'fbwin\', \'left=20,top=20,width=500,height=500,toolbar=1,resizable=1\');"><span class="fi-social-facebook">Facebook</span></a></li>',
			    rawurlencode( get_permalink() ),
			    rawurlencode( $image[0] ),
			    urlencode( html_entity_decode($text, ENT_COMPAT, 'UTF-8') ),
			    urlencode( html_entity_decode( $desc, ENT_COMPAT, 'UTF-8' ) )
			);
			//Google plus share
			$social_dropdown .= sprintf(
			    '<li class="dropdown-social pm-googleplus"><a href="javascript:void(0)" onclick="javascript:window.open(\'http://plus.google.com/share?url=%1$s\', \'gpluswin\', \'left=20,top=20,width=500,height=500,toolbar=1,resizable=1\');"><span class="fi-social-google-plus">Google+</span></a></li>',
			    rawurlencode( get_permalink() )
			);
			//Linkedin share
			$social_dropdown .= sprintf(
			    '<li class="dropdown-social pm-linkedin"><a href="javascript:void(0)" onclick="javascript:window.open(\'http://www.linkedin.com/shareArticle?mini=true&amp;url=%1$s&amp;title=%2$s&amp;source=%3$s\', \'linkedwin\', \'left=20,top=20,width=500,height=500,toolbar=1,resizable=1\');"><span class="fi-social-linkedin">LinkedIn</span></a></li>',
			    rawurlencode( get_permalink() ),
			    urlencode( html_entity_decode($text, ENT_COMPAT, 'UTF-8') ),
			    rawurlencode( home_url() )
			);
			//Pinterest Pin This
			$social_dropdown .= sprintf(
			    '<li class="dropdown-social pm-linkedin"><a href="javascript:void(0)" onclick="javascript:window.open(\'http://pinterest.com/pin/create/button/?url=%1$s&amp;media=%2$s&amp;description=%3$s\', \'pintwin\', \'left=20,top=20,width=500,height=500,toolbar=1,resizable=1\');"><span class="fi-social-pinterest">Pinterest</span></a></li>',
			    rawurlencode( get_permalink() ),
			    rawurlencode( $image[0] ),
			    urlencode( html_entity_decode($text, ENT_COMPAT, 'UTF-8') )
			);
			//Reddit submit
			$social_dropdown .= sprintf(
			    '<li class="dropdown-social pm-reddit"><a href="javascript:void(0)" onclick="javascript:window.open(\'http://www.reddit.com/submit?url=%1$s&amp;title=%2$s\', \'redditwin\', \'left=20,top=20,width=900,height=700,toolbar=1,resizable=1\');"><span class="fi-social-reddit">Reddit</span></a></li>',
			    rawurlencode( get_permalink() ),
			    urlencode( html_entity_decode($text, ENT_COMPAT, 'UTF-8') )
			);
			$social_dropdown .= '</ul>';
			return array($social_dropdown_link,$social_dropdown);
		}
	}
}

if ( !function_exists('reactor_top_bar') ) {
	function reactor_top_bar( $args = '' ) {

		$defaults = array(
			'title'      => get_bloginfo('name'),
			'title_url'  => home_url(),
			'menu_name'  => '',
			'left_menu'  => 'reactor_top_bar_l',
			'right_menu' => 'reactor_top_bar_r',
			'search_menu'=> false,
			'fixed'      => false,
			'contained'  => true,
			'sticky'     => false,
			'search'	 => false,
		);
		$args = wp_parse_args( $args, $defaults );
		$args = apply_filters( 'reactor_top_bar_args', $args );

		$args['search_menu'] = ( $args['search'] ) ? 'reactor_topbar_search' : $args['search_menu'];
		
		/* call functions to create right and left menus in the top bar. defaults to the registered menus for top bar */
        $left_menu = ( ( $args['left_menu'] && is_callable( $args['left_menu'] ) ) ) ? call_user_func( $args['left_menu'], (array) $args ) : '';
        $right_menu = ( ( $args['right_menu'] && is_callable( $args['right_menu'] ) ) ) ? call_user_func( $args['right_menu'], (array) $args ) : '';
        $search_menu = ( ( $args['search_menu'] && is_callable( $args['search_menu'] ) ) ) ? call_user_func( $args['search_menu'], (array) $args ) : '';

        $social_dropdown = reactor_top_bar_social();
		
		// assemble classes for top bar
		$classes = array(); $output = '';
		$classes[] = ( $args['fixed'] ) ? 'fixed' : '';
		$classes[] = ( $args['contained'] ) ? 'contain-to-grid' : '';
		$classes[] = ( $args['sticky'] ) ? 'sticky' : '';
		$classes = array_filter( $classes );
		$classes = implode( ' ', array_map( 'esc_attr', $classes ) );
		$stickyattrib = ( $args['sticky'] ) ? 'sticky_on: all;' : '';
		
		// start top bar output
		if ( has_nav_menu('top-bar-l') || has_nav_menu('top-bar-r') ) {
			$output .= '<div class="top-bar-container ' . $classes . '">';
				$output .= '<nav class="top-bar" data-topbar data-options="is_hover:true; scrolltop:false; custom_back_text:true; back_text:&laquo; Back; mobile_show_parent_link: true;' . $stickyattrib . '">';
					$output .= '<section class="top-bar-section">';
						$output .= $left_menu;
					$output .= '<div class="title-area">';
						$output .= '<li class="name">';
							$output .= '<p><a href="' . $args['title_url'] .'"><img src="' . get_stylesheet_directory_uri() . '/images/to-do-denver-logo.png" alt="Reverb site logo" /></a></p>';
						$output .= '</li>';
						$output .= '<li class="toggle-social menu-icon">' . $social_dropdown[0] . '</li>';
						$output .= '<li class="toggle-topbar menu-icon"><a href="#"><span>' . $args['menu_name'] . '</span></a></li>';
					$output .= '</div>';
					$output .= '</section>';
					$output .= '<section class="top-bar-section">';
						$output .= $search_menu;
					$output .= '</section>';
					$output .= '<section class="top-bar-section">';
						$output .= $right_menu;
					$output .= '</section>';
				$output .= '</nav>';
			$output .= '</div>';
			$output .= $social_dropdown[1];
			
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
		
		$output  = '<ul class="' . $args['side'] . '"><li class="has-form">';
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
