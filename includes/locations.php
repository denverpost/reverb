<?php

function tkno_locations_install() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'locations';
    
    $charset_collate = $wpdb->get_charset_collate();

	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
	    $sql = "CREATE TABLE $table_name (
	        id mediumint(9) NOT NULL AUTO_INCREMENT,
	        post_id mediumint(9) NOT NULL,
	        lat tinytext NOT NULL,
	        lng tinytext NOT NULL,
	        PRIMARY KEY  (id)
	    ) $charset_collate;";

	    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	    dbDelta( $sql );
	}
}
add_action( 'init', 'tkno_locations_install' );

/**** LOCATION POST TYPE ****/

// register the location post type
function tkno_locations_register_post_type() {
	$labels = array(
        'name'               => _x( 'Locations', 'post type general name' ),
        'singular_name'      => _x( 'Location', 'post type singular name' ),
        'add_new'            => _x( 'Add New', 'venue' ),
        'add_new_item'       => __( 'Add New Location' ),
        'edit_item'          => __( 'Edit Location' ),
        'new_item'           => __( 'New Location' ),
        'all_items'          => __( 'All Locations' ),
        'view_item'          => __( 'View Location' ),
        'search_items'       => __( 'Search Locations' ),
        'not_found'          => __( 'No locations found' ),
        'not_found_in_trash' => __( 'No locations found in the Trash' ), 
        'parent_item_colon'  => '',
        'menu_name'          => 'Locations'
    );
    $args = array(
        'labels'        => $labels,
        'description'   => 'Locations feature a single destination or geographic activity, and can pull in related items based on proximity',
        'public'        => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'show_ui'       => true,
        'menu_position' => 6,
        'capability_type' => 'post',
        'query_var'     => true,
        'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes', 'revisions', 'author', 'custom-fields', ),
        'rewrite' => array(
            'slug' => 'location',
            'with_front' => false
            ),
        'has_archive'   => true
    );
    register_post_type( 'location', $args );
}
add_action( 'init', 'tkno_locations_register_post_type' );

/* Fire our meta box setup function on the post editor screen. */
add_action( 'load-post.php', 'location_post_meta_boxes_setup' );
add_action( 'load-post-new.php', 'location_post_meta_boxes_setup' );

/* Meta box setup function. */
function location_post_meta_boxes_setup() {
    /* Add meta boxes on the 'add_meta_boxes' hook. */
    add_action( 'add_meta_boxes', 'location_add_post_meta_boxes' );
    /* Save post meta on the 'save_post' hook. */
    add_action( 'save_post', 'tkno_save_location_meta', 10, 2 );
}

/* Create one or more meta boxes to be displayed on the post editor screen. */
function location_add_post_meta_boxes() {
    add_meta_box(
        'location_details',      // Unique ID
        esc_html__( 'Location Details', 'example' ),    // Title
        'location_post_meta_box',   // Callback function
        array( 'venues', 'location', 'post' ),       // Admin page (or post type)
        'normal',         // Context
        'default'         // Priority
    );
}

/*** location METABOXES ***/
//Render the metabox
function location_post_meta_box() {
    global $post;
    // Noncename needed to verify where the data originated
    echo '<input type="hidden" name="location_meta_nonce" id="location_meta_nonce" value="' .
    wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
    // Get the field data if it has already been entered
    $address = get_post_meta($post->ID, '_location_street_address', true);

    $latitude = get_post_meta($post->ID, 'location-latitude', true);
    $longitude = get_post_meta($post->ID, 'location-longitude', true);

    // Echo out the fields

    echo '<p><label>Street address:</label> <input type="text" size="100" name="_location_street_address" value="' . $address  . '" /></p>';
    echo '<p><label>Latitude (calculated):</label> <input type="text" disabled="disabled" name="location-latitude" value="' . $latitude  .'" />';
    echo '<label style="margin-left:30px;">Longitude (calculated):</label> <input type="text" disabled="disabled" name="location-longitude" value="' . $longitude  .'" /></p>';
}

// Save the Metabox Data
function tkno_save_location_meta($post_id, $post) {
    /* Verify the nonce before proceeding. */
    if ( !isset( $_POST['location_meta_nonce'] ) || !wp_verify_nonce( $_POST['location_meta_nonce'], basename( __FILE__ ) ) )
        return $post_id;
    // Is the user allowed to edit the post or page?
    if ( !current_user_can( 'edit_post', $post->ID ))
        return $post->ID;
    // OK, we're authenticated: we need to find and save the data
    // We'll put it into an array to make it easier to loop though.
    $location_meta['_location_street_address'] = $_POST['_location_street_address'];

    //Get Lat/Long from address
        $address = $_POST['_location_street_address'];
        $prepAddr = str_replace(' ','+',$address);
        $geocode=file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false');
        $output= json_decode($geocode);
        $latitude = $output->results[0]->geometry->location->lat;
        $longitude = $output->results[0]->geometry->location->lng;

    $location_meta['location-latitude'] = $latitude;
    $location_meta['location-longitude'] = $longitude;

    // Add values of $location_meta as custom fields
    foreach ($location_meta as $key => $value) { // Cycle through the $location_meta array!
        if( $post->post_type == 'revision' ) return; // Don't store custom data twice
        $value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
        if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
            update_post_meta($post->ID, $key, $value);
        } else { // If the custom field doesn't have a value
            add_post_meta($post->ID, $key, $value);
        }
        if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
    }

    //Call function to save lat/long to custom table
    save_lat_lng($post->ID, $latitude, $longitude);

}
add_action('save_post', 'tkno_save_location_meta', 1, 2); // save the custom fields
/*** END METABOXES ***/

/*** DISPLAY COLUMNS ON MANAGE POSTS PAGE ***/
add_filter( 'manage_edit-location_columns', 'set_custom_edit_location_columns' );
add_action( 'manage_location_posts_custom_column' , 'custom_location_column', 10, 2 );

//Define columns to show
function set_custom_edit_location_columns($columns) {
    unset($columns['date']);
    $columns['_location_street_address'] = __( 'Address' );
    $columns['location-latitude'] = __( 'Latitude' );
    $columns['location-longitude'] = __( 'Longitude' );
    return $columns;
}
//Show the columns
function custom_location_column( $column, $post_id ) {
    echo get_post_meta( $post_id , $column , true );
}
/*** END COLUMN DISPLAY ***/

/*** SAVE LAT/LONG TO CUSTOM TABLE ON SAVE ***/
function save_lat_lng( $post_id, $latitude, $longitude )   
{  
    global $wpdb;  
    $table_name = $wpdb->prefix . 'locations';

    // Check that we are editing the right post type  
    if ( 'location' != $_POST['post_type'] )   
    {  
        return;  
    }  

    // Check if we have a lat/lng stored for this property already  
    $check_link = $wpdb->get_row("SELECT * FROM " . $table_name . " WHERE post_id = '" . $post_id . "'");  
    if ($check_link != null)   
    {  
        // We already have a lat lng for this post. Update row  
        $wpdb->update(   
        $table_name,   
        array(   
            'lat' => $latitude,  
            'lng' => $longitude  
        ),   
        array( 'post_id' => $post_id ),   
        array(   
            '%f',  
            '%f'  
        )  
        );  
    }  
    else  
    {  
        // We do not already have a lat lng for this post. Insert row  
        $wpdb->insert(   
        $table_name,   
        array(   
            'post_id' => $post_id,  
            'lat' => $latitude,  
            'lng' => $longitude  
        ),   
        array(   
            '%d',   
            '%f',  
            '%f'  
        )   
        );  
    }  
}
/*** END SAVE TO CUSTOM TABLE ***/

/*** ADD location SEARCH VARIABLES TO QUERY VARS ***/
function add_query_vars_filter( $vars ){
  $vars[] = "user_ZIP";
  $vars[] = "user_radius";
  return $vars;
}
add_filter( 'query_vars', 'add_query_vars_filter' );
/*** END QUERY VARS UPDATE ***/

/**** END location POST TYPE ****/

/**** LOCATION SEARCH WIDGET ****/

// Creating the widget
class tkno_location_search_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
		// Base ID of your widget
		'tkno_location_search_widget',

		// Widget name will appear in UI
		__('Find location', ''),

		// Widget description
		array( 'description' => __( 'Displays location search form.' ), )
		);
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		// This is where you run the code and display the output
		    echo do_shortcode('[location_search]');
		}

	// Widget Backend
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'New title', '' );
		}
	// Widget admin form
	?>
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	</p>
	<?php
	}

	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
} // Class srd_find_location_widget ends here

// Register and load the widget
function tkno_location_search_widget_load() {
    register_widget( 'tkno_location_search_widget' );
}
add_action( 'widgets_init', 'tkno_location_search_widget_load' );

/**** END LOCATION SEARCH WIDGET ****/

function location_search_form_shortcode() {
    $form = '<div class="location-search">';
		$form .= '<form method="get" action="' . get_stylesheet_directory_uri() . '/search-location.php">';
			$form .= '<input type="hidden" name="locationsearch" value="Y" />';
			$form .= '<div>';
				$form .= '<h3>Find places to go</h3>';
			    $form .= '<div>';
				    $form .= '<label>Start with a ZIP code</label>';
				    $form .= '<input type="text" pattern=".{5}" required name="user_ZIP" id="user_ZIP" value="' . get_query_var( 'user_ZIP' ) . '" />';
			    $form .= '</div>';
			    $form .= '<div>';
				    $form .= '<label>Distance</label>';
				    $form .= '<select name="user_radius" id="user_radius">';
				        $form .= '<option' . ( get_query_var( 'user_radius' ) == 25000 ? ' selected="selected"' : '' ) . ' value="25000">Any</option>';
				        $form .= '<option' . ( get_query_var( 'user_radius' ) == 5 ? ' selected="selected"' : '' ) . ' value="5">5 miles</option>';
				        $form .= '<option' . ( get_query_var( 'user_radius' ) == 10 || ! get_query_var( 'user_radius' ) ? ' selected="selected"' : '' ) .' value="10">10 miles</option>';
				        $form .= '<option' . ( get_query_var( 'user_radius' ) == 20 ? ' selected="selected"' : '' ) . ' value="20">20 miles</option>';
				        $form .= '<option' . ( get_query_var( 'user_radius' ) == 50 ? ' selected="selected"' : '' ) . ' value="50">50 miles</option>';
				        $form .= '<option' . ( get_query_var( 'user_radius' ) == 100 ? ' selected="selected"' : '' ) . ' value="100">100 miles</option>';
				    $form .= '</select>';
			    $form .= '</div>';
			$form .= '</div>';
			$form .= '<div>';
			    $form .= '<input class="button" type="submit" value="Find locations" />';
			$form .= '</div>';
		$form .= '</form>';
	$form .= '</div>';
    return $form;
}
add_shortcode( 'location_search', 'location_search_form_shortcode' );