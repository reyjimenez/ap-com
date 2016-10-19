<?php
$animate_css_effects = array("flash","bounce","shake","tada","swing","wobble","pulse","flip","flipInX","flipOutX","flipInY","flipOutY","fadeIn","fadeInUp","fadeInDown","fadeInLeft","fadeInRight","fadeInUpBig","fadeInDownBig","fadeInLeftBig","fadeInRightBig","fadeOut","fadeOutUp","fadeOutDown","fadeOutLeft","fadeOutRight","fadeOutUpBig","fadeOutDownBig","fadeOutLeftBig","fadeOutRightBig","bounceIn","bounceInDown","bounceInUp","bounceInLeft","bounceInRight","bounceOut","bounceOutDown","bounceOutUp","bounceOutLeft","bounceOutRight","rotateIn","rotateInDownLeft","rotateInDownRight","rotateInUpLeft","rotateInUpRight","rotateOut","rotateOutDownLeft","rotateOutDownRight","rotateOutUpLeft","rotateOutUpRight","hinge","rollIn","rollOut");
/**
* VC ROW
*/
vc_add_param("vc_row", array(
	"type" => 'checkbox',
	"heading" => __("Full Width?", "js_composer"),
	"param_name" => "row_fullwidth",
	"description" => __("If selected, your row will be stretched to limits of the parent container.", "js_composer"),
	"value" => Array(__("Yes, please", "js_composer") => '1')
));

vc_add_param("vc_row", array(
	"type" => 'checkbox',
	"heading" => __("Vertical Centered Content?", "js_composer"),
	"param_name" => "row_vertical_center",
	"description" => __("If selected, elements in the columns will be tried to aligned as verticaly centered.", "js_composer"),
	"value" => Array(__("Yes, please", "js_composer") => '1')
));

vc_add_param("vc_row", array(
	"type" => 'checkbox',
	"heading" => __("Full Height?", "js_composer"),
	"param_name" => "row_fullheight",
	"description" => __("If selected, your row will be stretched to limits of the document. Useful to build single page sites.", "js_composer"),
	"value" => Array(__("Yes, please", "js_composer") => '1')
));

vc_add_param("vc_row", array(
	"type" => 'checkbox',
	"heading" => __("Zero Column Space?", "js_composer"),
	"param_name" => "row_zero_column_space",
	"description" => __("If selected, your columns inside this row will have no horizontal space between themselves.", "js_composer"),
	"value" => Array(__("Yes, please", "js_composer") => '1')
));

vc_add_param("vc_row", array(
	"type" => 'dropdown',
	"heading" => __("Parallax?", "js_composer"),
	"param_name" => "bg_parallax",
	"description" => __("If selected, parallax effect will be applied on background image.", "js_composer"),
	"value" => array("off", "on"),
	"group" => __("Design options", "js_composer")	
));

vc_add_param("vc_row", array(
	"type" => "dropdown",
	"heading" => __('Background Scroll', 'wpb'),
	"param_name" => "bg_scroll",
	"value" => array(
			  __("Left", 'wpb') => 'h,-1',
			  __("Right", 'wpb') => 'h,1',
			  __('Top', 'wpb') => 'y,-1',
			  __('Bottom', 'wpb') => 'y,1'
			),
	"description" => __("Please do not use with other Background Options", "wpb"),
	"group" => __("Design options", "js_composer")	
));

vc_add_param("vc_row", array(
	"type" => "textfield",
	"heading" => __("Minimum Height", "js_composer"),
	"param_name" => "row_min_height",
	"description" => __("Set minimum height of your row in pixels. Not required", "js_composer")
));

vc_add_param("vc_row", array(
	"type" => 'dropdown',
	"heading" => __("Video Background", "js_composer"),
	"param_name" => "bg_video",
	"description" => __("If selected, you can set background of your row as video.", "js_composer"),
	"value" => array("off", "on"),
	"group" => __("Design options", "js_composer")	
));

vc_add_param("vc_row", array(
	"type" => "textfield",
	"heading" => __("MP4 File", "js_composer"),
	"param_name" => "bg_video_mp4",
	"description" => __("MP4 Video file path", "js_composer"),
	"dependency" => Array('element' => "bg_video", 'value' => 'on'),
	"group" => __("Design options", "js_composer")	
));

vc_add_param("vc_row", array(
	"type" => "textfield",
	"heading" => __("WEBM File", "js_composer"),
	"param_name" => "bg_video_webm",
	"description" => __("WEBM Video file path", "js_composer"),
	"dependency" => Array('element' => "bg_video", 'value' => 'on'),
	"group" => __("Design options", "js_composer")	
));

vc_add_param("vc_row", array(
	"type" => "textfield",
	"heading" => __("OGV File", "js_composer"),
	"param_name" => "bg_video_ogv",
	"description" => __("OGV Video file path", "js_composer"),
	"dependency" => Array('element' => "bg_video", 'value' => 'on'),
	"group" => __("Design options", "js_composer")	
));

vc_add_param("vc_row", array(
	"type" => "colorpicker",
	"heading" => __('Overlay Background', 'wpb'),
	"param_name" => "video_overlay_color",
	"description" => __("Select background color", "wpb"),
	//"edit_field_class" => 'col-md-6',
	"group" => __("Design options", "js_composer")	
));

/*vc_add_param("vc_row", array(
	"type" => "textfield",
	"heading" => __("Row ID", "js_composer"),
	"param_name" => "row_id",
	"description" => __("Set a unique ID for your row. Please do not use spaces and custom characters. Use like; 'about_us' or 'aboutus'. With this option, you can build a single page site.", "js_composer")
));*/

vc_add_param("vc_row", array(
	"type" => "dropdown",
	"heading" => __("Bottom Button", "ozy_backoffice"),
	"param_name" => "bottom_button",
	"value" => array("off", "on"),
	"admin_label" => false,
	"description" => __("If selected, you can put a button bottom of your row, useful to jump in page.", "ozy_backoffice")
));

vc_add_param("vc_row", array(
	"type" => "select_an_icon",
	"heading" => __("Icon", "js_composer"),
	"param_name" => "bottom_button_icon",
	"description" => __("Select an icon from the list of available icon set.", "js_composer"),
	"dependency" => Array('element' => "bottom_button", 'value' => 'on')
));

vc_add_param("vc_row", array(
	"type" => "textfield",
	"heading" => __("Link", "js_composer"),
	"param_name" => "bottom_button_link",
	"dependency" => Array('element' => "bottom_button", 'value' => 'on')
));

vc_add_param("vc_row", array(
	"type" => "colorpicker",
	"heading" => __("Color", "js_composer"),
	"param_name" => "bottom_button_color",
	"dependency" => Array('element' => "bottom_button", 'value' => 'on'),
	"value" => "#222222"
));

/**
* VC SINGLE IMAGE
*/
vc_add_param("vc_single_image", array(
	"type" => "dropdown",
	"heading" => __("Open Link in Lightbox?", "ozy_backoffice"),
	"param_name" => "lightbox",
	"value" => array("no", "yes"),
	"admin_label" => false
));
vc_add_param("vc_single_image", array(
	"type" => "dropdown",
	"heading" => __("Zoom on Hover?", "ozy_backoffice"),
	"param_name" => "zoom_on_hover",
	"value" => array("no", "yes"),
	"admin_label" => false
));
vc_add_param("vc_single_image", array(
	"type" => "dropdown",
	"heading" => __("Animated?", "ozy_backoffice"),
	"param_name" => "img_animated",
	"value" => array("no", "yes"),
	"admin_label" => false
));
vc_add_param("vc_single_image", array(
	"type" => "dropdown",
	"heading" => __("Infinite Animation?", "ozy_backoffice"),
	"param_name" => "img_infinite",
	"value" => array("no", "yes"),
	"dependency" => Array('element' => "img_animated", 'value' => 'yes'),	
	"admin_label" => false
));
vc_add_param("vc_single_image", array(
	"type" => "dropdown",
	"heading" => __("Animation", "ozy_backoffice"),
	"param_name" => "img_animation",
	"value" => $animate_css_effects,
	"dependency" => Array('element' => "img_animated", 'value' => 'yes'),	
	"admin_label" => false
));

/**
* VC BUTTON 2
*/
vc_add_param("vc_button2", array(
	"type" => "dropdown",
	"heading" => __("Full Width?", "ozy_backoffice"),
	"param_name" => "full_width",
	"value" => array("no", "yes"),
	"admin_label" => false
));
?>