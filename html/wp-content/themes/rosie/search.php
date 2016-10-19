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
	<div id="content" class="<?php echo esc_attr($ozy_data->_page_content_css_name); ?> search">
		<div id="search-results" <?php post_class('page'); ?>>
			<article>
            	<div class="post-content page-content">
                	<div>
						<?php		
                            if (have_posts()) : 
                                while (have_posts()) : the_post(); 
                        ?>
                                    <article class="result">
                                        <?php if ( has_post_thumbnail() ) { echo '<a href="'. get_permalink() .'">'; the_post_thumbnail('showbiz'); echo '</a>'; } ?>
                                        <h4><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h4>
                                        <p><?php echo ozy_excerpt_max_charlength(100, true) ?></p>
                                        <strong><?php $post_type_obj = get_post_type_object(get_post_type());echo $post_type_obj->labels->singular_name; ?></strong>
                                    </article>
	     				<?php
								endwhile;
                            else:
						?>
                        <div class="no-results">
                            <h2><?php _e('No Results', 'vp_textdomain'); ?></h2>
                            <p><?php _e('Please feel free try again!', 'vp_textdomain'); ?></p>
                            <?php get_search_form(); /* outputs the default Wordpress search form */ ?>
                        </div><!--noResults-->
                        <?php endif; ?>
					</div>                    
	                <?php echo get_pagination('<div class="page-pagination">', '</div>'); ?>
        		</div>
			</article>
		</div>
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