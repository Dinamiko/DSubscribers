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

	add_action(
		'wp_enqueue_scripts',
		function () {
			$scripts_handle = require __DIR__ . '/build/frontend.asset.php';

			$scripts_handle['dependencies'][] = 'jquery';

			wp_register_script(
				'dsubscribers-frontend-js',
				plugins_url( '/build/frontend.js', __FILE__ ),
				$scripts_handle['dependencies'],
				$scripts_handle['version']
			);
			wp_enqueue_script( 'dsubscribers-frontend-js' );

			wp_localize_script(
				'dsubscribers-frontend-js',
				'dsubscribers_data',
				[
					'ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ),
				]
			);

			$scripts_handle_css = require __DIR__ . '/build/frontend-css.asset.php';

			wp_register_style(
				'dsubscribers-frontend-css',
				plugins_url( '/build/frontend-css.css', __FILE__ ),
				$scripts_handle_css['dependencies'],
				$scripts_handle_css['version']
			);
			wp_enqueue_style( 'dsubscribers-frontend-css' );
		}
	);

	$instance = DSubscribers::instance( __FILE__, '1.2.1' );
	Settings::instance( $instance );
	Table::instance( $instance );
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\\init' );
