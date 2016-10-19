		<?php 
		if(!$ozy_data->hide_everything_but_content) { 
		?>
            <div id="header" class="header-v1">
                <div id="top-search" class="clearfix">
                    <div class="container">
                        <form action="<?php echo home_url(); ?>/" method="get" class="wp-search-form">
                            <i class="oic-zoom"></i>
                            <input type="text" name="s" id="search" autocomplete="off" placeholder="<?php echo get_search_query() == '' ? __('Type and hit Enter', 'vp_textdomain') : get_search_query() ?>" />
                            <i class="oic-simple-line-icons-129" id="ozy-close-search"></i>
                        </form>
                    </div>
                </div><!--#top-search-->
				
				<?php
				// Header Information Bar
				if(ozy_get_option('primary_menu_infobar_align', 'hidden') !== 'hidden') {
				?>
                <div id="header-information-bar" class="widget <?php echo ozy_get_option('primary_menu_infobar_align', 'right') ?>">
                	<div class="container">
						<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("ozy-header-information" . $ozy_data->wpml_current_language_) ) : ?><?php endif; ?>
                    </div>
                </div><!--#header-information-bar-->
                <?php
				}
				?>
                
                <header>
                    <div class="container">
                        <div id="header-logo" class="<?php echo $ozy_data->menu_align;?>">
                            <div class="header-logo">
                            <?php
                                $custom_logo_collapsed = '<i class="oic oic-list-1"></i>';
                                if(ozy_get_option('use_custom_logo') == '1') {
                                    echo '<a href="'. get_home_url() .'" id="logo">';
									echo '<img id="logo-default" src="'. ozy_get_option('custom_logo') .'" '. (ozy_get_option('custom_logo_retina') ? 'data-at2x="'. ozy_get_option('custom_logo_retina') .'"' : '') .' data-src="'. ozy_get_option('custom_logo') .'" alt="logo"/>';
									if(ozy_get_option('custom_logo_alternate')){
										echo '<img style="display:none" id="logo-alternate" src="'. ozy_get_option('custom_logo_alternate') .'" '. (ozy_get_option('custom_logo_alternate') ? 'data-at2x="'. ozy_get_option('custom_logo_retina_alternate') .'"' : '') .' alt="logo"/>' . PHP_EOL;
									}						
									echo '</a>';
                                }else{
                                    echo '<h1><a href="'. home_url() .'/" title="'. get_bloginfo('description') .'">'. get_bloginfo('name') .'</a></h1>';
                                }
                            ?>
                            </div>
                        </div><!--#header-logo.container-->                    
                    
                        <div id="nav-primary" class="nav black <?php echo $ozy_data->menu_align;?>"><nav>
                        <?php
							$ozy_data->is_primary_menu_called = true;
							$args = array(
								'menu_class' => ( $ozy_data->menu_type === 'mega' ? 'mega-menu' : 'sf-menu' ), 
								'theme_location' => (is_user_logged_in() ? 'logged-in-menu' : 'header-menu')
							);
							$show_menu = false;
							if(ozy_get_metabox('custom_menu') !== '-1' && ozy_get_metabox('custom_menu')) {
								$args['menu'] = ozy_get_metabox('custom_menu');
								$ozy_data->custome_primary_menu = true;
								$show_menu = true;
							}else if (has_nav_menu('logged-in-menu') && has_nav_menu('header-menu')) {
								$show_menu = true;
							}
							if($show_menu) {
								if( $ozy_data->menu_type === 'mega' ) { $args['walker'] = new ozyMegaMenuWalker; }
								wp_nav_menu( $args );
							}
							$ozy_data->is_primary_menu_called = false;
                        ?>
                        </nav></div><!--#nav-primary-->            
                        <div class="clear"></div>
                    </div><!--.container-->
                </header>
            </div><!--#header-->
        <?php
		} 
		?>