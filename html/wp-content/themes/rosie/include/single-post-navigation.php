            <?php
			if(is_single()) {
			?>
            <!--<div id="cooler-nav" class="navigation">-->
            <?php 
                $prevPost = get_previous_post(true);
                if($prevPost) {?>
                <div class="nav-box previous">
                <?php $prevthumbnail = get_the_post_thumbnail($prevPost->ID, array(100,100) ); $prevthumbnail = $prevthumbnail ? $prevthumbnail : '<img src="'. OZY_BASE_URL .'images/blank.gif" alt="blank">';?>
                <?php previous_post_link('%link',"$prevthumbnail<i class=\"oic-left-dir\"></i><span>%title</span>", TRUE); ?>
                </div>
                <?php } ?>
            
                <?php 
                $nextPost = get_next_post(true);
                if($nextPost) { 
                ?>
                <div class="nav-box next">
                <?php $nextthumbnail = get_the_post_thumbnail($nextPost->ID, array(100,100) );  $nextthumbnail = $nextthumbnail ? $nextthumbnail : '<img src="'. OZY_BASE_URL .'images/blank.gif" alt="blank">'; ?>
                <?php next_post_link('%link',"$nextthumbnail<i class=\"oic-right-dir\"></i><span>%title</span>", TRUE); ?>
                </div>
                <?php } ?>
            <!--</div><!--#cooler-nav div -->            
            <?php
			}
			?>