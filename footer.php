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

<script type='text/javascript'>
/* <![CDATA[ */
var biJsHost = (("https:" == document.location.protocol) ? "https://" : "http://");
(function(d, s, id, tid, vid) {
    var js, ljs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s);
    js.id = id;
    js.src = biJsHost + "cdn.listrakbi.com/scripts/script.js?m=" + tid + "&v=" + vid;
    ljs.parentNode.insertBefore(js, ljs);
})(document, 'script', 'ltkSDK', 'tUxHTINGb1zW', '1');
(function(d) {
    if (document.addEventListener) document.addEventListener('ltkAsyncListener', d);
    else {
        e = document.documentElement;
        e.ltkAsyncProperty = 0;
        e.attachEvent('onpropertychange', function(e) {
            if (e.propertyName == 'ltkAsyncProperty') { d(); }
        });
    }
})(function() {
    /********** Begin Custom Code **********/
    _ltk.Activity.AddPageBrowse();
    _ltk.Activity.Submit();
    /********** End Custom Code **********/
});
/* ]]> */
</script>
	</body>
</html>
