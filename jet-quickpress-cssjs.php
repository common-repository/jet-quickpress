<?php
function bpqp_admin_add_css_js() {
	wp_enqueue_script( 'common' );
	wp_enqueue_script( 'wp-lists' );
	wp_enqueue_script( 'postbox' );
	wp_enqueue_script( 'jquery.autocomplete', apply_filters('bp_quickpress_enqueue_url',get_stylesheet_directory_uri() . '/quickpress/_inc/js/tiny_mce.js'),array('jquery'), '1.0' );
}

add_action( 'admin_print_styles-buddypress_page_bp-quiockpress', 'bpqp_admin_add_css_js' );
?>