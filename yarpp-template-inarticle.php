<?php
/*
YARPP Template: In-Article List
Description: Simple in-artile list
Author: Daniel J. Schneider (@schneidan)
*/ ?>
<?php if ( have_posts() ): ?>
	<aside class="article-related">
	    <h3>Related Articles</h3>
		<ul class="related-post">
		<?php while ( have_posts() ) : the_post(); ?>
			<li <?php post_class('entry-title'); ?>>
				<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
			</li>
		<?php endwhile; ?>
		</ul>
		<div class="clear"></div>
	</aside>
<?php else: ?>

<?php endif; ?>
