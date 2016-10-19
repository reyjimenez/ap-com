<?php
/*
Template Name: Full Row Slider
*/

get_header(); 
?>
<div id="content" class="full-row-slider template-clean-page">
	<div id="full-page">
    <?php 
	if ( have_posts() ) while ( have_posts() ) : the_post();

		the_content();
		
	endwhile;
	?>
    </div><!--#full-page-->
</div><!--#content-->
<?php
get_footer();
?>