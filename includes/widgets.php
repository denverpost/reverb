<?php

/**
 * Let's hide some of the default WP widgets that we'll never actually use.
 *
 * After: OMG that's so much better.
 */
function unregister_default_widgets() {
    unregister_widget('WP_Widget_Media_Audio');
    unregister_widget('WP_Widget_Media_Image');
    unregister_widget('WP_Widget_Media_Video');
    unregister_widget('WP_Widget_Pages');
    unregister_widget('WP_Widget_Calendar');
    unregister_widget('WP_Widget_Archives');
    unregister_widget('WP_Widget_Links');
    unregister_widget('WP_Widget_Meta');
    unregister_widget('WP_Widget_Search');
    unregister_widget('WP_Widget_Categories');
    unregister_widget('WP_Widget_Recent_Posts');
    unregister_widget('WP_Widget_Recent_Comments');
    unregister_widget('WP_Widget_RSS');
    unregister_widget('WP_Widget_Tag_Cloud');
    unregister_widget('WP_Nav_Menu_Widget');
    unregister_widget('Twenty_Eleven_Ephemera_Widget');
}
add_action('widgets_init', 'unregister_default_widgets', 11);

// Popular widget
class tkno_popular_widget extends WP_Widget
{
    public function __construct()
    {
            parent::__construct(
                'tkno_popular_widget',
                __('The Know Popular widget', 'tkno_popular_widget'),
                array('description' => __('Put a The Know popular posts widget in the sidebar', 'tkno_popular_widget'), )
            );
    }

    public function form( $instance ) {
        //Check if limit_days exists, if its null, put "new limit_days" for use in the form
        if ( isset( $instance[ 'limit_days' ] ) ) {
            $limit_days = $instance[ 'limit_days' ];
        }
        else {
            $limit_days = __( '0', 'wpb_widget_domain' );
        } ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'limit_days' ); ?>"><?php _e( 'Display popular posts from the last __ days (0 for no limit):' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'limit_days' ); ?>" name="<?php echo $this->get_field_name( 'limit_days' ); ?>" type="text" value="<?php echo esc_attr( $limit_days ); ?>" />
        </p>
    <?php }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance[ 'limit_days' ] = ( ! empty( $new_instance[ 'limit_days' ] ) ) ? (int)strip_tags( $new_instance[ 'limit_days' ] ) : 0;
        return $instance;
    }

    public function widget( $args, $instance ) {
        $limit_days = $instance[ 'limit_days' ];
        $limit_days = ( $limit_days != 0 ) ? (int)$limit_days : 10000;
        if ( function_exists( 'stats_get_csv' ) ) {
            echo '<div id="sidebar-popular" class="widget widget_pop">
                    <h4 class="widget-title">Popular</h4>';
            $top_posts = stats_get_csv( 'postviews', 'days=7&limit=200' );
            if ( count( $top_posts ) > 0 ) {
                echo '<ul>';
                $i=1;
                foreach ($top_posts as $p) {
                    $post = get_post( $p[ 'post_id' ] );
                    $post_date = strtotime( $post->post_date );
                    $today_date = time();
                    if ( $i <= 5 && ( $today_date - $post_date ) <= 60*60*24*(int)$limit_days && ( get_post_type( $p['post_id'] ) != 'page' ) ) { ?>
                        <li class="clearfix"><span class="pop_num"><?php echo $i; ?></span><a href="<?php echo $p['post_permalink']; ?>"><?php echo $p['post_title']; ?></a><div class="clear"></div></li>
                        <?php
                        $i++;
                    }
                }
                echo '</ul>
                    </div>';
            }
        } else {
            ?> <!-- Sorry, there are no Popular posts to display! --><?php
        }
        if ( term_exists( 'dont-miss', 'post_tag' ) ) {
            $dm_tag = get_term_by( 'slug', 'dont-miss', 'post_tag' );
            remove_all_filters('posts_orderby'); // disable Post Types Order ordering for this query
            $args = array(
                'post_type'         => 'post',
                'tag_id'            => $dm_tag->term_id,
                'posts_per_page'    => '5',
                'orderby'           => 'rand',
                'adp_disable'       => true,
                );
            $dm_query = new WP_Query( $args );
            $i=1;
            if ( $dm_query->have_posts() ) {
                echo '<div id="sidebar-dontmiss" class="widget widget_dontmiss">
                    <h4 class="widget-title">Don\'t Miss</h4>
                    <ul>';
                while ( $dm_query->have_posts() ) : $dm_query->the_post();
                    if ( $i <= 5 ) { ?>
                        <li class="clearfix"><span class="pop_num"><?php echo $i; ?></span><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><div class="clear"></div></li>
                    <?php $i++;
                    }
                endwhile;
                echo '</ul>
                    </div>';
            } else {
                ?> <!-- Sorry, there are no Don't Miss posts at this time! --><?php
            }
            wp_reset_query();
        }
    }
}
function register_popular_widget() { register_widget('tkno_popular_widget'); }
add_action( 'widgets_init', 'register_popular_widget' );


// Create a simple widget for one-click newsletter signup
class newsletter_signup_widget extends WP_Widget {
    public function __construct() {
            parent::__construct(
                'newsletter_signup_widget',
                __('Newsletter Signup', 'newsletter_signup_widget'),
                array('description' => __('Come on, sign up for a newsletter. All the cool kids are doing it.', 'newsletter_signup_widget'), )
            );
    }

    public function form( $instance ) {
        //Check if limit_days exists, if its null, put "new limit_days" for use in the form
        if ( isset( $instance[ 'newletter_text' ] ) ) {
            $newletter_text = $instance[ 'newletter_text' ];
        }
        else {
            $newletter_text = __( 'Sign up for our <em>Now You Know</em> emails to get breaking entertainment news and weekend plans sent right to your inbox.', 'wpb_widget_domain' );
        } ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'newletter_text' ); ?>"><?php _e( 'Descriptive text (displayed above email form):' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'newletter_text' ); ?>" name="<?php echo $this->get_field_name( 'newletter_text' ); ?>" type="text" value="<?php echo esc_attr( $newletter_text ); ?>" />
        </p>
    <?php }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance[ 'newletter_text' ] = ( ! empty( $new_instance[ 'newletter_text' ] ) ) ? trim( wp_kses( $new_instance[ 'newletter_text' ] ) ) : 'Sign up for our <em>Now You Know</em> emails to get breaking entertainment news and weekend plans sent right to your inbox.';
        return $instance;
    }

    public function widget($args, $instance) {
        $newletter_text = $instance[ 'newletter_text' ];
        global $wp;
        $current_url = home_url(add_query_arg(array(),$wp->request));
        // The signup form for the email
        echo '<div id="sidebar-newsletter" class="widget widget_newsletter">
                <h4 class="widget-title">Get Our Newsletter</h4>
                <p>' . $newletter_text . '</p>
                <form action="http://www.denverpostplus.com/app/mailer/" method="post" name="reverbmail">
                    <div class="row collapse mx-form">
                        <div class="large-9 small-9 columns">
                            <input type="hidden" name="keebler" value="goof111" />
                            <input type="hidden" name="goof111" value="TRUE" />
                            <input type="hidden" name="redirect" value="' . $current_url . '" />
                            <input type="hidden" name="id" value="autoadd" />
                            <input type="hidden" name="which" value="theknow" />
                            <input type="text" name="name_first" value="Humans: Do Not Use" style="display:none;" />
                            <input required placeholder="Email Address" type="text" name="email_address" maxlength="50" value="" />
                        </div>
                        <div class="large-3 small-3 columns end">
                            <input class="button prefix" type="submit" id="newslettersubmit" value="Sign up">
                        </div>
                    </div>
                </form>
            </div>';
    }
}
function register_newsletter_signup_widget() { register_widget('newsletter_signup_widget'); }
add_action( 'widgets_init', 'register_newsletter_signup_widget' );

// Create a simple widget for one-click newsletter signup
class newstip_submit_widget extends WP_Widget {
    public function __construct() {
            parent::__construct(
                'newstip_submit_widget',
                __('Newstip Submit', 'newstip_submit_widget'),
                array('description' => __('Todd was warned about news tip submissions.', 'newstip_submit_widget'), )
            );
    }

    public function widget($args, $instance) {
        global $wp;
        $current_url = home_url(add_query_arg(array(),$wp->request));
        // The submit form for the newstip
        echo '<div id="sidebar-newstip" class="widget widget_newstip">
                <h4 class="widget-title">Send Us A Tip</h4>
                <form action="http://www.denverpostplus.com/app/mailer/" method="post" name="tipmail">
                    <div class="row collapse mx-form">
                        <textarea name="comments" rows="4" cols="30"></textarea>
                        <input type="hidden" name="keebler" value="goof111" />
                        <input type="hidden" name="goof111" value="TRUE" />
                        <input type="hidden" name="redirect" value="' . $current_url . '" />
                        <input type="hidden" name="id" value="newstip" />
                        <input type="text" name="name_first" value="Humans: Do Not Use" style="display:none;" />
                        <p>Your email address, so we can reply:</p>
                        <div class="large-9 small-9 columns">
                            <input type="email" name="email_address" value="" maxlength="50" required />
                        </div>
                        <div class="large-3 small-3 columns end">
                            <input class="button prefix" type="submit" id="newstipsubmit" value="Send tip">
                        </div>
                        <div class="clear"></div>
                    </div>
                </form>
            </div>';
    }
}
function register_newstip_submit_widget() { register_widget('newstip_submit_widget'); }
add_action( 'widgets_init', 'register_newstip_submit_widget' );

class sidebar_tagline_widget extends WP_Widget {
    public function __construct() {
            parent::__construct(
                'sidebar_tagline_widget',
                __('DP Logo + Tagline', 'sidebar_tagline_widget'),
                array('description' => __('If they don\'t know we are affiliated with The Denver Post, will they even care?', 'sidebar_tagline_widget'), )
            );
    }

    public function form( $instance ) {
        $defaults = array( 'tagline_text' => __( 'What to do, where to be and what to see, from', 'wpb_widget_domain' ), 'is_front' => 'off', 'is_sidebar' => 'off' );
        $instance = wp_parse_args( ( array ) $instance, $defaults ); ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'tagline_text' ); ?>"><?php _e( 'Tagline text (will be followed by "The Denver Post" logo):' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'tagline_text' ); ?>" name="<?php echo $this->get_field_name( 'tagline_text' ); ?>" type="text" value="<?php echo esc_attr( $instance[ 'tagline_text' ] ); ?>" />
        </p>
        <p>
        <input class="checkbox" type="checkbox" <?php checked( $instance[ 'is_front' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'is_front' ); ?>" name="<?php echo $this->get_field_name( 'is_front' ); ?>" /> 
        <label for="<?php echo $this->get_field_id( 'is_front' ); ?>">Check here for the homepage tagline</label>
        </p>
        <p>
        <input class="checkbox" type="checkbox" <?php checked( $instance[ 'is_sidebar' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'is_sidebar' ); ?>" name="<?php echo $this->get_field_name( 'is_sidebar' ); ?>" /> 
        <label for="<?php echo $this->get_field_id( 'is_sidebar' ); ?>">Check here for the sidebar tagline</label>
        </p>

    <?php }

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance[ 'tagline_text' ] = ( ! empty( $new_instance[ 'tagline_text' ] ) ) ? trim( strip_tags( $new_instance[ 'tagline_text' ] ) ) : 'What to do, where to be and what to see, from';
        $instance[ 'is_front' ] = $new_instance[ 'is_front' ];
        $instance[ 'is_sidebar' ] = $new_instance[ 'is_sidebar' ];
        return $instance;
    }

    public function widget($args, $instance) {
        extract( $args );
        $tagline_text = $instance[ 'tagline_text' ];
        $is_front = $instance[ 'is_front' ] ? true : false;
        $is_sidebar = $instance[ 'is_sidebar' ] ? true : false;
        // Display a fixed tagline and The Denver Post logo
        if ( ( $is_front && is_front_page() ) || ( $is_sidebar && ! is_front_page() ) ) {
            echo '<div id="sidebar-tagline" class="widget widget_tagline">
                <p>' . $tagline_text . ' <a href="https://www.denverpost.com"><img src="'.get_bloginfo('stylesheet_directory').'/images/dp-logo-blk.png" /></a></p>
            </div>';
        }
    }
}
function register_sidebar_tagline_widget() { register_widget('sidebar_tagline_widget'); }
add_action( 'widgets_init', 'register_sidebar_tagline_widget' );

// Calendar widget
class tkno_calendar_widget extends WP_Widget
{
    public function __construct()
    {
            parent::__construct(
                'tkno_calendar_widget',
                __('The Know Calendar', 'tkno_calendar_widget'),
                array('description' => __('Put an adaptive (by parent category) The Know calendar widget in a sidebar', 'tkno_calendar_widget'), )
            );
    }

    public function widget( $args, $instance ) {

        function tkno_cal_category() {
            $category = FALSE;
            $calcat = '8354';
            if ( is_home() || is_front_page() ) {
                $calcat = '8354';
            } else if ( is_outdoors() ) {
                $calcat = '8811';
            } else if ( is_category() ) {
                $id = get_query_var( 'cat' );
                $cat = get_category( (int)$id );
                $category = $cat->slug;
            } else if ( is_single() ) {
                $category = tkno_get_top_category_slug(true);
            }
            if ( $category ) {
                switch ( $category ) {
                    case 'music':
                        $calcat = '8347';
                        break;
                    case 'arts':
                        $calcat = '8350';
                        break;
                    case 'things-to-do':
                        $calcat = '8351';
                        break;
                    case 'food':
                        $calcat = '8352';
                        break;
                    case 'drink':
                        $calcat = '8353';
                        break;
                    default:
                        $calcat = '8354';
                }
            }
            return $calcat;
        }
        if ( ! is_post_type_archive( 'neighborhoods' ) && ! ( is_single() && ( get_post_type() == 'venues' || get_post_type() == 'neighborhoods' ) ) ) {
            echo '<div id="sidebar-calendar" class="widget widget_cal">
                    <div data-cswidget="' . tkno_cal_category() . '"></div>
                </div>';
        } /* else if ( is_post_type_archive( 'neighborhoods' ) || ( is_single() && get_post_type() == 'neighborhoods' ) ) {
            echo '<iframe src="https://extras.denverpost.com/real-estate/search-placester.html" width="100%" height="250px" scrolling="no" style="border:none;" seamless></iframe>';
        } */
    }
}
function register_calendar_widget() { register_widget('tkno_calendar_widget'); }
add_action( 'widgets_init', 'register_calendar_widget' );

class follow_us_on_widget extends WP_Widget
{
    public function __construct()
    {
            parent::__construct(
                'follow_us_on_widget',
                __('Follow Us On [social media]', 'follow_us_on_widget'),
                array('description' => __('Command readers to follow us on various mission-critical social networks!', 'follow_us_on_widget'), )
            );
    }

    public function widget($args, $instance)
    {
        // List of icons linked to various social networks' Intent pages
        echo '<div id="sidebar-followus" class="widget widget_followus">
                <h4 class="widget-title">Follow Us</h4>
                <ul>';
        if ( is_outdoors() ) {
            echo '      <li class="followus"><a href="https://twitter.com/coloradoutdoors"><img src="' . get_stylesheet_directory_uri() . '/images/social-twitter.png" alt="Follow The Know Outdoors on Twitter" /></a></li>
                    <li class="followus"><a href="https://facebook.com/coloradoutdoors"><img src="' . get_stylesheet_directory_uri() . '/images/social-facebook.png" alt="Like The Know Outdoors on Facebook" /></a></li>
                    <li class="followus"><a href="https://instagram.com/thknwco"><img src="' . get_stylesheet_directory_uri() . '/images/social-instagram.png" alt="Follow The Know on Instagram" /></a></li>
                    <li class="followus"><a href="' . get_bloginfo( 'url' ) . '/outdoors/feed/"><img src="' . get_stylesheet_directory_uri() . '/images/social-rss.png" alt="Follow The Know Outdoors via RSS" /></a></li>';
        } else {
            echo '      <li class="followus"><a href="https://twitter.com/thknwco"><img src="' . get_stylesheet_directory_uri() . '/images/social-twitter.png" alt="Follow The Know on Twitter" /></a></li>
                    <li class="followus"><a href="https://facebook.com/denverentertain" title="Like The Know on Facebook"><img src="' . get_stylesheet_directory_uri() . '/images/social-facebook.png" alt="Like The Know on Facebook" /></a></li>
                    <li class="followus"><a href="https://instagram.com/thknwco" title="Follow The Know on Instagram"><img src="' . get_stylesheet_directory_uri() . '/images/social-instagram.png" alt="Follow The Know on Instagram" /></a></li>
                    <li class="followus"><a href="' . get_bloginfo( 'url' ) . '/feed/" title="Follow The Know via RSS"><img src="' . get_stylesheet_directory_uri() . '/images/social-rss.png" alt="Follow The Know via RSS" /></a></li>';
        }
        echo '      <div class="clear"></div>
                </ul>
            </div>';
    }
}
function register_follow_us_on_widget() { register_widget('follow_us_on_widget'); }
add_action( 'widgets_init', 'register_follow_us_on_widget' );

// Create a simple widget for one-click newsletter signup
class more_from_widget extends WP_Widget {
    public function __construct() {
            parent::__construct(
                'more_from_widget',
                __('More from The Know', 'more_from_widget'),
                array('description' => __('Vertical cross-promotion synergy realized.', 'more_from_widget'), )
            );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        echo $args['before_title'] . 'More from The Know' . $args['after_title'];
        ?> <div class="widget_morefrom"> <?php
        $dupe = false;
        wp_reset_query();
        if ( ! is_outdoors() && ! is_location() ) {
            // On non-Outdoors pages, pimp Outdoors
            $outdoor_parent = get_category_by_slug( 'outdoors' );
            $out_args = array(
                'post_type'         => 'post',
                'cat'               => $outdoor_parent->term_id,
                'posts_per_page'    => 1,
                'adp_disable'       => true
            );
            $out_query = new WP_Query( $out_args );
            ?>
            <?php while ( $out_query->have_posts() ) : $out_query->the_post();
                $dupe = get_the_ID();
                if ( has_post_thumbnail() ) {
                    $medium_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium' );
                }
                $bg_image_url = ( isset( $medium_image_url ) && strlen( $medium_image_url[0] ) >= 1 ) ? $medium_image_url[0] : false; ?>
                <div class="morefrom_item">
                    <div class="frontpage-image neighborhood_page-post"<?php echo ( $bg_image_url ) ? 'style="background-image:url(' . $bg_image_url . ');"' : ''; ?>>
                        <div class="front-imgholder"></div>
                        <a href="<?php the_permalink(); ?>" rel="bookmark"></a>
                        <header class="archive-header category-outdoors">
                            <h2 class="archive-title category-outdoors">
                                <span><a href="<?php echo get_bloginfo( 'url' ); ?>/outdoors/">Outdoors</a></span>
                            </h2>
                        </header>
                    </div>
                    <h2 class="entry-title neighborhood_page-title">
                        <a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
                    </h2>
                </div>
            <?php endwhile;
            wp_reset_query();
            ?>
            </ul>
        <?php }
        if ( ! is_post_type_archive( 'neighborhoods' ) && ! ( is_single() && get_post_type() == 'neighborhoods' ) ) {
            // On non-Neighborhoods pages, pimp Neighborhoods
            $nei_args = array(
                'post_type'         => 'neighborhoods',
                'orderby'           => 'rand', 
                'posts_per_page'    => 1,
                'adp_disable'       => true
            );
            $nei_query = new WP_Query( $nei_args );
            ?>
            <?php while ( $nei_query->have_posts() ) : $nei_query->the_post();
                if ( has_post_thumbnail() ) {
                    $medium_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium' );
                }
                $bg_image_url = ( isset( $medium_image_url ) && strlen( $medium_image_url[0] ) >= 1 ) ? $medium_image_url[0] : false; ?>
                <div class="morefrom_item">
                    <div class="frontpage-image neighborhood_page-post"<?php echo ( $bg_image_url ) ? 'style="background-image:url(' . $bg_image_url . ');"' : ''; ?>>
                        <div class="front-imgholder"></div>
                        <a href="<?php the_permalink(); ?>" rel="bookmark"></a>
                        <header class="archive-header">
                            <h2 class="archive-title neighborhood-header">
                                <span><a href="<?php echo get_bloginfo( 'url' ); ?>/neighborhoods/">Neighborhoods</a></span>
                            </h2>
                        </header>
                    </div>
                    <h2 class="entry-title neighborhood_page-title">
                        <a href="<?php the_permalink(); ?>" rel="bookmark">Get to know <?php the_title(); ?></a>
                    </h2>
                </div>
            <?php endwhile;
            wp_reset_query();
            ?>
        <?php }
        if ( is_outdoors() || is_location() || is_post_type_archive( 'neighborhoods' ) || ( is_single() && get_post_type() == 'neighborhoods' )) {
            // Anywhere else, pimp something else
            $all_args = array(
                'post_type'      => 'post',
                'posts_per_page' => 1,
                'orderby'        => 'date',
                'order'          => 'DESC',
                'post_not_in'    => array( $dupe ),
                'adp_disable'    => true
            );
            $all_query = new WP_Query( $all_args );
            ?>
            <?php while ( $all_query->have_posts() ) : $all_query->the_post();
                if ( has_post_thumbnail() ) {
                    $medium_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium' );
                }
                $bg_image_url = ( isset( $medium_image_url ) && strlen( $medium_image_url[0] ) >= 1 ) ? $medium_image_url[0] : false; ?>
                <div class="morefrom_item">
                    <div class="frontpage-image neighborhood_page-post"<?php echo ( $bg_image_url ) ? 'style="background-image:url(' . $bg_image_url . ');"' : ''; ?>>
                        <div class="front-imgholder"></div>
                        <a href="<?php the_permalink(); ?>" rel="bookmark"></a>
                        <header class="archive-header category-things-to-do">
                            <h2 class="archive-title category-things-to-do">
                                <span><a href="<?php echo get_bloginfo( 'url' ); ?>">Things To Do</a></span>
                            </h2>
                        </header>
                    </div>
                    <h2 class="entry-title neighborhood_page-title">
                        <a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
                    </h2>
                </div>
            <?php endwhile;
            wp_reset_query();
            ?>
            </ul>
        <?php }
        ?> </div> <?php
        echo $args['after_widget'];
    }
}
function register_more_from_widget() { register_widget('more_from_widget'); }
add_action( 'widgets_init', 'register_more_from_widget' );