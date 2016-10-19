<?php
/**
 * Menu item custom fields example
 *
 * Copy this file into your wp-content/mu-plugins directory.
 *
 * @package Menu_Item_Custom_Fields
 * @version 0.1.0
 * @author Dzikri Aziz <kvcrvt@gmail.com>
 *
 *
 * Plugin name: Menu Item Custom Fields Example
 * Plugin URI: https://github.com/kucrut/wp-menu-item-custom-fields
 * Description: Example usage of Menu Item Custom Fields in plugins/themes
 * Version: 0.1.0
 * Author: Dzikri Aziz
 * Author URI: http://kucrut.org/
 * License: GPL v2
 * Text Domain: my-plugin
 */


/**
 * Sample menu item metadata
 *
 * This class demonstrate the usage of Menu Item Custom Fields in plugins/themes.
 *
 * @since 0.1.0
 */
class Menu_Item_Custom_Fields_Edit_Style {

	/**
	 * Initialize plugin
	 */
	public static function init() {
		add_action( 'menu_item_custom_fields', array( __CLASS__, '_fields' ), 10, 3 );
		add_action( 'wp_update_nav_menu_item', array( __CLASS__, '_save' ), 10, 3 );
		add_filter( 'manage_nav-menus_columns', array( __CLASS__, '_columns' ), 99 );
	}


	/**
	 * Save custom field value
	 *
	 * @wp_hook action wp_update_nav_menu_item
	 *
	 * @param int   $menu_id         Nav menu ID
	 * @param int   $menu_item_db_id Menu item ID
	 * @param array $menu_item_args  Menu item data
	 */
	public static function _save( $menu_id, $menu_item_db_id, $menu_item_args ) {
		//check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );

		// Sanitize
		if ( ! empty( $_POST['menu-item-style'][ $menu_item_db_id ] ) ) {
			// Do some checks here...
			$value = $_POST['menu-item-style'][ $menu_item_db_id ];
		}
		else {
			$value = '';
		}

		// Update
		if ( ! empty( $value ) ) {
			update_post_meta( $menu_item_db_id, 'menu-item-style', $value );
		}
		else {
			delete_post_meta( $menu_item_db_id, 'menu-item-style' );
		}
	}


	/**
	 * Print field
	 *
	 * @param object $item  Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args  Menu item args.
	 * @param int    $id    Nav menu ID.
	 *
	 * @return string Form fields
	 */
	public static function _fields( $item, $depth, $args = array(), $id = 0 ) {
		?>
			<p class="field-custom description description-wide">
				<label for="edit-menu-item-style-<?php echo esc_attr( $item->ID ) ?>"><?php _e( 'Mega Menu Styling', 'vp_textdomain' ) ?><br />
					<?php printf(
						'<input type="button" value="'. __('Edit Style', 'vp_textdomain') .'" name="menu-item-edit-style[%2$d]" class="widefat code edit-menu-item-edit-style" id="edit-menu-edit-style-%2$d">
						<textarea style="display:none;" name="menu-item-style[%2$d]" class="widefat code edit-menu-item-style" id="edit-menu-item-style-%2$d">%1$s</textarea><i>This option only available on mega menu selection.</i>',
						esc_attr( get_post_meta( $item->ID, 'menu-item-style', true ) ),
						$item->ID
					) ?>
				</label>
			</p>           
		<?php
	}


	/**
	 * Add our field to the screen options toggle
	 *
	 * To make this work, the field wrapper must have the class 'field-custom'
	 *
	 * @param array $columns Menu item columns
	 * @return array
	 */
	public static function _columns( $columns ) {
		$columns['custom_mega_menu_style'] = __( 'Mega Menu Style', 'vp_textdomain' );

		return $columns;
	}
}
Menu_Item_Custom_Fields_Edit_Style::init();

class Menu_Item_Custom_Fields_Mega_Menu_Title {

	/**
	 * Initialize plugin
	 */
	public static function init() {
		add_action( 'menu_item_custom_fields', array( __CLASS__, '_fields' ), 10, 3 );
		add_action( 'wp_update_nav_menu_item', array( __CLASS__, '_save' ), 10, 3 );
		add_filter( 'manage_nav-menus_columns', array( __CLASS__, '_columns' ), 99 );
	}

	/**
	 * Save custom field value
	 *
	 * @wp_hook action wp_update_nav_menu_item
	 *
	 * @param int   $menu_id         Nav menu ID
	 * @param int   $menu_item_db_id Menu item ID
	 * @param array $menu_item_args  Menu item data
	 */
	public static function _save( $menu_id, $menu_item_db_id, $menu_item_args ) {
		//check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );

		// Sanitize
		if ( ! empty( $_POST['menu-item-istitle'][ $menu_item_db_id ] ) ) {
			// Do some checks here...
			$value = $_POST['menu-item-istitle'][ $menu_item_db_id ];
		}
		else {
			$value = '';
		}

		// Update
		if ( ! empty( $value ) ) {
			update_post_meta( $menu_item_db_id, 'menu-item-istitle', $value );
		}
		else {
			delete_post_meta( $menu_item_db_id, 'menu-item-istitle' );
		}
	}


	/**
	 * Print field
	 *
	 * @param object $item  Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args  Menu item args.
	 * @param int    $id    Nav menu ID.
	 *
	 * @return string Form fields
	 */
	public static function _fields( $item, $depth, $args = array(), $id = 0 ) {
		?>
			<p class="field-custom description description-wide">
				<label for="edit-menu-item-istitle-<?php echo esc_attr( $item->ID ) ?>">
					<?php printf(
						'<input type="checkbox" value="1" '. (esc_attr( get_post_meta( $item->ID, 'menu-item-istitle', true )) ? 'checked':'') .' name="menu-item-istitle[%2$d]" class="widefat code menu-item-istitle" id="edit-menu-item-istitle-%2$d">'. __( 'Mega Menu Title?', 'vp_textdomain' ) .'<br/>
						<i>Check this option to display this item as H4 formartted title.</i>',
						esc_attr( get_post_meta( $item->ID, 'menu-item-istitle', true ) ),
						$item->ID
					) ?>
				</label>
			</p>            
		<?php
	}


	/**
	 * Add our field to the screen options toggle
	 *
	 * To make this work, the field wrapper must have the class 'field-custom'
	 *
	 * @param array $columns Menu item columns
	 * @return array
	 */
	public static function _columns( $columns ) {
		$columns['custom_mega_menu_title'] = __( 'Mega Menu Title?', 'vp_textdomain' );

		return $columns;
	}
}
Menu_Item_Custom_Fields_Mega_Menu_Title::init();
