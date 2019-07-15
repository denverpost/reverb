These are updates to plugin code for the know….these go in the normal Wordpress plugin folder, not the theme folder. I’m keeping them here so I don’t have to create a separate repository that might get looked over or lost.

Only plugins that have changes from the original plugin are in here.

Make sure and run a diff for changes when updating these plugins.

Acf-pro:
https://github.com/AdvancedCustomFields/acf/blob/a0c931588e7ed60b09f566249d820ab055fb89a2/includes/forms/form-post.php#L149-L151

Includes/forms/form-post:
Line: 120
//		// remove postcustom metabox (removes expensive SQL query)
//		if( acf_get_setting('remove_wp_meta_box') ) {
//			remove_meta_box( 'postcustom', false, 'normal' ); 
//		}