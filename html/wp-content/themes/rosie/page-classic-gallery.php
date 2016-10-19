<?php
/*
Template Name: Gallery : Classic Full
*/
get_header(); 

?>
<div id="content" class="no-sidebar template-clean-page">
    <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
        <div id="post-<?php the_ID(); ?>" <?php post_class('page'); ?>>
            <article>
                <div class="post-content page-content">

					<?php
                        $output 	= '';
                        foreach(ozy_grab_ids_from_gallery() as $attachment_id) {
                            $attachment = get_post($attachment_id);
							$output .= '<div class="rsContent" data-rsDelay="6000"><a class="rsImg" href="'. $attachment->guid .'" alt="'. $attachment->post_title .'"></a><div class="infoBlock infoBlockBottomLeft infoBlockBlack rsABlock" data-fade-effect="fa;se" data-move-offset="100" data-move-effect="bottom" data-speed="500">'.($attachment->post_title ? '<h4>'. $attachment->post_title .'</h4>' : '').($attachment->post_excerpt ? '<p>'. $attachment->post_excerpt .'</p>' : '').'</div></div>';
                        }
                    ?>

                    <div class="royalSlider rsMinCW" id="royal-classic-full">
                        <?php echo $output; ?>
                    </div>

                </div><!--.post-content .page-content -->
            </article>
        </div><!--#post-# .post-->

    <?php endwhile; ?>
</div><!--#content-->
<?php get_footer(); ?>