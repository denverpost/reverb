<?php
/**
 * The template for displaying search results
 *
 * @package Reactor
 * @subpackge Templates
 * @since 1.0.0
 */
?>

 <?php
//Check if this is a search-form submission
$locationsearch = $_GET['locationsearch'];

//If it is a search, grab the form variables
if($locationsearch) {
    $user_ZIP = $_GET['user_ZIP'];
    $user_radius = $_GET['user_radius'];

    //Check that user ZIP code is a 5-digit number between 10001 and 99999. If not, display error message.
    if($user_ZIP) {
        if(99999 < $user_ZIP || $user_ZIP < 10001) {$outofrange = 'y';}
        if(!is_numeric($user_ZIP) || $outofrange == 'y') {$form .= '<div>You did not enter a valid 5-digit ZIP code, so we do not know your location.</div>';unset($user_ZIP);unset($locationsearch);}
    }
    //If it's a blank form, act like it's not a search
    if(!$user_ZIP) { unset($locationsearch);}
    //If it's still a search, load Google Map code and div
    if($locationsearch) {
        $form .= '<script src="http://maps.google.com/maps/api/js?key=AIzaSyA1Eh51J16b3NHRslNzCTu1BCm44lICAl8&sensor=false" type="text/javascript"></script>';
        $form .= '<div id="map" style="width: 100%; height: 400px; margin:20px 0;"></div>';
    }

    if($user_ZIP) {
    //Get user lat/long from ZIP
    $geocode=file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$user_ZIP.'&sensor=false');
    $output= json_decode($geocode);
    $lat = $output->results[0]->geometry->location->lat;
    $lng = $output->results[0]->geometry->location->lng;
    }
}
$homeurl = home_url();

//If a search, display "cancel search" button that takes user back to plain "find a location" page
if($locationsearch) {$form .= '<a href="'.$homeurl.'/PATH_TO_FIND_LOCATION_PAGE/">Clear search</a>';}

//Generate table from database

//If it's a search, execute the search query
if($locationsearch) {
    // WP_Query arguments
    $args = array (
        'post_type'              => 'location',
        'post_status'            => 'published',
        'posts_per_page'        =>    5000,
        'order'                  => 'ASC',
        'orderby'                => 'title',
        'meta_query'             => array(),
    );

    //If distance is a factor, add the meta_query to $args
    if($user_ZIP) {
    //Add filter to compare Locations to user location and radius
    add_filter( 'posts_where' , 'location_posts_where' );  
    }
    // The Query
    $custom_posts = new WP_Query( $args );
    // Remove the filter after executing the query
    remove_filter( 'posts_where' , 'location_posts_where' );

}
//Otherwise do the default query
else {
$custom_posts = new WP_Query('post_type=location&orderby=title&order=ASC&posts_per_page=5000');
}

//Begin building results table
$form .= '<table><thead>';
$form .= '<tr><th>Name</th><th>Address</th><th>City</th><th>State</th>';

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
        $latitude = get_post_meta($post->ID, 'location-latitude', true);
        $longitude = get_post_meta($post->ID, 'location-longitude', true);

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
}
?>



<?php get_header(); ?>

	<div id="primary" class="site-content">
    
    	<?php reactor_content_before(); ?>
    
        <div id="content" role="main">
        	<div class="row">
                <div class="<?php reactor_columns(); ?>">
                
                <?php reactor_inner_content_before(); ?>
                
			<?php if ( have_posts() ) :
				$results_total = $wp_query->found_posts;
				if ( $paged == 0 ) {
					$results_first = 1;
					$results_last = $wp_query->query_vars['posts_per_page'];
				} else {
					$results_first = 1 + ($wp_query->query_vars['posts_per_page'] * ( $paged - 1 ) );
					if ( $wp_query->post_count == $wp_query->query_vars['posts_per_page'] ) {
						$results_last = $wp_query->query_vars['posts_per_page'] * ( $paged );
					} else {
						$results_last = ( $wp_query->query_vars['posts_per_page'] * ( $paged - 1 ) ) + $wp_query->post_count;
					}
				}
				$entries = ( $wp_query->post_count == 1 ) ? 'entry' : 'entries';
				?>
			
				<?php reactor_loop_before(); ?>
				
                	        <header class="page-header">
                        	<h1 class="page-title"><span class="searchresults">Showing <?php echo $entries; ?> <strong><?php echo $results_first; ?></strong> <?php if ( $wp_query->post_count != 1 && $paged != 1 ): ?> to <strong><?php echo $results_last; ?></strong> <?php endif; ?>(of <?php echo $results_total; ?>) for</span> <?php echo get_search_query(); ?></h1>
                    		</header> 

				<?php // start the loop
				while ( have_posts() ) : the_post(); ?>
				
					<?php // get post format and display template for that format
					if ( !get_post_format() ) : get_template_part('post-formats/format', 'catpage');
					else : get_template_part('post-formats/catpage', get_post_format()); endif; ?>
					
				<?php endwhile; ?>
				
				<?php reactor_loop_after(); ?>
				
				<?php // if no posts are found
				else : reactor_loop_else(); ?>
				
			<?php endif; // end have_posts() check ?> 
                
                <?php reactor_inner_content_after(); ?>
                
                </div><!-- .columns -->
                
                <?php get_sidebar(); ?>
                
            </div><!-- .row -->
        </div><!-- #content -->
        
        <?php reactor_content_after(); ?>
        
	</div><!-- #primary -->

<?php get_footer(); ?>
