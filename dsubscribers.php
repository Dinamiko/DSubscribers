<?php
/**
 * Plugin Name: DSubscribers
 * Version: 1.2.2
 * Description: Manage subscribers from your site with ease
 * Author: Emili Castells
 * Author URI: https://dinamiko.dev
 * Requires at least: 3.9
 * Tested up to: 6.7
 * Text Domain: dsubscribers
 * Domain Path: /languages
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package Dinamiko\Dsubscribers
 */

declare( strict_types = 1 );

namespace Dinamiko\Dsubscribers;

use Dinamiko\Dsubscribers\Admin\Subscribers;
use Dinamiko\Dsubscribers\Frontend\Assets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
	include_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Initializes the plugin.
 *
 * @return void
 */
function init(): void {
	DSubscribers::instance( __FILE__, '1.2.2' );
	Settings::instance( __FILE__ );
	Table::instance();

	(new Subscribers())->init();
	(new \Dinamiko\Dsubscribers\Admin\Settings())->init();
	(new Assets())->init();
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\\init' );

register_activation_hook( __FILE__, function () {
	update_option( 'dsubscribers_version', '1.2.2' );

	global $wpdb;
	global $jal_db_version;

	$table_name = $wpdb->prefix . 'dsubscribers';

	$sql = "CREATE TABLE $table_name (
			  		id mediumint(9) NOT NULL AUTO_INCREMENT,
			  		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			  		email VARCHAR(200) DEFAULT '' NOT NULL,
					UNIQUE KEY id (id)
				);";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );

	add_option( 'jal_db_version', $jal_db_version );
});


