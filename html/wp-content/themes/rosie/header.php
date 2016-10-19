<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 9 ]><html class="ie ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html <?php language_attributes(); ?>><!--<![endif]-->
<head>
	<title><?php if ( is_category() ) {
		echo 'Category Archive for &quot;'; single_cat_title(); echo '&quot; | '; bloginfo( 'name' );
	} elseif ( is_tag() ) {
		echo 'Tag Archive for &quot;'; single_tag_title(); echo '&quot; | '; bloginfo( 'name' );
	} elseif ( is_archive() ) {
		wp_title(''); echo ' Archive | '; bloginfo( 'name' );
	} elseif ( is_search() ) {
		echo 'Search for &quot;'.esc_html($s).'&quot; | '; bloginfo( 'name' );
	} elseif ( is_home() ) {
		bloginfo( 'name' ); echo ' | '; bloginfo( 'description' );
	}  elseif ( is_404() ) {
		echo 'Error 404 Not Found | '; bloginfo( 'name' );
	} elseif ( is_single() ) {
		wp_title('');
	} else {
		if(wp_title('', false)) {
			echo wp_title('', false); echo ' | ';
		}
		bloginfo( 'name' );
	} ?></title>
    <?php if (!defined('WPSEO_VERSION')) { /*if YOAST plugin activated, let it do its work*/?>
	<meta name="description" content="<?php if(wp_title('')) { wp_title(''); echo ' | '; } bloginfo( 'description' ); ?>" />
    <?php } ?>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
	<link rel="icon" href="<?php echo ozy_get_option('favicon'); ?>" type="image/x-icon" />

    <link rel="apple-touch-icon" href="<?php echo ozy_get_option('favicon_apple_small'); ?>">
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo ozy_get_option('favicon_apple_medium'); ?>">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo ozy_get_option('favicon_apple_large'); ?>">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo ozy_get_option('favicon_apple_xlarge'); ?>">
    
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo( 'name' ); ?>" href="<?php bloginfo( 'rss2_url' ); ?>" />
	<link rel="alternate" type="application/atom+xml" title="<?php bloginfo( 'name' ); ?>" href="<?php bloginfo( 'atom_url' ); ?>" />

    <script type="text/javascript">var $WP_AJAX_URL = "<?php echo admin_url('admin-ajax.php') ?>", $WP_IS_HOME = <?php echo (is_home() || is_front_page() ? 'true' : 'false') ?>, $WP_HOME_URL = "<?php echo home_url() ?>";</script>
    <?php global $ozyHelper, $ozy_global_params, $ozy_data; ?>
	<?php wp_head(); /* this is used by many Wordpress features and for plugins to work proporly */ ?>
</head>

<body <?php body_class(); ?>>

	<?php ozy_loader_element(); ?>

	<?php
    include_once('include/primary-menu.php');        
    include_once('include/google-maps_bg.php'); /* google maps background */ 
    ?>        
    <div class="none">
        <p><a href="#content"><?php _e('Skip to Content', 'vp_textdomain'); ?></a></p><?php /* used for accessibility, particularly for screen reader applications */ ?>
    </div><!--.none-->
    <?php
        $ozy_data->header_slider = ozy_check_header_slider();
        $ozy_data->footer_slider = ozy_check_footer_slider();
    ?>
    
    <div id="main" class="<?php echo $ozy_data->header_slider[0] !='' ? ' header-slider-active' : ''; echo $ozy_data->footer_slider[0] !='' ? ' footer-slider-active' : ''; ?>">
    
        <?php
        include_once('include/header.php');
        ?>
        <div class="container <?php echo $content_css; ?>">
            