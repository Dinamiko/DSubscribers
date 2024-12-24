<?php
/*
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
 */

namespace Dinamiko\Dsubscribers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function init() {
	if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
		include_once __DIR__ . '/vendor/autoload.php';
	}

	$instance = DSubscribers::instance( __FILE__, '1.2.1' );
	Settings::instance( $instance );
	Table::instance( $instance );
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\\init' );
