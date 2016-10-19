<?php

return array(
	'id'          => 'ozy_rosie_meta_page',
	'types'       => array('page'),
	'title'       => __('Page Options', 'vp_textdomain'),
	'priority'    => 'high',
	'template'    => array(
		array(
			'type' => 'select',
			'name' => 'ozy_rosie_meta_page_custom_menu',
			'label' => __('Custom Menu', 'vp_textdomain'),
			'description' => __('You can select a custom menu for this page.', 'vp_textdomain'),
			'items' => array(
				'data' => array(
					array(
						'source' => 'function',
						'value' => 'vp_bind_ozy_rosie_list_wp_menus',
					),
				),
			),
			'default' => '-1',
		),
		array(
			'type' => 'toggle',
			'name' => 'ozy_rosie_meta_page_use_alternate_menu',
			'label' => __('Use Alternate Menu Color Layout', 'vp_textdomain'),
			'description' => __('You can use alternate menu color layout as your default menu style. No effect will be applied for the menu on scroll. Only valid for this page.', 'vp_textdomain'),
		),
	
		array(
			'type' => 'select',
			'name' => 'ozy_rosie_meta_page_revolution_slider',
			'label' => __('Revolution Header Slider', 'vp_textdomain'),
			'description' => __('You can select a header slider if you have installed and activated Revolution Slider which comes bundled with your theme. Not available in Portfolio templates.', 'vp_textdomain'),
			'items' => array(
				'data' => array(
					array(
						'source' => 'function',
						'value' => 'vp_bind_ozy_rosie_revolution_slider',
					),
				),
			),
			'default' => '{{first}}',
		),
		array(
			'type' => 'select',
			'name' => 'ozy_rosie_meta_page_master_slider',
			'label' => __('Master Header Slider', 'vp_textdomain'),
			'description' => __('You can select a header slider if you have installed and activated Master Slider which comes bundled with your theme. Not available in Portfolio templates.', 'vp_textdomain'),
			'items' => array(
				'data' => array(
					array(
						'source' => 'function',
						'value' => 'vp_bind_ozy_rosie_master_slider',
					),
				),
			),
			'default' => '{{first}}',
		),		


		array(
			'type' => 'toggle',
			'name' => 'ozy_rosie_meta_page_use_footer_slider',
			'label' => __('Use Footer Slider', 'vp_textdomain'),
			'description' => __('You can use footer slider with header slider too.', 'vp_textdomain'),
		),
		array(
			'type'      => 'group',
			'repeating' => false,
			'length'    => 1,
			'name'      => 'ozy_rosie_meta_page_use_footer_slider_group',
			'title'     => __('Footer Slider', 'vp_textdomain'),
			'dependency' => array(
				'field'    => 'ozy_rosie_meta_page_use_footer_slider',
				'function' => 'vp_dep_boolean',
			),
			'fields'    => array(
				array(
					'type' => 'select',
					'name' => 'ozy_rosie_meta_page_revolution_footer_slider',
					'label' => __('Revolution Footer Slider', 'vp_textdomain'),
					'description' => __('You can select a footer slider if you have installed and activated Revolution Slider which comes bundled with your theme. Not available in Portfolio templates.', 'vp_textdomain'),
					'items' => array(
						'data' => array(
							array(
								'source' => 'function',
								'value' => 'vp_bind_ozy_rosie_revolution_slider',
							),
						),
					),
					'default' => '{{first}}',
				),
				array(
					'type' => 'select',
					'name' => 'ozy_rosie_meta_page_master_footer_slider',
					'label' => __('Master Footer Slider', 'vp_textdomain'),
					'description' => __('You can select a footer slider if you have installed and activated Master Slider which comes bundled with your theme. Not available in Portfolio templates.', 'vp_textdomain'),
					'items' => array(
						'data' => array(
							array(
								'source' => 'function',
								'value' => 'vp_bind_ozy_rosie_master_slider',
							),
						),
					),
					'default' => '{{first}}',
				),				
			),
		),

		array(
			'type' => 'toggle',
			'name' => 'ozy_rosie_meta_page_show_loader',
			'label' => __('Show Loading Screen', 'vp_textdomain'),
			'description' => __('Check this option to display a loading screen for this page only.', 'vp_textdomain'),
		),

		/*array(
			'type' => 'toggle',
			'name' => 'ozy_rosie_meta_page_hide_footer_widget_bar',
			'label' => __('Hide Footer Widget Bar', 'vp_textdomain'),
			'description' => __('Footer Widget bar will not be shown for this page only.', 'vp_textdomain'),
		),*/
		array(
			'type' => 'radiobutton',
			'name' => 'ozy_rosie_meta_page_hide_footer_widget_bar',
			'label' => __('Footer Bars Visiblity', 'vp_textdomain'),
			'description' => __('By this option you can hide footer bars as you wish.', 'vp_textdomain'),
			'items' => array(
				array(
					'value' => '-1',
					'label' => __('All Visible', 'vp_textdomain'),
				),
				array(
					'value' => '1',
					'label' => __('Hide Widget Bar', 'vp_textdomain'),
				),
				array(
					'value' => '2',
					'label' => __('Hide Widget Bar and Footer', 'vp_textdomain'),
				),
			),
			'default' => array(
				'-1',
			),
		),
		


		array(
			'type' => 'toggle',
			'name' => 'ozy_rosie_meta_page_hide_title',
			'label' => __('Hide Page Title', 'vp_textdomain'),
			'description' => __('Page title will not be shown on the page.', 'vp_textdomain'),
		),
		
		array(
			'type' => 'toggle',
			'name' => 'ozy_rosie_meta_page_use_no_content_padding',
			'label' => __('No content top padding', 'vp_textdomain'),
			'description' => __('Check this option to disable the padding top of your content (after page title).', 'vp_textdomain'),
		),		

		array(
			'type' => 'notebox',
			'name' => 'ozy_rosie_meta_page_no_menu_space_infobox',
			'label' => __('Use No Menu Space', 'vp_textdomain'),
			'description' => __('Following option will help you to create page with no space top of it, just like you can see our preview <a href=\'http://rosie.freevision.me/\' target=\'_blank\'>Home</a> and <a href=\'http://rosie.freevision.me/pages/about-us/\' target=\'_blank\'>About Us</a> pages.', 'vp_textdomain'),
			'status' => 'info',
		),
		array(
			'type' => 'toggle',
			'name' => 'ozy_rosie_meta_page_no_menu_space',
			'label' => __('Use No Menu Space', 'vp_textdomain'),
			'description' => __('By checking this option you can make your content start from zero instead.', 'vp_textdomain'),
			/*'dependency' => array(
				'field'    => 'ozy_rosie_meta_page_hide_title',
				'function' => 'vp_dep_boolean',
			),*/
		),
		array(
			'type' => 'toggle',
			'name' => 'ozy_rosie_meta_page_use_custom_title',
			'label' => __('Custom Header/Title', 'vp_textdomain'),
			'description' => __('There are several options to help you customize your page header.', 'vp_textdomain'),
		),
		array(
			'type'      => 'group',
			'repeating' => false,
			'length'    => 1,
			'name'      => 'ozy_rosie_meta_page_use_custom_title_group',
			'title'     => __('Custom Header/Title Options', 'vp_textdomain'),
			'dependency' => array(
				'field'    => 'ozy_rosie_meta_page_use_custom_title',
				'function' => 'vp_dep_boolean',
			),
			'fields'    => array(
				/*array(
					'type' => 'radiobutton',
					'name' => 'ozy_rosie_meta_page_custom_title_nopadding',
					'label' => __('No Padding', 'vp_textdomain'),
					'description' => __('Use this option to remove padding from content container. Usable when you like to use continious/same color from heading to content.', 'vp_textdomain'),
					'items' => array(
						array(
							'value' => 'left',
							'label' => __('Left', 'vp_textdomain'),
						),
						array(
							'value' => 'right',
							'label' => __('Right', 'vp_textdomain'),
						),
						array(
							'value' => 'center',
							'label' => __('Center', 'vp_textdomain'),
						),
					),
					'default' => array(
						'left',
					),
				),*/		
				array(
					'type' => 'radiobutton',
					'name' => 'ozy_rosie_meta_page_custom_title_position',
					'label' => __('Title Position', 'vp_textdomain'),
					'items' => array(
						array(
							'value' => 'left',
							'label' => __('Left', 'vp_textdomain'),
						),
						array(
							'value' => 'right',
							'label' => __('Right', 'vp_textdomain'),
						),
						array(
							'value' => 'center',
							'label' => __('Center', 'vp_textdomain'),
						),
					),
					'default' => array(
						'left',
					),
				),			
				array(
					'type'      => 'textbox',
					'name'      => 'ozy_rosie_meta_page_custom_title',
					'label'     => __('Page Title', 'vp_textdomain'),
				),
				array(
					'type'      => 'color',
					'name'      => 'ozy_rosie_meta_page_custom_title_color',
					'label'     => __('Title Color', 'vp_textdomain'),
					'default' => '',
					'format' => 'rgba'
				),				
				array(
					'type'      => 'textbox',
					'name'      => 'ozy_rosie_meta_page_custom_sub_title',
					'label'     => __('Sub Title', 'vp_textdomain'),
				),
				array(
					'type'      => 'color',
					'name'      => 'ozy_rosie_meta_page_custom_sub_title_color',
					'label'     => __('Sub Title Color', 'vp_textdomain'),
					'default' => '',
					'format' => 'rgba'
				),				
				array(
					'type'      => 'color',
					'name'      => 'ozy_rosie_meta_page_custom_title_bgcolor',
					'label'     => __('Header Background Color', 'vp_textdomain'),
					'default' => '',
					'format' => 'rgba'
				),				
				array(
					'type'      => 'upload',
					'name'      => 'ozy_rosie_meta_page_custom_title_bg',
					'label'     => __('Header Image', 'vp_textdomain'),
					'description'=> __('Please use images like 1600px, 2000px wide and have a minimum height like 475px for good results.', 'vp_textdomain'),
				),
				array(
					'type' => 'radiobutton',
					'name' => 'ozy_rosie_meta_page_custom_title_bg_x_position',
					'label' => __('Background X-Position', 'vp_textdomain'),
					'items' => array(
						array(
							'value' => 'left',
							'label' => __('Left', 'vp_textdomain'),
						),
						array(
							'value' => 'right',
							'label' => __('Right', 'vp_textdomain'),
						),
						array(
							'value' => 'center',
							'label' => __('Center', 'vp_textdomain'),
						),
						array(
							'value' => 'top',
							'label' => __('Top', 'vp_textdomain'),
						),
						array(
							'value' => 'bottom',
							'label' => __('Bottom', 'vp_textdomain'),
						),
					),
					'default' => array(
						'left',
					),
				),
				array(
					'type' => 'radiobutton',
					'name' => 'ozy_rosie_meta_page_custom_title_bg_y_position',
					'label' => __('Background Y-Position', 'vp_textdomain'),
					'items' => array(
						array(
							'value' => 'left',
							'label' => __('Left', 'vp_textdomain'),
						),
						array(
							'value' => 'right',
							'label' => __('Right', 'vp_textdomain'),
						),
						array(
							'value' => 'center',
							'label' => __('Center', 'vp_textdomain'),
						),
						array(
							'value' => 'top',
							'label' => __('Top', 'vp_textdomain'),
						),
						array(
							'value' => 'bottom',
							'label' => __('Bottom', 'vp_textdomain'),
						),
					),
					'default' => array(
						'top',
					),
				),				
				array(
					'type'      => 'textbox',
					'name'      => 'ozy_rosie_meta_page_custom_title_height',
					'label'     => __('Header Height', 'vp_textdomain'),
					'description'=> __('Height of your header in pixels? Don\'t include "px" in the string. e.g. 400', 'vp_textdomain'),
					'default'	=> 100,
					'validation' => 'numeric'
				),				
			),
		),		
		
	
		
		
		array(
			'type' => 'toggle',
			'name' => 'ozy_rosie_meta_page_hide_content',
			'label' => __('Hide Page Content', 'vp_textdomain'),
			'description' => __('Page content will not be shown. Supposed to use with Video backgrounds or Fullscreen sliders.', 'vp_textdomain'),
		),		
		array(
			'type' => 'toggle',
			'name' => 'ozy_rosie_meta_page_use_sidebar',
			'label' => __('Use Custom Sidebar', 'vp_textdomain'),
			'description' => __('You can use custom sidebar individually.', 'vp_textdomain'),
		),
		array(
			'type'      => 'group',
			'repeating' => false,
			'length'    => 1,
			'name'      => 'ozy_rosie_meta_page_sidebar_group',
			'title'     => __('Custom Sidebar', 'vp_textdomain'),
			'dependency' => array(
				'field'    => 'ozy_rosie_meta_page_use_sidebar',
				'function' => 'vp_dep_boolean',
			),
			'fields'    => array(
				array(
					'type' => 'radioimage',
					'name' => 'ozy_rosie_meta_page_sidebar_position',
					'label' => __('Sidebar Position', 'vp_textdomain'),
					'description' => __('Select one of available header type.', 'vp_textdomain'),
					'item_max_width' => '86',
					'items' => array(
						array(
							'value' => 'full',
							'label' => __('No Sidebar', 'vp_textdomain'),
							'img' => OZY_BASE_URL . 'admin/images/full-width.png',
						),
						array(
							'value' => 'left',
							'label' => __('Left Sidebar', 'vp_textdomain'),
							'img' => OZY_BASE_URL . 'admin/images/left-sidebar.png',
						),
						array(
							'value' => 'right',
							'label' => __('Right Sidebar', 'vp_textdomain'),
							'img' => OZY_BASE_URL . 'admin/images/right-sidebar.png',
						)
					),
					'default' => '{{first}}',
				),			
				array(
					'type' => 'select',
					'name' => 'ozy_rosie_meta_page_sidebar',
					'label' => __('Sidebar', 'vp_textdomain'),
					'items' => array(
						'data' => array(
							array(
								'source' => 'function',
								'value' => 'vp_bind_ozy_rosie_sidebars',
							),
						),
					),
				),											
			),
		),
		array(
			'type' => 'toggle',
			'name' => 'ozy_rosie_meta_page_use_custom_style',
			'label' => __('Use Custom Style', 'vp_textdomain'),
			'description' => __('Options to customize your page individually.', 'vp_textdomain'),
		),
		array(
			'type'      => 'group',
			'repeating' => false,
			'length'    => 1,
			'name'      => 'ozy_rosie_meta_page_layout_group',
			'title'     => __('Layout Styling', 'vp_textdomain'),
			'dependency' => array(
				'field'    => 'ozy_rosie_meta_page_use_custom_style',
				'function' => 'vp_dep_boolean',
			),
			'fields'    => array(					
				array(
					'type' => 'color',
					'name' => 'ozy_rosie_meta_page_layout_ascend_background',
					'label' => __('Background Color', 'vp_textdomain'),
					'description' => __('This option will affect, main wrapper\'s background color.', 'vp_textdomain'),
					'default' => 'rgba(255,255,255,1)',
					'format' => 'rgba',
				),
				array(
					'type' => 'toggle',
					'name' => 'ozy_rosie_meta_page_layout_transparent_background',
					'label' => __('Transparent Content Background', 'vp_textdomain'),
					'description' => __('If you want, you can use transparent background for your content.', 'vp_textdomain'),
					'default' => '0',
				)														
			),
		),
		array(
			'type' => 'toggle',
			'name' => 'ozy_rosie_meta_page_use_custom_background',
			'label' => __('Use Custom Background', 'vp_textdomain'),
			'description' => __('Lots of options to customize your page background individually.', 'vp_textdomain'),
		),		
		array(
			'type'      => 'group',
			'repeating' => false,
			'name'      => 'ozy_rosie_meta_page_background_group',
			'title'     => __('Background Styling', 'vp_textdomain'),
			'dependency' => array(
				'field'    => 'ozy_rosie_meta_page_use_custom_background',
				'function' => 'vp_dep_boolean',
			),
			'fields'    => array(					
				array(
					'type' => 'upload',
					'name' => 'ozy_rosie_meta_page_background_image',
					'label' => __('Custom Background Image', 'vp_textdomain'),
					'description' => __('Upload or choose custom page background image.', 'vp_textdomain'),
				),
				array(
					'type' => 'radiobutton',
					'name' => 'ozy_rosie_meta_page_background_image_size',
					'label' => __('Background Image Size', 'vp_textdomain'),
					'description' => __('Only available on browsers which supports CSS3.', 'vp_textdomain'),
					'items' => array(
						array(
							'value' => '',
							'label' => __('-not set-', 'vp_textdomain'),
						),			
						array(
							'value' => 'cover',
							'label' => __('cover', 'vp_textdomain'),
						),
						array(
							'value' => 'contain',
							'label' => __('contain', 'vp_textdomain'),
						)
					),
					'default' => '{{first}}',
				),
				array(
					'type' => 'radiobutton',
					'name' => 'ozy_rosie_meta_page_background_image_repeat',
					'label' => __('Background Image Repeat', 'vp_textdomain'),
					'items' => array(
						array(
							'value' => 'inherit',
							'label' => __('inherit', 'vp_textdomain'),
						),			
						array(
							'value' => 'no-repeat',
							'label' => __('no-repeat', 'vp_textdomain'),
						),
						array(
							'value' => 'repeat',
							'label' => __('repeat', 'vp_textdomain'),
						),
						array(
							'value' => 'repeat-x',
							'label' => __('repeat-x', 'vp_textdomain'),
						),
						array(
							'value' => 'repeat-y',
							'label' => __('repeat-y', 'vp_textdomain'),
						)
					),
					'default' => '{{first}}',
				),
				array(
					'type' => 'radiobutton',
					'name' => 'ozy_rosie_meta_page_background_image_attachment',
					'label' => __('Background Image Attachment', 'vp_textdomain'),
					'items' => array(
						array(
							'value' => '',
							'label' => __('-not set-', 'vp_textdomain'),
						),			
						array(
							'value' => 'fixed',
							'label' => __('fixed', 'vp_textdomain'),
						),
						array(
							'value' => 'scroll',
							'label' => __('scroll', 'vp_textdomain'),
						),
						array(
							'value' => 'local',
							'label' => __('local', 'vp_textdomain')
						)
					),
					'default' => '{{first}}',
				),										
				array(
					'type' => 'color',
					'name' => 'ozy_rosie_meta_page_background_color',
					'label' => __('Background Color', 'vp_textdomain'),
					'description' => __('This option will affect only page background.', 'vp_textdomain'),
					'default' => '#ffffff',
					'format' => 'hex',
				),
				array(
					'type' => 'toggle',
					'name' => 'ozy_rosie_meta_page_background_use_gmap',
					'label' => __('Use Google Map', 'vp_textdomain'),
					'description' => __('Instead of using a static background, you can use a Google Map as background.', 'vp_textdomain'),
				),					
				array(
					'type'      => 'group',
					'repeating' => false,
					'name'      => 'ozy_rosie_meta_page_background_gmap_group',
					'title'     => __('Google Map', 'vp_textdomain'),
					'dependency' => array(
						'field'    => 'ozy_rosie_meta_page_background_use_gmap',
						'function' => 'vp_dep_boolean',
					),
					'fields'    => array(					
						array(
							'type' => 'textbox',
							'name' => 'ozy_rosie_meta_page_background_gmap_address',
							'label' => __('iFrame Src', 'vp_textdomain'),
							'description' => __('Enter src attribute of your Google Map iFrame.', 'vp_textdomain'),
						)												
					),
				),
				array(
					'type' => 'toggle',
					'name' => 'ozy_rosie_meta_page_background_use_slider',
					'label' => __('Use Background Slider', 'vp_textdomain'),
					'description' => __('Instead of using a static background, you can use background image slider.', 'vp_textdomain'),
				),					
				array(
					'type'      => 'group',
					'repeating' => true,
					'sortable' => true,
					'name'      => 'ozy_rosie_meta_page_background_slider_group',
					'title'     => __('Slider Image', 'vp_textdomain'),
					'dependency' => array(
						'field'    => 'ozy_rosie_meta_page_background_use_slider',
						'function' => 'vp_dep_boolean',
					),
					'fields'    => array(					
						array(
							'type' => 'upload',
							'name' => 'ozy_rosie_meta_page_background_slider_image',
							'label' => __('Slider Image', 'vp_textdomain'),
							'description' => __('Upload or choose custom background image.', 'vp_textdomain'),
						)												
					),
				),
				array(
					'type' => 'toggle',
					'name' => 'ozy_rosie_meta_page_background_use_video_self',
					'label' => __('Use Self Hosted Video', 'vp_textdomain'),
					'description' => __('Instead of using a static background, you can use self hosted video.', 'vp_textdomain'),
				),					
				array(
					'type'      => 'group',
					'repeating' => false,
					'sortable' => false,
					'name'      => 'ozy_rosie_meta_page_background_video_self_group',
					'title'     => __('Self Hosted Video', 'vp_textdomain'),
					'dependency' => array(
						'field'    => 'ozy_rosie_meta_page_background_use_video_self',
						'function' => 'vp_dep_boolean',
					),
					'fields'    => array(					
						array(
							'type' => 'upload',
							'name' => 'ozy_rosie_meta_page_background_video_self_image',
							'label' => __('Poster Image', 'vp_textdomain'),
							'description' => __('Upload or choose a poster image.', 'vp_textdomain'),
						),
						array(
							'type' => 'upload',
							'name' => 'ozy_rosie_meta_page_background_video_self_mp4',
							'label' => __('MP4 File', 'vp_textdomain'),
							'description' => __('Upload or choose a MP4 file.', 'vp_textdomain'),
						),
						array(
							'type' => 'upload',
							'name' => 'ozy_rosie_meta_page_background_video_self_webm',
							'label' => __('WEBM File', 'vp_textdomain'),
							'description' => __('Upload or choose a WEBM file.', 'vp_textdomain'),
						),
						array(
							'type' => 'upload',
							'name' => 'ozy_rosie_meta_page_background_video_self_ogv',
							'label' => __('OGV File', 'vp_textdomain'),
							'description' => __('Upload or choose an OGV file.', 'vp_textdomain'),
						)
					),
				),
				array(
					'type' => 'toggle',
					'name' => 'ozy_rosie_meta_page_background_use_video_youtube',
					'label' => __('Use YouTube Video', 'vp_textdomain'),
					'description' => __('Instead of using a static background, you can use YouTube video.', 'vp_textdomain'),
				),					
				array(
					'type'      => 'group',
					'repeating' => false,
					'sortable' => false,
					'name'      => 'ozy_rosie_meta_page_background_video_youtube_group',
					'title'     => __('YouTube Video', 'vp_textdomain'),
					'dependency' => array(
						'field'    => 'ozy_rosie_meta_page_background_use_video_youtube',
						'function' => 'vp_dep_boolean',
					),
					'fields'    => array(					
						array(
							'type' => 'upload',
							'name' => 'ozy_rosie_meta_page_background_video_youtube_image',
							'label' => __('Poster Image', 'vp_textdomain'),
							'description' => __('Upload or choose a poster image.', 'vp_textdomain'),
						),
						array(
							'type' => 'textbox',
							'name' => 'ozy_rosie_meta_page_background_video_youtube_id',
							'label' => __('YouTube Video ID', 'vp_textdomain'),
							'description' => __('Enter YouTube video ID. http://www.youtube.com/watch?v=<span style="color:red;">mYKA-VokOtA</span> text marked with red is the ID you have to be looking for.', 'vp_textdomain'),
						)
					),
				),
				array(
					'type' => 'toggle',
					'name' => 'ozy_rosie_meta_page_background_use_video_vimeo',
					'label' => __('Use Vimeo Video', 'vp_textdomain'),
					'description' => __('Instead of using a static background, you can use Vimeo video.', 'vp_textdomain'),
				),					
				array(
					'type'      => 'group',
					'repeating' => false,
					'sortable' => false,
					'name'      => 'ozy_rosie_meta_page_background_video_vimeo_group',
					'title'     => __('Vimeo Video', 'vp_textdomain'),
					'dependency' => array(
						'field'    => 'ozy_rosie_meta_page_background_use_video_vimeo',
						'function' => 'vp_dep_boolean',
					),
					'fields'    => array(					
						array(
							'type' => 'upload',
							'name' => 'ozy_rosie_meta_page_background_video_vimeo_image',
							'label' => __('Poster Image', 'vp_textdomain'),
							'description' => __('Upload or choose a poster image.', 'vp_textdomain'),
						),
						array(
							'type' => 'textbox',
							'name' => 'ozy_rosie_meta_page_background_video_vimeo_id',
							'label' => __('Vimeo Video ID', 'vp_textdomain'),
							'description' => __('Enter Vimeo video ID. http://vimeo.com/<span style="color:red;">71964690</span> text marked with red is the ID you have to be looking for.', 'vp_textdomain'),
						)
					),
				)
			),
		),
		array(
			'type' => 'radiobutton',
			'name' => 'ozy_rosie_meta_page_page_model',
			'label' => __('Default Page Model', 'vp_textdomain'),
			'items' => array(
				array(
					'value' => 'generic',
					'label' => __('Use From Theme Options', 'vp_textdomain'),
				),			
				array(
					'value' => 'boxed',
					'label' => __('Boxed', 'vp_textdomain'),
				),
				array(
					'value' => 'full',
					'label' => __('Full', 'vp_textdomain'),
				),
			),
			'default' => array(
				'{{first}}',
			),
		)				
	),	
);

/**
 * EOF
 */