<?php
/*
Template Name: Gallery : Visible Nearby
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
							$output.= '<a class="rsImg" href="'. $attachment->guid .'">'. ($attachment->post_title ? '<h4>'. $attachment->post_title .'</h4>' : '') . ($attachment->post_excerpt ? '<p>'. $attachment->post_excerpt .'</p>' : '') .'</a>';
                        }
                    ?>

                    <div class="royalSlider visibleNearby rsMinCW" id="royal-nearby-full">
                        <?php echo $output; ?>
                    </div>

                </div><!--.post-content .page-content -->
            </article>
        </div><!--#post-# .post-->

    <?php endwhile; ?>
</div><!--#content-->
<?php get_footer(); ?>