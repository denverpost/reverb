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
 * Post category overline Icons
 * in all formats when tumblog icons are enabled
 * 
 * @since 1.0.0
 */
function reactor_do_overline() {
	if ( get_post_type() == 'venues' ) { ?>
		<header class="archive-header">
            <h1 class="archive-title venue-header"><span><a href="/venues/">Venues</a></span></h1>
        </header><!-- .archive-header -->
	<?php } else if ( is_single() ) {
		$primary_category = rvrb_get_primary_category();
		?>
        <header class="archive-header">
            <h2 <?php post_class('archive-title'); ?>><span><a href="<?php echo $primary_category->url; ?>"><?php echo $primary_category->name; ?></a></span></h2>
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

	$primary_category = rvrb_get_primary_category();

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
 * in format-standard
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
			<p class="catexcerpt"><?php echo smart_trim(get_the_content(),25); ?></p>
			<?php 
			reactor_post_meta(array('show_cat'=>false,'show_tag'=>false,'catpage'=>true,'link_date'=>false)); ?>
		</div>
		<div class="clear"></div>
	</div>
<?php }
add_action('reactor_post_catpage', 'reactor_post_catpage_format', 1);


/**
 * Post header
 * in format-standard
 * 
 * @since 1.0.0
 */
function reactor_do_standard_header_titles() {
	$show_titles = reactor_option('frontpage_show_titles', 1);
	$link_titles = reactor_option('frontpage_link_titles', 0);
	
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
		<?php } else { ?>
		<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
		<?php } ?>
<?php }
}
add_action('reactor_post_header', 'reactor_do_standard_header_titles', 3);


/**
 * Post footer meta
 * in all formats
 * 
 * @since 1.0.0
 */
function reactor_do_post_header_meta() {

	if ( is_single() ) {
		reactor_post_meta(array('show_cat'=>false,'show_tag'=>false,'catpage'=>true,'link_date'=>false,'social_dropdown'=>true));
	}
}
add_action('reactor_post_header', 'reactor_do_post_header_meta', 4);

/**
 * Post thumbnail
 * in format-standard
 * 
 * @since 1.0.0
 */
function reactor_do_standard_thumbnail() { 
	$link_titles = reactor_option('frontpage_link_titles', 0);
	
	if ( has_post_thumbnail() ) { 
		$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large');
		?>
		<div class="entry-thumbnail">
		<?php if ( is_page_template('page-templates/front-page.php') ) {
			?>
			<div class="mainimgholder"></div>
			<a href="<?php the_permalink(); ?>" rel="bookmark"><div class="mainimg" style="background-image:url('<?php echo $large_image_url[0]; ?>');"></div></a>
        <?php } else { ?>
			<?php if (!is_single()) { ?><a href="<?php the_permalink(); ?>" rel="bookmark"><?php } ?>
				<span class="postimg" style="background-image:url('<?php echo $large_image_url[0]; ?>');"></span>
			<?php if (!is_single()) { ?></a><?php } ?>
        <?php } ?>
		</div>
	<?php }
}
add_action('reactor_post_header', 'reactor_do_standard_thumbnail', 4);

/**
 * Post content after tags
 * in format-standard
 */
function rvrb_post_body_content_tags() {
	if ( is_single() ) {
		reactor_post_meta( array( 'just_tags' => true ) );
	}
}
add_action('reactor_post_footer', 'rvrb_post_body_content_tags', 1);

/**
 * Post footer edit 
 * in single.php
 * 
 * @since 1.0.0
 */
function reactor_do_post_edit() {
	if ( is_single() ) {
		edit_post_link( __('Edit this post', 'reactor'), '<div class="edit-link"><span>', '</span></div>');
	}
}
add_action('reactor_post_footer', 'reactor_do_post_edit', 4);

/**
 * Post footer meta
 * in all formats
 * 
 * @since 1.0.0
 */
function reactor_do_post_footer_meta() {
	if ( is_single() ) {
		reactor_post_meta( array('show_photo' => true,'show_tag' => true) );
	} else {
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
	}
}
add_action('reactor_post_footer', 'reactor_do_post_footer_meta', 2);

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
function rvrb_single_post_related() {
    if ( is_single() && function_exists( 'yarpp_related' ) ) { ?>
    <div class="related">
	    <?php yarpp_related( array( 'template' => 'yarpp-template-rvrb.php' ) ); ?>
    </div>
<?php }
}
add_action('reactor_post_after', 'rvrb_single_post_related', 4);

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
?>
