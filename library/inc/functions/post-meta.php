<?php 
/**
 * Reactor Post Meta
 *
 * @package Reactor
 * @author Anthony Wilhelm (@awshout / anthonywilhelm.com)
 * @since 1.0.0
 * @credit TewentyTwelve Theme
 * @usees $post
 * @param $args Optional. Override defaults.
 * @license GNU General Public License v2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 */

/**
 * Meta information for current post: categories, tags, author, and date
 */
if ( !function_exists('reactor_post_meta') ) {
	function reactor_post_meta( $args = '' ) {
		
		do_action('reactor_post_meta', $args);
		
		global $post; $meta = ''; $output = '';
		
		$defaults = array( 
			'show_author' => true,
			'show_date'   => true,
			'link_date'		=> true,
			'show_cat'    => true,
			'show_tag'    => true,
			'show_icons'  => false,
			'show_uncategorized' => false,
			'comments' => false,
			'catpage' => false,
			'show_photo' => false,
			'social_dropdown'	=> false,
		 );
        $args = wp_parse_args( $args, $defaults );
		
		if ( 'portfolio' == get_post_type() ) {
			$categories_list = get_the_term_list( $post->ID, 'portfolio-category', '', ', ', '' );
		} else {
			// $categories_list = get_the_category_list(', ');
			$count = 0;
			$categories_list = '';
			$categories = get_the_category();			
			foreach ( $categories as $category ) {
				$count++;
				if ( $args['show_uncategorized'] ) {
					$categories_list .= '<a href="' . get_category_link( $category->term_id ) . '" title="'.sprintf( __('View all posts in %s', 'reactor'), $category->name ) . '">' . $category->name . '</a>';
					if ( $count != count( $categories ) ){
						$categories_list .= ', ';
					}
				} else {
					if ( $category->slug != 'uncategorized' || $category->name != 'Uncategorized' ) {
						$categories_list .= '<a href="' . get_category_link( $category->term_id ) . '" title="'.sprintf( __('View all posts in %s', 'reactor'), $category->name ) . '">' . $category->name . '</a>';
						if ( $count != count( $categories ) ){
							$categories_list .= ', ';
						}
					}
				}
					
			}
		}
		
		if ( 'portfolio' == get_post_type() ) {
			$tag_list = get_the_term_list( $post->ID, 'portfolio-tag', '', ', ', '' );
		} else {
			$tag_list = get_the_tag_list( '', ', ', '' );
		}
	
		$raw_date = ($args['link_date']) ? '<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a>' : '<time class="entry-date" datetime="%3$s">%4$s</time>';
		$date = sprintf($raw_date,
			esc_url( get_month_link( get_the_time('Y'), get_the_time('m') ) ),
			esc_attr( sprintf( __('View all posts from %s %s', 'reactor'), get_the_time('M'), get_the_time('Y') ) ),
			esc_attr( get_the_date('c') ),
			esc_html( get_the_date() )
		 );
	
		$authorraw = ( !$args['show_photo'] ) ? '<a class="url fn n" href="%1$s" title="%2$s" rel="author"><span>%3$s</span></a>' : '<a class="url fn n" href="%1$s" title="%2$s" rel="author"><h4>%3$s</h4></a>';
		$author = sprintf($authorraw,
			esc_url( get_author_posts_url( get_the_author_meta('ID') ) ),
			esc_attr( sprintf( __('View all posts by %s', 'reactor'), get_the_author() ) ),
			get_the_author()
		 );

		$num_comments = get_comments_number(); // get_comments_number returns only a numeric value
		if ( $num_comments == 0 ) {
			$comments = __('No Comments');
		} elseif ( $num_comments > 1 ) {
			$comments = $num_comments . __(' Comments');
		} else {
			$comments = __('1 Comment');
		}
		$comments = '<span class="comments fi-comment right"><a href="' . get_comments_link() .'"><span>'. $comments.'</span></a></span>';

		$author_social = '';
		if( $args['show_photo'] ) {
			$author_social = sprintf('<ul class="author-social-small right inline-list">%1$s%2$s%3$s%4$s%5$s</ul>',
	            ( get_the_author_meta('twitter') != '' ? sprintf('<li><a href="http://twitter.com/%1$s" title="%1$s on Twitter">Twitter</a></li>', get_the_author_meta('twitter') ) : '' ),
	            ( get_the_author_meta('facebook') != '' ? sprintf('<li><a href="%1$s" title="%2$%s on Facebook">Facebook</a></li>', get_the_author_meta('facebook'), get_the_author() ) : '' ),
	            ( get_the_author_meta('instagram') != '' ? sprintf('<li><a href="http://instagram.com/%1$s" title="%1$s on Instagram">Instagram</a></li>', get_the_author_meta('instagram') ) : '' ),
	            ( get_the_author_meta('googleplus') != '' ? sprintf('<li><a href="%1$s" title="%2$%s on Google Plus" rel="me">Google+</a></li>', get_the_author_meta('googleplus'), get_the_author() ) : '' ),
	            ( get_the_author_meta('email_public') != '' ? sprintf('<li><a href="mailto:%1$s" title="Email %1$s">Email</a></li>', get_the_author_meta('email_public') ) : '' )
			);
		}

		$nickname = ( (get_the_author_meta('nickname') != 'hidden' ) ? sprintf(', %s', get_the_author_meta('nickname') ) : '' );

		$author_desc = '';
		if ( !is_null(get_the_author_meta('description') ) )  {
			$author_desc = '<p class="author-desc">' . smart_trim(get_the_author_meta('description'),30) . '</p>';
		}

		if ( 'post' == get_post_type() ) {
			$author_photo = sprintf('<div class="authorimage large-3 medium-3 small-3 columns"><div class="authorimageholder"></div><a class="url fn n" href="%1$s" title="%2$s" rel="author"><img src="%3$s" class="authormug" alt="%4$s" /></a></div>',
				esc_url( get_author_posts_url( get_the_author_meta('ID') ) ),
				esc_attr( sprintf( __('View all posts by %s', 'reactor'), get_the_author() ) ),
				the_author_image_url( get_the_author_meta('ID') ),
				get_the_author()
			 );
		}

		$social_dropdown = '';
		if ( $args['social_dropdown'] ) {
			$text = html_entity_decode(get_the_title());
			if ( (is_single() || is_page() ) && has_post_thumbnail($post->ID) ) {
				$image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large');
			} else {
				$image = get_stylesheet_directory_uri() . '/images/logo-large.png';
			}
			$desc = ( get_the_excerpt() != '' ? get_the_excerpt() : get_bloginfo('description') );
			$social_dropdown .= '<span class="fi-share social-dropdown-link right"><a href="javascript:void(0);" aria-controls="socialdrop" aria-expanded="false" data-dropdown="socialdrop"><span>Share</span></a>';
			$social_dropdown .= '<ul id="socialdrop" class="tiny content f-dropdown" data-dropdown-content>';
				//Twitter button
				$social_dropdown .= sprintf(
				    '<li class="dropdown-social pm-twitter"><a href="javascript:void(0)" onclick="javascript:window.open(\'http://twitter.com/share?text=%1$s&amp;url=%2$s&amp;via=%3$s\', \'twitwin\', \'left=20,top=20,width=500,height=500,toolbar=1,resizable=1\');"><span class="fi-social-twitter">Twitter</span></a></li>',
				    urlencode(html_entity_decode($text, ENT_COMPAT, 'UTF-8') . ':'),
				    rawurlencode( wp_get_shortlink() ),
				    'rvrb'
				);
				//Facebook share
				$social_dropdown .= sprintf(
				    '<li class="dropdown-social pm-facebook"><a href="javascript:void(0)" onclick="javascript:window.open(\'http://www.facebook.com/sharer/sharer.php?s=100&amp;p[url]=%1$s&amp;p[images][0]=%2$s&amp;p[title]=%3$s&amp;p[summary]=%4$s\', \'fbwin\', \'left=20,top=20,width=500,height=500,toolbar=1,resizable=1\');"><span class="fi-social-facebook">Facebook</span></a></li>',
				    rawurlencode( wp_get_shortlink() ),
				    rawurlencode( $image[0] ),
				    urlencode( html_entity_decode($text, ENT_COMPAT, 'UTF-8') ),
				    urlencode( html_entity_decode( $desc, ENT_COMPAT, 'UTF-8' ) )
				);
				//Google plus share
				$social_dropdown .= sprintf(
				    '<li class="dropdown-social pm-googleplus"><a href="javascript:void(0)" onclick="javascript:window.open(\'http://plus.google.com/share?url=%1$s\', \'gpluswin\', \'left=20,top=20,width=500,height=500,toolbar=1,resizable=1\');"><span class="fi-social-google-plus">Google+</span></a></li>',
				    rawurlencode( wp_get_shortlink() )
				);
				//Linkedin share
				$social_dropdown .= sprintf(
				    '<li class="dropdown-social pm-linkedin"><a href="javascript:void(0)" onclick="javascript:window.open(\'http://www.linkedin.com/shareArticle?mini=true&amp;url=%1$s&amp;title=%2$s&amp;source=%3$s\', \'linkedwin\', \'left=20,top=20,width=500,height=500,toolbar=1,resizable=1\');"><span class="fi-social-linkedin">LinkedIn</span></a></li>',
				    rawurlencode( wp_get_shortlink() ),
				    urlencode( html_entity_decode($text, ENT_COMPAT, 'UTF-8') ),
				    rawurlencode( home_url() )
				);
				//Pinterest Pin This
				$social_dropdown .= sprintf(
				    '<li class="dropdown-social pm-linkedin"><a href="javascript:void(0)" onclick="javascript:window.open(\'http://pinterest.com/pin/create/button/?url=%1$s&amp;media=%2$s&amp;description=%3$s\', \'pintwin\', \'left=20,top=20,width=500,height=500,toolbar=1,resizable=1\');"><span class="fi-social-pinterest">Pinterest</span></a></li>',
				    rawurlencode( wp_get_shortlink() ),
				    rawurlencode( $image[0] ),
				    urlencode( html_entity_decode($text, ENT_COMPAT, 'UTF-8') )
				);
				//Reddit submit
				$social_dropdown .= sprintf(
				    '<li class="dropdown-social pm-reddit"><a href="javascript:void(0)" onclick="javascript:window.open(\'http://www.reddit.com/submit?url=%1$s&amp;title=%2$s\', \'redditwin\', \'left=20,top=20,width=900,height=700,toolbar=1,resizable=1\');"><span class="fi-social-reddit">Reddit</span></a></li>',
				    rawurlencode( wp_get_shortlink() ),
				    urlencode( html_entity_decode($text, ENT_COMPAT, 'UTF-8') )
				);
				//Email this article
				$social_dropdown .= '<li class="dropdown-social pm-email">' . email_link('','',false) . '</li>';
				//Print
				$social_dropdown .= '<li class="dropdown-social pm-print"><a href="javascript:window.print()"><span class="fi-print">Print</span></a></li>';
			$social_dropdown .= '</ul></span>';
		}

		/**
		 * 1	category
		 * 2	tag
		 * 3	date
		 * 4 	author's name
		 * 5	comments
		 * 6 	author's mugshot
		 * 7	nickname (organization name)
		 * 8	author short description
		 * 9	author-social-small
		 * 10	social dropdown
		 */
		if ( $date || $categories_list || $author || $tag_list ) {
			if ( $args['catpage'] ) {
				$meta .= ( $author && $args['show_author'] ) ? '<span class="by-author">%4$s</span> ' : '';
				$meta .= ( $date && $args['show_date'] ) ? '%3$s ' : '';
				$meta .= ( $comments && $args['comments'] ) ? '%5$s ' : '';
				$meta .= ( $args['social_dropdown'] ) ? '%10$s' : '';
				$meta .= ( $categories_list && $args['show_cat'] ) ? __('in', 'reactor') . ' %1$s' : '';
				$meta .= ( $tag_list && $args['show_tag'] ) ? '<div class="entry-tags">' . __('Tags:', 'reactor') . ' %2$s</div>' : '';

				if ( $meta ) {
					$output = '<div class="entry-meta">' . $meta . '</div>';
				}
			} else if ( $args['show_icons'] ) {
				$meta .= ( $author && $args['show_author'] ) ? '<i class="social foundicon-torso" title="Written by"></i> <span class="by-author">%4$s</span>' : '';
				$meta .= ( $date && $args['show_date'] ) ? '<i class="general foundicon-calendar" title="Publish on"></i> %3$s' : '';
				$meta .= ( $categories_list && $args['show_cat'] ) ? '<i class="general foundicon-folder" title="Posted in"></i> %1$s' : '';
				$meta .= ( $tag_list && $args['show_tag'] ) ? '<div class="entry-tags"><i class="general foundicon-flag" title="Tagged with"></i> %2$s</div>' : '';
				
				if ( $meta ) {
					$output = '<div class="entry-meta icons">' . $meta . '</div>';
				}
			} else if ( $args['show_photo'] && get_the_author_meta('list_author_single') ) {
				$meta .= ( $author_photo && $args['show_photo'] ) ? '%6$s' : '';
				$meta .= '<div class="large-9 medium-9 small-9 columns">';
				$meta .= ( $author && $args['show_author'] ) ? '<div class="by-author">%4$s</div>' : '';
				$meta .= ( $author_desc ) ? '%8$s' : '';
				$meta .= ( $author_social ) ? '%9$s' : '';
				$meta .= '<div class="clear"></div></div>';
				
				if ( $meta ) {
					$output = '<div class="entry-meta-author panel radius row collapse">' . __('', 'reactor') . $meta . '</div>';
				}
			} else if (!$args['show_photo']) {
				$meta .= ( $date && $args['show_date'] ) ? '%3$s ' : '';
				$meta .= ( $author && $args['show_author'] ) ? __('by', 'reactor') . ' <span class="by-author">%4$s</span> ' : '';
				$meta .= ( $categories_list && $args['show_cat'] ) ? __('in', 'reactor') . ' %1$s' : '';
				$meta .= ( $tag_list && $args['show_tag'] ) ? '<div class="entry-tags">' . __('Tags:', 'reactor') . ' %2$s</div>' : '';

				if ( $meta ) {
					$output = '<div class="entry-meta">' . __('Posted: ', 'reactor') . $meta . '</div>';
				}
			}
	
			$post_meta = sprintf( $output, $categories_list, $tag_list, $date, $author, $comments, $author_photo, $nickname, $author_desc, $author_social, $social_dropdown );

			echo apply_filters('reactor_post_meta', $post_meta, $defaults);
		}
	}
}