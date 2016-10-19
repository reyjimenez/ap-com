<?php

return array(
	'id'          => 'ozy_rosie_meta_page_portfolio',
	'types'       => array('page'),
	'title'       => __('Portfolio Options', 'vp_textdomain'),
	'priority'    => 'high',
	'template'    => array(
		array(
			'type' => 'notebox',
			'name' => 'ozy_rosie_meta_page_portfolio_infobox',
			'label' => __('Portfolio Options', 'vp_textdomain'),
			'description' => __('Below this point all the options are only works with Portfolio template types.', 'vp_textdomain'),
			'status' => 'info',
		),
		array(
			'type' => 'sorter',
			'name' => 'ozy_rosie_meta_page_portfolio_category_sort',
			'label' => __('Category Select / Order', 'vp_textdomain'),
			'description' => __('If you leave this field blank, all available categories will be listed. By this option, you can create multiple portfolio/gallery pages with different items.', 'vp_textdomain'),			
			'default' => '{{all}}',
			'items' => array(
				'data' => array(
					array(
						'source' => 'function',
						'value' => 'vp_bind_ozy_rosie_portfolio_categories_simple',
					),
				),
			),
		),	
		array(
			'type' => 'radiobutton',
			'name' => 'ozy_rosie_meta_page_portfolio_order',
			'label' => __('Item Order', 'vp_textdomain'),
			'description' => __('By selecting "Custom Order ..." you will have to set the order field of each of the items.', 'vp_textdomain'),			
			'items' => array(
				array(
					'value' => 'date-desc',
					'label' => 'Date DESC',
				),
				array(
					'value' => 'date-asc',
					'label' => 'Date ASC',
				),
				array(
					'value' => 'menu_order-desc',
					'label' => 'Custom DESC',
				),
				array(
					'value' => 'menu_order-asc',
					'label' => 'Custom ASC',
				),
			),
			'default' => '{{first}}'
		),
		array(
			'type' => 'radiobutton',
			'name' => 'ozy_rosie_meta_page_portfolio_column_count',
			'label' => __('Column Count', 'vp_textdomain'),
			'items' => array(
				array(
					'value' => '3',
					'label' => '3',
				),
				array(
					'value' => '4',
					'label' => '4',
				)
			),
			'default' => '3'
		),			
		/*array(
			'type' => 'toggle',
			'name' => 'ozy_rosie_meta_page_portfolio_filter',
			'label' => __('Category Filter', 'vp_textdomain'),
			'description' => __('A category filter will be displayed.', 'vp_textdomain'),
			'default' => '1',
		),*/
		array(
			'type' => 'textbox',
			'name' => 'ozy_rosie_meta_page_portfolio_count',
			'label' => __('Item Count Per Load', 'vp_textdomain'),
			'description' => __('How many portfolio item will be loaded for each load.', 'vp_textdomain'),
			'default' => '32',
			'validation' => 'numeric',
		),
		array(
			'type' => 'select',
			'name' => 'ozy_rosie_meta_page_portfolio_grid_effect',
			'label' => __('Default Modern Grid Effect', 'vp_textdomain'),
			'items' => array(
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
				'lily',
			),
		),
			
	),	
);

/**
 * EOF
 */