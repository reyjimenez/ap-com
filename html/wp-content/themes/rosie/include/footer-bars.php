            <?php if(!$ozy_data->hide_everything_but_content) { ?>
            
            <?php if(ozy_get_metabox('hide_footer_widget_bar') !== '1' && ozy_get_metabox('hide_footer_widget_bar') !== '2') { ?>
            <div id="footer-widget-bar" class="widget">
                <div class="container">
	                <section class="widget-area">
	                    <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("ozy-footer-widget-bar-one" . $ozy_data->wpml_current_language_) ) : ?><?php endif; ?>
                    <div class="separator"></div>
                    </section>
                    <section class="widget-area">
    	                <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("ozy-footer-widget-bar-two" . $ozy_data->wpml_current_language_) ) : ?><?php endif; ?>
                        <div class="separator"></div>
                    </section>
                    <section class="widget-area">
	                    <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("ozy-footer-widget-bar-three" . $ozy_data->wpml_current_language_) ) : ?><?php endif; ?>
                    </section>
                </div><!--.container-->
            </div><!--#footer-widget-bar-->
            <?php } ?>
			<?php if(ozy_get_metabox('hide_footer_widget_bar') !== '2') { ?>			
            <div id="footer" class="widget"><footer>
                <div class="container">
                    <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("ozy-footer-bar" . $ozy_data->wpml_current_language_) ) : ?><?php endif; ?>
                </div><!--.container-->
            </footer></div><!--#footer-->
            <?php } ?>
            <?php } ?>
