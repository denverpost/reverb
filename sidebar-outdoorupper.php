<?php
/**
 * The sidebar template containing the front page widget area
 *
 * @package Reactor
 * @subpackge Templates
 * @since 1.0.0
 */
?>

	<?php // get the front page layout

    wp_reset_postdata();

    function shuffle_from_recent( $posts, $query ) {
        if( $pick = $query->get( '_location_posts_where' ) ) {
            shuffle( $posts );
            $posts = array_slice( $posts, 0, (int) $pick );
        }
        return $posts;
    }
    add_filter( 'the_posts', 'shuffle_from_recent', 10, 2 );
    $current_cat = get_query_var('cat');
    $args = array( 
        'post_type'           => 'location',
        'order_by'            => 'post_date',
        'posts_per_page'      => 20,
        '_location_posts_where' => 5
        );
    $outdoormap_query = new WP_Query( $args );
    if ( $outdoormap_query->have_posts() ) : ?>


    <h2 class="location-recent">Recently featured places to go</h2>
    <div class="neighborhood-map-form" style="margin-bottom:0;">
        <div class="map-expander"></div>
        <?php echo do_shortcode( '[leaflet-map zoomcontrol="1"]' );        

        $map_display = '';
        while ( $outdoormap_query->have_posts() ) : $outdoormap_query->the_post();
            $address = get_post_meta( $post->ID, '_location_street_address', true );
            $latitude = get_post_meta( $post->ID, '_location_latitude', true );
            $longitude = get_post_meta( $post->ID, '_location_longitude', true );
            if ( $address && $latitude && $longitude ) {
                $medium_img_url = ( $post->ID ) ? wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium') : false;
                $img_div = ( $medium_img_url && strlen( $medium_img_url[0] ) >= 1 ) ? '<div class="cat-thumbnail"><div class="cat-imgholder"></div><a href="' . get_permalink( $post->ID ) . '"><div class="cat-img" style="background-image:url(\\\'' . $medium_img_url[0] . '\\\');"></div></a></div>' : '';
                $map_display .= do_shortcode('[leaflet-marker zoom=11 lat=' . $latitude . ' lng=' . $longitude . ']<h3><a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a></h3><p>' . $address . '</p>' . $img_div . '[/leaflet-marker]' );
            }
        endwhile; // end of the loop
        remove_filter( 'the_posts' , '_location_posts_where' );
        echo $map_display;
        wp_reset_postdata(); ?>

    </div>
    <div class="location-search-wrap">
        <form method="get" action="<?php echo get_site_url(); ?>/location/">
            <input type="hidden" name="locationsearch" value="Y" />
            <div class="row">
                <div class="large-4 columns">
                    <label>What are you looking for?</label>
                    <input type="text" name="user_text" id="user_text" value="" />
                </div>
                <div class="large-2 columns">
                    <label>Near ZIP</label>
                    <input type="text" pattern=".{5}" name="user_ZIP" id="user_ZIP" value="" />
                </div>
                <div class="large-2 columns">
                    <label>Distance</label>
                    <select name="user_radius" id="user_radius">
                        <option value="25000">Any</option>
                        <option value="5">5 miles</option>
                        <option selected="selected" value="10">10 miles</option>
                        <option value="20">20 miles</option>
                        <option value="50">50 miles</option>
                        <option value="100">100 miles</option>
                    </select>
                </div>
                <div class="large-4 columns">
                    <input class="button" type="submit" value="Find locations" style="margin-top:7px;height:52px;" />
                    <a class="button warning" href="<?php echo get_site_url(); ?>/location/" style="margin-top:7px;font-size:200%;padding:.36em .5em .55em;line-height:.5;height:53px;">&times;</a>
                </div>
            </div>
        </form>
    </div>

    <?php endif; // end have_posts() check

    set_query_var("cat",$current_cat);
    
    // if front page has two sidebars and second sidear is active
    if ( is_active_sidebar('sidebar-outdoorupper') ) : ?>
    
    <?php reactor_sidebar_before(); ?>

        <div id="sidebar-outdooruppper" class="sidebar" role="complementary">
            <?php dynamic_sidebar('sidebar-outdoorupper'); ?>
        </div><!-- #sidebar-frontpage-2 -->
    
    <?php reactor_sidebar_after(); ?>

    <div class="clear"></div>
    
    <?php endif; ?>