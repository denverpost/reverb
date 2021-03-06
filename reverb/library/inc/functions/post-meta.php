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
			'catpage' => false,
			'show_photo' => false,
			'social_dropdown'	=> false,
			'just_tags'	=> false,
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
	
		$raw_date = ($args['link_date']) ? '<a href="%1$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a>' : '<time class="entry-date" datetime="%3$s">%4$s</time>';
		$date = sprintf($raw_date,
			esc_url( get_month_link( get_the_time('Y'), get_the_time('m') ) ),
			esc_attr( sprintf( __('View all posts from %s %s', 'reactor'), get_the_time('M'), get_the_time('Y') ) ),
			esc_attr( get_the_date('c') ),
			esc_html( get_the_date() )
		 );
		
		if (!function_exists('authorTitleCase')) {
			function authorTitleCase($string) {
				$word_splitters = array(' ', '-', "O'", "L'", "D'", 'St.', 'Mc');
				$lowercase_exceptions = array('van', 'den', 'von', 'und', 'der', 'de', 'da', 'of', 'and', "l'", "d'");
				$uppercase_exceptions = array('III', 'IV', 'VI', 'VII', 'VIII', 'IX');
			 
				$string = strtolower($string);
				foreach ($word_splitters as $delimiter)
				{ 
					$words = explode($delimiter, $string); 
					$newwords = array(); 
					foreach ($words as $word)
					{ 
						if (in_array(strtoupper($word), $uppercase_exceptions)) {
							$word = strtoupper($word);
						} else if (!in_array($word, $lowercase_exceptions)) {
							$word = ucfirst($word); 
						}
						$newwords[] = $word;
					}
					if (in_array(strtolower($delimiter), $lowercase_exceptions)) {
						$delimiter = strtolower($delimiter);
					}
					$string = join($delimiter, $newwords); 
				} 
				return $string; 
			}
		}

		if (!function_exists('get_author_matches')) {
			function get_author_matches($feedauthor) {
				$feedauthor = preg_replace('/\s\s\s+/', ',', $feedauthor);
				$feedauthor = str_replace('<br>', ',', $feedauthor);
				$feedauthor = str_replace(' camera staff writer', ',camera staff writer', $feedauthor);
				$feedauthor = str_replace(' longmont times-call', ',longmont times-call', $feedauthor);
				$feedauthor = str_replace(' reporter-herald staff writer', ',reporter-herald staff writer', $feedauthor);
				$feedauthor = strip_tags( $feedauthor );
				$feedauthor = str_replace(',,,', ',', $feedauthor);
				$feedauthor = str_replace(', ,', ',', $feedauthor);
				$feedauthor = str_replace(',,', ',', $feedauthor);
				$authpart = explode(',', $feedauthor);
				foreach ($authpart as $value) {
				    $authparts[] = authorTitleCase($value);
				}
				$feedauthname = str_replace('By ', '', $authparts[0]);
				$feedauthname = str_replace('Yourhub', 'YourHub', $feedauthname);
				$feedauthname = str_replace('7news', '7News', $feedauthname);
				$feedauthname = str_replace('Propublica', 'ProPublica', $feedauthname);
				$feedauthnametwo = '';
				if ( isset( $authparts[1] ) ) {
					$authparts[1] = str_replace('Yourhub', 'YourHub', $authparts[1]);
					$authparts[1] = str_replace('7news', '7News', $authparts[1]);
					$authparts[1] = str_replace('Propublica', 'ProPublica', $authparts[1]);
					$feedauthnametwo = $authparts[1];
				}
				$feedauthname = str_replace(' And ', ' and ', $feedauthname);

				return array($feedauthname,$feedauthnametwo);
			}
		}
		
		if (!function_exists('assemble_author_matches')) {
			function assemble_author_matches($authormatches,$is_second) {
				$before_name = $is_second ? ' and' : 'By';
				$authmatches = array('Associated Press','The Associated Press','The Denver Post','Longmont Times-Call','Boulder Daily Camera','Steamboat Pilot and Today','The Denver Post Editorial Board','The Gazette','7News','The Cannabist','The Cannabist Staff');
				if ( in_array( $authormatches[0], $authmatches, true ) ) {
					$feedauthorout = '<em>By ' . $authormatches[0] . '</em>';
				} else if ($authormatches[0] == 'Denver Post Staff' || $authormatches[0] == 'Daily Camera' || $authormatches[0] == 'Cannabist Staff') {
					$feedauthorout = '<em>' . $authormatches[0] . '</em>';
				} else {
					$feedauthorg = ($authormatches[1] == 'Associated Press' || $authormatches[1] == 'Denver Post' || $authormatches[1] == 'Daily Camera' ) ? 'The ' . $authormatches[1] : $authormatches[1];
					$feedauthorg = ($feedauthorg != '') ? ', <em>' . $feedauthorg . '</em>' : '';
					$feedauthorout = $before_name . ' <strong>' . $authormatches[0] . '</strong>' . $feedauthorg;
				}
				return $feedauthorout;
			}
		}

		$author = $feedauthor = $description_author = '';
		$do_bio = false;

		if ( get_post_meta( get_the_ID(), 'original_author_name', true ) != '' ) {
				
			$feedauthorin = strtolower( html_entity_decode( get_post_meta( get_the_ID(), 'original_author_name', true ) ) );
			$authnamematches = get_author_matches( $feedauthorin );
			$feedauthorone = assemble_author_matches( $authnamematches, '' );
			$feedauthortwo = '';
			
			if (get_post_meta(get_the_ID(), 'second_author_name', true) != '') {
				$feedauthorintwo = strtolower( html_entity_decode( get_post_meta( get_the_ID(), 'second_author_name', true ) ) );
				$authnamematchestwo = get_author_matches( $feedauthorintwo );
				$feedauthortwo = assemble_author_matches( $authnamematchestwo, true );
				$checkauthone = explode( ',', $feedauthorone );
				$checkauthtwo = explode( ',', $feedauthortwo );
				if ( isset( $checkauthone[1] ) && isset( $checkauthtwo[1] ) ) {
					if ( $checkauthone[1] == $checkauthtwo[1] ) { 
						$feedauthorone = $checkauthone[0];
					} else {
						$feedauthorone .= ', ';
					}
				}
			}
			$feedauthor = $feedauthorone . $feedauthortwo;
		} else if ( function_exists( 'get_coauthors' ) && count( get_coauthors( get_the_id() ) ) > 1 ) {
			$coauthors = get_coauthors();
			$author = '<span class="author">By ';
			$i=count($coauthors);
			$ii=0;
			foreach( $coauthors as $coauthor ) {
				$ii++;
				$author .= ( $ii == $i ) ? ' and ' : ( ( $ii > 1 ) ? ', ' : '');
				if ( isset( $coauthor->type ) && $coauthor->type == 'guest-author' ) {
					$author .= sprintf( '<strong>%1$s</strong>',
						$coauthor->display_name
					 );
				} else {
					$coauth_pub = ( get_the_author_meta( 'publication', $coauthor->ID ) && get_the_author_meta( 'publication', $coauthors[ $ii + 1 ]->ID ) != get_the_author_meta( 'publication', $coauthor->ID ) ) ? ', ' . get_the_author_meta( 'publication' ) : '';
					$author .= sprintf( '<a class="url fn n" href="%1$s" rel="author">%2$s</a>%3$s',
						esc_url( get_author_posts_url( $coauthor->ID ) ),
						$coauthor->display_name,
						$coauth_pub
					);
				}
			}
			$author .= '</span>';
		} else if ( get_the_author_meta('publication') != 'hidden'  ) {
			$authorraw = ( !$args['show_photo'] ) ? '<span class="author">By <a class="url fn n" href="%1$s" rel="author">%2$s</a>%3$s</span>' : '<h4 class="author"><a class="url fn n" href="%1$s" rel="author">%2$s</a></h4>';
			$author = sprintf($authorraw,
				esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
				get_the_author_meta( 'display_name' ),
				( get_the_author_meta('publication') != '' ) ? ', ' . get_the_author_meta( 'publication' ) : ''
			 );
			$do_bio = true;
			$description_author = $author;
		}

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

		$author_desc = '';
		if ( !is_null( get_the_author_meta( 'description' ) ) )  {
			$author_desc = '<p class="author-desc">' . smart_trim( get_the_author_meta( 'description' ), 30 ) . '</p>';
		}

		$author_photo = '';
		if ( 'post' == get_post_type() && the_author_image_url( get_the_author_meta('ID') ) ) {
			$author_photo = sprintf('<div class="authorimage large-3 medium-3 small-3 columns"><div class="authorimageholder"></div><a class="url fn n" href="%1$s" rel="author"><img src="%2$s" class="authormug" alt="%3$s" /></a></div>',
				esc_url( get_author_posts_url( get_the_author_meta('ID') ) ),
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
		 * 5 	author's mugshot
		 * 6	author short description
		 * 7	author-social-small
		 * 8	social dropdown
		 */
		if ( $date || $categories_list || $author || $tag_list ) {
			if ( $args['catpage'] ) {
				$meta .= ( $author && ! $feedauthor ) ? '<span class="by-author">%4$s</span> ' : '';
				$meta .= ( ! $author && $feedauthor ? '<span class="by-author">' . $feedauthor . '</span> ' : '');
				$meta .= ( $date && $args['show_date'] ) ? '%3$s ' : '';
				$meta .= ( $args['social_dropdown'] ) ? '%8$s' : '';
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
			} else if ( $args['just_tags'] ) {
				$meta .= ( $tag_list ) ? '<div class="entry-tags">Post tags: %2$s</div>' : '';
				
				if ( $meta ) {
					$output = '<div class="entry-meta icons">' . $meta . '</div>';
				}
			} else if ( $do_bio && $args['show_photo'] && get_the_author_meta('list_author_single') ) {
				$meta .= ( $author_photo ) ? '%5$s' : '';
				$meta .= '<div class="large-9 medium-9 small-9 columns">';
				$meta .= ( $author && $args['show_author'] ) ? '<div class="by-author">%4$s</div>' : '';
				$meta .= ( $author_desc ) ? '%6$s' : '';
				$meta .= ( $author_social ) ? '%7$s' : '';
				$meta .= '<div class="clear"></div></div>';
				
				if ( $meta ) {
					$output = '<div class="entry-meta-author row collapse">' . __('', 'reactor') . $meta . '</div>';
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
	
			$post_meta = sprintf( $output, $categories_list, $tag_list, $date, $author, $author_photo, $author_desc, $author_social, $social_dropdown );

			echo apply_filters('reactor_post_meta', $post_meta, $defaults);
		}
	}
}