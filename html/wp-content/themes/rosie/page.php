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
<div id="content" class="<?php echo esc_attr($ozy_data->_page_content_css_name); ?>">
    <?php if ( have_posts() && $ozy_data->_page_hide_page_content != '1') while ( have_posts() ) : the_post(); ?>
        <div id="post-<?php the_ID(); ?>" <?php post_class('page'); ?>>
            <article>
                <div class="post-content page-content">
                    <?php the_content(); ?>
                    <?php wp_link_pages('before=<div class="pagination">&after=</div>'); ?>
                </div><!--.post-content .page-content -->
            </article>
			
            <?php if( ozy_get_option('page_page_author') == '1') { ?>
            <div id="page-meta">
                <h3><?php _e('Written by ', 'vp_textdomain'); the_author_posts_link() ?></h3>
                <p class="gravatar"><?php if(function_exists('get_avatar')) { echo get_avatar( get_the_author_meta('email'), '80' ); } ?></p>
                <p><?php _e('Posted on ', 'vp_textdomain'); the_time('F j, Y'); _e(' at ', 'vp_textdomain'); the_time() ?></p>
            </div><!--#pageMeta-->
            <?php } ?>
        </div><!--#post-# .post-->

		<?php 
			if( ozy_get_option('page_page_comment') == '1') { 
        		comments_template( '', true );
			}
		?>

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
