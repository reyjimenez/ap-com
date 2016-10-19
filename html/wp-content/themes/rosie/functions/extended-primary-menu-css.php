	.ozy-alternate-menu #sidr-menu>button .lines,
    .ozy-alternate-menu #sidr-menu>button .lines:before,
    .ozy-alternate-menu #sidr-menu>button .lines:after{
    	background:<?php echo ozy_get_option('primary_menu_font_color_2')?>;
    }
    .ozy-alternate-menu #sidr-menu>button:hover .lines,
    .ozy-alternate-menu #sidr-menu>button:hover .lines:before,
    .ozy-alternate-menu #sidr-menu>button:hover .lines:after{
    	background:<?php echo ozy_get_option('primary_menu_font_color_hover_2')?>;
    }
	.ozy-alternate-menu .mega-menu .sub-container,
    .ozy-alternate-menu #slide-menu,
    .ozy-alternate-menu #header{
    	background-color:<?php echo ozy_get_option('primary_menu_background_color_2') ?>;
    }
	.ozy-alternate-menu .sf-menu ul li:hover, 
    .ozy-alternate-menu .sf-menu ul li.sfHover{
    	background-color:<?php echo ozy_get_option('primary_menu_background_color_hover_2') ?>;
    }
	.ozy-alternate-menu .sf-menu .sf-mega, 
    .ozy-alternate-menu .sf-menu ul li, 
    .ozy-alternate-menu .sf-menu ul ul li, 
    .ozy-alternate-menu .sf-menu .sub-menu li:hover, 
    .ozy-alternate-menu .sf-menu .sub-menu li.sfHover, 
    .ozy-alternate-menu #header #slide-menu, 
    .ozy-alternate-menu .sf-menu .sub-menu .current-menu-parent{
    	background-color:<?php echo ozy_get_option('primary_menu_background_color_2') ?>;
    }
	.ozy-alternate-menu #nav-primary .sf-menu>li::after{
    	background-color:<?php echo ozy_get_option('primary_menu_background_color_2') ?>;
    }
	.ozy-alternate-menu .sf-menu a,
    .ozy-alternate-menu .sf-menu>li:before,
    .ozy-alternate-menu .menu-item-wc .sub-container *,
    .ozy-alternate-menu #header-information-bar>div{
    	color: <?php echo ozy_get_option('primary_menu_font_color_2');?>
    }
	.ozy-alternate-menu .menu-item-wc .widget_shopping_cart_content>ul.cart_list>li{
    	border-color:<?php echo  $ozyHelper->change_opacity(ozy_get_option('primary_menu_font_color_2'),.2) ?>;
    }
   	.ozy-alternate-menu #header-information-bar{
        border-color:<?php echo $ozyHelper->change_opacity(ozy_get_option('primary_menu_font_color_2'), '0.3')?>;
    }    
	
	.ozy-alternate-menu .sf-menu ul li:hover>a,
	.ozy-alternate-menu nav>div>ul>li.current-menu-item:before, 
	.ozy-alternate-menu .sf-menu>li.current-menu-ancestor:before, 
	.ozy-alternate-menu .sf-menu>li:hover:before,
	.ozy-alternate-menu .sub-menu .current-menu-parent>a {
    	color:<?php echo ozy_get_option('primary_menu_font_color_hover_2') ?>;
    }
	
	.ozy-alternate-menu .sf-arrows .sf-with-ul:after,
    .ozy-alternate-menu .sf-arrows>li:hover>.sf-with-ul:after{
    	border-top-color: <?php echo ozy_get_option('primary_menu_font_color_2') ?>;
	}
	.ozy-alternate-menu .header-logo>h1>a{color:<?php echo ozy_get_option('primary_menu_logo_color_2')?> !important;}
<?php	
if('mega' === $ozy_data->menu_type) {
?>
	/*mega*/
	.ozy-alternate-menu #nav-primary>nav>div>ul>li:before,
    .ozy-alternate-menu .sub-menu li>h4,
    .ozy-alternate-menu .mega-menu li>a{color:<?php echo ozy_get_option('primary_menu_font_color_2') ?>;}
	.ozy-alternate-menu .mega-menu li:hover>a,
    .ozy-alternate-menu .mega-menu li:hover:before,
    .ozy-alternate-menu .mega-menu li.current-menu-item>a,
    .ozy-alternate-menu .mega-menu li.current-menu-item:before,
    .ozy-alternate-menu .mega-menu li.current-menu-ancestor>a,
    .ozy-alternate-menu .mega-menu li.current-menu-ancestor:before,
    .ozy-alternate-menu #nav-primary>nav>div>ul ul>li.current_page_item>a,
    .ozy-alternate-menu .mega-menu-html-shortcode *:not(input){
    	color:<?php echo ozy_get_option('primary_menu_font_color_hover_2')?> !important;
    }
	.ozy-alternate-menu #nav-primary>nav>div>ul .row>li{
    	border-color:<?php echo $ozyHelper->change_opacity(ozy_get_option('primary_menu_font_color_2'), '.25') ?>;
    }
<?php
	} else if('classic' === $ozy_data->menu_type) {
?>
	/*classic*/
	.ozy-alternate-menu .sf-menu ul li:hover, 
    .ozy-alternate-menu .sf-menu ul li.sfHover, 
    .ozy-alternate-menu #header #slide-menu:hover,
    .ozy-alternate-menu #nav-primary>nav>div>ul ul>li.current_page_item>a{background-color:<?php echo ozy_get_option('primary_menu_background_color_hover_2') ?>;}
	.ozy-alternate-menu .sf-menu .sf-mega, 
    .ozy-alternate-menu .sf-menu ul li, 
    .ozy-alternate-menu .sf-menu ul ul li, 
    .ozy-alternate-menu .sub-menu li:hover, 
    .ozy-alternate-menu .sub-menu li.sfHover, 
    .ozy-alternate-menu #header #slide-menu, 
    .ozy-alternate-menu .sub-menu .current-menu-parent,
    .ozy-alternate-menu #nav-primary .sf-menu>li::after {
    	background-color:<?php echo ozy_get_option('primary_menu_background_color_2') ?>;
    }
	.ozy-alternate-menu .sf-menu>li:hover>a,
    .ozy-alternate-menu .current-menu-parent>a,
    .ozy-alternate-menu .current-menu-ancestor>a,
    .ozy-alternate-menu .current_page_item>a {
		color:<?php echo ozy_get_option('primary_menu_font_color_hover_2') ?>;    
    }
    .ozy-alternate-menu .sf-menu ul li:hover>a,
    .ozy-alternate-menu .sf-menu ul .current-menu-parent>a,
    .ozy-alternate-menu .sf-menu ul .current-menu-ancestor>a,
    .ozy-alternate-menu .sf-menu ul .current_page_item>a {
	    background-color:<?php echo ozy_get_option('primary_menu_background_color_hover_2') ?>;
    }
	.ozy-alternate-menu .sf-arrows .sf-with-ul:after,
    .ozy-alternate-menu .sf-arrows>li:hover>.sf-with-ul:after{
    	border-top-color: <?php echo ozy_get_option('primary_menu_font_color_2') ?>;
    }
<?php
	}	
?>