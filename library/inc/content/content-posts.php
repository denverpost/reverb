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

	$categories_list = '';
	$categories = get_the_category();
	end($categories);
	foreach($categories as $category) {
		if ( strtolower($category->slug) != 'uncategorized' && $category->category_parent == 0) {
			$categories_list = $category->name;
		}
	}

	if ( has_post_thumbnail() ) {
		$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large');
	}
	if (isset($large_image_url) && strlen($large_image_url[0]) >= 1) { ?>
		<div class="frontpage-image frontpage-post" style="background-image:url('<?php echo $large_image_url[0]; ?>');">
			<div class="front-thumbnail">
				<div class="front-imgholder"></div>
				<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
					<div class="front-img" style="background-image:url('<?php echo $large_image_url[0]; ?>');"></div>
				</a>
			</div>
	<?php } else { ?>
		<div class="frontpage-post">
	<?php } ?>
		<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __('%s', 'reactor'), the_title_attribute('echo=0') ) ); ?>" rel="bookmark">
			<h2 class="entry-title"><span><?php echo $categories_list; ?></span><?php the_title(); ?></h2>
		</a>
	</div>
<?php }
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
			<?php 
			$showcomments = ( is_search() ) ? false : true;
			reactor_post_meta(array('show_cat'=>false,'show_tag'=>false,'comments'=>$showcomments,'catpage'=>true,'link_date'=>false)); ?>
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
		reactor_post_meta(array('show_cat'=>false,'show_tag'=>false,'comments'=>true,'catpage'=>true,'link_date'=>false,'social_dropdown'=>true));
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
add_action('reactor_post_footer', 'reactor_do_post_edit', 1);


/**
 * Post body social links
 * in format-standard
 * 
 * @since 1.0.0
 */
function reactor_do_post_body_social() {
	global $wp;
	global $post;

	$text = html_entity_decode(get_the_title());
	if ( (is_single() || is_page() ) && has_post_thumbnail($post->ID) ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large');
	} else {
		$image = get_stylesheet_directory_uri() . '/images/logo-large.png';
	}
	$desc = ( get_the_excerpt() != '' ? get_the_excerpt() : get_bloginfo('description') );
	$social_string = '<div class="post-body-social"><ul class="inline-list">';
	//Twitter button
	$social_string .= sprintf(
	    '<li class="post-meta-social pm-twitter"><a href="javascript:void(0)" onclick="javascript:window.open(\'http://twitter.com/share?text=%1$s&amp;url=%2$s&amp;via=%3$s\', \'twitwin\', \'left=20,top=20,width=500,height=500,toolbar=1,resizable=1\');"><span class="fi-social-twitter">Twitter</span></a></li>',
	    urlencode(html_entity_decode($text, ENT_COMPAT, 'UTF-8') . ':'),
	    rawurlencode( wp_get_shortlink() ),
	    'rvrb'
	);
	//Facebook share
	$social_string .= sprintf(
	    '<li class="post-meta-social pm-facebook"><a href="javascript:void(0)" onclick="javascript:window.open(\'http://www.facebook.com/sharer/sharer.php?s=100&amp;p[url]=%1$s&amp;p[images][0]=%2$s&amp;p[title]=%3$s&amp;p[summary]=%4$s\', \'fbwin\', \'left=20,top=20,width=500,height=500,toolbar=1,resizable=1\');"><span class="fi-social-facebook">Facebook</span></a></li>',
	    rawurlencode( wp_get_shortlink() ),
	    rawurlencode( $image[0] ),
	    urlencode( html_entity_decode($text, ENT_COMPAT, 'UTF-8') ),
	    urlencode( html_entity_decode( $desc, ENT_COMPAT, 'UTF-8' ) )
	);
	//Google plus share
	$social_string .= sprintf(
	    '<li class="post-meta-social pm-googleplus"><a href="javascript:void(0)" onclick="javascript:window.open(\'http://plus.google.com/share?url=%1$s\', \'gpluswin\', \'left=20,top=20,width=500,height=500,toolbar=1,resizable=1\');"><span class="fi-social-google-plus">Google+</span></a></li>',
	    rawurlencode( wp_get_shortlink() )
	);
	//Linkedin share
	$social_string .= sprintf(
	    '<li class="post-meta-social pm-linkedin"><a href="javascript:void(0)" onclick="javascript:window.open(\'http://www.linkedin.com/shareArticle?mini=true&amp;url=%1$s&amp;title=%2$s&amp;source=%3$s\', \'linkedwin\', \'left=20,top=20,width=500,height=500,toolbar=1,resizable=1\');"><span class="fi-social-linkedin">LinkedIn</span></a></li>',
	    rawurlencode( wp_get_shortlink() ),
	    urlencode( html_entity_decode($text, ENT_COMPAT, 'UTF-8') ),
	    rawurlencode( home_url() )
	);
	//Pinterest Pin This
	$social_string .= sprintf(
	    '<li class="post-meta-social pm-pinterest"><a href="javascript:void(0)" onclick="javascript:window.open(\'http://pinterest.com/pin/create/button/?url=%1$s&amp;media=%2$s&amp;description=%3$s\', \'pintwin\', \'left=20,top=20,width=500,height=500,toolbar=1,resizable=1\');"><span class="fi-social-pinterest">Pinterest</span></a></li>',
	    rawurlencode( wp_get_shortlink() ),
	    rawurlencode( $image[0] ),
	    urlencode( html_entity_decode($text, ENT_COMPAT, 'UTF-8') )
	);
	//Reddit submit
	$social_string .= sprintf(
	    '<li class="post-meta-social pm-reddit"><a href="javascript:void(0)" onclick="javascript:window.open(\'http://www.reddit.com/submit?url=%1$s&amp;title=%2$s\', \'redditwin\', \'left=20,top=20,width=900,height=700,toolbar=1,resizable=1\');"><span class="fi-social-reddit">Reddit</span></a></li>',
	    rawurlencode( wp_get_shortlink() ),
	    urlencode( html_entity_decode($text, ENT_COMPAT, 'UTF-8') )
	);
	$social_string .= '<div class="clear"></div></ul></div>';
	echo $social_string;
}
add_action('reactor_post_footer', 'reactor_do_post_body_social', 2);

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
add_action('reactor_post_footer', 'reactor_do_post_footer_meta', 3);

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
            <!-- <span class="nav-previous alignleft">
            <?php //previous_post_link('%link', '<span class="meta-nav">' . _x('&larr;', 'Previous post link', 'reactor') . '</span> %title', false, $exclude); ?>
            </span> -->
            <span class="nav-next">
            <?php next_post_link('%link', 'Up next: %title', false, $exclude); ?>
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
	if ( is_single() && !is_category() && !is_tag() ) {
		//comments_template('', true);
		//$commentnum = ( '0' != get_comments_number() ? 'Read the comments (' . get_comments_number() . ')' : 'Show article comments');
		$commentnum = '<a class="fi-comment radius" href="' . get_permalink() . '#disqus_thread" title="Display article comments">Add a comment</a>'
		?>
		<div id="comments" class="comments-area">
        	<div id="disqus_thread"></div>
        	<div class="showdisqus"><?php echo $commentnum; ?></div>
		</div>
		<div id="relatedwrap" class="noprint">
			<h4 class="related-title widget-title noprint">Related Content</h4>
			<div id="related-content" class="noprint"></div>
			<div class="relatednext"></div>
		</div>
		<?php
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
