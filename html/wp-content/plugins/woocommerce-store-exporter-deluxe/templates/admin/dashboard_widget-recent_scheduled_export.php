<?php if( !empty( $recent_exports ) ) { ?>
<ol>
	<?php foreach( $recent_exports as $recent_export ) { ?>
	<li>
		<p><?php echo $recent_export['name']; ?><?php if( !empty( $recent_export['post_id'] ) && get_post_status( $recent_export['post_id'] ) !== false ) { ?> <a href="<?php echo get_edit_post_link( $recent_export['post_id'] ); ?>">#</a><?php } ?></p>
		<p><?php echo ( isset( $recent_export['scheduled_id'] ) ? sprintf( '<a href="' . get_edit_post_link( $recent_export['scheduled_id'] ) . '">%s</a> - ', woo_ce_format_post_title( get_the_title( $recent_export['scheduled_id'] ) ) ) : '' ); ?><span title="<?php echo woo_ce_format_date( date( 'd/m/Y h:i:s', $recent_export['date'] ), 'd/m/y h:i:s' ); ?>"><?php echo woo_ce_format_archive_date( $recent_export['post_id'], $recent_export['date'] ); ?></span>, <?php echo ( !empty( $recent_export['error'] ) ? __( 'error', 'woocommerce-exporter' ) . ': <span class="error">' . $recent_export['error'] . '</span>' : woo_ce_format_archive_method( $recent_export['method'] ) . '.' ); ?></p>
	</li>
	<?php } ?>
</ol>
<?php } else { ?>
<p><?php _e( 'Ready for your first scheduled export, shouldn\'t be long now.', 'woocommerce-exporter' ); ?>  <strong>:)</strong></p>
<?php } ?>
<p style="text-align:right;"><a href="<?php echo esc_url( add_query_arg( array( 'page' => 'woo_ce', 'tab' => 'archive' ), 'admin.php' ) ); ?>"><?php _e( 'View all archived exports', 'woocommerce-exporter' ); ?></a></p>