<?php
declare( strict_types=1 );

namespace Dinamiko\Dsubscribers\Frontend;

class Assets {
	public function init() {
		add_action(
			'wp_enqueue_scripts',
			function () {
				$scripts_handle                   = require dirname( realpath( __FILE__ ), 3 ) . '/build/frontend.asset.php';
				$scripts_handle['dependencies'][] = 'jquery';

				wp_register_script(
					'dsubscribers-frontend-js',
					plugins_url( '/build/frontend.js', dirname( realpath( __FILE__ ), 2 ) ),
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

				$scripts_handle_css = require dirname( realpath( __FILE__ ), 3 ) . '/build/frontend-css.asset.php';

				wp_register_style(
					'dsubscribers-frontend-css',
					plugins_url( '/build/frontend-css.css', dirname( realpath( __FILE__ ), 2 ) ),
					$scripts_handle_css['dependencies'],
					$scripts_handle_css['version']
				);
				wp_enqueue_style( 'dsubscribers-frontend-css' );
			}
		);
	}
}
