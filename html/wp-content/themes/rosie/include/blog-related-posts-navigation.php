<?php
	global $ozy_data;

	/*RELATED POSTS AND POST NAVIGATION*/		
	$tags = wp_get_post_tags($post->ID);
	
	if(ozy_get_option('page_blog_related_posts') != '1') {
		
		echo '<div id="newer-older-posts-wrapper">';
		$ozyHelper->newer_older_post_navigation_post(true);
		echo '</div>';
		
	} else if(is_array($tags) && count($tags) > 0 && $ozy_data->vc_active) {
		
		// Related Posts
		echo '<div id="ozy-related-posts-wrapper">
				<h4>'. __('Related Posts', 'vp_textdomain') .'</h4>';
		
		$ozyHelper->newer_older_post_navigation_post(false);
		
		$original_post = $post; //save original post for rest of the page
		global $post;
		
		echo '  <ul class="ozy-related-posts">';

		$tag_ids = array();  
		foreach($tags as $individual_tag) { 
			$tag_ids[] = $individual_tag->term_id; 
		}

		$args = array(
			'post_type' 			=> 'post',
			'tag__in' 				=> $tag_ids,
			'post__not_in' 			=> array($post->ID),
			'posts_per_page'		=> 8,
			'ignore_sticky_posts' 	=> 1,
			'meta_key' 				=> '_thumbnail_id',
			'orderby'				=> 'rand'
		);

		$related_posts_query = new WP_Query($args);
		if( $related_posts_query->have_posts() ) {
			$shortcode_list = '';
			while ($related_posts_query->have_posts()) : $related_posts_query->the_post();
				$shortcode_list.= '[ozy_vc_owlcarousel2 img_size="showbiz"  default_overlay="on" src="'. get_post_thumbnail_id() .'" title="'. get_the_title() .'" excerpt="" link="'. get_permalink() .'" title_size="h4"]';
			endwhile;
			echo do_shortcode('[ozy_vc_owlcarousel_wrapper autoplay="false" items="3"]'. $shortcode_list .'[/ozy_vc_owlcarousel_wrapper]');							
		}
		$post = $original_post;
		
		wp_reset_query();
		
		echo '	</ul>
			  </div><!-- #related posts-## -->';

	} else {
		echo '<div id="newer-older-posts-wrapper">';
		$ozyHelper->newer_older_post_navigation_post(true);
		echo '</div>';
	}
	?>