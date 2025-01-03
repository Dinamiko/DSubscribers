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

	/**
	 * Unsubscribe user with the given email.
	 *
	 * @param string $email
	 * @return void
	 */
	public function unsubscribe( string $email ): void {
		if ( ! $this->email_exist( $email ) ) {
			throw new Exception( 'Email doesn\'t exists.' );
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'dsubscribers';

		$wpdb->delete( $table_name, array( 'email' => $email ) );
	}

	/**
	 * Returns a subscriber entry from the given email.
	 *
	 * @param string $email
	 * @return array|null
	 */
	public function subscriber( string $email ): ?array {
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

		return (bool) $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM $table_name WHERE email = %s", $user_email )
		);
	}
}
