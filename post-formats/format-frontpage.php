<?php
/**
 * The template for displaying post content
 *
 * @package Reactor
 * @subpackage Post-Formats
 * @since 1.0.0
 */

$categories_list = rvrb_get_top_category_slug();
$cat_slug_class = 'format-frontpage category-' . $categories_list->slug;
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class($cat_slug_class); ?>>
        <div class="entry-body">
        	<?php reactor_post_frontpage(); ?>             
        </div><!-- .entry-body -->
	</article><!-- #post -->