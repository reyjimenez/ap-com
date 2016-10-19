<?php
	while ( $the_query->have_posts() ) {
		$the_query->the_post();

		$full_image_url  = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'showbiz');

		$post_categories 	= get_the_terms(get_the_ID(), 'portfolio_category');
		$category_arr		= array();
		foreach ($post_categories as $cat) { $category_arr[$cat->slug] = $cat->name; }	
		
		$grid_effect = vp_metabox('ozy_rosie_meta_portfolio.ozy_rosie_meta_portfolio_grid_effect', get_the_ID());
		$grid_effect = $grid_effect === '-1' ? $ozy_data->_portfolio_grid_effect : $grid_effect;		
		
		echo 
		'<figure class="effect-'. $grid_effect .'" style="display:none;"> 
			<img src="'. $full_image_url[0] .'" alt="'. get_the_title() .'"/>
			<figcaption>
				<h2>'. preg_replace('/ /', ' <span>', get_the_title(), 1) .'</span></h2>
				<p>'. ozy_excerpt_max_charlength(50, true, true)  .'</p>
				<a href="'. get_permalink() .'">'. __('View more', 'vp_textdomain') .'</a>
			</figcaption>			
		</figure>';			
	}
?>