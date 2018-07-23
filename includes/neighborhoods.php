<?php

/**
 * Everything to do with The Know Neighborhoods' special functionality
 */

// Get a neighborhood when the custom field matches a neighborhood taxonomy slug
function tkno_get_neighborhood_from_slug($neighborhood_slug) {
    $args = array(
        'post_type'     => 'neighborhoods',
        'meta_query'    => array(
            array(
                'key'   => '_neighborhood_slug',
                'value' => $neighborhood_slug,
                'compare' => '=',
                'adp_disable' => true
                )
            ),
        'posts_limits'    => 1
        );
    $query = new WP_Query( $args );
    $neighborhoods = $query->get_posts();
    $neighborhood = ( count($neighborhoods) > 0 ) ? $neighborhoods[0] : false;
    wp_reset_query();
    return $neighborhood;
}

/**
 * Add a Neighborhood taxonomy for neighborhoods (to tie into a Neighborhood post format)
 */
function tkno_register_neighborhood_taxonomy() {
    $labels = array(
        'name'                           => 'Neighborhoods',
        'singular_name'                  => 'Neighborhood',
        'search_items'                   => 'Search Neighborhoods',
        'all_items'                      => 'All Neighborhoods',
        'edit_item'                      => 'Edit Neighborhood',
        'update_item'                    => 'Update Neighborhood',
        'add_new_item'                   => 'Add New Neighborhood',
        'new_item_name'                  => 'New Neighborhood Name',
        'menu_name'                      => 'Neighborhoods',
        'view_item'                      => 'View Neighborhoods',
        'popular_items'                  => 'Popular Neighborhoods',
        'separate_items_with_commas'     => 'Separate neighborhoods with commas',
        'add_or_remove_items'            => 'Add or remove neighborhoods',
        'choose_from_most_used'          => 'Choose from the most used neighborhoods',
        'not_found'                      => 'No neighborhoods found'
    );
    register_taxonomy(
        'neighborhood',
        array('post','location'),
        array(
            'label' => __( 'Neighborhood' ),
            'hierarchical' => true,
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_nav_menus' => false,
            'show_tagcloud' => false,
            'show_admin_column' => true,
            'rewrite' => array(
                'slug' => 'neighborhood',
                'with_front' => false
                ),
        )
    );
}
add_action( 'init', 'tkno_register_neighborhood_taxonomy' );

/** 
 * Adds a imagepath field to our taxonomy in create mode
 * 
 * @author Hendrik Schuster <contact@deviantdev.com>
 * @param int $term the concrete term
 */
function add_neighborhood_fields_oncreate( $term ){
    
    echo '<div class="form-field term-pretty-name-wrap">';
    echo '<label for="pretty_name_field">Pretty name</label>';
    echo '<input id="pretty_name_field" value="" size="40" type="text" name="pretty_name_field"/>';
    echo '<p class="description">Used to power listings widget.</p>';
    echo '</div>';
}

/** 
 * Adds a custom field to our taxonomy in edit mode
 * Gets the current value from the database and renders the output
 * 
 * @since 1.0
 * @author Hendrik Schuster <contact@deviantdev.com>
 * @param int $term the concrete term
 */
function add_neighborhood_field_onedit( $term ){
    $termID = $term->term_id;
    $termMeta = get_option( "neighborhood_$termID" );    
    $pretty_name_field = $termMeta['pretty_name_field'];
    
    echo '<tr class="form-field form-required term-name-wrap">';
    echo '<th scope="row"><label for="pretty_name_field">Pretty name</label></th>';
    echo '<td><input id="pretty_name_field" value="' . $pretty_name_field . '" size="40" type="text" name="pretty_name_field" />';
    echo '<p class="description">Version of the name to use when looking for real estate listings.</p>';
    echo '</tr>';
}
add_action( 'neighborhood_add_form_fields', 'add_neighborhood_fields_oncreate' );
add_action( 'neighborhood_edit_form_fields', 'add_neighborhood_field_onedit' );

/** 
 * Does the saving for our extra image property field
 * Takes the options array from the database and alters the pretty_name_field value
 * 
 * @since 1.0
 * @author Hendrik Schuster <contact@deviantdev.com>
 * @param int $termID ID of the term we are saving 
 */
function save_custom_neighborhood_fields( $termID ){

    if ( isset( $_POST['pretty_name_field'] ) ) {
        
        // get options from database - if not a array create a new one
        $termMeta = get_option( "neighborhood_$termID" );
        if ( !is_array( $termMeta ))
            $termMeta = array();
        
        // get value and save it into the database - maybe you have to sanitize your values (urls, etc...)
        $termMeta['pretty_name_field'] = isset( $_POST['pretty_name_field'] ) ? $_POST['pretty_name_field'] : '';
        update_option( "neighborhood_$termID", $termMeta );
    }
}
add_action( 'create_neighborhood', 'save_custom_neighborhood_fields' );
add_action( 'edited_neighborhood', 'save_custom_neighborhood_fields' );

/**
 * Add a Neighborhood Page post type to tie in to the taxonomy
 */
function tkno_register_neighborhood_page_posttype() {
    $labels = array(
        'name'               => _x( 'Neighborhood Pages', 'post type general name' ),
        'singular_name'      => _x( 'Neighborhood Page', 'post type singular name' ),
        'add_new'            => _x( 'Add New', 'neighborhood' ),
        'add_new_item'       => __( 'Add New Neighborhood Page' ),
        'edit_item'          => __( 'Edit Neighborhood Page' ),
        'new_item'           => __( 'New Neighborhood Page' ),
        'all_items'          => __( 'All Neighborhood Pages' ),
        'view_item'          => __( 'View Neighborhood Page' ),
        'search_items'       => __( 'Search Neighborhood Pages' ),
        'not_found'          => __( 'No neighborhood pages found' ),
        'not_found_in_trash' => __( 'No neighborhood pages found in the Trash' ), 
        'parent_item_colon'  => '',
        'menu_name'          => 'Neighborhood Pages'
    );
    $args = array(
        'labels'        => $labels,
        'description'   => 'Neighborhood Pages feature a single neighborhood and pull in related items based on the Neighborhood taxonomy',
        'public'        => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'show_ui'       => true,
        'menu_position' => 6,
        'capability_type' => 'post',
        'query_var'     => true,
        'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes', 'revisions', 'author', 'custom-fields', ),
        'rewrite' => array(
            'slug' => 'neighborhoods',
            'with_front' => false
            ),
        'has_archive'   => true
    );
    register_post_type( 'neighborhoods', $args );
}
add_action( 'init', 'tkno_register_neighborhood_page_posttype' );

/**
 * Custom interaction messages for Neighborhood Page post type
 */
function tkno_neighborhood_page_messages( $messages ) {
    global $post, $post_ID;
    $messages['neighborhoods'] = array(
        0 => '', 
        1 => sprintf( __('Neighborhood page updated. <a href="%s">View neighborhood page</a>'), esc_url( get_permalink($post_ID) ) ),
        2 => __('Custom field updated.'),
        3 => __('Custom field deleted.'),
        4 => __('Neighborhood page updated.'),
        5 => isset($_GET['revision']) ? sprintf( __('Neighborhood page restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
        6 => sprintf( __('Neighborhood page published. <a href="%s">View neighborhood page</a>'), esc_url( get_permalink($post_ID) ) ),
        7 => __('Neighborhood page saved.'),
        8 => sprintf( __('Neighborhood page submitted. <a target="_blank" href="%s">Preview neighborhood page</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
        9 => sprintf( __('Neighborhood page scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview neighborhood page</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
        10 => sprintf( __('Neighborhood page draft updated. <a target="_blank" href="%s">Preview neighborhood page</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    );
    return $messages;
}
add_filter( 'post_updated_messages', 'tkno_neighborhood_page_messages' );

/**
 * Contextual help for neighborhood pages
 */
function tkno_neighborhood_page_contextual_help( $contextual_help, $screen_id, $screen ) { 
  if ( 'neighborhoods' == $screen->id ) {

    $contextual_help = '<h2>Neighborhood pages</h2>
    <p>Neighborhood pages show details about a particular neighborhood, and tie in recent posts assigned to that neighborhood. You can see a list of them on this page in reverse chronological order - the latest one we added is first.</p> 
    <p>You can view/edit the details of each neighborhood page by clicking on its name, or you can perform bulk actions using the dropdown menu and selecting multiple items.</p>';

  } elseif ( 'edit-neighborhood_page' == $screen->id ) {

    $contextual_help = '<h2>Editing neighborhood pages</h2>
    <p>This page allows you to view/modify neighborhood pages. Please make sure to fill out the available boxes with the appropriate details and <strong>not</strong> add these details to the neighborhood description.</p>';

  }
  return $contextual_help;
}
add_action( 'contextual_help', 'tkno_neighborhood_page_contextual_help', 10, 3 );

/* Fire our meta box setup function on the post editor screen. */
add_action( 'load-post.php', 'neighborhoods_post_meta_boxes_setup' );
add_action( 'load-post-new.php', 'neighborhoods_post_meta_boxes_setup' );

/* Neighborhoods Meta box setup function. */
function neighborhoods_post_meta_boxes_setup() {
    /* Add meta boxes on the 'add_meta_boxes' hook. */
    add_action( 'add_meta_boxes', 'neighborhoods_add_post_meta_boxes' );
    /* Save post meta on the 'save_post' hook. */
    add_action( 'save_post', 'neighborhoods_save_post_meta', 10, 2 );
}

/* Create one or more meta boxes to be displayed on the post editor screen. */
function neighborhoods_add_post_meta_boxes() {
    add_meta_box(
        'neighborhood_details',      // Unique ID
        esc_html__( 'Neighborhood Details', 'example' ),    // Title
        'neighborhoods_post_meta_box',   // Callback function
        'neighborhoods',         // Admin page (or post type)
        'side',         // Context
        'default'         // Priority
    );
}

/* Display the post meta box. */
function neighborhoods_post_meta_box( $post ) { ?>
    <?php wp_nonce_field( basename( __FILE__ ), 'neighborhoods_meta_nonce' );
    $args = array(
        'orderby'                  => 'name',
        'order'                    => 'ASC',
        'hide_empty'               => 0,
        'taxonomy'                 => 'neighborhood'
        );
    $neighborhoods_list = get_terms( $args );
    foreach( $neighborhoods_list as $neighborhoods_single ) { 
        $neighborhoods[] =  array(
            'slug' => $neighborhoods_single->slug,
            'name' => $neighborhoods_single->name
            );
    }
    $neighborhoods_slug_current = get_post_meta( $post->ID, '_neighborhood_slug', true ); ?>
    <p>
    <label for="_neighborhood_slug"><?php _e( "Neighborhood for related stories:", '' ); ?></label>
    <br />
    <select class="widefat" name="_neighborhood_slug" id="_neighborhood_slug">
        <?php foreach ($neighborhoods as $neighborhood) { ?>
            <option value="<?php echo $neighborhood['slug']; ?>"<?php echo ($neighborhoods_slug_current == $neighborhood['slug'] ) ? ' selected="selected"' : ''; ?>><?php echo $neighborhood['name']; ?></option>
        <?php }?>
    </select>
    </p>
    <p>
    <label for="_neighborhood_feed"><?php _e( "Feed URL for location news:", '' ); ?></label>
    <br />
    <input class="widefat" type="text" name="_neighborhood_feed" id="_neighborhood_feed" value="<?php echo esc_attr( get_post_meta( $post->ID, '_neighborhood_feed', true ) ); ?>" size="30" />
    </p>
<?php }

/* Save the neighborhoods meta box's post metadata. */
function neighborhoods_save_post_meta( $post_id, $post ) {

    /* Verify the nonce before proceeding. */
    if ( !isset( $_POST['neighborhoods_meta_nonce'] ) || !wp_verify_nonce( $_POST['neighborhoods_meta_nonce'], basename( __FILE__ ) ) )
        return $post_id;

    /* Get the post type object. */
    $post_type = get_post_type_object( $post->post_type );

    /* Check if the current user has permission to edit the post. */
    if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
        return $post_id;


    $slug_new_meta_value = ( isset( $_POST['_neighborhood_slug'] ) ) ? sanitize_html_class( $_POST['_neighborhood_slug'] ) : '';
    $slug_meta_key = '_neighborhood_slug';
    $slug_meta_value = get_post_meta( $post_id, $slug_meta_key, true );
    if ( $slug_new_meta_value && '' == $slug_meta_value )
        add_post_meta( $post_id, $slug_meta_key, $slug_new_meta_value, true );
    elseif ( $slug_new_meta_value && $slug_new_meta_value != $slug_meta_value )
        update_post_meta( $post_id, $slug_meta_key, $slug_new_meta_value );
    elseif ( '' == $slug_new_meta_value && $slug_meta_value )
        delete_post_meta( $post_id, $slug_meta_key, $slug_meta_value );

    $feed_new_meta_value = ( isset( $_POST['_neighborhood_feed'] ) ) ? esc_url( strip_tags( $_POST['_neighborhood_feed'] ) ) : '';
    $feed_meta_key = '_neighborhood_feed';
    $feed_meta_value = get_post_meta( $post_id, $feed_meta_key, true );
    if ( $feed_new_meta_value && '' == $feed_meta_value )
        add_post_meta( $post_id, $feed_meta_key, $feed_new_meta_value, true );
    elseif ( $feed_new_meta_value && $feed_new_meta_value != $feed_meta_value )
        update_post_meta( $post_id, $feed_meta_key, $feed_new_meta_value );
    elseif ( '' == $feed_new_meta_value && $feed_meta_value )
        delete_post_meta( $post_id, $feed_meta_key, $feed_meta_value );
}

/**
 * A widget that finds related post based on the
 * stories' neighborhood taxonomy values
 * 
 * @return html list inserted in widget
 */
class neighborhood_related_widget extends WP_Widget {
    public function __construct() {
            parent::__construct(
                'neighborhood_related_widget',
                __('Neighborhood related', 'neighborhood_related_widget'),
                array('description' => __('Displays stories from a selected category or tag that also include the given neighborhood.', 'neighborhood_related_widget'), )
            );
    }

    public function form( $instance ) {
        $defaults = array( 'neighborhood_category' => __( '' ), 'neighborhood_tag' => __( '' ), 'neighborhood_posts' => __( '3' ) );
        $instance = wp_parse_args( ( array ) $instance, $defaults ); ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'neighborhood_posts' ); ?>"><?php _e( 'Number of posts to display:' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'neighborhood_posts' ); ?>" name="<?php echo $this->get_field_name( 'neighborhood_posts' ); ?>" type="text" value="<?php echo $instance[ 'neighborhood_posts' ]; ?>" />
        </p>
        <p>
        <label for="<?php echo $this->get_field_id( 'neighborhood_category' ); ?>"><?php _e( 'Category for related articles (falls back to parent if no posts found in a child category):' ); ?></label> 
        <select id="<?php echo $this->get_field_id( 'neighborhood_category' ); ?>" name="<?php echo $this->get_field_name( 'neighborhood_category' ); ?>" class="widefat" style="width:100%;">
        <option <?php echo ( $instance[ 'neighborhood_category' ] == '' ) ? 'selected="selected" ' : ''; ?> value="">&nbsp;</option>
            <?php foreach( get_terms( 'category' ) as $term) { ?>
            <option <?php selected( $instance[ 'neighborhood_category' ], $term->term_id ); ?> value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
            <?php } ?>      
        </select>
        </p>
        <p>
        <label for="<?php echo $this->get_field_id( 'neighborhood_tag' ); ?>"><?php _e( 'Or tag (overrides category, if set):' ); ?></label> 
        <select id="<?php echo $this->get_field_id( 'neighborhood_tag' ); ?>" name="<?php echo $this->get_field_name( 'neighborhood_tag' ); ?>" class="widefat" style="width:100%;">
            <option <?php echo ( $instance[ 'neighborhood_tag' ] == '' ) ? 'selected="selected" ' : ''; ?> value="">&nbsp;</option>
            <?php foreach( get_terms( 'post_tag' ) as $term) { ?>
            <option <?php selected( $instance[ 'neighborhood_tag' ], $term->term_id ); ?> value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
            <?php } ?>      
        </select>
        </p>

    <?php }

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance[ 'neighborhood_category' ] = ( ! empty( $new_instance[ 'neighborhood_category' ] ) ) ? trim( strip_tags( $new_instance[ 'neighborhood_category' ] ) ) : '';
        $instance[ 'neighborhood_tag' ] = ( ! empty( $new_instance[ 'neighborhood_tag' ] ) ) ? trim( strip_tags( $new_instance[ 'neighborhood_tag' ] ) ) : '';
        $instance[ 'neighborhood_posts' ] = ( ! empty( $new_instance[ 'neighborhood_posts' ] ) ) ? (int)trim( strip_tags( $new_instance[ 'neighborhood_posts' ] ) ) : 3;
        return $instance;
    }

    public function widget( $args, $instance ) {
        // A reusable query for multiple attempts
        if ( ! function_exists( 'widget_query' ) ) {
            function widget_query( $nei_slug, $nei_tax, $tax_id, $posts_numb ) {
                wp_reset_query();
                remove_all_filters( 'posts_orderby' ); // disable Post Types Order ordering for this query
                $args = array(
                    'post_type'         => 'post',
                    'tax_query'         => array(
                        'relation'  => 'AND',
                        array(
                            'taxonomy'      => 'neighborhood',
                            'field'         => 'slug',
                            'terms'         => $nei_slug,
                            'operator'      => 'IN'
                            ),
                        array(
                            'taxonomy'      => $nei_tax,
                            'field'         => 'term_id',
                            'terms'         => $tax_id,
                            'operator'      => 'IN'
                            ),
                        ),
                    'posts_per_page'    => $posts_numb,
                    'order'             => 'DESC',
                    'orderby'           => 'date',
                    'adp_disable'       => true,
                    );
                $nei_query = new WP_Query( $args );
                return $nei_query;
            }
        }

        global $post;
        $nei_slug = get_post_meta( $post->ID, '_neighborhood_slug', true );
        $neighborhood = get_term_by( 'slug', $nei_slug, 'neighborhood' );
        $posts_numb = ( isset( $instance[ 'neighborhood_posts' ] ) && $instance[ 'neighborhood_posts' ] != '' ) ? $instance[ 'neighborhood_posts' ] : 3;
        $nei_cat = ( isset( $instance[ 'neighborhood_category' ] ) && $instance[ 'neighborhood_category' ] != '' ) ? $instance[ 'neighborhood_category' ] : false;
        $nei_tag = ( isset( $instance[ 'neighborhood_tag' ] ) && $instance[ 'neighborhood_tag' ] != '' ) ? $instance[ 'neighborhood_tag' ] : false;
        if ( term_exists( $nei_slug, 'neighborhood' ) ) {
            $cat = ( $nei_cat != false ) ? get_term_by( 'id', $nei_cat, 'category' ) : false;
            $tag = ( $nei_tag != false ) ? get_term_by( 'id', $nei_tag, 'post_tag' ) : false;
            $nei_tax = ( $tag == false ) ? 'category' : 'post_tag';
            $tax_id = ( $tag == false ) ? $cat->term_id : $tag->term_id;
            $nei_query = widget_query( $nei_slug, $nei_tax, $tax_id, $posts_numb );
            // if no results from neighborhood with first try taxonomy, try parent neighborhood if there is one
            if ( $nei_query->post_count == 0 && $neighborhood->parent != 0 ) {
                $parent_nei = get_term_by( 'id', $neighborhood->parent, 'neighborhood' );
                $nei_query = widget_query( $parent_nei->slug, $nei_tax, $tax_id, $posts_numb );
            // or else, if there's no parent neighborhood but there is a parent category (for a category search) try it, too
            }
            if ( $tag == false && $nei_query->post_count == 0 && $cat->parent != 0 ) {
                $parent_cat = get_term_by( 'id', $cat->parent, 'category' );
                $cat = $parent_cat;
                $nei_query = widget_query( $nei_slug, $nei_tax, $cat->term_id, $posts_numb );
                if ( $nei_query->post_count == 0 ) {
                    $nei_query = widget_query( $nei_slug, $nei_tax, $parent_cat->term_id, $posts_numb );
                }
            }
            if ( $nei_query->have_posts() ) {
                echo $args['before_widget'];
                $primary_category_name = tkno_get_primary_category( $nei_query->posts[0]->ID );
                $primary_category = get_term_by( 'name', $primary_category_name->name, 'category' );
                $class_cat = ( $tag == false ) ? tkno_get_top_category_slug( true, $cat->term_id ) : $primary_category->slug;
                $cat_class = ( $tag == false ) ? $cat : $tag; ?>
                <div class="neighborhood_widget_inner">
                    <h4 class="widget-title category-<?php echo $class_cat; ?>"><a href="<?php echo get_category_link( $cat_class->term_id ); ?>"><?php echo $cat_class->name; ?></a></h4>
                    <ul>
                    <?php while ( $nei_query->have_posts() ) : $nei_query->the_post(); ?>
                        <li class="clearfix"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                    <?php endwhile; ?>
                    </ul>
                </div>
                <?php echo $args['after_widget'];
            }
            wp_reset_postdata();
            wp_reset_query();
        }
    }
}

function register_neighborhood_related_widget() { register_widget('neighborhood_related_widget'); }
add_action( 'widgets_init', 'register_neighborhood_related_widget' );

// determine the topmost parent of a term
function get_term_topmost_parent( $term_id, $taxonomy ){
    // start from the current term
    $parent = get_term_by( 'id', $term_id, $taxonomy );
    // climb up the hierarchy until we reach a term with parent = '0'
    while ( $parent->parent != '0' ){
        $term_id = $parent->parent;
        $parent = get_term_by( 'id', $term_id, $taxonomy );
    }
    return $parent;
}

// Inserts a WalkScore widget based on the neighborhood name
class neighborhood_walkscore_widget extends WP_Widget
{
    public function __construct()
    {
            parent::__construct(
                'neighborhood_walkscore_widget',
                __('Real Estate Walkscore widget', 'neighborhood_walkscore_widget'),
                array('description' => __('Displays a Walkscore widget in the lower sidebar (only works on Neighborhood pages).', 'neighborhood_walkscore_widget'), )
            );
    }

    public function widget($args, $instance)
    {
        if ( is_post_type_archive( 'neighborhoods' ) || ( is_single() && get_post_type() == 'neighborhoods' ) ) {
            // It's a listing search and display widget
            global $post;
            $locality = $neighborhood = '';
            $neighborhood_slug = get_post_meta( $post->ID, '_neighborhood_slug', true );
            $neighborhood_child = get_term_by( 'slug', $neighborhood_slug, 'neighborhood' );
            $neighborhood_parent = get_term_topmost_parent( $neighborhood_child->term_id, $neighborhood_child->taxonomy );
            if ( $neighborhood_child->slug == $neighborhood_parent->slug ) {
                $locality = $neighborhood_child->name;
            } else {
                $locality = $neighborhood_parent->name;
                $neighborhood = $neighborhood_child->name;
                $neighborhood_meta = get_option( "neighborhood_$neighborhood_child->term_id" );
                $neighborhood_pretty = $neighborhood_meta[ 'pretty_name_field' ];
                $neighborhood = ( isset( $neighborhood_pretty ) ) ? $neighborhood_pretty : $neighborhood;
            }
            echo $args['before_widget']; ?>
            <div class="neighborhood-map-widget-wrapper">
                <script type='text/javascript'>
                    var ws_wsid = 'gb3da441cec0347ccbb53ba059a320338';
                    var ws_address = '<?php echo $neighborhood; ?>, <?php echo $locality; ?>, CO';
                    var ws_format = 'square';
                    var ws_width = '100%';
                    var ws_height = '400';
                    </script>
                <style type='text/css'>#ws-walkscore-tile{position:relative;text-align:left}#ws-walkscore-tile *{float:none;}</style>
                <div id="ws-walkscore-tile"></div>
                <script src="https://www.walkscore.com/tile/show-walkscore-tile.php"></script>
            </div>
            <?php echo $args['after_widget'];
        }
    }
}
function register_neighborhood_walkscore_widget() { register_widget('neighborhood_walkscore_widget'); }
add_action( 'widgets_init', 'register_neighborhood_walkscore_widget' );

// Inserts a Placester listings widget based on the neighborhood name
class neighborhood_listings_widget extends WP_Widget
{
    public function __construct()
    {
            parent::__construct(
                'neighborhood_listings_widget',
                __('Real Estate Listings widget', 'neighborhood_listings_widget'),
                array('description' => __('Displays a Placester listings widget in the sidebar (only works on Neighborhood pages).', 'neighborhood_listings_widget'), )
            );
    }

    public function widget($args, $instance)
    {
        if ( is_post_type_archive( 'neighborhoods' ) || ( is_single() && get_post_type() == 'neighborhoods' ) ) {
            // It's a listing search and display widget
            global $post;
            $locality = $neighborhood = '';
            $neighborhood_slug = get_post_meta( $post->ID, '_neighborhood_slug', true );
            $neighborhood_child = get_term_by( 'slug', $neighborhood_slug, 'neighborhood' );
            $neighborhood_parent = get_term_topmost_parent( $neighborhood_child->term_id, $neighborhood_child->taxonomy );
            if ( $neighborhood_child->slug == $neighborhood_parent->slug ) {
                $locality = $neighborhood_child->name;
            } else {
                $locality = $neighborhood_parent->name;
                $neighborhood = $neighborhood_child->name;
                $neighborhood_meta = get_option( "neighborhood_$neighborhood_child->term_id" );
                $neighborhood_pretty = $neighborhood_meta[ 'pretty_name_field' ];
                $neighborhood = ( isset( $neighborhood_pretty ) ) ? $neighborhood_pretty : $neighborhood;
            }
            echo '
                <script>
                    (function(window,document,url,funcName,a,m) {
                     window.plsWidgetPendingObj = funcName;
                     window.plsWidgetLoadBase = url;
                     window[funcName] = window[funcName] || [],
                     
                     a = document.createElement(\'script\'),
                     m = document.getElementsByTagName(\'script\')[0];
                     a.async = 1;
                     a.src = \'//\' + url + \'/api/widgets/\';
                     m.parentNode.insertBefore(a,m);
                    })(window,document,\'realestate.denverpost.com\',\'plsWidgets\');
                </script>
                <div id="listing_widget"></div>
                <script type="text/javascript">
                     plsWidgets.push([\'ListingSearch\',
                {"domId":"listing_widget","title":"Search Local Listings","use_search_form":"1","use_links":"","use_listings":"1","use_for_sale":"1","use_rentals":"1","use_open_house":"1","for_sale_url":"","rentals_url":"","open_house_url":"","placeholder":"Enter City, Zip, Amenity...","col_0_title":"","col_0_link_0_text":"","col_0_link_0_url":"","col_0_link_1_text":"","col_0_link_1_url":"","col_0_link_2_text":"","col_0_link_2_url":"","col_0_link_3_text":"","col_0_link_3_url":"","col_0_link_4_text":"","col_0_link_4_url":"","listings_title":"","searchParams":{"text_search":"","search_num_results":"3","min_beds":"","min_baths":"","min_price":"","max_price":"","locality":"' . $locality . '","neighborhood":"' . $neighborhood . '","zip":"","region":"","min_sqft":"","max_sqft":""}}
                     ]);
                </script>';
        }
    }
}
function register_neighborhood_listings_widget() { register_widget('neighborhood_listings_widget'); }
add_action( 'widgets_init', 'register_neighborhood_listings_widget' );

/**
 *
 * Let's make a widget that can display a GeoJSON file for a given
 * neighborhood, if it exists, and if the Leaflet Map plugin exists.
 *
**/

class neighborhood_map_widget extends WP_Widget
{
    public function __construct()
    {
            parent::__construct(
                'neighborhood_map_widget',
                __('Neighborhood map widget', 'neighborhood_map_widget'),
                array('description' => __('Displays a boundary map widget in the sidebar (only works on Neighborhood pages).', 'neighborhood_map_widget'), )
            );
    }

    public function widget($args, $instance)
    {
        if ( class_exists( 'Leaflet_Map' ) && ( is_post_type_archive( 'neighborhoods' ) || ( is_single() && get_post_type() == 'neighborhoods' ) ) ) {
            global $post;
            $neighborhood_slug = get_post_meta( $post->ID, '_neighborhood_slug', true );
            $neighborhood_child = get_term_by( 'slug', $neighborhood_slug, 'neighborhood' );
            $if_children = get_terms( $neighborhood_child->taxonomy, array(
                'parent'    => $neighborhood_child->term_id,
                'hide_empty' => false
                ) );
            $map_shape_file = get_stylesheet_directory() . '/geojson/' . $neighborhood_slug . '.json';
            $map_shape_file_url = get_stylesheet_directory_uri() . '/geojson/' . $neighborhood_slug . '.json';
            if ( file_exists( $map_shape_file ) && ! $if_children ) {
                echo $args['before_widget']; ?>
                <div class="neighborhood-map-widget-wrapper">
                    <div class="neighborhood-map-widget">
                        <div class="map-expander"></div>
                        <?php echo do_shortcode('[leaflet-map]'); ?>
                        <?php echo do_shortcode('[leaflet-geojson src="' . $map_shape_file_url . '" fitbounds=1] '); ?>
                    </div>
                </div>
            <?php echo $args['after_widget'];
            }
        }
    }
}
function register_neighborhood_map_widget() { register_widget('neighborhood_map_widget'); }
add_action( 'widgets_init', 'register_neighborhood_map_widget' );

/**
 *
 * Let's make a widget that can display a neighborhood Great Schools widget embed
 *
**/
class neighborhood_schools_widget extends WP_Widget
{
    public function __construct()
    {
            parent::__construct(
                'neighborhood_schools_widget',
                __('Neighborhood schools widget', 'neighborhood_schools_widget'),
                array('description' => __('Displays a boundary map widget in the sidebar (only works on Neighborhood pages).', 'neighborhood_schools_widget'), )
            );
    }

    public function widget($args, $instance)
    {
        echo $args['before_widget']; ?>
        <div class="neighborhood-map-widget-wrapper">
            <iframe className="greatschools" src="//www.greatschools.org/widget/map?searchQuery=&textColor=0066B8&borderColor=CCCCCC&cityName=Denver&state=CO&normalizedAddress=Denver%2C%20CO&height=320&width=320&zoom=12" width="100%" height="320" marginHeight="0" marginWidth="0" frameBorder="0" scrolling="no"></iframe><script type="text/javascript">var _gsreq = new XMLHttpRequest();var _gsid = new Date().getTime();_gsreq.open("GET", "https://www.google-analytics.com/collect?v=1&tid=UA-54676320-1&cid="+_gsid+"&t=event&ec=widget&ea=loaded&el="+window.location.hostname+"&cs=widget&cm=web&cn=widget&cm1=1&ni=1");_gsreq.send();</script>
        </div>
        <?php echo $args['after_widget'];
    }
}
function register_neighborhood_schools_widget() { register_widget('neighborhood_schools_widget'); }
add_action( 'widgets_init', 'register_neighborhood_schools_widget' );

/**
 *
 * A new meta box for demographic information on neighborhood pages
 *
**/

/* Neighborhoods Meta box setup function. */
function neighborhood_page_demographic_metabox_setup() {
    add_action( 'add_meta_boxes', 'neighborhood_page_demographic_add_metabox' );
    add_action( 'save_post', 'neighborhood_page_demographic_save_post_meta', 10, 2 );
}
add_action( 'load-post.php', 'neighborhood_page_demographic_metabox_setup' );
add_action( 'load-post-new.php', 'neighborhood_page_demographic_metabox_setup' );

/* Create one or more meta boxes to be displayed on the post editor screen. */
function neighborhood_page_demographic_add_metabox() {
    add_meta_box(
        'neighborhood_demographics',
        esc_html__( 'Neighborhood Demographics', 'example' ),
        'neighborhood_page_demographic_metabox',
        'neighborhoods',
        'side',
        'default'
    );
}

/* Display the post meta box. */
function neighborhood_page_demographic_metabox( $post ) { ?>
    <?php wp_nonce_field( basename( __FILE__ ), 'neighborhood_demographics_meta_nonce' );
    /* add each field to this form to collect them -- don't forget to add them to the array & widget below! */
    ?>

    <p>
    <label for="_neighborhood_median_age"><?php _e( "Median age:", '' ); ?></label>
    <br />
    <input class="widefat" type="text" name="_neighborhood_median_age" id="_neighborhood_median_age" value="<?php echo esc_attr( get_post_meta( $post->ID, '_neighborhood_median_age', true ) ); ?>" size="30" />
    </p>
    <p>
    <label for="_neighborhood_rent_vs_own"><?php _e( "Rent vs. Own percentage:", '' ); ?></label>
    <br />
    <input class="widefat" type="text" name="_neighborhood_rent_vs_own" id="_neighborhood_rent_vs_own" value="<?php echo esc_attr( get_post_meta( $post->ID, '_neighborhood_rent_vs_own', true ) ); ?>" size="30" />
    </p>
    <p>
    <label for="_neighborhood_married_vs_single"><?php _e( "Married vs. Single percentage:", '' ); ?></label>
    <br />
    <input class="widefat" type="text" name="_neighborhood_married_vs_single" id="_neighborhood_married_vs_single" value="<?php echo esc_attr( get_post_meta( $post->ID, '_neighborhood_married_vs_single', true ) ); ?>" size="30" />
    </p>
    <p>
    <label for="_neighborhood_kids_vs_none"><?php _e( "Kids vs. No Kids percentage:", '' ); ?></label>
    <br />
    <input class="widefat" type="text" name="_neighborhood_kids_vs_none" id="_neighborhood_kids_vs_none" value="<?php echo esc_attr( get_post_meta( $post->ID, '_neighborhood_kids_vs_none', true ) ); ?>" size="30" />
    </p>

<?php }

/* Save the neighborhoods meta box's post metadata. */
function neighborhood_page_demographic_save_post_meta( $post_id, $post ) {

    /* Verify the nonce before proceeding. */
    if ( !isset( $_POST['neighborhood_demographics_meta_nonce'] ) || !wp_verify_nonce( $_POST['neighborhood_demographics_meta_nonce'], basename( __FILE__ ) ) )
        return $post_id;

    /* Get the post type object. */
    $post_type = get_post_type_object( $post->post_type );

    /* Check if the current user has permission to edit the post. */
    if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
        return $post_id;

    /* add each field to this array to save them -- don't forget to add them to the widget below! */
    $meta_save_fields = array('_neighborhood_median_age','_neighborhood_rent_vs_own','_neighborhood_married_vs_single','_neighborhood_kids_vs_none');
    foreach ($meta_save_fields as $meta_save_field) {
        $new_meta_value = ( isset( $_POST[$meta_save_field] ) ) ? sanitize_text_field( $_POST[$meta_save_field] ) : '';
        $meta_key = $meta_save_field;
        $meta_value = get_post_meta( $post_id, $meta_key, true );
        if ( $new_meta_value && '' == $meta_value )
            add_post_meta( $post_id, $meta_key, $new_meta_value, true );
        elseif ( $new_meta_value && $new_meta_value != $meta_value )
            update_post_meta( $post_id, $meta_key, $new_meta_value );
        elseif ( '' == $new_meta_value && $meta_value )
            delete_post_meta( $post_id, $meta_key, $meta_value );
    }
}

/**
 *
 * A widget that displays the array of demographic information
 * attached to a neighborhood by way of the metabox above.
 *
**/

class neighborhood_demographics_widget extends WP_Widget
{
    public function __construct()
    {
            parent::__construct(
                'neighborhood_demographics_widget',
                __('Neighborhood demographics widget', 'neighborhood_demographics_widget'),
                array('description' => __('Displays a demographic information widget in the sidebar (only works on Neighborhood pages).', 'neighborhood_demographics_widget'), )
            );
    }

    public function widget($args, $instance)
    {
        if ( is_post_type_archive( 'neighborhoods' ) || ( is_single() && get_post_type() == 'neighborhoods' ) ) {

            global $post;
            $neighborhood_median_age = get_post_meta( $post->ID, '_neighborhood_median_age', true );
            $neighborhood_rent_vs_own = get_post_meta( $post->ID, '_neighborhood_rent_vs_own', true );
            $neighborhood_married_vs_single = get_post_meta( $post->ID, '_neighborhood_married_vs_single', true );
            $neighborhood_kids_vs_none = get_post_meta( $post->ID, '_neighborhood_kids_vs_none', true );

            if ( $neighborhood_median_age || $neighborhood_rent_vs_own || $neighborhood_married_vs_single || $neighborhood_kids_vs_none ) {
                echo $args['before_widget']; ?>
                <div class="neighborhood-map-widget-wrapper demo_widget">
                    <h4 class="widget-title"><a class="noclick" href="javascript:void(0);">About the neighborhood</a></h4>
                    <ul class="">
                        <?php echo ( $neighborhood_median_age ) ? '<li><strong>Median age:</strong> ' . $neighborhood_median_age . '</li>' : ''; ?>
                        <?php echo ( $neighborhood_rent_vs_own ) ? '<li><strong>Rent vs. own %:</strong> ' . $neighborhood_rent_vs_own . '</li>' : ''; ?>
                        <?php echo ( $neighborhood_married_vs_single ) ? '<li><strong>Married vs. single %:</strong> ' . $neighborhood_married_vs_single . '</li>' : ''; ?>
                        <?php echo ( $neighborhood_kids_vs_none ) ? '<li><strong>Kids vs no kids %:</strong> ' . $neighborhood_kids_vs_none . '</li>' : ''; ?>
                    </ul>
                </div>
            <?php echo $args['after_widget'];
            }
        }
    }
}
function register_neighborhood_demographics_widget() { register_widget('neighborhood_demographics_widget'); }
add_action( 'widgets_init', 'register_neighborhood_demographics_widget' );