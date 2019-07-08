<?php
/**
 * Template Name: Quick Trip
 * 
 * The template for displaying single quick trips
 *
 * @package Reactor
 * @subpackage Templates
 * @since 1.0.0
 */
?>
<?php

    function addSlickScripts() {
        wp_enqueue_style( 'quicktripapp', get_stylesheet_directory_uri() . '/library/css/quicktrip.css', '1.0.0', true );
        wp_enqueue_style( 'swiper', get_stylesheet_directory_uri() . '/library/css/swiper.min.css', '1.0.0', true );
        wp_enqueue_script( 'swiper', get_stylesheet_directory_uri() . '/library/js/swiper.min.js', array(), '1.0.0', true );
        wp_enqueue_script( 'app', get_stylesheet_directory_uri() . '/library/js/quicktrip.js', array(), '1.0.0', true );
        wp_enqueue_script( 'waypoints', get_stylesheet_directory_uri() . '/library/js/jquery.waypoints.min.js', array(), '1.0.0', false );
        wp_enqueue_script( 'waypointsticky', get_stylesheet_directory_uri() . '/library/js/sticky.min.js', array(), '1.0.0', false );
    //	wp_enqueue_style( 'aoscss', 'https://unpkg.com/aos@next/dist/aos.css', '1.0.0', true );
    //	wp_enqueue_script( 'aos', 'https://unpkg.com/aos@next/dist/aos.js', array(), '1.0.0', true );
        wp_enqueue_style( 'aoscss', get_stylesheet_directory_uri() . '/library/css/aos.css', array(), '1.0.0', false );
        wp_enqueue_script( 'aos', get_stylesheet_directory_uri() . '/library/js/aos.js', array(), '1.0.0', false );
    }
    add_action( 'wp_enqueue_scripts', 'addSlickScripts' );
    add_action('wp_enqueue_scripts', 'jquery', false);

?>

<?php get_header();?>
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,400,700" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://js.api.here.com/v3/3.0/mapsjs-ui.css?dp-version=1542186754" />
    <link href="https://fonts.googleapis.com/css?family=Heebo:300,800&display=swap" rel="stylesheet">

	<div id="primary" class="site-content">

    	<?php reactor_content_before(); ?>

        <div id="content" role="main">

                <?PHP $headerPhoto = get_field('featured_header_image');?>
                <img class="qt_featuredImage" src="<?php echo $headerPhoto['url']; ?>" />
                <div class="headerCaption"><?php echo $headerPhoto['original_image']['caption']; ?></div>
            <div class="row">
                <div class="md-overlay"></div><!-- the overlay element -->
                <div class="<?php reactor_columns(); ?> stickyWrap">
                
                <?php reactor_inner_content_before(); ?>
					<?php
                    global $post;
                    // start the loop
                    while ( have_posts() ) : the_post();
                    //get map location
                    $location = get_field('map');
                    ?>


                        <div class="row">
                            <div class="small-12 columns qt_title">
                                <div class="qt_titleBG qt_font">
                                    <div class="qt_logo qt_font"><img src="<?PHP echo get_stylesheet_directory_uri() ?>/library/img/QTlogo.svg" /></div>
                                    <div class="qt_cityName qt_font">
                                        <?php
                                        $cityName = get_field('name_of_city');
                                        echo $cityName;
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div id="qt_map" ></div>
                        </div>
                        <div class="row">
                            <div class="small-12" id="qt_summary">
                                <?PHP the_field('summary'); ?>
                                <div class="modaltriggerMobile">Where should we go next?<div id="modalSubmitText">Submit a City</div></div>
                            </div>
                        </div>

	                    <?php
                        $weekdayArray = array();
                        $previousDay = '';
                    //  $outputDay='';
                        // check if the repeater field has rows of data
                        if( have_rows('section') ):
                            // loop through the rows of data
                            while ( have_rows('section') ) : the_row();
                                // display a sub field value
                                //section variables
                                $business = get_sub_field('business_location');
                                $gallery = get_sub_field('section_gallery');
                                $alternatives = get_sub_field('alternative_options');

                                $stickyName = get_sub_field('section_title');
                                $stickyName = str_replace(" ","-", $stickyName);
	                            $day = get_sub_field('day');

//                                echo '<script>';
//                                echo 'console.log("current: '.$day.' / previous: '.$previousDay.'")';
//                                echo '</script>';
	                            if ($previousDay != $day){
	                                if ($previousDay == ''){
	                                    //do nothing it's the first one
                                    }else {
		                                echo "</div>";
	                                }
//		                            echo '<script>';
//		                            echo 'console.log("end wrapper please")';
//		                            echo '</script>';
	                            }

	                            $previousDay = $day;
	                            if (in_array($day,$weekdayArray)) {
	                                //do nothing it's already there but set output to empty
		                            $outputDay = '';
//
                                }else{
		                            //put into array and output to display variable
		                            $outputDay = $day;
		                            $weekdayArray[] = $day;
                                }
                            if ($outputDay != ''){
                                echo '<div class="entireDayWrap">';
                                echo '<div class="stickyFlag"><div data-aos="fade-left" data-aos-offset="0" class="dayFlag dayBGColor'.$day.' dayFlagBorder'.$day.'">'.$day.'</div></div>';
                            }

                        ?>
                                <!-- a div will populate here if it's a new day along with the day flag div -->
                                <div class="row sectionWrapper section<?PHP echo $day?>">
                                        <div class="dayOfWeek dayBGColor<?PHP echo $day?>"><?PHP echo $outputDay ?></div>
                                    <?PHP
                                        if (get_sub_field('section_photo') != '') {
                                            $sectionPhotoURL = get_sub_field('section_photo');
                                            echo '
                                            <div class="row">
                                                <div class="sectionPhoto">
                                                    <img src="'.$sectionPhotoURL['url'].'"/>
                                                    <div class="photoCaption">'.$sectionPhotoURL['caption'].'</div>
                                                </div>
                                            </div>';
                                        }
                                    ?>

                                        <div class="row" style="padding-left:15px;">
                                            <div class="qt_sectionTitle qt_font " id="<?PHP echo $stickyName?>"><?PHP echo the_sub_field('section_title');?></div>
                                        </div>
                                    <div class="row" style="padding-right:40px!important;padding-left:15px;">

                                        <div class="qt_sectionHighlights qt_font">
                                            Price: <?PHP echo the_sub_field('price');?><br/>
                                            Location: <a href="<?PHP echo $business['business_link']; ?>" target="_blank"><?PHP echo $business['business_name']; ?></a>
                                        </div>
                                        <div class="row">
                                            <div class="small-12 columns qt_font">
                                                <?PHP echo the_sub_field('section_content');?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="arrows"></div>
                                            <?php
                                                $gallerySize = count($gallery);
                                                $errors = array_filter($gallery);
                                                if (!empty($errors)) {
//                                                    echo '<script>';
//                                                    echo 'console.log("i have something'.$gallerySize.'")';
//                                                    echo '</script>';
                                                }else{
                                                    $flattenDiv = 'flattenDiv';
                                                }
                                            ?>
                                            <div class="small-12 columns qt_gallery <?PHP echo $flattenDiv ?>">
                                                <!-- Slider main container -->
                                                <div class="swiper-container">
                                                    <!-- Additional required wrapper -->

                                                        <!-- If we need navigation buttons -->
                                                        <div class="swiper-button-prev"></div>
                                                        <div class="swiper-button-next"></div>
                                                    <div class="swiper-wrapper">
                                                <?PHP

                                                if( $gallery ):
                                                    foreach( $gallery as $image ):
                                                        echo "<div class=\"swiper-slide\">
                                                                <img src='".$image['url']."' />
                                                                <div class='photoCaption'>".$sectionPhotoURL['caption']."</div>
                                                                </div>";
                                                    endforeach;
                                                endif;
                                                ?>
                                                    </div>
                                                </div> <!-- close swiper-container -->
                                            </div>
                                        </div>
                                        <div class="row">
                                            <?PHP

                                            if( $alternatives ):
                                                if (count($alternatives) > 1) { $addS = 's';}
                                                echo "<h2 class='qt_alternativeTitle'><span class='qa_alternativeTitleDash'></span><span class='qa_alternativeTitleText'>Alternate Option".$addS."</span><span class='qa_alternativeTitleDash'></span></h2>";
                                                echo '<div class="flexContainer qt_alternativeBoxWrapper">';
                                                foreach( $alternatives as $otherBusiness ):
                                                    echo '<div class="qt_alternativeBox">';
                                                        //create link if it's there
                                                        if ($otherBusiness['alternative_link']){
           $createAltTitle = '<a class="qt_alternativeBusinessTitle" href="'.$otherBusiness['alternative_link'].'" target="_blank">'.$otherBusiness['alternative_name'].'</a><br/>';
                                                        }else {
                                                            $createAltTitle = '<div class="qt_alternativeBusinessTitle">'.$otherBusiness['alternative_name'].'</div>';
                                                        }
                                                        echo $createAltTitle;
                                                        echo '<p>'.$otherBusiness['alternative_description'].'</p>';
                                                    echo '</div>';
                                                endforeach;
                                                echo '</div>';
                                            endif;
                                            ?>
                                        </div>
                                    </div>
                                </div> <!-- /sectionWrapper -->
                        <?PHP
                        endwhile;
                        echo "</div>";
                            //adding post tags to templates
	                        echo get_the_tag_list( $before = '<div class="entry-tags">Post tags: ', $sep = ', ', $after = '</div>' );
                        else :
                        // no rows found
                    endif;
                    ?>
                    <div class="endForm">
                        <div id="commentMessage"></div>
                        <span id="hideForm">
                            <h3>Did we miss something?</h3>
                            <p>
                                Let us know here.<br/>
                                Know of a must-do in <?PHP echo $cityName; ?> that we missed? Let us know!
                            </p>
                            <form>
                                <input type="hidden" id="reading" disabled readonly value="<?PHP echo $cityName; ?>" />
                                <span id="commentErrors" style="color:red;"></span>
                                <label>Name</label>
                                <input type="text" id="getName" name="name"/>
                                <label>Email</label>
                                <input type="text" id="getEmail" name="email"/>
                                <label>Comments</label>
                                <textarea id="getComment" name="comment" rows="5" cols="33"></textarea>
                                <button class="endFormSubmit" id="commentSubmit">Submit</button>
                            </form>
                        </span>
                    </div>
					<?php  /* // get post format and display code for that format
                    if ( !get_post_format() ) : get_template_part('post-formats/format', 'single'); 
					else : get_template_part('post-formats/format', get_post_format() ); endif; */?>
                    
                    <?php reactor_post_after(); ?>
        
                    <?php endwhile; // end of the loop ?>
                    
                <?php reactor_inner_content_after(); ?>

                
                </div><!-- .columns -->
                
                <?php
                    get_sidebar();
                    echo '<div class="modaltrigger">Where should we go next?<div id="modalSubmitText">Submit a City</div></div>';

                ?>

            </div><!-- .row -->

        </div><!-- #content -->

        <?php reactor_content_after(); ?>

	</div><!-- #primary -->
    <div class="modalSubmitCity" id="modalSubmitCity">
        <div class="md-content">
            <span id="cityMessage"></span>
            <h3 id="thanksCity">Submit a City</h3>
            <div id="modalCityContent">
                <p>Let us know where you'd like us to go next!</p>
                <form>
                    <input type="hidden" id="reading" disabled readonly value="<?PHP echo $cityName; ?>" />
                    <input type="text" id="getCity" placeholder="city name here" />
                    <button class="md-submit" id="citySubmit">Submit</button>
                </form>
                <button class="md-close">Never Mind</button>
            </div>
        </div>
    </div>

<script type="text/javascript" src="https://js.api.here.com/v3/3.0/mapsjs-core.js"></script>
<script type="text/javascript" src="https://js.api.here.com/v3/3.0/mapsjs-service.js"></script>
<script type="text/javascript" src="https://js.api.here.com/v3/3.0/mapsjs-ui.js"></script>
<script type="text/javascript" src="https://js.api.here.com/v3/3.0/mapsjs-mapevents.js"></script>
<script>
    //map stuff

    // Initialize the platform object:
    var platform = new H.service.Platform({
        'app_id': 'ZoeraxgLCeeviHisrqKU',
        'app_code': 'NT-uWhAwq95FFEPawybywg',
        'useHTTPS': true
    });

    // Obtain the default map types from the platform object
    var maptypes = platform.createDefaultLayers();

    // Instantiate (and display) a map object:
    var map = new H.Map(
        document.getElementById('qt_map'),
        maptypes.normal.map,
        {
            zoom: 12,
            center: { lng: <?php echo $location['lng']; ?>, lat: <?php echo $location['lat']; ?> }
        });
    // Change the map base layer to the satellite map with traffic information:
    map.setBaseLayer(maptypes.normal.base);
    // var behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));
</script>

<?php get_footer(); ?>

