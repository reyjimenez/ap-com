<?php
/*
Template Name: Portfolio : Modern Grid
*/
get_header();

global $ozyHelper, $ozy_data;

// meta params & bg slider for page
ozy_page_meta_params();

// meta params for blog
ozy_portfolio_meta_params();

/* Widgetized LEFT sidebar */
if(function_exists( 'dynamic_sidebar' ) && $ozyHelper->hasIt($ozy_data->_page_content_css_name,'left-sidebar') && $ozy_data->_page_sidebar_name) {
?>
	<div id="sidebar" class="<?php echo esc_attr($ozy_data->_page_content_css_name); ?>">
		<ul>
        	<?php dynamic_sidebar( $ozy_data->_page_sidebar_name ); ?>
		</ul>
	</div>
	<!--sidebar-->
<?php
}
?>
<div id="content" class="<?php echo esc_attr($ozy_data->_page_content_css_name); ?> template-clean-page">
    <?php if ( have_posts() && $ozy_data->_page_hide_page_content != '1') while ( have_posts() ) : the_post(); ?>
        <div id="post-<?php the_ID(); ?>" <?php post_class('page'); ?>>
            <article>
                
                <div class="post-content page-content">
                    <?php the_content(); ?>

                    <!--modern-grid-->
                    <div class="modern-grid <?php echo $ozy_data->_portfolio_column_count === '4' ? 'four-columns' : '' ?>">
					<?php
						$args = array(
							'post_type' 			=> 'ozy_portfolio',
							'posts_per_page'		=> $ozy_data->_portfolio_post_per_load,
							'orderby' 				=> $ozy_data->_portfolio_orderby,
							'order' 				=> $ozy_data->_portfolio_order,
							'ignore_sticky_posts' 	=> 1,
							'meta_key' 				=> '_thumbnail_id',
							'tax_query' => array(
								array(
									'taxonomy' 	=> 'portfolio_category',
									'field' 	=> 'id',
									'terms' 	=> $ozy_data->_portfolio_include_categories,
									'operator' 	=> 'IN'
								),
							)
						);

						$the_query = new WP_Query( $args );

						while ( $the_query->have_posts() ) {
							$the_query->the_post();
							
							/*get post format*/
							$post_format = get_post_format();
							if ( false === $post_format ) {
								$post_format = 'standard';
							}

						   	$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'showbiz');
							
							$grid_effect = vp_metabox('ozy_rosie_meta_portfolio.ozy_rosie_meta_portfolio_grid_effect', get_the_ID());
							$grid_effect = $grid_effect === '-1' ? $ozy_data->_portfolio_grid_effect : $grid_effect;
							
							echo 
							'<figure class="effect-'. esc_attr($grid_effect) .'">
								<img src="'. esc_attr($large_image_url[0]) .'" alt="'. get_the_title() .'"/>
								<figcaption>
									<h2>'. preg_replace('/ /', ' <span>', get_the_title(), 1) .'</span></h2>';
							if($grid_effect !== 'honey') {
								echo '	<p>'. ozy_excerpt_max_charlength(50, true, true)  .'</p>';
							}
							echo '	<a href="'. get_permalink() .'">'. __('View more', 'vp_textdomain') .'</a>
								</figcaption>			
							</figure>';							
							
						}
					?>
                    </div>
                    <!--.modern-grid-->
					
                    <?php if($the_query->found_posts > $ozy_data->_portfolio_post_per_load) { ?>
                    <span class="load_more_blog" data-layout_type="modern" data-default_grid_effect="<?php echo esc_attr($ozy_data->_portfolio_grid_effect) ?>" data-item_count="<?php echo esc_attr($ozy_data->_portfolio_post_per_load) ?>" data-offset="0" data-found="<?php echo esc_attr($the_query->found_posts) ?>" data-order_by="<?php echo esc_attr($ozy_data->_portfolio_orderby) ?>" data-order="<?php echo esc_attr($ozy_data->_portfolio_order) ?>" data-category_name="<?php  echo esc_attr((is_array($ozy_data->_portfolio_include_categories) ? join($ozy_data->_portfolio_include_categories,',') : '')) ?>" data-loadingcaption="<?php echo esc_attr(__('LOADING...', 'vp_textdomain')) ?>" data-loadmorecaption="<?php echo esc_attr(__('LOAD MORE POSTS', 'vp_textdomain')) ?>"><?php echo esc_attr(__('LOAD MORE POSTS', 'vp_textdomain')) ?></span>
					<!--.load more portfolio-->
                    <!--<div class="bottom-spacer clear"></div>-->
                    <?php } ?>

	                <?php //edit_post_link('<small>Edit this entry</small>','',''); ?>
                </div><!--.post-content .page-content -->
            </article>
			
        </div><!--#post-# .post-->

    <?php endwhile; ?>
</div><!--#content-->
<?php
/* Widgetized RIGHT sidebar */
if(function_exists( 'dynamic_sidebar' ) && $ozyHelper->hasIt($ozy_data->_page_content_css_name,'right-sidebar') && $ozy_data->_page_sidebar_name) {
?>
	<div id="sidebar" class="<?php echo esc_attr($ozy_data->_page_content_css_name); ?>">
		<ul>
        	<?php dynamic_sidebar( $ozy_data->_page_sidebar_name ); ?>
		</ul>
	</div>
	<!--sidebar-->
<?php
}
get_footer();
?>
