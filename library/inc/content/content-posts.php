<?php
/**
 * Post Content
 * hook in the content for post formats
 *
 * @package Reactor
 * @author Anthony Wilhelm (@awshout / anthonywilhelm.com)
 * @since 1.0.0
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 * @license GNU General Public License v2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 */


/**
 * Big image above single posts
 * in all formats when tumblog icons are enabled
 * 
 * @since 1.0.0
 */
function reactor_do_hero() {
	if ( is_single() ) {
		global $post;
		$hero_url = get_post_meta( $post->ID, 'hero_img_url', true );
		if ( is_single() && get_post_type() != 'venues' && get_post_type() != 'neighborhoods' && $hero_url ) { ?>
			<div class="large-12 columns">
				<img src="<?php echo $hero_url; ?>" class="singlehero" />
			</div>
		<?php }
	}
}
add_action('reactor_inner_content_before', 'reactor_do_hero', 0);

/**
 * Post category overline Icons
 * in all formats when tumblog icons are enabled
 * 
 * @since 1.0.0
 */
function reactor_do_overline() {
	if ( is_single() && get_post_type() == 'location' ) { ?>
		<header class="archive-header">
            <h1 class="archive-title venue-header"><span><a href="<?php echo get_bloginfo( 'url' ); ?>/locations/">Location</a></span></h1>
        </header><!-- .archive-header -->
	<?php } else if ( is_single() && get_post_type() == 'venues' ) { ?>
		<header class="archive-header">
            <h1 class="archive-title venue-header"><span><a href="<?php echo get_bloginfo( 'url' ); ?>/venues/">Venues</a></span></h1>
        </header><!-- .archive-header -->
	<?php } else if ( is_single() && get_post_type() == 'neighborhoods' ) { ?>
		<header class="archive-header">
            <h1 class="archive-title neighborhood-header"><span><a href="<?php echo get_bloginfo( 'url' ); ?>/neighborhoods/">Neighborhoods</a></span></h1>
        </header><!-- .archive-header -->
	<?php } else if ( is_single() && ! is_page_template( 'page-templates/calendar.php' ) ) {
		$primary_category = tkno_get_primary_category();
		$class_category = 'archive-title category-' . tkno_get_top_category_slug(true);
		$class_header_category = ' category-' . tkno_get_top_category_slug( true );
		?>
        <header class="archive-header<?php echo $class_header_category; ?>">
            <h2 <?php post_class($class_category); ?>><span><a href="<?php echo $primary_category->url; ?>"><?php echo $primary_category->name; ?></a></span></h2>
        </header><!-- .archive-header -->
	<?php }
}
add_action('reactor_post_before', 'reactor_do_overline', 1);

/**
 * Front page main format
 * in format-standard
 * 
 * @since 1.0.0
 */
function reactor_post_frontpage_format() {

	$primary_category = tkno_get_primary_category();

	if ( has_post_thumbnail() ) {
		$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
	}
	if ( isset( $large_image_url ) && strlen( $large_image_url[0] ) >= 1 ) {
		?><div class="frontpage-image frontpage-post" style="background-image:url('<?php echo $large_image_url[0]; ?>');">
			<div class="front-imgholder"></div>
			<a href="<?php the_permalink(); ?>" rel="bookmark"></a>
		</div>
	<?php } ?>
		<span>
			<a href="<?php echo $primary_category->url; ?>"><?php echo $primary_category->name; ?></a>
		</span>
		<h2 class="entry-title">
			<a href="<?php the_permalink(); ?>" rel="bookmark""><?php the_title(); ?></a>
		</h2> <?php }
add_action('reactor_post_frontpage', 'reactor_post_frontpage_format', 1);

/**
 * Category page main format
 * in format-catpage
 * 
 * @since 1.0.0
 */
function reactor_post_catpage_format() {
	if ( has_post_thumbnail() ) {
		$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large');
	} ?>
	<?php if (isset($large_image_url) && strlen($large_image_url[0]) >= 1) { ?>
	<div class="catpage-post has-image">
		<div class="catpage-image" style="background-image:url('<?php echo $large_image_url[0]; ?>');">
			<div class="catimgspace"></div>
		</div>
	<?php } else { ?>
	<div class="catpage-post">
	<?php } ?>
		<div class="catpage-post-inner">
			<a href="<?php the_permalink(); ?>" rel="bookmark">
				<h2 class="entry-title"><?php the_title(); ?></h2>
			</a>
			<p class="catexcerpt"><?php echo smart_trim( strip_shortcodes( get_the_content() ), 25 ); ?></p>
			<?php 
			reactor_post_meta(array('show_cat'=>false,'show_tag'=>false,'catpage'=>true,'link_date'=>false)); ?>
		</div>
		<div class="clear"></div>
	</div>
<?php }
add_action('reactor_post_catpage', 'reactor_post_catpage_format', 1);

/**
 * Location page main format
 * in format-location
 * 
 * @since 1.0.0
 */
function reactor_post_location_format() { ?>
	<div class="catpage-post">
		<div class="catpage-post-inner">
			<a href="<?php the_permalink(); ?>" rel="bookmark">
				<h2 class="entry-title"><?php the_title(); ?></h2>
			</a>
			<?php 
			reactor_post_meta( array( 'show_cat' => false, 'show_tag' => false, 'location' => true, 'show_date' => true, 'link_date' => false ) ); ?>
		</div>
		<div class="clear"></div>
	</div>
<?php }
add_action('reactor_post_location', 'reactor_post_location_format', 1);

/**
 * Post header
 * in format-standard
 * 
 * @since 1.0.0
 */
function reactor_do_standard_header_titles() {
	$show_titles = reactor_option('frontpage_show_titles', 1);
	$link_titles = reactor_option('frontpage_link_titles', 0);
	global $post;
	if ( is_page_template('page-templates/front-page.php') && $show_titles ) { ?>
		<?php if ( !$link_titles ) { ?>
		<h2 class="entry-title"><?php the_title(); ?></h2>
		<?php } else { ?>
		<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
	<?php }
	} elseif ( is_author() ) { ?>
		<a href="<?php the_permalink(); ?>" rel="bookmark"><h2 class="entry-title"><?php the_title(); ?></h2></a>
	<?php
	} elseif ( !get_post_format() && !is_page_template('page-templates/front-page.php') ) {  ?>    
		<?php if ( is_single() ) { ?>
		<h1 class="entry-title"><?php the_title(); ?></h1>
		<?php if ( get_the_subtitle( $post->ID, '', '', false ) != '' ): ?>
			<h2 class="entry-subtitle"><?php the_subtitle(); ?></h2>
		<?php endif; ?>
		<?php } else { ?>
		<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
		<?php } ?>
<?php }
}
add_action('reactor_post_header', 'reactor_do_standard_header_titles', 3);


/**
 * Post header meta
 * in all formats
 * 
 * @since 1.0.0
 */
function reactor_do_post_header_meta() {

	if ( is_single() && get_post_type() != 'venues' && get_post_type() != 'neighborhoods' && get_post_type() != 'location' ) {
		reactor_post_meta(array('show_cat'=>false,'show_tag'=>false,'catpage'=>true,'link_date'=>false,'social_dropdown'=>false));
	}
}
add_action('reactor_post_header', 'reactor_do_post_header_meta', 4);

/**
 * Post header social
 * in all formats
 * 
 * @since 1.0.0
 */
function reactor_do_post_header_social() {

	if ( is_single() && get_post_type() != 'venues' && get_post_type() != 'neighborhoods' && get_post_type() != 'location' ) { ?>
		<div class="row">
			<div class="large-12 medium-12 small-12 columns masocial">
				<?php echo do_shortcode('[mashshare]'); ?>
			</div>
		</div>
	<?php }
}
add_action('reactor_post_header', 'reactor_do_post_header_social', 5);

/**
 * Post footer venue
 * in all formats
 * 
 * @since 1.0.0
 */
function reactor_do_post_footer_venue() {
	
	global $post;
	$venue = wp_get_post_terms( $post->ID, 'venue' );
	$venue_page = ( ! empty( $venue[0] ) ) ? tkno_get_venue_from_slug( $venue[0]->slug ) : '';

	if ( is_single() && ! $venue_page == '' ) { ?>
		<div class="row">
			<div class="large-12 medium-12 small-12 columns single_venue venue_link">
				<h3>Upcoming events and more about <a href="<?php echo get_permalink( $venue_page->ID ); ?>"><?php echo $venue_page->post_title; ?></a></h3>
			</div>
		</div>
	<?php }
}
add_action('reactor_post_footer', 'reactor_do_post_footer_venue', 1);

/**
 * Post footer neighborhood
 * in all formats
 * 
 * @since 1.0.0
 */
function reactor_do_post_footer_neighborhood() {
	
	global $post;
	$neighborhood = wp_get_post_terms( $post->ID, 'neighborhood' );
	$neighborhood_page = ( ! empty( $neighborhood[0] ) ) ? tkno_get_neighborhood_from_slug( $neighborhood[0]->slug ) : '';

	if ( is_single() && ! $neighborhood_page == '' ) { ?>
		<div class="row">
			<div class="large-12 medium-12 small-12 columns single_neighborhood neighborhood_link">
				<h3>neighborhood more about the neighborhood: <a href="<?php echo get_permalink( $neighborhood_page->ID ); ?>"><?php echo $neighborhood_page->post_title; ?></a></h3>
			</div>
		</div>
	<?php }
}
add_action('reactor_post_footer', 'reactor_do_post_footer_neighborhood', 1);

/**
 * Post footer social
 * in all formats
 * 
 * @since 1.0.0
 */
function reactor_do_post_footer_social() {

	if ( is_single() && get_post_type() != 'neighborhoods' && get_post_type() != 'location' ) { ?>
		<div class="row">
			<div class="large-12 medium-12 small-12 columns masocial">
				<?php echo do_shortcode('[mashshare]'); ?>
			</div>
		</div>
	<?php }
}
add_action('reactor_post_footer', 'reactor_do_post_footer_social', 3);

/**
 * Post content after tags
 * in format-standard
 */
function tkno_post_body_content_tags() {
	if ( is_single() ) {
		reactor_post_meta( array( 'just_tags' => true ) );
	}
}
add_action('reactor_post_footer', 'tkno_post_body_content_tags', 4);

/**
 * Post footer meta
 * in all formats
 * 
 * @since 1.0.0
 */
function reactor_do_post_footer_meta() {
	if ( is_single() && get_post_type() != 'venues' && get_post_type() != 'neighborhoods' && get_post_type() != 'location' ) {
		reactor_post_meta( array('show_photo' => true,'show_tag' => true) );
		$post_meta = true;
	} else if ( get_post_type() != 'venues' && get_post_type() != 'neighborhoods' && get_post_type() != 'location' ) {
		if ( is_page_template('page-templates/front-page.php') ) {
			$post_meta = reactor_option('frontpage_post_meta', 1);
		}
		elseif ( is_page_template('page-templates/news-page.php') ) {
			$post_meta = reactor_option('newspage_post_meta', 1);
		} else {
			$post_meta = reactor_option('post_meta', 1);
		}

		if ( $post_meta && current_theme_supports('reactor-post-meta') ) {
			reactor_post_meta();
		}
	} else if ( get_post_type() == 'venues' ) {
		global $post;
		$venue_calendar_id = ( get_post_meta( $post->ID, '_venue_calendar_id', true ) && get_post_meta( $post->ID, '_venue_calendar_id', true ) != '' ) ? get_post_meta( $post->ID, '_venue_calendar_id', true ) : false;
		
		if ( $venue_calendar_id ) { ?>
			<div class="row">
				<div class="large-12 medium-12 small-12 columns single_venue with_calendar">
				<h3>Upcoming events</h3>
					<div data-cswidget="<?php echo $venue_calendar_id; ?>"> </div>
				</div>
			</div>
		<?php } else {
			$link_uri = 'http://theknow.denverpost.com/calendar/#!/show/?search=' . rawurlencode( $post->post_title ); ?>
			<div class="row">
				<div class="large-12 medium-12 small-12 columns single_venue">
					<h3>More events: <a href="<?php echo $link_uri; ?>"><?php echo $post->post_title; ?> on our calendar</a></h3>
				</div>
			</div>
		<?php }
		}
}
add_action('reactor_post_footer', 'reactor_do_post_footer_meta', 5);

/**
 * Venue pages details 
 * in single.php
 * 
 * @since 1.0.0
 */
function reactor_do_venue_details() {
	if ( is_single() && get_post_type() == 'venues' ) {
		global $post;
		$detail_fields = array();
		$all_fields = get_post_custom( $post->id );
		foreach ( $all_fields as $key => $value ) {
			if ( strpos( $key, 'venue_details_' ) !== false ) {
				$newkey = str_replace('venue_details_', '', $key );
				$detail_fields[$newkey] = $value[0];
			}
		}
		if ( count( $detail_fields ) > 0 ) { ?>
			<div class="row">
				<div class="venue-details">
				<?php foreach( $detail_fields as $key => $value ) { ?>
						<div class="large-6 medium-6 small-12 columns">
							<div class="venue-detail">
								<div class="venue-detail-title"><?php echo ucwords( str_replace('_', ' ', $key ) ); ?></div>
								<div class="venue-detail-value"><?php echo $value; ?></div>
								<div class="clear"></div>
							</div>
							<div class="clear"></div>
						</div>
					<?php } ?>
				<div class="clear"></div>
				</div>
			</div>
		<?php }
	}
}
add_action('reactor_post_footer', 'reactor_do_venue_details', 6);

/**
 * Venue pages map
 * in single.php
 * 
 * @since 1.0.0
 */
function reactor_do_venue_map() {
	if ( is_single() && get_post_type() == 'venues' ) {
		global $post;
		$map_id = get_post_meta( $post->ID, '_venue_map_id', true );
		if ( $map_id != '' ) { ?>
			<div class="row">
				<div class="large-12 medium-12 small-12 columns single_venue">
					<div style="margin:0;padding:0;overflow:hidden;height:300px;width:100%;">
						<iframe src="https://www.google.com/maps/d/embed?mid=<?php echo $map_id; ?>" style="border:none;width:100%;height:365px;margin-top:-50px;" seamless></iframe>
						</div>
				</div>
			</div>
		<?php }
	}
}
add_action('reactor_post_footer', 'reactor_do_venue_map', 7);

/**
 * Post footer edit 
 * in single.php
 * 
 * @since 1.0.0
 */
function reactor_do_post_edit() {
	if ( is_single() ) {
		edit_post_link( __('Edit this post', 'reactor'), '<div class="edit-link"><span>', '</span></div>');
	} else if ( is_single() && get_post_type() == 'venues' ) {
		edit_post_link( __('Edit this Venue page', 'reactor'), '<div class="edit-link"><span>', '</span></div>');
	} else if ( is_single() && get_post_type() == 'neighborhoods' ) {
		edit_post_link( __('Edit this Neighborhood page', 'reactor'), '<div class="edit-link"><span>', '</span></div>');
	} else if ( is_single() && get_post_type() == 'location' ) {
		edit_post_link( __('Edit this Location page', 'reactor'), '<div class="edit-link"><span>', '</span></div>');
	}
}
add_action('reactor_post_footer', 'reactor_do_post_edit', 8);

/**
 * Single post nav 
 * in single.php
 * 
 * @since 1.0.0
 */
function reactor_do_nav_single() {
    if ( is_single() ) { 
    $exclude = ( reactor_option('frontpage_exclude_cat', 1) ) ? reactor_option('frontpage_post_category', '') : ''; ?>
        <nav class="nav-single">
            <span class="nav-previous alignleft">
            <?php previous_post_link('%link', 'Last hit: %title', false, $exclude); ?>
            </span>
            <span class="nav-next">
            <?php next_post_link('%link', 'Up next: %title', false, $exclude); ?>
            </span>
        </nav><!-- .nav-single -->
<?php }
}
add_action('reactor_post_after', 'reactor_do_nav_single', 3);

/**
 * Single post related 
 * in single.php
 * 
 * @since 1.0.0
 */
function tkno_single_post_related() {
    if ( is_single() && get_post_type() != 'venues' && get_post_type() != 'neighborhoods' && get_post_type() != 'location' && function_exists( 'yarpp_related' ) ) {
    	global $post; ?>
	    <div class="related">
		    <?php yarpp_related( array( 
		    	'post_type'			=> array('post'),
		    	'show_pass_post'	=> false,
		    	'exclude'			=> array(),
		    	'recent'			=> '6 month',
		    	'weight'			=> array(
		    		'tax'	=> array(
		    			'post_tag'		=> 2,
		    			'venue'			=> 1,
		    			'neighborhood'	=> 1
		    		)
		    	),
		    	'threshold'			=> 2,
		    	'template'			=> 'yarpp-template-rvrb.php',
		    	'limit'				=> 3,
		    	'order'				=> 'score DESC'
		    	),
	    	$post->ID,
	    	true); ?>
	    </div>
	<?php }
}
add_action('reactor_post_after', 'tkno_single_post_related', 4);

/**
 * Single neighborhood children 
 * in single.php
 * 
 * @since 1.0.0
 */
function tkno_single_neighborhood_children() {
    if ( is_single() && get_post_type() == 'neighborhoods' ) {
    	global $post;
    	$neighborhood_slug = get_post_meta( $post->ID, '_neighborhood_slug', true );
    	$neighborhood_obj = get_term_by( 'slug', $neighborhood_slug, 'neighborhood' );
		$neighborhood_children = get_term_children( $neighborhood_obj->term_id, 'neighborhood' );
		$list_neighborhoods = array();
		foreach ( $neighborhood_children as $child_id ) {
			$child = get_term_by( 'id', $child_id, 'neighborhood' );
			$child_page = tkno_get_neighborhood_from_slug( $child->slug );
			if ( $child_page != false && $child_page->ID ) {
				$list_neighborhoods[] = array(
					'url' => get_post_permalink( $child_page->ID ),
					'name' => $child->name
				);
			}
		}
		if ( count( $list_neighborhoods ) > 0 ) { ?>
			<div class="neighborhood_children_list">
				<h3><span>Neighborhoods within <?php echo $neighborhood_obj->name; ?></span></h3>
				<ul class="inline-list">
					<?php foreach ( $list_neighborhoods as $neighborhood_item ) { ?>
						<li><a href="<?php echo $neighborhood_item[ 'url' ] ?>"><?php echo $neighborhood_item[ 'name' ]; ?></a></li>
					<?php } ?>
					
			</div> <?php
		}
		?>
	    
	<?php }
}
add_action('reactor_post_after', 'tkno_single_neighborhood_children', 5);

/**
 * No posts format
 * loop else in page templates
 * 
 * @since 1.0.0
 */
function reactor_do_loop_else() {
	get_template_part('post-formats/format', 'none');
}
add_action('reactor_loop_else', 'reactor_do_loop_else', 1);
