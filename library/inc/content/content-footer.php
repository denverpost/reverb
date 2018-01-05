<?php
/**
 * Footer Content
 * hook in the content for footer.php
 *
 * @package Reactor
 * @author Anthony Wilhelm (@awshout / anthonywilhelm.com)
 * @since 1.0.0
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 * @license GNU General Public License v2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 */

/**
 * Footer widgets
 * in footer.php
 * 
 * @since 1.0.0
 */
function reactor_do_footer_widgets() { ?>
	<div class="row">
		<div class="<?php reactor_columns( 12 ); ?>">
			<div class="inner-footer">
				<?php get_sidebar('footer'); ?>       
				</div><!-- .inner-footer -->
			</div><!-- .columns -->
	</div><!-- .row -->
<?php 
}
add_action('reactor_footer_inside', 'reactor_do_footer_widgets', 1);

/**
 * Footer links and site info
 * in footer.php
 * 
 * @since 1.0.0
 */
function reactor_do_footer_content() { ?>
	<div class="site-info">
		<div class="row">
        
			<div class="<?php reactor_columns( array(5,12,12) ); ?>">
			<?php if ( function_exists('reactor_footer_links') ) : ?>
				<nav class="footer-links" role="navigation">
					<?php reactor_footer_links(); ?>
				</nav><!-- #footer-links -->
			<?php endif; ?>
			</div><!--.columns -->
                    
			<div class="<?php reactor_columns( array(7,12,12) ); ?>">
				<div id="colophon">                      
					<?php if ( reactor_option('footer_siteinfo') ) : echo reactor_option('footer_siteinfo'); else : ?>
					<p><span class="copyright">An edition of <a href="https://www.denverpost.com/" title="The Denver Post" target="_blank">The Denver Post</a>.<br />
					All contents Copyright &copy; <?php echo date_i18n('Y'); ?> <a href="https://www.denverpost.com/" title="The Denver Post" target="_blank">The Denver Post</a> or other copyright holders.</span> All rights reserved.<br />
					This material may not be published, broadcast, rewritten or redistributed for any commercial purpose.</p>
					<?php endif; ?>
				</div><!-- #colophon -->
			</div><!-- .columns -->
            
		</div><!-- .row -->
	</div><!-- #site-info -->
<?php 
}
add_action('reactor_footer_inside', 'reactor_do_footer_content', 2);
?>
