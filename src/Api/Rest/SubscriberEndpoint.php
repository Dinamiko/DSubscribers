<?php

namespace Dinamiko\Dsubscribers\Api\Rest;

use Dinamiko\Dsubscribers\Api\SubscriberRepository;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class SubscriberEndpoint {
	public function register() {
		add_action( 'rest_api_init', function () {
			register_rest_route( 'dsubscribers/v1', '/subscribers', [
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => function () {
					$repository  = new SubscriberRepository();
					$subscribers = $repository->subscribers();

					return new WP_REST_Response( $subscribers, 200 );
				},
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			] );

			register_rest_route( 'dsubscribers/v1', '/subscriber', [
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => function ( WP_REST_Request $request ) {
					$email     = sanitize_text_field( wp_unslash( $request['email'] ?? '' ) );
					$new_email = sanitize_text_field( wp_unslash( $request['new_email'] ?? '' ) );

					$repository = new SubscriberRepository();
					$repository->update( $email, $new_email );

					return new WP_REST_Response( null, 204 );
				},
				'permission_callback' => static function () {
					return current_user_can( 'manage_options' );
				},
			] );

			register_rest_route( 'dsubscribers/v1', '/subscriber/(?P<email>[A-Za-z0-9._+-]+\@[A-Za-z0-9]+\.[A-Za-z]{2,}+)', [
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => function ( WP_REST_Request $request ) {
					$email = sanitize_email( $request['email'] );

					$repository = new SubscriberRepository();
					$subscriber = $repository->subscriber( $email );

					return new WP_REST_Response( $subscriber, 200 );
				},
				'permission_callback' => static function () {
					return current_user_can( 'manage_options' );
				},
			] );

			register_rest_route( 'dsubscribers/v1', '/subscriber/(?P<email>[A-Za-z0-9._+-]+\@[A-Za-z0-9]+\.[A-Za-z]{2,}+)', [
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => function ( WP_REST_Request $request ) {
					$email = sanitize_email( $request['email'] );

					$repository = new SubscriberRepository();
					$repository->delete( $email );

					return new WP_REST_Response( null, 204 );
				},
				'permission_callback' => static function () {
					return current_user_can( 'manage_options' );
				},
			] );
		} );
	}
}
