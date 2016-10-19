<div id="comments">
	<?php global $post; ?>
	<!-- Prevents loading the file directly -->
	<?php if(!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME'])) : ?>
	    <?php die('Please do not load this page directly. Thanks and have a great day!'); ?>
	<?php endif; ?>
	
	<!-- Password Required -->
	<?php if(!empty($post->post_password)) : ?>
	    <?php if($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) : ?>
	    <?php endif; ?>
	<?php endif; ?>
	
	<?php $i=0;/*$i++;*/ ?> <!-- variable for alternating comment styles -->
	<?php if($comments) : ?>
		<h3><?php comments_number('No comments', 'One comment', '% comments'); ?></h3>
	    <ol>
			<?php wp_list_comments( array('avatar_size' => '60') ); ?>
	    </ol>
	    <?php if (isset($trackback) && $trackback == true) { ?><!-- checks for comment type: trackback -->
	    <h3>Trackbacks</h3>
		    <ol>
		    	<!-- outputs trackbacks -->
			    <?php foreach ($comments as $comment) : ?>
				    <?php $comment_type = get_comment_type(); ?>
				    <?php if($comment_type != 'comment') { ?>
					    <li><?php comment_author_link() ?></li>
				    <?php } ?>
			    <?php endforeach; ?>
		    </ol>
	    <?php } ?>
	<?php else :
		/*enable following block to make visible "no comments yet" message*/
	    /*echo '<p>';
		_e('No comments yet.');
		echo '</p>';*/
	endif; ?>
	
    <div id="comment-navigation" class="page-pagination">
	<?php paginate_comments_links(array('prev_text' => '&laquo;', 'next_text' => '&raquo;')); ?>
    </div>
    
	<div id="comments-form">
	    <div id="respond" class="comment-respond">    
		<?php if(comments_open()) : ?>
        <small><a rel="nofollow" id="cancel-comment-reply-link" href=<?php the_permalink() ?>#respond" style="display:none;"><?php _e('Cancel reply', 'vp_textdomain') ?></a></small>
			<?php if(get_option('comment_registration') && !$user_ID) : ?>
				<p><?php _e('Our apologies, you must be ', 'vp_textdomain'); ?><a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>"><?php _e('logged in', 'vp_textdomain'); ?></a><?php _e(' to post a comment.', 'vp_textdomain'); ?></p><?php else : ?>
                <?php 
					$comment_form_fields = array(
						'author' => 
							'<p class="half-input">
								<label for="author">'. __('Name', 'vp_textdomain') .' <small>' . ($req ? "<span class='required'>*</span>" : "") .'</small></label>
								<input type="text" name="author" id="author" value="'. $comment_author .'" placeholder="'. __('Name', 'vp_textdomain') .' ' . ($req ? "(required)" : "") .'" size="22" tabindex="1" />
							</p>',
						'url' =>
							'<p class="half-input">
								<label for="url">'. __('Website', 'vp_textdomain') .'</label>
								<input type="text" name="url" id="url" value="' .$comment_author_url .'" placeholder="'. __('Website', 'vp_textdomain') .'" size="22" tabindex="2" />
							</p>',							
						'email' => 
							'<p class="full-input">
								<label for="email">'. __('Mail (will not be shared)', 'vp_textdomain') .' <small>' . ($req ? "<span class='required'>*</span>" : "") .'</small></label>
								<input type="text" name="email" id="email" value="'. $comment_author_email .'" placeholder="'. __('Mail (will not be shared)', 'vp_textdomain') .' ' . ($req ? "(required)" : "") .'" size="22" tabindex="3" />
							</p>'
					);
					
					comment_form( array(
						'id' => 'commentform',
						'fields' => apply_filters( 'comment_form_default_fields', $comment_form_fields ),
						'comment_notes_after' => '<p><small>'. __('By submitting a comment you grant ', 'vp_textdomain'). get_bloginfo('name'). __(' a perpetual license to reproduce your words and name/web site in attribution. Inappropriate and irrelevant comments will be removed at an admin’s discretion. Your email is used for verification purposes only, it will never be shared.', 'vp_textdomain') .'</small></p>',
						'comment_field' => '
							<p>
								<label for="comment">'. __('Comment', 'vp_textdomain') .'</label>
								<textarea name="comment" id="comment" cols="100%" placeholder="'. __('Comment', 'vp_textdomain') .'" rows="10" tabindex="4"></textarea>
							</p>
							<p>'. __('Allowed HTML tags:', 'vp_textdomain') . allowed_tags() .'</p>'
					));
				?>
			<?php endif; ?>
		<?php else : ?>
        	<?php if(ozy_get_option('ozy_rosie_page_page_comment_closed') == '1') { ?>
			<p><?php _e('The comments are closed.', 'vp_textdomain'); ?></p>
            <?php } ?>
		<?php endif; ?>
        </div>
	</div><!--#commentsForm-->
</div><!--#comments-->