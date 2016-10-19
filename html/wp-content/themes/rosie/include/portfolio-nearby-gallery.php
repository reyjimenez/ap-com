<?php
	$output 	= '';
	foreach(ozy_grab_ids_from_gallery() as $attachment_id) {
		$attachment = get_post($attachment_id);
		$output.= '<a class="rsImg" href="'. $attachment->guid .'">'. ($attachment->post_title ? '<h4>'. $attachment->post_title .'</h4>' : '') . ($attachment->post_excerpt ? '<br/><p>'. $attachment->post_excerpt .'</p>' : '') .'</a>';
	}
?>

    <div class="royalSlider visibleNearby rsMinW" id="royal-nearby-full">
        <?php echo $output; ?>
    </div>
    <!--.royal-slider-->
    
    <div id="royal-slider-counter">../...</div>
    <!--.slide-counter-->    
    
    <div id="full-portfolio-like">
        <a href="#" class="blog-like-link" data-post_id="<?php the_ID(); ?>">
            <div class="fawrapper"><i class="oic-heart-3"></i></div>&nbsp;
            <span><?php echo (int)get_post_meta(get_the_ID(), "ozy_post_like_count", true); ?></span>
        </a>
    </div>
    <!--.like-button-->