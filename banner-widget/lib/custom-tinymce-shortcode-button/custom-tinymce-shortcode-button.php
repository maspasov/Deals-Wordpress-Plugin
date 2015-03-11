<?php

function get_button_options(){

	$stscb_btn_img = plugin_dir_url(__FILE__).'image.png';
	$sbscb_popup = plugin_dir_url(__FILE__).'popup.php';

	echo '<script type="text/javascript">'."\n";
	echo "var sc_popup = '". $sbscb_popup ."'\n";
	echo "var sc_img = '". $stscb_btn_img ."'\n";
	echo '</script>'."\n";

	echo '<style type="text/css">'."\n";
	echo ' .mce-toolbar .mce-btn-group .mce-btn-deals i {width:30px}'."\n";
	echo '</style>'."\n";
}

if( preg_match("/(post-new|post)\.php/", basename(getenv('SCRIPT_FILENAME'))) ) {
	add_filter('admin_head', 'get_button_options');
}

add_action( 'init', 'offers_buttons' );
function offers_buttons() {
    add_filter( "mce_external_plugins", "offers_add_buttons" );
    add_filter( 'mce_buttons', 'offers_register_buttons' );
}

function offers_add_buttons( $plugin_array ) {
    $plugin_array['offers'] = plugin_dir_url(__FILE__) . '/stscb.js';
    return $plugin_array;
}

function offers_register_buttons( $buttons ) {
    array_push( $buttons, 'showoffers' );
    return $buttons;
}

?>