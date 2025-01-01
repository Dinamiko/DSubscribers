<?php

namespace Dinamiko\Dsubscribers\Api;

class Subscriber {
	/**
	 * @param \wpdb $wpdb
	 * @param string $table_name
	 * @param $dsubscribers_email
	 *
	 * @return bool|int|\mysqli_result|null
	 */
	public function create( \wpdb $wpdb, string $table_name, $dsubscribers_email ) {
		$inserted = $wpdb->insert(
			$table_name,
			array(
				'email' => $dsubscribers_email,
				'time'  => gmdate( 'Y-m-d h:i:s', time() ),
			),
			array(
				'%s',
				'%s',
			)
		);

		return $inserted;
	}
}
