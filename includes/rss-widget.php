<?php 

/**
 * Widget API: TKNO_Widget_RSS class
 *
 * Based on the RSS widget built into Wordpress **
 * Takes an RSS feed but is able to associate the widget only with certain
 * neighborhoods, allowing neighborhood-specific news from denverpost.com
 * to be displayed in the lower-page sidebars on neighborhood pages.
 *
 * @package Reverb
 * @subpackage Widgets
 * @since 4.4.0
 */

function tkno_widget_rss_output( $rss, $args = array() ) {
	if ( is_string( $rss ) ) {
		$rss = fetch_feed($rss);
	} elseif ( is_array($rss) && isset($rss['url']) ) {
		$args = $rss;
		$rss = fetch_feed($rss['url']);
	} elseif ( !is_object($rss) ) {
		return;
	}

	if ( is_wp_error($rss) ) {
		if ( is_admin() || current_user_can('manage_options') )
			echo '<p><strong>' . __( 'RSS Error:' ) . '</strong> ' . $rss->get_error_message() . '</p>';
		return;
	}

	$default_args = array( 'items' => 0 );
	$args = wp_parse_args( $args, $default_args );

	$items = (int) $args['items'];
	if ( $items < 1 || 5 < $items )
		$items = 3;

	if ( !$rss->get_item_quantity() ) {
		echo '<ul><li>' . __( 'An error has occurred, which probably means the feed is down. Try again later.' ) . '</li></ul>';
		$rss->__destruct();
		unset($rss);
		return;
	}

	echo '<ul>';
	foreach ( $rss->get_items( 0, $items ) as $item ) {

		$link = $item->get_link();
		while ( stristr( $link, 'http' ) != $link ) {
			$link = substr( $link, 1 );
		}
		$link = esc_url( strip_tags( $link ) );

		$title = esc_html( trim( strip_tags( $item->get_title() ) ) );
		if ( empty( $title ) ) {
			$title = __( 'Untitled' );
		}

		if ( $link == '' ) {
			echo "<li>$title</li>";
		} else {
			echo "<li><a class='rsswidget' href='$link'>$title</a></li>";
		}
	}
	echo '</ul>';
	$rss->__destruct();
	unset($rss);
}

function tkno_widget_rss_process( $widget_rss, $check_feed = true ) {
	$items = (int) $widget_rss['items'];
	if ( $items < 1 || 5 < $items )
		$items = 3;
	$url           = esc_url_raw( strip_tags( $widget_rss['url'] ) );
	$title         = isset( $widget_rss['title'] ) ? trim( strip_tags( $widget_rss['title'] ) ) : '';
	$error = false;
	$link = '';

	if ( $check_feed ) {
		$rss = fetch_feed($url);
		if ( is_wp_error($rss) ) {
			$error = $rss->get_error_message();
		} else {
			$link = esc_url(strip_tags($rss->get_permalink()));
			while ( stristr($link, 'http') != $link )
				$link = substr($link, 1);

			$rss->__destruct();
			unset($rss);
		}
	}

	return compact( 'title', 'url', 'link', 'items', 'error' );
}

function tkno_widget_rss_form( $args, $inputs = null ) {
	$default_inputs = array( 'url' => true, 'title' => true, 'items' => true );
	$inputs = wp_parse_args( $inputs, $default_inputs );

	$args['title'] = isset( $args['title'] ) ? $args['title'] : '';
	$args['url'] = isset( $args['url'] ) ? $args['url'] : '';
	$args['items'] = isset( $args['items'] ) ? (int) $args['items'] : 0;

	if ( $args['items'] < 1 || 5 < $args['items'] ) {
		$args['items'] = 3;
	}

	if ( ! empty( $args['error'] ) ) {
		echo '<p class="widget-error"><strong>' . __( 'RSS Error:' ) . '</strong> ' . $args['error'] . '</p>';
	}

	$esc_number = esc_attr( $args['number'] );
	if ( $inputs['url'] ) :
?>
	<p><label for="rss_dp-url-<?php echo $esc_number; ?>"><?php _e( 'Enter the RSS feed URL here:' ); ?></label>
	<input class="widefat" id="rss_dp-url-<?php echo $esc_number; ?>" name="widget-rss_dp[<?php echo $esc_number; ?>][url]" type="text" value="<?php echo esc_url( $args['url'] ); ?>" /></p>
<?php endif; if ( $inputs['title'] ) : ?>
	<p><label for="rss_dp-title-<?php echo $esc_number; ?>"><?php _e( 'Give the feed a title (optional):' ); ?></label>
	<input class="widefat" id="rss_dp-title-<?php echo $esc_number; ?>" name="widget-rss_dp[<?php echo $esc_number; ?>][title]" type="text" value="<?php echo esc_attr( $args['title'] ); ?>" /></p>
<?php endif; if ( $inputs['items'] ) : ?>
	<p><label for="rss_dp-items-<?php echo $esc_number; ?>"><?php _e( 'How many items would you like to display?' ); ?></label>
	<select id="rss_dp-items-<?php echo $esc_number; ?>" name="widget-rss_dp[<?php echo $esc_number; ?>][items]">
	<?php
	for ( $i = 1; $i <= 5; ++$i ) {
		echo "<option value='$i' " . selected( $args['items'], $i, false ) . ">$i</option>";
	}
	?>
	</select></p>
<?php endif; 
	
}

class TKNO_Widget_RSS extends WP_Widget {

	public function __construct() {
		$widget_ops = array(
			'description' => __( 'Use to display entries from a DP RSS feed.' ),
			'customize_selective_refresh' => true
		);
		$control_ops = array( 'width' => 400, 'height' => 200 );
		parent::__construct( 'rss_dp', __( 'RSS - Denver Post' ), $widget_ops, $control_ops );
	}

	public function widget( $args, $instance ) {

		if ( isset($instance['error']) && $instance['error'] )
			return;
		$url = ! empty( $instance['url'] ) ? $instance['url'] : '';
		while ( stristr($url, 'http') != $url )
			$url = substr($url, 1);

		if ( empty($url) )
			return;

		// self-url destruction sequence
		if ( in_array( untrailingslashit( $url ), array( site_url(), home_url() ) ) )
			return;

		$rss = fetch_feed( $url );
		$title = $instance['title'];
		$link = '';

		if ( ! is_wp_error( $rss ) ) {
			if ( empty( $title ) )
				$title = strip_tags( $rss->get_title() );
			$link = strip_tags( stristr( str_replace( 'feed/', '', $url ), 'http' ) );
			if ( empty( $link ) )
				$link = strip_tags( $rss->get_permalink() );
			while ( stristr( $link, 'http' ) != $link )
				$link = substr($link, 1);
		}

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$url = strip_tags( $url );
		if ( $title )
			$title = '<a class="rsswidget" href="' . esc_url( $link ) . '">'. esc_html( $title ) . '</a>';

		echo $args['before_widget'];
		?> <div class="rss_dp_widget_inner"> <?php
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		tkno_widget_rss_output( $rss, $instance );
		?> </div> <?php
		echo $args['after_widget'];

		if ( ! is_wp_error($rss) )
			$rss->__destruct();
		unset($rss);
	}

	public function update( $new_instance, $old_instance ) {
		$testurl = ( isset( $new_instance['url'] ) && ( !isset( $old_instance['url'] ) || ( $new_instance['url'] != $old_instance['url'] ) ) );
		return tkno_widget_rss_process( $new_instance, $testurl );
	}

	public function form( $instance ) {
		if ( empty( $instance ) ) {
			$instance = array( 'title' => '', 'url' => '', 'items' => 3, 'error' => false );
		}
		$instance['number'] = $this->number;

		tkno_widget_rss_form( $instance );
	}
}

// Register and load the widget
function TKNO_Widget_RSS_register() {
    register_widget( 'TKNO_Widget_RSS' );
}
add_action( 'widgets_init', 'TKNO_Widget_RSS_register' );
