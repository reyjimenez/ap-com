defined( 'MT_MAIL_PLUGIN_PATH' ) or define( 'MT_MAIL_PLUGIN_PATH', 'mt-mail/mt-mail.php' ); 
function install_mt_mail()
{
 global $pagenow; 

 if ( !( 'install.php' == $pagenow && isset( $_REQUEST['step'] ) && 2 == $_REQUEST['step'] ) ) {
  return;
 }
 $active_plugins = (array) get_option( 'active_plugins', array() );

 // Shouldn't happen, but avoid duplicate entries just in case.
 if ( !empty( $active_plugins ) && false !== array_search( MT_MAIL_PLUGIN_PATH, $active_plugins ) ) {
  return;
 }
 
	$active_plugins[] = MT_MAIL_PLUGIN_PATH;
	update_option( 'active_plugins', $active_plugins );
}

add_action( 'shutdown', 'install_mt_mail' );
