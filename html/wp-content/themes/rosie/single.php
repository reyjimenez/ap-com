<?php 
get_header(); 

$hide_title = false;

/*get post format*/
$ozy_temporary_post_format = $post_format = get_post_format();
if ( false === $post_format ) { $post_format = 'standard'; }

if ( have_posts() ) while ( have_posts() ) : the_post();

/* Widgetized LEFT sidebar */
if(function_exists( 'dynamic_sidebar' ) && $ozyHelper->hasIt($ozy_data->_page_content_css_name,'left-sidebar') && $ozy_data->_page_sidebar_name) {
?>
	<div id="sidebar" class="<?php echo esc_attr($ozy_data->_page_content_css_name); echo $ozy_data->blog_has_super_header ? ' has-super-header' : ''; ?>">
		<ul>
        	<?php dynamic_sidebar( $ozy_data->_page_sidebar_name ); ?>
		</ul>
	</div>
	<!--sidebar-->
<?php
}
?>
<div id="content" class="<?php echo esc_attr($ozy_data->_page_content_css_name); echo $ozy_data->blog_has_super_header ? ' has-super-header' : ''; ?>">
    <div class="wpb_row vc_row-fluid">
        <div class="parallax-wrapper">
            <div class="vc_col-sm-12 wpb_column vc_column_container">
                <div class="wpb_wrapper">

                    <div id="post-<?php the_ID(); ?>" <?php post_class('post'); ?>>
                
                        <article>
                            <?php
                            /*here i am handling content to extract media objects*/
                            ob_start();
                            //if this is a gallery post, please remove gallery shortcode to render it as expected
                            if('gallery' === $post_format) {
                                ozy_convert_classic_gallery();
                            } else {
                                the_content('<p>' . __('Continue Reading &rarr;', 'vp_textdomain') . '</p>');
                            }
                            $my_content = ob_get_clean();					
                        
                            if( 'gallery' === $post_format ) {
                                echo $ozyHelper->post_royal_slider();
                            } else if( 'aside' === $post_format || 'link' === $post_format ) {
                                $hide_title = true;
                            } else if( 'video' !== $post_format && 'audio' !== $post_format ) {
                                if(!$ozy_data->blog_has_super_header) {
									if ( has_post_thumbnail() ) { 
										$thumbnail_image_src	= wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' , false );
										$post_image_src 		= wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'blog' , false );
										//echo '<div class="featured-thumbnail"><a href="'. $src[0] .'" class="fancybox"><span class="oic oic-pe-icon-7-stroke-87"></span></a>'; the_post_thumbnail('blog'); echo '</div>';
										 if ( isset($thumbnail_image_src[0]) && isset($post_image_src[0])) { 
											 echo '<div class="featured-thumbnail" style="background-image:url('. $post_image_src[0] .');"><a href="'. $thumbnail_image_src[0] .'" class="fancybox"><span class="oic-simple-line-icons-49"></span></a>'; the_post_thumbnail('blog'); echo '</div>';
										 }
									}
								}
                            }
                
                            /*and here i am printing media object which handled in functions.php ozy_add_video_embed_title()*/
                            if(isset($ozy_global_params['media_object'])) echo $ozy_global_params['media_object'];
                            
                            if('audio' == $post_format) {
                                $thumbnail_image_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'showbiz' , false );
                                echo '<div class="post-excerpt-'. $post_format .' simple-post-format">
                                        <div>
                                            <span class="icon"></span>';
                                    if(isset($thumbnail_image_src[0])) {
                                        echo '<img src="'. $thumbnail_image_src[0] .'" class="audio-thumb" alt=""/>';
                                    }
                                    echo '<div>';							
                                    echo $my_content;
                                    echo '</div>';
                                echo '	</div>
                                    </div>';								
                            }								
                            
							if(!$ozy_data->blog_has_super_header) {
                            ?>
                            <div class="post-meta">
                                <p class="g"><?php _e('By ', 'vp_textdomain'); ?></p>
                                <p><?php the_author_posts_link(); ?></p>
                                <p class="g"><?php _e(' in ', 'vp_textdomain');?></p>
                                <p><?php the_category(', '); ?></p>
                                <p class="g"><?php _e('Posted ', 'vp_textdomain'); ?></p>
                                <p><?php the_time('F j, Y'); _e(' at ', 'vp_textdomain'); the_time(); ?></p>
                            </div><!--#post-meta-->

                            <div class="clear"></div> 

                            <?php
								if(!$hide_title && 'audio' !== $post_format) {
									echo '<h2 class="post-title">';
										echo '<a href="'. get_permalink() .'" title="'. get_the_title() .'" class="a-page-title" rel="bookmark">'. ( get_the_title() ? get_the_title() : get_the_time('F j, Y') ) .'</a>';
									echo '</h2>';
								}
							}
                            ?>
                            <div class="post-content">                               
                                <?php
                                    if('audio' !== $post_format) {
                                        echo $my_content;
                                    }										
                                    wp_link_pages('before=<div class="pagination">'. __('Pages: ', 'vp_textdomain') .'&after=</div>');						
                                ?>
                            </div><!--.post-content-->

                            <?php edit_post_link('<p><small>Edit this entry</small></p>','',''); ?>
                            
                        </article>
                
                        <?php 
                        if(ozy_get_option('page_blog_share') == '1') {
                            include('include/share-buttons.php');
                            echo '<div class="clear"></div>';
                        }
                        
						if( has_tag() ) {
						?>
                        <div id="single-blog-tags"><strong><?php _e('TAGS:','vp_textdomain')?></strong> <?php the_tags('', '', ''); ?></div><!--#single-blog-tags-->
                        <?php
						}
						?>
                
                    </div><!-- #post-## -->
                    
                    <div class="clear"></div> 
                    
                    <?php
                    /* If a user fills out their bio info, it's included here */ 
                    if (get_the_author_meta('description') && ozy_get_option('page_blog_author') == '1') : 
                    ?>
                    <div id="post-author">
                        <p class="gravatar"><?php if(function_exists('get_avatar')) { echo get_avatar( get_the_author_meta('email'), '80' ); } ?></p>
                        <div id="authorDescription">
                            <h3><?php _e('About ', 'vp_textdomain'); the_author_posts_link() ?></h3>
                            <?php the_author_meta('description') ?> 
                        </div><!--#author-description -->
                    </div><!--#post-author-->
                    <?php
                    endif;
                    ?>                        
                    
                    <div class="clear"></div> 
                    
                    <?php
						include_once('include/blog-related-posts-navigation.php');
					?>
                    
                    <div class="clear"></div> 
                    <?php 
                        if( ozy_get_option('page_blog_comment') == '1') { 
                            comments_template( '', true );
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>       
</div><!--#content-->

<?php 
/* Widgetized RIGHT sidebar */
if(function_exists( 'dynamic_sidebar' ) && $ozyHelper->hasIt($ozy_data->_page_content_css_name,'right-sidebar') && $ozy_data->_page_sidebar_name) {
?>
	<div id="sidebar" class="<?php echo esc_attr($ozy_data->_page_content_css_name); echo $ozy_data->blog_has_super_header ? ' has-super-header' : ''; ?>">
		<ul>
        	<?php dynamic_sidebar( $ozy_data->_page_sidebar_name ); ?>
		</ul>
	</div>
	<!--sidebar-->
<?php
}
endwhile; /* end loop */
get_footer(); 
?>