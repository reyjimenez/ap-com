			<?php
			global $post;
			$ozy_data->blog_has_super_header = false;
			
			/*header slider*/
			ozy_put_header_slider($ozy_data->header_slider);

			ozy_page_master_meta_params();
			
			ozy_blog_meta_params();
			
			// meta params & bg slider for page
			if (isset($post->post_type) && $post->post_type === 'ozy_portfolio') {
				ozy_page_meta_params('portfolio');
			}else if(is_single()) {
				ozy_page_meta_params('blog');
			}else{
				ozy_page_meta_params();
			}
			
			$content_css 			= $ozy_data->_page_content_css_name;
			$page_title_available 	= is_page() || is_search() || is_archive() || is_category() || is_home();
			
			$shop_page_id = ozy_get_woocommerce_page_id();
			if ($shop_page_id > 0) { 
				ozy_woocommerce_meta_params();
				$content_css = $ozy_data->_woocommerce_content_css_name;
				if(!is_product_category() && !is_product_tag()) {
					if(ozy_get_metabox('hide_title', 0, $shop_page_id) !== '1') {
						$ozy_data->_page_custom_page_title = 
							ozy_get_metabox('use_custom_title', 0, $shop_page_id) == '1' ? 
							ozy_get_metabox('use_custom_title_group.0.ozy_rosie_meta_page_custom_title', '', $shop_page_id) : 
							get_the_title($shop_page_id);
						$ozy_data->_page_custom_page_sub_title = 
							ozy_get_metabox('use_custom_title_group.0.ozy_rosie_meta_page_custom_sub_title', '', $shop_page_id);
					}else{
						$ozy_data->_page_hide_page_title = '1';
					}
					$page_title_available = true;
				}else if(is_product_category() || is_product_tag()){ // if is product category page
					global $wp_query;
					$cat_obj = $wp_query->get_queried_object();
					if($cat_obj)    {
						$ozy_data->_page_custom_page_title = $cat_obj->name;
						//if($cat_obj->description) $ozy_data->_page_custom_page_sub_title = $cat_obj->description;
					}				
				}
			}else{				
				if(is_search()) {
					$ozy_data->_page_custom_page_title 		= __('Search results for: "', 'vp_textdomain') . get_search_query() . '"';
					$ozy_data->_page_custom_page_sub_title 	= '';
					$ozy_data->_page_hide_page_title = '0';
				}else if(is_home()) {
					$ozy_data->_page_custom_page_title 		= __('Blog', 'vp_textdomain');
					$ozy_data->_page_custom_page_sub_title 	= '';
					$ozy_data->_page_hide_page_title = '0';
				}else if(is_author()) {
					if(isset($_GET['author_name'])){$curauth = get_userdatabylogin($author_name);}else{$curauth = get_userdata(intval($author));}
					$ozy_data->_page_custom_page_title 		= __('About: ', 'vp_textdomain') . $curauth->display_name;
					$ozy_data->_page_custom_page_sub_title 	= '';
					$ozy_data->_page_hide_page_title = '0';
				}else if(is_category()) {
					$ozy_data->_page_custom_page_title 		= __('Category Archives: ', 'vp_textdomain') . '<span>' . single_cat_title( '', false ) . '</span>';
					$ozy_data->_page_custom_page_sub_title 	= '';
					$ozy_data->_page_hide_page_title = '0';
				}else if(is_archive()) {
					if ( is_day() ) : /* if the daily archive is loaded */
						$ozy_data->_page_custom_page_title = sprintf(__('Daily Archives: <span>%s</span>', 'vp_textdomain'), get_the_date() );
					elseif ( is_month() ) : /* if the montly archive is loaded */
						$ozy_data->_page_custom_page_title = sprintf( __('Monthly Archives: <span>%s</span>', 'vp_textdomain'), get_the_date('F Y'));
					elseif ( is_year() ) : /* if the yearly archive is loaded */
						$ozy_data->_page_custom_page_title = sprintf(__( 'Yearly Archives: <span>%s</span>', 'vp_textdomain'), get_the_date('Y'));
					else : /* if anything else is loaded, ex. if the tags or categories template is missing this page will load */
						$ozy_data->_page_custom_page_title = __('Blog Archives', 'vp_textdomain');
					endif;
					$ozy_data->_page_custom_page_sub_title 	= '';
					$ozy_data->_page_hide_page_title = '0';					
				}else if(is_tag()) {
					$ozy_data->_page_custom_page_title = sprintf(__( 'Tag Archives: %s', 'vp_textdomain'), '<span>' . single_tag_title( '', false ) . '</span>');
					$ozy_data->_page_custom_page_sub_title 	= '';
					$ozy_data->_page_hide_page_title = '0';					
				}else{
					if(isset($post->ID)) {
						$ozy_data->_page_custom_page_title = 
							ozy_get_metabox('use_custom_title', 0) == '1' ? 
							ozy_get_metabox('use_custom_title_group.0.ozy_rosie_meta_page_custom_title', '') : get_the_title($post->ID);
						$ozy_data->_page_custom_page_sub_title = 
							ozy_get_metabox('use_custom_title_group.0.ozy_rosie_meta_page_custom_sub_title', '', $post->ID);	
					}
				}
			}
			
			if (is_single() && get_post_type() === 'post' ) {
				if ( have_posts() ) while ( have_posts() ) : the_post();
				
					if ( has_post_thumbnail() ) { 
						$src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' , false );
						if(isset($src[0])) {
							echo '<div class="featured-thumbnail-header" style="background-image:url('. $src[0] .')">';
							echo '	<div>';
							echo '		<div class="container">
											<div>';
							echo '				<h1 class="heading-font">'. ( get_the_title() ? get_the_title() : get_the_time('F j, Y') ) .'</h1>';
							echo '				<div class="post-meta content-font">';
							echo '					<p class="g">' . __('By ', 'vp_textdomain') . '</p>';
							echo '					<p>'; the_author_posts_link(); echo '</p>';
							echo '					<p class="g">' . __(' in ', 'vp_textdomain') . '</p>';
							echo '					<p>'; the_category(', '); echo '</p>';
							echo '					<p class="g">' . __('Posted ', 'vp_textdomain') . '</p>';
							echo '					<p>'; the_time('F j, Y'); _e(' at ', 'vp_textdomain'); the_time(); echo '</p>';
							echo '				</div>';
							echo '			</div>
										</div>';
							echo '		<div class="a"></div><div class="b"></div>';
							echo '	</div>';		
							echo '</div>';
				
							$ozy_data->blog_has_super_header = true;
						}
					}
					
				endwhile;
			}
			else
			{
				/*page title*/			
				if($ozy_data->_page_hide_page_title != '1' && $page_title_available && !$ozy_data->hide_everything_but_content) { 
				?>
				<div id="page-title-wrapper">
					<div>
						<h1 class="page-title"><?php echo trim($ozy_data->_page_custom_page_title) ? $ozy_data->_page_custom_page_title : get_the_title() ?></h1>
						<?php if($ozy_data->_page_custom_page_sub_title) { echo '<h3>'. $ozy_data->_page_custom_page_sub_title .'</h3>'; } ?>
					</div>
				</div>
				<?php
				}
			}
			?>