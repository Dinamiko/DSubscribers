<?php
declare( strict_types = 1 );

namespace Dinamiko\Dsubscribers\Admin\Tools;

use Dinamiko\Dsubscribers\Api\SubscriberRepository;

class Export {
	/**
	 * Export subscribers as .csv.
	 *
	 * @return void
	 */
	public function csv() {
		$repository = new SubscriberRepository();
		$subscribers = $repository->subscribers();

		$filename = 'dsubscribers-' . gmdate( 'Y-m-d' ) . '.csv';

		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Content-Type: text/csv; charset=' . get_option( 'blog_charset' ) );

		echo 'ID,Email,Created' . "\n";

		foreach ( $subscribers as $subscriber ) {
			echo esc_html( $subscriber->id . ',' . $subscriber->email . ',' . $subscriber->time . "\n" );
		}

		exit;
	}
}
