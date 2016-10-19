<?php
	$output 	= '';
	foreach(ozy_grab_ids_from_gallery() as $attachment_id) {
		$attachment = get_post($attachment_id);
		if(isset($attachment->guid) && isset($attachment->post_title) && isset($attachment->post_excerpt)) {
			$output .= '<div class="rsContent" data-rsDelay="6000"><img class="rsImg" src="'. $attachment->guid .'" alt="'. $attachment->post_title .'" /><div class="infoBlock infoBlockTopLeft infoBlockBlack rsABlock" data-fade-effect="fa;se" data-move-offset="100" data-move-effect="bottom" data-speed="500">'.($attachment->post_title ? '<h4>'. $attachment->post_title .'</h4>' : '').($attachment->post_excerpt ? '<p>'. $attachment->post_excerpt .'</p>' : '').'</div></div>';
		}
	}
?>
    <div class="royalSlider rsMinCW" id="royal-classic-full">
        <?php echo $output; ?>
    </div>
    <!--.royal-slider-->
    
    <div id="royal-slider-counter">.. / ...</div>
    <!--.slide-counter-->
    
    <div id="full-portfolio-like">
        <a href="#" class="blog-like-link" data-post_id="<?php the_ID(); ?>">
            <div class="fawrapper"><i class="oic-heart-3"></i></div>&nbsp;
            <span><?php echo (int)get_post_meta(get_the_ID(), "ozy_post_like_count", true); ?></span>
        </a>
    </div>
    <!--.like-button-->