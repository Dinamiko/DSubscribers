<?php
declare( strict_types = 1 );

namespace Dinamiko\Dsubscribers\Api;

use Exception;
use wpdb;

class SubscriberRepository {

	/**
	 * Subscribe user with the given email.
	 *
	 * @param string $email User email.
	 * @return void
	 * @throws Exception If it could not subscribe user.
	 */
	public function subscribe( string $email ): void {
		global $wpdb;
		$table_name = $wpdb->prefix . 'dsubscribers';

		if ( $this->email_exist( $email ) ) {
			throw new Exception( 'Email already exists.' );
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->insert(
			$table_name,
			array(
				'email' => $email,
				'time'  => gmdate( 'Y-m-d h:i:s', time() ),
			),
			array(
				'%s',
				'%s',
			)
		);

		do_action( 'dsubscribers_subscribed', $email );
	}

	/**
	 * Unsubscribe user with the given email.
	 *
	 * @param string $email User email.
	 * @return void
	 * @throws Exception If it could not unsubscribe user.
	 */
	public function unsubscribe( string $email ): void {
		if ( ! $this->email_exist( $email ) ) {
			throw new Exception( 'Email doesn\'t exists.' );
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'dsubscribers';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->delete( $table_name, array( 'email' => $email ) );
	}

	/**
	 * Returns a subscriber entry from the given email.
	 *
	 * @param string $email User email.
	 * @return array|null
	 */
	public function subscriber( string $email ): ?array {
		global $wpdb;
		$table_name = $wpdb->prefix . 'dsubscribers';

		// phpcs:disable WordPress.DB.DirectDatabaseQuery
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $table_name WHERE email = %s",
				sanitize_email( $email )
			),
			ARRAY_A
		);
		// phpcs:enable
	}

	/**
	 * Check whether email exist in the database.
	 *
	 * @param string $user_email User email.
	 * @return bool
	 */
	private function email_exist( string $user_email ): bool {
		global $wpdb;
		$table_name = $wpdb->prefix . 'dsubscribers';

		// phpcs:disable WordPress.DB.DirectDatabaseQuery
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return (bool) $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $table_name WHERE email = %s",
				sanitize_email( $user_email )
			)
		);
		// phpcs:enable
	}
}
