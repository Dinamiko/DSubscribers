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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Initializes the plugin.
 *
 * @return void
 */
function init(): void {
	if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
		include_once __DIR__ . '/vendor/autoload.php';
	}

	DSubscribers::instance( __FILE__, '1.2.2' );
	Settings::instance( __FILE__ );
	Table::instance();

	add_action(
		'wp_enqueue_scripts',
		function () {
			$scripts_handle                   = require __DIR__ . '/build/frontend.asset.php';
			$scripts_handle['dependencies'][] = 'jquery';

			wp_register_script(
				'dsubscribers-frontend-js',
				plugins_url( '/build/frontend.js', __FILE__ ),
				$scripts_handle['dependencies'],
				$scripts_handle['version'],
				true
			);
			wp_enqueue_script( 'dsubscribers-frontend-js' );

			wp_localize_script(
				'dsubscribers-frontend-js',
				'dsubscribers_data',
				array(
					'ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ),
				)
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

	add_action('admin_menu', function () {
		add_submenu_page(
			'dsubscribers',
			'Settings',
			'Settings',
			'manage_options',
			'dsubscribers-settings',
			function() {
				echo '<div id="react-settings-page"></div>';
			}
		);
	});

	add_action( 'admin_enqueue_scripts', function($page) {
		if($page !== 'dsubscribers_page_dsubscribers-settings') {
			return;
		}

		$asset_file = require __DIR__ . '/build/admin.asset.php';

		wp_register_script(
			'dsubscribers-settings',
			plugins_url( '/build/admin.js', __FILE__ ),
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);

		wp_enqueue_script( 'dsubscribers-settings' );
		wp_enqueue_style( 'wp-components' );
	});

	add_action('dsubscribers_subscribed', function(string $email) {
		if ( get_option( 'dsubscribers_send_checkbox' ) === 'on' ) {
			$subject = 'The subject';
			$message = get_option( 'dsubscribers_message_block' );
			$headers = 'From: ' . get_bloginfo( 'name' ) . ' <' . get_bloginfo( 'admin_email' ) . '>';

			wp_mail( $email, $subject, $message, $headers );
		}
	});

	function dsubscribers_register_settings() {
		$default = array(
			'send_checkbox' => false,
			'subscribed_msg' => __( 'Thank you for subscribing!', 'dsubscribers' ),
			'exists_msg' => __( 'Sorry, this e-mail already exists', 'dsubscribers' ),
			'unsubscribed_msg' => __( 'Sorry, this e-mail already exists', 'dsubscribers' ),
			'dont_exists_msg' => __( 'Sorry, subscriber do not exists', 'dsubscribers' ),
		);

		$schema  = array(
			'type'       => 'object',
			'properties' => array(
				'send_checkbox' => array(
					'type' => 'boolean',
				),
				'subscribed_msg' => array(
					'type' => 'string',
				),
				'exists_msg' => array(
					'type' => 'string',
				),
				'unsubscribed_msg' => array(
					'type' => 'string',
				),
				'dont_exists_msg' => array(
					'type' => 'string',
				),
			),
		);

		register_setting(
			'dsubscribers',
			'dsubscribers_options',
			array(
				'type'         => 'object',
				'default'      => $default,
				'show_in_rest' => array(
					'schema' => $schema,
				),
			)
		);
	}

	add_action( 'init', __NAMESPACE__ . '\\dsubscribers_register_settings' );
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


