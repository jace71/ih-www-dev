<?php
/*
/*
Plugin Name: Contently
Description: This plugin integrates with Contently
Version: 1.1.7
Author: Contently
Author URI: http://www.contently.com
License: GPL2

*/

// Require Contently Library
require_once( __DIR__ . '/vendor/autoload.php' );

//$options_state = get_option('cl_options',array('install'=>false));
//if (!$options_state['install']){
//	echo 'Is start page';
//	die;
//}

$logger_state = get_option( 'cl_options', array( 'debug_state' => true ) );

$log          = \Contently\Log::instance( array(
	'file'       => plugin_dir_path( __FILE__ ) . '.log/contently.log',
	'table_name' => 'contently_log',
	'state'      => filter_var( $logger_state['debug_state'], FILTER_VALIDATE_BOOLEAN ),
	'driver'     => array( 'Filesystem', 'Wp_database' )
) );

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

register_activation_hook( __FILE__, array( 'WP_Contently', 'on_activation' ) );
register_deactivation_hook( __FILE__, array( 'WP_Contently', 'on_deactivation' ) );
add_action( 'init', array( 'WP_Contently', 'update_plugin_settings' ) );

require __DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'plugin-update-checker-master' . DIRECTORY_SEPARATOR . 'plugin-update-checker.php';
$update_checker = PucFactory::buildUpdateChecker(
	'http://integrations.contently.com/wordpress/release.json',
	__FILE__
);

/*
 * shortcuts in variables names
 * cl = contently
 * wp = wordpress
 * cf = custom field
 *
 * */

// require main class file
require_once __DIR__ . DIRECTORY_SEPARATOR . 'contently.php';

// Instantiate class
$WP_Contently = WP_Contently::getInstance();
