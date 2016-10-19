<?php 
//global $ozyHelper, $ozy_data, $woocommerce;

get_header(); 

ozy_woocommerce_meta_params();

/* Widgetized LEFT sidebar */
if(function_exists( 'dynamic_sidebar' ) && $ozyHelper->hasIt($ozy_data->_woocommerce_content_css_name,'left-sidebar') && $ozy_data->_woocommerce_sidebar_name) {
?>
	<div id="sidebar" class="<?php echo $ozy_data->_woocommerce_content_css_name; ?>">
		<ul>
        	<?php dynamic_sidebar( $ozy_data->_woocommerce_sidebar_name ); ?>
		</ul>
	</div>
	<!--sidebar-->
<?php
}
?>
<div id="content" class="<?php echo $ozy_data->_woocommerce_content_css_name; ?>">

    <div id="post-<?php the_ID(); ?>" <?php post_class('page'); ?>>
        <article>
            <div class="post-content page-content">
                <?php 
                    woocommerce_content();
                ?>
            </div><!--.post-content .page-content -->
        </article>			
    </div>
        
</div><!--#content-->

<?php
/* Widgetized RIGHT sidebar */
if(function_exists( 'dynamic_sidebar' )  && $ozyHelper->hasIt($ozy_data->_woocommerce_content_css_name,'right-sidebar') && $ozy_data->_woocommerce_sidebar_name) {
?>
	<div id="sidebar" class="<?php echo $ozy_data->_woocommerce_content_css_name; ?>">
		<ul>
        	<?php dynamic_sidebar( $ozy_data->_woocommerce_sidebar_name ); ?>
		</ul>
	</div>
	<!--sidebar-->
<?php
}
get_footer();
?>
