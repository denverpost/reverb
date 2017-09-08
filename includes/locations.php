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
// Render the metabox
function location_post_meta_box( $post ) {
    // Noncename needed to verify where the data originated
    echo '<input type="hidden" name="location_meta_nonce" id="location_meta_nonce" value="' .
    wp_create_nonce( basename(__FILE__) ) . '" />';
    // Get the field data if it has already been entered
    $address = get_post_meta($post->ID, '_location_street_address', true);
    $latitude = get_post_meta($post->ID, '_location_latitude', true);
    $longitude = get_post_meta($post->ID, '_location_longitude', true);

    // Echo out the fields

    echo '<p><label>Street address:</label> <input type="text" size="100" name="_location_street_address" id="_location_street_address" value="' . $address  . '" /></p>';
    echo '<p><label>Latitude (calculated):</label> <input type="text" disabled="disabled" name="location-latitude" id="location-latitude" value="' . $latitude  .'" />';
    echo '<label style="margin-left:30px;">Longitude (calculated):</label> <input type="text" disabled="disabled" name="location-longitude" id="location-longitude" value="' . $longitude  .'" /></p>';
}

// Save the Metabox Data
function tkno_save_location_meta( $post_id, $post ) {
    /* Verify the nonce before proceeding. */
    if ( ! isset( $_POST['location_meta_nonce'] ) || ! wp_verify_nonce( $_POST['location_meta_nonce'], basename( __FILE__ ) ) ) {
        return $post_id;
    }

    // Is the user allowed to edit the post or page?
    if ( ! current_user_can( 'edit_post', $post->ID ) )
        return $post_id;

    // OK, we're authenticated: we need to find and save the data
    // We'll put it into an array to make it easier to loop though.
    $location_meta['_location_street_address'] = $_POST['_location_street_address'];

    //Get Lat/Long from address
    if ( $location_meta['_location_street_address'] != '' ) {
        $address = $_POST['_location_street_address'];
        $prepAddr = str_replace( ' ', '+', $address );
        $geocode = file_get_contents( 'http://maps.google.com/maps/api/geocode/json?address=' . $prepAddr . '&sensor=false' );
        $output = json_decode($geocode);
        $latitude = $output->results[0]->geometry->location->lat;
        $longitude = $output->results[0]->geometry->location->lng;

        $location_meta['_location_latitude'] = $latitude;
        $location_meta['_location_longitude'] = $longitude;
    } else {
        $location_meta['_location_latitude'] = '';
        $location_meta['_location_longitude'] = '';
    }

    // Add values of $location_meta as custom fields
    foreach ( $location_meta as $key => $value ) {
        $loc_shortcode_new_value = ( isset( $value ) ) ? $value : '';
        $loc_shortcode_meta_key = $key;
        $loc_shortcode_meta_value = get_post_meta( $post_id, $loc_shortcode_meta_key, true );
        if ( $loc_shortcode_new_value && '' == $loc_shortcode_meta_value )
            add_post_meta( $post_id, $loc_shortcode_meta_key, $loc_shortcode_new_value, true );
        elseif ( $loc_shortcode_new_value && $loc_shortcode_new_value != $loc_shortcode_meta_value )
            update_post_meta( $post_id, $loc_shortcode_meta_key, $loc_shortcode_new_value );
        elseif ( '' == $loc_shortcode_new_value && $loc_shortcode_meta_value )
            delete_post_meta( $post_id, $loc_shortcode_meta_key, $loc_shortcode_meta_value );
    }

    //Call function to save lat/long to custom table
    save_lat_lng($post->ID, $latitude, $longitude);
}
/*** END METABOXES ***/

/*** DISPLAY COLUMNS ON MANAGE POSTS PAGE ***/
add_filter( 'manage_edit-location_columns', 'set_custom_edit_location_columns' );
add_action( 'manage_location_posts_custom_column' , 'custom_location_column', 10, 2 );

//Define columns to show
function set_custom_edit_location_columns($columns) {
    unset($columns['date']);
    $columns['_location_street_address'] = __( 'Address' );
    $columns['_location_latitude'] = __( 'Latitude' );
    $columns['_location_longitude'] = __( 'Longitude' );
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
		$form .= '<form method="get" action="' . get_site_url() . '/location/">';
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

/*** CALCULATE DISTANCE USING LAT/LONG, GIVEN A ZIP CODE ***/
function location_posts_where( $where )  
{  
    global $wpdb;
    //Get user location from ZIP
    $geocode=file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.get_query_var('user_ZIP').'&sensor=false');
    $output= json_decode($geocode);
    $lat = $output->results[0]->geometry->location->lat;
    $lng = $output->results[0]->geometry->location->lng;

    $radius = get_query_var('user_radius'); // (in miles)  

    $table_name = $wpdb->prefix . 'locations';
    // Append our radius calculation to the WHERE  
    $where .= " AND $wpdb->posts.ID IN (SELECT post_id FROM " . $table_name . " WHERE
         ( 3959 * acos( cos( radians(" . $lat . ") )
                        * cos( radians( lat ) )
                        * cos( radians( lng )
                        - radians(" . $lng . ") )
                        + sin( radians(" . $lat . ") )
                        * sin( radians( lat ) ) ) ) <= " . $radius . ")";

    // Return the updated WHERE part of the query  
    return $where;  
}

/*** CALCULATE DISTANCE BETWEEN TWO POINTS OF LATITUDE/LONGITUDE ***/
function distance($lat1, $lon1, $lat2, $lon2) {
     $theta = $lon1 - $lon2;
     $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
     $dist = acos($dist);
     $dist = rad2deg($dist);
     $miles = $dist * 60 * 1.1515;
    return $miles;
}

/**
 * Here's where we build a metabox, with a form in it, that searches
 * for location post-type posts that are published. Can't limit
 * by time or the re-usability of the location post type is
 * compromised. The idea is to let a producer or writer create
 * locations, and then quickly build list articles from them.
 */

/* The search function that the metabox form will use */
function se_lookup() {
    global $wpdb;
    $search = like_escape($_REQUEST['q']);
    $query = 'SELECT ID,post_title FROM ' . $wpdb->posts . '
        WHERE post_title LIKE \'%' . $search . '%\'
        AND post_type = \'location\'
        AND post_status = \'publish\'
        ORDER BY post_title ASC';
    foreach ($wpdb->get_results($query) as $row) {
        $post_title = $row->post_title;
        $id = $row->ID;
        echo $post_title . ' (' . $id . ')' . "\n";
    }
    die();
}
add_action('wp_ajax_se_lookup', 'se_lookup');
add_action('wp_ajax_nopriv_se_lookup', 'se_lookup');

function se_wp_enqueue_scripts() {
    wp_enqueue_script('suggest');
}
add_action('wp_enqueue_scripts', 'se_wp_enqueue_scripts');

function location_shortcode_metabox_setup() {
    add_action( 'add_meta_boxes', 'location_shortcode_add_metabox' );
    add_action( 'save_post', 'location_shortcode_save_metabox', 10, 2 );
}
add_action( 'load-post.php', 'location_shortcode_metabox_setup' );
add_action( 'load-post-new.php', 'location_shortcode_metabox_setup' );

/* Create one or more meta boxes to be displayed on the post editor screen. */
function location_shortcode_add_metabox() {
    add_meta_box(
        'location_shortcode',
        esc_html__( 'Locations Shortcode', 'example' ),
        'location_shortcode_metabox',
        'post',
        'side',
        'default'
    );
}

/* Display the post meta box. */
function location_shortcode_metabox( $post ) {
    echo '<input type="hidden" name="location_shortcode_nonce" id="location_shortcode_nonce" value="' .
    wp_create_nonce( basename(__FILE__) ) . '" />';
    $loc_shortcode_ranked = get_post_meta( $post->ID, '_loc_shortcode_ranked', true );
    $loc_shortcode = get_post_meta( $post->ID, '_loc_shortcode', true );
    $loc_shortcode_ids = explode( ',', $loc_shortcode );
    $args = array(
        'post_type' => 'location',
        'post__in' => $loc_shortcode_ids
    );
    $loc_shortcode_query = new WP_Query( $args ); ?>
    <form id="location_shortcode_search">
        <p><label>Location name to add:</label> <input class="widefat" type="text" name="location_shortcode_search_text" id="location_shortcode_search_text" value="" /></p>
        <div>
            <ul id="selected_suggestions">
            <?php 
            /* Note how this is done for the future: You can't use ->the_post()
            in the admin unless you want to change the values in the $post object
            that's rendering the editor page, because the global $post object
            isn't actually set up in post.php. This is a 6-year-old-bug! */
            if ( $loc_shortcode_query->have_posts() ) : 
                foreach( $loc_shortcode_query->get_posts() as $loc_post ) {
                    $loc_post_id = $loc_post->ID; ?>
                    <li id="sug_<?php echo $loc_post_id; ?>"><button type="button" id="loc_remove-sug_<?php echo $loc_post_id; ?>" class="loc_delbutton" onclick="javascript:removeSuggestion(this);"><span class="remove-tag-icon"></span></button><?php echo $loc_post->post_title; ?> (<?php echo $loc_post_id; ?>)<input type="hidden" name="loc_shortcode[]" value="<?php echo $loc_post_id; ?>"/></li>
                <?php }
            endif; ?>
            </ul>
        </div>
        <p><label><input type="checkbox" name="loc_shortcode_ranked" id="loc_shortcode_ranked" value="true" <?php if ( $loc_shortcode_ranked == 'true' ) echo 'checked'; ?> /> Ranked and numbered?</label></p>
        <input type="button" class="button" onclick="javascript:insertLocationShortcode();" value="Insert shortcode" />
    </form>
    <script type="text/javascript">
        jQuery.fn.enterKey = function (fnc) {
            return this.each(function () {
                $(this).keypress(function (ev) {
                    var keycode = (ev.keyCode ? ev.keyCode : ev.which);
                    if (keycode == '13') {
                        fnc.call(this, ev);
                    }
                })
            })
        }
        function removeSuggestion(el) {
            var elTwo = el.parentNode;
            elTwo.parentNode.removeChild(elTwo);
        }
        function getLocationIDs() {
            var idList = new Array();
            jQuery('#selected_suggestions').children('li').each( function() {
                idList.push( jQuery(this).attr('id').replace('sug_','') );
            });
            return idList.join();
        }
        // What actually inserts the shortcode we'll use below
        function insertLocationShortcode() {
            var locationRankedSrc = document.getElementById('loc_shortcode_ranked').checked;
            var locationIdsSrc = getLocationIDs();
            var locationRanked = (locationRankedSrc) ? ' ranked="true"' : '';
            var locationIds = (locationIdsSrc !== '') ? ' ids="' + locationIdsSrc + '"' : false;
            if ( locationIds !== false ) {
                wp.media.editor.insert('[locations' + locationIds + locationRanked + ']');
            }
        }
        var se_ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';
        jQuery(document).ready(function() {
            jQuery('#location_shortcode_search_text').suggest(se_ajax_url + '?action=se_lookup',
                {
                    minchars: 2,
                    onSelect: function() {
                        var stripped_id = this.value.match(/\(([^)]+)\)/)[1];
                        var el_id = 'sug_' + stripped_id;
                        if ( !jQuery('#'+el_id).length) {
                            jQuery('#selected_suggestions').append('<li id="' + el_id + '"><button type="button" id="loc_remove-' + el_id + '" class="loc_delbutton" onclick="javascript:removeSuggestion(this);"><span class="remove-tag-icon"></span></button>' + this.value + '<input type="hidden" name="loc_shortcode[]" value="' + stripped_id + '"/></li>');
                        }
                        jQuery('#location_shortcode_search_text').val('');
                    }
                });
        });
    </script>
    <?php
}

// checks for only digits between the commas; utility for validation below
function only_ids_with_commas( $sent_value ) {
    $values = explode( ',', $sent_value );
    $valid = true;
    foreach( $values as $value ) {
        if( ! ctype_digit( $value ) ) {
            $valid = false;
            break;
        }
    }
    return $valid;
}

// Let's save data from the metabox for the future, duh
function location_shortcode_save_metabox( $post_id, $post ) {
    if ( !isset( $_POST['location_shortcode_nonce'] ) || !wp_verify_nonce( $_POST['location_shortcode_nonce'], basename( __FILE__ ) ) ) {
        return $post_id;
    }
    if ( !current_user_can( 'edit_post', $post->ID ) )
        return $post_id;

    $loc_shortcode = implode( ',', $_POST['loc_shortcode'] );

    $loc_shortcode_new_value = ( isset( $loc_shortcode ) && only_ids_with_commas( $loc_shortcode ) ) ? $loc_shortcode : '';
    $loc_shortcode_meta_key = '_loc_shortcode';
    $loc_shortcode_meta_value = get_post_meta( $post_id, $loc_shortcode_meta_key, true );
    if ( $loc_shortcode_new_value && '' == $loc_shortcode_meta_value )
        add_post_meta( $post_id, $loc_shortcode_meta_key, $loc_shortcode_new_value, true );
    elseif ( $loc_shortcode_new_value && $loc_shortcode_new_value != $loc_shortcode_meta_value )
        update_post_meta( $post_id, $loc_shortcode_meta_key, $loc_shortcode_new_value );
    elseif ( '' == $loc_shortcode_new_value && $loc_shortcode_meta_value )
        delete_post_meta( $post_id, $loc_shortcode_meta_key, $loc_shortcode_meta_value );

    $loc_shortcode_ranked = $_POST['loc_shortcode_ranked'];

    $loc_shortcode_ranked_new_value = ( isset( $loc_shortcode_ranked ) && $loc_shortcode_ranked == 'true' ) ? 'true' : 'false';
    $loc_shortcode_ranked_meta_key = '_loc_shortcode_ranked';
    $loc_shortcode_ranked_meta_value = get_post_meta( $post_id, $loc_shortcode_ranked_meta_key, true );
    if ( $loc_shortcode_ranked_new_value && '' == $loc_shortcode_ranked_meta_value )
        add_post_meta( $post_id, $loc_shortcode_ranked_meta_key, $loc_shortcode_ranked_new_value, true );
    elseif ( $loc_shortcode_ranked_new_value && $loc_shortcode_ranked_new_value != $loc_shortcode_ranked_meta_value )
        update_post_meta( $post_id, $loc_shortcode_ranked_meta_key, $loc_shortcode_ranked_new_value );
    elseif ( '' == $loc_shortcode_ranked_new_value && $loc_shortcode_ranked_meta_value )
        delete_post_meta( $post_id, $loc_shortcode_ranked_meta_key, $loc_shortcode_ranked_meta_value );
}

function locations_shortcode() {
    global $post;
    $loc_shortcode_ranked = get_post_meta( $post->ID, '_loc_shortcode_ranked', true );
    $loc_shortcode_ids = explode( ',', get_post_meta( $post->ID, '_loc_shortcode', true ) );
    $locations_display = '<div class="list_locations">';
$locations_display .= '<h1>' . implode(', ',$loc_shortcode_ids) . '</h1>';
    foreach( $loc_shortcode_ids as $loc_post_id ) {
        var_dump($loc_post_id);
        $loc_post = get_post( $loc_post_id );
        var_dump($loc_post);
        $locations_display .= '<h2 class="entry-title"><a href="' . $loc_post->ID . '" rel="bookmark">' . $loc_post->post_title . '</a></h2>';
    }
    $locations_display .= '</div>';
    return $locations_display;
}
add_shortcode( 'locations', 'locations_shortcode' );