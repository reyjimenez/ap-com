<?php
/*
Template Name: Countdown
*/
get_header(); 
?>
<div id="content">
	<div id="ozycounter" class="post">
		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
		<h1><?php the_title() ?></h1>
		<div class="post-content">
			<?php the_content(''); ?>
            <div id="counter"></div>
            <!--#counter-->         
		</div><!--.post-content-->
		<?php endwhile; ?>
	</div><!--#ozycounter .post-->
</div><!--#content-->

<canvas id="canvas" width="100%" height="100%"></canvas>        

<div id="trees"></div>

<?php get_footer(); ?>