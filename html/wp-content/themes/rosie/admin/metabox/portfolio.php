<?php

return array(
	'id'          => 'ozy_rosie_meta_portfolio',
	'types'       => array('ozy_portfolio'),
	'title'       => __('Portfolio Options', 'vp_textdomain'),
	'priority'    => 'high',
	'template'    => array(
		array(
			'type' => 'notebox',
			'name' => 'ozy_rosie_meta_portfolio_info',
			'label' => __('IMPORTANT!', 'vp_textdomain'),
			'description' => __('To use "Video" Post Type, you have to enable "Use Custom Thumbnail" and enter path of one Vimeo or YouTube video.', 'vp_textdomain'),
			'status' => 'info',
		),	
		array(
			'type' => 'radioimage',
			'name' => 'ozy_rosie_meta_portfolio_post_format',
			'label' => __('Project Type', 'vp_textdomain'),
			'description' => __('Select the one suits your project.', 'vp_textdomain'),
			'default' => 'standard',
			'items' => array(
				array(
					'value' => 'standard',
					'label' => __('Standard Page', 'vp_textdomain'),
					'img' => OZY_BASE_URL . 'admin/images/portfolio-standard.png',
				),
				array(
					'value' => 'video',
					'label' => __('Video', 'vp_textdomain'),
					'img' => OZY_BASE_URL . 'admin/images/portfolio-video.png',
				),			
				array(
					'value' => 'inpage-slider',
					'label' => __('Classic Slider With Content', 'vp_textdomain'),
					'img' => OZY_BASE_URL . 'admin/images/portfolio-classic-slider.png',
				),
				array(
					'value' => 'full-page-slider',
					'label' => __('Full Page Slider', 'vp_textdomain'),
					'img' => OZY_BASE_URL . 'admin/images/portfolio-full-page-classic.png',
				),
				array(
					'value' => 'full-page-nearby-slider',
					'label' => __('Full Page Visible Nearby Slider', 'vp_textdomain'),
					'img' => OZY_BASE_URL . 'admin/images/portfolio-nearby.png',
				),
			),
		),
		array(
			'type' => 'radiobutton',
			'name' => 'ozy_rosie_meta_portfolio_grid_effect',
			'label' => __('Modern Grid Effect', 'vp_textdomain'),
			'description' => __('Select an effect to apply on your item. Only available for Modern Grid page templates.', 'vp_textdomain'),
			'items' => array(
				array(
					'value' => '-1',
					'label' => __('-As set on page\'s Portfolio Options-', 'vp_textdomain'),
				),
				array(
					'value' => 'lily',
					'label' => __('Lilly', 'vp_textdomain'),
				),
				array(
					'value' => 'sadie',
					'label' => __('Sadie', 'vp_textdomain'),
				),
				array(
					'value' => 'honey',
					'label' => __('Honey', 'vp_textdomain'),
				),
				array(
					'value' => 'layla',
					'label' => __('Layla', 'vp_textdomain'),
				),				
				array(
					'value' => 'zoe',
					'label' => __('Zoe', 'vp_textdomain'),
				),				
				array(
					'value' => 'oscar',
					'label' => __('Oscar', 'vp_textdomain'),
				),				
				array(
					'value' => 'marley',
					'label' => __('Marley', 'vp_textdomain'),
				),				
				array(
					'value' => 'ruby',
					'label' => __('Ruby', 'vp_textdomain'),
				),				
				array(
					'value' => 'roxy',
					'label' => __('Roxy', 'vp_textdomain'),
				),				
				array(
					'value' => 'bubba',
					'label' => __('Bubba', 'vp_textdomain'),
				),				
				array(
					'value' => 'romeo',
					'label' => __('Rome', 'vp_textdomain'),
				),
				array(
					'value' => 'dexter',
					'label' => __('Dexter', 'vp_textdomain'),
				),
				array(
					'value' => 'sarah',
					'label' => __('Sarah', 'vp_textdomain'),
				),
				array(
					'value' => 'chico',
					'label' => __('Chico', 'vp_textdomain'),
				),
				array(
					'value' => 'milo',
					'label' => __('Milo', 'vp_textdomain'),
				),									
			),
			'default' => array(
				'-1',
			),
		),			
		array(
			'type' => 'toggle',
			'name' => 'ozy_rosie_meta_portfolio_hide_meta_info',
			'label' => __('Hide Meta Info', 'vp_textdomain'),
			'description' => __('Check this box if you like to hide Meta Information section.', 'vp_textdomain'),
		),		
		array(
			'type'      => 'group',
			'repeating' => true,
			'sortable'	=> true,
			'name'      => 'ozy_rosie_meta_portfolio_meta_info',
			'title'     => __('Meta Info', 'vp_textdomain'),
			'fields'    => array(
				array(
					'type' => 'textbox',
					'name' => 'ozy_rosie_meta_portfolio_meta_info_label',
					'label' => __('Label', 'vp_textdomain'),
					'description' => __('Enter a label, like "Client"', 'vp_textdomain'),
					'default' => ''
				),
				array(
					'type' => 'textbox',
					'name' => 'ozy_rosie_meta_portfolio_meta_info_value',
					'label' => __('Value', 'vp_textdomain'),
					'description' => __('Type something, like "John Doe Inc."', 'vp_textdomain'),
					'default' => ''
				),				
			),
		),				
	),
);

/**
 * EOF
 */