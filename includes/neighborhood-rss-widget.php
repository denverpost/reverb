<?php 

/**
 * Widget API: TKNO_Widget_Location class
 *
 * Takes an RSS feed and outputs it in in a sidebar. Based on default 
 * RSS widget code, but able to be tied to a neighborhood page
 * so many feeds can be selected and displayed only on certain pages.
 *
 * @package Reverb
 * @subpackage Widgets
 * @since 4.4.0
 */

function tkno_widget_location_output( $rss, $args = array() ) {
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

function tkno_widget_location_form( $args, $inputs = null ) {
	$default_inputs = array( 'items' => true );
	$inputs = wp_parse_args( $inputs, $default_inputs );

	$args['items'] = isset( $args['items'] ) ? (int) $args['items'] : 0;

	if ( $args['items'] < 1 || 5 < $args['items'] ) {
		$args['items'] = 3;
	}

	if ( ! empty( $args['error'] ) ) {
		echo '<p class="widget-error"><strong>' . __( 'RSS Error:' ) . '</strong> ' . $args['error'] . '</p>';
	}

	$esc_number = esc_attr( $args['number'] );
	if ( $inputs['items'] ) : ?>
	<p><label for="rss_dp_loc-items-<?php echo $esc_number; ?>"><?php _e( 'How many items would you like to display?' ); ?></label>
	<select id="rss_dp_loc-items-<?php echo $esc_number; ?>" name="widget-rss_dp_loc[<?php echo $esc_number; ?>][items]">
	<?php
	for ( $i = 1; $i <= 5; ++$i ) {
		echo "<option value='$i' " . selected( $args['items'], $i, false ) . ">$i</option>";
	}
	?>
	</select></p>
<?php endif; 
	
}

class TKNO_Widget_Location extends WP_Widget {

	public function __construct() {
		$widget_ops = array(
			'description' => __( 'Displays headlines from a DP Location feed, set in the Neighborhood Page.' ),
			'customize_selective_refresh' => true
		);
		$control_ops = array( 'width' => 400, 'height' => 200 );
		parent::__construct( 'rss_dp_loc', __( 'DP News by Location' ), $widget_ops, $control_ops );
	}

	public function widget( $args, $instance ) {

		if ( is_single() && get_post_type() == 'neighborhoods' ) {
            // It's a listing search and display widget
            global $post;
            $neighborhood_feed = get_post_meta( $post->ID, '_neighborhood_feed', true );
            $neighborhood_slug = get_post_meta( $post->ID, '_neighborhood_slug', true );
            $neighborhood = get_term_by( 'slug', $neighborhood_slug, 'neighborhood' );
            $neighborhood_name = $neighborhood->name;
        }

        if ( isset( $instance[ 'error' ]) && $instance[ 'error' ] )
			return;
		$url = ! empty( $neighborhood_feed ) ? $neighborhood_feed : '';
		while ( stristr( $url, 'http' ) != $url )
			$url = substr( $url, 1 );

		if ( empty( $url ) )
			return;

		// self-url destruction sequence
		if ( in_array( untrailingslashit( $url ), array( site_url(), home_url() ) ) )
			return;

		$rss = fetch_feed( $url );
		$title = $neighborhood_name . ' News';
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

		$url = strip_tags( $url );
		if ( $title )
			$title = '<a class="rsswidget" href="' . esc_url( $link ) . '">'. esc_html( $title ) . '</a>';

		echo $args['before_widget'];
		?> <div class="rss_dp_loc_widget_inner"> <?php
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		tkno_widget_location_output( $rss, $instance );
		?> </div> <?php
		echo $args['after_widget'];

		if ( ! is_wp_error($rss) )
			$rss->__destruct();
		unset($rss);
	}

	public function update( $new_instance, $old_instance ) {
		$items = (int) $widget_rss['items'];
		if ( $items < 1 || 5 < $items )
			$items = 3;
		
		return compact( 'items', 'error' );
	}

	public function form( $instance ) {
		if ( empty( $instance ) ) {
			$instance = array( 'items' => 3, 'error' => false );
		}
		$instance['number'] = $this->number;

		tkno_widget_location_form( $instance );
	}
}

// Register and load the widget
function TKNO_Widget_Location_register() {
    register_widget( 'TKNO_Widget_Location' );
}
add_action( 'widgets_init', 'TKNO_Widget_Location_register' );
