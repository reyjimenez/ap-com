<?php

	add_action( 'init', 'ozy_create_post_types', 0 );
	
	function ozy_create_post_types() {

		//User managaged sidebars
		register_post_type( 'ozy_sidebars',
			array(
				'labels' => array(
					'name' => __( 'Sidebars', 'vp_textdomain'),
					'singular_name' => __( 'Sidebars', 'vp_textdomain'),
					'add_new' => 'Add Sidebar',
					'add_new_item' => 'Add Sidebar',
					'edit_item' => 'Edit Sidebar',
					'new_item' => 'New Sidebar',
					'view_item' => 'View Sidebars',
					'search_items' => 'Search Sidebar',
					'not_found' => 'No Sidebar found',
					'not_found_in_trash' => 'No Sidebar found in Trash'				
				),
				'can_export' => true,
				'public' => true,
				'exclude_from_search' => true,
				'publicly_queryable' => false,				
				'has_archive' => false,
				'rewrite' => false,
				'supports' => array('title'),
				'taxonomies' => array(''),
				'menu_icon' => 'dashicons-align-left'
			)
		);
		
		//User managaged sidebars
		register_post_type( 'ozy_fonts',
			array(
				'labels' => array(
					'name' => __( 'Custom Fonts', 'vp_textdomain'),
					'singular_name' => __( 'Custom Fonts', 'vp_textdomain'),
					'add_new' => 'Add Font',
					'add_new_item' => 'Add Font',
					'edit_item' => 'Edit Font',
					'new_item' => 'New Font',
					'view_item' => 'View Font',
					'search_items' => 'Search Font',
					'not_found' => 'No Font found',
					'not_found_in_trash' => 'No Font found in Trash'				
				),
				'can_export' => true,
				'public' => true,
				'exclude_from_search' => true,
				'publicly_queryable' => false,				
				'has_archive' => false,
				'rewrite' => false,
				'supports' => array('title'),
				'taxonomies' => array(''),
				'menu_icon' => 'dashicons-editor-textcolor'
			)
		);		
	}
	
?>