<?php
/**
 * Plugin Name: ozythemes Rosie Theme Essentials
 * Plugin URI: http://themeforest.net/user/freevision/portfolio
 * Description: This plugin will enable Custom Post types like Portfolio and few other features for your ROSIE theme.
 * Version: 1.1
 * Author: freevision
 */

define( 'OZY_ROSIE_ESSENTIALS_ACTIVATED', 1 );

/**
 * Custom post types for portfolio
 */
function ozy_plugin_create_post_types() {
	
	load_plugin_textdomain('vp_textdomain', false, basename( dirname( __FILE__ ) ) . '/translate');
	
	$essentials_options = get_option('ozy_rosie_essentials');
	if(is_array($essentials_options) && isset($essentials_options['portfolio_slug'])) {
		$portfolio_slug = $essentials_options['portfolio_slug'];
	} else {
		$portfolio_slug = 'portfolio';
	}
	
	//Portfolio
	register_post_type( 'ozy_portfolio',
		array(
			'labels' => array(
				'name' => __( 'Portfolio', 'vp_textdomain'),
				'singular_name' => __( 'Portfolio', 'vp_textdomain'),
				'add_new' => __( 'Add Portfolio Item', 'vp_textdomain'),
				'edit_item' => __( 'Edit Portfolio Item', 'vp_textdomain'),
				'new_item' => __( 'New Portfolio Item', 'vp_textdomain'),
				'view_item' => __( 'View Portfolio Item', 'vp_textdomain'),
				'search_items' => __( 'Search Portfolio Items', 'vp_textdomain'),
				'not_found' => __( 'No Portfolio Items found', 'vp_textdomain'),
				'not_found_in_trash' => __( 'No Portfolio Items found in Trash', 'vp_textdomain')				
			),
			'can_export' => true,
			'public' => true,
			'sort' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => $portfolio_slug, 'with_front' => true),
			'supports' => array('title','editor','thumbnail','excerpt','page-attributes','comments'),
			'menu_icon' => 'dashicons-portfolio'
		)
	);

}
add_action( 'init', 'ozy_plugin_create_post_types', 0 );

/**
 * Custom taxonomy registration
 */
function ozy_plugin_create_custom_taxonomies()
{
	//Portfolio Categories
	$labels = array(
		'name' => __( 'Portfolio Categories', 'vp_textdomain' ),
		'singular_name' => __( 'Portfolio Category', 'vp_textdomain' ),
		'search_items' =>  __( 'Search Portfolio Categories', 'vp_textdomain' ),
		'popular_items' => __( 'Popular Portfolio Categories', 'vp_textdomain' ),
		'all_items' => __( 'All Portfolio Categories', 'vp_textdomain' ),
		'parent_item' => __( 'Parent Portfolio Categories', 'vp_textdomain' ),
		'parent_item_colon' => __( 'Parent Portfolio Category:', 'vp_textdomain' ),
		'edit_item' => __( 'Edit Portfolio Category', 'vp_textdomain' ),
		'update_item' => __( 'Update Portfolio Category', 'vp_textdomain' ),
		'add_new_item' => __( 'Add New Portfolio Category', 'vp_textdomain' ),
		'new_item_name' => __( 'New Portfolio Category', 'vp_textdomain' ),
	);
	
	register_taxonomy('portfolio_category', array('ozy_portfolio'), array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'query_var' => true,
		'sort' => true,
		'rewrite' => array( 'slug' => 'portfolio_category' ),
	));
	

}
add_action( 'init', 'ozy_plugin_create_custom_taxonomies', 0 );

/**
 * Options panel for this plugin
 */
class OzyEssentialsOptionsPage_Rosie
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'ozy Essentials', 
            'manage_options', 
            'ozy-rosie-essentials-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'ozy_rosie_essentials' );
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>ozy Essentials Options</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'ozy_rosie_essentials_option_group' );
                do_settings_sections( 'ozy-rosie-essentials-setting-admin' );
				do_settings_sections( 'ozy-rosie-essentials-setting-admin-twitter' );
			
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'ozy_rosie_essentials_option_group', // Option group
            'ozy_rosie_essentials', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'ozy-rosie-essentials-setting-admin', // ID
            'Options', // Title
            array( $this, 'print_section_info' ), // Callback
            'ozy-rosie-essentials-setting-admin' // Page
        );

        add_settings_field(
            'portfolio_slug', 
            'Portfolio Slug Name', 
            array( $this, 'field_callback' ), 
            'ozy-rosie-essentials-setting-admin', 
            'ozy-rosie-essentials-setting-admin'
        );

        add_settings_section(
            'ozy-rosie-essentials-setting-admin-twitter', 
            'Twitter Parameters', 
            array( $this, 'print_twitter_section_info' ),
            'ozy-rosie-essentials-setting-admin-twitter'
        );		
		
        add_settings_field(
            'twitter_consumer_key', 
            'Consumer Key', 
            array( $this, 'field_callback_twitter_consumer_key' ), 
            'ozy-rosie-essentials-setting-admin-twitter', 
            'ozy-rosie-essentials-setting-admin-twitter'
        );

		add_settings_field(
            'twitter_secret_key', 
            'Secret Key', 
            array( $this, 'field_callback_twitter_secret_key' ), 
            'ozy-rosie-essentials-setting-admin-twitter', 
            'ozy-rosie-essentials-setting-admin-twitter'
        );
		
		add_settings_field(
            'twitter_token_key', 
            'Access Token Key', 
            array( $this, 'field_callback_twitter_token_key' ), 
            'ozy-rosie-essentials-setting-admin-twitter', 
            'ozy-rosie-essentials-setting-admin-twitter'
        );
		
		add_settings_field(
            'twitter_token_secret_key', 
            'Access Token Secret Key', 
            array( $this, 'field_callback_twitter_token_secret_key' ), 
            'ozy-rosie-essentials-setting-admin-twitter', 
            'ozy-rosie-essentials-setting-admin-twitter'
        );		

    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        if( !empty( $input['portfolio_slug'] ) )
            $input['portfolio_slug'] = sanitize_text_field( $input['portfolio_slug'] );

		if( !empty( $input['twitter_consumer_key'] ) )
            $input['twitter_consumer_key'] = sanitize_text_field( $input['twitter_consumer_key'] );

		if( !empty( $input['twitter_secret_key'] ) )
            $input['twitter_secret_key'] = sanitize_text_field( $input['twitter_secret_key'] );

        if( !empty( $input['twitter_token_key'] ) )
            $input['twitter_token_key'] = sanitize_text_field( $input['twitter_token_key'] );

        if( !empty( $input['twitter_token_secret_key'] ) )
            $input['twitter_token_secret_key'] = sanitize_text_field( $input['twitter_token_secret_key'] );			

        return $input;
    }

    /** 
     * Print the Section text
     */

    public function print_section_info()
    {
        print 'Rosie theme parameters.';
    }   

    /** 
     * Print the Section text
     */

    public function print_twitter_section_info()
    {
        print 'Enter required parameters of your Twitter Dev. account <a href="https://dev.twitter.com/apps" target="_blank">https://dev.twitter.com/apps</a>';
    }	

    /** 
     * Get the settings option array and print one of its values : Portfolio Slug
     */
    public function field_callback()
    {
        printf(
            '<input type="text" id="portfolio_slug" name="ozy_rosie_essentials[portfolio_slug]" value="%s" />',
            (!isset($this->options['portfolio_slug']) ? 'portfolio' : esc_attr( $this->options['portfolio_slug']))
        );
    }	
	
    /** 
     * Get the settings option array and print one of its values : Twitter Consumer Key
     */	
    public function field_callback_twitter_consumer_key()
    {
        printf(
            '<input type="text" id="twitter_consumer_key" name="ozy_rosie_essentials[twitter_consumer_key]" value="%s" />',
            (!isset($this->options['twitter_consumer_key']) ? '' : esc_attr( $this->options['twitter_consumer_key']))
        );
    }

    /** 
     * Get the settings option array and print one of its values : Twitter Secret Key
     */	
    public function field_callback_twitter_secret_key()
    {
        printf(
            '<input type="text" id="twitter_secret_key" name="ozy_rosie_essentials[twitter_secret_key]" value="%s" />',
            (!isset($this->options['twitter_secret_key']) ? '' : esc_attr( $this->options['twitter_secret_key']))
        );		
    }

    /** 
     * Get the settings option array and print one of its values : Twitter Token Key
     */	
    public function field_callback_twitter_token_key()
    {
        printf(
            '<input type="text" id="twitter_token_key" name="ozy_rosie_essentials[twitter_token_key]" value="%s" />',
            (!isset($this->options['twitter_token_key']) ? '' : esc_attr( $this->options['twitter_token_key']))
        );		
    }

    /** 
     * Get the settings option array and print one of its values : Twitter Token Secret Key
     */
    public function field_callback_twitter_token_secret_key()
    {
        printf(
            '<input type="text" id="twitter_token_secret_key" name="ozy_rosie_essentials[twitter_token_secret_key]" value="%s" />',
            (!isset($this->options['twitter_token_secret_key']) ? '' : esc_attr( $this->options['twitter_token_secret_key']))
        );		
    }

}

/** 
 * Register activation redirection
 */
register_activation_hook(__FILE__, 'ozy_essentials_plugin_activate');
add_action('admin_init', 'ozy_essentials_plugin_activate_redirect');

function ozy_essentials_plugin_activate() {
    add_option('ozy_essentials_plugin_activate_redirect', true);
}

function ozy_essentials_plugin_activate_redirect() {
    if (get_option('ozy_essentials_plugin_activate_redirect', false)) {
        delete_option('ozy_essentials_plugin_activate_redirect');
        wp_redirect('options-general.php?page=ozy-rosie-essentials-setting-admin');
    }
}

/**
 * We need this plugin to work only on admin side
 */

if( is_admin() ) {
    $ozy_essentials_options_page = new OzyEssentialsOptionsPage_Rosie();
}