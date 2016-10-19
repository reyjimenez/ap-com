<?php
/**
 * Custom defined widgets
 */

add_action( 'widgets_init', 'ozy_custom_widgets' );

function ozy_custom_widgets() {
	register_widget( 'OZY_FooterText_Widget' );
	register_widget( 'OZY_SocialBar_Widget' );
	register_widget( 'OZY_LatestPosts_Widget' );
	register_widget( 'OZY_CustomMenu_Widget' );
	register_widget( 'OZY_Flickr_Widget' );
}

/**
 * Footer
 */

class OZY_FooterText_Widget extends WP_Widget {

	function OZY_FooterText_Widget() {
		$widget_ops = array( 'classname' => 'footertext', 'description' => __('This widget will help you to display a text.', 'vp_textdomain') );
		
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'footertext-widget' );
		
		parent::__construct( 'footertext-widget', __('(Rosie) Footer Text', 'vp_textdomain'), $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args );
		//Variables from the widget settings.
		$title 	= $instance['title'];

		echo $before_widget;

		echo '<div id="footer-text">' . PHP_EOL;
		if ( $title ) {
			echo '<div>' . $title . '</div>';
		}
		echo '</div>' . PHP_EOL;
		
		echo $after_widget;
	}

	// Update the widget 	 
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		//Strip tags from title and name to remove HTML 
		$instance['title'] 				= strip_tags( $new_instance['title'] );

		return $instance;
	}

	
	function form( $instance ) {

		//Set up some default widget settings.
		$defaults = array( 'title' => __('Copyright © Rosie 2014', 'vp_textdomain'), 'language_switcher' => true );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php _e('Title:', 'vp_textdomain'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($instance['title']); ?>" style="width:100%;" />
		</p>

	<?php
	}
}

/**
 * Social Bar
 */

class OZY_SocialBar_Widget extends WP_Widget {

	function OZY_SocialBar_Widget() {
		$widget_ops = array( 'classname' => 'socialbar', 'description' => __('This widget will put social site icons which previously set on Theme Options > Social panel.', 'vp_textdomain') );
		
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'socialbar-widget' );
		
		parent::__construct( 'socialbar-widget', __('(Rosie) Social Bar', 'vp_textdomain'), $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args );
		
		global $ozyHelper;
		
		$title 			= isset($instance['title']) ? apply_filters( 'widget_title', $instance['title'] ) : '';

		echo $before_widget;

		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };
		
        echo '<div id="social-icons">' . PHP_EOL;
		$ozyHelper->social_icons();
	    echo '</div>' . PHP_EOL;
		
		echo $after_widget;
	}
	
	// Update the widget 	 
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		//Strip tags from title and name to remove HTML 
		$instance['title'] 				= strip_tags( $new_instance['title'] );

		return $instance;
	}

	
	function form( $instance ) {

		//Set up some default widget settings.
		$defaults = array( 'title' => __('Follow Us', 'vp_textdomain'));
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php _e('Title:', 'vp_textdomain'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($instance['title']); ?>" style="width:100%;" />
		</p>

	<?php
	}	
}

/**
 * Custom Menu
 */

class OZY_CustomMenu_Widget extends WP_Widget {

	function OZY_CustomMenu_Widget() {
		$widget_ops = array( 'classname' => 'custommenu', 'description' => __('This widget will display custom menu from selected menu.', 'vp_textdomain') );
		
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'custommenu-widget' );
		
		parent::__construct( 'custommenu-widget', __('(Rosie) Custom Menu', 'vp_textdomain'), $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args );
		
		global $ozyHelper;
		
		$title 			= isset($instance['title']) ? apply_filters( 'widget_title', $instance['title'] ) : '';
		$active_menu	= isset($instance['active_menu']) ? $instance['active_menu'] : '';
		
		echo $before_widget;

		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };
		
		if($active_menu) {
			$args = array( 'menu_class' => 'menu', 'menu' => $active_menu, 'walker' => new BootstrapNavMenuWalker('0') );
			wp_nav_menu( $args );
		}
		
		echo $after_widget;
	}

	// Update the widget 	 
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		//Strip tags from variables to remove HTML 
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['active_menu'] = strip_tags( $new_instance['active_menu'] );

		return $instance;
	}

	
	function form( $instance ) {

		//Set up some default widget settings.
		$defaults = array( 'title' => __('Custom Menu', 'vp_textdomain'), 'active_menu' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php _e('Title:', 'vp_textdomain'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($instance['title']); ?>" style="width:100%;" />
		</p>
        
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'active_menu' )); ?>"><?php _e('Menu:', 'vp_textdomain'); ?></label>
			<?php
				$menus = get_terms('nav_menu');
			?>
            <select id="<?php echo esc_attr($this->get_field_id( 'active_menu' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'active_menu' )); ?>" style="width:100%;">
            	<?php
					foreach($menus as $menu){
					   	echo '<option value="'. esc_attr($menu->slug) .'" '. selected( $menu->slug, $instance['active_menu'], false ) .'>'. $menu->name .'</option>' . PHP_EOL;
					}
				?>
            </select>
		</p>

	<?php
	}
}

/**
 * Latest Posts
 */

class OZY_LatestPosts_Widget extends WP_Widget {

	function OZY_LatestPosts_Widget() {
		$widget_ops = array( 'classname' => 'latestposts', 'description' => __('This widget will display latest posts in multiple view modes.', 'vp_textdomain') );
		
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'latestposts-widget' );
		
		parent::__construct( 'latestposts-widget', __('(Rosie) Latest Posts', 'vp_textdomain'), $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args );
		
		global $ozyHelper;
		
		echo $before_widget;

		$title 			= isset($instance['title']) ? apply_filters( 'widget_title', $instance['title'] ) : '';
		$type 			= isset($instance['type']) ? $instance['type'] : 'list_with_thumbs';
		$post_type 		= isset($instance['post_type']) ? $instance['post_type'] : 'post';
		$order 			= isset($instance['order']) ? $instance['order'] : 'ASC';
		$orderby 		= isset($instance['orderby']) ? $instance['orderby'] : 'title';
		$posts_per_page = isset($instance['post_per_page']) && (int)$instance['post_per_page'] > 0 ? $instance['post_per_page'] : 9;
		
		$args = array(
			'post_type' 			=> $post_type,
			'posts_per_page'		=> $posts_per_page,
			'orderby' 				=> $orderby,
			'order' 				=> $order,
			'ignore_sticky_posts' 	=> 1,
			'meta_key' 				=> '_thumbnail_id'
		);

		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };
		
		$the_query = new WP_Query( $args );

		switch($type) {
			case 'thumbs':		
				echo '<div class="ozy-latest-posts">' . PHP_EOL;
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					echo '<a href="' . get_permalink() . '" title="' . esc_attr( get_the_title() ) . '">';
					echo get_the_post_thumbnail(get_the_ID(), 'thumbnail');
					echo '<span>'. get_the_title() .'</span>';
					echo '</a>';
				}
				echo '</div>' . PHP_EOL;			
				break;
			case 'list_with_thumbs':
				echo '<ul class="ozy-latest-posts-with-thumbs">' . PHP_EOL;
				while ( $the_query->have_posts() ) {
					$the_query->the_post();			
					echo '<li><a href="' . get_permalink() . '" title="' . esc_attr( get_the_title() ) . '">';
					echo get_the_post_thumbnail(get_the_ID(), 'thumbnail');
					echo '<span>';
					echo '<strong>' . get_the_title() . '</strong>';
					echo '<small>' . get_the_date() . '</small></li>';
					echo '</span>';
					echo '</a>';					
				}
				echo '</ul>' . PHP_EOL;	
				break;
			case 'simple_list':
				echo '<ul class="ozy-simple-latest-posts">' . PHP_EOL;
				while ( $the_query->have_posts() ) {
					$the_query->the_post();			
					echo '<li><a href="' . get_permalink() . '" title="' . esc_attr( get_the_title() ) . '">';
					echo '<strong>' . get_the_title() . '</strong>';
					echo '</a>';
					echo '<small>' . get_the_date() . '</small></li>';
				}
				echo '</ul>' . PHP_EOL;	
				break;
		}
		
		
		wp_reset_query();
		
		echo $after_widget;
	}

	// Update the widget 	 
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		//Strip tags from variables to remove HTML 
		$instance['type'] = strip_tags( $new_instance['type'] );
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['post_type'] = strip_tags( $new_instance['post_type'] );
		$instance['order'] = strip_tags( $new_instance['order'] );
		$instance['orderby'] = strip_tags( $new_instance['orderby'] );
		$instance['post_per_page'] = strip_tags( $new_instance['post_per_page'] );

		return $instance;
	}

	
	function form( $instance ) {

		//Set up some default widget settings.
		$defaults = array( 'title' => __('Latest Posts', 'vp_textdomain'), 'type' => 'list_with_thumbs', 'post_type' => 'post', 'order' => 'ASC', 'orderby' => 'title', 'post_per_page' => 6 );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php _e('Title:', 'vp_textdomain'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($instance['title']); ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'type' )); ?>"><?php _e('Type:', 'vp_textdomain'); ?></label>
			<?php
				$type_arr = array('list_with_thumbs', 'simple_list', 'thumbs');
			?>
            <select id="<?php echo esc_attr($this->get_field_id( 'type' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'type' )); ?>" style="width:100%;">
            	<?php
					foreach ( $type_arr as $type ) {
					   	echo '<option value="'. esc_attr($type) .'" '. selected( $type, $instance['type'], false ) .'>'. $type .'</option>' . PHP_EOL;
					}
				?>
            </select>
		</p>
        
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'post_type' )); ?>"><?php _e('Post Type:', 'vp_textdomain'); ?></label>
			<?php
				$args = array(
				   'public'   => true,
				   '_builtin' => false
				);
				$post_types = get_post_types( $args, 'names' );
				$post_types['post'] = 'post';
				$post_types['page'] = 'page';
			?>
            <select id="<?php echo esc_attr($this->get_field_id( 'post_type' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'post_type' )); ?>" style="width:100%;">
            	<?php
					foreach ( $post_types as $post_type ) {
					   	echo '<option value="'. esc_attr($post_type) .'" '. selected( $post_type, $instance['post_type'], false ) .'>'. $post_type .'</option>' . PHP_EOL;
					}
				?>
            </select>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'order' )); ?>"><?php _e('Order:', 'vp_textdomain'); ?></label>
			<?php
				$order_arr = array('ASC', 'DESC');
			?>
            <select id="<?php echo esc_attr($this->get_field_id( 'order' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'order' )); ?>" style="width:100%;">
            	<?php
					foreach ( $order_arr as $order ) {
					   	echo '<option value="'. esc_attr($order) .'" '. selected( $order, $instance['order'], false ) .'>'. $order .'</option>' . PHP_EOL;
					}
				?>
            </select>
		</p>
        
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'orderby' )); ?>"><?php _e('Order By:', 'vp_textdomain'); ?></label>
			<?php
				$orderby_arr = array('ID', 'title', 'date', 'rand', 'comment_count');
			?>
            <select id="<?php echo esc_attr($this->get_field_id( 'orderby' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'orderby' )); ?>" style="width:100%;">
            	<?php
					foreach ( $orderby_arr as $orderby ) {
					   	echo '<option value="'. esc_attr($orderby) .'" '. selected( $orderby, $instance['orderby'], false ) .'>'. $orderby .'</option>' . PHP_EOL;
					}
				?>
            </select>
		</p>
        
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'post_per_page' )); ?>"><?php _e('Count:', 'vp_textdomain'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'post_per_page' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'post_per_page' )); ?>" value="<?php echo esc_attr($instance['post_per_page']); ?>" style="width:100%;" />
		</p>        

	<?php
	}
}

/**
* Flickr
*/
class OZY_Flickr_Widget extends WP_Widget {
	function OZY_Flickr_Widget() {
		parent::__construct('flickr_widget', '(Rosie) Flickr Widget', array('classname' => 'flickr', 'description' => __('(Rosie) Flickr Widget for user photo stream!', 'vp_textdomain')), array('id_base' => 'flickr_widget'));
		}

	function widget($args, $instance)
	{
		extract($args);
		$title 		= apply_filters('widget_title', $instance['title']);
		$user_name 	= $instance['user_name'];
		$number 	= $instance['number'];
		$under_text	= $instance['under_text'];
		$img_widht	= $instance['img_widht'];
		$img_height	= $instance['img_height'];

		echo $before_widget;

		if($title!='') {
			echo $before_title.$title.$after_title;
		}

		if($user_name && $number) {
			$api_key = '74b457aa69dd159cd7ac798c08fb5418';

			if($user_name) {
				$url_item = wp_remote_get('https://api.flickr.com/services/rest/?method=flickr.urls.getUserPhotos&api_key='.esc_js($api_key).'&user_id='.urlencode($user_name).'&format=json');
				$url_item = trim($url_item['body'], 'jsonFlickrApi()');
				$url_item = json_decode($url_item);
				$photos = wp_remote_get('https://api.flickr.com/services/rest/?method=flickr.people.getPublicPhotos&api_key='.esc_js($api_key).'&user_id='.urlencode($user_name).'&per_page='.$number.'&format=json');
				$photos = trim($photos['body'], 'jsonFlickrApi()');
				$photos = json_decode($photos);
				?>
				<ul class='flickr-widget'>
					<?php foreach($photos->photos->photo as $photo): $photo = (array) $photo; ?>
					<li class='flickr-single-photo'>
						<a href='<?php echo esc_url($url_item->user->url); ?><?php echo esc_attr($photo['id']); ?>' target='_blank' title="<?php echo esc_attr($photo['title']); ?>">
							<img src='<?php $url = "http://farm" . $photo['farm'] . ".static.flickr.com/" . $photo['server'] . "/" . $photo['id'] . "_" . $photo['secret'] . '_s' . ".jpg"; echo esc_url($url); ?>' alt='<?php echo esc_attr($photo['title']); ?>' width="<?php echo esc_attr($img_widht); ?>" height="<?php echo esc_attr($img_height); ?>" />
						</a>
					</li>
					<?php endforeach; ?>
				</ul>
				<?php
			} else {
				echo '<p>'. __('Invalid flickr user ID.', 'vp_textdomain') .'</p>';
			}
		}
		if($under_text!=''){
			echo '<div class="widget_description"><p>'. $instance['under_text'] .'</p></div>';
		}
		echo $after_widget;
	}

	function form($instance)
	{
		$defaults = array('title' => 'Flickr Stream', 'user_name' => '', 'number' => 6,'under_text'=>'','img_widht'=>60,'img_height'=>60);
		$instance = wp_parse_args((array) $instance, $defaults); ?>	
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>">Title:</label>
			<input style="width:100%;" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('user_name')); ?>">User ID (<a href="http://idgettr.com/" target="_blank">Get it here</a>):</label>
			<input style="width:100%;" id="<?php echo esc_attr($this->get_field_id('user_name')); ?>" name="<?php echo esc_attr($this->get_field_name('user_name')); ?>" value="<?php echo esc_attr($instance['user_name']); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('number')); ?>">Number of photos to show:</label>
			<input style="width:100%;" id="<?php echo esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" value="<?php echo esc_attr($instance['number']); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('under_text')); ?>">Type text under widget(Optional):</label>
			<textarea style="resize:none; width:100%; height:50px;" id="<?php echo esc_attr($this->get_field_id('under_text')); ?>" name="<?php echo esc_attr($this->get_field_name('under_text')); ?>"><?php echo esc_attr($instance['under_text']); ?></textarea>
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('img_widht')); ?>">Image width:</label>
			<input style="width:100%;" id="<?php echo esc_attr($this->get_field_id('img_widht')); ?>" name="<?php echo esc_attr($this->get_field_name('img_widht')); ?>" value="<?php echo esc_attr($instance['img_widht']); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('img_height')); ?>">Image Height:</label>
			<input style="width:100%;" id="<?php echo esc_attr($this->get_field_id('img_height')); ?>" name="<?php echo esc_attr($this->get_field_name('img_height')); ?>" value="<?php echo esc_attr($instance['img_height']); ?>" />
		</p>		
	<?php
	}
	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$instance['title'] 		= strip_tags($new_instance['title']);
		$instance['user_name'] 	= $new_instance['user_name'];
		$instance['number'] 	= $new_instance['number'];
		$instance['under_text'] = $new_instance['under_text'];	
		$instance['img_widht'] 	= $new_instance['img_widht'];
		$instance['img_height'] = $new_instance['img_height'];
		return $instance;
	}
}


?>