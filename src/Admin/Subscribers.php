<?php

namespace Dinamiko\Dsubscribers\Admin;

class Subscribers {
	public function init() {
		add_action( 'admin_menu', function () {
			add_submenu_page(
				'dsubscribers',
				'Subscribers',
				'Subscribers',
				'manage_options',
				'dsubscribers-subscribers',
				function () {
					echo '<div class="wrap" id="dsubscribers-subscribers"></div>';
				}
			);
		} );

		add_action( 'admin_enqueue_scripts', function ( $page ) {
			if ( $page !== 'dsubscribers_page_dsubscribers-subscribers' ) {
				return;
			}

			$asset_file = require dirname( realpath( __FILE__ ), 3 ) . '/build/subscribers.asset.php';

			wp_register_script(
				'dsubscribers-subscribers',
				plugins_url( '/build/subscribers.js', dirname( realpath( __FILE__ ), 2 ) ),
				$asset_file['dependencies'],
				$asset_file['version'],
				true
			);
			wp_enqueue_script( 'dsubscribers-subscribers' );

			$scripts_handle_css                   = require dirname( realpath( __FILE__ ), 3 ) . '/build/subscribers-css.asset.php';
			$scripts_handle_css['dependencies'][] = 'wp-components';

			wp_enqueue_style( 'dsubscribers-subscribers-css',
				plugins_url( '/build/style-subscribers.css', dirname( realpath( __FILE__ ), 2 ) ),
				$scripts_handle_css['dependencies'],
				$scripts_handle_css['version'] );

			wp_enqueue_style( 'dsubscribers-css',
				plugins_url( '/build/subscribers-css.css', dirname( realpath( __FILE__ ), 2 ) ),
				$scripts_handle_css['dependencies'],
				$scripts_handle_css['version'] );
		} );
	}
}
