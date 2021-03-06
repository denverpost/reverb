<?php
/**
 * The template for displaying location archive pages
 *
 * @package Reactor
 * @subpackge Templates
 * @since 1.0.0
 */


function location_search_join( $join ) {
    global $wpdb;
    return $join .=' LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
}
add_filter('posts_join', 'location_search_join' );
function location_search_where( $where ) {
    global $pagenow, $wpdb;

    return preg_replace(
            "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
            "(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_value LIKE $1)", $where );
}
add_filter( 'posts_where', 'location_search_where' );
function location_search_distinct( $where ) {
    global $wpdb;
    return "DISTINCT";
}
add_filter( 'posts_distinct', 'location_search_distinct' );

add_action( 'pre_get_posts', function( $q ) {
    if( $title = $q->get( '_meta_or_title' ) ) {
        add_filter( 'get_meta_sql', function( $sql ) use ( $title ) {
            global $wpdb;
            // Only run once:
            static $nr = 0; 
            if( 0 != $nr++ ) return $sql;
            // Modified WHERE
            $sql['where'] = sprintf(
                " AND ( %s OR %s ) ",
                $wpdb->prepare( "{$wpdb->posts}.post_title like '%%%s%%'", $title),
                mb_substr( $sql['where'], 5, mb_strlen( $sql['where'] ) )
            );
            return $sql;
        });
    }
});

//Check if this is a search-form submission
$locationsearch = ( isset( $_GET[ 'locationsearch' ] ) && $_GET[ 'locationsearch' ] == 'Y' ) ? true : false;
$user_radius =  false;
//If it is a search, grab the form variables
if ( $locationsearch ) {
    $user_ZIP = ( isset( $_GET[ 'user_ZIP' ] ) ) ? $_GET[ 'user_ZIP' ] : FALSE;
    $user_radius = ( isset( $_GET[ 'user_radius' ] ) ) ? $_GET[ 'user_radius' ] : FALSE;
    $user_text = ( isset( $_GET[ 'user_text' ] ) ) ? $_GET[ 'user_text' ] : FALSE;

    //Check that user ZIP code is a 5-digit number between 10001 and 99999. If not, display error message.
    if( $user_ZIP ) {
        if( ( 99999 < $user_ZIP || $user_ZIP < 10001 ) || ! is_numeric( $user_ZIP ) ) {
            $location_error = '<div>You did not enter a valid 5-digit ZIP code, so we do not know your location.</div>';
            unset( $user_ZIP );
            $locationsearch = false;
        } else {
            //Get user lat/long from ZIP
            $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key=AIzaSyDFesAMjYEKk6hCIxnQ_3SIwJ6rImbSch8&address='.$user_ZIP.'&sensor=false');
            $output= json_decode($geocode);
            $lat = $output->results[0]->geometry->location->lat;
            $lng = $output->results[0]->geometry->location->lng;
        }
    } elseif ( ! $user_text ) {
        $locationsearch = false;
    }
}

?>

<?php get_header(); ?>

	<div id="primary" class="site-content">
    
    	<?php reactor_content_before(); ?>
    
        <div id="content" role="main">
        	<div class="row">
                <div class="<?php reactor_columns(); ?>">

                <?php
 
                    if( $locationsearch ) {
                        global $wp_query;
                        // WP_Query arguments
                        $args = array (
                            'post_type'              => 'location',
                            'post_status'            => 'published',
                            'posts_per_page'         => 5000,
                            'order'                  => 'ASC',
                            'orderby'                => 'title',
                            'meta_query'             => array(),
                            'adp_disable'            => true,
                        );
                        if( $user_text ) {
                            $args['s'] = $user_text;
                        }
                        //If distance is a factor, add the meta_query to $args
                        if( $user_ZIP ) {
                            //Add filter to compare Locations to user location and radius
                            add_filter( 'posts_where' , 'location_posts_where' );
                        }
                        // The Query
                        $wp_query = new WP_Query( $args );
                        // Remove the filter after executing the query
                        remove_filter( 'posts_where' , 'location_posts_where' );

                    } else {
                        global $wp_query;
                        $number_posts = 25;
        
                        $args = array(
                            'post_type' => 'location',
                            'posts_per_page' => $number_posts,
                            'paged' => get_query_var( 'paged' ),
                            'adp_disable'       => true
                            );

                        $wp_query = new WP_Query( $args );
                    } ?>

                            <header class="page-header">
                                <h1 class="archive-title location-header"><span>Locations</span></h1>
                            </header>

                        <div class="location-search">
                            <div class="neighborhood-map-form">
                                <div class="map-expander"></div>
                                <?php echo do_shortcode('[leaflet-map]'); ?>
                            </div>
                            <form method="get" action="<?php echo get_site_url(); ?>/location/">
                                <input type="hidden" name="locationsearch" value="Y" />
                                <div class="row">
                                    <div class="large-12 columns">
                                        <h2>Find places to go</h2>
                                    </div>
                                    <div class="large-4 columns">
                                        <label>What are you looking for?</label>
                                        <input type="text" name="user_text" id="user_text" value="<?php echo ( isset ($user_text) ) ? $user_text : ''; ?>" />
                                    </div>
                                    <div class="large-2 columns">
                                        <label>Near ZIP</label>
                                        <input type="text" pattern=".{5}" name="user_ZIP" id="user_ZIP" value="<?php echo ( isset ($user_ZIP) ) ? $user_ZIP : ''; ?>" />
                                    </div>
                                    <div class="large-2 columns">
                                        <label>Distance</label>
                                        <select name="user_radius" id="user_radius">
                                            <option<?php echo ( $user_radius == 25000 ) ? ' selected="selected"' : ''; ?> value="25000">Any</option>
                                            <option<?php echo ( $user_radius == 5 ) ? ' selected="selected"' : ''; ?> value="5">5 miles</option>
                                            <option<?php echo ( $user_radius == 10 || ! $user_radius ) ? ' selected="selected"' : ''; ?> value="10">10 miles</option>
                                            <option<?php echo ( $user_radius == 20 ) ? ' selected="selected"' : ''; ?> value="20">20 miles</option>
                                            <option<?php echo ( $user_radius == 50 ) ? ' selected="selected"' : ''; ?> value="50">50 miles</option>
                                            <option<?php echo ( $user_radius == 100 ) ? ' selected="selected"' : ''; ?> value="100">100 miles</option>
                                        </select>
                                    </div>
                                    <div class="large-4 columns">
                                        <input class="button" type="submit" value="Find locations" style="margin-top:7px;" />
                                        <a class="button warning" href="<?php echo get_site_url(); ?>/location/" style="margin-top:7px;font-size:200%;padding:.25em .5em .6em;line-height:.78;">&times;</a>
                                    </div>
                                </div>
                            </form>
                        </div>

                    <?php if ( $wp_query->have_posts() ) : ?>

                        <?php reactor_loop_before(); ?>

                        <?php if ( $locationsearch ) { ?>
                            <h4 class="location-archive">Search results...</h4>
                        <?php } else { ?>
                            <h4 class="location-archive">Recently added locations:</h4>
                        <?php } ?>
                        
                        <?php $address_list = array();
                        while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
                            
                            <?php reactor_post_before(); ?>

                            <?php get_template_part('post-formats/format', 'location'); ?>

                            <?php 
                                $latitude = get_post_meta($post->ID, '_location_latitude', true);
                                $longitude = get_post_meta($post->ID, '_location_longitude', true);
                                $address = get_post_meta($post->ID, '_location_street_address', true);
                                $address_list[] = $address;
                                
                                $medium_img_url = ( has_post_thumbnail() ) ? wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium') : false;
                                
                                $img_div = ( $medium_img_url && strlen( $medium_img_url[0]) >= 1) ? '<div class="cat-thumbnail"><div class="cat-imgholder"></div><a href="' . get_the_permalink() . '"><div class="cat-img" style="background-image:url(\\\'' . $medium_img_url[0] . '\\\');"></div></a></div>' : '';

                                $loc_map_icon = get_post_meta( $post->ID, '_loc_map_icon', true );
                                $marker_icon = ( isset( $loc_map_icon ) ) ? get_marker_icon($loc_map_icon) : '';

                                echo do_shortcode('[leaflet-marker lat=' . $latitude . ' lng=' . $longitude . $marker_icon . ']<h3><a href="' . get_the_permalink() . '">' . addslashes( get_the_title() ) . '</a></h3><p>' . $address . '</p>' . $img_div . '[/leaflet-marker]'); ?>

                            <?php reactor_post_after(); ?>

                        <?php endwhile; // end of the post loop 
                        $addresses = implode( ';', $address_list );
                        echo do_shortcode( '[leaflet-line addresses="' . $addresses . '" fitbounds=true stroke=false]' );
                        ?>

                    <?php elseif ( $locationsearch ): ?>

                        <h3>We don't have anything that close to your ZIP code yet. Check back soon!</h3>
                    
                    <?php endif; ?>
                        
                    <?php reactor_loop_after(); ?>
                
                <?php reactor_inner_content_after(); ?>
                
                </div><!-- .columns -->
                
                <?php get_sidebar('outdoors'); ?>
                
            </div><!-- .row -->
        </div><!-- #content -->
        
        <?php reactor_content_after(); ?>
        
	</div><!-- #primary -->

<?php get_footer(); ?>