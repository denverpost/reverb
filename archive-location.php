<?php
/**
 * The template for displaying location archive pages
 *
 * @package Reactor
 * @subpackge Templates
 * @since 1.0.0
 */


//Check if this is a search-form submission
$locationsearch = ( isset( $_GET[ 'locationsearch' ] ) && $_GET[ 'locationsearch' ] == 'Y' ) ? true : false;

//If it is a search, grab the form variables
if ( $locationsearch ) {
    $user_ZIP = $_GET[ 'user_ZIP' ];
    $user_radius = $_GET[ 'user_radius' ];

    //Check that user ZIP code is a 5-digit number between 10001 and 99999. If not, display error message.
    if( $user_ZIP ) {
        if( ( 99999 < $user_ZIP || $user_ZIP < 10001 ) || ! is_numeric( $user_ZIP ) ) {
            $location_error = '<div>You did not enter a valid 5-digit ZIP code, so we do not know your location.</div>';
            unset( $user_ZIP );
            $locationsearch = false;
        } else {
            //Get user lat/long from ZIP
            $geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$user_ZIP.'&sensor=false');
            $output= json_decode($geocode);
            $lat = $output->results[0]->geometry->location->lat;
            $lng = $output->results[0]->geometry->location->lng;
        }
    } else {
        $locationsearch = false;
    }
}


//Generate table from database

/*
//Begin building results table
$form .= '<table><thead>
<tr><th>Name</th><th>Address</th><th>City</th><th>State</th>';

if($user_ZIP) {$form .= '<th>Miles</th>';}
$form .= '</tr></thead><tbody>';

global $post;
while ($custom_posts->have_posts()) : $custom_posts->the_post();
    $title = get_the_title();
    $street = get_post_meta($post->ID, 'location-street-address', true);
    $city = get_post_meta($post->ID, 'location-city', true);
    $state = get_post_meta($post->ID, 'location-state', true);

    //If street address exists, make it a link to Google Maps
    if($street) {
        $streetplain = $street;
        $mapquery = str_replace(' ','+',$titletext).'+';
        $mapquery = str_replace('UCC','',$mapquery);
        $mapquery .= str_replace(' ','+',$street).'+';
        $mapquery .= str_replace('','+',$city).'+'.$state;
        $street = '<a target="_blank" href="https://www.google.com/maps/search/'.$mapquery.'/">'.$street.'</a>';      
    }

    $form .= '<tr><td>'.$title.'</td><td>'.$street.'</td><td>'.$city.'</td><td>'.$state.'</td>';

    if($locationsearch) {
        //Get location of location
        $latitude = get_post_meta($post->ID, '_location_latitude', true);
        $longitude = get_post_meta($post->ID, '_location_longitude', true);

        //Add location to the Map array
        $locations .= "['<div style=\"line-height:1.35; overflow:hidden; white-space:nowrap;\"><p>$title<br/>$streetplain<br/>$city, $state</p></div>',$latitude,$longitude],";

        if($user_ZIP) {
            //Calculate distance from user ZIP
            $distance = number_format(round(distance($lat,$lng,$latitude,$longitude),1),1);
            $form .= "<td>$distance</td>";    
        }
    }

    $form .= '</tr>';

endwhile;
wp_reset_postdata();

$form .= '</tbody></table>';

if($locationsearch) {
    //If no user location provided, use center of Minnesota to center map
    if(!$user_ZIP) {
        $lat = '45.7326';
        $lng = '-93.9196';
    }
    //Add Google Map init script
    $form .= "  <script type='text/javascript'>
        var locations = [$locations];

        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 10,
          center: new google.maps.LatLng($lat, $lng),
          mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        var infowindow = new google.maps.InfoWindow();

        var marker, i;
        var bounds = new google.maps.LatLngBounds();
        for (i = 0; i < locations.length; i++) {  
          marker = new google.maps.Marker({
            position: new google.maps.LatLng(locations[i][1], locations[i][2]),
            map: map
          });
          bounds.extend(marker.position);

          google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
              infowindow.setContent(locations[i][0]);
              infowindow.open(map, marker);
            }
          })(marker, i));
        }
        map.fitBounds(bounds);
      </script>";
} */
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
                        );

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
                            );

                        $wp_query = new WP_Query( $args );
                    } ?>

                            <header class="page-header">
                                <h1 class="archive-title location-header"><span>Locations</span></h1>
                            </header>

                        <div class="location-search">
                            <!-- <script src="http://maps.google.com/maps/api/js?key=AIzaSyA1Eh51J16b3NHRslNzCTu1BCm44lICAl8 &sensor=false"></script> -->
                            <?php echo do_shortcode('[leaflet-map]'); ?>
                            <form method="get" action="<?php echo get_site_url(); ?>/location/">
                                <input type="hidden" name="locationsearch" value="Y" />
                                <div class="row">
                                    <div class="large-12 columns">
                                        <h2>Find places to go</h2>
                                    </div>
                                    <div class="large-4 columns">
                                        <label>Start with a ZIP code</label>
                                        <input type="text" pattern=".{5}" required name="user_ZIP" id="user_ZIP" value="<?php echo ( isset ($user_ZIP) ) ? $user_ZIP : ''; ?>" />
                                    </div>
                                    <div class="large-4 columns">
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

                        <?php if ( isset( $user_radius ) || isset( $user_ZIP ) ) { ?>
                            <h4 class="location-archive">Search results...</h4>
                        <?php } else { ?>
                            <h4 class="location-archive">Recently added locations:</h4>
                        <?php } ?>
                        
                        <?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
                            
                            <?php reactor_post_before(); ?>

                            <?php get_template_part('post-formats/format', 'location'); ?>

                            <?php 
                                $latitude = get_post_meta($post->ID, '_location_latitude', true);
                                $longitude = get_post_meta($post->ID, '_location_longitude', true);
                                $address = get_post_meta($post->ID, '_location_street_address', true);

                                $medium_img_url = ( has_post_thumbnail() ) ? wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium') : false;
                                
                                $img_div = ( $medium_img_url && strlen( $medium_img_url[0]) >= 1) ? '<div class="cat-thumbnail"><div class="cat-imgholder"></div><a href="' . get_the_permalink() . '"><div class="cat-img" style="background-image:url(\\\'' . $medium_img_url[0] . '\\\');"></div></a></div>' : '';

                                echo do_shortcode('[leaflet-marker lat=' . $latitude . ' lng=' . $longitude . ']<h3><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></h3><p>' . $address . '</p>' . $img_div . '[/leaflet-marker]'); ?>

                            <?php reactor_post_after(); ?>

                        <?php endwhile; // end of the post loop ?>

                    <?php elseif ( $locationsearch ): ?>

                        <h3>We don't have anything that close to your ZIP code yet. Check back soon!</h3>
                    
                    <?php endif; ?>
                        
                    <?php reactor_loop_after(); ?>
                
                <?php reactor_inner_content_after(); ?>
                
                </div><!-- .columns -->
                
                <?php get_sidebar(); ?>
                
            </div><!-- .row -->
        </div><!-- #content -->
        
        <?php reactor_content_after(); ?>
        
	</div><!-- #primary -->

<?php get_footer(); ?>