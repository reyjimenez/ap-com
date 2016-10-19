<h3>
	<?php _e( 'Scheduled Exports', 'woocommerce-exporter' ); ?>
	<a href="<?php echo esc_url( admin_url( add_query_arg( 'post_type', 'scheduled_export', 'post-new.php' ) ) ); ?>" class="add-new-h2"><?php _e( 'Add New', 'woocommerce-exporter' ); ?></a>
</h3>

<table class="widefat page fixed scheduled-exports">
	<thead>

		<tr>
			<th class="manage-column"><?php _e( 'Name', 'woocommerce-exporter' ); ?></th>
			<th class="manage-column"><?php _e( 'Export Type', 'woocommerce-exporter' ); ?></th>
			<th class="manage-column"><?php _e( 'Export Format', 'woocommerce-exporter' ); ?></th>
			<th class="manage-column"><?php _e( 'Export Method', 'woocommerce-exporter' ); ?></th>
			<th class="manage-column"><?php _e( 'Status', 'woocommerce-exporter' ); ?></th>
			<th class="manage-column"><?php _e( 'Frequency', 'woocommerce-exporter' ); ?></th>
			<th class="manage-column"><?php _e( 'Action', 'woocommerce-exporter' ); ?></th>
		</tr>

	</thead>
	<tbody id="the-list">

<?php if( !empty( $scheduled_exports ) ) { ?>
	<?php foreach( $scheduled_exports as $scheduled_export ) { ?>
		<tr id="post-<?php echo $scheduled_export; ?>">
			<td class="post-title column-title">
				<strong><a href="<?php echo get_edit_post_link( $scheduled_export ); ?>" title="<?php _e( 'Edit scheduled export', 'woocommerce-exporter' ); ?>"><?php echo woo_ce_format_post_title( get_the_title( $scheduled_export ) ); ?></a></strong>
				<div class="row-actions">
					<a href="<?php echo get_edit_post_link( $scheduled_export ); ?>" title="<?php _e( 'Edit this scheduled export', 'woocommerce-exporter' ); ?>"><?php _e( 'Edit', 'woocommerce-exporter' ); ?></a> | 
					<span class="trash"><a href="<?php echo get_delete_post_link( $scheduled_export ); ?>" class="submitdelete" title="<?php _e( 'Delete this scheduled export', 'woocommerce-exporter' ); ?>"><?php _e( 'Delete', 'woocommerce-exporter' ); ?></a></span>
				</div>
			</td>
			<td><?php echo ucfirst( get_post_meta( $scheduled_export, '_export_type', true ) ); ?></td>
			<td><?php echo strtoupper( get_post_meta( $scheduled_export, '_export_format', true ) ); ?></td>
			<td><?php echo woo_ce_format_export_method( get_post_meta( $scheduled_export, '_export_method', true ) ); ?></td>
			<td><?php echo ucfirst( get_post_status( $scheduled_export ) ); ?></td>
			<td><?php echo ucfirst( get_post_meta( $scheduled_export, '_auto_schedule', true ) == 'custom' ? sprintf( __( 'Every %d minutes', 'woocommerce-exporter' ), get_post_meta( $scheduled_export, '_auto_interval', true ) ) : get_post_meta( $scheduled_export, '_auto_schedule', true ) ); ?></td>
			<td>
				<a href="<?php echo add_query_arg( array( 'action' => 'override_scheduled_export', 'post' => $scheduled_export, '_wpnonce' => wp_create_nonce( 'woo_ce_override_scheduled_export' ) ) ); ?>" title="<?php echo ( ( get_post_status( $scheduled_export ) == 'draft' || $enable_auto == false ) ? __( 'Scheduled exports are turned off.', 'woocommerce-exporter' ) : __( 'Run this scheduled export now', 'woocommerce-exporter' ) ); ?>" class="button<?php echo( ( get_post_status( $scheduled_export ) == 'draft' || $enable_auto == false ) ? ' disabled' : '' ); ?>"><?php _e( 'Execute', 'woocommerce-exporter' ); ?></a>
			</td>
		</tr>

	<?php } ?>
<?php } else { ?>
		<tr>
				<td class="colspanchange" colspan="6"><?php _e( 'No scheduled exports found.', 'woocommerce-exporter' ); ?></td>
		</tr>
<?php } ?>

	</tbody>
	<tfoot>

		<tr>
			<th class="manage-column"><?php _e( 'Name', 'woocommerce-exporter' ); ?></th>
			<th class="manage-column"><?php _e( 'Export Type', 'woocommerce-exporter' ); ?></th>
			<th class="manage-column"><?php _e( 'Export Format', 'woocommerce-exporter' ); ?></th>
			<th class="manage-column"><?php _e( 'Export Method', 'woocommerce-exporter' ); ?></th>
			<th class="manage-column"><?php _e( 'Status', 'woocommerce-exporter' ); ?></th>
			<th class="manage-column"><?php _e( 'Frequency', 'woocommerce-exporter' ); ?></th>
			<th class="manage-column"><?php _e( 'Action', 'woocommerce-exporter' ); ?></th>
		</tr>

	</tfoot>

</table>
<!-- .scheduled-exports -->

<?php if( !empty( $scheduled_exports ) ) { ?>
<p style="text-align:right;"><?php printf( __( '%d items', 'woocommerce-exporter' ), count( $scheduled_exports ) ); ?></p>
<?php } ?>