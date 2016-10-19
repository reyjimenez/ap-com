<?php
	/**
	* ozy_load_dynamic_sidebars()
	* Load dynamic and default sidebars here
	*/
	function ozy_load_dynamic_sidebars() {
		
		// Static sidebars
		register_sidebar(array(
			'name'			=> 'Widget Bar (Footer) #1 ('. OZY_WPLANG . ')',
			'id'			=> 'ozy-footer-widget-bar-one',
			'before_widget' => '<div class="widget"><div class="separator"></div>',
			'after_widget' 	=> '</div>',
			'before_title' 	=> '<h4>',
			'after_title' 	=> '</h4>'
		));

		register_sidebar(array(
			'name'			=> 'Widget Bar (Footer) #2 ('. OZY_WPLANG . ')',
			'id'			=> 'ozy-footer-widget-bar-two',
			'before_widget' => '<div class="widget"><div class="separator"></div>',
			'after_widget' 	=> '</div>',
			'before_title' 	=> '<h4>',
			'after_title' 	=> '</h4>'
		));

		register_sidebar(array(
			'name'			=> 'Widget Bar (Footer) #3 ('. OZY_WPLANG . ')',
			'id'			=> 'ozy-footer-widget-bar-three',
			'before_widget' => '<div class="widget"><div class="separator"></div>',
			'after_widget' 	=> '</div>',
			'before_title' 	=> '<h4>',
			'after_title' 	=> '</h4>'
		));
		
		register_sidebar(array(
			'name'			=> 'Footer ('. OZY_WPLANG . ')',
			'id'			=> 'ozy-footer-bar',
			'before_widget' => '',
			'after_widget' 	=> '',
			'before_title' 	=> '<!--',
			'after_title' 	=> '-->'
		));
		
		register_sidebar(array(
			'name'			=> 'Header Information ('. OZY_WPLANG . ')',
			'id'			=> 'ozy-header-information',
			'before_widget' => '',
			'after_widget' 	=> '',
			'before_title' 	=> '<!--',
			'after_title' 	=> '-->'
		));
		
		register_sidebar(array(
			'name'			=> 'Side Menu ('. OZY_WPLANG . ')',
			'id'			=> 'ozy-side-menu',
			'before_widget' => '<div class="widget-area"><ul>',
			'after_widget' 	=> '</ul></div>',
			'before_title' 	=> '<h4>',
			'after_title' 	=> '</h4>'
		));		
		
		register_sidebar(array(
			'name'			=> 'Mobile Menu ('. OZY_WPLANG . ')',
			'id'			=> 'ozy-mobile-side-menu',
			'before_widget' => '<div class="widget-area"><ul>',
			'after_widget' 	=> '</ul></div>',
			'before_title' 	=> '<h4>',
			'after_title' 	=> '</h4>'
		));			
		
		$sidebar_posts = get_posts(array(
			'posts_per_page' 	=> -1,
			'post_type' 		=> 'ozy_sidebars'
		));
	
		foreach ($sidebar_posts as $post)
		{
			register_sidebar(array(
				'name'			=> $post->post_title . ' ('. OZY_WPLANG . ')',
				'id'			=> $post->post_name,
				'before_widget'	=> '<li class="widget">',
				'after_widget'	=> '</li>',
				'before_title'	=> '<h4>',
				'after_title'	=> '</h4>'
			));
		}
		
		// If WPML activated create sidebars for available languages too
		//$default_wp_language = get_bloginfo('language');
		if(function_exists("icl_get_languages") && defined("ICL_LANGUAGE_CODE")){
			$languages = icl_get_languages('skip_missing=0&orderby=code');
			if(!empty($languages)){
				foreach($languages as $l){
					if($l['language_code'] != OZY_WPLANG) {
						register_sidebar(array(
							'name'			=> 'Widget Bar (Footer) #1' . ' (' . $l['native_name'] . ')',
							'id'			=> 'ozy-footer-widget-bar-one' . '_' . $l['language_code'],
							'before_widget' => '<div class="widget"><div class="separator"></div>',
							'after_widget' 	=> '</div>',
							'before_title' 	=> '<h4>',
							'after_title' 	=> '</h4>'
						));
						
						register_sidebar(array(
							'name'			=> 'Widget Bar (Footer) #2' . ' (' . $l['native_name'] . ')',
							'id'			=> 'ozy-footer-widget-bar-two' . '_' . $l['language_code'],
							'before_widget' => '<div class="widget"><div class="separator"></div>',
							'after_widget' 	=> '</div>',
							'before_title' 	=> '<h4>',
							'after_title' 	=> '</h4>'
						));
						
						register_sidebar(array(
							'name'			=> 'Widget Bar (Footer) #3' . ' (' . $l['native_name'] . ')',
							'id'			=> 'ozy-footer-widget-bar-three' . '_' . $l['language_code'],
							'before_widget' => '<div class="widget"><div class="separator"></div>',
							'after_widget' 	=> '</div>',
							'before_title' 	=> '<h4>',
							'after_title' 	=> '</h4>'
						));												
						
						register_sidebar(array(
							'name'			=> 'Footer' . ' (' . $l['native_name'] . ')',
							'id'			=> 'ozy-footer-bar' . '_' . $l['language_code'],
							'before_widget' => '',
							'after_widget' 	=> '',
							'before_title' 	=> '<!--',
							'after_title' 	=> '-->'
						));

						register_sidebar(array(
							'name'			=> 'Side Menu' . ' (' . $l['native_name'] . ')',
							'id'			=> 'ozy-side-menu' . '_' . $l['language_code'],
							'before_widget' => '<div class="widget-area"><ul>',
							'after_widget' 	=> '</ul></div>',
							'before_title' 	=> '<h4>',
							'after_title' 	=> '</h4>'
						));						
						
						register_sidebar(array(
							'name'			=> 'Mobile Menu' . ' (' . $l['native_name'] . ')',
							'id'			=> 'ozy-mobile-side-menu' . '_' . $l['language_code'],
							'before_widget' => '<div class="widget-area"><ul>',
							'after_widget' 	=> '</ul></div>',
							'before_title' 	=> '<h4>',
							'after_title' 	=> '</h4>'
						));												
												
						foreach ($sidebar_posts as $post) {
							register_sidebar(array(
								'name'			=> $post->post_title . ' (' . $l['native_name'] . ')',
								'id'			=> $post->post_name . '_' . $l['language_code'],
								'before_widget'	=> '<li class="widget">',
								'after_widget'	=> '</li>',
								'before_title'	=> '<h4>',
								'after_title'	=> '</h4>'
							));											
						}
					}
				}
			}
		}
		
	}

	add_action('widgets_init', 'ozy_load_dynamic_sidebars');
?>