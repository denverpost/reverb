<?php

// Get a neighborhood when the custom field matches a neighborhood taxonomy slug
function tkno_get_neighborhood_from_slug($neighborhood_slug) {
    $args = array(
        'post_type'     => 'neighborhoods',
        'meta_query'    => array(
            array(
                'key'   => '_neighborhood_slug',
                'value' => $neighborhood_slug,
                'compare' => 'LIKE',
                'adp_disable' => true
                )
            ),
        'posts_limits'    => 1
        );
    $query = new WP_Query( $args );
    $neighborhoods = $query->get_posts();
    $neighborhood = ( count($neighborhoods) > 0 ) ? $neighborhoods[0] : false;
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
 * neighborhood_related_by_category_widget
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
        $defaults = array( 'neighorhood_category' => __( '' ), 'neighorhood_tag' => __( '' ), 'neighorhood_posts' => __( '3' ) );
        $instance = wp_parse_args( ( array ) $instance, $defaults ); ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'neighorhood_posts' ); ?>"><?php _e( 'Number of posts to display:' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'neighorhood_posts' ); ?>" name="<?php echo $this->get_field_name( 'neighorhood_posts' ); ?>" type="text" value="<?php echo $instance[ 'neighorhood_posts' ]; ?>" />
        </p>
        <p>
        <label for="<?php echo $this->get_field_id( 'neighorhood_category' ); ?>"><?php _e( 'Category for related articles (falls back to parent if no posts found in a child category):' ); ?></label> 
        <select id="<?php echo $this->get_field_id( 'neighorhood_category' ); ?>" name="<?php echo $this->get_field_name( 'neighorhood_category' ); ?>" class="widefat" style="width:100%;">
        <option <?php echo ( $instance[ 'neighorhood_category' ] == '' ) ? 'selected="selected" ' : ''; ?> value="">&nbsp;</option>
            <?php foreach( get_terms( 'category' ) as $term) { ?>
            <option <?php selected( $instance[ 'neighorhood_category' ], $term->term_id ); ?> value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
            <?php } ?>      
        </select>
        </p>
        <p>
        <label for="<?php echo $this->get_field_id( 'neighorhood_tag' ); ?>"><?php _e( 'Or tag (overrides category, if set):' ); ?></label> 
        <select id="<?php echo $this->get_field_id( 'neighorhood_tag' ); ?>" name="<?php echo $this->get_field_name( 'neighorhood_tag' ); ?>" class="widefat" style="width:100%;">
            <option <?php echo ( $instance[ 'neighorhood_tag' ] == '' ) ? 'selected="selected" ' : ''; ?> value="">&nbsp;</option>
            <?php foreach( get_terms( 'post_tag' ) as $term) { ?>
            <option <?php selected( $instance[ 'neighorhood_tag' ], $term->term_id ); ?> value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
            <?php } ?>      
        </select>
        </p>

    <?php }

    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance[ 'neighorhood_category' ] = ( ! empty( $new_instance[ 'neighorhood_category' ] ) ) ? trim( strip_tags( $new_instance[ 'neighorhood_category' ] ) ) : '';
        $instance[ 'neighorhood_tag' ] = ( ! empty( $new_instance[ 'neighorhood_tag' ] ) ) ? trim( strip_tags( $new_instance[ 'neighorhood_tag' ] ) ) : '';
        $instance[ 'neighorhood_posts' ] = ( ! empty( $new_instance[ 'neighorhood_posts' ] ) ) ? (int)trim( strip_tags( $new_instance[ 'neighorhood_posts' ] ) ) : 3;
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
        $posts_numb = ( $instance[ 'neighorhood_posts' ] != '' ) ? $instance[ 'neighorhood_posts' ] : 3;
        $nei_cat = ( $instance[ 'neighorhood_category' ] != '' ) ? $instance[ 'neighorhood_category' ] : false;
        $nei_tag = ( $instance[ 'neighorhood_tag' ] != '' ) ? $instance[ 'neighorhood_tag' ] : false;
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
                $nei_query = widget_query( $nei_slug, $nei_tax, $parent_cat->term_id, $posts_numb );
                if ( $nei_query->post_count == 0 ) {
                    $nei_query = widget_query( $parent_nei->slug, $nei_tax, $parent_cat->term_id, $posts_numb );
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