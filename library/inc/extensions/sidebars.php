<?php
/**
 * Register Sidebar Widget Areas
 *
 * @package Reactor
 * @author Anthony Wilhelm (@awshout / anthonywilhelm.com)
 * @since 1.0.0
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 * @see register_sidebar
 * @license GNU General Public License v2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 */
add_action('widgets_init', 'reactor_register_sidebars'); 

function reactor_register_sidebars() {

	$sidebars = get_theme_support( 'reactor-sidebars' );
	
	if ( !is_array( $sidebars[0] ) ) {
		return;
	}
	
	if ( in_array( 'primary', $sidebars[0] ) ) {
		register_sidebar( array( 
			'name'          => __('Primary Sidebar', 'reactor'),
			'id'            => 'sidebar',
			'description'   => 'The primary sidebar for 2 column layouts',
			'class'         => '',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );
	}

	if ( in_array( 'outdoors', $sidebars[0] ) ) {
		register_sidebar( array( 
			'name'          => __('Outdoors Sidebar', 'reactor'),
			'id'            => 'sidebar-outdoors',
			'description'   => 'The primary sidebar for Outdoors pages',
			'class'         => '',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );
	}
	
	if ( in_array( 'front-upper', $sidebars[0] ) ) {	
		register_sidebar( array( 
			'name'          => __('Frontpage Upper Sidebar', 'reactor'),
			'id'            => 'sidebar-frontupper',
			'description'   => 'Lower sidebar for the front page template',
			'class'         => '',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );
	}

	if ( in_array( 'front-lower', $sidebars[0] ) ) {
		register_sidebar( array(
			'name'          => __('Front Page Lower', 'reactor'),
			'id'            => 'sidebar-frontlower',
			'description'   => 'Lower sidebar for the front page template',
			'class'         => '',
			'before_widget' => '<div id="%1$s" class="widget frontpage-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );
	}

	if ( in_array( 'front-mobile', $sidebars[0] ) ) {
		register_sidebar( array(
			'name'          => __('Front Page mobile', 'reactor'),
			'id'            => 'sidebar-frontmobile',
			'description'   => 'Mobile-only sidebar for the front page template',
			'class'         => '',
			'before_widget' => '<div id="%1$s" class="widget frontpage-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );
	}

	if ( in_array( 'footer', $sidebars[0] ) ) {
		$footer  = '<div id="%1$s" class="widget top-bar-widget ';
		$footer .= 'large-' . reactor_get_widget_columns('sidebar-footer');
		$footer .= ' columns %2$s">';
		register_sidebar( array(
			'name'          => __('Footer', 'reactor'),
			'id'            => 'sidebar-footer',
			'description'   => 'Footer widget area',
			'class'         => '',
			'before_widget' => $footer,
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );
	}
	
	if ( in_array( 'neighborhood-upper', $sidebars[0] ) ) {	
		register_sidebar( array( 
			'name'          => __('Neighborhood Upper Sidebar', 'reactor'),
			'id'            => 'sidebar-neighborhoodupper',
			'description'   => 'Lower sidebar for the Neighborhood page template',
			'class'         => '',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );
	}

	if ( in_array( 'neighborhood-lower', $sidebars[0] ) ) {
		register_sidebar( array(
			'name'          => __('Neighborhood Page Lower', 'reactor'),
			'id'            => 'sidebar-neighborhoodlower',
			'description'   => 'Lower sidebar for the Neighborhood page template',
			'class'         => '',
			'before_widget' => '<li id="%1$s" class="widget %2$s">',
			'after_widget'  => '</li>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );
	}

	if ( in_array( 'outdoor-upper', $sidebars[0] ) ) {
		register_sidebar( array(
			'name'          => __('Outdoors Page Upper', 'reactor'),
			'id'            => 'sidebar-outdoorupper',
			'description'   => 'Top-of-page sidebar for the Outdoors template',
			'class'         => '',
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );
	}

	if ( in_array( 'outdoor-middle', $sidebars[0] ) ) {
		register_sidebar( array(
			'name'          => __('Outdoors Page Middle', 'reactor'),
			'id'            => 'sidebar-outdoormiddle',
			'description'   => 'Mid-page sidebar for the Outdoors template',
			'class'         => '',
			'before_widget' => '<li id="%1$s" class="widget %2$s">',
			'after_widget'  => '</li>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );
	}

	if ( in_array( 'category-sponsor', $sidebars[0] ) ) {
		register_sidebar( array(
			'name'          => __('Category Sponsors', 'reactor'),
			'id'            => 'sidebar-categorysponsor',
			'description'   => 'Category sponsorships',
			'class'         => '',
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '',
			'after_title'   => '',
		) );
	}
}

/**
 * Count Widgets
 * Count the number of widgets to add dynamic column class
 *
 * @param string $sidebar_id id of sidebar
 * @since 1.0.0
 */
function reactor_get_widget_columns( $sidebar_id ) {
	// Default number of columns in Foundation grid is 12
	$columns = apply_filters( 'reactor_columns', 12 );
	
	// get the sidebar widgets
	$the_sidebars = wp_get_sidebars_widgets();
	
	// if sidebar doesn't exist return error
	if ( !isset( $the_sidebars[$sidebar_id] ) ) {
		return __('Invalid sidebar ID', 'reactor');
	}
	
	/* count number of widgets in the sidebar
	and do some simple math to calculate the columns */
	$num = count( $the_sidebars[$sidebar_id] );
	switch( $num ) {
		case 1 : $num = $columns; break;
		case 2 : $num = $columns / 2; break;
		case 3 : $num = $columns / 3; break;
		case 4 : $num = $columns / 4; break;
		case 5 : $num = $columns / 5; break;
		case 6 : $num = $columns / 6; break;
		case 7 : $num = $columns / 7; break;
		case 8 : $num = $columns / 8; break;
	}
	$num = floor( $num );
	return $num;
}