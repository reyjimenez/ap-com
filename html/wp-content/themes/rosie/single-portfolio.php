<?php 
get_header(); 

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
	<?php 
	if ( have_posts() ) while ( have_posts() ) : the_post(); 
		$post_format = vp_metabox('ozy_rosie_meta_portfolio.ozy_rosie_meta_portfolio_post_format');
		
		// if any of full slider template selected
		if( 'full-page-slider' === $post_format || 
			'full-page-nearby-slider' === $post_format ) 
		{
			$ozy_data->_page_content_css_name = 'no-sidebar template-clean-page';
		}
	?>
	<div id="content" class="<?php echo esc_attr($ozy_data->_page_content_css_name); ?>">
		<div id="post-<?php the_ID(); ?>" <?php post_class('post'); ?>>
			<article>
	            <div class="post-content portfolio-content">
				<?php
					// In-Page-Slider, Video or Thumbnail image
					if( 'inpage-slider' === $post_format || 'inpage-slider-full' === $post_format ) {
						echo $ozyHelper->post_royal_slider( ('inpage-slider-full' === $post_format ? true : false) );
					}
					else if( 'video' === $post_format ) {
						echo $ozyHelper->convert_videos( vp_metabox('ozy_rosie_meta_portfolio.ozy_rosie_meta_portfolio_custom_thumbnail_group.0.ozy_rosie_meta_portfolio_custom_thumbnail_video') );
					} 
					else if( 'video' !== $post_format 
						&& ('full-page-slider' !== $post_format 
						&& 'full-page-nearby-slider' !== $post_format 
						&& 'inpage-slider-full' !== $post_format) )
					{
						if ( has_post_thumbnail() ) { 
							//$src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' , false );
							$thumbnail_image_src	= wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' , false );
							$post_image_src			= wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'blog' , false );
							//echo '<div class="featured-thumbnail"><a href="'. $src[0] .'" class="fancybox"><span class="oic oic-pe-icon-7-stroke-87"></span></a>'; the_post_thumbnail('blog'); echo '</div>'; 
							if ( isset($thumbnail_image_src[0]) && isset($post_image_src[0])) { 
								echo '<div class="featured-thumbnail" style="background-image:url('. $post_image_src[0] .');"><a href="'. $thumbnail_image_src[0] .'" class="fancybox"><span class="oic-simple-line-icons-49"></span></a>'; the_post_thumbnail('blog'); echo '</div>';
							}
						}
					}
					
					$is_page_full = true;
					// Classic Slider
					if( 'full-page-slider' === $post_format ) {
						include_once(locate_template('include/portfolio-classic-gallery.php'));
					} 
					// Visible Near By Slider
					else if ('full-page-nearby-slider' === $post_format ) {
						include_once(locate_template('include/portfolio-nearby-gallery.php'));
					}
					// Standard, video and inline slider page
					else {
						include_once(locate_template('include/porfolio-standard.php'));
						$is_page_full = false;
					}
					
					// Render required style definitions
					if( $is_page_full === true ) {
						$ozyHelper->set_footer_style(
							".page-content,.post-content{padding:0 !important;margin:0 !important;}\r\n
							#main>.container{width:100% !important;padding:0 !important;margin:". ozy_get_option('header_height'). "px 0 ". ozy_get_option('footer_height') ."px 0 !important;background-color:transparent !important;-webkit-box-shadow:none;-moz-box-shadow:none;box-shadow:none;}\r\n
							#main>.container>#content.template-clean-page{padding:0 !important;}\r\n
							#main>.container>#content{width:100% !important;}\r\n
							#main>.container>#content>div>article>.page-content{margin:0 !important;}\r\n
							/*#footer-spacer{display:none !important;}\r\n*/
							body.has-page-title #main>.container{padding-top:0 !important;}\r\n
							.no-padding-margin{margin:0 !important;padding:0 !important;}\r\n"
						);
					}
                ?>
				</div><!--.post-content-->

			</article>

			<?php 
			/* If a user fills out their bio info, it's included here */ 
			if (get_the_author_meta('description') && ozy_get_option('page_portfolio_author') == '1') : 
			?>
			<div id="post-author">
				<h3><?php _e('Written by ', 'vp_textdomain'); the_author_posts_link() ?></h3>
				<p class="gravatar"><?php if(function_exists('get_avatar')) { echo get_avatar( get_the_author_meta('email'), '80' ); /* This avatar is the user's gravatar (http://gravatar.com) based on their administrative email address */  } ?></p>
				<div id="authorDescription">
					<?php the_author_meta('description') ?> 
					<div id="author-link">
						<p><?php _e('View all posts by: ', 'vp_textdomain'); the_author_posts_link() ?></p>
					</div><!--#author-link-->
				</div><!--#author-description -->
			</div><!--#post-author-->
            <?php
			endif;
			?>

		</div><!-- #post-## -->

		<?php 
			if( ozy_get_option('page_portfolio_comment') == '1') { 
        		comments_template( '', true );
			}
		?>
	</div><!--#content-->
	<?php endwhile; /* end loop */ ?>

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