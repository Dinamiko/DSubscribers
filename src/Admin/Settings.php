<?php
declare( strict_types = 1 );

namespace Dinamiko\Dsubscribers\Admin;

class Settings {
	public function init() {
		add_action( 'admin_menu', function () {
			add_submenu_page(
				'dsubscribers',
				'Settings',
				'Settings',
				'manage_options',
				'dsubscribers-settings',
				function () {
					echo '<div class="wrap" id="dsubscribers-settings"></div>';
				}
			);
		} );

		add_action( 'admin_enqueue_scripts', function ( $page ) {
			if ( $page !== 'dsubscribers_page_dsubscribers-settings' ) {
				return;
			}

			$asset_file = require dirname(realpath(__FILE__), 3) . '/build/admin.asset.php';

			wp_register_script(
				'dsubscribers-settings',
				plugins_url( '/build/admin.js', dirname( realpath( __FILE__ ), 2 ) ),
				$asset_file['dependencies'],
				$asset_file['version'],
				true
			);

			wp_enqueue_script( 'dsubscribers-settings' );
			wp_enqueue_style( 'wp-components' );

			$scripts_handle_css = require dirname(realpath(__FILE__), 3) . '/build/admin-css.asset.php';

			wp_register_style(
				'dsubscribers-admin-css',
				plugins_url( '/build/admin-css.css', dirname( realpath( __FILE__ ), 2 ) ),
				$scripts_handle_css['dependencies'],
				$scripts_handle_css['version']
			);
			wp_enqueue_style( 'dsubscribers-admin-css' );
		} );

		add_action( 'dsubscribers_subscribed', function ( string $email ) {
			if ( get_option( 'dsubscribers_send_checkbox' ) === 'on' ) {
				$subject = 'The subject';
				$message = get_option( 'dsubscribers_message_block' );
				$headers = 'From: ' . get_bloginfo( 'name' ) . ' <' . get_bloginfo( 'admin_email' ) . '>';

				wp_mail( $email, $subject, $message, $headers );
			}
		} );

		add_action( 'init', function () {
			$default = array(
				'send_email_checkbox' => false,
				'email_msg'           => __( 'Thank you for subscribing!', 'dsubscribers' ),
				'subscribed_msg'      => __( 'Thank you for subscribing!', 'dsubscribers' ),
				'exists_msg'          => __( 'Sorry, this e-mail already exists', 'dsubscribers' ),
				'unsubscribed_msg'    => __( 'Unsubscribed correctly', 'dsubscribers' ),
				'dont_exists_msg'     => __( 'Sorry, subscriber do not exists', 'dsubscribers' ),
			);

			$schema = array(
				'type'       => 'object',
				'properties' => array(
					'send_email_checkbox' => array(
						'type' => 'boolean',
					),
					'email_msg'           => array(
						'type' => 'string',
					),
					'subscribed_msg'      => array(
						'type' => 'string',
					),
					'exists_msg'          => array(
						'type' => 'string',
					),
					'unsubscribed_msg'    => array(
						'type' => 'string',
					),
					'dont_exists_msg'     => array(
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
		} );
	}
}
