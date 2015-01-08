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
	if ( is_single() ) {
		$categories_list = '';
		$categories = get_the_category();
		foreach($categories as $category) {
			if ( strtolower($category->slug) != 'uncategorized' && $category->category_parent == 0) {
				$categories_list = $category;
			}
		} ?>
        <header class="archive-header">
            <h2 <?php post_class('archive-title'); ?>><a href="<?php echo get_category_link(intval($categories_list->term_id) ); ?>"><?php echo $categories_list->cat_name; ?></a></h2>
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
	$show_titles = reactor_option('frontpage_show_titles', 1);
	$link_titles = reactor_option('frontpage_link_titles', 0);

	$categories_list = '';
	$categories = get_the_category();
	end($categories);
	foreach($categories as $category) {
		if ( strtolower($category->slug) != 'uncategorized' && $category->category_parent == 0) {
			$categories_list = $category->name;
		}
	}
	
	if ( is_page_template('page-templates/front-page.php') && $show_titles ) {
		if ( has_post_thumbnail() ) {
			$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large');
		}
		if (strlen($large_image_url[0]) >= 1) { ?>
			<div class="frontpage-image frontpage-post" style="background-image:url('<?php echo $large_image_url[0]; ?>');">
		<?php } else { ?>
			<div class="frontpage-post">
		<?php } ?>
			<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __('%s', 'reactor'), the_title_attribute('echo=0') ) ); ?>" rel="bookmark">
				<h2 class="entry-title"><span><?php echo $categories_list; ?></span><?php the_title(); ?></h2>
			</a>
		</div>
<?php }
}
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
			<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __('%s', 'reactor'), the_title_attribute('echo=0') ) ); ?>" rel="bookmark">
				<h2 class="entry-title"><?php the_title(); ?></h2>
			</a>
			<p class="catexcerpt"><?php echo smart_trim(get_the_content(),25); ?></p>
			<?php reactor_post_meta(array('show_cat'=>false,'show_tag'=>false,'comments'=>true,'catpage'=>true,'link_date'=>false)); ?>
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
		<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __('%s', 'reactor'), the_title_attribute('echo=0') ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
	<?php }
	} elseif ( is_author() ) { ?>
		<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __('%s', 'reactor'), the_title_attribute('echo=0') ) ); ?>" rel="bookmark"><h2 class="entry-title"><?php the_title(); ?></h2></a>
	<?php
	} elseif ( !get_post_format() && !is_page_template('page-templates/front-page.php') ) {  ?>    
		<?php if ( is_single() ) { ?>
		<h1 class="entry-title"><?php the_title(); ?></h1>
		<?php } else { ?>
		<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __('%s', 'reactor'), the_title_attribute('echo=0') ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
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
		reactor_post_meta(array('show_cat'=>false,'show_tag'=>false,'comments'=>true,'catpage'=>true,'link_date'=>false));
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
			<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><div class="mainimg" style="background-image:url('<?php echo $large_image_url[0]; ?>');"></div></a>
        <?php } else { ?>
			<?php if (!is_single()) { ?><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php } ?>
				<span class="postimg" style="background-image:url('<?php echo $large_image_url[0]; ?>');"></span>
			<?php if (!is_single()) { ?></a><?php } ?>
        <?php } ?>
		</div>
	<?php }
}
add_action('reactor_post_header', 'reactor_do_standard_thumbnail', 4);
            
/**
 * Post footer title 
 * in format-audio, format-gallery, format-image, format-video
 * 
 * @since 1.0.0
 */
function reactor_do_post_footer_title() {
$format = ( get_post_format() ) ? get_post_format() : 'standard'; 

    switch ( $format ) { 
		case 'audio' : 
		case 'gallery' :
		case 'image' :
		case 'video' : ?>
        
            <h2 class="entry-title">
                <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __('%s', 'reactor'), the_title_attribute('echo=0') ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
            </h2>
               
		<?php break; 
	}
}
add_action('reactor_post_footer', 'reactor_do_post_footer_title', 1);

/**
 * Post footer meta
 * in all formats
 * 
 * @since 1.0.0
 */
function reactor_do_post_footer_meta() {

	if ( is_single() ) {
		reactor_post_meta( array('show_photo' => true) );
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
 * Post footer comments link 
 * in all formats
 * 
 * @since 1.0.0
 */
function reactor_do_post_footer_comments_link() {
	
	if ( is_page_template('page-templates/front-page.php') ) {
		$comments_link = reactor_option('frontpage_comment_link', 1);
	}
	elseif ( is_page_template('page-templates/news-page.php') ) {
		$comments_link = reactor_option('newspage_comment_link', 1);
	} else {
		$comments_link = reactor_option('comment_link', 1);
	}
	
	if ( comments_open() && $comments_link ) { ?>
		<div class="comments-link">
			<i class="icon social foundicon-chat" title="Comments"></i>
			<?php comments_popup_link('<span class="leave-comment">' . __('Leave a Comment', 'reactor') . '</span>', __('1 Comment', 'reactor'), __('% Comments', 'reactor') ); ?>
		</div><!-- .comments-link -->
    <?php }
}
//add_action('reactor_post_footer', 'reactor_do_post_footer_comments_link', 3);

/**
 * Post footer edit 
 * in single.php
 * 
 * @since 1.0.0
 */
function reactor_do_post_edit() {
	if ( is_single() ) {
		edit_post_link( __('Edit', 'reactor'), '<div class="edit-link"><span>', '</span></div>');
	}
}
add_action('reactor_post_footer', 'reactor_do_post_edit', 4);

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
            <?php previous_post_link('%link', '<span class="meta-nav">' . _x('&larr;', 'Previous post link', 'reactor') . '</span> %title', false, $exclude); ?>
            </span>
            <span class="nav-next alignright">
            <?php next_post_link('%link', '%title <span class="meta-nav">' . _x('&rarr;', 'Next post link', 'reactor') . '</span>', false, $exclude); ?>
            </span>
        </nav><!-- .nav-single -->
<?php }
}
add_action('reactor_post_after', 'reactor_do_nav_single', 1);

/**
 * Comments 
 * in single.php
 * 
 * @since 1.0.0
 */
function reactor_do_post_comments() {      
	// If comments are open or we have at least one comment, load up the comment template
	if ( is_single() && ( comments_open() || '0' != get_comments_number() ) ) {
		comments_template('', true);
	}
}
add_action('reactor_post_after', 'reactor_do_post_comments', 2);

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
