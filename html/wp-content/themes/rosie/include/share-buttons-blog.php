                            <div class="post-submeta">
	                            <?php if(!$hide_title && comments_open()) { ?>
                                <a href="<?php the_permalink() ?>#comments" class="button"><i class="oic-simple-line-icons-73"></i><span><?php comments_number('0', '1', '%') ?></span></a>
                                <?php } ?>
                                <a href="<?php the_permalink() ?>" class="button blog-like-link" data-post_id="<?php echo $post->ID; ?>"><i class="oic-simple-line-icons-137"></i><span><?php echo (int)get_post_meta($post->ID, "ozy_post_like_count", true); ?></span></a>
                               	<?php if(!$hide_title) { ?>
                                <a href="<?php the_permalink() ?>" class="button post-share" data-open="0"><i class="oic-simple-line-icons-90"></i></a>
                                <div>
                                	<div class="arrow"></div>
                                    <div class="button">
                                    	<a href="http://www.facebook.com/share.php?u=<?php the_permalink() ?>"><span class="symbol">facebook</span></a>
                                        <a href="https://twitter.com/share?url=<?php the_permalink() ?>"><span class="symbol">twitterbird</span></a>
                                        <a href="https://www.linkedin.com/cws/share?url=<?php the_permalink() ?>"><span class="symbol">linkedin</span></a>
                                        <a href="https://plus.google.com/share?url=<?php the_permalink() ?>"><span class="symbol">googleplus</span></a>
                                        <a href="http://pinterest.com/pin/create/button/?url=<?php the_permalink() ?>"><span class="symbol">pinterest</span></a>
                                    </div>
                                </div>                                
                                <?php
								}
								if(!$hide_title) { echo '<a href="'. get_permalink() .'">'. __('Continue Reading &rarr;', 'vp_textdomain') .'</a>'; } ?>                                
                            </div>