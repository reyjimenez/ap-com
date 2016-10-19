<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	function woo_ce_get_export_type_product_count() {

		$count = 0;
		// Check if the existing Transient exists
		$cached = get_transient( WOO_CD_PREFIX . '_product_count' );
		if( $cached == false ) {
			$post_type = apply_filters( 'woo_ce_get_export_type_product_count_post_types', array( 'product', 'product_variation' ) );
			$args = array(
				'post_type' => $post_type,
				'posts_per_page' => 1,
				'fields' => 'ids',
				'suppress_filters' => 1
			);
			$count_query = new WP_Query( $args );
			$count = $count_query->found_posts;
			set_transient( WOO_CD_PREFIX . '_product_count', $count, HOUR_IN_SECONDS );
		} else {
			$count = $cached;
		}
		return $count;

	}

	// HTML template for Filter Products by Product Category widget on Store Exporter screen
	function woo_ce_products_filter_by_product_category() {

		$args = array(
			'hide_empty' => 1
		);
		$product_categories = woo_ce_get_product_categories( $args );

		ob_start(); ?>
<p><label><input type="checkbox" id="products-filters-categories" /> <?php _e( 'Filter Products by Product Category', 'woocommerce-exporter' ); ?></label></p>
<div id="export-products-filters-categories" class="separator">
	<ul>
		<li>
<?php if( !empty( $product_categories ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Product Category...', 'woocommerce-exporter' ); ?>" name="product_filter_category[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_categories as $product_category ) { ?>
				<option value="<?php echo $product_category->term_id; ?>"<?php disabled( $product_category->count, 0 ); ?>><?php echo woo_ce_format_product_category_label( $product_category->name, $product_category->parent_name ); ?> (<?php printf( __( 'Term ID: %d', 'woocommerce-exporter' ), $product_category->term_id ); ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Product Categories were found.', 'woocommerce-exporter' ); ?></li>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Product Categories you want to filter exported Products by. Product Categories not assigned to Products are hidden from view. Default is to include all Product Categories.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-products-filters-categories -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Products by Product Tag widget on Store Exporter screen
	function woo_ce_products_filter_by_product_tag() {

		$args = array(
			'hide_empty' => 1
		);
		$product_tags = woo_ce_get_product_tags( $args );

		ob_start(); ?>
<p><label><input type="checkbox" id="products-filters-tags" /> <?php _e( 'Filter Products by Product Tag', 'woocommerce-exporter' ); ?></label></p>
<div id="export-products-filters-tags" class="separator">
	<ul>
		<li>
<?php if( !empty( $product_tags ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Product Tag...', 'woocommerce-exporter' ); ?>" name="product_filter_tag[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_tags as $product_tag ) { ?>
				<option value="<?php echo $product_tag->term_id; ?>"<?php disabled( $product_tag->count, 0 ); ?>><?php echo $product_tag->name; ?> (<?php printf( __( 'Term ID: %d', 'woocommerce-exporter' ), $product_tag->term_id ); ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Product Tags were found.', 'woocommerce-exporter' ); ?></li>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Product Tags you want to filter exported Products by. Product Tags not assigned to Products are hidden from view. Default is to include all Product Tags.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-products-filters-tags -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Products by Product Brand widget on Store Exporter screen
	function woo_ce_products_filter_by_product_brand() {

		// Check if Brands is available
		if( woo_ce_detect_product_brands() == false )
			return;

		$args = array(
			'hide_empty' => 1,
			'orderby' => 'term_group'
		);
		$product_brands = woo_ce_get_product_brands( $args );

		ob_start(); ?>
<p><label><input type="checkbox" id="products-filters-brands" /> <?php _e( 'Filter Products by Product Brand', 'woocommerce-exporter' ); ?></label></p>
<div id="export-products-filters-brands" class="separator">
	<ul>
		<li>
<?php if( !empty( $product_brands ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Product Brand...', 'woocommerce-exporter' ); ?>" name="product_filter_brand[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_brands as $product_brand ) { ?>
				<option value="<?php echo $product_brand->term_id; ?>"<?php disabled( $product_brand->count, 0 ); ?>><?php echo woo_ce_format_product_category_label( $product_brand->name, $product_brand->parent_name ); ?> (<?php printf( __( 'Term ID: %d', 'woocommerce-exporter' ), $product_brand->term_id ); ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Product Brands were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Product Brands you want to filter exported Products by. Product Brands not assigned to Products are hidden from view. Default is to include all Product Brands.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-products-filters-brands -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Products by Product Vendor widget on Store Exporter screen
	function woo_ce_products_filter_by_product_vendor() {

		if( class_exists( 'WooCommerce_Product_Vendors' ) == false )
			return;

		$args = array(
			'hide_empty' => 1
		);
		$product_vendors = woo_ce_get_product_vendors( $args, 'full' );

		ob_start(); ?>
<p><label><input type="checkbox" id="products-filters-vendors" /> <?php _e( 'Filter Products by Product Vendor', 'woocommerce-exporter' ); ?></label></p>
<div id="export-products-filters-vendors" class="separator">
	<ul>
		<li>
<?php if( !empty( $product_vendors ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Product Vendor...', 'woocommerce-exporter' ); ?>" name="product_filter_vendor[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_vendors as $product_vendor ) { ?>
				<option value="<?php echo $product_vendor->term_id; ?>"<?php disabled( $product_vendor->count, 0 ); ?>><?php echo $product_vendor->name; ?> (<?php printf( __( 'Term ID: %d', 'woocommerce-exporter' ), $product_vendor->term_id ); ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Product Vendors were found.', 'woocommerce-exporter' ); ?></li>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Product Vendors you want to filter exported Products by. Product Vendors not assigned to Products are hidden from view. Default is to include all Product Vendors.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-products-filters-vendors -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Products by Product Status widget on Store Exporter screen
	function woo_ce_products_filter_by_product_status() {

		$product_statuses = get_post_statuses();
		if( !isset( $product_statuses['trash'] ) )
			$product_statuses['trash'] = __( 'Trash', 'woocommerce-exporter' );
		$types = woo_ce_get_option( 'product_status', array() );

		ob_start(); ?>
<p><label><input type="checkbox" id="products-filters-status"<?php checked( !empty( $types ), true ); ?> /> <?php _e( 'Filter Products by Product Status', 'woocommerce-exporter' ); ?></label></p>
<div id="export-products-filters-status" class="separator">
	<ul>
		<li>
<?php if( !empty( $product_statuses ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Product Status...', 'woocommerce-exporter' ); ?>" name="product_filter_status[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_statuses as $key => $product_status ) { ?>
				<option value="<?php echo $key; ?>"<?php echo ( is_array( $types ) ? selected( in_array( $key, $types, false ), true ) : '' ); ?>><?php echo $product_status; ?></option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Product Status were found.', 'woocommerce-exporter' ); ?></li>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Product Status options you want to filter exported Products by. Default is to include all Product Status options.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-products-filters-status -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Products by Product Type widget on Store Exporter screen
	function woo_ce_products_filter_by_product_type() {

		$product_types = woo_ce_get_product_types();
		$types = woo_ce_get_option( 'product_type', array() );

		ob_start(); ?>
<p><label><input type="checkbox" id="products-filters-type"<?php checked( !empty( $types ), true ); ?> /> <?php _e( 'Filter Products by Product Type', 'woocommerce-exporter' ); ?></label></p>
<div id="export-products-filters-type" class="separator">
	<ul>
		<li>
<?php if( !empty( $product_types ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Product Type...', 'woocommerce-exporter' ); ?>" name="product_filter_type[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_types as $key => $product_type ) { ?>
				<option value="<?php echo $key; ?>"<?php echo ( is_array( $types ) ? selected( in_array( $key, $types, false ), true ) : '' ); ?><?php disabled( $product_type['count'], 0 ); ?>><?php echo woo_ce_format_product_type( $product_type['name'] ); ?> (<?php echo $product_type['count']; ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Product Types were found.', 'woocommerce-exporter' ); ?></li>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Product Type\'s you want to filter exported Products by. Default is to include all Product Types except Variations.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-products-filters-type -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Products by Product widget on Store Exporter screen
	function woo_ce_products_filter_by_sku() {

		$args = array();
		$products = woo_ce_get_products( $args );
		add_filter( 'the_title', 'woo_ce_get_product_title_sku', 10, 2 );

		ob_start(); ?>
<p><label><input type="checkbox" id="products-filters-sku" /> <?php _e( 'Filter Products by Product', 'woocommerce-exporter' ); ?></label></p>
<div id="export-products-filters-sku" class="separator">
	<ul>
		<li>
<?php if( wp_script_is( 'wc-enhanced-select', 'enqueued' ) ) { ?>
			<p><input type="hidden" id="product_filter_sku" name="product_filter_sku[]" class="multiselect wc-product-search" data-multiple="true" style="width:95;" data-placeholder="<?php _e( 'Search for a Product&hellip;', 'woocommerce-exporter' ); ?>" data-action="woocommerce_json_search_products_and_variations" /></p>
<?php } else { ?>
	<?php if( !empty( $products ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Product...', 'woocommerce-exporter' ); ?>" name="product_filter_sku[]" multiple class="chzn-select" style="width:95%;">
		<?php foreach( $products as $product ) { ?>
				<option value="<?php echo $product; ?>"><?php echo woo_ce_format_post_title( get_the_title( $product ) ); ?></option>
		<?php } ?>
			</select>
	<?php } else { ?>
			<?php _e( 'No Products were found.', 'woocommerce-exporter' ); ?></li>
	<?php } ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Product\'s you want to filter exported Products by. Default is to include all Products.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-products-filters-sku -->
<?php
		ob_end_flush();
		remove_filter( 'the_title', 'woo_ce_get_product_title_sku' );

	}

	// HTML template for Filter Products by Stock Status widget on Store Exporter screen
	function woo_ce_products_filter_by_stock_status() {

		$types = woo_ce_get_option( 'product_stock', false );

		ob_start(); ?>
<p><label><input type="checkbox" id="products-filters-stock"<?php checked( !empty( $types ), true ); ?> /> <?php _e( 'Filter Products by Stock Status', 'woocommerce-exporter' ); ?></label></p>
<div id="export-products-filters-stock" class="separator">
	<ul>
		<li value=""><label><input type="radio" name="product_filter_stock" value=""<?php checked( $types, false ); ?> /><?php _e( 'Include both', 'woocommerce-exporter' ); ?></label></li>
		<li value="instock"><label><input type="radio" name="product_filter_stock" value="instock"<?php checked( $types, 'instock' ); ?> /><?php _e( 'In stock', 'woocommerce-exporter' ); ?></label></li>
		<li value="outofstock"><label><input type="radio" name="product_filter_stock" value="outofstock"<?php checked( $types, 'outofstock' ); ?> /><?php _e( 'Out of stock', 'woocommerce-exporter' ); ?></label></li>
	</ul>
	<p class="description"><?php _e( 'Select the Stock Status\'s you want to filter exported Products by. Default is to include all Stock Status\'s.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-products-filters-stock -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Products by Featured widget on Store Exporter screen
	function woo_ce_products_filter_by_featured() {

		$types = woo_ce_get_option( 'product_featured', false );

		ob_start(); ?>
<p><label><input type="checkbox" id="products-filters-featured"<?php checked( !empty( $types ), true ); ?> /> <?php _e( 'Filter Products by Featured', 'woocommerce-exporter' ); ?></label></p>
<div id="export-products-filters-featured" class="separator">
	<ul>
		<li value=""><label><input type="radio" name="product_filter_featured" value=""<?php checked( $types, false ); ?> /><?php _e( 'Include both', 'woocommerce-exporter' ); ?></label></li>
		<li value="yes"><label><input type="radio" name="product_filter_featured" value="yes"<?php checked( $types, 'yes' ); ?> /><?php _e( 'Featured', 'woocommerce-exporter' ); ?></label></li>
		<li value="no"><label><input type="radio" name="product_filter_featured" value="no"<?php checked( $types, 'no' ); ?> /><?php _e( 'Un-featured', 'woocommerce-exporter' ); ?></label></li>
	</ul>
	<p class="description"><?php _e( 'Select the Featured state you want to filter exported Products by. Default is to include all Products.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-products-filters-featured -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Products by Shipping Classes widget on Store Exporter screen
	function woo_ce_products_filter_by_shipping_class() {

		$shipping_classes = woo_ce_get_shipping_classes();

		ob_start(); ?>
<p><label><input type="checkbox" id="products-filters-shipping_class" /> <?php _e( 'Filter Products by Shipping Class', 'woocommerce-exporter' ); ?></label></p>
<div id="export-products-filters-shipping_class" class="separator">
	<ul>
		<li>
<?php if( !empty( $shipping_classes ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Shipping Class...', 'woocommerce-exporter' ); ?>" name="product_filter_shipping_class[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $shipping_classes as $shipping_class ) { ?>
				<option value="<?php echo $shipping_class->term_id; ?>"<?php disabled( $shipping_class->count, 0 ); ?>><?php echo $shipping_class->name; ?> (<?php echo $shipping_class->count; ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Shipping Classes were found.', 'woocommerce-exporter' ); ?></li>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Shipping Class you want to filter exported Products by. Default is to include all Products.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-products-filters-shipping_class -->
<?php
		ob_end_flush();

	}

	// HTML template for Filter Products by Language widget on Store Exporter screen
	function woo_ce_products_filter_by_language() {

		if( !woo_ce_detect_wpml() )
			return;

		$languages = ( function_exists( 'icl_get_languages' ) ? icl_get_languages( 'skip_missing=N' ) : array() );

		ob_start(); ?>
<p><label><input type="checkbox" id="products-filters-language" /> <?php _e( 'Filter Products by Language', 'woocommerce-exporter' ); ?></label></p>
<div id="export-products-filters-language" class="separator">
	<ul>
		<li>
<?php if( !empty( $languages ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Language...', 'woocommerce-exporter' ); ?>" name="product_filter_language[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $languages as $key => $language ) { ?>
				<option value="<?php echo $key; ?>"><?php echo $language['native_name']; ?> (<?php echo $language['translated_name']; ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Languages were found.', 'woocommerce-exporter' ); ?></li>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Language\'s you want to filter exported Products by. Default is to include all Language\'s.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-products-filters-language -->

<?php
		ob_end_flush();

	}

	// HTML template for Filter Products by Date Modified widget on Store Exporter screen
	function woo_ce_products_filter_by_date_modified() {

		$date_format = woo_ce_get_option( 'date_format', 'd/m/Y' );
		$product_dates_from = woo_ce_get_product_first_date( $date_format );
		$product_dates_to = date( $date_format );

		ob_start(); ?>
<p><label><input type="checkbox" id="products-filters-date_modified" /> <?php _e( 'Filter Products by Date Modified', 'woocommerce-exporter' ); ?></label></p>
<div id="export-products-filters-date_modified" class="separator">
	<ul>
		<li>
			<label><input type="radio" name="product_dates_filter" value="manual" /> <?php _e( 'Fixed date', 'woocommerce-exporter' ); ?></label>
			<div style="margin-top:0.2em;">
				<input type="text" size="10" maxlength="10" id="product_dates_from" name="product_dates_from" value="<?php echo esc_attr( $product_dates_from ); ?>" class="text code datepicker product_export" /> to <input type="text" size="10" maxlength="10" id="product_dates_to" name="product_dates_to" value="<?php echo esc_attr( $product_dates_to ); ?>" class="text code datepicker product_export" />
				<p class="description"><?php _e( 'Filter the dates of Products to be included in the export. Default is the date of the first Product Modified to today in the date format <code>DD/MM/YYYY</code>.', 'woocommerce-exporter' ); ?></p>
			</div>
		</li>
	</ul>
</div>
<!-- #export-products-filters-date_modified -->
<?php
		ob_end_flush();

	}

	// HTML template for jump link to Custom Product Fields within Order Options on Store Exporter screen
	function woo_ce_products_custom_fields_link() {

		ob_start(); ?>
<div id="export-products-custom-fields-link">
	<p><a href="#export-products-custom-fields"><?php _e( 'Manage Custom Product Fields', 'woocommerce-exporter' ); ?></a></p>
</div>
<!-- #export-products-custom-fields-link -->
<?php
		ob_end_flush();

	}

	// HTML template for Product Sorting widget on Store Exporter screen
	function woo_ce_product_sorting() {

		$product_orderby = woo_ce_get_option( 'product_orderby', 'ID' );
		$product_order = woo_ce_get_option( 'product_order', 'DESC' );

		ob_start(); ?>
<p><label><?php _e( 'Product Sorting', 'woocommerce-exporter' ); ?></label></p>
<div>
	<select name="product_orderby">
		<option value="ID"<?php selected( 'ID', $product_orderby ); ?>><?php _e( 'Product ID', 'woocommerce-exporter' ); ?></option>
		<option value="title"<?php selected( 'title', $product_orderby ); ?>><?php _e( 'Product Name', 'woocommerce-exporter' ); ?></option>
		<option value="sku"<?php selected( 'sku', $product_orderby ); ?>><?php _e( 'Product SKU', 'woocommerce-exporter' ); ?></option>
		<option value="date"<?php selected( 'date', $product_orderby ); ?>><?php _e( 'Date Created', 'woocommerce-exporter' ); ?></option>
		<option value="modified"<?php selected( 'modified', $product_orderby ); ?>><?php _e( 'Date Modified', 'woocommerce-exporter' ); ?></option>
		<option value="rand"<?php selected( 'rand', $product_orderby ); ?>><?php _e( 'Random', 'woocommerce-exporter' ); ?></option>
		<option value="menu_order"<?php selected( 'menu_order', $product_orderby ); ?>><?php _e( 'Sort Order', 'woocommerce-exporter' ); ?></option>
	</select>
	<select name="product_order">
		<option value="ASC"<?php selected( 'ASC', $product_order ); ?>><?php _e( 'Ascending', 'woocommerce-exporter' ); ?></option>
		<option value="DESC"<?php selected( 'DESC', $product_order ); ?>><?php _e( 'Descending', 'woocommerce-exporter' ); ?></option>
	</select>
	<p class="description"><?php _e( 'Select the sorting of Products within the exported file. By default this is set to export Products by Product ID in Desending order.', 'woocommerce-exporter' ); ?></p>
</div>
<?php
		ob_end_flush();

	}

	// HTML template for Up-sells formatting on Store Exporter screen
	function woo_ce_products_upsells_formatting() {

		$upsell_formatting = woo_ce_get_option( 'upsell_formatting', 1 );

		ob_start(); ?>
<tr class="export-options product-options">
	<th><label for=""><?php _e( 'Up-sells formatting', 'woocommerce-exporter' ); ?></label></th>
	<td>
		<label><input type="radio" name="product_upsell_formatting" value="0"<?php checked( $upsell_formatting, 0 ); ?> />&nbsp;<?php _e( 'Export Up-Sells as Product ID', 'woocommerce-exporter' ); ?></label><br />
		<label><input type="radio" name="product_upsell_formatting" value="1"<?php checked( $upsell_formatting, 1 ); ?> />&nbsp;<?php _e( 'Export Up-Sells as Product SKU', 'woocommerce-exporter' ); ?></label>
		<p class="description"><?php _e( 'Choose the up-sell formatting that is accepted by your WooCommerce import Plugin (e.g. Product Importer Deluxe, Product Import Suite, etc.).', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>

<?php
		ob_end_flush();

	}

	// HTML template for Cross-sells formatting on Store Exporter screen
	function woo_ce_products_crosssells_formatting() {

		$crosssell_formatting = woo_ce_get_option( 'crosssell_formatting', 1 );

		ob_start(); ?>
<tr class="export-options product-options">
	<th><label for=""><?php _e( 'Cross-sells formatting', 'woocommerce-exporter' ); ?></label></th>
	<td>
		<label><input type="radio" name="product_crosssell_formatting" value="0"<?php checked( $crosssell_formatting, 0 ); ?> />&nbsp;<?php _e( 'Export Cross-Sells as Product ID', 'woocommerce-exporter' ); ?></label><br />
		<label><input type="radio" name="product_crosssell_formatting" value="1"<?php checked( $crosssell_formatting, 1 ); ?> />&nbsp;<?php _e( 'Export Cross-Sells as Product SKU', 'woocommerce-exporter' ); ?></label>
		<p class="description"><?php _e( 'Choose the cross-sell formatting that is accepted by your WooCommerce import Plugin (e.g. Product Importer Deluxe, Product Import Suite, etc.).', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>

<?php
		ob_end_flush();

	}

	// HTML template for Variation formatting on Store Exporter screen
	function woo_ce_products_variation_formatting() {

		$variation_formatting = woo_ce_get_option( 'variation_formatting', 0 );

		ob_start(); ?>
					<tr class="export-options product-options">
						<th><label for=""><?php _e( 'Variation formatting', 'woocommerce-exporter' ); ?></label></th>
						<td>
							<label><input type="radio" name="variation_formatting" value="0"<?php checked( $variation_formatting, 0 ); ?> />&nbsp;<?php _e( 'Leave empty Variant details intact', 'woocommerce-exporter' ); ?></label><br />
							<label><input type="radio" name="variation_formatting" value="1"<?php checked( $variation_formatting, 1 ); ?> />&nbsp;<?php _e( 'Default Variant details to Parent Product', 'woocommerce-exporter' ); ?></label>
							<p class="description"><?php _e( 'Choose the default formatting rule that is applied to Product Variations.', 'woocommerce-exporter' ); ?></p>
						</td>
					</tr>

<?php
		ob_end_flush();

	}

	function woo_ce_products_description_excerpt_formatting() {

		$description_excerpt_formatting = woo_ce_get_option( 'description_excerpt_formatting', 0 );

		ob_start(); ?>
					<tr class="export-options product-options category-options tag-options order-options">
						<th><label for=""><?php _e( 'Description/Excerpt formatting', 'woocommerce-exporter' ); ?></label></th>
						<td>
							<label><input type="radio" name="description_excerpt_formatting" value="0"<?php checked( $description_excerpt_formatting, 0 ); ?> />&nbsp;<?php _e( 'Leave HTML tags from Description/Excerpt intact', 'woocommerce-exporter' ); ?></label><br />
							<label><input type="radio" name="description_excerpt_formatting" value="1"<?php checked( $description_excerpt_formatting, 1 ); ?> />&nbsp;<?php _e( 'Strip HTML tags from Description/Excerpt', 'woocommerce-exporter' ); ?></label>
							<p class="description"><?php _e( 'Choose the HTML tag formatting rule that is applied to the Description/Excerpt within the Product, Category, Tag, Brand and Order export.', 'woocommerce-exporter' ); ?></p>
						</td>
					</tr>
<?php
		ob_end_flush();

	}

	// HTML template for Custom Products widget on Store Exporter screen
	function woo_ce_products_custom_fields() {

		if( $custom_products = woo_ce_get_option( 'custom_products', '' ) )
			$custom_products = implode( "\n", $custom_products );
		if( $custom_attributes = woo_ce_get_option( 'custom_attributes', '' ) )
			$custom_attributes = implode( "\n", $custom_attributes );

		$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/usage/';

		ob_start(); ?>
<form method="post" id="export-products-custom-fields" class="export-options product-options">
	<div id="poststuff">

		<div class="postbox" id="export-options product-options">
			<h3 class="hndle"><?php _e( 'Custom Product Fields', 'woocommerce-exporter' ); ?></h3>
			<div class="inside">
				<p class="description"><?php _e( 'To include additional custom Product meta or custom Attributes in the Export Products table above fill the meta text box then click Save Custom Fields.', 'woocommerce-exporter' ); ?></p>
				<table class="form-table">

					<tr>
						<th>
							<label><?php _e( 'Product meta', 'woocommerce-exporter' ); ?></label>
						</th>
						<td>
							<textarea name="custom_products" rows="5" cols="70"><?php echo esc_textarea( $custom_products ); ?></textarea>
							<p class="description"><?php _e( 'Include additional custom Product meta in your export file by adding each custom Product meta name to a new line above.<br />For example: <code>Customer UA</code> (new line) <code>Customer IP Address</code>', 'woocommerce-exporter' ); ?></p>
						</td>
					</tr>

					<tr>
						<th>
							<label><?php _e( 'Custom attribute', 'woocommerce-exporter' ); ?></label>
						</th>
						<td>
							<textarea name="custom_attributes" rows="5" cols="70"><?php echo esc_textarea( $custom_attributes ); ?></textarea>
							<p class="description"><?php _e( 'Include custom Attributes in your export file by adding each custom Attribute name - typically in lowercase and replacing spaces with dashes, e.g. Size becomes size or Sample Attribute becomes sample-attribute - to a new line above.<br />For example: <code>condition</code> (new line) <code>colour</code>', 'woocommerce-exporter' ); ?></p>
						</td>
					</tr>

					<?php do_action( 'woo_ce_products_custom_fields' ); ?>

				</table>
				<p class="submit">
					<input type="submit" value="<?php _e( 'Save Custom Fields', 'woocommerce-exporter' ); ?>" class="button" />
				</p>
				<p class="description"><?php printf( __( 'For more information on exporting custom Product meta and Attributes consult our <a href="%s" target="_blank">online documentation</a>.', 'woocommerce-exporter' ), $troubleshooting_url ); ?></p>
			</div>
			<!-- .inside -->
		</div>
		<!-- .postbox -->

	</div>
	<!-- #poststuff -->
	<input type="hidden" name="action" value="update" />
</form>
<!-- #export-products-custom-fields -->
<?php
		ob_end_flush();

	}

	function woo_ce_products_custom_fields_product_addons() {

		if( ( class_exists( 'Product_Addon_Admin' ) || class_exists( 'Product_Addon_Display' ) ) == false )
			return;

		if( $custom_product_addons = woo_ce_get_option( 'custom_product_addons', '' ) )
			$custom_product_addons = implode( "\n", $custom_product_addons );

		ob_start(); ?>
					<tr>
						<th>
							<label><?php _e( 'Custom Product Add-ons', 'woocommerce-exporter' ); ?></label>
						</th>
						<td>
							<textarea name="custom_product_addons" rows="5" cols="70"><?php echo esc_textarea( $custom_product_addons ); ?></textarea>
							<p class="description"><?php _e( 'Include custom Product Add-ons (not Global Add-ons) linked to individual Products within in your export file by adding the Group Name of each Product Addon to a new line above.<br />For example: <code>Customer UA</code> (new line) <code>Customer IP Address</code>', 'woocommerce-exporter' ); ?></p>
						</td>
					</tr>
<?php
		ob_end_flush();

	}

	function woo_ce_products_custom_fields_tab_manager() {

		if( class_exists( 'WC_Tab_Manager' ) == false )
			return;

		if( $custom_product_tabs = woo_ce_get_option( 'custom_product_tabs', '' ) )
			$custom_product_tabs = implode( "\n", $custom_product_tabs );

		ob_start(); ?>
					<tr>
						<th>
							<label><?php _e( 'Custom Product Tabs', 'woocommerce-exporter' ); ?></label>
						</th>
						<td>
							<textarea name="custom_product_tabs" rows="5" cols="70"><?php echo esc_textarea( $custom_product_tabs ); ?></textarea>
							<p class="description"><?php _e( 'Include custom Product Tabs linked to individual Products within in your export file by adding the Name of each Product Tab to a new line above.<br />For example: <code>Ingredients</code> (new line) <code>Specification</code>', 'woocommerce-exporter' ); ?></p>
						</td>
					</tr>
<?php
		ob_end_flush();

	}

	function woo_ce_export_options_gallery_format() {

		$gallery_formatting = woo_ce_get_option( 'gallery_formatting', 1 );
		$gallery_unique = woo_ce_get_option( 'gallery_unique', 0 );
		$max_size = woo_ce_get_option( 'max_product_gallery', 3 );

		ob_start(); ?>
<tr class="export-options product-options">
	<th><label for=""><?php _e( 'Product gallery formatting', 'woocommerce-exporter' ); ?></label></th>
	<td>
		<label><input type="radio" name="product_gallery_formatting" value="0"<?php checked( $gallery_formatting, 0 ); ?> />&nbsp;<?php _e( 'Export Product Gallery as Attachment ID', 'woocommerce-exporter' ); ?></label><br />
		<label><input type="radio" name="product_gallery_formatting" value="1"<?php checked( $gallery_formatting, 1 ); ?> />&nbsp;<?php _e( 'Export Product Gallery as Image URL', 'woocommerce-exporter' ); ?></label><br />
		<label><input type="radio" name="product_gallery_formatting" value="2"<?php checked( $gallery_formatting, 2 ); ?> />&nbsp;<?php _e( 'Export Product Gallery as Image filepath', 'woocommerce-exporter' ); ?></label>
		<hr />
		<label><input type="radio" name="product_gallery_unique" value="0"<?php checked( $gallery_unique, 0 ); ?> />&nbsp;<?php _e( 'Export Product Gallery as a single combined image cell', 'woocommerce-exporter' ); ?></label><br />
		<label><input type="radio" name="product_gallery_unique" value="1"<?php checked( $gallery_unique, 1 ); ?> />&nbsp;<?php _e( 'Export Product Gallery as individual image cells', 'woocommerce-exporter' ); ?></label>
		<p class="description"><?php _e( 'Choose the product gallery formatting that is accepted by your WooCommerce import Plugin (e.g. Product Importer Deluxe, Product Import Suite, etc.).', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<tr id="max_product_gallery_option" class="export-options product-options">
	<th><label for=""><?php _e( 'Max unique Product Gallery images', 'woocommerce-exporter' ); ?></label></th>
	<td>
		<input type="text" id="max_product_gallery" name="max_product_gallery" size="3" class="text" value="<?php echo esc_attr( $max_size ); ?>" />
		<p class="description"><?php _e( 'Manage the number of Product Gallery colums displayed when the \'Export Product Gallery as individual image cells\' Product gallery formatting option is selected.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<?php
		ob_end_flush();

	}

	// Returns date of first Product Date Modified, any status
	function woo_ce_get_product_first_date( $date_format = 'd/m/Y' ) {

		$output = date( $date_format, mktime( 0, 0, 0, date( 'n' ), 1 ) );

		$post_type = 'product';
		$args = array(
			'post_type' => $post_type,
			'orderby' => 'post_date',
			'order' => 'ASC',
			'numberposts' => 1,
			'post_status' => 'any'
		);
		$products = get_posts( $args );
		if( !empty( $products ) ) {
			$output = date( $date_format, strtotime( $products[0]->post_date ) );
			unset( $products );
		}
		return $output;

	}

	/* End of: WordPress Administration */

}

// Returns a list of Product export columns
function woo_ce_get_product_fields( $format = 'full' ) {

	$export_type = 'product';

	$fields = array();
	$fields[] = array(
		'name' => 'parent_id',
		'label' => __( 'Parent ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'parent_sku',
		'label' => __( 'Parent SKU', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'product_id',
		'label' => __( 'Product ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'sku',
		'label' => __( 'Product SKU', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'name',
		'label' => __( 'Product Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'post_title',
		'label' => __( 'Post Title', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'slug',
		'label' => __( 'Slug', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'permalink',
		'label' => __( 'Permalink', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'product_url',
		'label' => __( 'Product URL', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'description',
		'label' => __( 'Description', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'excerpt',
		'label' => __( 'Excerpt', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'post_date',
		'label' => __( 'Product Published', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'post_modified',
		'label' => __( 'Product Modified', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'type',
		'label' => __( 'Type', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'visibility',
		'label' => __( 'Visibility', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'featured',
		'label' => __( 'Featured', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'virtual',
		'label' => __( 'Virtual', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'downloadable',
		'label' => __( 'Downloadable', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'price',
		'label' => __( 'Price', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'sale_price',
		'label' => __( 'Sale Price', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'sale_price_dates_from',
		'label' => __( 'Sale Price Dates From', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'sale_price_dates_to',
		'label' => __( 'Sale Price Dates To', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'weight',
		'label' => __( 'Weight', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'weight_unit',
		'label' => __( 'Weight Unit', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'height',
		'label' => __( 'Height', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'height_unit',
		'label' => __( 'Height Unit', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'width',
		'label' => __( 'Width', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'width_unit',
		'label' => __( 'Width Unit', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'length',
		'label' => __( 'Length', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'length_unit',
		'label' => __( 'Length Unit', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'category',
		'label' => __( 'Category', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'tag',
		'label' => __( 'Tag', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'image',
		'label' => __( 'Featured Image', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'image_thumbnail',
		'label' => __( 'Featured Image Thumbnail', 'woocommerce-exporter' )
	);
	if( apply_filters( 'woo_ce_enable_product_image_embed', false ) ) {
		$fields[] = array(
			'name' => 'image_embed',
			'label' => __( 'Featured Image (Embed)', 'woocommerce-exporter' )
		);
	}
	$fields[] = array(
		'name' => 'product_gallery',
		'label' => __( 'Product Gallery', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'product_gallery_thumbnail',
		'label' => __( 'Product Gallery Thumbnail', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'tax_status',
		'label' => __( 'Tax Status', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'tax_class',
		'label' => __( 'Tax Class', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_class',
		'label' => __( 'Shipping Class', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'download_file_name',
		'label' => __( 'Download File Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'download_file_path',
		'label' => __( 'Download File URL Path', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'download_limit',
		'label' => __( 'Download Limit', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'download_expiry',
		'label' => __( 'Download Expiry', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'download_type',
		'label' => __( 'Download Type', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'manage_stock',
		'label' => __( 'Manage Stock', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'quantity',
		'label' => __( 'Quantity', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'stock_status',
		'label' => __( 'Stock Status', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'allow_backorders',
		'label' => __( 'Allow Backorders', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'sold_individually',
		'label' => __( 'Sold Individually', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'total_sales',
		'label' => __( 'Total Sales', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'upsell_ids',
		'label' => __( 'Up-Sells', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'crosssell_ids',
		'label' => __( 'Cross-Sells', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'external_url',
		'label' => __( 'External URL', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'button_text',
		'label' => __( 'Button Text', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'purchase_note',
		'label' => __( 'Purchase Note', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'product_status',
		'label' => __( 'Product Status', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'enable_reviews',
		'label' => __( 'Enable Reviews', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'review_count',
		'label' => __( 'Review Count', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'rating_count',
		'label' => __( 'Rating Count', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'average_rating',
		'label' => __( 'Average rating', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'menu_order',
		'label' => __( 'Sort Order', 'woocommerce-exporter' )
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
	$hidden = woo_ce_get_option( $export_type . '_hidden', array() );
	if( !empty( $remember ) ) {
		$remember = maybe_unserialize( $remember );
		$hidden = maybe_unserialize( $hidden );
		$size = count( $fields );
		for( $i = 0; $i < $size; $i++ ) {
			$fields[$i]['disabled'] = ( isset( $fields[$i]['disabled'] ) ? $fields[$i]['disabled'] : 0 );
			$fields[$i]['hidden'] = ( isset( $fields[$i]['hidden'] ) ? $fields[$i]['hidden'] : 0 );
			$fields[$i]['default'] = 1;
			if( isset( $fields[$i]['name'] ) ) {
				// If not found turn off default
				if( !array_key_exists( $fields[$i]['name'], $remember ) )
					$fields[$i]['default'] = 0;
				// Remove the field from exports if found
				if( array_key_exists( $fields[$i]['name'], $hidden ) )
					$fields[$i]['hidden'] = 1;
			}
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
				if( !isset( $fields[$i]['name'] ) ) {
					unset( $fields[$i] );
					continue;
				}
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

function woo_ce_override_product_field_labels( $fields = array() ) {

	$labels = woo_ce_get_option( 'product_labels', array() );
	if( !empty( $labels ) ) {
		foreach( $fields as $key => $field ) {
			if( isset( $labels[$field['name']] ) )
				$fields[$key]['label'] = $labels[$field['name']];
		}
	}
	return $fields;

}
add_filter( 'woo_ce_product_fields', 'woo_ce_override_product_field_labels', 11 );

function woo_ce_extend_product_fields( $fields ) {

	// Attributes
	$has_attributes = false;
	$attributes = ( function_exists( 'wc_get_attribute_taxonomies' ) ? wc_get_attribute_taxonomies() : array() );
	if( !empty( $attributes ) ) {
		$has_attributes = true;
		foreach( $attributes as $attribute ) {
			$label = $attribute->attribute_label ? $attribute->attribute_label : $attribute->attribute_name;
			$fields[] = array(
				'name' => sprintf( 'attribute_%s', esc_attr( $attribute->attribute_name ) ),
				'label' => sprintf( __( 'Attribute: %s', 'woocommerce-exporter' ), esc_attr( $label ) ),
				'alias' => array( sprintf( 'pa_%s', esc_attr( $attribute->attribute_name ) ) ),
				'hover' => sprintf( apply_filters( 'woo_ce_extend_product_fields_attribute', '%s: %s (#%d)' ), __( 'Attribute', 'woocommerce-exporter' ), $attribute->attribute_name, $attribute->attribute_id )
			);
		}
		unset( $attributes, $attribute, $label );
	}

	// Custom Attributes
	$custom_attributes = woo_ce_get_option( 'custom_attributes', '' );
	if( !empty( $custom_attributes ) ) {
		$has_attributes = true;
		foreach( $custom_attributes as $custom_attribute ) {
			if( !empty( $custom_attribute ) ) {
				$fields[] = array(
					'name' => sprintf( 'attribute_%s', ( function_exists( 'remove_accents' ) ? remove_accents( $custom_attribute ) : $custom_attribute ) ),
					'label' => sprintf( __( 'Attribute: %s', 'woocommerce-exporter' ), woo_ce_clean_export_label( $custom_attribute ) ),
					'hover' => sprintf( apply_filters( 'woo_ce_extend_product_fields_custom_attribute_hover', '%s: %s' ), __( 'Custom Attribute', 'woocommerce-exporter' ), $custom_attribute )
				);
			}
		}
		unset( $custom_attributes, $custom_attribute );
	}

	// Show Default Attributes field
	if( $has_attributes ) {
		$fields[] = array(
			'name' => 'default_attributes',
			'label' => __( 'Default Attributes', 'woocommerce-exporter' )
		);
	}

	// Advanced Google Product Feed - http://www.leewillis.co.uk/wordpress-plugins/
	if( function_exists( 'woocommerce_gpf_install' ) ) {
		$fields[] = array(
			'name' => 'gpf_availability',
			'label' => __( 'Advanced Google Product Feed - Availability', 'woocommerce-exporter' ),
			'hover' => __( 'Advanced Google Product Feed', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'gpf_condition',
			'label' => __( 'Advanced Google Product Feed - Condition', 'woocommerce-exporter' ),
			'hover' => __( 'Advanced Google Product Feed', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'gpf_brand',
			'label' => __( 'Advanced Google Product Feed - Brand', 'woocommerce-exporter' ),
			'hover' => __( 'Advanced Google Product Feed', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'gpf_product_type',
			'label' => __( 'Advanced Google Product Feed - Product Type', 'woocommerce-exporter' ),
			'hover' => __( 'Advanced Google Product Feed', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'gpf_google_product_category',
			'label' => __( 'Advanced Google Product Feed - Google Product Category', 'woocommerce-exporter' ),
			'hover' => __( 'Advanced Google Product Feed', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'gpf_gtin',
			'label' => __( 'Advanced Google Product Feed - Global Trade Item Number (GTIN)', 'woocommerce-exporter' ),
			'hover' => __( 'Advanced Google Product Feed', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'gpf_mpn',
			'label' => __( 'Advanced Google Product Feed - Manufacturer Part Number (MPN)', 'woocommerce-exporter' ),
			'hover' => __( 'Advanced Google Product Feed', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'gpf_gender',
			'label' => __( 'Advanced Google Product Feed - Gender', 'woocommerce-exporter' ),
			'hover' => __( 'Advanced Google Product Feed', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'gpf_agegroup',
			'label' => __( 'Advanced Google Product Feed - Age Group', 'woocommerce-exporter' ),
			'hover' => __( 'Advanced Google Product Feed', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'gpf_colour',
			'label' => __( 'Advanced Google Product Feed - Colour', 'woocommerce-exporter' ),
			'hover' => __( 'Advanced Google Product Feed', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'gpf_size',
			'label' => __( 'Advanced Google Product Feed - Size', 'woocommerce-exporter' ),
			'hover' => __( 'Advanced Google Product Feed', 'woocommerce-exporter' )
		);
	}

	// All in One SEO Pack - http://wordpress.org/extend/plugins/all-in-one-seo-pack/
	if( function_exists( 'aioseop_activate' ) ) {
		$fields[] = array(
			'name' => 'aioseop_keywords',
			'label' => __( 'All in One SEO - Keywords', 'woocommerce-exporter' ),
			'hover' => __( 'All in One SEO Pack', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'aioseop_description',
			'label' => __( 'All in One SEO - Description', 'woocommerce-exporter' ),
			'hover' => __( 'All in One SEO Pack', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'aioseop_title',
			'label' => __( 'All in One SEO - Title', 'woocommerce-exporter' ),
			'hover' => __( 'All in One SEO Pack', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'aioseop_title_attributes',
			'label' => __( 'All in One SEO - Title Attributes', 'woocommerce-exporter' ),
			'hover' => __( 'All in One SEO Pack', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'aioseop_menu_label',
			'label' => __( 'All in One SEO - Menu Label', 'woocommerce-exporter' ),
			'hover' => __( 'All in One SEO Pack', 'woocommerce-exporter' )
		);
	}

	// WordPress SEO - http://wordpress.org/plugins/wordpress-seo/
	if( function_exists( 'wpseo_admin_init' ) ) {
		$fields[] = array(
			'name' => 'wpseo_focuskw',
			'label' => __( 'WordPress SEO - Focus Keyword', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wpseo_metadesc',
			'label' => __( 'WordPress SEO - Meta Description', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wpseo_title',
			'label' => __( 'WordPress SEO - SEO Title', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wpseo_noindex',
			'label' => __( 'WordPress SEO - Noindex', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wpseo_follow',
			'label' => __( 'WordPress SEO - Follow', 'woocommerce-exporter' ),
			'hover' => __( 'WordPress SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wpseo_googleplus_description',
			'label' => __( 'WordPress SEO - Google+ Description', 'woocommerce-exporter' ),
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

	// Ultimate SEO - http://wordpress.org/plugins/seo-ultimate/
	if( function_exists( 'su_wp_incompat_notice' ) ) {
		$fields[] = array(
			'name' => 'useo_meta_title',
			'label' => __( 'Ultimate SEO - Title Tag', 'woocommerce-exporter' ),
			'hover' => __( 'Ultimate SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'useo_meta_description',
			'label' => __( 'Ultimate SEO - Meta Description', 'woocommerce-exporter' ),
			'hover' => __( 'Ultimate SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'useo_meta_keywords',
			'label' => __( 'Ultimate SEO - Meta Keywords', 'woocommerce-exporter' ),
			'hover' => __( 'Ultimate SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'useo_social_title',
			'label' => __( 'Ultimate SEO - Social Title', 'woocommerce-exporter' ),
			'hover' => __( 'Ultimate SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'useo_social_description',
			'label' => __( 'Ultimate SEO - Social Description', 'woocommerce-exporter' ),
			'hover' => __( 'Ultimate SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'useo_meta_noindex',
			'label' => __( 'Ultimate SEO - NoIndex', 'woocommerce-exporter' ),
			'hover' => __( 'Ultimate SEO', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'useo_meta_noautolinks',
			'label' => __( 'Ultimate SEO - Disable Autolinks', 'woocommerce-exporter' ),
			'hover' => __( 'Ultimate SEO', 'woocommerce-exporter' )
		);
	}

	// WooCommerce MSRP Pricing - http://woothemes.com/woocommerce/
	if( function_exists( 'woocommerce_msrp_activate' ) ) {
		$fields[] = array(
			'name' => 'msrp',
			'label' => __( 'MSRP', 'woocommerce-exporter' ),
			'hover' => __( 'Manufacturer Suggested Retail Price (MSRP)', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Brands Addon - http://woothemes.com/woocommerce/
	// WooCommerce Brands - http://proword.net/Woocommerce_Brands/
	if( woo_ce_detect_product_brands() ) {
		$fields[] = array(
			'name' => 'brands',
			'label' => __( 'Brands', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Brands', 'woocommerce-exporter' )
		);
	}

	// Cost of Goods - http://www.skyverge.com/product/woocommerce-cost-of-goods-tracking/
	if( class_exists( 'WC_COG' ) ) {
		$fields[] = array(
			'name' => 'cost_of_goods',
			'label' => __( 'Cost of Goods', 'woocommerce-exporter' ),
			'hover' => __( 'Cost of Goods', 'woocommerce-exporter' )
		);
	}

	// Per-Product Shipping - http://www.woothemes.com/products/per-product-shipping/
	if( function_exists( 'woocommerce_per_product_shipping_init' ) ) {
		$fields[] = array(
			'name' => 'per_product_shipping',
			'label' => __( 'Per-Product Shipping', 'woocommerce-exporter' ),
			'hover' => __( 'Per-Product Shipping', 'woocommerce-exporter' )
		);
	}

	// Product Vendors - http://www.woothemes.com/products/product-vendors/
	if( class_exists( 'WooCommerce_Product_Vendors' ) ) {
		$fields[] = array(
			'name' => 'vendors',
			'label' => __( 'Product Vendors', 'woocommerce-exporter' ),
			'hover' => __( 'Product Vendors', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'vendor_ids',
			'label' => __( 'Product Vendor ID\'s', 'woocommerce-exporter' ),
			'hover' => __( 'Product Vendors', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'vendor_commission',
			'label' => __( 'Vendor Commission', 'woocommerce-exporter' ),
			'hover' => __( 'Product Vendors', 'woocommerce-exporter' )
		);
	}

	// WC Vendors - http://wcvendors.com
	if( class_exists( 'WC_Vendors' ) ) {
		$fields[] = array(
			'name' => 'vendor',
			'label' => __( 'Vendor' ),
			'hover' => __( 'WC Vendors', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'vendor_commission_rate',
			'label' => __( 'Commission (%)' ),
			'hover' => __( 'WC Vendors', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Wholesale Pricing - http://ignitewoo.com/woocommerce-extensions-plugins-themes/woocommerce-wholesale-pricing/
	if( class_exists( 'woocommerce_wholesale_pricing' ) ) {
		$fields[] = array(
			'name' => 'wholesale_price',
			'label' => __( 'Wholesale Price', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Wholesale Pricing', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'wholesale_price_text',
			'label' => __( 'Wholesale Text', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Wholesale Pricing', 'woocommerce-exporter' )
		);
	}

	// Advanced Custom Fields - http://www.advancedcustomfields.com
	if( class_exists( 'acf' ) ) {
		$custom_fields = woo_ce_get_acf_product_fields();
		if( !empty( $custom_fields ) ) {
			foreach( $custom_fields as $custom_field ) {
				$fields[] = array(
					'name' => $custom_field['name'],
					'label' => $custom_field['label'],
					'hover' => __( 'Advanced Custom Fields', 'woocommerce-exporter' )
				);
			}
			unset( $custom_fields, $custom_field );
		}
	}

	// WooCommerce Custom Fields - http://www.rightpress.net/woocommerce-custom-fields
	if( class_exists( 'RP_WCCF' ) ) {
		$options = get_option( 'rp_wccf_options' );
		if( !empty( $options ) ) {
			$custom_fields = ( isset( $options[1]['product_admin_fb_config'] ) ? $options[1]['product_admin_fb_config'] : false );
			if( !empty( $custom_fields ) ) {
				foreach( $custom_fields as $custom_field ) {
					$fields[] = array(
						'name' => sprintf( 'wccf_%s', sanitize_key( $custom_field['key'] ) ),
						'label' => ucfirst( $custom_field['label'] ),
						'hover' => __( 'WooCommerce Custom Fields', 'woocommerce-exporter' )
					);
				}
			}
			unset( $custom_fields, $custom_field );
		}
		unset( $options );
	}

	// WooCommerce Subscriptions - http://www.woothemes.com/products/woocommerce-subscriptions/
	if( class_exists( 'WC_Subscriptions_Manager' ) ) {
		$fields[] = array(
			'name' => 'subscription_price',
			'label' => __( 'Subscription Price', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Subscriptions', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'subscription_period_interval',
			'label' => __( 'Subscription Period Interval', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Subscriptions', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'subscription_period',
			'label' => __( 'Subscription Period', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Subscriptions', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'subscription_length',
			'label' => __( 'Subscription Length', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Subscriptions', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'subscription_sign_up_fee',
			'label' => __( 'Subscription Sign-up Fee', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Subscriptions', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'subscription_trial_length',
			'label' => __( 'Subscription Trial Length', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Subscriptions', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'subscription_trial_period',
			'label' => __( 'Subscription Trial Period', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Subscriptions', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'subscription_limit',
			'label' => __( 'Limit Subscription', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Subscriptions', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Bookings - http://www.woothemes.com/products/woocommerce-bookings/
	if( class_exists( 'WC_Bookings' ) ) {
		$fields[] = array(
			'name' => 'booking_has_persons',
			'label' => __( 'Booking Has Persons', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Bookings', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'booking_has_resources',
			'label' => __( 'Booking Has Resources', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Bookings', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'booking_base_cost',
			'label' => __( 'Booking Base Cost', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Bookings', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'booking_block_cost',
			'label' => __( 'Booking Block Cost', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Bookings', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'booking_display_cost',
			'label' => __( 'Booking Display Cost', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Bookings', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'booking_requires_confirmation',
			'label' => __( 'Booking Requires Confirmation', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Bookings', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'booking_user_can_cancel',
			'label' => __( 'Booking Can Be Cancelled', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Bookings', 'woocommerce-exporter' )
		);
	}

	// Barcodes for WooCommerce - http://www.wolkenkraft.com/produkte/barcodes-fuer-woocommerce/
	if( function_exists( 'wpps_requirements_met' ) ) {
		$fields[] = array(
			'name' => 'barcode_type',
			'label' => __( 'Barcode Type', 'woocommerce-exporter' ),
			'hover' => __( 'Barcodes for WooCommerce', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'barcode',
			'label' => __( 'Barcode', 'woocommerce-exporter' ),
			'hover' => __( 'Barcodes for WooCommerce', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Pre-Orders - http://www.woothemes.com/products/woocommerce-pre-orders/
	if( class_exists( 'WC_Pre_Orders' ) ) {
		$fields[] = array(
			'name' => 'pre_orders_enabled',
			'label' => __( 'Pre-Order Enabled', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Pre-Orders', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'pre_orders_availability_date',
			'label' => __( 'Pre-Order Availability Date', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Pre-Orders', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'pre_orders_fee',
			'label' => __( 'Pre-Order Fee', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Pre-Orders', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'pre_orders_charge',
			'label' => __( 'Pre-Order Charge', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Pre-Orders', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Product Fees - https://wordpress.org/plugins/woocommerce-product-fees/
	if( class_exists( 'WooCommerce_Product_Fees' ) ) {
		$fields[] = array(
			'name' => 'fee_name',
			'label' => __( 'Product Fee Name', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Product Fees', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'fee_amount',
			'label' => __( 'Product Fee Amount', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Product Fees', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'fee_multiplier',
			'label' => __( 'Product Fee Multiplier', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Product Fees', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Events - http://www.woocommerceevents.com/
	if( class_exists( 'WooCommerce_Events' ) ) {
		$fields[] = array(
			'name' => 'is_event',
			'label' => __( 'Is Event', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Events', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'event_date',
			'label' => __( 'Event Date', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Events', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'event_start_time',
			'label' => __( 'Event Start Time', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Events', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'event_end_time',
			'label' => __( 'Event End Time', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Events', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'event_venue',
			'label' => __( 'Event Venue', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Events', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'event_gps',
			'label' => __( 'Event GPS Coordinates', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Events', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'event_googlemaps',
			'label' => __( 'Event Google Maps Coordinates', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Events', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'event_directions',
			'label' => __( 'Event Directions', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Events', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'event_phone',
			'label' => __( 'Event Phone', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Events', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'event_email',
			'label' => __( 'Event E-mail', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Events', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'event_ticket_logo',
			'label' => __( 'Event Ticket Logo', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Events', 'woocommerce-exporter' )
		);
		$fields[] = array(
			'name' => 'event_ticket_text',
			'label' => __( 'Event Ticket Text', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Events', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Variation Swatches and Photos - https://www.woothemes.com/products/variation-swatches-and-photos/
	if( class_exists( 'WC_SwatchesPlugin' ) ) {
		// Do something
	}

	// WooCommerce Uploads - https://wpfortune.com/shop/plugins/woocommerce-uploads/
	if( class_exists( 'WPF_Uploads' ) ) {
		$fields[] = array(
			'name' => 'enable_uploads',
			'label' => __( 'Enable Uploads', 'woocommerce-exporter' ),
			'hover' => __( 'WooCommerce Uploads', 'woocommerce-exporter' )
		);
	}

	// WooCommerce Tab Manager - http://www.woothemes.com/products/woocommerce-tab-manager/
	if( class_exists( 'WC_Tab_Manager' ) ) {
		// Custom Product Tabs
		$custom_product_tabs = woo_ce_get_option( 'custom_product_tabs', '' );
		if( !empty( $custom_product_tabs ) ) {
			foreach( $custom_product_tabs as $custom_product_tab ) {
				if( !empty( $custom_product_tab ) ) {
					$fields[] = array(
						'name' => sprintf( 'product_tab_%s', sanitize_key( $custom_product_tab ) ),
						'label' => sprintf( __( 'Product Tab: %s', 'woocommerce-exporter' ), woo_ce_clean_export_label( $custom_product_tab ) ),
						'hover' => sprintf( __( 'Custom Product Tab: %s', 'woocommerce-exporter' ), $custom_product_tab )
					);
				}
			}
		}
		unset( $custom_product_tabs, $custom_product_tab );
	}

	// WooCommerce Jetpack - http://woojetpack.com/shop/wordpress-woocommerce-jetpack-plus/
/*
	// @mod - Needs alot of love in 2.1+, JetPack Plus, now Booster is huge
	if( class_exists( 'WC_Jetpack' ) ) {
		// Check if Call for Price is enabled
		if( get_option( 'wcj_call_for_price_enabled', false ) ) {
			// Instead of the price
			$fields[] = array(
				'name' => 'wcf_price_instead',
				'label' => __( 'Instead of the ', 'woocommerce-exporter' )
			);
			// WooCommerce Jetpack Plus fields
			if( class_exists( 'WC_Jetpack_Plus' ) ) {
				// Do something
			}
		}
	}
*/

	// Custom Product meta
	$custom_products = woo_ce_get_option( 'custom_products', '' );
	if( !empty( $custom_products ) ) {
		foreach( $custom_products as $custom_product ) {
			if( !empty( $custom_product ) ) {
				$fields[] = array(
					'name' => $custom_product,
					'label' => woo_ce_clean_export_label( $custom_product ),
					'hover' => sprintf( apply_filters( 'woo_ce_extend_product_fields_custom_product_hover', '%s: %s' ), __( 'Custom Product', 'woocommerce-exporter' ), $custom_product )
				);
			}
		}
	}
	unset( $custom_products, $custom_product );

	return $fields;

}
add_filter( 'woo_ce_product_fields', 'woo_ce_extend_product_fields' );

// Returns the export column header label based on an export column slug
function woo_ce_get_product_field( $name = null, $format = 'name' ) {

	$output = '';
	if( $name ) {
		$fields = woo_ce_get_product_fields();
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

// Returns a list of WooCommerce Product IDs to export process
function woo_ce_get_products( $args = array() ) {

	global $export;

	$limit_volume = -1;
	$offset = 0;
	$product_categories = false;
	$product_tags = false;
	$product_brands = false;
	$product_vendors = false;
	$product_status = false;
	$product_type = false;
	$product_sku = false;
	$product_stock = false;
	$product_featured = false;
	$product_status = false;
	$product_shipping_class = false;
	$product_language = false;
	$orderby = 'ID';
	$order = 'ASC';
	if( $args ) {
		$limit_volume = ( isset( $args['limit_volume'] ) ? $args['limit_volume'] : false );
		$offset = ( isset( $args['offset'] ) ? $args['offset'] : false );
		if( !empty( $args['product_categories'] ) )
			$product_categories = $args['product_categories'];
		if( !empty( $args['product_tags'] ) )
			$product_tags = $args['product_tags'];
		if( !empty( $args['product_brands'] ) )
			$product_brands = $args['product_brands'];
		if( !empty( $args['product_vendors'] ) )
			$product_vendors = $args['product_vendors'];
		if( !empty( $args['product_status'] ) )
			$product_status = $args['product_status'];
		if( !empty( $args['product_type'] ) )
			$product_type = $args['product_type'];
		if( !empty( $args['product_sku'] ) )
			$product_sku = $args['product_sku'];
		if( !empty( $args['product_stock'] ) )
			$product_stock = $args['product_stock'];
		if( !empty( $args['product_featured'] ) )
			$product_featured = $args['product_featured'];
		if( !empty( $args['product_shipping_class'] ) )
			$product_shipping_class = $args['product_shipping_class'];
		if( !empty( $args['product_language'] ) )
			$product_language = $args['product_language'];
		if( isset( $args['product_orderby'] ) )
			$orderby = $args['product_orderby'];
		if( isset( $args['product_order'] ) )
			$order = $args['product_order'];
		$product_dates_filter = ( isset( $args['product_dates_filter'] ) ? $args['product_dates_filter'] : false );
		switch( $product_dates_filter ) {

			case 'manual':
				$date_format = woo_ce_get_option( 'date_format', 'd/m/Y' );

				// Populate empty from or to dates
				if( !empty( $args['product_dates_from'] ) )
					$product_dates_from = woo_ce_format_order_date( $args['product_dates_from'] );
				else
					$product_dates_from = woo_ce_get_product_first_date( $date_format );
				if( !empty( $args['product_dates_to'] ) )
					$product_dates_to = woo_ce_format_order_date( $args['product_dates_to'] );
				else
					$product_dates_to = date( 'd-m-Y', mktime( 0, 0, 0, date( 'n' ), date( 'd' ) ) );

				// WP_Query only accepts D-m-Y so we must format dates to that
				if( $date_format <> 'd/m/Y' ) {
					$date_format = woo_ce_format_order_date( $date_format );
					if( function_exists( 'date_create_from_format' ) && function_exists( 'date_format' ) ) {
						if( $product_dates_from = date_create_from_format( $date_format, $product_dates_from ) )
							$product_dates_from = date_format( $product_dates_from, 'd-m-Y' );
						if( $product_dates_to = date_create_from_format( $date_format, $product_dates_to ) )
							$product_dates_to = date_format( $product_dates_to, 'd-m-Y' );
					}
				}
				break;

			default:
				$product_dates_from = false;
				$product_dates_to = false;
				break;

		}
		if( !empty( $product_dates_from ) && !empty( $product_dates_to ) ) {
			$product_dates_from = explode( '-', $product_dates_from );
			// Check that a valid date was provided
			if( isset( $product_dates_from[0] ) && isset( $product_dates_from[1] ) && isset( $product_dates_from[2] ) ) {
				$product_dates_from = array(
					'year' => $product_dates_from[2],
					'month' => $product_dates_from[1],
					'day' => $product_dates_from[0],
					'hour' => 0,
					'minute' => 0,
					'second' => 0
				);
			} else {
				$product_dates_from = false;
			}
			$product_dates_to = explode( '-', $product_dates_to );
			// Check that a valid date was provided
			if( isset( $product_dates_to[0] ) && isset( $product_dates_to[1] ) && isset( $product_dates_to[2] ) ) {
				$product_dates_to = array(
					'year' => $product_dates_to[2],
					'month' => $product_dates_to[1],
					'day' => $product_dates_to[0],
					'hour' => 23,
					'minute' => 59,
					'second' => 59
				);
			} else {
				$product_dates_to = false;
			}
		}
	}
	$post_type = apply_filters( 'woo_ce_get_products_post_type', array( 'product' ) );
	$post_status = apply_filters( 'woo_ce_get_products_status', array( 'publish', 'pending', 'draft', 'future', 'private' ) );

	$args = array(
		'post_type' => $post_type,
		'orderby' => $orderby,
		'order' => $order,
		'offset' => $offset,
		'posts_per_page' => $limit_volume,
		'post_status' => woo_ce_post_statuses( $post_status, true ),
		'fields' => 'ids',
		'suppress_filters' => false
	);
	// Filter Products by Product Category
	if( $product_categories ) {
		$term_taxonomy = 'product_cat';
		// Check if tax_query has been created
		if( !isset( $args['tax_query'] ) )
			$args['tax_query'] = array();
		$args['tax_query'][] = array(
			array(
				'taxonomy' => $term_taxonomy,
				'field' => 'id',
				'terms' => $product_categories
			)
		);
	}
	// Filter Products by Product Tag
	if( $product_tags ) {
		$term_taxonomy = 'product_tag';
		// Check if tax_query has been created
		if( !isset( $args['tax_query'] ) )
			$args['tax_query'] = array();
		$args['tax_query'][] = array(
			array(
				'taxonomy' => $term_taxonomy,
				'field' => 'id',
				'terms' => $product_tags
			)
		);
	}
	// WooCommerce Brands Addon - http://woothemes.com/woocommerce/
	if( $product_brands ) {
		$term_taxonomy = apply_filters( 'woo_ce_brand_term_taxonomy', 'product_brand' );
		// Check if tax_query has been created
		if( !isset( $args['tax_query'] ) )
			$args['tax_query'] = array();
		$args['tax_query'][] = array(
			array(
				'taxonomy' => $term_taxonomy,
				'field' => 'id',
				'terms' => $product_brands
			)
		);
	}
	// Product Vendors - http://www.woothemes.com/products/product-vendors/
	if( $product_vendors ) {
		$term_taxonomy = 'shop_vendor';
		// Check if tax_query has been created
		if( !isset( $args['tax_query'] ) )
			$args['tax_query'] = array();
		$args['tax_query'][] = array(
			array(
				'taxonomy' => $term_taxonomy,
				'field' => 'id',
				'terms' => $product_vendors
			)
		);
	}
	// Filter Products by Shipping Class
	if( $product_shipping_class ) {
		$term_taxonomy = 'product_shipping_class';
		// Check if tax_query has been created
		if( !isset( $args['tax_query'] ) )
			$args['tax_query'] = array();
		$args['tax_query'][] = array(
			array(
				'taxonomy' => $term_taxonomy,
				'field' => 'id',
				'terms' => $product_shipping_class
			)
		);
	}
	// Filter Products by Language
	if( $product_language ) {

		global $sitepress;

		// See if our WPML integration magic sticks
		remove_filter( 'posts_where' , array( $sitepress, 'posts_where_filter' ), 10 );
		add_filter( 'posts_where' , 'woo_ce_wp_query_product_where_override_language' );

	}
	// Filter Products by Post Status
	if( $product_status ) {
		$args['post_status'] = woo_ce_post_statuses( $product_status, true );
	}
	// Filter Products by Product Type
	if( is_array( $product_type ) && !empty( $product_type ) ) {
		// Check if we are just exporting variations
		if( in_array( 'variation', $product_type ) && count( $product_type ) == 1 ) {
			$args['post_type'] = array( 'product_variation' );
		}
		$args['meta_query'] = array(
			'relation' => 'OR'
		);
		if( in_array( 'downloadable', $product_type ) ) {
			$args['meta_query'][] = array(
				'key' => '_downloadable',
				'value' => 'yes',
				'compare' => 'EXISTS'
			);
		}
		if( in_array( 'virtual', $product_type ) ) {
			$args['meta_query'][] = array(
				'key' => '_virtual',
				'value' => 'yes'
			);
		}
		// Remove non-Term based Product Types before we tack on our tax_query
		$term_product_type = $product_type;
		foreach( $term_product_type as $key => $type ) {
			if( in_array( $type, array( 'downloadable', 'virtual', 'variation' ) ) )
				unset( $term_product_type[$key] );
		}
		// Override for exporting Variations without Variables
		if( in_array( 'variation', $product_type ) && in_array( 'variable', $product_type ) == false ) {
			$term_product_type[] = 'variable';
		}
		if( !empty( $term_product_type ) ) {
			$term_taxonomy = 'product_type';
			$args['tax_query'][] = array(
				array(
					'taxonomy' => $term_taxonomy,
					'field' => 'slug',
					'terms' => $term_product_type
				)
			);
		} else {
			unset( $args['meta_query'] );
		}
		unset( $term_product_type );
	}
	// Filter Products by Featured
	if( $product_featured ) {
		$args['meta_query'][] = array(
			'key' => '_featured',
			'value' => $product_featured
		);
	}
	// Filter Products by SKU
	if( $product_sku ) {
		$args['post__in'] = array_map( 'absint', $product_sku );
	}
	// Filter Product dates
	if( !empty( $product_dates_from ) && !empty( $product_dates_to ) ) {
		$args['date_query'] = array(
			array(
				'column' => 'post_modified_date',
				'before' => $product_dates_to,
				'after' => $product_dates_from,
				'inclusive' => true
			)
		);
	}
	// Sort Products by SKU
	if( $orderby == 'sku' ) {
		$args['orderby'] = 'meta_value';
		$args['meta_key'] = '_sku';
	}
	$products = array();

	// Allow other developers to bake in their own filters
	$args = apply_filters( 'woo_ce_get_products_args', $args );

	$product_ids = new WP_Query( $args );
	if( $product_ids->posts ) {
		foreach( $product_ids->posts as $product_id ) {

			// Get Product details
			$product = get_post( $product_id );

			// Filter out Variations that don't have a Parent Product that exists
			if( isset( $product->post_type ) && $product->post_type == 'product_variation' ) {
				// Check if Parent exists
				if( $product->post_parent ) {
					if( get_post( $product->post_parent ) == false ) {
						unset( $product_id, $product );
						continue;
					}
				}
			}

			// Filter out Products based on the Stock Status and Quantity
			$term_taxonomy = 'product_type';
			if( $product_stock && has_term( 'variable', $term_taxonomy, $product_id ) !== true ) {
				$manage_stock = get_post_meta( $product_id, '_manage_stock', true );
				$stock_status = get_post_meta( $product_id, '_stock_status', true );
				$quantity = get_post_meta( $product_id, '_stock', true );
				$quantity = ( function_exists( 'wc_stock_amount' ) ? wc_stock_amount( $quantity ) : absint( $quantity ) );
				switch( $product_stock ) {

					case 'outofstock':
						if( ( $manage_stock == 'yes' && $quantity > 0 ) || $stock_status <> 'outofstock' ) {
							unset( $product_id, $product );
							continue;
						}
						break;

					case 'instock':
						if( ( $manage_stock == 'yes' && $quantity == 0 ) || $stock_status <> 'instock' ) {
							unset( $product_id, $product );
							continue;
						}
						break;

				}
				unset( $stock_status, $quantity );
			}

			if( isset( $product_id ) )
				$products[] = $product_id;

			// Include Variables in a new WP_Query if a tax_query filter is used or WPML exists
			if( ( isset( $args['tax_query'] ) || woo_ce_detect_wpml() ) && isset( $product_id ) ) {
				$term_taxonomy = 'product_type';
				if( has_term( 'variable', $term_taxonomy, $product_id ) && ( $product_type !== false && in_array( 'variation', $product_type ) ) ) {
					$variable_args = array(
						'post_type' => 'product_variation',
						'orderby' => $orderby,
						'order' => $order,
						'post_parent' => $product_id,
						'post_status' => array( 'publish' ),
						'fields' => 'ids'
					);
					// Filter Products by Post Status
					if( $product_status )
						$variable_args['post_status'] = woo_ce_post_statuses( $product_status, true );
					$variables = array();
					$variable_ids = new WP_Query( $variable_args );
					if( $variable_ids->posts ) {
						foreach( $variable_ids->posts as $variable_id ) {

							// Filter out Products based on the Stock Status and Quantity
							if( $product_stock ) {
								$manage_stock = get_post_meta( $variable_id, '_manage_stock', true );
								$stock_status = get_post_meta( $variable_id, '_stock_status', true );
								$quantity = get_post_meta( $variable_id, '_stock', true );
								$quantity = ( function_exists( 'wc_stock_amount' ) ? wc_stock_amount( $quantity ) : absint( $quantity ) );
								switch( $product_stock ) {

									case 'outofstock':
										if( ( $manage_stock == 'yes' && $quantity > 0 ) || $stock_status <> 'outofstock' ) {
											unset( $variable_id );
											continue;
										}
										break;

									case 'instock':
										if( ( $manage_stock == 'yes' && $quantity == 0 ) || $stock_status <> 'instock' ) {
											unset( $variable_id );
											continue;
										}
										break;

								}
								unset( $stock_status, $quantity );
							}

							if( isset( $variable_id ) ) {
								// Check we're not including a duplicate Product ID
								if( !in_array( $variable_id, $product_ids->posts ) )
									$products[] = $variable_id;
							}
						}
					}
					unset( $variables, $variable_ids, $variable_args, $variable_id );
				}
			}

			// Override for exporting Variations without Variables
			if( is_array( $product_type ) && !empty( $product_type ) ) {
				if( in_array( 'variation', $product_type ) && in_array( 'variable', $product_type ) == false ) {
					$term_taxonomy = 'product_type';
					if( has_term( 'variable', $term_taxonomy, $product_id ) ) {
						// Remove the Variable Product ID
						$key = array_search( $product_id, $products );
						if( $key !== false )
							unset( $products[$key] );
					}
				}
			}

		}
		// Only populate the $export Global if it is an export
		if( isset( $export ) )
			$export->total_rows = count( $products );
		unset( $product_ids, $product_id );
	}
	// Filter Products by Language
	if( $product_language ) {

		global $sitepress;

		add_filter( 'posts_where' , array( $sitepress, 'posts_where_filter' ), 10, 2 );
		remove_filter( 'posts_where' , 'woo_ce_wp_query_product_where_override_language' );
	}

	return $products;

}

function woo_ce_wp_query_product_where_override_language( $where ) {

	global $export;

	$condition = '';
	if( !empty( $export->args ) ) {
		$languages = $export->args['product_language'];
		if( !empty( $languages ) ) {
			$where = " AND t.language_code IN ('" . implode( "', '", array_values( $languages ) ) . "')";
		}
	}
	return $where . $condition;

}

function woo_ce_get_product_data( $product_id = 0, $args = array(), $fields = array() ) {

	// Get Product defaults
	$weight_unit = get_option( 'woocommerce_weight_unit' );
	$dimension_unit = get_option( 'woocommerce_dimension_unit' );
	$height_unit = $dimension_unit;
	$width_unit = $dimension_unit;
	$length_unit = $dimension_unit;

	$product = get_post( $product_id );
	$_product = ( function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : false );

	$product->parent_id = '';
	$product->parent_sku = '';
	if( $product->post_type == 'product_variation' ) {
		// Assign Parent ID for Variants then check if Parent exists
		if( $product->parent_id = $product->post_parent )
			$product->parent_sku = get_post_meta( $product->post_parent, '_sku', true );
		else
			$product->parent_id = '';
	}
	$product->product_id = $product_id;
	$product->sku = get_post_meta( $product_id, '_sku', true );
	add_filter( 'the_title', 'woo_ce_get_product_title', 10, 2 );
	$product->name = woo_ce_format_post_title( get_the_title( $product_id ) );
	remove_filter( 'the_title', 'woo_ce_get_product_title' );
	if( $product->post_type <> 'product_variation' )
		$product->permalink = get_permalink( $product_id );
	$product->product_url = ( method_exists( $_product, 'get_permalink' ) ? $_product->get_permalink() : get_permalink( $product_id ) );
	$product->slug = $product->post_name;
	$product->description = woo_ce_format_description_excerpt( $product->post_content );
	$product->excerpt = woo_ce_format_description_excerpt( $product->post_excerpt );
	// Check if we're dealing with a Variable Product Type
	$term_taxonomy = 'product_type';
	if( has_term( 'variable', $term_taxonomy, $product_id ) ) {
		$product->price = get_post_meta( $product_id, '_price', true );
		if( method_exists( $_product, 'get_variation_regular_price' ) && method_exists( $_product, 'get_variation_sale_price' ) ) {
			$pricing_args = array(
				'min_price' => $_product->get_variation_regular_price( 'min', false ),
				'max_price' => $_product->get_variation_regular_price( 'max', false ),
				'min_sale_price' => $_product->get_variation_sale_price( 'min', true ),
				'max_sale_price' => $_product->get_variation_sale_price( 'max', true )
			);
			if( $pricing_args['min_price'] == $pricing_args['max_price'] ) {
				$product->price = woo_ce_format_price( $pricing_args['min_price'] );
				$product->sale_price = woo_ce_format_price( $pricing_args['min_sale_price'] );
			} else {
				$product->price = sprintf( apply_filters( 'woo_ce_product_variable_price', '%s-%s' ), woo_ce_format_price( $pricing_args['min_price'] ), woo_ce_format_price( $pricing_args['max_price'] ) );
				$product->sale_price = sprintf( apply_filters( 'woo_ce_product_variable_sale_price', '%s-%s' ), woo_ce_format_price( $pricing_args['min_sale_price'] ), woo_ce_format_price( $pricing_args['max_sale_price'] ) );
			}
			$product = apply_filters( 'woo_ce_product_variation_pricing', $product, $pricing_args );
			unset( $pricing_args );
		}
	} else {
		$product->price = get_post_meta( $product_id, '_regular_price', true );
		$product->sale_price = get_post_meta( $product_id, '_sale_price', true );
		if( $product->price != '' )
			$product->price = woo_ce_format_price( $product->price );
		if( $product->sale_price != '' )
			$product->sale_price = woo_ce_format_price( $product->sale_price );
	}
	$product->sale_price_dates_from = woo_ce_format_product_sale_price_dates( get_post_meta( $product_id, '_sale_price_dates_from', true ) );
	$product->sale_price_dates_to = woo_ce_format_product_sale_price_dates( get_post_meta( $product_id, '_sale_price_dates_to', true ) );
	$product->post_date = woo_ce_format_date( $product->post_date );
	$product->post_modified = woo_ce_format_date( $product->post_modified );
	$product->type = woo_ce_get_product_assoc_type( $product_id );
	if( $product->post_type == 'product_variation' ) {
		$product->description = woo_ce_format_description_excerpt( get_post_meta( $product_id, '_variation_description', true ) );
		// Override the Product Type for Variations
		$product->type = __( 'Variation', 'woocommerce-exporter' );
		// Override the Description and Excerpt if Variation Formatting is enabled
		if( woo_ce_get_option( 'variation_formatting', 0 ) ) {
			$parent = get_post( $product->parent_id );
			if( empty( $product->description ) )
				$product->description = $parent->post_content;
			if( empty( $product->excerpt ) )
				$product->excerpt = $parent->post_excerpt;
			unset( $parent );
		}
	}
	$product->visibility = woo_ce_format_product_visibility( get_post_meta( $product_id, '_visibility', true ) );
	$product->featured = woo_ce_format_switch( get_post_meta( $product_id, '_featured', true ) );
	$product->virtual = woo_ce_format_switch( get_post_meta( $product_id, '_virtual', true ) );
	$product->downloadable = woo_ce_format_switch( get_post_meta( $product_id, '_downloadable', true ) );
	$product->weight = get_post_meta( $product_id, '_weight', true );
	$product->weight_unit = ( $product->weight != '' ? $weight_unit : '' );
	$product->height = get_post_meta( $product_id, '_height', true );
	$product->height_unit = ( $product->height != '' ? $height_unit : '' );
	$product->width = get_post_meta( $product_id, '_width', true );
	$product->width_unit = ( $product->width != '' ? $width_unit : '' );
	$product->length = get_post_meta( $product_id, '_length', true );
	$product->length_unit = ( $product->length != '' ? $length_unit : '' );
	$product->category = woo_ce_get_product_assoc_categories( $product_id, $product->parent_id );
	$product->tag = woo_ce_get_product_assoc_tags( $product_id );
	$product->manage_stock = woo_ce_format_switch( get_post_meta( $product_id, '_manage_stock', true ) );
	$product->allow_backorders = woo_ce_format_switch( get_post_meta( $product_id, '_backorders', true ) );
	$product->sold_individually = woo_ce_format_switch( get_post_meta( $product_id, '_sold_individually', true ) );
	$product->total_sales = get_post_meta( $product_id, 'total_sales', true );
	$product->upsell_ids = woo_ce_get_product_assoc_upsell_ids( $product_id );
	$product->crosssell_ids = woo_ce_get_product_assoc_crosssell_ids( $product_id );
	$product->quantity = get_post_meta( $product_id, '_stock', true );
	// Override Variable with total stock quantity
	if( has_term( 'variable', $term_taxonomy, $product_id ) ) {
		$product->quantity = ( method_exists( $_product, 'get_total_stock' ) ? $_product->get_total_stock() : $product->quantity );
	}
	$product->quantity = ( function_exists( 'wc_stock_amount' ) ? wc_stock_amount( $product->quantity ) : $product->quantity );
	$product->stock_status = woo_ce_format_product_stock_status( get_post_meta( $product_id, '_stock_status', true ), $product->quantity );
	$product->image = woo_ce_get_product_assoc_featured_image( $product_id, $product->parent_id );
	$product->image_embed = '';
	if( !empty( $product->image ) ) {
		$image_id = woo_ce_get_product_assoc_featured_image( $product_id, $product->parent_id, 'image_id' );
		$upload_dir = wp_upload_dir();
		if( $metadata = wp_get_attachment_metadata( $image_id ) ) {
			$thumbnail_size = 'shop_thumbnail';
			if( isset( $metadata['sizes'][$thumbnail_size] ) && $metadata['sizes'][$thumbnail_size]['file'] ) {
				$image_path = pathinfo( $metadata['file'] );
				$product->image_embed = trailingslashit( $upload_dir['basedir'] ) . trailingslashit( $image_path['dirname'] ) . $metadata['sizes'][$thumbnail_size]['file'];
			}
		}
	}
	$product->image_thumbnail = woo_ce_get_product_assoc_featured_image( $product_id, $product->parent_id, 'thumbnail' );
	$product->product_gallery = woo_ce_get_product_assoc_product_gallery( $product_id );
	$product->product_gallery_thumbnail = woo_ce_get_product_assoc_product_gallery( $product_id, 'thumbnail' );
	$product->tax_status = woo_ce_format_product_tax_status( get_post_meta( $product_id, '_tax_status', true ) );
	$product->tax_class = woo_ce_format_product_tax_class( get_post_meta( $product_id, '_tax_class', true ) );
	$product->shipping_class = woo_ce_get_product_assoc_shipping_class( $product_id );
	$product->external_url = get_post_meta( $product_id, '_product_url', true );
	$product->button_text = get_post_meta( $product_id, '_button_text', true );
	$product->download_file_path = woo_ce_get_product_assoc_download_files( $product_id, 'url' );
	$product->download_file_name = woo_ce_get_product_assoc_download_files( $product_id, 'name' );
	$product->download_limit = get_post_meta( $product_id, '_download_limit', true );
	$product->download_expiry = get_post_meta( $product_id, '_download_expiry', true );
	$product->download_type = woo_ce_format_product_download_type( get_post_meta( $product_id, '_download_type', true ) );
	$product->purchase_note = get_post_meta( $product_id, '_purchase_note', true );
	$product->product_status = woo_ce_format_post_status( $product->post_status );
	$product->enable_reviews = woo_ce_format_comment_status( $product->comment_status );
	$product->review_count = get_post_meta( $product_id, '_wc_review_count', true );
	$rating_count = get_post_meta( $product_id, '_wc_rating_count', true );
	if( $product->post_type == 'product' ) {
		$product->rating_count = count( $rating_count );
	}
	$product->average_rating = get_post_meta( $product_id, '_wc_average_rating', true );
	unset( $_product );

	// Scan for global Attributes first
	$attributes = woo_ce_get_product_attributes();
	if( !empty( $attributes ) && $product->post_type == 'product_variation' ) {
		// We're dealing with a single Variation, strap yourself in.
		foreach( $attributes as $attribute ) {
			$attribute_value = get_post_meta( $product_id, sprintf( 'attribute_pa_%s', $attribute->attribute_name ), true );
			if( !empty( $attribute_value ) ) {
				$term_id = term_exists( $attribute_value, sprintf( 'pa_%s', $attribute->attribute_name ) );
				if( $term_id !== 0 && $term_id !== null && !is_wp_error( $term_id ) ) {
					$term = get_term( $term_id['term_id'], sprintf( 'pa_%s', $attribute->attribute_name ) );
					$attribute_value = $term->name;
					unset( $term );
				}
				unset( $term_id );
			}
			$product->{'attribute_' . $attribute->attribute_name} = $attribute_value;
			unset( $attribute_value );
		}
	} else {
		// Either the Variation Parent or a Simple Product, scan for global and custom Attributes
		$product->attributes = maybe_unserialize( get_post_meta( $product_id, '_product_attributes', true ) );
		if( !empty( $product->attributes ) ) {
			$default_attributes = maybe_unserialize( get_post_meta( $product_id, '_default_attributes', true ) );
			$product->default_attributes = '';
			// Check for taxonomy-based attributes
			if( !empty( $attributes ) ) {
				foreach( $attributes as $attribute ) {
					if( !empty( $default_attributes ) && is_array( $default_attributes ) ) {
						if( array_key_exists( 'pa_' . $attribute->attribute_name, $default_attributes ) )
							$product->default_attributes .= $attribute->attribute_label . ': ' . $default_attributes['pa_' . $attribute->attribute_name] . "|";
					}
					if( isset( $product->attributes['pa_' . $attribute->attribute_name] ) )
						$product->{'attribute_' . $attribute->attribute_name} = woo_ce_get_product_assoc_attributes( $product_id, $product->attributes['pa_' . $attribute->attribute_name], 'product' );
					else
						$product->{'attribute_' . $attribute->attribute_name} = woo_ce_get_product_assoc_attributes( $product_id, $attribute, 'global' );
				}
			}
			// Check for per-Product attributes (custom)
			foreach( $product->attributes as $key => $attribute ) {
				if( !empty( $default_attributes ) && is_array( $default_attributes ) ) {
					if( array_key_exists( $key, $default_attributes ) )
						$product->default_attributes .= $attribute['name'] . ': ' . $default_attributes[$key] . "|";
				}
				if( $attribute['is_taxonomy'] == 0 ) {
					if( !isset( $product->{'attribute_' . $key} ) )
						$product->{'attribute_' . $key} = $attribute['value'];
				}
			}
			if( !empty( $product->default_attributes ) )
				$product->default_attributes = substr( $product->default_attributes, 0, -1 );
		}
	}

	// Allow Plugin/Theme authors to add support for additional Product columns
	$product = apply_filters( 'woo_ce_product_item', $product, $product_id );

	// Trim back the Product just to requested export fields
	if( !empty( $fields ) ) {
		$fields = array_merge( $fields, array( 'id', 'ID', 'post_parent', 'filter' ) );
		if( !empty( $product ) ) {
			foreach( $product as $key => $data ) {
				if( !in_array( $key, $fields ) )
					unset( $product->$key );
			}
		}
	}

	return $product;

}

// Filters the get_the_title() function and adds friendly Variation information
function woo_ce_get_product_title( $title = '', $post_ID = '' ) {

	if( !empty( $post_ID ) ) {

		$product = ( function_exists( 'wc_get_product' ) ? wc_get_product( $post_ID ) : false );
		if( !empty( $product ) ) {
			// Check if we're dealing with a Variation
			$title = $product->get_title();
			if ( $product->is_type( 'variation' ) ) {
				$list_attributes = array();
				$attributes = $product->get_variation_attributes();
				if( !empty( $attributes ) ) {
					foreach ( $attributes as $name => $attribute ) {
						$list_attributes[] = wc_attribute_label( str_replace( 'attribute_', '', $name ) ) . ': ' . $attribute;
					}
					$title .= ' - ' . implode( ', ', $list_attributes );
				}
				unset( $attributes );
			}
		}

	}
	return $title;

}

// Filters the get_the_title() function and adds friendly Variation information suffixed with SKU
function woo_ce_get_product_title_sku( $title = '', $post_ID = '' ) {

	if( !empty( $post_ID ) ) {

		$product = ( function_exists( 'wc_get_product' ) ? wc_get_product( $post_ID ) : false );
		if( !empty( $product ) ) {
			// Check if we're dealing with a Variation
			$title = $product->get_title();
			if ( $product->is_type( 'variation' ) ) {
				$list_attributes = array();
				$attributes = $product->get_variation_attributes();
				if( !empty( $attributes ) ) {
					foreach ( $attributes as $name => $attribute ) {
						$list_attributes[] = wc_attribute_label( str_replace( 'attribute_', '', $name ) ) . ': ' . $attribute;
					}
					$title .= ' - ' . implode( ', ', $list_attributes );
				}
				unset( $attributes );
			}
			$sku = $product->get_sku();
			if( !empty( $sku ) )
				$title .= ' (' . sprintf( __( 'SKU: %s', 'woocommerce-exporter' ), $sku ) . ')';
			unset( $sku );
		}

	}
	return $title;

}

// Returns Product Categories associated to a specific Product
function woo_ce_get_product_assoc_categories( $product_id = 0, $parent_id = 0 ) {

	global $export;

	$output = '';
	$term_taxonomy = 'product_cat';
	// Return Product Categories of Parent if this is a Variation
	$categories = array();
	if( !empty( $parent_id ) )
		$product_id = $parent_id;
	if( !empty( $product_id ) )
		$categories = wp_get_object_terms( $product_id, $term_taxonomy );
	if( !empty( $categories ) && !is_wp_error( $categories ) ) {
		$size = apply_filters( 'woo_ce_get_product_assoc_categories_size', count( $categories ) );
		for( $i = 0; $i < $size; $i++ ) {
			if( $categories[$i]->parent == '0' ) {
				$output .= $categories[$i]->name . $export->category_separator;
			} else {
				// Check if Parent -> Child
				$parent_category = get_term( $categories[$i]->parent, $term_taxonomy );
				// Check if Parent -> Child -> Subchild
				if( $parent_category->parent == '0' ) {
					$output .= $parent_category->name . '>' . $categories[$i]->name . $export->category_separator;
					$output = str_replace( $parent_category->name . $export->category_separator, '', $output );
				} else {
					$root_category = get_term( $parent_category->parent, $term_taxonomy );
					$output .= $root_category->name . '>' . $parent_category->name . '>' . $categories[$i]->name . $export->category_separator;
					$output = str_replace( array(
						$root_category->name . '>' . $parent_category->name . $export->category_separator,
						$parent_category->name . $export->category_separator
					), '', $output );
				}
				unset( $root_category, $parent_category );
			}
		}
		$output = substr( $output, 0, -1 );
	} else {
		$output .= __( 'Uncategorized', 'woocommerce-exporter' );
	}
	return $output;

}

// Returns Product Tags associated to a specific Product
function woo_ce_get_product_assoc_tags( $product_id = 0 ) {

	global $export;

	$output = '';
	$term_taxonomy = 'product_tag';
	$tags = wp_get_object_terms( $product_id, $term_taxonomy );
	if( !empty( $tags ) && is_wp_error( $tags ) == false ) {
		$size = count( $tags );
		for( $i = 0; $i < $size; $i++ ) {
			if( $tag = get_term( $tags[$i]->term_id, $term_taxonomy ) )
				$output .= $tag->name . $export->category_separator;
		}
		$output = substr( $output, 0, -1 );
	}
	return $output;

}

// Returns the Featured Image associated to a specific Product
function woo_ce_get_product_assoc_featured_image( $product_id = 0, $parent_id = 0, $size = 'full' ) {

	$output = '';
	if( !empty( $product_id ) ) {
		$thumbnail_id = get_post_meta( $product_id, '_thumbnail_id', true );
		if( !empty( $thumbnail_id ) ) {
			if( $size == 'full' )
				$output = wp_get_attachment_url( $thumbnail_id );
			else if( $size == 'thumbnail' )
				$output = wp_get_attachment_thumb_url( $thumbnail_id );
			else if( $size == 'image_id' )
				$output = $thumbnail_id;
		} else if( !empty( $parent_id ) && woo_ce_get_option( 'variation_formatting', 0 ) ) {
			// Return Feature Image of Parent if this is a Variation
			$thumbnail_id = get_post_meta( $parent_id, '_thumbnail_id', true );
			if( !empty( $thumbnail_id ) ) {
				if( $size == 'full' )
					$output = wp_get_attachment_url( $thumbnail_id );
				else if( $size == 'thumbnail' )
					$output = wp_get_attachment_thumb_url( $thumbnail_id );
				else if( $size == 'image_id' )
					$output = $thumbnail_id;
			}
		}
	}
	return $output;

}

// Returns the Product Galleries associated to a specific Product
function woo_ce_get_product_assoc_product_gallery( $product_id = 0, $image_format = 'full' ) {

	global $export;

	if( !empty( $product_id ) ) {
		$images = get_post_meta( $product_id, '_product_image_gallery', true );
		if( !empty( $images ) ) {
			// Check if we're returning ID's or URL's
			if( $export->gallery_formatting == '0' ) {
				$images = explode( ',', $images );
				$output = implode( $export->category_separator, $images );
			} else if( in_array( $export->gallery_formatting, array( '1', '2' ) ) ) {
				$images = explode( ',', $images );
				$size = count( $images );
				for( $i = 0; $i < $size; $i++ ) {
					switch( $export->gallery_formatting ) {

						case '1':
							// Media URL
							if( $image_format == 'full' )
								$images[$i] = wp_get_attachment_url( $images[$i] );
							else if( $image_format == 'thumbnail' )
								$images[$i] = wp_get_attachment_thumb_url( $images[$i] );
							break;

						case '2':
							// Media filename
							if( $image_format == 'full' )
								$images[$i] = get_attached_file( $images[$i] );
							else if( $image_format == 'thumbnail' )
								$images[$i] = wp_get_attachment_thumb_file( $images[$i] );
							break;

					}
				}
				$output = implode( $export->category_separator, $images );
			}
			return $output;
		}
	}

}

// Returns the Product Type of a specific Product
function woo_ce_get_product_assoc_type( $product_id = 0 ) {

	global $export;

	$output = '';
	$term_taxonomy = 'product_type';
	$types = wp_get_object_terms( $product_id, $term_taxonomy );
	if( empty( $types ) )
		$types = array( get_term_by( 'name', 'simple', $term_taxonomy ) );
	if( $types ) {
		$size = count( $types );
		for( $i = 0; $i < $size; $i++ ) {
			$type = get_term( $types[$i]->term_id, $term_taxonomy );
			$output .= woo_ce_format_product_type( $type->name ) . $export->category_separator;
		}
		$output = substr( $output, 0, -1 );
	}
	return $output;

}

// Returns the Shipping Class of a specific Product
function woo_ce_get_product_assoc_shipping_class( $product_id = 0 ) {

	global $export;

	$output = '';
	$term_taxonomy = 'product_shipping_class';
	$types = wp_get_object_terms( $product_id, $term_taxonomy );
	if( empty( $types ) )
		$types = get_term_by( 'name', 'simple', $term_taxonomy );
	if( !empty( $types ) ) {
		$size = count( $types );
		for( $i = 0; $i < $size; $i++ ) {
			$type = get_term( $types[$i]->term_id, $term_taxonomy );
			if( is_wp_error( $type ) !== true )
				$output .= $type->name . $export->category_separator;
		}
		$output = substr( $output, 0, -1 );
	}
	return $output;

}

// Returns the Up-Sell associated to a specific Product
function woo_ce_get_product_assoc_upsell_ids( $product_id = 0 ) {

	global $export;

	$output = '';
	if( $product_id ) {
		$upsell_ids = get_post_meta( $product_id, '_upsell_ids', true );
		// Convert Product ID to Product SKU as per Up-Sells Formatting
		if( $export->upsell_formatting == 1 && !empty( $upsell_ids ) ) {
			$size = count( $upsell_ids );
			for( $i = 0; $i < $size; $i++ ) {
				$upsell_ids[$i] = get_post_meta( $upsell_ids[$i], '_sku', true );
				if( empty( $upsell_ids[$i] ) )
					unset( $upsell_ids[$i] );
			}
			// 'reindex' array
			$upsell_ids = array_values( $upsell_ids );
		}
		$output = woo_ce_convert_product_ids( $upsell_ids );
	}
	return $output;

}

// Returns the Cross-Sell associated to a specific Product
function woo_ce_get_product_assoc_crosssell_ids( $product_id = 0 ) {

	global $export;

	$output = '';
	if( $product_id ) {
		$crosssell_ids = get_post_meta( $product_id, '_crosssell_ids', true );
		// Convert Product ID to Product SKU as per Cross-Sells Formatting
		if( $export->crosssell_formatting == 1 && !empty( $crosssell_ids ) ) {
			$size = count( $crosssell_ids );
			for( $i = 0; $i < $size; $i++ ) {
				$crosssell_ids[$i] = get_post_meta( $crosssell_ids[$i], '_sku', true );
				// Remove Cross-Sell if SKU is empty
				if( empty( $crosssell_ids[$i] ) )
					unset( $crosssell_ids[$i] );
			}
			// 'reindex' array
			$crosssell_ids = array_values( $crosssell_ids );
		}
		$output = woo_ce_convert_product_ids( $crosssell_ids );
	}
	return $output;
	
}

// Returns Product Attributes associated to a specific Product
function woo_ce_get_product_assoc_attributes( $product_id = 0, $attribute = array(), $type = 'product' ) {

	global $export;

	$output = '';
	if( $product_id ) {
		$terms = array();
		if( $type == 'product' ) {
			if( $attribute['is_taxonomy'] == 1 )
				$term_taxonomy = $attribute['name'];
		} else if( $type == 'global' ) {
			$term_taxonomy = sprintf( 'pa_%s', $attribute->attribute_name );
		}
		$terms = wp_get_object_terms( $product_id, $term_taxonomy );
		if( !empty( $terms ) && is_wp_error( $terms ) == false ) {
			$size = count( $terms );
			for( $i = 0; $i < $size; $i++ )
				$output .= $terms[$i]->name . $export->category_separator;
			unset( $terms );
		}
		$output = substr( $output, 0, -1 );
	}
	return $output;

}

// Returns File Downloads associated to a specific Product
function woo_ce_get_product_assoc_download_files( $product_id = 0, $type = 'url' ) {

	global $export;

	$output = '';
	if( $product_id ) {
		if( version_compare( WOOCOMMERCE_VERSION, '2.0', '>=' ) ) {
			// If WooCommerce 2.0+ is installed then use new _downloadable_files Post meta key
			if( $file_downloads = maybe_unserialize( get_post_meta( $product_id, '_downloadable_files', true ) ) ) {
				foreach( $file_downloads as $file_download ) {
					if( $type == 'url' )
						$output .= $file_download['file'] . $export->category_separator;
					else if( $type == 'name' )
						$output .= $file_download['name'] . $export->category_separator;
				}
				unset( $file_download, $file_downloads );
			}
			$output = substr( $output, 0, -1 );
		} else {
			// If WooCommerce -2.0 is installed then use legacy _file_paths Post meta key
			if( $file_downloads = maybe_unserialize( get_post_meta( $product_id, '_file_paths', true ) ) ) {
				foreach( $file_downloads as $file_download ) {
					if( $type == 'url' )
						$output .= $file_download . $export->category_separator;
				}
				unset( $file_download, $file_downloads );
			}
			$output = substr( $output, 0, -1 );
		}
	}
	return $output;

}

function woo_ce_get_product_assoc_order_ids( $products = array() ) {

	// Save database processing
	if( count( $products ) == 0 )
		return;

	global $wpdb;

	$output = false;
	// $order_ids_sql = "SELECT `order_id` FROM `" . $wpdb->prefix . "woocommerce_order_items` as order_items, `" . $wpdb->prefix . "woocommerce_order_itemmeta` as order_itemmeta WHERE `order_items`.order_item_id = `order_itemmeta`.order_item_id AND `order_itemmeta`.meta_key IN ( '_product_id', '_variation_id' ) AND `order_itemmeta`.meta_value IN ( " . implode( ',', $products ) . " )";
	$order_ids_sql = "SELECT `order_id` FROM `" . $wpdb->prefix . "woocommerce_order_items` as order_items, `" . $wpdb->prefix . "woocommerce_order_itemmeta` as order_itemmeta WHERE `order_items`.order_item_id = `order_itemmeta`.order_item_id AND `order_itemmeta`.meta_key = '_product_id' AND `order_itemmeta`.meta_value IN ( " . implode( ',', $products ) . " )";
	$order_ids = $wpdb->get_col( $order_ids_sql );
	$wpdb->flush();
	if( !empty( $order_ids ) ) {
		$output = $order_ids;
		unset( $order_ids );
	}
	return $output;

}

// Returns list of Product Add-on columns
function woo_ce_get_product_addons() {

	// Product Add-ons - http://www.woothemes.com/
	if( class_exists( 'Product_Addon_Admin' ) || class_exists( 'Product_Addon_Display' ) ) {
		$post_type = 'global_product_addon';
		$args = array(
			'post_type' => $post_type,
			'numberposts' => -1
		);
		$output = array();

		// First grab the Global Product Add-ons
		if( $product_addons = get_posts( $args ) ) {
			foreach( $product_addons as $product_addon ) {
				if( $meta = maybe_unserialize( get_post_meta( $product_addon->ID, '_product_addons', true ) ) ) {
					$size = count( $meta );
					for( $i = 0; $i < $size; $i++ ) {
						$output[] = (object)array(
							'post_name' => $meta[$i]['name'],
							'post_title' => $meta[$i]['name'],
							'form_title' => sprintf( __( 'Global Product Add-on: %s', 'woocommerce-exporter' ), $product_addon->post_title )
						);
					}
					unset( $size );
				}
				unset( $meta );
			}
		}

		// Custom Product Add-ons
		$custom_product_addons = woo_ce_get_option( 'custom_product_addons', '' );
		if( !empty( $custom_product_addons ) ) {
			foreach( $custom_product_addons as $custom_product_addon ) {
				if( !empty( $custom_product_addon ) ) {
					$output[] = (object)array(
						'post_name' => $custom_product_addon,
						'post_title' => woo_ce_clean_export_label( $custom_product_addon ),
						'form_title' => sprintf( __( 'Custom Product Add-on: %s', 'woocommerce-exporter' ), $custom_product_addon )
					);
				}
			}
		}
		unset( $custom_product_addons, $custom_product_addon );

		if( !empty( $output ) )
			return $output;
	}

}

function woo_ce_get_product_tabs() {

	$post_type = 'wc_product_tab';
	$args = array(
		'post_type' => $post_type,
		'post_status' => 'publish',
		'posts_per_page' => -1
	);
	$product_tabs = new WP_Query( $args );
	if( !empty( $product_tabs->posts ) ) {
		return $product_tabs->posts;
	}

}

function woo_ce_format_product_visibility( $visibility = '' ) {

	$output = '';
	if( !empty( $visibility ) ) {
		switch( $visibility ) {

			case 'visible':
				$output = __( 'Catalog & Search', 'woocommerce-exporter' );
				break;

			case 'catalog':
				$output = __( 'Catalog', 'woocommerce-exporter' );
				break;

			case 'search':
				$output = __( 'Search', 'woocommerce-exporter' );
				break;

			case 'hidden':
				$output = __( 'Hidden', 'woocommerce-exporter' );
				break;

		}
	}
	return $output;

}

function woo_ce_format_product_download_type( $download_type = '' ) {

	$output = __( 'Standard', 'woocommerce-exporter' );
	if( !empty( $download_type ) ) {
		switch( $download_type ) {

			case 'application':
				$output = __( 'Application', 'woocommerce-exporter' );
				break;

			case 'music':
				$output = __( 'Music', 'woocommerce-exporter' );
				break;

		}
	}
	return $output;

}

function woo_ce_format_gpf_availability( $availability = null ) {

	$output = '';
	if( !empty( $availability ) ) {
		switch( $availability ) {

			case 'in stock':
				$output = __( 'In Stock', 'woocommerce-exporter' );
				break;

			case 'available for order':
				$output = __( 'Available For Order', 'woocommerce-exporter' );
				break;

			case 'preorder':
				$output = __( 'Pre-order', 'woocommerce-exporter' );
				break;

		}
	}
	return $output;

}

function woo_ce_format_gpf_condition( $condition ) {

	$output = '';
	if( !empty( $condition ) ) {
		switch( $condition ) {

			case 'new':
				$output = __( 'New', 'woocommerce-exporter' );
				break;

			case 'refurbished':
				$output = __( 'Refurbished', 'woocommerce-exporter' );
				break;

			case 'used':
				$output = __( 'Used', 'woocommerce-exporter' );
				break;

		}
	}
	return $output;

}

function woo_ce_format_product_stock_status( $stock_status = '', $stock = '' ) {

	$output = '';
	if( empty( $stock_status ) && !empty( $stock ) ) {
		if( $stock )
			$stock_status = 'instock';
		else
			$stock_status = 'outofstock';
	}
	if( $stock_status ) {
		switch( $stock_status ) {

			case 'instock':
				$output = __( 'In Stock', 'woocommerce-exporter' );
				break;

			case 'outofstock':
				$output = __( 'Out of Stock', 'woocommerce-exporter' );
				break;

		}
	}
	return $output;

}

function woo_ce_format_product_tax_status( $tax_status = null ) {

	$output = '';
	if( !empty( $tax_status ) ) {
		switch( $tax_status ) {
	
			case 'taxable':
				$output = __( 'Taxable', 'woocommerce-exporter' );
				break;
	
			case 'shipping':
				$output = __( 'Shipping Only', 'woocommerce-exporter' );
				break;

			case 'none':
				$output = __( 'None', 'woocommerce-exporter' );
				break;

		}
	}
	return $output;

}

function woo_ce_format_product_tax_class( $tax_class = '' ) {

	global $export;

	$output = '';
	if( $tax_class ) {
		switch( $tax_class ) {

			case '*':
				$tax_class = __( 'Standard', 'woocommerce-exporter' );
				break;

			case 'reduced-rate':
				$tax_class = __( 'Reduced Rate', 'woocommerce-exporter' );
				break;

			case 'zero-rate':
				$tax_class = __( 'Zero Rate', 'woocommerce-exporter' );
				break;

		}
		$output = $tax_class;
	}
	return $output;

}

function woo_ce_format_product_type( $type_id = '' ) {

	$output = $type_id;
	if( $output ) {
		$product_types = apply_filters( 'woo_ce_format_product_types', array(
			'simple' => __( 'Simple Product', 'woocommerce' ),
			'downloadable' => __( 'Downloadable', 'woocommerce' ),
			'grouped' => __( 'Grouped Product', 'woocommerce' ),
			'virtual' => __( 'Virtual', 'woocommerce' ),
			'variable' => __( 'Variable', 'woocommerce' ),
			'external' => __( 'External/Affiliate Product', 'woocommerce' ),
			'variation' => __( 'Variation', 'woocommerce-exporter' ),
			'subscription' => __( 'Simple Subscription', 'woocommerce-exporter' ),
			'variable-subscription' => __( 'Variable Subscription', 'woocommerce-exporter' )
		) );
		if( isset( $product_types[$type_id] ) )
			$output = $product_types[$type_id];
	}
	return $output;

}

// Returns a list of WooCommerce Product Types to export process
function woo_ce_get_product_types() {

	$term_taxonomy = 'product_type';
	$args = array(
		'hide_empty' => 0
	);
	$types = get_terms( $term_taxonomy, $args );
	if( !empty( $types ) && is_wp_error( $types ) == false ) {
		$output = array();
		$size = count( $types );
		for( $i = 0; $i < $size; $i++ ) {
			$output[$types[$i]->slug] = array(
				'name' => ucfirst( $types[$i]->name ),
				'count' => $types[$i]->count
			);
			// Override the Product Type count for Downloadable and Virtual
			if( in_array( $types[$i]->slug, array( 'downloadable', 'virtual' ) ) ) {
				if( $types[$i]->slug == 'downloadable' ) {
					$args = array(
						'meta_key' => '_downloadable',
						'meta_value' => 'yes'
					);
				} else if( $types[$i]->slug == 'virtual' ) {
					$args = array(
						'meta_key' => '_virtual',
						'meta_value' => 'yes'
					);
				}
				$output[$types[$i]->slug]['count'] = woo_ce_get_product_type_count( 'product', $args );
			}
		}
		$output['variation'] = array(
			'name' => __( 'variation', 'woocommerce-exporter' ),
			'count' => woo_ce_get_product_type_count( 'product_variation' )
		);
		asort( $output );
		return $output;
	}

}

function woo_ce_get_product_type_count( $post_type = 'product', $args = array() ) {

	$defaults = array(
		'post_type' => $post_type,
		'posts_per_page' => 1,
		'fields' => 'ids'
	);
	$args = wp_parse_args( $args, $defaults );
	$product_ids = new WP_Query( $args );
	$size = $product_ids->found_posts;
	return $size;

}

// Returns a list of WooCommerce Product Attributes to export process
function woo_ce_get_product_attributes() {

	global $wpdb;

	$output = array();
	$attributes_sql = "SELECT * FROM `" . $wpdb->prefix . "woocommerce_attribute_taxonomies`";
	$attributes = $wpdb->get_results( $attributes_sql );
	$wpdb->flush();
	if( !empty( $attributes ) ) {
		// Splice in our custom Attributes
		$custom_attributes = woo_ce_get_option( 'custom_attributes', '' );
		if( !empty( $custom_attributes ) ) {
			foreach( $custom_attributes as $custom_attribute ) {
				if( !empty( $custom_attribute ) ) {
					$attributes[] = (object)array(
						'attribute_id' => 0,
						'attribute_name' => remove_accents( $custom_attribute ),
						'attribute_label' => $custom_attribute,
						'attribute_type' => 'select',
						'attribute_orderby' => 'menu_order',
						'attribute_public' => 0
					);
				}
			}
			unset( $custom_attributes, $custom_attribute );
		}
		$output = $attributes;
		unset( $attributes );
	} else {
		$output = ( function_exists( 'wc_get_attribute_taxonomies' ) ? wc_get_attribute_taxonomies() : array() );
	}
	return $output;

}

function woo_ce_get_acf_product_fields() {

	global $wpdb;

	$post_type = 'acf';
	$args = array(
		'post_type' => $post_type,
		'numberposts' => -1
	);
	if( $field_groups = get_posts( $args ) ) {
		$fields = array();
		$post_types = array( 'product', 'product_variation' );
		foreach( $field_groups as $field_group ) {
			$has_fields = false;
			if( $rules = get_post_meta( $field_group->ID, 'rule' ) ) {
				$size = count( $rules );
				for( $i = 0; $i < $size; $i++ ) {
					if( ( $rules[$i]['param'] == 'post_type' ) && ( $rules[$i]['operator'] == '==' ) && ( in_array( $rules[$i]['value'], $post_types ) ) ) {
						$has_fields = true;
						$i = $size;
					}
				}
			}
			unset( $rules );
			if( $has_fields ) {
				$custom_fields_sql = "SELECT `meta_value` FROM `" . $wpdb->postmeta . "` WHERE `post_id` = " . absint( $field_group->ID ) . " AND `meta_key` LIKE 'field_%'";
				if( $custom_fields = $wpdb->get_col( $custom_fields_sql ) ) {
					foreach( $custom_fields as $custom_field ) {
						$custom_field = maybe_unserialize( $custom_field );
						$fields[] = array(
							'name' => $custom_field['name'],
							'label' => $custom_field['label']
						);
					}
				}
				unset( $custom_fields, $custom_field );
			}
		}
		return $fields;
	}

}

function woo_ce_get_product_assoc_brands( $product_id = 0, $parent_id = 0 ) {

	global $export;

	$output = '';
	$term_taxonomy = apply_filters( 'woo_ce_brand_term_taxonomy', 'product_brand' );
	// Return Product Brands of Parent if this is a Variation
	if( $parent_id )
		$product_id = $parent_id;
	if( $product_id )
		$brands = wp_get_object_terms( $product_id, $term_taxonomy );
	if( !empty( $brands ) && is_wp_error( $brands ) == false ) {
		$size = count( $brands );
		for( $i = 0; $i < $size; $i++ ) {
			if( $brands[$i]->parent == '0' ) {
				$output .= $brands[$i]->name . $export->category_separator;
			} else {
				// Check if Parent -> Child
				$parent_brand = get_term( $brands[$i]->parent, $term_taxonomy );
				// Check if Parent -> Child -> Subchild
				if( $parent_brand->parent == '0' ) {
					$output .= $parent_brand->name . '>' . $brands[$i]->name . $export->category_separator;
					$output = str_replace( $parent_brand->name . $export->category_separator, '', $output );
				} else {
					$root_brand = get_term( $parent_brand->parent, $term_taxonomy );
					$output .= $root_brand->name . '>' . $parent_brand->name . '>' . $brands[$i]->name . $export->category_separator;
					$output = str_replace( array(
						$root_brand->name . '>' . $parent_brand->name . $export->category_separator,
						$parent_brand->name . $export->category_separator
					), '', $output );
				}
				unset( $root_brand, $parent_brand );
			}
		}
		$output = substr( $output, 0, -1 );
	}
	return $output;

}

function woo_ce_extend_product_item( $product, $product_id ) {

	global $export;

	// Advanced Google Product Feed - http://plugins.leewillis.co.uk/downloads/wp-e-commerce-product-feeds/
	if( function_exists( 'woocommerce_gpf_install' ) ) {
		$product->gpf_data = get_post_meta( $product_id, '_woocommerce_gpf_data', true );
		$product->gpf_availability = ( isset( $product->gpf_data['availability'] ) ? woo_ce_format_gpf_availability( $product->gpf_data['availability'] ) : '' );
		$product->gpf_condition = ( isset( $product->gpf_data['condition'] ) ? woo_ce_format_gpf_condition( $product->gpf_data['condition'] ) : '' );
		$product->gpf_brand = ( isset( $product->gpf_data['brand'] ) ? $product->gpf_data['brand'] : '' );
		$product->gpf_product_type = ( isset( $product->gpf_data['product_type'] ) ? $product->gpf_data['product_type'] : '' );
		$product->gpf_google_product_category = ( isset( $product->gpf_data['google_product_category'] ) ? $product->gpf_data['google_product_category'] : '' );
		$product->gpf_gtin = ( isset( $product->gpf_data['gtin'] ) ? $product->gpf_data['gtin'] : '' );
		$product->gpf_mpn = ( isset( $product->gpf_data['mpn'] ) ? $product->gpf_data['mpn'] : '' );
		$product->gpf_gender = ( isset( $product->gpf_data['gender'] ) ? $product->gpf_data['gender'] : '' );
		$product->gpf_age_group = ( isset( $product->gpf_data['age_group'] ) ? $product->gpf_data['age_group'] : '' );
		$product->gpf_color = ( isset( $product->gpf_data['color'] ) ? $product->gpf_data['color'] : '' );
		$product->gpf_size = ( isset( $product->gpf_data['size'] ) ? $product->gpf_data['size'] : '' );
	}

	// All in One SEO Pack - http://wordpress.org/extend/plugins/all-in-one-seo-pack/
	if( function_exists( 'aioseop_activate' ) ) {
		$product->aioseop_keywords = get_post_meta( $product_id, '_aioseop_keywords', true );
		$product->aioseop_description = get_post_meta( $product_id, '_aioseop_description', true );
		$product->aioseop_title = get_post_meta( $product_id, '_aioseop_title', true );
		$product->aioseop_title_attributes = get_post_meta( $product_id, '_aioseop_titleatr', true );
		$product->aioseop_menu_label = get_post_meta( $product_id, '_aioseop_menulabel', true );
	}

	// WordPress SEO - http://wordpress.org/plugins/wordpress-seo/
	if( function_exists( 'wpseo_admin_init' ) ) {
		$product->wpseo_focuskw = get_post_meta( $product_id, '_yoast_wpseo_focuskw', true );
		$product->wpseo_metadesc = get_post_meta( $product_id, '_yoast_wpseo_metadesc', true );
		$product->wpseo_title = get_post_meta( $product_id, '_yoast_wpseo_title', true );
		$product->wpseo_noindex = woo_ce_format_wpseo_noindex( get_post_meta( $product_id, '_yoast_wpseo_meta-robots-noindex', true ) );
		$product->wpseo_follow = woo_ce_format_wpseo_follow( get_post_meta( $product_id, '_yoast_wpseo_meta-robots-nofollow', true ) );
		$product->wpseo_googleplus_description = get_post_meta( $product_id, '_yoast_wpseo_google-plus-description', true );
		$product->wpseo_opengraph_title = get_post_meta( $product_id, '_yoast_wpseo_opengraph-title', true );
		$product->wpseo_opengraph_description = get_post_meta( $product_id, '_yoast_wpseo_opengraph-description', true );
		$product->wpseo_opengraph_image = get_post_meta( $product_id, '_yoast_wpseo_opengraph-image', true );
		$product->wpseo_twitter_title = get_post_meta( $product_id, '_yoast_wpseo_twitter-title', true );
		$product->wpseo_twitter_description = get_post_meta( $product_id, '_yoast_wpseo_twitter-description', true );
		$product->wpseo_twitter_image = get_post_meta( $product_id, '_yoast_wpseo_twitter-image', true );
	}

	// Ultimate SEO - http://wordpress.org/plugins/seo-ultimate/
	if( function_exists( 'su_wp_incompat_notice' ) ) {
		$product->useo_meta_title = get_post_meta( $product_id, '_su_title', true );
		$product->useo_meta_description = get_post_meta( $product_id, '_su_description', true );
		$product->useo_meta_keywords = get_post_meta( $product_id, '_su_keywords', true );
		$product->useo_social_title = get_post_meta( $product_id, '_su_og_title', true );
		$product->useo_social_description = get_post_meta( $product_id, '_su_og_description', true );
		$product->useo_meta_noindex = get_post_meta( $product_id, '_su_meta_robots_noindex', true );
		$product->useo_meta_noautolinks = get_post_meta( $product_id, '_su_disable_autolinks', true );
	}

	// WooCommerce MSRP Pricing - http://woothemes.com/woocommerce/
	if( function_exists( 'woocommerce_msrp_activate' ) ) {
		$product->msrp = get_post_meta( $product_id, '_msrp_price', true );
		if( $product->msrp == false && $product->post_type == 'product_variation' )
			$product->msrp = get_post_meta( $product_id, '_msrp', true );
		// Check that a valid price has been provided
		if( isset( $product->msrp ) && $product->msrp != '' )
			$product->msrp = woo_ce_format_price( $product->msrp );
	}

	// WooCommerce Brands Addon - http://woothemes.com/woocommerce/
	// WooCommerce Brands - http://proword.net/Woocommerce_Brands/
	if( woo_ce_detect_product_brands() )
		$product->brands = woo_ce_get_product_assoc_brands( $product_id, $product->parent_id );

	// Cost of Goods - http://www.skyverge.com/product/woocommerce-cost-of-goods-tracking/
	if( class_exists( 'WC_COG' ) ) {
		$product->cost_of_goods = get_post_meta( $product_id, '_wc_cog_cost', true );
		// Check if this is a Variation and the Cost of Goods is empty
		if( $product->post_type == 'product_variation' && $product->cost_of_goods == '' )
			$product->cost_of_goods = get_post_meta( $product->parent_id, '_wc_cog_cost_variable', true );
		if( isset( $product->cost_of_goods ) && $product->cost_of_goods != '' )
			$product->cost_of_goods = woo_ce_format_price( $product->cost_of_goods );
	}

	// Per-Product Shipping - http://www.woothemes.com/products/per-product-shipping/
	if( function_exists( 'woocommerce_per_product_shipping_init' ) )
		$product->per_product_shipping = get_post_meta( $product_id, '_per_product_shipping', true );

	// Product Vendors - http://www.woothemes.com/products/product-vendors/
	if( class_exists( 'WooCommerce_Product_Vendors' ) ) {
		$product->vendors = woo_ce_get_product_assoc_product_vendors( $product_id, $product->parent_id );
		$product->vendor_ids = woo_ce_get_product_assoc_product_vendors( $product_id, $product->parent_id, 'term_id' );
		$product->vendor_commission = woo_ce_get_product_assoc_product_vendor_commission( $product_id, $product->vendor_ids );
	}

	// WC Vendors - http://wcvendors.com
	if( class_exists( 'WC_Vendors' ) ) {
		$product->vendor = ( !empty( $product->post_author ) ? woo_ce_get_username( $product->post_author ) : false );
		$product->vendor_commission_rate = get_post_meta( $product_id, 'pv_commission_rate', true );
	}

	// WooCommerce Wholesale Pricing - http://ignitewoo.com/woocommerce-extensions-plugins-themes/woocommerce-wholesale-pricing/
	if( class_exists( 'woocommerce_wholesale_pricing' ) ) {
		$product->wholesale_price = woo_ce_format_price( get_post_meta( $product_id, 'wholesale_price', true ) );
		$product->wholesale_price_text = get_post_meta( $product_id, 'wholesale_price_text', true );
	}

	// WooCommerce Custom Fields - http://www.rightpress.net/woocommerce-custom-fields
	if( class_exists( 'RP_WCCF' ) ) {
		$custom_fields = get_post_meta( $product_id, '_wccf_product_admin', true );
		if( !empty( $custom_fields ) ) {
			foreach( $custom_fields as $custom_field ) {
				$product->{sanitize_key( $custom_field['key'] )} = ( isset( $custom_field['value'] ) ? $custom_field['value'] : '' );
			}
		}
		unset( $custom_fields, $custom_field );
	}

	// WooCommerce Subscriptions - http://www.woothemes.com/products/woocommerce-subscriptions/
	if( class_exists( 'WC_Subscriptions_Manager' ) ) {
		$product->subscription_price = get_post_meta( $product_id, '_subscription_price', true );
		$product->subscription_period_interval = woo_ce_format_product_subscription_period_interval( get_post_meta( $product_id, '_subscription_period_interval', true ) );
		$product->subscription_period = get_post_meta( $product_id, '_subscription_period', true );
		$product->subscription_length = woo_ce_format_product_subscripion_length( get_post_meta( $product_id, '_subscription_length', true ), $product->subscription_period );
		$product->subscription_sign_up_fee = get_post_meta( $product_id, '_subscription_sign_up_fee', true );
		$product->subscription_trial_length = get_post_meta( $product_id, '_subscription_trial_length', true );
		$product->subscription_trial_period = get_post_meta( $product_id, '_subscription_trial_period', true );
		$product->subscription_limit = woo_ce_format_product_subscription_limit( get_post_meta( $product_id, '_subscription_limit', true ) );
	}

	// WooCommerce Bookings - http://www.woothemes.com/products/woocommerce-bookings/
	if( class_exists( 'WC_Bookings' ) ) {
		$product->booking_has_persons = get_post_meta( $product_id, '_wc_booking_has_persons', true );
		$product->booking_has_resources = get_post_meta( $product_id, '_wc_booking_has_resources', true );
		$product->booking_base_cost = get_post_meta( $product_id, '_wc_booking_cost', true );
		$product->booking_block_cost = get_post_meta( $product_id, '_wc_booking_base_cost', true );
		$product->booking_display_cost = get_post_meta( $product_id, '_wc_display_cost', true );
		$product->booking_requires_confirmation = get_post_meta( $product_id, '_wc_booking_requires_confirmation', true );
		$product->booking_user_can_cancel = get_post_meta( $product_id, '_wc_booking_user_can_cancel', true );
	}

	// Barcodes for WooCommerce - http://www.wolkenkraft.com/produkte/barcodes-fuer-woocommerce/
	if( function_exists( 'wpps_requirements_met' ) ) {
		// Cannot clean up the barcode type as the developer has not exposed any functions or methods
		$product->barcode_type = get_post_meta( $product_id, '_barcode_type', true );
		$product->barcode = get_post_meta( $product_id, '_barcode', true );
	}

	// WooCommerce Pre-Orders - http://www.woothemes.com/products/woocommerce-pre-orders/
	if( class_exists( 'WC_Pre_Orders' ) ) {
		$product->pre_orders_enabled = woo_ce_format_switch( get_post_meta( $product_id, '_wc_pre_orders_enabled', true ) );
		$product->pre_orders_availability_date = woo_ce_format_product_sale_price_dates( get_post_meta( $product_id, '_wc_pre_orders_availability_datetime', true ) );
		$product->pre_orders_fee = woo_ce_format_price( get_post_meta( $product_id, '_wc_pre_orders_fee', true ) );
		$product->pre_orders_charge = woo_ce_format_pre_orders_charge( get_post_meta( $product_id, '_wc_pre_orders_when_to_charge', true ) );
	}

	// WooCommerce Product Fees - https://wordpress.org/plugins/woocommerce-product-fees/
	if( class_exists( 'WooCommerce_Product_Fees' ) ) {
		$product->fee_name = get_post_meta( $product_id, 'product-fee-name', true );
		$product->fee_amount = get_post_meta( $product_id, 'product-fee-amount', true );
		$product->fee_multiplier = woo_ce_format_switch( get_post_meta( $product_id, 'product-fee-multiplier', true ) );
	}

	// WooCommerce Events - http://www.woocommerceevents.com/
	if( class_exists( 'WooCommerce_Events' ) ) {
		$product->is_event = woo_ce_format_events_is_event( get_post_meta( $product_id, 'WooCommerceEventsEvent', true ) );
		$product->event_date = get_post_meta( $product_id, 'WooCommerceEventsDate', true );
		$event_hour = absint( get_post_meta( $product_id, 'WooCommerceEventsHour', true ) );
		$event_minutes = absint( get_post_meta( $product_id, 'WooCommerceEventsMinutes', true ) );
		if( !empty( $event_hour ) || !empty( $event_minutes ) )
			$product->event_start_time = sprintf( '%d:%s', $event_hour, $event_minutes );
		unset( $event_hour, $event_minutes );
		$event_hour = absint( get_post_meta( $product_id, 'WooCommerceEventsHourEnd', true ) );
		$event_minutes = absint( get_post_meta( $product_id, 'WooCommerceEventsMinutesEnd', true ) );
		if( !empty( $event_hour ) || !empty( $event_minutes ) )
			$product->event_end_time = sprintf( '%d:%s', $event_hour, $event_minutes );
		unset( $event_hour, $event_minutes );
		$product->event_venue = get_post_meta( $product_id, 'WooCommerceEventsLocation', true );
		$product->event_gps = get_post_meta( $product_id, 'WooCommerceEventsGPS', true );
		$product->event_googlemaps = get_post_meta( $product_id, 'WooCommerceEventsGoogleMaps', true );
		$product->event_directions = get_post_meta( $product_id, 'WooCommerceEventsDirections', true );
		$product->event_phone = get_post_meta( $product_id, 'WooCommerceEventsSupportContact', true );
		$product->event_email = get_post_meta( $product_id, 'WooCommerceEventsEmail', true );
		$product->event_ticket_logo = get_post_meta( $product_id, 'WooCommerceEventsTicketLogo', true );
		$product->event_ticket_text = get_post_meta( $product_id, 'WooCommerceEventsTicketText', true );
	}

	// WooCommerce Uploads - https://wpfortune.com/shop/plugins/woocommerce-uploads/
	if( class_exists( 'WPF_Uploads' ) ) {
		$product->enable_uploads = woo_ce_format_switch( get_post_meta( $product_id, '_wpf_umf_upload_enable', true ) );
	}

/*
	// @mod - waiting for information from users
	// WooCommerce Variation Swatches and Photos - https://www.woothemes.com/products/variation-swatches-and-photos/
	if( class_exists( 'WC_SwatchesPlugin' ) ) {
		$colours = get_post_meta( $product_id, '_swatch_type_options', true );
		unset( $colours );
	}
*/

	// WooCommerce Tab Manager - http://www.woothemes.com/products/woocommerce-tab-manager/
	if( class_exists( 'WC_Tab_Manager' ) ) {
		$tabs = get_post_meta( $product_id, '_product_tabs', true );
		if( !empty( $tabs ) ) {
			foreach( $tabs as $tab ) {
				$product->{'product_tab_' . sanitize_key( $tab['name'] ) } = get_post_field( 'post_content', $tab['id'] );
			}
		}
	}

	// Custom Product meta
	$custom_products = woo_ce_get_option( 'custom_products', '' );
	if( !empty( $custom_products ) ) {
		foreach( $custom_products as $custom_product ) {
			if( !empty( $custom_product ) ) {
				$product->{$custom_product} = woo_ce_format_custom_meta( get_post_meta( $product_id, $custom_product, true ) );
			}
		}
	}

	if( $export->gallery_unique ) {
		$max_size = woo_ce_get_option( 'max_product_gallery', 3 );
		if( !empty( $product->product_gallery ) ) {
			// Tack on a extra digit to max_size so we get the correct number of columns
			$max_size++;
			$product_gallery = explode( $export->category_separator, $product->product_gallery );
			$size = count( $product_gallery );
			for( $i = 1; $i < $size; $i++ ) {
				if( $i == $max_size )
					break;
				$product->{'product_gallery_' . $i} = $product_gallery[$i];
			}
			$product->product_gallery = $product_gallery[0];
			unset( $product_gallery );
		}
	}

	return $product;

}
add_filter( 'woo_ce_product_item', 'woo_ce_extend_product_item', 10, 2 );

function woo_ce_format_product_sale_price_dates( $sale_date = '' ) {

	$output = $sale_date;
	if( $sale_date )
		$output = woo_ce_format_date( date( 'Y-m-d H:i:s', $sale_date ) );
	return $output;

}

function woo_ce_format_pre_orders_charge( $charge = '' ) {

	$output = $charge;
	if( !empty( $charge ) ) {
		switch( $charge ) {

			case 'upon_release':
				$output = __( 'Upon Release', 'woocommerce-exporter' );
				break;

			case 'upfront':
				$output = __( 'Upfront', 'woocommerce-exporter' );
				break;

		}
	}
	return $output;

}

function woo_ce_format_events_is_event( $is_event = '' ) {

	$is_event = strtolower( $is_event );
	switch( $is_event ) {

		case 'event':
			$output = __( 'Yes', 'woocommerce-exporter' );
			break;

		default:
		case 'notevent':
			$output = __( 'No', 'woocommerce-exporter' );
			break;

	}
	return $output;

}

function woo_ce_get_export_type_attribute_count() {

	$count = 0;
	$attributes = ( function_exists( 'wc_get_attribute_taxonomies' ) ? wc_get_attribute_taxonomies() : array() );
	$count = count( $attributes );
	return $count;

}

// Returns a list of Attribute export columns
function woo_ce_get_attribute_fields( $format = 'full' ) {

	$export_type = 'attribute';

	$fields = array();
	$fields[] = array(
		'name' => 'attribute',
		'label' => __( 'Attribute', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'term_id',
		'label' => __( 'Term ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'term_name',
		'label' => __( 'Term Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'term_slug',
		'label' => __( 'Term Slug', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'term_parent',
		'label' => __( 'Term Parent', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'term_description',
		'label' => __( 'Term Description', 'woocommerce-exporter' )
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

	if( $remember = woo_ce_get_option( $export_type . '_fields', array() ) ) {
		$remember = maybe_unserialize( $remember );
		$size = count( $fields );
		for( $i = 0; $i < $size; $i++ ) {
			$fields[$i]['disabled'] = ( isset( $fields[$i]['disabled'] ) ? $fields[$i]['disabled'] : 0 );
			$fields[$i]['default'] = 1;
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
			usort( $fields, woo_ce_sort_fields( 'order' ) );
			return $fields;
			break;

	}

}

function woo_ce_unique_product_gallery_fields( $fields = array() ) {

	$max_size = woo_ce_get_option( 'max_product_gallery', 3 );
	if( !empty( $fields ) ) {
		// Tack on a extra digit to max_size so we get the correct number of columns
		$max_size++;
		for( $i = 1; $i < $max_size; $i++ ) {
			if( isset( $fields['product_gallery'] ) )
				$fields[sprintf( 'product_gallery_%d', $i )] = 'on';
		}
	}
	return $fields;

}

function woo_ce_unique_product_gallery_columns( $columns = array(), $fields = array() ) {

	$max_size = woo_ce_get_option( 'max_product_gallery', 3 );
	if( !empty( $columns ) ) {
		// Tack on a extra digit to max_size so we get the correct number of columns
		$max_size++;
		for( $i = 1; $i < $max_size; $i++ ) {
			if( isset( $fields[sprintf( 'product_gallery_%d', $i )] ) )
				$columns[] = sprintf( apply_filters( 'woo_ce_unique_product_gallery_column', __( '%s #%d', 'woocommerce-exporter' ) ), woo_ce_get_product_field( 'product_gallery' ), $i );
		}
	}
	return $columns;

}

// Returns the export column header label based on an export column slug
function woo_ce_get_attribute_field( $name = null, $format = 'name' ) {

	$output = '';
	if( $name ) {
		$fields = woo_ce_get_attribute_fields();
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

/*
function woo_ce_get_attributes( $args = array() ) {

}
*/
?>