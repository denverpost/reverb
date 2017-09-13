<?php
// Get a venue when the custom field matches a venue taxonomy slug
function tkno_get_venue_from_slug($venue_slug) {
    $args = array(
        'post_type'     => 'venues',
        'meta_query'    => array(
            array(
                'key'   => '_venue_slug',
                'value' => $venue_slug,
                'compare' => 'LIKE',
                'adp_disable' => true
                )
            ),
        'posts_limits'    => 1
        );
    $query = new WP_Query( $args );
    $venues = $query->get_posts();
    $venue = ( count($venues) > 0 ) ? $venues[0] : false;
    wp_reset_postdata();
    return $venue;
}

/**
 * Add a Venue taxonomy for venues (to tie into a Venue post format)
 */
function tkno_register_venue_taxonomy() {
    $labels = array(
        'name'                           => 'Venues',
        'singular_name'                  => 'Venue',
        'search_items'                   => 'Search Venues',
        'all_items'                      => 'All Venues',
        'edit_item'                      => 'Edit Venue',
        'update_item'                    => 'Update Venue',
        'add_new_item'                   => 'Add New Venue',
        'new_item_name'                  => 'New Venue Name',
        'menu_name'                      => 'Venues',
        'view_item'                      => 'View Venues',
        'popular_items'                  => 'Popular Venues',
        'separate_items_with_commas'     => 'Separate venues with commas',
        'add_or_remove_items'            => 'Add or remove venues',
        'choose_from_most_used'          => 'Choose from the most used venues',
        'not_found'                      => 'No venues found'
    );
    register_taxonomy(
        'venue',
        array('post'),
        array(
            'label' => __( 'Venue' ),
            'hierarchical' => false,
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_nav_menus' => false,
            'show_tagcloud' => false,
            'show_admin_column' => true,
            'rewrite' => array(
                'slug' => 'venue',
                'with_front' => false
                ),
        )
    );
}
add_action( 'init', 'tkno_register_venue_taxonomy' );

/**
 * Add a Venue Page post type to tie in to the taxonomy
 */
function tkno_register_venue_page_posttype() {
    $labels = array(
        'name'               => _x( 'Venue Pages', 'post type general name' ),
        'singular_name'      => _x( 'Venue Page', 'post type singular name' ),
        'add_new'            => _x( 'Add New', 'venue' ),
        'add_new_item'       => __( 'Add New Venue Page' ),
        'edit_item'          => __( 'Edit Venue Page' ),
        'new_item'           => __( 'New Venue Page' ),
        'all_items'          => __( 'All Venue Pages' ),
        'view_item'          => __( 'View Venue Page' ),
        'search_items'       => __( 'Search Venue Pages' ),
        'not_found'          => __( 'No venue pages found' ),
        'not_found_in_trash' => __( 'No venue pages found in the Trash' ), 
        'parent_item_colon'  => '',
        'menu_name'          => 'Venue Pages'
    );
    $args = array(
        'labels'        => $labels,
        'description'   => 'Venue Pages feature a single venue and pull in related items based on the Venue taxonomy',
        'public'        => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'show_ui'       => true,
        'menu_position' => 5,
        'capability_type' => 'post',
        'query_var'     => true,
        'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes', 'revisions', 'author', 'custom-fields', ),
        'rewrite' => array(
            'slug' => 'venues',
            'with_front' => false
            ),
        'has_archive'   => true
    );
    register_post_type( 'venues', $args );
}
add_action( 'init', 'tkno_register_venue_page_posttype' );

/**
 * Custom interaction messages for Venue Page post type
 */
function tkno_venue_page_messages( $messages ) {
    global $post, $post_ID;
    $messages['venues'] = array(
        0 => '', 
        1 => sprintf( __('Venue page updated. <a href="%s">View venue page</a>'), esc_url( get_permalink($post_ID) ) ),
        2 => __('Custom field updated.'),
        3 => __('Custom field deleted.'),
        4 => __('Venue page updated.'),
        5 => isset($_GET['revision']) ? sprintf( __('Venue page restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
        6 => sprintf( __('Venue page published. <a href="%s">View venue page</a>'), esc_url( get_permalink($post_ID) ) ),
        7 => __('Venue page saved.'),
        8 => sprintf( __('Venue page submitted. <a target="_blank" href="%s">Preview venue page</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
        9 => sprintf( __('Venue page scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview venue page</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
        10 => sprintf( __('Venue page draft updated. <a target="_blank" href="%s">Preview venue page</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    );
    return $messages;
}
add_filter( 'post_updated_messages', 'tkno_venue_page_messages' );

/**
 * Contextual help for venue pages
 */
function tkno_venue_page_contextual_help( $contextual_help, $screen_id, $screen ) { 
  if ( 'venues' == $screen->id ) {

    $contextual_help = '<h2>Venue pages</h2>
    <p>Venue pages show details about a particular venue, and tie in recent posts assigned to that venue. You can see a list of them on this page in reverse chronological order - the latest one we added is first.</p> 
    <p>You can view/edit the details of each venue page by clicking on its name, or you can perform bulk actions using the dropdown menu and selecting multiple items.</p>';

  } elseif ( 'edit-venue_page' == $screen->id ) {

    $contextual_help = '<h2>Editing venue pages</h2>
    <p>This page allows you to view/modify venue pages. Please make sure to fill out the available boxes with the appropriate details and <strong>not</strong> add these details to the venue description.</p>';

  }
  return $contextual_help;
}
add_action( 'contextual_help', 'tkno_venue_page_contextual_help', 10, 3 );

/* Fire our meta box setup function on the post editor screen. */
add_action( 'load-post.php', 'venue_post_meta_boxes_setup' );
add_action( 'load-post-new.php', 'venue_post_meta_boxes_setup' );

/* Meta box setup function. */
function venue_post_meta_boxes_setup() {
    /* Add meta boxes on the 'add_meta_boxes' hook. */
    add_action( 'add_meta_boxes', 'venue_add_post_meta_boxes' );
    /* Save post meta on the 'save_post' hook. */
    add_action( 'save_post', 'venue_save_post_meta', 10, 2 );
}

/* Create one or more meta boxes to be displayed on the post editor screen. */
function venue_add_post_meta_boxes() {
    add_meta_box(
        'venue_details',      // Unique ID
        esc_html__( 'Venue Details', 'example' ),    // Title
        'venue_post_meta_box',   // Callback function
        'venues',         // Admin page (or post type)
        'side',         // Context
        'default'         // Priority
    );
}

/* Display the post meta box. */
function venue_post_meta_box( $post ) { ?>
    <?php wp_nonce_field( basename( __FILE__ ), 'venue_meta_nonce' );
    $args = array(
        'orderby'                  => 'name',
        'order'                    => 'ASC',
        'hide_empty'               => 0,
        'taxonomy'                 => 'venue'
        );
    $venues_list = get_terms( $args );
    foreach( $venues_list as $venue_single ) { 
        $venues[] =  array(
            'slug' => $venue_single->slug,
            'name' => $venue_single->name
            );
    }
    $venue_slug_current = get_post_meta( $post->ID, '_venue_slug', true ); ?>
    <p>
    <label for="_venue_slug"><?php _e( "Venue for related stories:", '' ); ?></label>
    <br />
    <select class="widefat" name="_venue_slug" id="_venue_slug">
        <?php foreach ($venues as $venue) { ?>
            <option value="<?php echo $venue['slug']; ?>"<?php echo ($venue_slug_current == $venue['slug'] ) ? ' selected="selected"' : ''; ?>><?php echo $venue['name']; ?></option>
        <?php }?>
    </select>
    </p>
    <p>
    <label for="_venue_calendar_id"><?php _e( "CitySpark widget ID:", '' ); ?></label>
    <br />
    <input class="widefat" type="text" name="_venue_calendar_id" id="_venue_calendar_id" value="<?php echo esc_attr( get_post_meta( $post->ID, '_venue_calendar_id', true ) ); ?>" size="30" />
    </p>
    <p>
    <label for="_venue_map_id"><?php _e( "Google My Maps ID:", '' ); ?></label>
    <br />
    <input class="widefat" type="text" name="_venue_map_id" id="_venue_map_id" value="<?php echo esc_attr( get_post_meta( $post->ID, '_venue_map_id', true ) ); ?>" size="30" />
    </p>
<?php }

/* Save the meta box's post metadata. */
function venue_save_post_meta( $post_id, $post ) {

    /* Verify the nonce before proceeding. */
    if ( !isset( $_POST['venue_meta_nonce'] ) || !wp_verify_nonce( $_POST['venue_meta_nonce'], basename( __FILE__ ) ) )
        return $post_id;

    /* Get the post type object. */
    $post_type = get_post_type_object( $post->post_type );

    /* Check if the current user has permission to edit the post. */
    if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
        return $post_id;

    /* Get the calendar data and validate it, then save, update or delete it. */
    $cal_new_meta_value = ( isset( $_POST['_venue_calendar_id'] ) && ctype_digit( $_POST['_venue_calendar_id'] ) && strlen( $_POST['_venue_calendar_id'] ) == 4 ) ? $_POST['_venue_calendar_id'] : '';
    $cal_meta_key = '_venue_calendar_id';
    $cal_meta_value = get_post_meta( $post_id, $meta_key, true );
    if ( $cal_new_meta_value && '' == $cal_meta_value )
        add_post_meta( $post_id, $cal_meta_key, $cal_new_meta_value, true );
    elseif ( $cal_new_meta_value && $cal_new_meta_value != $cal_meta_value )
        update_post_meta( $post_id, $cal_meta_key, $cal_new_meta_value );
    elseif ( '' == $cal_new_meta_value && $cal_meta_value )
        delete_post_meta( $post_id, $cal_meta_key, $cal_meta_value );

    $slug_new_meta_value = ( isset( $_POST['_venue_slug'] ) ) ? sanitize_html_class( $_POST['_venue_slug'] ) : '';
    $slug_meta_key = '_venue_slug';
    $slug_meta_value = get_post_meta( $post_id, $slug_meta_key, true );
    if ( $slug_new_meta_value && '' == $slug_meta_value )
        add_post_meta( $post_id, $slug_meta_key, $slug_new_meta_value, true );
    elseif ( $slug_new_meta_value && $slug_new_meta_value != $slug_meta_value )
        update_post_meta( $post_id, $slug_meta_key, $slug_new_meta_value );
    elseif ( '' == $slug_new_meta_value && $slug_meta_value )
        delete_post_meta( $post_id, $slug_meta_key, $slug_meta_value );

    $map_new_meta_value = ( isset( $_POST['_venue_map_id'] ) ) ? sanitize_text_field( $_POST['_venue_map_id'] ) : '';
    $map_meta_key = '_venue_map_id';
    $map_meta_value = get_post_meta( $post_id, $map_meta_key, true );
    if ( $map_new_meta_value && '' == $map_meta_value )
        add_post_meta( $post_id, $map_meta_key, $map_new_meta_value, true );
    elseif ( $map_new_meta_value && $map_new_meta_value != $map_meta_value )
        update_post_meta( $post_id, $map_meta_key, $map_new_meta_value );
    elseif ( '' == $map_new_meta_value && $map_meta_value )
        delete_post_meta( $post_id, $map_meta_key, $map_meta_value );
}
