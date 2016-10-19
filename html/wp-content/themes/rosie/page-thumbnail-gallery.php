<?php
/*
Template Name: Gallery : Thumbnail Navigation
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
							$thumb_image 	= wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
							$big_image 		= wp_get_attachment_image_src( $attachment_id, 'blog' );//gridfolio-x-large
							$full_image 	= wp_get_attachment_image_src( $attachment_id, 'full' );

							$output .= '<a class="rsImg" data-rsw="'. $full_image[1] .'" data-rsh="'. $full_image[2] .'" data-rsbigimg="'. $big_image[0] .'" href="'. $full_image[0] .'"><img width="74" height="74" class="rsTmb" src="'. $thumb_image[0] .'"></a>';
                        }
                    ?>

                    <div class="royalSlider rsMinCW" id="royal-classic-thumbnail-full">
                        <?php echo $output; ?>
                    </div>

                </div><!--.post-content .page-content -->
            </article>
        </div><!--#post-# .post-->

    <?php endwhile; ?>
</div><!--#content-->
<?php get_footer(); ?>