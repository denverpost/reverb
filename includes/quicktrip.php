<?php
/*
* Creating a function to create our CPT
*/

function aday_post_type() {

// Set UI labels for Custom Post Type
    $labels = array(
        'name'                           => 'Quick Trip',
        'singular_name'                  => 'Quick Trip',
        'search_items'                   => 'Search Quick Trip',
        'all_items'                      => 'Quick Trip',
        'edit_item'                      => 'Edit Quick Trip',
        'update_item'                    => 'Update Quick Trip',
        'add_new_item'                   => 'Add New Quick Trip',
        'new_item_name'                  => 'New Quick Trip',
        'menu_name'                      => 'Quick Trip',
        'view_item'                      => 'View Quick Trip',
        'popular_items'                  => 'Popular Quick Trip',
        'separate_items_with_commas'     => 'Separate Quick Trip with commas',
        'add_or_remove_items'            => 'Add or remove Quick Trip',
        'choose_from_most_used'          => 'Choose from the most used Quick Trip',
        'not_found'                      => 'No Quick Trip found'
    );

// Set other options for Custom Post Type

	$args = array(
		'label'               => __( 'Quick Trip', 'reverb' ),
		'description'         => __( 'Quick Trip these cities', 'reverb' ),
		'labels'              => $labels,
		// Features this CPT supports in Post Editor
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes', 'revisions', 'author', 'custom-fields' ),
		// You can associate this CPT with a taxonomy or custom taxonomy.
		//'taxonomies'          => array( 'genres' ),
		/* A hierarchical CPT is like Pages and can have
		* Parent and child items. A non-hierarchical CPT
		* is like Posts.
		*/
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'taxonomies'    => array( 'category','post_tag' ),
		'rewrite' => array('slug' => 'quicktrip'),
	);

	// Registering your Custom Post Type
	register_post_type( 'quicktrip', $args );

}

/* Hook into the 'init' action so that the function
* Containing our post type registration is not
* unnecessarily executed.
*/

add_action( 'init', 'aday_post_type', 0 );


add_action('admin_init', 'add_meta_boxes', 1);
/*
function add_meta_boxes() {
	add_meta_box( 'repeatable-fields', 'Event', 'repeatable_meta_box_display', 'aday');
}

function get_product($value, $value2, $isHidden = false) {
	if (!empty($isHidden)) {
		//do nothing
		$addTextArea = '';
	}else {
		$addTextArea = '<textarea class="fulleditor" id="fulleditor" name="fulleditor[]" >' . (!empty($value2) ? esc_attr($value2) : esc_attr('')) . '</textarea>';
	}

	return '
	<li class="product js-sort ' . (!empty($isHidden) ? esc_attr('empty-row screen-reader-text') : esc_attr('')) . '">
		<a class="button js-remove" title="' . esc_attr(__('Click to remove the element', 'reverb')). '">-</a>
		Event Type: <input type="text" name="name[]" value="' . (!empty($value) ? esc_attr($value) : esc_attr('')) . '" />
		<br/><br/>Content:<br/>
		'.$addTextArea.'
	</li>';
}


function repeatable_meta_box_display($post) {
	wp_nonce_field('repeatable_meta_box_nonce', 'repeatable_meta_box_nonce');
	$repeatable_fields = get_post_meta($post->ID, 'repeatable_fields', true);

	?>
	<script src='https://cloud.tinymce.com/stable/tinymce.min.js'></script>
	<script>
        jQuery(document).ready(function($) {

            function AddTinyMce(editorId) {
                if(tinyMCE.get(editorId))
                {
                    tinyMCE.EditorManager.execCommand('mceFocus', false, editorId);
                    tinyMCE.EditorManager.execCommand('mceRemoveEditor', true, editorId);
                    $("#"+editorId).attr('id', 'fulleditor');
                } else {
                    tinymce.EditorManager.execCommand('mceAddEditor', false, editorId);
                    $("#"+editorId).attr('id', 'fulleditor');
                }
            }

            $('#js-add').on('click', function() {
                var $row = $('.empty-row.screen-reader-text').clone(true);
                $row.removeClass('empty-row screen-reader-text');
                $editorID = 'tiny_'+jQuery.now();
                $addEditor = '<textarea id="'+$editorID+'" class="fulleditor" id="fulleditor" name="fulleditor[]" >';
                $placeEditor = $row.children('#editorUnderHere');
                $row.append($addEditor);
                $('#js-products').append($row);
                AddTinyMce($editorID);
                return false;
            });

            $('.js-remove').on('click', function() {
                $(this).parent().remove();
                return false;
            });

            $('#js-products').sortable({
                opacity: 0.6,
                revert: true,
                cursor: 'move',
                handle: '.js-sort'
            });


            tinymce.init({
                selector: '.fulleditor',
                height: 350,
                menubar: false,
                plugins: [
                    'autolink lists link image charmap print preview anchor textcolor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table contextmenu paste code help wordcount'
                ],
                toolbar: 'insert | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
                content_css: [
                    '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
                    '//www.tinymce.com/css/codepen.min.css']
            });


        });
	</script>
	<div class="experiment-metabox-container">
		<ul id="js-products">
			<?php
			if($repeatable_fields) {
				foreach($repeatable_fields as $field) {
					echo get_product($field['name'], $field['fulleditor']);
				}
			} else {
				echo get_product(null,null); // empty product (no args)
			}

			echo get_product(null,null, true); // empty hidden one for jQuery
			?>
		</ul>
		<a id="js-add" class="button">Add Product</a>
	</div>
	<?php
}

add_action('save_post', 'repeatable_meta_box_save');

function repeatable_meta_box_save($post_id) {
	if ( ! isset( $_POST['repeatable_meta_box_nonce'] ) ||
	     ! wp_verify_nonce( $_POST['repeatable_meta_box_nonce'], 'repeatable_meta_box_nonce' ) )
		return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return;

	if (!current_user_can('edit_post', $post_id))
		return;

	$old = get_post_meta($post_id, 'repeatable_fields', true);
	$new = array();


	$names = $_POST['name'];
	$eventContent = $_POST['fulleditor'];

	$count = count( $names );

	for ( $i = 0; $i < $count; $i++ ) {
		if ( $names[$i] != '' ) :
			$new[$i]['name'] = stripslashes( strip_tags( $names[$i] ) );
			$new[$i]['fulleditor'] = $eventContent[$i];
		endif;
	}

	if ( !empty( $new ) && $new != $old )
		update_post_meta( $post_id, 'repeatable_fields', $new );
	elseif ( empty($new) && $old )
		delete_post_meta( $post_id, 'repeatable_fields', $old );
}
*/
?>