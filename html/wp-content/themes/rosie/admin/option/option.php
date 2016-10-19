<?php

//return 
$ozy_rosie_option_arr = array(
	'title' => __('ROSIE Option Panel', 'vp_textdomain'),
	'logo' => OZY_BASE_URL . 'admin/images/logo.png',
	'menus' => array(
		array(
			'title' => __('General Options', 'vp_textdomain'),
			'name' => 'ozy_rosie_general_options',
			'icon' => 'font-awesome:fa-gear',
			'controls' => array(
				array(
					'type' => 'section',
					'title' => __('General', 'vp_textdomain'),
					'fields' => array(
						array(
							'type' => 'toggle',
							'name' => 'ozy_rosie_back_to_top_button',
							'label' => __('Back To Top Button', 'vp_textdomain'),
							'description' => __('Enable / Disable Back To Top Button globally.', 'vp_textdomain'),
							'default' => '1',
						),						
						array(
							'type' => 'upload',
							'name' => 'ozy_rosie_favicon',
							'label' => __('Favicon', 'vp_textdomain'),
							'description' => __('Upload a 16px x 16px .png or .gif image, will be set as your favicon.', 'vp_textdomain'),
							'default' => get_stylesheet_directory_uri() . '/favico.png',
						),
						array(
							'type' => 'upload',
							'name' => 'ozy_rosie_favicon_apple_small',
							'label' => __('Apple Touch Icon (small)', 'vp_textdomain'),
							'description' => __('Upload a 57px x 57px .png image, will be set as your small Apple Touch Icon.', 'vp_textdomain'),
							'default' => get_stylesheet_directory_uri() . '/images/favico_57.png',
						),array(
							'type' => 'upload',
							'name' => 'ozy_rosie_favicon_apple_medium',
							'label' => __('Apple Touch Icon (medium)', 'vp_textdomain'),
							'description' => __('Upload a 76px x 76px .png image, will be set as your large Apple Touch Icon (iPad).', 'vp_textdomain'),
							'default' => get_stylesheet_directory_uri() . '/images/favico_76.png',
						),array(
							'type' => 'upload',
							'name' => 'ozy_rosie_favicon_apple_large',
							'label' => __('Apple Touch Icon (large)', 'vp_textdomain'),
							'description' => __('Upload a 120px x 120px .png image, will be set as your large Apple Touch Icon (iPhone Retina).', 'vp_textdomain'),
							'default' => get_stylesheet_directory_uri() . '/images/favico_120.png',
						),array(
							'type' => 'upload',
							'name' => 'ozy_rosie_favicon_apple_xlarge',
							'label' => __('Apple Touch Icon (large)', 'vp_textdomain'),
							'description' => __('Upload a 152px x 152px .png image, will be set as your large Apple Touch Icon (iPad Retina).', 'vp_textdomain'),
							'default' => get_stylesheet_directory_uri() . '/images/favico_152.png',
						),
						array(
							'type' => 'codeeditor',
							'name' => 'ozy_rosie_custom_css',
							'label' => __('Custom CSS', 'vp_textdomain'),
							'description' => __('Write your custom css here. <strong>Please do not add "style" tags.</strong>', 'vp_textdomain'),
							'theme' => 'eclipse',
							'mode' => 'css',
						),
						array(
							'type' => 'codeeditor',
							'name' => 'ozy_rosie_custom_script',
							'label' => __('Custom JS', 'vp_textdomain'),
							'description' => __('Write your custom js here. Please do not add script tags into this box. <strong>Please do not add "script" tags.</strong>', 'vp_textdomain'),
							'theme' => 'mono_industrial',
							'mode' => 'javascript',
						),
					),
				),
			),
		),
		
		
		array(
			'title' => __('Typography', 'vp_textdomain'),
			'name' => 'ozy_rosie_typography',
			'icon' => 'font-awesome:fa-pencil',
			'controls' => array(
				array(
					'type' => 'section',
					'title' => __('Extended Parameters', 'vp_textdomain'),
					'fields' => array(
						array(
							'type' => 'textbox',
							'name' => 'ozy_rosie_typography_google_param',
							'description' => 'Add extra parameters here. By this option, you can load non-latin charset or more types byt available parameters. Use like ":400,100,300,700".',
							'default' => ':400,100,300,700'
						),
					)
				),			
				array(
					'type' => 'section',
					'title' => __('Content Typography', 'vp_textdomain'),
					'fields' => array(
						array(
							'type' => 'html',
							'name' => 'ozy_rosie_typography_font_preview',
							'binding' => array(
								'field'    => 'ozy_rosie_typography_font_face,ozy_rosie_typography_font_style,ozy_rosie_typography_font_weight,ozy_rosie_typography_font_size, ozy_rosie_typography_font_line_height',
								'function' => 'vp_font_preview',
							),
						),
						array(
							'type' => 'select',
							'name' => 'ozy_rosie_typography_font_face',
							'label' => __('Font Face', 'vp_textdomain'),
							'items' => array(
								'data' => array(
									array(
										'source' => 'function',
										'value' => 'vp_get_gwf_family',
									),
								),
							),
							'default' => 'Raleway'
						),
						array(
							'type' => 'radiobutton',
							'name' => 'ozy_rosie_typography_font_style',
							'label' => __('Font Style', 'vp_textdomain'),
							'items' => array(
								'data' => array(
									array(
										'source' => 'binding',
										'field' => 'ozy_rosie_typography_font_face',
										'value' => 'vp_get_gwf_style',
									),
								),
							),
							'default' => array(
								'normal',
							),
						),
						array(
							'type' => 'radiobutton',
							'name' => 'ozy_rosie_typography_font_weight',
							'label' => __('Font Weight', 'vp_textdomain'),
							'default' => 'normal',
							'items' => array(
								'data' => array(
									array(
										'source' => 'binding',
										'field' => 'ozy_rosie_typography_font_face',
										'value' => 'vp_get_gwf_weight',
									),
								),
							),
						),
						array(
							'type'    => 'slider',
							'name'    => 'ozy_rosie_typography_font_size',
							'label'   => __('Font Size (px)', 'vp_textdomain'),
							'min'     => '5',
							'max'     => '86',
							'default' => '14',
						),
						array(
							'type'    => 'slider',
							'name'    => 'ozy_rosie_typography_font_line_height',
							'label'   => __('Line Height (em)', 'vp_textdomain'),
							'min'     => '0',
							'max'     => '3',
							'default' => '1.5',
							'step'    => '0.1',
						),
					),
				),
				array(
					'type' => 'section',
					'title' => __('Heading Typography', 'vp_textdomain'),
					'fields' => array(
						array(
							'type' => 'html',
							'name' => 'ozy_rosie_typography_heading_font_preview',
							'binding' => array(
								'field'    => 'ozy_rosie_typography_heading_font_face,ozy_rosie_typography_heading_font_style,ozy_rosie_typography_heading_font_weight,ozy_rosie_typography_heading_h1_font_size',
								'function' => 'vp_font_preview_simple',
							),
						),
						array(
							'type' => 'select',
							'name' => 'ozy_rosie_typography_heading_font_face',
							'label' => __('Font Face', 'vp_textdomain'),
							'items' => array(
								'data' => array(
									array(
										'source' => 'function',
										'value' => 'vp_get_gwf_family',
									),
								),
							),
							'default' => 'Raleway'
						),
						array(
							'type' => 'radiobutton',
							'name' => 'ozy_rosie_typography_heading_font_style',
							'label' => __('Font Style', 'vp_textdomain'),
							'items' => array(
								'data' => array(
									array(
										'source' => 'binding',
										'field' => 'ozy_rosie_typography_heading_font_face',
										'value' => 'vp_get_gwf_style',
									),
								),
							),
							'default' => array(
								'normal',
							),
						),
						array(
							'type'    => 'slider',
							'name'    => 'ozy_rosie_typography_heading_h1_font_size',
							'label'   => __('H1 Size (px)', 'vp_textdomain'),
							'min'     => '5',
							'max'     => '128',
							'default' => '40',
						),
						array(
							'type' => 'select',
							'name' => 'ozy_rosie_typography_heading_font_weight_h1',
							'label' => __('H1 Weight', 'vp_textdomain'),
							'items' => array(
								'data' => array(
									array(
										'source' => 'function',
										'value' => 'vp_get_font_weight_list',
									),
								),
							),
						),
						array(
							'type'    => 'slider',
							'name'    => 'ozy_rosie_typography_heading_line_height_h1',
							'label'   => __('H1 Line Height (em)', 'vp_textdomain'),
							'min'     => '0',
							'max'     => '3',
							'default' => '1.5',
							'step'    => '0.1',
						),					
						array(
							'type' => 'select',
							'name' => 'ozy_rosie_typography_heading_font_ls_h1',
							'label' => __('H1 Letter Spacing', 'vp_textdomain'),
							'default' => 'normal',							
							'items' => array(
								'data' => array(
									array(
										'source' => 'function',
										'value' => 'vp_get_font_letter_spacing_list',
									),
								),
							),
						),									
						array(
							'type'    => 'slider',
							'name'    => 'ozy_rosie_typography_heading_h2_font_size',
							'label'   => __('H2 Size (px)', 'vp_textdomain'),
							'min'     => '5',
							'max'     => '128',
							'default' => '30',
						),
						array(
							'type' => 'select',
							'name' => 'ozy_rosie_typography_heading_font_weight_h2',
							'label' => __('H2 Weight', 'vp_textdomain'),
							'items' => array(
								'data' => array(
									array(
										'source' => 'function',
										'value' => 'vp_get_font_weight_list',
									),
								),
							),
						),
						array(
							'type'    => 'slider',
							'name'    => 'ozy_rosie_typography_heading_line_height_h2',
							'label'   => __('H2 Line Height (em)', 'vp_textdomain'),
							'min'     => '0',
							'max'     => '3',
							'default' => '1.5',
							'step'    => '0.1',
						),						
						array(
							'type' => 'select',
							'name' => 'ozy_rosie_typography_heading_font_ls_h2',
							'label' => __('H2 Letter Spacing', 'vp_textdomain'),
							'default' => 'normal',
							'items' => array(
								'data' => array(
									array(
										'source' => 'function',
										'value' => 'vp_get_font_letter_spacing_list',
									),
								),
							),
						),
						array(
							'type'    => 'slider',
							'name'    => 'ozy_rosie_typography_heading_h3_font_size',
							'label'   => __('H3 Size (px)', 'vp_textdomain'),
							'min'     => '5',
							'max'     => '128',
							'default' => '22',
						),
						array(
							'type' => 'select',
							'name' => 'ozy_rosie_typography_heading_font_weight_h3',
							'label' => __('H3 Weight', 'vp_textdomain'),
							'items' => array(
								'data' => array(
									array(
										'source' => 'function',
										'value' => 'vp_get_font_weight_list',
									),
								),
							),
						),
						array(
							'type'    => 'slider',
							'name'    => 'ozy_rosie_typography_heading_line_height_h3',
							'label'   => __('H3 Line Height (em)', 'vp_textdomain'),
							'min'     => '0',
							'max'     => '3',
							'default' => '1.5',
							'step'    => '0.1',
						),						
						array(
							'type' => 'select',
							'name' => 'ozy_rosie_typography_heading_font_ls_h3',
							'label' => __('H3 Letter Spacing', 'vp_textdomain'),
							'default' => 'normal',							
							'items' => array(
								'data' => array(
									array(
										'source' => 'function',
										'value' => 'vp_get_font_letter_spacing_list',
									),
								),
							),
						),
						array(
							'type'    => 'slider',
							'name'    => 'ozy_rosie_typography_heading_h4_font_size',
							'label'   => __('H4 Size (px)', 'vp_textdomain'),
							'min'     => '5',
							'max'     => '128',
							'default' => '18',
						),
						array(
							'type' => 'select',
							'name' => 'ozy_rosie_typography_heading_font_weight_h4',
							'label' => __('H4 Weight', 'vp_textdomain'),
							'items' => array(
								'data' => array(
									array(
										'source' => 'function',
										'value' => 'vp_get_font_weight_list',
									),
								),
							),
						),
						array(
							'type'    => 'slider',
							'name'    => 'ozy_rosie_typography_heading_line_height_h4',
							'label'   => __('H4 Line Height (em)', 'vp_textdomain'),
							'min'     => '0',
							'max'     => '3',
							'default' => '1.5',
							'step'    => '0.1',
						),						
						array(
							'type' => 'select',
							'name' => 'ozy_rosie_typography_heading_font_ls_h4',
							'label' => __('H4 Letter Spacing', 'vp_textdomain'),
							'default' => 'normal',							
							'items' => array(
								'data' => array(
									array(
										'source' => 'function',
										'value' => 'vp_get_font_letter_spacing_list',
									),
								),
							),
						),
						array(
							'type'    => 'slider',
							'name'    => 'ozy_rosie_typography_heading_h5_font_size',
							'label'   => __('H5 Size (px)', 'vp_textdomain'),
							'min'     => '5',
							'max'     => '128',
							'default' => '16',
						),
						array(
							'type' => 'select',
							'name' => 'ozy_rosie_typography_heading_font_weight_h5',
							'label' => __('H5 Weight', 'vp_textdomain'),
							'items' => array(
								'data' => array(
									array(
										'source' => 'function',
										'value' => 'vp_get_font_weight_list',
									),
								),
							),
						),
						array(
							'type'    => 'slider',
							'name'    => 'ozy_rosie_typography_heading_line_height_h5',
							'label'   => __('H5 Line Height (em)', 'vp_textdomain'),
							'min'     => '0',
							'max'     => '3',
							'default' => '1.5',
							'step'    => '0.1',
						),						
						array(
							'type' => 'select',
							'name' => 'ozy_rosie_typography_heading_font_ls_h5',
							'label' => __('H5 Letter Spacing', 'vp_textdomain'),
							'default' => 'normal',							
							'items' => array(
								'data' => array(
									array(
										'source' => 'function',
										'value' => 'vp_get_font_letter_spacing_list',
									),
								),
							),
						),
						array(
							'type'    => 'slider',
							'name'    => 'ozy_rosie_typography_heading_h6_font_size',
							'label'   => __('H6 Size (px)', 'vp_textdomain'),
							'min'     => '5',
							'max'     => '128',
							'default' => '14',
						),
						array(
							'type' => 'select',
							'name' => 'ozy_rosie_typography_heading_font_weight_h6',
							'label' => __('H6 Weight', 'vp_textdomain'),
							'items' => array(
								'data' => array(
									array(
										'source' => 'function',
										'value' => 'vp_get_font_weight_list',
									),
								),
							),
						),
						array(
							'type'    => 'slider',
							'name'    => 'ozy_rosie_typography_heading_line_height_h6',
							'label'   => __('H6 Line Height (em)', 'vp_textdomain'),
							'min'     => '0',
							'max'     => '3',
							'default' => '1.5',
							'step'    => '0.1',
						),						
						array(
							'type' => 'select',
							'name' => 'ozy_rosie_typography_heading_font_ls_h6',
							'label' => __('H6 Letter Spacing', 'vp_textdomain'),
							'default' => 'normal',
							'items' => array(
								'data' => array(
									array(
										'source' => 'function',
										'value' => 'vp_get_font_letter_spacing_list',
									),
								),
							),
						),						
					),
				),
				

				array(
					'type' => 'section',
					'title' => __('Primary Menu Typography', 'vp_textdomain'),
					'name' => 'ozy_rosie_primary_menu_section_typography',
					'fields' => array(
						array(
							'type' => 'select',
							'name' => 'ozy_rosie_primary_menu_typography_font_face',
							'label' => __('Font Face', 'vp_textdomain'),
							'items' => array(
								'data' => array(
									array(
										'source' => 'function',
										'value' => 'vp_get_gwf_family',
									),
								),
							),
							'default' => 'Raleway'
						),
						array(
							'type' => 'radiobutton',
							'name' => 'ozy_rosie_primary_menu_typography_font_style',
							'label' => __('Font Style', 'vp_textdomain'),
							'items' => array(
								'data' => array(
									array(
										'source' => 'binding',
										'field' => 'ozy_rosie_primary_menu_typography_font_face',
										'value' => 'vp_get_gwf_style',
									),
								),
							),
							'default' => array(
								'normal',
							),
						),
						array(
							'type' => 'radiobutton',
							'name' => 'ozy_rosie_primary_menu_typography_font_weight',
							'label' => __('Font Weight', 'vp_textdomain'),
							'items' => array(
								'data' => array(
									array(
										'source' => 'binding',
										'field' => 'ozy_rosie_primary_menu_typography_font_face',
										'value' => 'vp_get_gwf_weight',
									),
								),
							),
							'default' => array(
								'600',
							),
						),
						array(
							'type'    => 'slider',
							'name'    => 'ozy_rosie_primary_menu_typography_font_size',
							'label'   => __('Font Size (px)', 'vp_textdomain'),
							'min'     => '5',
							'max'     => '128',
							'default' => '14',
						),
						array(
							'type'    => 'slider',
							'name'    => 'ozy_rosie_primary_menu_typography_line_height',
							'label'   => __('Line Height (em)', 'vp_textdomain'),
							'min'     => '0',
							'max'     => '3',
							'default' => '1.5',
							'step'    => '0.1',
						),
					),
				),
								
			),
		),
		
				
		array(
			'title' => __('Layout', 'vp_textdomain'),
			'name' => 'ozy_rosie_layout',
			'icon' => 'font-awesome:fa-magic',
			'menus' => array(
				array(
					'title' => __('Primary Menu / Logo', 'vp_textdomain'),
					'name' => 'ozy_rosie_primary_menu',
					'icon' => 'font-awesome:fa-cogs',
					'controls' => array(
					
						array(
							'type' => 'section',
							'title' => __('Primary Menu', 'vp_textdomain'),
							'name' => 'ozy_rosie_section_header_layout',
							'fields' => array(			
								array(
									'type' => 'radiobutton',
									'name' => 'ozy_rosie_primary_menu_type',
									'label' => __('Primary Menu Layout', 'vp_textdomain'),
									'items' => array(
										array(
											'value' => 'classic',
											'label' => __('Classic Dropdown', 'vp_textdomain'),
										),
										array(
											'value' => 'mega',
											'label' => __('Mega Menu', 'vp_textdomain'),
										),
									),
									'default' => array(
										'mega',
									),
								),
								array(
									'type'    => 'slider',
									'name'    => 'ozy_rosie_primary_menu_height',
									'label'   => __('Menu / Logo Height', 'vp_textdomain'),
									'description'   => __('Set this value to fit at least same as your logo height for perfect results', 'vp_textdomain'),
									'min'     => '40',
									'max'     => '500',
									'default' => '60',
								),								
								array(
									'type' => 'radiobutton',
									'name' => 'ozy_rosie_primary_menu_search',
									'label' => __('Search Button / Box', 'vp_textdomain'),
									'items' => array(
										array(
											'value' => '1',
											'label' => __('On', 'vp_textdomain'),
										),
										array(
											'value' => '-1',
											'label' => __('Off', 'vp_textdomain'),
										)
									),
									'default' => array(
										'1',
									),
								),
								array(
									'type' => 'radiobutton',
									'name' => 'ozy_rosie_primary_menu_side_menu',
									'label' => __('Side Menu Menu', 'vp_textdomain'),
									'description' => __('Disabling this will not disable mobile menu, only on desktop mode', 'vp_textdomain'),
									'items' => array(
										array(
											'value' => '1',
											'label' => __('On', 'vp_textdomain'),
										),
										array(
											'value' => '-1',
											'label' => __('Off', 'vp_textdomain'),
										)
									),
									'default' => array(
										'1',
									),
								),								
								array(
									'type' => 'radiobutton',
									'name' => 'ozy_rosie_primary_menu_align',
									'label' => __('Menu Align', 'vp_textdomain'),
									'description' => __('Only available for Classic and Mega Menu layouts', 'vp_textdomain'),
									'items' => array(
										'data' => array(
											array(
												'source' => 'binding',
												'field' => 'ozy_rosie_primary_menu_type',
												'value' => 'vp_bind_ozy_rosie_align_by_menu_type',
											),
										),										
									),
									'validation' => 'required',
									'default' => array(
										'right',
									),								
								),
								array(
									'type' => 'radiobutton',
									'name' => 'ozy_rosie_primary_menu_infobar_align',
									'label' => __('Top Information Bar', 'vp_textdomain'),
									'description' => __('This place is a Sidebar. To manage it <a href="widgets.php#ozy-header-information">Appearance > Widgets : Header Information</a>', 'vp_textdomain'),
									'items' => array(
										array(
											'value' => 'hidden',
											'label' => __('Hidden', 'vp_textdomain'),
										),
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
										)
									),
									'default' => array(
										'hidden',
									),
								),
							),
						),
						array(
							'type' => 'section',
							'title' => __('Logo', 'vp_textdomain'),
							'name' => 'ozy_rosie_section_image_logo',
							'description' => __('You can use custom image logo for your site. To use this option, first activate \'Use Custom Logo\' switch', 'vp_textdomain'),
							'fields' => array(				
								array(
									'type' => 'toggle',
									'name' => 'ozy_rosie_use_custom_logo',
									'label' => __('Use Custom Logo', 'vp_textdomain'),
									'default' => 1,
									'description' => __('Use custom logo or text logo', 'vp_textdomain'),
								),
								array(
									'type' => 'upload',
									'name' => 'ozy_rosie_custom_logo',
									'label' => __('Custom Logo', 'vp_textdomain'),
									'default' => OZY_BASE_URL . 'images/logo.png',
									'dependency' => array(
										'field' => 'ozy_rosie_use_custom_logo',
										'function' => 'vp_dep_boolean',
									),
									'description' => __('Upload or choose custom logo', 'vp_textdomain'),
								),
								array(
									'type' => 'upload',
									'name' => 'ozy_rosie_custom_logo_alternate',
									'label' => __('Custom Logo (Alternate)', 'vp_textdomain'),
									'default' => OZY_BASE_URL . 'images/logo-alternate.png',
									'dependency' => array(
										'field' => 'ozy_rosie_use_custom_logo',
										'function' => 'vp_dep_boolean',
									),
									'description' => __('Upload or choose custom logo', 'vp_textdomain'),
								),								
								array(
									'type' => 'upload',
									'name' => 'ozy_rosie_custom_logo_retina',
									'label' => __('Custom Logo Retina', 'vp_textdomain'),
									'default' => OZY_BASE_URL . 'images/logo@2x.png',
									'dependency' => array(
										'field' => 'ozy_rosie_use_custom_logo',
										'function' => 'vp_dep_boolean',
									),
									'description' => __('Upload or choose custom 2x bigger logo', 'vp_textdomain'),
								),
								array(
									'type' => 'upload',
									'name' => 'ozy_rosie_custom_logo_retina_alternate',
									'label' => __('Custom Logo Retina (Alternate)', 'vp_textdomain'),
									'default' => OZY_BASE_URL . 'images/logo-alternate@2x.png',
									'dependency' => array(
										'field' => 'ozy_rosie_use_custom_logo',
										'function' => 'vp_dep_boolean',
									),
									'description' => __('Upload or choose custom 2x bigger logo', 'vp_textdomain'),
								),								
							),
						),						
					),
				),
				
				
				array(
					'title' => __('Footer', 'vp_textdomain'),
					'name' => 'ozy_rosie_footer',
					'icon' => 'font-awesome:fa-cog',
					'controls' => array(
						array(
							'type' => 'section',
							'title' => __('Footer Layout', 'vp_textdomain'),
							'name' => 'ozy_rosie_section_footer_layout',
							'fields' => array(
								array(
									'type' => 'slider',
									'name' => 'ozy_rosie_footer_height',
									'label' => __('Footer Height', 'vp_textdomain'),
									'description' => __('Select height of your footer. Minimum value set to 30 and maximum set to 360. Will be processed in pixels.', 'vp_textdomain'),
									'min' => '30',
									'max' => '360',
									'step' => '1',
									'default' => '56',
								),
							),
						),
												
					),
				),				
				

				array(
					'title' => __('Content / Page / Post', 'vp_textdomain'),
					'name' => 'ozy_rosie_page',
					'icon' => 'font-awesome:fa-pencil',
					'controls' => array(
						array(
							'type' => 'section',
							'title' => __('Page Position / Layout', 'vp_textdomain'),
							'name' => 'ozy_rosie_page_section_position',
							'description' => __('Select position for your page content', 'vp_textdomain'),
							'fields' => array(
								/*array(
									'type' => 'radiobutton',
									'name' => 'ozy_rosie_page_width',
									'label' => __('Default Width', 'vp_textdomain'),
									'description' => __('Only available for desktop resolations.', 'vp_textdomain'),
									'items' => array(
										array(
											'value' => '1140',
											'label' => __('1140px', 'vp_textdomain'),
										),
										array(
											'value' => '960',
											'label' => __('960px', 'vp_textdomain'),
										),
										array(
											'value' => '861',
											'label' => __('861px', 'vp_textdomain'),
										),
									),
									'default' => array(
										'1140',
									),
								),*/
								array(
									'type' => 'radiobutton',
									'name' => 'ozy_rosie_page_model',
									'label' => __('Default Page Model', 'vp_textdomain'),
									'items' => array(
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
										'full',
									),
								),																								
							),
						),
						array(
							'type' => 'section',
							'title' => __('Custom 404 Page', 'vp_textdomain'),
							'name' => 'ozy_rosie_page_section_404_page',
							'description' => __('Select a page to use as your custom 404 (not found) page', 'vp_textdomain'),
							'fields' => array(
								array(
									'type' => 'select',
									'name' => 'ozy_rosie_page_404_page_id',
									'label' => __('Custom 404 Page', 'vp_textdomain'),
									'description' => __('Select a page to use as custom 4040 page.', 'vp_textdomain'),
									'items' => array(
										'data' => array(
											array(
												'source' => 'function',
												'value' => 'vp_bind_ozy_rosie_pages',
											),
										),
									),
								)													
							),
						),							
						array(
							'type' => 'section',
							'title' => __('Page', 'vp_textdomain'),
							'name' => 'ozy_rosie_page_section_page_sidebar_position',
							'description' => __('Select position for your page sidebar', 'vp_textdomain'),
							'fields' => array(
								array(
									'type' => 'radioimage',
									'name' => 'ozy_rosie_page_page_sidebar_position',
									'label' => __('Default Sidebar Position', 'vp_textdomain'),
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
									'default' => array(
										'{{first}}',
									),
								),
								array(
									'type' => 'select',
									'name' => 'ozy_rosie_page_page_sidebar_id',
									'label' => __('Default Sidebar', 'vp_textdomain'),
									'description' => __('This option could be overriden individually.', 'vp_textdomain'),
									'items' => array(
										'data' => array(
											array(
												'source' => 'function',
												'value' => 'vp_bind_ozy_rosie_sidebars',
											),
										),
									),
								),
								array(
									'type' => 'toggle',
									'name' => 'ozy_rosie_page_page_comment',
									'label' => __('Comments Section', 'vp_textdomain'),
									'description' => __('Enable / Disable comment section on the pages', 'vp_textdomain'),
									'default' => '0',
								),
								array(
									'type' => 'toggle',
									'name' => 'ozy_rosie_page_page_author',
									'label' => __('Author Section', 'vp_textdomain'),
									'description' => __('Enable / Disable author section on the pages', 'vp_textdomain'),
									'default' => '0',
								),
								array(
									'type' => 'toggle',
									'name' => 'ozy_rosie_page_page_comment_closed',
									'label' => __('Show Comments Closed Message', 'vp_textdomain'),
									'description' => __('Whenever comments closed on a page or post a message appears, you can hide it.', 'vp_textdomain'),
									'default' => '0',
								),	
								array(
									'type' => 'toggle',
									'name' => 'ozy_rosie_page_page_share',
									'label' => __('Share Buttons', 'vp_textdomain'),
									'description' => __('Enable / Disable share buttons for pages.', 'vp_textdomain'),
									'default' => '0',
								)															
							),
						),
						array(
							'type' => 'section',
							'title' => __('Blog', 'vp_textdomain'),
							'name' => 'ozy_rosie_page_section_blog_sidebar_position',
							'description' => __('Select position for your blog page sidebar', 'vp_textdomain'),
							'fields' => array(
								array(
									'type' => 'radioimage',
									'name' => 'ozy_rosie_page_blog_sidebar_position',
									'label' => __('Defaul Sidebar Position', 'vp_textdomain'),
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
									'default' => array(
										'{{first}}',
									),
								),
								array(
									'type' => 'select',
									'name' => 'ozy_rosie_page_blog_sidebar_id',
									'label' => __('Default Sidebar', 'vp_textdomain'),
									'description' => __('This option could be overriden individually.', 'vp_textdomain'),
									'items' => array(
										'data' => array(
											array(
												'source' => 'function',
												'value' => 'vp_bind_ozy_rosie_sidebars',
											),
										),
									),
								),
								array(
									'type' => 'toggle',
									'name' => 'ozy_rosie_page_blog_comment',
									'label' => __('Comments Section', 'vp_textdomain'),
									'description' => __('Enable / Disable comment section on the blog posts', 'vp_textdomain'),
									'default' => '1',
								),
								array(
									'type' => 'toggle',
									'name' => 'ozy_rosie_page_blog_author',
									'label' => __('Author Section', 'vp_textdomain'),
									'description' => __('Enable / Disable author section on the blog posts', 'vp_textdomain'),
									'default' => '1',
								),
								array(
									'type' => 'toggle',
									'name' => 'ozy_rosie_page_blog_share',
									'label' => __('Share Buttons', 'vp_textdomain'),
									'description' => __('Enable / Disable share buttons for posts.', 'vp_textdomain'),
									'default' => '1',
								),
								array(
									'type' => 'toggle',
									'name' => 'ozy_rosie_page_blog_related_posts',
									'label' => __('Related Posts', 'vp_textdomain'),
									'description' => __('Enable / Disable related posts.', 'vp_textdomain'),
									'default' => '1',
								),
								array(
									'type' => 'select',
									'name' => 'ozy_rosie_page_blog_list_page_id',
									'label' => __('Default Listing Page', 'vp_textdomain'),
									'description' => __('Select a page to use as "Return to Blog" link.', 'vp_textdomain'),
									'items' => array(
										'data' => array(
											array(
												'source' => 'function',
												'value' => 'vp_bind_ozy_rosie_pages',
											),
										),
									),
								)											
							),
						),
						array(
							'type' => 'section',
							'title' => __('Portfolio', 'vp_textdomain'),
							'name' => 'ozy_rosie_page_section_portfolio_sidebar_position',
							'description' => __('Select position for your portfolio page sidebar', 'vp_textdomain'),
							'fields' => array(
								array(
									'type' => 'radioimage',
									'name' => 'ozy_rosie_page_portfolio_sidebar_position',
									'label' => __('Default Sidebar Position', 'vp_textdomain'),
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
									'default' => array(
										'{{first}}',
									),
								),
								array(
									'type' => 'select',
									'name' => 'ozy_rosie_page_portfolio_sidebar_id',
									'label' => __('Default Sidebar', 'vp_textdomain'),
									'description' => __('This option could be overriden individually.', 'vp_textdomain'),
									'items' => array(
										'data' => array(
											array(
												'source' => 'function',
												'value' => 'vp_bind_ozy_rosie_sidebars',
											),
										),
									),
								),
								array(
									'type' => 'toggle',
									'name' => 'ozy_rosie_page_portfolio_comment',
									'label' => __('Comments Section', 'vp_textdomain'),
									'description' => __('Enable / Disable comment section on the portfolio posts', 'vp_textdomain'),
									'default' => '0',
								),
								array(
									'type' => 'toggle',
									'name' => 'ozy_rosie_page_portfolio_author',
									'label' => __('Author Section', 'vp_textdomain'),
									'description' => __('Enable / Disable author section on the portfolio posts', 'vp_textdomain'),
									'default' => '0',
								),
								array(
									'type' => 'toggle',
									'name' => 'ozy_rosie_page_portfolio_share',
									'label' => __('Share Buttons', 'vp_textdomain'),
									'description' => __('Enable / Disable share buttons for portfolio.', 'vp_textdomain'),
									'default' => '0',
								),
								array(
									'type' => 'toggle',
									'name' => 'ozy_rosie_page_portfolio_related_posts',
									'label' => __('Related Posts', 'vp_textdomain'),
									'description' => __('Enable / Disable related posts.', 'vp_textdomain'),
									'default' => '1',
								),
								array(
									'type' => 'select',
									'name' => 'ozy_rosie_page_portfolio_list_page_id',
									'label' => __('Default Listing Page', 'vp_textdomain'),
									'description' => __('Select a page to use as "All Projects" link.', 'vp_textdomain'),
									'items' => array(
										'data' => array(
											array(
												'source' => 'function',
												'value' => 'vp_bind_ozy_rosie_pages',
											),
										),
									),
								)													
							),
						),





						/*array(
							'type' => 'section',
							'title' => __('Video Gallery', 'vp_textdomain'),
							'name' => 'ozy_rosie_page_section_video_gallery_sidebar_position',
							'description' => __('Select position for your video gallery page sidebar', 'vp_textdomain'),
							'fields' => array(
								array(
									'type' => 'select',
									'name' => 'ozy_rosie_page_video_gallery_list_page_id',
									'label' => __('Default Gallery Page', 'vp_textdomain'),
									'description' => __('Select a page to use as "Return to Gallery" link.', 'vp_textdomain'),
									'items' => array(
										'data' => array(
											array(
												'source' => 'function',
												'value' => 'vp_bind_ozy_rosie_pages',
											),
										),
									),
								)													
							),
						),*/




						
						array(
							'type' => 'section',
							'title' => __('WooCommerce', 'vp_textdomain'),
							'name' => 'ozy_rosie_page_section_woocommerce_sidebar_position',
							'description' => __('Select position for your WooCommerce page sidebar', 'vp_textdomain'),
							'fields' => array(
								array(
									'type' => 'radioimage',
									'name' => 'ozy_rosie_page_woocommerce_sidebar_position',
									'label' => __('Default Sidebar Position', 'vp_textdomain'),
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
									'default' => array(
										'{{first}}',
									),
								),
								array(
									'type' => 'select',
									'name' => 'ozy_rosie_page_woocommerce_sidebar_id',
									'label' => __('Default Sidebar', 'vp_textdomain'),
									'description' => __('This option could be overriden individually.', 'vp_textdomain'),
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
					),
				),	
				
				
				array(
					'title' => __('Miscellaneous', 'vp_textdomain'),
					'name' => 'ozy_rosie_misc',
					'icon' => 'font-awesome:fa-puzzle-piece',
					'controls' => array(
						array(
							'type' => 'section',
							'title' => __('Fancy Box (Lightbox)', 'vp_textdomain'),
							'name' => 'ozy_rosie_section_fancybox_layout',
							'fields' => array(
								array(
									'type' => 'toggle',
									'name' => 'ozy_rosie_fancbox_media',
									'label' => __('Video Support', 'vp_textdomain'),
									'description' => __('By enabling this option Fancybox will start to support popular media links.', 'vp_textdomain'),
									'default' => '1',
								),
								array(
									'type' => 'toggle',
									'name' => 'ozy_rosie_fancbox_thumbnail',
									'label' => __('Thumbnail', 'vp_textdomain'),
									'description' => __('Enable this option to show thumnails under your Fancybox window.', 'vp_textdomain'),
									'default' => '0',
								),								
							),
						),
					),
				),



				array(
					'title' => __('Countdown Page', 'vp_textdomain'),
					'name' => 'ozy_rosie_countdown',
					'icon' => 'font-awesome:fa-clock-o',
					'controls' => array(
						array(
							'type' => 'section',
							'title' => __('Countdown Page Options', 'vp_textdomain'),
							'name' => 'ozy_rosie_section_countdown',
							'fields' => array(
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_countdown_year',
									'label' => __('End Year', 'vp_textdomain'),
									'description' => __('Enter the Year of the date counter will count to.', 'vp_textdomain'),
									'default' => date('Y', time())
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_countdown_month',
									'label' => __('End Month', 'vp_textdomain'),
									'description' => __('Enter the Month of the date counter will count to.', 'vp_textdomain'),
									'default' => date('m', time())
								),								
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_countdown_day',
									'label' => __('End Day', 'vp_textdomain'),
									'description' => __('Enter the Day of the date counter will count to.', 'vp_textdomain'),
									'default' => '15'
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_countdown_hour',
									'label' => __('End Hour', 'vp_textdomain'),
									'description' => __('Enter the Hour of the date counter will count to.', 'vp_textdomain'),
									'default' => '12'
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_countdown_minute',
									'label' => __('End Minute', 'vp_textdomain'),
									'description' => __('Enter the Minute of the date counter will count to.', 'vp_textdomain'),
									'default' => '12'
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_countdown_second',
									'label' => __('End Second', 'vp_textdomain'),
									'description' => __('Enter the Second of the date counter will count to.', 'vp_textdomain'),
									'default' => '00'
								)/*,
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_countdown_minute',
									'label' => __('URL to go on complete', 'vp_textdomain'),
									'value' => ''
								)*/			
							),
						),
												
					),
				),



				
											
			),
		),
		array(
			'name' => 'ozy_rosie_color_options',
			'title' => __('Color Options', 'vp_textdomain'),
			'icon' => 'font-awesome:fa-eye',
			'controls' => array(
							
				array(
					'type' => 'section',
					'title' => __('GENERIC', 'vp_textdomain'),
					'fields' => array(
						/*array(
							'type' => 'color',
							'name' => 'ozy_rosie_header_background_color',
							'label' => __('Header Background', 'vp_textdomain'),
							'description' => __('Change this color to alter the accent color globally for your site.', 'vp_textdomain'),
							'format' => 'rgba',
							'binding' => array(
								'field' => 'ozy_rosie_color_preset',
								'function' => 'vp_bind_ozy_rosie_header_background',
							),
						),*/
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_content_background_color',
							'label' => __('Content Background', 'vp_textdomain'),
							'format' => 'rgba',
							'default' => 'rgba(255,255,255,1)'
						),
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_content_color',
							'label' => __('Content Color', 'vp_textdomain'),
							'description' => __('Font color of the content', 'vp_textdomain'),
							'format' => 'hex',
							'default' => '#506a85'
						),
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_content_color_alternate',
							'label' => __('Alternate Color #1', 'vp_textdomain'),
							'description' => __('Like link color, hover color and input elements active border', 'vp_textdomain'),
							'format' => 'hex',
							'default' => '#00c1cf'
						),
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_content_color_alternate2',
							'label' => __('Alternate Color #2', 'vp_textdomain'),
							'description' => __('Like footer, footer sidebar title color, text color and seperator color', 'vp_textdomain'),
							'format' => 'hex',
							'default' => '#41566d'
						),
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_content_color_alternate3',
							'label' => __('Alternate Color #3', 'vp_textdomain'),
							'description' => __('Like footer sidebar link color', 'vp_textdomain'),
							'format' => 'hex',
							'default' => '#ffffff'
						),						
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_content_background_color_alternate',
							'label' => __('Alternate Background Color', 'vp_textdomain'),
							'description' => __('Like comments background color', 'vp_textdomain'),
							'format' => 'rgba',
							'default' => 'rgba(240,240,240,1)'
						),						
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_primary_menu_separator_color',
							'label' => __('Separator / Border Color', 'vp_textdomain'),
							'description' => __('Used for, Primary menu, in page Seperators and Comments bottom border', 'vp_textdomain'),
							'default' => 'rgba(240,240,240,1)',
							'format' => 'rgba'
						),						
					),
				),
				
				array(
					'type' => 'section',
					'title' => __('Primary Menu', 'vp_textdomain'),
					'name' => 'ozy_rosie_primary_menu_section_colors',
					'fields' => array(
						array(
							'type' => 'toggle',
							'name' => 'ozy_rosie_primary_menu_section_colors_transparent_header',
							'label' => __('Transparent Header?', 'vp_textdomain'),
							'description' => __('Turn on use transparent header. Recommended to keep as ON.', 'vp_textdomain'),
							'default' => '1'							
						),					
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_primary_menu_logo_color',
							'label' => __('Text Logo Color', 'vp_textdomain'),
							'default' => 'rgba(255,255,255,1)',
							'format' => 'rgba',
							'description' => 'Available only when one or both logo image not supplied',
						),					
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_primary_menu_font_color',
							'label' => __('Font Color', 'vp_textdomain'),
							'default' => 'rgba(255,255,255,1)',
							'format' => 'rgba',
						),
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_primary_menu_font_color_hover',
							'label' => __('Font Color : Hover / Active', 'vp_textdomain'),
							'default' => 'rgba(255,255,255,1)',
							'format' => 'rgba',
						),
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_primary_menu_background_color',
							'label' => __('Background Color', 'vp_textdomain'),
							'default' => 'rgba(47,64,82,1)',
							'format' => 'rgba',
						),
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_primary_menu_background_color_hover',
							'label' => __('Background Color : Hover / Active', 'vp_textdomain'),
							'default' => 'rgba(53,71,89,1)',
							'format' => 'rgba',
						),
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_primary_menu_icon_color',
							'label' => __('Icon Color', 'vp_textdomain'),
							'default' => 'rgba(255,255,255,1)',
							'format' => 'rgba',
						),
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_primary_menu_separator_color_2',
							'label' => __('Separator Color', 'vp_textdomain'),
							'default' => 'rgba(255,255,255,1)',
							'format' => 'rgba',
						),
						
						array(
							'type' => 'toggle',
							'name' => 'ozy_rosie_primary_menu_section_colors_enable_alternate',
							'label' => __('Enable Alternate Colors', 'vp_textdomain'),
							'description' => __('Turn on use different scheme for alternate menu and header layout colors.', 'vp_textdomain'),
							'default' => '1',
						),

																													
					),
				),
				
				
				array(
					'type' => 'section',
					'title' => __('Primary Menu / Alternate', 'vp_textdomain'),
					'name' => 'ozy_rosie_primary_menu_section_colors2',
					'dependency' => array(
						'field' => 'ozy_rosie_primary_menu_section_colors_enable_alternate',
						'function' => 'vp_dep_boolean',
					),
					'fields' => array(
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_primary_menu_logo_color_2',
							'label' => __('Text Logo Color', 'vp_textdomain'),
							'default' => 'rgba(80,106,133,1)',
							'format' => 'rgba',
							'description' => 'Available only when one or both logo image not supplied',
						),					
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_primary_menu_font_color_2',
							'label' => __('Font Color', 'vp_textdomain'),
							'default' => 'rgba(80,106,133,1)',
							'format' => 'rgba',
						),
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_primary_menu_font_color_hover_2',
							'label' => __('Font Color : Hover / Active', 'vp_textdomain'),
							'default' => 'rgba(0,193,207,1)',
							'format' => 'rgba',
						),
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_primary_menu_background_color_2',
							'label' => __('Background Color', 'vp_textdomain'),
							'default' => 'rgba(255,255,255,1)',
							'format' => 'rgba',
						),
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_primary_menu_background_color_hover_2',
							'label' => __('Background Color : Hover / Active', 'vp_textdomain'),
							'default' => 'rgba(80,106,133,1)',
							'format' => 'rgba',
						),
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_primary_menu_icon_color_2',
							'label' => __('Icon Color', 'vp_textdomain'),
							'default' => 'rgba(80,106,133,1)',
							'format' => 'rgba',
						),
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_primary_menu_separator_color_2_2',
							'label' => __('Separator Color', 'vp_textdomain'),
							'default' => 'rgba(117,141,164,1)',
							'format' => 'rgba',
						),																									
					),
				),				
				
				
				
				array(
					'type' => 'section',
					'title' => __('Footer', 'vp_textdomain'),
					'name' => 'ozy_rosie_footer_section_colors',
					'fields' => array(
						array(
							'type' => 'notebox',
							'name' => 'ozy_rosie_footer_color_infobox',
							'label' => __('Footer Styling', 'vp_textdomain'),
							'description' => __('<a href="'. OZY_CSS_DIRECTORY_URL .'admin/images/footer.png?width=800&height=600" title="'. __('Footer Styling', 'vp_textdomain') .'" class="thickbox">Click here</a> to see which color will affect which part.', 'vp_textdomain'),
							'status' => 'info',
						),					
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_footer_color_1',
							'label' => __('Color #1', 'vp_textdomain'),
							'default' => 'rgba(255,255,255,1)',
							'format' => 'rgba',
						),					
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_footer_color_2',
							'label' => __('Color #2', 'vp_textdomain'),
							'default' => 'rgba(40,54,69,1)',
							'format' => 'rgba',
						),	
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_footer_color_3',
							'label' => __('Color #3', 'vp_textdomain'),
							'default' => 'rgba(92,108,125,1)',
							'format' => 'rgba',
						),	
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_footer_color_4',
							'label' => __('Color #4', 'vp_textdomain'),
							'default' => 'rgba(65,86,109,1)',
							'format' => 'rgba',
						),	
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_footer_color_5',
							'label' => __('Color #5', 'vp_textdomain'),
							'default' => 'rgba(37,50,65,1)',
							'format' => 'rgba',
						),
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_footer_color_6',
							'label' => __('Color #6', 'vp_textdomain'),
							'default' => 'rgba(47,64,82,1)',
							'format' => 'rgba',
						),
																													
					),
				),				
				
				
				
				array(
					'type' => 'section',
					'title' => __('Form', 'vp_textdomain'),
					'name' => 'ozy_rosie_form_section_coloring',
					'fields' => array(
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_form_font_color',
							'label' => __('Font Color', 'vp_textdomain'),
							'default' => 'rgba(35,35,35,1)',
							'format' => 'rgba',
						),
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_form_background_color',
							'label' => __('Background Color', 'vp_textdomain'),
							'default' => 'rgba(255,255,255,1)',
							'format' => 'rgba',
						),
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_form_button_font_color',
							'label' => __('Font Color (Button)', 'vp_textdomain'),
							'default' => 'rgba(255,255,255,1)',
							'format' => 'rgba',
						),
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_form_button_font_color_hover',
							'label' => __('Font Color : Hover / Active (Button)', 'vp_textdomain'),
							'default' => 'rgba(255,255,255,1)',
							'format' => 'rgba',
						),
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_form_button_background_color',
							'label' => __('Background Color (Button)', 'vp_textdomain'),
							'default' => 'rgba(80,106,133,1)',
							'format' => 'rgba',
						),	
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_form_button_background_color_hover',
							'label' => __('Background Color : Hover / Active (Button)', 'vp_textdomain'),
							'default' => 'rgba(94,132,172,1)',
							'format' => 'rgba',
						),											
					),
				),				
							
				
				array(
					'type' => 'section',
					'title' => __('Background Styling', 'vp_textdomain'),
					'fields' => array(
						array(
							'type' => 'color',
							'name' => 'ozy_rosie_body_background_color',
							'label' => __('Background Color', 'vp_textdomain'),
							'description' => __('This option will affect only page background.', 'vp_textdomain'),
							'default' => '#506a85',
							'format' => 'hex',
						),					
						array(
							'type' => 'upload',
							'name' => 'ozy_rosie_body_background_image',
							'label' => __('Custom Background Image', 'vp_textdomain'),
							'description' => __('Upload or choose custom page background image.', 'vp_textdomain'),
						),
						array(
							'type' => 'radiobutton',
							'name' => 'ozy_rosie_body_background_image_size',
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
							'name' => 'ozy_rosie_body_background_image_repeat',
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
							'name' => 'ozy_rosie_body_background_image_attachment',
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
					),
				),
				
			),
		),			
		
		array(
			'title' => __('Social', 'vp_textdomain'),
			'name' => 'ozy_rosie_typography',
			'icon' => 'font-awesome:fa-group',
			'menus' => array(
				array(
					'title' => __('Accounts', 'vp_textdomain'),
					'name' => 'ozy_rosie_social_accounts',
					'icon' => 'font-awesome:fa-heart-o',
					'controls' => array(
						array(
							'type' => 'section',
							'title' => __('Social Accounts', 'vp_textdomain'),
							'description' => __('Enter social account names/IDs box below', 'vp_textdomain'),
							'fields' => array(
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_social_accounts_fivehundredpx',
									'label' => __('500px', 'vp_textdomain')
								),							
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_social_accounts_behance',
									'label' => __('Behance', 'vp_textdomain')
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_social_accounts_blogger',
									'label' => __('Blogger', 'vp_textdomain')
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_social_accounts_delicious',
									'label' => __('Delicious', 'vp_textdomain')
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_social_accounts_deviantart',
									'label' => __('DeviantArt', 'vp_textdomain')
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_social_accounts_digg',
									'label' => __('Digg', 'vp_textdomain')
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_social_accounts_dribble',
									'label' => __('Dribble', 'vp_textdomain')
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_social_accounts_email',
									'label' => __('Email', 'vp_textdomain')
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_social_accounts_facebook',
									'label' => __('Facebook', 'vp_textdomain')
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_social_accounts_flickr',
									'label' => __('Flickr', 'vp_textdomain')
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_social_accounts_forrst',
									'label' => __('Forrst', 'vp_textdomain')
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_social_accounts_foursquare',
									'label' => __('Foursquare', 'vp_textdomain')
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_social_accounts_github',
									'label' => __('Github', 'vp_textdomain')
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_social_accounts_googleplus',
									'label' => __('Google+', 'vp_textdomain')
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_social_accounts_instagram',
									'label' => __('Instagram', 'vp_textdomain')
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_social_accounts_lastfm',
									'label' => __('Last.FM', 'vp_textdomain')
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_social_accounts_linkedin',
									'label' => __('LinkedIn', 'vp_textdomain')
								),

								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_social_accounts_myspace',
									'label' => __('MySpace', 'vp_textdomain')
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_social_accounts_pinterest',
									'label' => __('Pinterest', 'vp_textdomain')
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_social_accounts_rss',
									'label' => __('RSS', 'vp_textdomain')
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_social_accounts_soundcloud',
									'label' => __('SoundCloud', 'vp_textdomain')
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_social_accounts_stumbleupon',
									'label' => __('StumbleUpon', 'vp_textdomain')
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_social_accounts_tumblr',
									'label' => __('Tumblr', 'vp_textdomain')
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_social_accounts_twitter',
									'label' => __('Twitter', 'vp_textdomain')
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_social_accounts_vimeo',
									'label' => __('Vimeo', 'vp_textdomain')
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_social_accounts_wordpress',
									'label' => __('WordPress', 'vp_textdomain')
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_social_accounts_yahoo',
									'label' => __('Yahoo!', 'vp_textdomain')
								),
								array(
									'type' => 'textbox',
									'name' => 'ozy_rosie_social_accounts_youtube',
									'label' => __('Youtube', 'vp_textdomain')
								),																																																																																																																																																																																														
							),
						),
					),
				),			
				array(
					'title' => __('General', 'vp_textdomain'),
					'name' => 'ozy_rosie_social_general',
					'icon' => 'font-awesome:fa-group',
					'controls' => array(
						array(
							'type' => 'section',
							'title' => __('Social Icons', 'vp_textdomain'),
							'fields' => array(
								array(
									'type' => 'toggle',
									'name' => 'ozy_rosie_social_use',
									'label' => __('Social Share Buttons', 'vp_textdomain'),
									'description' => __('Enable / Disable social share buttons.', 'vp_textdomain'),
									'default' => '1',
								),
								array(
									'type' => 'sorter',
									'name' => 'ozy_rosie_social_icon_order',
									'max_selection' => 20,
									'label' => __('Icon List / Order', 'vp_textdomain'),
									'description' => __('Select visible icons and sort.', 'vp_textdomain'),
									'items' => array(
										'data' => array(
											array(
												'source' => 'function',
												'value' => 'vp_get_social_medias',
											),
										),
									),
								),
								array(
									'type' => 'select',
									'name' => 'ozy_rosie_social_icon_target',
									'label' => __('Target Window', 'vp_textdomain'),
									'description' => __('Where links will be opened?', 'vp_textdomain'),
									'items' => array(
										array(
											'value' => '_blank',
											'label' => __('Blank Window / New Tab', 'vp_textdomain'),
										),
										array(
											'value' => '_self',
											'label' => __('Self Window', 'vp_textdomain'),
										),
									),
									'default' => array(
										'_blank',
									),
								),								
							),
						),
					),
				),			
			),
		),
	)
);

return $ozy_rosie_option_arr;

/**
 *EOF
 */