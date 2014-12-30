<?php
/**
 * Template Name: About Page
 *
 * @package reverb
 * @subpackge Page-Templates
 * @since 0.1
 */
?>

<?php get_header(); ?>

    <div id="primary" class="site-content">
    
        <?php reactor_content_before(); ?>
    
        <div id="content" role="main">
            <div class="row">
                <div class="<?php reactor_columns(8); ?> primary">
                
                <?php reactor_inner_content_before(); ?>
                 
                    <?php // get the page loop
                    get_template_part('loops/loop', 'page'); ?>

                    <h2 class="about-authors-list">Our writers:</h2>

                    <?php $args = array(
                        'orderby'       => 'ID',
                        'who'           => 'authors'
                    );

                    $blogauthors = get_users( $args );

                    $authorsout = '<ul class="multi-column large-block-grid-2" data-match-height="">';

                    foreach($blogauthors as $author) {
                        if (strpos(strtolower(get_user_meta($author->ID,'publication',true)),'reverb') !== false) {
                            $authorsout .= '<li class="about-author">';
                                $authorsout .= sprintf('<h2 class="about-author-title"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%2$s</a></h2>',
                                    esc_url( get_author_posts_url( $author->ID ) ),
                                    get_the_author_meta('display_name', $author->ID)
                                );
                                $authorsout .= '<div class="about-author-image">';
                                if (the_author_image_url($author->ID) !== '') {
                                    $authorsout .= sprintf('<a class="url fn n" href="%1$s" title="%3$s"><img src="%2$s" alt="%3$s" /></a>',
                                        esc_url( get_author_posts_url( $author->ID ) ),
                                        the_author_image_url($author->ID),
                                        get_the_author_meta('display_name', $author->ID)
                                    );
                                }
                                $authorsout .= '</div>';
                                $author_desc = substr(get_the_author_meta('description', $author->ID),0,200);
                                $authorsout .= '<p>' . substr($author_desc,0,strrpos($author_desc,' ')) . '...</p>';
                            $authorsout .= '</li>';
                        }
                    }

                    $authorsout .= '</ul>';

                    echo $authorsout;
                    ?>
                    
                <?php reactor_inner_content_after(); ?>
                
                </div><!-- .columns -->
                
                <?php get_sidebar(); ?>
                
            </div><!-- .row -->
        </div><!-- #content -->
        
        <?php reactor_content_after(); ?>
        
    </div><!-- #primary -->

<?php get_footer(); ?>