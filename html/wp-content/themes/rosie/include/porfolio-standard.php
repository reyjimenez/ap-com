					<?php
						$portfolio_category = 0;
						$div_class = array('portfolio-details-part-one','portfolio-details-part-two',1);
						if(vp_metabox('ozy_rosie_meta_portfolio.ozy_rosie_meta_portfolio_hide_meta_info') == '1') {
							$div_class = array('','',0);
						}
					?>
				
                    <h2 class="post-title"><?php the_title() ?></h2>
                    
                    <div class="<?php echo $div_class[0] ?>">					
                        <?php 
							if( 'inpage-slider' !== $post_format && 'inpage-slider-full' !== $post_format ) {
								the_content();
							}else{
								ozy_convert_classic_gallery();
							}
						?>
                    </div>
                    <?php
					if($div_class[2] == '1') {
					?>
                    <div class="<?php echo $div_class[1] ?> has-border">
                    <?php
                        //Meta info boxes
						$meta_info_boxes = vp_metabox('ozy_rosie_meta_portfolio.ozy_rosie_meta_portfolio_meta_info');
						if(is_array($meta_info_boxes) && count($meta_info_boxes) > 0) {
	                        foreach($meta_info_boxes as $meta_info) {
								if($meta_info['ozy_rosie_meta_portfolio_meta_info_label']) {
    	                        	echo '<p><strong>' . $meta_info['ozy_rosie_meta_portfolio_meta_info_label'] . ':</strong> ' . $ozyHelper->convert_to_href( $meta_info['ozy_rosie_meta_portfolio_meta_info_value'] ) . '</p>';
								}
        	                }
						}
                        // Post category
                        echo '<p><strong>'. __('Category', 'vp_textdomain') .':</strong> ';
						$comma = '';
                        foreach (get_the_terms($post->ID, 'portfolio_category') as $cat) {
                            echo $comma . $cat->name;
							$comma = ', ';
                            $portfolio_category = $cat->term_id;
                        }
                        echo '</p>';
                    ?>
                    </div>
                    <?php
					}else{
                        // Post category
                        foreach (get_the_terms($post->ID, 'portfolio_category') as $cat) {$portfolio_category = $cat->term_id;}						
					}
					?>
            
					<?php 
                    if(ozy_get_option('page_portfolio_share') == '1') {
						include('share-buttons.php');
                    }			
                    ?>                   
                    
					<?php wp_link_pages('before=<div class="pagination">'. __('Pages: ', 'vp_textdomain') .'&after=</div>'); ?>
                    
                    <div class="clear"></div> 
                    
                    <?php
					include_once('portfolio-related-posts-navigation.php');
					?>