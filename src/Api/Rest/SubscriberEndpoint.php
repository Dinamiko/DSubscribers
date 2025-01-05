<?php

namespace Dinamiko\Dsubscribers\Api\Rest;

use Dinamiko\Dsubscribers\Api\SubscriberRepository;
use WP_REST_Response;
use WP_REST_Server;

class SubscriberEndpoint {
	public function register() {
		add_action( 'rest_api_init', function () {
			register_rest_route( 'dsubscribers/v1', '/subscribers', array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => function () {
					$repository  = new SubscriberRepository();
					$subscribers = $repository->subscribers();

					return new WP_REST_Response( $subscribers, 200 );
				},
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			) );
		} );
	}
}
