<?php
/**
 * The template for displaying author archive pages
 *
 * @package Reactor
 * @subpackge Templates
 * @since 1.0.0
 */
?>

<?php get_header(); ?>

	<div id="primary" class="site-content">
    
    	<?php reactor_content_before(); ?>
    
        <div id="content" role="main">
        	<div class="row">
                <div class="<?php reactor_columns(8,12,6); ?>">
                
                <?php reactor_inner_content_before(); ?>

                <?php // the get options
                    $curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
                    $number_posts = 50;
                    
                    $args = array(
                        'post_type' => 'post',
                        'author'    => $curauth->ID,
                        'posts_per_page'    => $number_posts,
                        'paged' => get_query_var( 'paged' ),
                        );
                    
                    global $authpage_query; 
                    $authpage_query = new WP_Query( $args ); ?>
                
				<?php if ( $authpage_query->have_posts() ) : $authpage_query->the_post(); ?>

                    <?php reactor_loop_before(); ?>
        
                    <header class="archive-header">
                        <h1 class="archive-title author-title"><?php printf( __('%s', 'reactor'), get_the_author() ); ?></h1>
                    </header><!-- .archive-header -->
        
                    <?php $authpage_query->rewind_posts(); ?>
        
                    <div class="row">
                        <?php // If a user has filled out their description, show a bio on their entries.
                        if ( get_the_author_meta('description') ) : ?>
                        <div class="author-info large-5 medium-5 small-12 columns panel">
                            <div class="author-avatar right large-6 medium-6 small-6">
                                <?php echo sprintf('<img src="%1$s" class="authormug" alt="%2$s" />',
                                    the_author_image_url( get_the_author_meta('ID') ),
                                    esc_attr( sprintf( __('%s', 'reactor'), get_the_author() ) )
                                 ); ?>
                            </div><!-- .author-avatar -->
                            <div class="author-description">
                                <p><?php echo nl2br(get_the_author_meta('description')); ?></p>
                                <ul class="author-social">
                                    <?php 
                                    $auth_tw = ( get_the_author_meta('twitter') != '' ? sprintf('<li>Twitter: <a href="http://twitter.com/%1$s" alt="%1$s on Twitter">@%1$s</a></li>', get_the_author_meta('twitter') ) : '' );
                                    $auth_fb = ( get_the_author_meta('facebook') != '' ? sprintf('<li>Facebook: <a href="%1$s" alt="%2$%s on Facebook">%2$s</a></li>', get_the_author_meta('facebook'), get_the_author() ) : '' );
                                    $auth_ig = ( get_the_author_meta('instagram') != '' ? sprintf('<li>Instagram: <a href="http://instagram.com/%1$s" alt="%2$%s on Instagram" rel="me">%2$s</a></li>', get_the_author_meta('instagram'), get_the_author() ) : '' );
                                    $auth_gp = ( get_the_author_meta('googleplus') != '' ? sprintf('<li>Google+: <a href="%1$s" alt="%2$%s on Google Plus" rel="me">%2$s</a></li>', get_the_author_meta('googleplus'), get_the_author() ) : '' );
                                    echo $auth_tw . $auth_fb . $auth_ig . $auth_gp;
                                    ?>
                                </ul>
                            </div><!-- .author-description	-->
                        </div><!-- .author-info -->
                        <?php endif; ?>

                    <?php endif; // end have_posts() check ?> 
                    
                    <div class="author-posts large-7 medium-7 small-12 columns">
                        <h2 class="author-posts-title">Recent posts</h2>

        				<?php if ( $authpage_query->have_posts() ) : ?>
                                
                            <?php reactor_loop_before(); ?>
                                            
                            <?php while ( $authpage_query->have_posts() ) : $authpage_query->the_post(); ?>
                                                
                                <?php // get post format and display template for that format
                                if ( !get_post_format() ) : get_template_part('post-formats/format', 'standard');
                                else : get_template_part('post-formats/format', get_post_format()); endif; ?>
                                                
                                <?php reactor_post_after(); ?>
                                                
                            <?php endwhile; // end of the loop ?>
                                            
                            <?php reactor_loop_after(); ?>
                                            
                            <?php // if no posts are found
                            else : reactor_loop_else(); ?>
                        
                        <?php endif; // end have_posts() check ?> 
                    </div><!-- .author-posts -->
                </div>

                <?php reactor_inner_content_after(); ?>
                
                </div><!-- .columns -->
                
                <?php get_sidebar(); ?>
                
            </div><!-- .row -->
        </div><!-- #content -->
        
        <?php reactor_content_after(); ?>
        
	</div><!-- #primary -->

<?php get_footer(); ?>