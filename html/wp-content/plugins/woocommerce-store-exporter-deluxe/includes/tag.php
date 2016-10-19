<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	function woo_ce_get_export_type_tag_count() {

		$count = 0;
		// Check if the existing Transient exists
		$cached = get_transient( WOO_CD_PREFIX . '_tag_count' );
		if( $cached == false ) {
			$term_taxonomy = 'product_tag';
			if( taxonomy_exists( $term_taxonomy ) )
				$count = wp_count_terms( $term_taxonomy );
			set_transient( WOO_CD_PREFIX . '_tag_count', $count, HOUR_IN_SECONDS );
		} else {
			$count = $cached;
		}
		return $count;

	}

	// HTML template for Filter Tags by Language widget on Store Exporter screen
	function woo_ce_tags_filter_by_language() {

		if( !woo_ce_detect_wpml() )
			return;

		$languages = ( function_exists( 'icl_get_languages' ) ? icl_get_languages( 'skip_missing=N' ) : array() );

		ob_start(); ?>
<p><label><input type="checkbox" id="tags-filters-language" /> <?php _e( 'Filter Tags by Language', 'woocommerce-exporter' ); ?></label></p>
<div id="export-tags-filters-language" class="separator">
	<ul>
		<li>
<?php if( !empty( $languages ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Language...', 'woocommerce-exporter' ); ?>" name="tag_filter_language[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $languages as $key => $language ) { ?>
				<option value="<?php echo $key; ?>"><?php echo $language['native_name']; ?> (<?php echo $language['translated_name']; ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Languages were found.', 'woocommerce-exporter' ); ?></li>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Language\'s you want to filter exported Tags by. Default is to include all Language\'s.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-tags-filters-language -->

<?php
		ob_end_flush();

	}

	// HTML template for Tag Sorting widget on Store Exporter screen
	function woo_ce_tag_sorting() {

		$tag_orderby = woo_ce_get_option( 'tag_orderby', 'ID' );
		$tag_order = woo_ce_get_option( 'tag_order', 'DESC' );

		ob_start(); ?>
<p><label><?php _e( 'Product Tag Sorting', 'woocommerce-exporter' ); ?></label></p>
<div>
	<select name="tag_orderby">
		<option value="id"<?php selected( 'id', $tag_orderby ); ?>><?php _e( 'Term ID', 'woocommerce-exporter' ); ?></option>
		<option value="name"<?php selected( 'name', $tag_orderby ); ?>><?php _e( 'Tag Name', 'woocommerce-exporter' ); ?></option>
	</select>
	<select name="tag_order">
		<option value="ASC"<?php selected( 'ASC', $tag_order ); ?>><?php _e( 'Ascending', 'woocommerce-exporter' ); ?></option>
		<option value="DESC"<?php selected( 'DESC', $tag_order ); ?>><?php _e( 'Descending', 'woocommerce-exporter' ); ?></option>
	</select>
	<p class="description"><?php _e( 'Select the sorting of Product Tags within the exported file. By default this is set to export Product Tags by Term ID in Desending order.', 'woocommerce-exporter' ); ?></p>
</div>
<?php
		ob_end_flush();

	}

	/* End of: WordPress Administration */

}

// Returns a list of Product Tag export columns
function woo_ce_get_tag_fields( $format = 'full' ) {

	$export_type = 'tag';

	$fields = array();
	$fields[] = array(
		'name' => 'term_id',
		'label' => __( 'Term ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'name',
		'label' => __( 'Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'slug',
		'label' => __( 'Slug', 'woocommerce-exporter' )
	);

/*
	$fields[] = array(
		'name' => '',
		'label' => __( '', 'woocommerce-exporter' )
	);
*/

	// Drop in our content filters here
	add_filter( 'sanitize_key', 'woo_ce_sanitize_key' );

	// Allow Plugin/Theme authors to add support for additional columns
	$fields = apply_filters( sprintf( WOO_CD_PREFIX . '_%s_fields', $export_type ), $fields, $export_type );

	// Remove our content filters here to play nice with other Plugins
	remove_filter( 'sanitize_key', 'woo_ce_sanitize_key' );

	$remember = woo_ce_get_option( $export_type . '_fields', array() );
	if( !empty( $remember ) ) {
		$remember = maybe_unserialize( $remember );
		$size = count( $fields );
		for( $i = 0; $i < $size; $i++ ) {
			$fields[$i]['disabled'] = ( isset( $fields[$i]['disabled'] ) ? $fields[$i]['disabled'] : 0 );
			$fields[$i]['default'] = 1;
			// If not found turn off default
			if( !array_key_exists( $fields[$i]['name'], $remember ) )
				$fields[$i]['default'] = 0;
		}
	}

	switch( $format ) {

		case 'summary':
			$output = array();
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
				if( isset( $fields[$i] ) )
					$output[$fields[$i]['name']] = 'on';
			}
			return $output;
			break;

		case 'full':
		default:
			$sorting = woo_ce_get_option( $export_type . '_sorting', array() );
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
				$fields[$i]['reset'] = $i;
				$fields[$i]['order'] = ( isset( $sorting[$fields[$i]['name']] ) ? $sorting[$fields[$i]['name']] : $i );
			}
			// Check if we are using PHP 5.3 and above
			if( version_compare( phpversion(), '5.3' ) >= 0 )
				usort( $fields, woo_ce_sort_fields( 'order' ) );
			return $fields;
			break;

	}

}

function woo_ce_override_tag_field_labels( $fields = array() ) {

	$labels = woo_ce_get_option( 'tag_labels', array() );
	if( !empty( $labels ) ) {
		foreach( $fields as $key => $field ) {
			if( isset( $labels[$field['name']] ) )
				$fields[$key]['label'] = $labels[$field['name']];
		}
	}
	return $fields;

}
add_filter( 'woo_ce_tag_fields', 'woo_ce_override_tag_field_labels', 11 );

function woo_ce_extend_tag_fields( $fields ) {

	// WordPress SEO - http://wordpress.org/plugins/wordpress-seo/
	if( function_exists( 'wpseo_admin_init' ) ) {
		$fields[] = array(
			'name' => 'wpseo_title',
			'label' => __( 'WordPress SEO - SEO Title', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wpseo_description',
			'label' => __( 'WordPress SEO - SEO Description', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wpseo_focuskw',
			'label' => __( 'WordPress SEO - Focus Keyword', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wpseo_canonical',
			'label' => __( 'WordPress SEO - Canonical', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wpseo_noindex',
			'label' => __( 'WordPress SEO - Noindex', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wpseo_sitemap_include',
			'label' => __( 'WordPress SEO - Sitemap include', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wpseo_opengraph_title',
			'label' => __( 'WordPress SEO - Facebook Title', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wpseo_opengraph_description',
			'label' => __( 'WordPress SEO - Facebook Description', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wpseo_opengraph_image',
			'label' => __( 'WordPress SEO - Facebook Image', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wpseo_twitter_title',
			'label' => __( 'WordPress SEO - Twitter Title', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wpseo_twitter_description',
			'label' => __( 'WordPress SEO - Twitter Description', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wpseo_twitter_image',
			'label' => __( 'WordPress SEO - Twitter Image', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
	}

	return $fields;

}
add_filter( 'woo_ce_tag_fields', 'woo_ce_extend_tag_fields' );

// Returns the export column header label based on an export column slug
function woo_ce_get_tag_field( $name = null, $format = 'name' ) {

	$output = '';
	if( $name ) {
		$fields = woo_ce_get_tag_fields();
		$size = count( $fields );
		for( $i = 0; $i < $size; $i++ ) {
			if( $fields[$i]['name'] == $name ) {
				switch( $format ) {

					case 'name':
						$output = $fields[$i]['label'];
						break;

					case 'full':
						$output = $fields[$i];
						break;

				}
				$i = $size;
			}
		}
	}
	return $output;

}

// Returns a list of WooCommerce Product Tags to export process
function woo_ce_get_product_tags( $args = array() ) {

	$term_taxonomy = 'product_tag';
	$defaults = array(
		'orderby' => 'name',
		'order' => 'ASC',
		'hide_empty' => 0
	);
	$args = wp_parse_args( $args, $defaults );

	// Allow other developers to bake in their own filters
	$args = apply_filters( 'woo_ce_get_product_tags_args', $args );

	$tags = get_terms( $term_taxonomy, $args );
	if( !empty( $tags ) && is_wp_error( $tags ) == false ) {
		$size = count( $tags );
		for( $i = 0; $i < $size; $i++ ) {
			$tags[$i]->description = woo_ce_format_description_excerpt( $tags[$i]->description );
			$tags[$i]->disabled = 0;
			if( $tags[$i]->count == 0 )
				$tags[$i]->disabled = 1;
		}

		// Allow Plugin/Theme authors to add support for additional Tag columns
		$tags = apply_filters( 'woo_ce_tag_item', $tags );


		return $tags;
	}

}

function woo_ce_extend_tag_item( $tags ) {

	if( !empty( $tags ) ) {

		// WordPress SEO - http://wordpress.org/plugins/wordpress-seo/
		if( function_exists( 'wpseo_admin_init' ) ) {
			$meta = get_option( 'wpseo_taxonomy_meta' );
			// Check if the WordPress Option is empty
			if( $meta !== false ) {
				// Check if the WordPress Option is an array
				if( is_array( $meta ) ) {
					// Check if the product_cat Taxonomy exists within the WordPress Option
					$term_taxonomy = 'product_tag';
					if( array_key_exists( $term_taxonomy, $meta ) ) {
						$meta = $meta[$term_taxonomy];
						foreach( $tags as $key => $tag ) {
							// Check if the Term ID exists within the array
							$term_id = ( isset( $tag->term_id ) ? $tag->term_id : 0 );
							if( array_key_exists( $term_id, $meta ) ) {
								$tags[$key]->wpseo_title = ( isset( $meta[$term_id]['wpseo_title'] ) ? $meta[$term_id]['wpseo_title'] : '' );
								$tags[$key]->wpseo_description = ( isset( $meta[$term_id]['wpseo_desc'] ) ? $meta[$term_id]['wpseo_desc'] : '' );
								$tags[$key]->wpseo_canonical = ( isset( $meta[$term_id]['wpseo_canonical'] ) ? $meta[$term_id]['wpseo_canonical'] : '' );
								$tags[$key]->wpseo_noindex = ( isset( $meta[$term_id]['wpseo_noindex'] ) ? woo_ce_format_wpseo_noindex( $meta[$term_id]['wpseo_noindex'] ) : '' );
								$tags[$key]->wpseo_sitemap_include = ( isset( $meta[$term_id]['wpseo_sitemap_include'] ) ? woo_ce_format_wpseo_sitemap_include( $meta[$term_id]['wpseo_sitemap_include'] ) : '' );
								$tags[$key]->wpseo_focuskw = ( isset( $meta[$term_id]['wpseo_focuskw'] ) ? $meta[$term_id]['wpseo_focuskw'] : '' );
								$tags[$key]->wpseo_opengraph_title = ( isset( $meta[$term_id]['wpseo_opengraph-title'] ) ? $meta[$term_id]['wpseo_opengraph-title'] : '' );
								$tags[$key]->wpseo_opengraph_description = ( isset( $meta[$term_id]['wpseo_opengraph-description'] ) ? $meta[$term_id]['wpseo_opengraph-description'] : '' );
								$tags[$key]->wpseo_opengraph_image = ( isset( $meta[$term_id]['wpseo_opengraph-image'] ) ? $meta[$term_id]['wpseo_opengraph-image'] : '' );
								$tags[$key]->wpseo_twitter_title = ( isset( $meta[$term_id]['wpseo_twitter-title'] ) ? $meta[$term_id]['wpseo_twitter-title'] : '' );
								$tags[$key]->wpseo_twitter_description = ( isset( $meta[$term_id]['wpseo_twitter-description'] ) ? $meta[$term_id]['wpseo_twitter-description'] : '' );
								$tags[$key]->wpseo_twitter_image = ( isset( $meta[$term_id]['wpseo_twitter-image'] ) ? $meta[$term_id]['wpseo_twitter-image'] : '' );
							}
							unset( $term_id );
						}
					}
				}
			}
		}

	}
	return $tags;

}
add_filter( 'woo_ce_tag_item', 'woo_ce_extend_tag_item' );
?>