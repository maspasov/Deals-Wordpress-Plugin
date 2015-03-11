<?php 

add_action( 'init', 'banners_and_deals' );
function banners_and_deals() {
	$labels = array(
		'name' => _x('Banners &amp; Deals', 'Banner &amp; Deal General Name', 'fos_theme'),
		'singular_name' => _x('Banner &amp; Deal Item', 'Banner &amp; Deal Singular Name', 'fos_theme'),
		'add_new' => _x('Add New', 'Add New Banner &amp; Deal Name', 'fos_theme'),
		'add_new_item' => __('Banner &amp; Deal Name', 'fos_theme'),
		'edit_item' => __('Banner &amp; Deal Name', 'fos_theme'),
		'new_item' => __('New Banner &amp; Deal', 'fos_theme'),
		'view_item' =>__('View', 'fos_theme'),
		'search_items' => __('Search Banner &amp; Deal', 'fos_theme'),
		'not_found' =>  __('Nothing found', 'fos_theme'),
		'not_found_in_trash' => __('Nothing found in Trash', 'fos_theme'),
		'parent_item_colon' => ''
	);
	
	$args = array(
		'labels' => $labels,
		'public' => false,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => 6,
		"show_in_nav_menus" => false,
		'exclude_from_search' => true,
		'supports' => array('title','editor','thumbnail','excerpt'),
		'register_meta_box_cb' => 'add_banners_and_deals_metaboxes'
	); 
	  
	register_post_type( 'banners_and_deals' , $args);
}


add_action( 'add_meta_boxes', 'add_banners_and_deals_metaboxes' );
function add_banners_and_deals_metaboxes() {
	add_meta_box('banners_and_deals_destination', 'Enter Destination or Leave it Blank for a Deals select functionality:', 'banners_and_deals_destination', 'banners_and_deals', 'normal', 'high');
}

// The Banner and Deal Destination Metabox
function banners_and_deals_destination() {
	global $post;
	
	// Noncename needed to verify where the data originated
	echo '<input type="hidden" name="banners_and_deals_destination_noncename" id="banners_and_deals_destination_noncename" value="' . 
	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	
	// Get the destination data if its already been entered
	$destination = get_post_meta($post->ID, '_destination', true);
	
	// Echo out the field
	echo '<input type="text" name="_destination" value="' . $destination  . '" class="widefat" />';

}


// Save the Metabox Data
add_action('save_post', 'save_banners_and_deals_meta', 1, 2); // save the custom fields
function save_banners_and_deals_meta($post_id, $post) {
	
	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times
	if ( !wp_verify_nonce( $_POST['banners_and_deals_destination_noncename'], plugin_basename(__FILE__) )) {
	return $post->ID;
	}

	// Is the user allowed to edit the post or page?
	if ( !current_user_can( 'edit_post', $post->ID ))
		return $post->ID;

	// OK, we're authenticated: we need to find and save the data
	// We'll put it into an array to make it easier to loop though.
	$banners_and_deals_meta['_destination'] = $_POST['_destination'];
	
	// Add values of $banners_and_deals_meta as custom fields
	
	foreach ($banners_and_deals_meta as $key => $value) { // Cycle through the $banners_and_deals_meta array!
		if( $post->post_type == 'revision' ) return; // Don't store custom data twice
		$value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
		if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
			update_post_meta($post->ID, $key, $value);
		} else { // If the custom field doesn't have a value
			add_post_meta($post->ID, $key, $value);
		}
		if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
	}

}
