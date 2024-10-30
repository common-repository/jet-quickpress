<?php
/*
Plugin Name: Jet Quickpress
Plugin URI: http://milordk.ru/r-lichnoe/opyt/cms/publikaciya-v-wordpress-minuyu-administrativnuyu-panel-jet-quickpress.html
Description: Allows the users to write simple posts outside the dashboard (just like QuickPress from the dashboard)
Version: 2.2.5
Revision Date: May 11, 2010
Requires at least: WPMU 2.9.2, BuddyPress 1.2.3
Tested up to: WP MU 3.0, BuddyPress 1.2.5
License: (GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html
Author: Jettochkin
Author URI: http://milordk.ru
Donate link: http://milordk.ru/uslugi.html
Site Wide Only: true
*/

/*** Make sure BuddyPress is loaded ********************************/
if ( !function_exists( 'bp_core_install' ) ) {
	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	if ( is_plugin_active( 'buddypress/bp-loader.php' ) )
		require_once ( WP_PLUGIN_DIR . '/buddypress/bp-loader.php' );
	else
		return;
}


function quickpress_load_textdomain() {
	$locale = apply_filters( 'bp_quickpress_locale', get_locale() );
	$mofile = WP_PLUGIN_DIR . "/jet-quickpress/$locale.mo";

	if ( file_exists( $mofile ) )
		load_textdomain( 'jet-quickpress-slug', $mofile );
}
add_action ( 'plugins_loaded', 'quickpress_load_textdomain', 9 );
/*******************************************************************/

function bp_quickpress_init() {
	define ( 'BP_QUICKPRESS_IS_INSTALLED', 1 );
	define ( 'BP_QUICKPRESS_VERSION', '1.0' );
	define ( 'BP_QUICKPRESS_PLUGIN_NAME', 'jet-quickpress' );
	define ( 'BP_QUICKPRESS_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . BP_QUICKPRESS_PLUGIN_NAME );
	define ( 'BP_QUICKPRESS_PLUGIN_URL', WP_PLUGIN_URL . '/' . BP_QUICKPRESS_PLUGIN_NAME );

	require_once 'jet-quickpress.php';
}

if ( defined( 'BP_VERSION' ) )
	bp_quickpress_init();
else
	add_action( 'bp_init', 'bp_quickpress_init' );


?>