<?php
declare( strict_types=1 );

namespace Dinamiko\Dsubscribers\Api;

use Exception;

class SubscriberRepository {

	/**
	 * Subscribe user with the given email.
	 *
	 * @param string $email User email.
	 *
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
	 *
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
	 *
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
	 * Returns subscriber entries.
	 *
	 * @return array|null
	 */
	public function subscribers( array $args = [], string $email = '' ): ?array {
		global $wpdb;
		$table_name = $wpdb->prefix . 'dsubscribers';

		$query = "SELECT * FROM $table_name";

		if ( $email ) {
			$query .= $wpdb->prepare(
				" WHERE email=%s LIMIT 1",
				sanitize_email( $email )
			);

			return $wpdb->get_results( $query );
		}

		if ( $args['limit'] ?? '' ) {
			$query .= " LIMIT {$args['limit']}";
		}

		return $wpdb->get_results( $query );
	}

	/**
	 * Updates subscriber with the given email.
	 *
	 * @param string $email User email.
	 * @param string $new_email New email to update.
	 * @return void
	 */
	public function update( string $email, string $new_email ): void {
		global $wpdb;
		$table_name = $wpdb->prefix . 'dsubscribers';

		$wpdb->update(
			$table_name,
			[
				'email' => $new_email,
			],
			[
				'email' => $email,
			],
		);
	}

	/**
	 * Check whether email exist in the database.
	 *
	 * @param string $user_email User email.
	 *
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
