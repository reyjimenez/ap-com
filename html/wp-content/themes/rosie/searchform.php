<form action="<?php echo home_url(); ?>/" method="get" class="wp-search-form">
	<i class="oic-zoom"></i>
    <input type="text" name="s" id="search" placeholder="<?php echo get_search_query() == '' ? __('Type and hit Enter', 'vp_textdomain') : get_search_query() ?>" />
</form>