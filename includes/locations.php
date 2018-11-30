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
        /*'rewrite' => array(
            'slug' => 'location',
            'with_front' => true
            ),*/
        'has_archive'   => true,
        'taxonomies'    => array( 'category' ),
    );
    register_post_type( 'location', $args );
}
add_action( 'init', 'tkno_locations_register_post_type' );

/*** location METABOXES ***/
/* Meta box setup function. */
function location_post_meta_boxes_setup() {
    add_action( 'add_meta_boxes', 'location_add_post_meta_boxes' );
    add_action( 'save_post', 'tkno_save_location_meta', 10, 2 );
}
add_action( 'load-post.php', 'location_post_meta_boxes_setup' );
add_action( 'load-post-new.php', 'location_post_meta_boxes_setup' );

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

    echo '<p><label>Street address:</label> <input type="text" size="60" name="_location_street_address" id="_location_street_address" value="' . $address  . '" /></p>';
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

    // If the input string is already lat/long, let's just save it as is
    if ( preg_match('/^(\-?\d+(\.\d+)?),\s*(\-?\d+(\.\d+)?)$/', $location_meta['_location_street_address'] ) ) {
        $matches = explode(',', $location_meta['_location_street_address']);
        $location_meta['_location_latitude'] = $latitude = trim( (float)$matches[0] );
        $location_meta['_location_longitude'] = $longitude = trim ( (float)$matches[1] );
    } else if ( $location_meta['_location_street_address'] != '' ) {
        // Get Lat/Long from address
        $address = $_POST['_location_street_address'];
        $prepAddr = str_replace( ' ', '+', $address );
        $geocode = file_get_contents( 'https://maps.google.com/maps/api/geocode/json?key=AIzaSyDFesAMjYEKk6hCIxnQ_3SIwJ6rImbSch8&address=' . $prepAddr . '&sensor=false' );
        $output = json_decode($geocode);
        if ( $output->status == 'OK' ) {
            $latitude = $output->results[0]->geometry->location->lat;
            $longitude = $output->results[0]->geometry->location->lng;
            $location_meta['_location_latitude'] = $latitude;
            $location_meta['_location_longitude'] = $longitude;
        }
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
		// This is where you run the code and display the output
	    echo do_shortcode('[location_search title="' . $title . '"]');
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

function location_search_form_shortcode( $atts = [], $content = null, $tag = '' ) {
    $atts = array_change_key_case( (array)$atts, CASE_LOWER );
    // override default attributes with user attributes
    $loc_atts = shortcode_atts([
        'title' => 'Find places to go'
     ], $atts, $tag);
    $user_text = ( null !== get_query_var( 'user_text' ) ) ? get_query_var( 'user_text' ) : '';
    $user_ZIP = ( null !== get_query_var( 'user_ZIP' ) ) ? get_query_var( 'user_ZIP' ) : '';
    $user_radius = ( null !== get_query_var( 'user_radius' ) ) ? get_query_var( 'user_radius' ) : '';
    $form = '<form method="get" action="' . get_site_url() . '/location/">';
        $form .= '<input type="hidden" name="locationsearch" value="Y" />';
        $form .= '<div class="row">';
            $form .= '<div class="large-12 columns">';
                $form .= '<h2>' . $loc_atts['title'] . '</h2>';
            $form .= '</div>';
            $form .= '<div class="large-4 columns">';
                $form .= '<label>What are you looking for?</label>';
                $form .= '<input type="text" name="user_text" id="user_text" value="' . $user_text . '" />';
            $form .= '</div>';
            $form .= '<div class="large-2 columns">';
                $form .= '<label>Near ZIP</label>';
                $form .= '<input type="text" pattern=".{5}" name="user_ZIP" id="user_ZIP" value="' . $user_ZIP . '" />';
            $form .= '</div>';
            $form .= '<div class="large-2 columns">';
                $form .= '<label>Distance</label>';
                $form .= '<select name="user_radius" id="user_radius">';
                    $form .= '<option' . ( $user_radius == 25000 ? ' selected="selected"' : '' ) . ' value="25000">Any</option>';
                    $form .= '<option' . ( $user_radius == 5 ? ' selected="selected"' : '' ) . ' value="5">5 miles</option>';
                    $form .= '<option' . ( ! $user_radius || $user_radius == 10 ? ' selected="selected"' : '' ) . ' value="10">10 miles</option>';
                    $form .= '<option' . ( $user_radius == 20 ? ' selected="selected"' : '' ) . ' value="20">20 miles</option>';
                    $form .= '<option' . ( $user_radius == 50 ? ' selected="selected"' : '' ) . ' value="50">50 miles</option>';
                    $form .= '<option' . ( $user_radius == 100 ? ' selected="selected"' : '' ) . ' value="100">100 miles</option>';
                $form .= '</select>';
            $form .= '</div>';
            $form .= '<div class="large-4 columns">';
                $form .= '<input class="button" type="submit" value="Find locations" style="margin-top:7px;" />';
                $form .= '<a class="button warning" href="<?php echo get_site_url(); ?>/location/" style="margin-top:7px;font-size:200%;padding:.25em .5em .6em;line-height:.78;">&times;</a>';
            $form .= '</div>';
        $form .= '</div>';
    $form .= '</form>';
    return $form;
}
add_shortcode( 'location_search', 'location_search_form_shortcode' );

/*** CALCULATE DISTANCE USING LAT/LONG, GIVEN A ZIP CODE ***/
function location_posts_where( $where )  
{  
    global $wpdb;
    //Get user location from ZIP
    $geocode=file_get_contents('https://maps.google.com/maps/api/geocode/json?key=AIzaSyDFesAMjYEKk6hCIxnQ_3SIwJ6rImbSch8&address='.get_query_var('user_ZIP').'&sensor=false');
    $output = json_decode($geocode);
    $table_name = $wpdb->prefix . 'locations';
    if ( $output->status == 'OK' ) {
        $lat = $output->results[0]->geometry->location->lat;
        $lng = $output->results[0]->geometry->location->lng;

        $radius = get_query_var('user_radius'); // (in miles)

        // Append our radius calculation to the WHERE  
        $where .= " AND $wpdb->posts.ID IN (SELECT post_id FROM " . $table_name . " WHERE
             ( 3963.1676 * acos( cos( radians(" . $lat . ") )
                            * cos( radians( lat ) )
                            * cos( radians( lng )
                            - radians(" . $lng . ") )
                            + sin( radians(" . $lat . ") )
                            * sin( radians( lat ) ) ) ) <= " . $radius . ")";
    } else {
        $where .= " AND $wpdb->posts.ID IN (SELECT post_id FROM " . $table_name . " WHERE
             ( 3963.1676 * acos( cos( radians(39.5501) )
                            * cos( radians( lat ) )
                            * cos( radians( lng )
                            - radians(-105.7821) )
                            + sin( radians(39.5501) )
                            * sin( radians( lat ) ) ) ) <= 20)";
    }
    // Return the updated WHERE part of the query  
    return $where;
}

/* Search filter for nearby stuff by lat/lon */
function location_posts_near( $where )  
{  
    global $wpdb;

    global $post;
    $lat = get_post_meta($post->ID, '_location_latitude', true);
    $lng = get_post_meta($post->ID, '_location_longitude', true);

    $table_name = $wpdb->prefix . 'locations';
    // Append our radius calculation to the WHERE  
    $where .= " AND $wpdb->posts.ID IN (SELECT post_id FROM " . $table_name . " WHERE
         ( 3963.1676 * acos( cos( radians(" . $lat . ") )
                        * cos( radians( lat ) )
                        * cos( radians( lng )
                        - radians(" . $lng . ") )
                        + sin( radians(" . $lat . ") )
                        * sin( radians( lat ) ) ) ) < 5 )"; // 2.5 = 'nearby' distance radius in miles

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
    $loc_shortcode_wide = get_post_meta( $post->ID, '_loc_shortcode_wide', true );
    $loc_shortcode = get_post_meta( $post->ID, '_loc_shortcode', true );
    $loc_shortcode_map = get_post_meta( $post->ID, '_loc_shortcode_map', true );
    $loc_shortcode_ids = explode( ',', $loc_shortcode );
    $args = array(
        'post_type' => 'location',
        'post__in' => $loc_shortcode_ids,
        'adp_disable'       => true,
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
        <p><label><input type="checkbox" name="loc_shortcode_wide" id="loc_shortcode_wide" value="true" <?php if ( $loc_shortcode_wide == 'true' ) echo 'checked'; ?> /> Display full-width?</label></p>
        <p><label>Mapping type: <select name="loc_shortcode_map" id="loc_shortcode_map">
            <option value="none"<?php echo ( ! isset( $loc_shortcode_map ) || $loc_shortcode_map == 'none' ) ? ' selected="selected"' : ''; ?>>None</option>
            <option value="above"<?php echo ( isset( $loc_shortcode_map ) && $loc_shortcode_map == 'above' ) ? ' selected="selected"' : ''; ?>>Above</option>
            <option value="below"<?php echo ( isset( $loc_shortcode_map ) && $loc_shortcode_map == 'below' ) ? ' selected="selected"' : ''; ?>>Below</option>
            <option value="only"<?php echo ( isset( $loc_shortcode_map ) && $loc_shortcode_map == 'only' ) ? ' selected="selected"' : ''; ?>>Only</option>
            </select></label></p>
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
            var locationWideSrc = document.getElementById('loc_shortcode_wide').checked;
            var mapSelect = document.getElementById("loc_shortcode_map");
            var mapSelectValue = mapSelect.options[mapSelect.selectedIndex].value;
            var locationIdsSrc = getLocationIDs();
            var locationRanked = (locationRankedSrc) ? ' ranked="true"' : '';
            var locationWide = (locationWideSrc) ? ' wide="true"' : '';
            var mapSelected = (mapSelectValue) ? ' map="' + mapSelectValue + '"' : '';
            var locationIds = (locationIdsSrc !== '') ? ' ids="' + locationIdsSrc + '"' : false;
            if ( locationIds !== false ) {
                wp.media.editor.insert('[locations' + locationIds + locationRanked + locationWide + mapSelected + ']');
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
    <?php wp_reset_query();
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

    $loc_shortcode_wide = $_POST['loc_shortcode_wide'];

    $loc_shortcode_wide_new_value = ( isset( $loc_shortcode_wide ) && $loc_shortcode_wide == 'true' ) ? 'true' : 'false';
    $loc_shortcode_wide_meta_key = '_loc_shortcode_wide';
    $loc_shortcode_wide_meta_value = get_post_meta( $post_id, $loc_shortcode_wide_meta_key, true );
    if ( $loc_shortcode_wide_new_value && '' == $loc_shortcode_wide_meta_value )
        add_post_meta( $post_id, $loc_shortcode_wide_meta_key, $loc_shortcode_wide_new_value, true );
    elseif ( $loc_shortcode_wide_new_value && $loc_shortcode_wide_new_value != $loc_shortcode_wide_meta_value )
        update_post_meta( $post_id, $loc_shortcode_wide_meta_key, $loc_shortcode_wide_new_value );
    elseif ( '' == $loc_shortcode_wide_new_value && $loc_shortcode_wide_meta_value )
        delete_post_meta( $post_id, $loc_shortcode_wide_meta_key, $loc_shortcode_wide_meta_value );

    $loc_shortcode_map = $_POST['loc_shortcode_map'];

    $loc_shortcode_map_new_value = ( isset( $loc_shortcode_map ) ) ? sanitize_html_class( $loc_shortcode_map ) : 'none';
    $loc_shortcode_map_meta_key = '_loc_shortcode_map';
    $loc_shortcode_map_meta_value = get_post_meta( $post_id, $loc_shortcode_map_meta_key, true );
    if ( $loc_shortcode_map_new_value && '' == $loc_shortcode_map_meta_value )
        add_post_meta( $post_id, $loc_shortcode_map_meta_key, $loc_shortcode_map_new_value, true );
    elseif ( $loc_shortcode_map_new_value && $loc_shortcode_map_new_value != $loc_shortcode_map_meta_value )
        update_post_meta( $post_id, $loc_shortcode_map_meta_key, $loc_shortcode_map_new_value );
    elseif ( '' == $loc_shortcode_map_new_value && $loc_shortcode_map_meta_value )
        delete_post_meta( $post_id, $loc_shortcode_map_meta_key, $loc_shortcode_map_meta_value );
}

function locations_shortcode( $atts = [], $content = null, $tag = '' ) {
    global $post;
    $recursive = ( $post->post_type == 'location' ) ? true : false;
    if ( $recursive ) {
        remove_filter( 'the_content', 'location_image_the_content' );
    } 
    $atts = array_change_key_case( (array)$atts, CASE_LOWER );
    // override default attributes with user attributes
    $loc_atts = shortcode_atts([
        'ids' => '',
        'ranked' => 'false',
        'wide' => 'false',
        'map' => 'none'
     ], $atts, $tag);

    // Get all the pieces of the shortcode input
    $loc_shortcode_ids = explode( ',', $loc_atts['ids'] );
    $loc_add_map = ( in_array( $loc_atts['map'], array('above','below','only') ) ) ? true : false;
    if ( $loc_add_map ) {
        $map_div = '<div class="neighborhood-map-form">';
            $map_div .= '<div class="map-expander"></div>';
            $map_div .= do_shortcode('[leaflet-map zoomcontrol="1"]');
        $map_div .= '</div>';
    }
    $loc_wide = ( $loc_atts['wide'] == 'true' ) ? ' fullbleed' : '';
    // Start the output string and add the map if it's at the top or standalone and start the map data
    $map_display = '';
    $locations_display = '<div class="list_locations' . $loc_wide . '">';
    $locations_display .= ( $loc_atts['map'] == 'above' || $loc_atts['map'] == 'only' && ! is_feed() ) ? $map_div : '';
    $loc_i = 0;
    foreach( $loc_shortcode_ids as $loc_post_id ) {
        $loc_i++;
        // Setup individual items based on shortcode info and add post data from each
        $image_url = $locations_display_img = $large_image_url = false;
        $loc_rank = ( $loc_atts['ranked'] == 'true' ) ? '<span class="loc-rank">' . $loc_i . '.</span>': '';
        $loc_post = get_post( $loc_post_id );
        $post_classes = 'location-embed ' . join( ' ', get_post_class( $loc_post->ID ) );
        $loc_imgoverride = get_post_meta( $loc_post->ID, '_loc_imgoverride', true );
        $loc_address_override = get_post_meta( $loc_post->ID, '_loc_address_override', true );
        $address = ( isset( $loc_address_override ) && strlen( $loc_address_override ) >= 1 ) ? $loc_address_override : get_post_meta( $loc_post->ID, '_location_street_address', true );
        if ( $loc_imgoverride != 'true' && has_post_thumbnail( $loc_post->ID ) ) {
            $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $loc_post->ID ), 'large' );
            $image_meta = get_post( get_post_thumbnail_id( $loc_post->ID ) );
        }
        $image_caption = ( isset( $image_meta->post_excerpt ) ) ? $image_meta->post_excerpt : '';
        $image_url = ( isset( $large_image_url ) && strlen( $large_image_url[0] ) >= 1 ) ? $large_image_url[0] : false;
        if ( $loc_atts['map'] != 'only' ) {
            $image_url = ( isset( $large_image_url ) && strlen( $large_image_url[0] ) >= 1 ) ? $large_image_url[0] : false;
            if ( $loc_imgoverride != 'true' && $image_url ) { 
                $locations_display_img = '<figure class="figure wp-caption alignnone">'; 
                    $locations_display_img .= '<img class="size-full" src="' . $image_url . '" alt="' . $loc_post->post_title . '" />';
                    $locations_display_img .= '<figcaption class="wp-caption-text">' . $image_caption . '</figcaption>';
                $locations_display_img .= '</figure>';
            } else {
                $locations_display_img = '';
            }

            $locations_display_string = ( is_feed() ) ? '<h2>%3$s<a href="%4$s" rel="bookmark">%5$s</a></h2><h3>%6$s</h3>%7$s %8$s' : '<article id="location-%1$s" class="%2$s"><div class="entry-body"><header class="entry-header"><h2 class="entry-title">%3$s<a href="%4$s" rel="bookmark">%5$s</a></h2></header><h3 class="entry-subtitle">%6$s</h3><div class="entry-content">%7$s %8$s</div></div></article>';

            $locations_display .= sprintf( $locations_display_string,
                $loc_post->ID,
                $post_classes,
                $loc_rank,
                get_permalink( $loc_post->ID ),
                $loc_post->post_title,
                $address,
                $locations_display_img,
                apply_filters( 'the_content', $loc_post->post_content )
                );
        }
        // Create the map elements for each item
        if ( $loc_add_map && ! is_feed() ) {
            $latitude = get_post_meta( $loc_post->ID, '_location_latitude', true );
            $longitude = get_post_meta( $loc_post->ID, '_location_longitude', true );
            $medium_img_url = ( $loc_post->ID ) ? wp_get_attachment_image_src( get_post_thumbnail_id( $loc_post->ID ), 'medium') : false;
            $img_div = ( $medium_img_url && strlen( $medium_img_url[0] ) >= 1 ) ? '<div class="cat-thumbnail"><div class="cat-imgholder"></div><a href="' . get_permalink( $loc_post->ID ) . '"><div class="cat-img" style="background-image:url(\\\'' . $medium_img_url[0] . '\\\');"></div></a></div>' : '';
            $loc_map_icon = get_post_meta( $loc_post->ID, '_loc_map_icon', true );
            $marker_icon = ( isset( $loc_map_icon ) ) ? get_marker_icon($loc_map_icon) : '';
            $map_display .= do_shortcode('[leaflet-marker zoom=11 lat=' . $latitude . ' lng=' . $longitude . $marker_icon . ']<h3><a href="' . get_permalink( $loc_post->ID ) . '">' . addslashes( $loc_post->post_title ) . '</a></h3><p>' . $address . '</p>' . $img_div . '[/leaflet-marker]' );
        }
    }
    $locations_display .= ( $loc_atts['map'] == 'below' && ! is_feed() ) ? $map_div : '';
    $locations_display .= '</div>';
    $locations_display .= ( $loc_add_map ) ? $map_display : '';
    return $locations_display;
}
add_shortcode( 'locations', 'locations_shortcode' );

/**
 * Let's output the featured image at the top of post that
 * are locations, but lets append it to the top of the content
 * instead of outputting it above the headline or with metas
 */
function location_image_the_content( $content ) {
    if ( is_single() && get_post_type() == 'location' ) {
        global $post;
        $loc_imgoverride = get_post_meta( $post->ID, '_loc_imgoverride', true );
        if ( $loc_imgoverride != 'true' && has_post_thumbnail( $post->ID ) ) {
            $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large' );
            $image_meta = get_post( get_post_thumbnail_id( $post->ID ) );
        }
        $image_caption = ( isset( $image_meta->post_excerpt ) ) ? $image_meta->post_excerpt : '';
        $image_url = ( isset( $large_image_url ) && strlen( $large_image_url[0] ) >= 1 ) ? $large_image_url[0] : false;
        if ( $image_url ) { 
            $image_code = '<figure class="figure wp-caption alignnone">'; 
                $image_code .= '<img class="size-full" src="' . $image_url . '" alt="' . $post->post_title . '" />';
                $image_code .= '<figcaption class="wp-caption-text">' . $image_caption . '</figcaption>';
            $image_code .= '</figure>';
            $content = $image_code . $content;
        }
    }
    return $content;
}
add_filter( 'the_content', 'location_image_the_content' );

/**
 * A fuller sidebar widget with a map like on the Outdoors main
 * page, and a mini search form, too. Since it's really only 
 * for article pages, let's just do it on is_single() only.
 * That means it has no options!
 */
class tkno_location_recent_widget extends WP_Widget {

    function __construct() {
        parent::__construct(
        // Base ID of your widget
        'tkno_location_recent_widget',

        // Widget name will appear in UI
        __('Recent locations Map', ''),

        // Widget description
        array( 'description' => __( 'Displays map of recent locations and mini search form. Display on article pages only.' ), )
        );
    }

    // Creating widget front-end
    // This is where the action happens
    public function widget( $args, $instance ) {
        // This is where you run the code and display the output
        if ( is_single() ) {
            wp_reset_postdata();

            function shuffle_from_recent( $posts, $query ) {
                if( $pick = $query->get( '_location_posts_where' ) ) {
                    shuffle( $posts );
                    $posts = array_slice( $posts, 0, (int) $pick );
                }
                return $posts;
            }
            add_filter( 'the_posts', 'shuffle_from_recent', 10, 2 );
            $query_args = array( 
                'post_type'           => 'location',
                'order_by'            => 'post_date',
                'posts_per_page'      => 20,
                '_location_posts_where' => 5,
                'adp_disable'       => true
                );
            $outdoormap_recent_query = new WP_Query( $query_args );
            if ( $outdoormap_recent_query->have_posts() ) :
            echo $args['before_widget']; ?>
            <h4 class="widget-title">Recently featured places</h4>
            <div class="neighborhood-map-form">
                <div class="map-expander"></div>
                <?php echo do_shortcode( '[leaflet-map zoomcontrol="0"]' );        

                $map_display = '';
                while ( $outdoormap_recent_query->have_posts() ) : $outdoormap_recent_query->the_post();
                    $address = get_post_meta( get_the_ID(), '_location_street_address', true );
                    $latitude = get_post_meta( get_the_ID(), '_location_latitude', true );
                    $longitude = get_post_meta( get_the_ID(), '_location_longitude', true );
                    $loc_map_icon = get_post_meta( get_the_ID(), '_loc_map_icon', true );
                    $marker_icon = ( isset( $loc_map_icon ) ) ? get_marker_icon($loc_map_icon) : '';
                    if ( $address && $latitude && $longitude ) {
                        $map_display .= do_shortcode('[leaflet-marker zoom=11 lat=' . $latitude . ' lng=' . $longitude . $marker_icon . ']<h3><a href="' . get_permalink() . '">' . addslashes( get_the_title() ) . '</a></h3><p>' . $address . '</p>[/leaflet-marker]' );
                    }
                endwhile; // end of the loop
                remove_filter( 'the_posts' , '_location_posts_where' );
                echo $map_display;
                wp_reset_postdata(); ?>
            </div>
            <form class="recent_loc" method="get" action="<?php echo get_site_url(); ?>/location/">
                <input type="hidden" name="locationsearch" value="Y" />
                <div class="row collapse">
                    <div class="large-12 columns">
                        <h2>What are you looking for?</h2>
                    </div>
                    <div class="large-8 columns">
                        <input type="text" name="user_text" id="user_text" value="" />
                    </div>
                    <div class="large-4 columns">
                        <input class="button" type="submit" value="Search" />
                    </div>
                </div>
            </form>
            <?php echo $args['after_widget'];
            endif; // end have_posts() check
            wp_reset_query();
        }
    }
} // Class srd_find_location_widget ends here

// Register and load the widget
function tkno_location_recent_widget_load() {
    register_widget( 'tkno_location_recent_widget' );
}
add_action( 'widgets_init', 'tkno_location_recent_widget_load' );

/**
 * We need a way to override the automatic display of featured images
 * in case we used a slideshow or displayed the/an image differently
 * for whatever reason
 */
function location_imgoverride_metabox_setup() {
    add_action( 'add_meta_boxes', 'location_imgoverride_add_metabox' );
    add_action( 'save_post', 'location_imgoverride_save_metabox', 10, 2 );
}
add_action( 'load-post.php', 'location_imgoverride_metabox_setup' );
add_action( 'load-post-new.php', 'location_imgoverride_metabox_setup' );

/* Create one or more meta boxes to be displayed on the post editor screen. */
function location_imgoverride_add_metabox() {
    add_meta_box(
        'location_imgoverride',
        esc_html__( 'Location Options', 'example' ),
        'location_imgoverride_metabox',
        'location',
        'side',
        'default'
    );
}

/* Display the post meta box. */
function location_imgoverride_metabox( $post ) {
    echo '<input type="hidden" name="location_imgoverride_nonce" id="location_imgoverride_nonce" value="' .
    wp_create_nonce( basename(__FILE__) ) . '" />';
    $loc_imgoverride = get_post_meta( $post->ID, '_loc_imgoverride', true );
    $loc_address_override = get_post_meta( $post->ID, '_loc_address_override', true );
    $loc_map_icon = get_post_meta( $post->ID, '_loc_map_icon', true );
    ?>
    <form id="location_imgoverride_options">
        <p><label><input type="checkbox" name="loc_imgoverride" id="loc_imgoverride" value="true" <?php if ( $loc_imgoverride == 'true' ) echo 'checked'; ?> /> Hide featured image on output?</label></p>
        <p><label>Address override text:</label> <input class="widefat" type="text" name="loc_address_override" id="loc_address_override" value="<?php echo $loc_address_override; ?>" /></p>
        <p><label>Map icon: <select name="loc_map_icon" id="loc_map_icon">
            <option value="none"<?php echo ( ! isset( $loc_map_icon ) || $loc_map_icon == 'none' ) ? ' selected="selected"' : ''; ?>>Default</option>
            <option value="boating"<?php echo ( isset( $loc_map_icon ) && $loc_map_icon == 'boating' ) ? ' selected="selected"' : ''; ?>>Boating</option>
            <option value="camping"<?php echo ( isset( $loc_map_icon ) && $loc_map_icon == 'camping' ) ? ' selected="selected"' : ''; ?>>Camping</option>
            <option value="climbing"<?php echo ( isset( $loc_map_icon ) && $loc_map_icon == 'climbing' ) ? ' selected="selected"' : ''; ?>>Climbing</option>
            <option value="cycling"<?php echo ( isset( $loc_map_icon ) && $loc_map_icon == 'cycling' ) ? ' selected="selected"' : ''; ?>>Cycling</option>
            <option value="dangling"<?php echo ( isset( $loc_map_icon ) && $loc_map_icon == 'dangling' ) ? ' selected="selected"' : ''; ?>>Dangling</option>
            <option value="drinking"<?php echo ( isset( $loc_map_icon ) && $loc_map_icon == 'drinking' ) ? ' selected="selected"' : ''; ?>>Drinking</option>
            <option value="fishing"<?php echo ( isset( $loc_map_icon ) && $loc_map_icon == 'fishing' ) ? ' selected="selected"' : ''; ?>>Fishing</option>
            <option value="flowers"<?php echo ( isset( $loc_map_icon ) && $loc_map_icon == 'flowers' ) ? ' selected="selected"' : ''; ?>>Flowers</option>
            <option value="hiking"<?php echo ( isset( $loc_map_icon ) && $loc_map_icon == 'hiking' ) ? ' selected="selected"' : ''; ?>>Hiking</option>
            <option value="hunting"<?php echo ( isset( $loc_map_icon ) && $loc_map_icon == 'hunting' ) ? ' selected="selected"' : ''; ?>>Hunting</option>
            <option value="offroading"<?php echo ( isset( $loc_map_icon ) && $loc_map_icon == 'offroading' ) ? ' selected="selected"' : ''; ?>>Off-Roading</option>
            <option value="picnic"<?php echo ( isset( $loc_map_icon ) && $loc_map_icon == 'picnic' ) ? ' selected="selected"' : ''; ?>>Picnic</option>
            <option value="running"<?php echo ( isset( $loc_map_icon ) && $loc_map_icon == 'running' ) ? ' selected="selected"' : ''; ?>>Running</option>
            <option value="shopping"<?php echo ( isset( $loc_map_icon ) && $loc_map_icon == 'shopping' ) ? ' selected="selected"' : ''; ?>>Shopping</option>
            <option value="sightseeing"<?php echo ( isset( $loc_map_icon ) && $loc_map_icon == 'sightseeing' ) ? ' selected="selected"' : ''; ?>>Sightseeing</option>
            <option value="skiing"<?php echo ( isset( $loc_map_icon ) && $loc_map_icon == 'skiing' ) ? ' selected="selected"' : ''; ?>>Skiing</option>
            <option value="snowboarding"<?php echo ( isset( $loc_map_icon ) && $loc_map_icon == 'snowboarding' ) ? ' selected="selected"' : ''; ?>>Snowboarding</option>
            <option value="wildlife"<?php echo ( isset( $loc_map_icon ) && $loc_map_icon == 'wildlife' ) ? ' selected="selected"' : ''; ?>>Wildlife</option>
            </select></label></p>
    </form>
    <?php
}

// Let's save data from the metabox for the future, duh
function location_imgoverride_save_metabox( $post_id, $post ) {
    if ( !isset( $_POST['location_imgoverride_nonce'] ) || !wp_verify_nonce( $_POST['location_imgoverride_nonce'], basename( __FILE__ ) ) ) {
        return $post_id;
    }
    if ( !current_user_can( 'edit_post', $post->ID ) )
        return $post_id;

    $loc_imgoverride = $_POST['loc_imgoverride'];

    $loc_imgoverride_new_value = ( isset( $loc_imgoverride ) && $loc_imgoverride == 'true' ) ? 'true' : '';
    $loc_imgoverride_meta_key = '_loc_imgoverride';
    $loc_imgoverride_meta_value = get_post_meta( $post_id, $loc_imgoverride_meta_key, true );
    if ( $loc_imgoverride_new_value && '' == $loc_imgoverride_meta_value )
        add_post_meta( $post_id, $loc_imgoverride_meta_key, $loc_imgoverride_new_value, true );
    elseif ( $loc_imgoverride_new_value && $loc_imgoverride_new_value != $loc_imgoverride_meta_value )
        update_post_meta( $post_id, $loc_imgoverride_meta_key, $loc_imgoverride_new_value );
    elseif ( '' == $loc_imgoverride_new_value && $loc_imgoverride_meta_value )
        delete_post_meta( $post_id, $loc_imgoverride_meta_key, $loc_imgoverride_meta_value );

    $loc_address_override = $_POST['loc_address_override'];

    $loc_address_override_new_value = ( isset( $loc_address_override ) ) ? sanitize_text_field( $loc_address_override ) : 'none';
    $loc_address_override_meta_key = '_loc_address_override';
    $loc_address_override_meta_value = get_post_meta( $post_id, $loc_address_override_meta_key, true );
    if ( $loc_address_override_new_value && '' == $loc_address_override_meta_value )
        add_post_meta( $post_id, $loc_address_override_meta_key, $loc_address_override_new_value, true );
    elseif ( $loc_address_override_new_value && $loc_address_override_new_value != $loc_address_override_meta_value )
        update_post_meta( $post_id, $loc_address_override_meta_key, $loc_address_override_new_value );
    elseif ( '' == $loc_address_override_new_value && $loc_address_override_meta_value )
        delete_post_meta( $post_id, $loc_address_override_meta_key, $loc_address_override_meta_value );

    $loc_map_icon = $_POST['loc_map_icon'];

    $loc_map_icon_new_value = ( isset( $loc_map_icon ) ) ? sanitize_text_field( $loc_map_icon ) : 'none';
    $loc_map_icon_meta_key = '_loc_map_icon';
    $loc_map_icon_meta_value = get_post_meta( $post_id, $loc_map_icon_meta_key, true );
    if ( $loc_map_icon_new_value && '' == $loc_map_icon_meta_value )
        add_post_meta( $post_id, $loc_map_icon_meta_key, $loc_map_icon_new_value, true );
    elseif ( $loc_map_icon_new_value && $loc_map_icon_new_value != $loc_map_icon_meta_value )
        update_post_meta( $post_id, $loc_map_icon_meta_key, $loc_map_icon_new_value );
    elseif ( '' == $loc_map_icon_new_value && $loc_map_icon_meta_value )
        delete_post_meta( $post_id, $loc_map_icon_meta_key, $loc_map_icon_meta_value );
}

/**
 * A function for looking up the special icon, if set,
 * and returning the text to add to the leaflet-marker
 * shortcode for the special icon
 */
function get_marker_icon( $selection ) {
    return ( $selection == '' || $selection == 'none' ) ? '' : ' iconUrl="' . get_stylesheet_directory_uri() . '/map-icons/icon-' . $selection . '.png" iconSize="32,32" iconAnchor="1,31"';
}
