<?php
/*
Template Name: Full Master Slider
*/

get_header(); 

if ( have_posts() ) while ( have_posts() ) : the_post();

	$master_slider_id = ozy_get_metabox('master_slider');
	
	if(function_exists( 'masterslider' )) {
		masterslider( $master_slider_id );
	}
	
endwhile;

get_footer();
?>