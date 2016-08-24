<?php
/**
 * The template for displaying the footer
 *
 * @package Reactor
 * @subpackge Templates
 * @since 1.0.0
 */
?>
       
        <?php if (!is_page_template('page-templates/main-page.php')) {
        	reactor_footer_before();
        	} ?>
        
        <footer id="footer" class="site-footer" role="contentinfo">
        
        	<?php reactor_footer_inside(); ?>
  
        </footer><!-- #footer -->
        
        <?php reactor_footer_after(); ?>

    </div><!-- #main -->
</div><!-- #page -->

<?php wp_footer(); reactor_foot(); ?>

<script type="text/javascript">
	var _sf_async_config={};
	/** CONFIGURATION START **/
	_sf_async_config.title = "<?php echo addslashes(html_entity_decode(wp_title('', false), ENT_QUOTES, 'UTF-8') ); ?>";
	_sf_async_config.uid = 2671;
	_sf_async_config.domain = "heyreverb.com";
	_sf_async_config.sections = "<?php echo $GLOBALS['dfmcat'][0]; ?>";
	<?php if ( is_single() && strlen( $GLOBALS['dfmby'] ) > 2 ): ?>
		_sf_async_config.authors = '<?php echo $GLOBALS['dfmby']; ?>'
	<?php endif; ?>
	_sf_async_config.useCanonical = false;
	var _sf_async_config={};
	/** CONFIGURATION END **/
	(function(){
	  function loadChartbeat() {
	    window._sf_endpt=(new Date()).getTime();
	    var e = document.createElement("script");
	    e.setAttribute("language", "javascript");
	    e.setAttribute("type", "text/javascript");
	    e.setAttribute('src', '//static.chartbeat.com/js/chartbeat.js');
	    document.body.appendChild(e);
	  }
	  var oldonload = window.onload;
	  window.onload = (typeof window.onload != "function") ?
	     loadChartbeat : function() { oldonload(); loadChartbeat(); };
	})();
</script>

	</body>
</html>
