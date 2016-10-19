<?php

/**
 * Shortcode attributes
 * @var $atts
 * @var $el_class
 * @var $full_width
 * @var $full_height
 * @var $content_placement
 * @var $parallax
 * @var $parallax_image
 * @var $css
 * @var $el_id
 * @var $video_bg
 * @var $video_bg_url
 * @var $video_bg_parallax
 * @var $content - shortcode content
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Row
 */
$el_class = $full_height = $full_width = $content_placement = $parallax = $parallax_image = $css = $el_id = $video_bg = $video_bg_url = $video_bg_parallax = '';
$output = $after_output = '';

/*ozy*/
$row_fullwidth = $row_fullheight = $bg_parallax = $row_min_height  =$bg_slider = $bg_slider_images = $bg_video = $bg_video_mp4 = $bg_video_webm = $bg_video_ogv = $row_id = 	$video_overlay_color = $bottom_button = $bottom_button_icon = $bottom_button_link = $bottom_button_color = $row_zero_column_space = $bg_scroll = $row_vertical_center = $bg_slider_size = '';

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

wp_enqueue_script( 'wpb_composer_front_js' );

$el_class = $this->getExtraClass( $el_class );

$css_classes = array(
	'vc_row',
	'wpb_row', //deprecated
	'vc_row-fluid',
	$el_class,
	vc_shortcode_custom_css_class( $css ),
);

$wrapper_attributes = array();
// build attributes for wrapper
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
}
if ( ! empty( $full_width ) ) {
	$wrapper_attributes[] = 'data-vc-full-width="true"';
	$wrapper_attributes[] = 'data-vc-full-width-init="false"';
	if ( 'stretch_row_content' === $full_width ) {
		$wrapper_attributes[] = 'data-vc-stretch-content="true"';
	} elseif ( 'stretch_row_content_no_spaces' === $full_width ) {
		$wrapper_attributes[] = 'data-vc-stretch-content="true"';
		$css_classes[] = 'vc_row-no-padding';
	}
	$after_output .= '<div class="vc_row-full-width"></div>';
}

if ( ! empty( $full_height ) ) {
	$css_classes[] = ' vc_row-o-full-height';
	if ( ! empty( $content_placement ) ) {
		$css_classes[] = ' vc_row-o-content-' . $content_placement;
	}
}

$has_video_bg = ( ! empty( $video_bg ) && ! empty( $video_bg_url ) && vc_extract_youtube_id( $video_bg_url ) );

if ( $has_video_bg ) {
	$parallax = $video_bg_parallax;
	$parallax_image = $video_bg_url;
	$css_classes[] = ' vc_video-bg-container';
	wp_enqueue_script( 'vc_youtube_iframe_api_js' );
}

if ( ! empty( $parallax ) ) {
	wp_enqueue_script( 'vc_jquery_skrollr_js' );
	$wrapper_attributes[] = 'data-vc-parallax="1.5"'; // parallax speed
	$css_classes[] = 'vc_general vc_parallax vc_parallax-' . $parallax;
	if ( false !== strpos( $parallax, 'fade' ) ) {
		$css_classes[] = 'js-vc_parallax-o-fade';
		$wrapper_attributes[] = 'data-vc-parallax-o-fade="on"';
	} elseif ( false !== strpos( $parallax, 'fixed' ) ) {
		$css_classes[] = 'js-vc_parallax-o-fixed';
	}
}

if ( ! empty( $parallax_image ) ) {
	if ( $has_video_bg ) {
		$parallax_image_src = $parallax_image;
	} else {
		$parallax_image_id = preg_replace( '/[^\d]/', '', $parallax_image );
		$parallax_image_src = wp_get_attachment_image_src( $parallax_image_id, 'full' );
		if ( ! empty( $parallax_image_src[0] ) ) {
			$parallax_image_src = $parallax_image_src[0];
		}
	}
	$wrapper_attributes[] = 'data-vc-parallax-image="' . esc_attr( $parallax_image_src ) . '"';
}
if ( ! $parallax && $has_video_bg ) {
	$wrapper_attributes[] = 'data-vc-video-bg="' . esc_attr( $video_bg_url ) . '"';
}
$css_class = preg_replace( '/\s+/', ' ', apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, implode( ' ', array_filter( $css_classes ) ), $this->settings['base'], $atts ) );


/*ozy*/
/*if font color selected for the row element, make sure all the sub elements are affected*/
/*if($font_color && $css_class) {
	global $ozyHelper;
	$rand_id = "ozy-crfclr-". rand(1,10000);
	$ozyHelper->set_footer_style(".$rand_id h1,.$rand_id h2,.$rand_id h3,.$rand_id h4,.$rand_id h5,.$rand_id h6{color:$font_color !important;}");
	$css_class .= " " . $rand_id;
}*/

/*ozy*/
$css_class .= ($row_fullwidth == '1' ? ' ozy-custom-full-row' : '');
$css_class .= ($row_fullheight == '1' ? ' ozy-custom-fullheight-row' : '');
$css_class .= ($row_vertical_center == '1' ? ' ozy-custom-verticalcentered-row' : '');
$css_class .= ($bg_parallax == 'on' || $bg_parallax == '1' ? ' ozy-custom-row parallax' : '');

if((int)$row_min_height>0) {
	/*if(strpos($style, 'style="')>-1) {
		$style = str_replace('style="', ' style="position:relative;overflow:hidden;min-height:'. $row_min_height .'px;', $style);
	}else{
		$style = ' style="position:relative;overflow:hidden;min-height:'. $row_min_height .'px;"';
	}*/
	$wrapper_attributes[] = ' style="position:relative;overflow:hidden;min-height:'. $row_min_height .'px;"';
}
$css_class .= ($bg_video == 'on' ? ' ozy-row-has-video' : '');
$css_class .= ($row_zero_column_space == '1' ? ' ozy-row-zero-space' : '');
$css_class .= ($full_width == 'stretch_row_content_no_spaces' ? ' vc_row-no-padding' : '');
if(!$el_id && $row_id) $el_id = $row_id; //ozy. cover old ROW ID value



$wrapper_attributes[] = 'class="' . esc_attr( trim( $css_class ) ) . '"';

$output .= '<div ' . implode( ' ', $wrapper_attributes ) . '>';
//$output .= wpb_js_remove_wpautop( $content );
/*ozy*/
if($bg_slider === 'on') {
	$bg_slider_images = explode(',', $bg_slider_images); $counter = 0;
	$output .= '<div id="ozy-background-cycler" class="'. esc_attr($bg_slider_size) .'"  >';
	foreach($bg_slider_images as $bg_slider_image) {
		$current_image = wp_get_attachment_image_src($bg_slider_image, 'full');
		if(isset($current_image[0])) {
			$output .= '<div class="'. ($counter === 0? 'active' : '') .'" style="background-image:url('. esc_attr($current_image[0]) .')"></div>';
		}
		$counter++;
	}
	$output .= '</div>';
}

if($el_id != NULL && $el_id != '') {	
	global $ozyHelper;
	array_push($ozyHelper->vertical_nav_buttons, 'page-' . esc_attr($el_id));
}

/*ozy*/
if($bg_video == 'on') { 
	$output .= '<video class="slider-video" width="1920" height="1081" style="position:absolute;left:0;top:0;" preload="auto" loop autoplay src="'.$bg_video_mp4.'">';
	if($bg_video_ogv) $output .= '<source type="video/ogv" src="'. $bg_video_ogv .'">';
	if($bg_video_mp4) $output .= '<source type="video/mp4" src="'. $bg_video_mp4 .'">';	
	if($bg_video_webm) $output .= '<source type="video/webm" src="'. $bg_video_webm .'">';
	$output .= '</video>';
}
if($video_overlay_color) {
	$output .= '<div class="video-mask'. ($video_overlay_color ? ' has-bg' : '' ) .'" '. ($video_overlay_color ? ' style="background-color:'. $video_overlay_color .';"' : '' ) .'></div>';
}
$output .= '<div class="parallax-wrapper">'; //ozy
$output .= wpb_js_remove_wpautop( $content );
$output .= '</div>'; //ozy
/*ozy*/
if($bottom_button == 'on') {
	$output .= '<a href="'. $bottom_button_link .'" class="row-botton-button" style="color:'. $bottom_button_color .'"><span class="'. $bottom_button_icon .'" ></span></a>';
}
$output .= '</div>';
$output .= $after_output;

echo $output;
