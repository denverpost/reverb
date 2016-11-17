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

                    <?php $args = array(
                        'orderby'       => 'post_count',
                        'order'         => 'DESC'
                    );

                    $blogauthors = get_users( $args );

                    $editorsout = $authorsout = $photogsout = '';

                    foreach($blogauthors as $author) {
                        if ( get_the_author_meta('list_author_about', $author->ID) == true ) {
                            $display_title = ( get_the_author_meta( 'display_title', $author->ID ) ) ? '<span>, <em>' . get_the_author_meta( 'display_title', $author->ID ) . '</em></span>' : '';
                            if ( get_the_author_meta('display_author_as', $author->ID) == 'editor' ) {
                                $editorsout .= '<li class="about-author">';
                                    $editorsout .= sprintf('<h2 class="about-author-title"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%2$s</a>%3$s</h2>',
                                        esc_url( get_author_posts_url( $author->ID ) ),
                                        get_the_author_meta( 'display_name', $author->ID),
                                        $display_title
                                    );
                                    $editorsout .= '<div class="about-author-image">';
                                    if (the_author_image_url($author->ID) !== '') {
                                        $editorsout .= sprintf('<a class="url fn n" href="%1$s" title="%3$s"><img src="%2$s" alt="%3$s" /></a>',
                                            esc_url( get_author_posts_url( $author->ID ) ),
                                            the_author_image_url($author->ID),
                                            get_the_author_meta('display_name', $author->ID)
                                        );
                                    }
                                    $editorsout .= '</div>';
                                    $editorsout .= '<p>' . smart_trim(get_the_author_meta('description', $author->ID),30) . '</p>';
                                $editorsout .= '</li>';
                            }
                            if ( get_the_author_meta('display_author_as', $author->ID) == 'writer' ) {
                                $authorsout .= '<li class="about-author">';
                                    $authorsout .= sprintf('<h2 class="about-author-title"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%2$s</a>%3$s</h2>',
                                        esc_url( get_author_posts_url( $author->ID ) ),
                                        get_the_author_meta('display_name', $author->ID),
                                        $display_title
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
                                    $authorsout .= '<p>' . smart_trim(get_the_author_meta('description', $author->ID),30) . '</p>';
                                $authorsout .= '</li>';
                            }
                            if ( get_the_author_meta('display_author_as', $author->ID) == 'photographer' ) {
                                $photogsout .= '<li class="about-author">';
                                    $photogsout .= sprintf('<h2 class="about-author-title"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%2$s</a>%3$s</h2>',
                                        esc_url( get_author_posts_url( $author->ID ) ),
                                        get_the_author_meta('display_name', $author->ID),
                                        $display_title
                                    );
                                    $photogsout .= '<div class="about-author-image">';
                                    if (the_author_image_url($author->ID) !== '') {
                                        $photogsout .= sprintf('<a class="url fn n" href="%1$s" title="%3$s"><img src="%2$s" alt="%3$s" /></a>',
                                            esc_url( get_author_posts_url( $author->ID ) ),
                                            the_author_image_url($author->ID),
                                            get_the_author_meta('display_name', $author->ID)
                                        );
                                    }
                                    $photogsout .= '</div>';
                                    $photogsout .= '<p>' . smart_trim(get_the_author_meta('description', $author->ID),30) . '</p>';
                                $photogsout .= '</li>';
                            }
                        }
                    }

                    $staffdisplay = '';
                    if ($editorsout != '') {
                        $staffdisplay .= '<h2 class="about-authors-list">Editors:</h2><ul class="multi-column large-block-grid-2" data-match-height="">' . $editorsout . '</ul>';
                    }
                    if ($authorsout != '') {
                        $staffdisplay .= '<h2 class="about-authors-list">Writers:</h2><ul class="multi-column large-block-grid-2" data-match-height="">' . $authorsout . '</ul>';
                    }
                    if ($photogsout != '') {
                        $staffdisplay .= '<h2 class="about-authors-list">Photographers:</h2><ul class="multi-column large-block-grid-2" data-match-height="">' . $photogsout . '</ul>';
                    } ?>

                    <h2 class="stafflist">The Know Staff</h2>

                    <?php
                    echo $staffdisplay;
                    ?>
                    
                <?php reactor_inner_content_after(); ?>
                
                </div><!-- .columns -->
                
                <?php get_sidebar(); ?>
                
            </div><!-- .row -->
        </div><!-- #content -->
        
        <?php reactor_content_after(); ?>
        
    </div><!-- #primary -->

<?php get_footer(); ?>