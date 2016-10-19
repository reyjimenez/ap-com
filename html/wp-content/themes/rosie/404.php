<?php
// Look for custom 404 page, Apperance > Theme Options > Layout > Content / Page / Post : Custom 404 Page
$notfound_page_id = ozy_get_option("page_404_page_id");
if((int)$notfound_page_id > 0 && get_page($notfound_page_id)) {
	header("location:" . get_permalink($notfound_page_id) );
	exit();
}

get_header(); 
?>
<div id="content">
	<div id="error404" class="post">
		<h1><?php _e('Error 404 Not Found', 'vp_textdomain'); ?></h1>
		<div class="post-content">
			<p><?php _e('Oops. Fail. The page cannot be found.', 'vp_textdomain'); ?></p>
			<p><?php _e('Please check your URL or use the search form below.', 'vp_textdomain'); ?></p>
            <p>&nbsp;</p>
            <p><a href="<?php echo OZY_HOME_URL ?>" class="vc_btn vc_btn-sky vc_btn-md vc_btn-rounded"><span><?php _e('Click here to return main page', 'vp_textdomain'); ?></span></a></p>
			<?php get_search_form(); /* outputs the default Wordpress search form */ ?>
		</div><!--.post-content-->
	</div><!--#error404 .post-->
</div><!--#content-->

<canvas id="canvas" width="100%" height="100%"></canvas>        

<div id="trees"></div>

<?php get_footer(); ?>