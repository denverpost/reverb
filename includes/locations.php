<?php

/**** LOCATION POST TYPE ****/

// setup the location custom post type
add_action( 'init', 'tkno_locations_register_post_type' );

// register the location post type
function tkno_locations_register_post_type() {

    // setup the arguments for the location post type
    $locations_args = array(
        'public' => true,
        'query_var' => 'location',
        'rewrite' => array(
            'slug' => 'location',
            'with_front' => false
        ),
        'supports' => array(
            'title',
            'editor',
            'excerpt',
            'thumbnail'
        ),
        'labels' => array(
            'name' => 'Locations',
            'singular_name' => 'Location',
            'add_new' => 'Add New Location',
            'add_new_item' => 'Add New Location',
            'edit_item' => 'Edit Location',
            'new_item' => 'New Location',
            'view_item' => 'View Location',
            'search_items' => 'Search Locations',
            'not_found' => 'No Locations Found',
            'not_found_in_trash' => 'No Locations Found in Trash'
        ),
        'register_meta_box_cb' => 'add_location_metaboxes'
    );

    //register the post type
    register_post_type( 'location', $locations_args );

    // Add the location metaboxes
    function add_location_metaboxes() {
        add_meta_box('location-details', 'location details', 'location_details', 'location', 'normal', 'default');
    }
}
//Get rid of the content editor
add_action( 'init', 'tkno_locations_init' );
function tkno_locations_init() {
    remove_post_type_support( 'location', 'editor' );
}

/*** location METABOXES ***/
//Render the metabox
function location_details() {
    global $post;
    // Noncename needed to verify where the data originated
    echo '<input type="hidden" name="eventmeta_noncename" id="eventmeta_noncename" value="' .
    wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
    // Get the field data if it has already been entered
    $address = get_post_meta($post->ID, 'location-street-address', true);
    $city = get_post_meta($post->ID, 'location-city', true);
    $state = get_post_meta($post->ID, 'location-state', true);
    $ZIP = get_post_meta($post->ID, 'location-ZIP', true);

    $latitude = get_post_meta($post->ID, 'location-latitude', true);
    $longitude = get_post_meta($post->ID, 'location-longitude', true);

    // Echo out the fields

    echo '<p><label>Street address:</label> <input type="text" name="location-street-address" value="' . $address  . '" /></p>';
    echo '<p><label>City:</label> <input type="text" name="location-city" value="' . $city  . '" /></p>';
    echo '<p><label>State:</label> <input type="text" name="location-state" value="' . $state . '" /></p>';
    echo '<p><label>ZIP code:</label> <input type="text" name="location-ZIP" value="' . $ZIP  . '" /></p>';
    echo '<p><label>Latitude (calculated):</label> <input type="text" disabled="disabled" name="location-latitude" value="' . $latitude  .'" /></p>';
    echo '<p><label>Longitude (calculated):</label> <input type="text" disabled="disabled" name="location-longitude" value="' . $longitude  .'" /></p>';
}

// Save the Metabox Data
function tkno_save_location_meta($post_id, $post) {
    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if ( !wp_verify_nonce( $_POST['eventmeta_noncename'], plugin_basename(__FILE__) )) {
    return $post->ID;
    }
    // Is the user allowed to edit the post or page?
    if ( !current_user_can( 'edit_post', $post->ID ))
        return $post->ID;
    // OK, we're authenticated: we need to find and save the data
    // We'll put it into an array to make it easier to loop though.
    $location_meta['location-street-address'] = $_POST['location-street-address'];
    $location_meta['location-city'] = $_POST['location-city'];
    $location_meta['location-state'] = $_POST['location-state'];
    $location_meta['location-ZIP'] = $_POST['location-ZIP'];

    //Get Lat/Long from address
        $address = $_POST['location-street-address']." ".$_POST['location-city']." ".$_POST['location-state']. " ".$_POST['location-ZIP'];
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
    $columns['location-street-address'] = __( 'Address' );
    $columns['location-city'] = __( 'City' );
    $columns['location-state'] = __( 'State' );
    $columns['location-ZIP'] = __( 'ZIP code' );
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

    // Check that we are editing the right post type  
    if ( 'location' != $_POST['post_type'] )   
    {  
        return;  
    }  

    // Check if we have a lat/lng stored for this property already  
    $check_link = $wpdb->get_row("SELECT * FROM lat_lng_post WHERE post_id = '" . $post_id . "'");  
    if ($check_link != null)   
    {  
        // We already have a lat lng for this post. Update row  
        $wpdb->update(   
        'lat_lng_post',   
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
        'lat_lng_post',   
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
