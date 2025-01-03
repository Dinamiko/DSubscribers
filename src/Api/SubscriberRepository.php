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
	}

	public function subscriber( string $email ): array {
		global $wpdb;
		$table_name = $wpdb->prefix . 'dsubscribers';

		return $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM $table_name WHERE email = %s", $email ),
			ARRAY_A
		);
	}

	/**
	 * Check whether email exist in the database.
	 *
	 * @param wpdb   $wpdb WordPress database.
	 * @param string $table_name Database table name.
	 * @param string $user_email User email.
	 *
	 * @return bool
	 */
	private function email_exist( $user_email ): bool {
		global $wpdb;
		$table_name = $wpdb->prefix . 'dsubscribers';

		// phpcs:disable WordPress.DB.DirectDatabaseQuery
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$emails = $wpdb->get_results( "SELECT * FROM $table_name" );
		// phpcs:enable

		foreach ( $emails as $email ) {
			if ( $email->email === $user_email ) {
				return true;
			}
		}

		return false;
	}
}
