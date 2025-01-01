<?php
declare( strict_types = 1 );

namespace Dinamiko\Dsubscribers;

use WP_List_Table;
use wpdb;

class ListTable extends WP_List_Table {

	/**
	 * ListTable constructor.
	 */
	public function __construct() {

		parent::__construct(
			array(
				'singular' => 'dsubscriber',
				'plural'   => 'dsubscribers',
				'ajax'     => false,
			)
		);
	}

	/**
	 * Gets a list of columns.
	 *
	 * @return array
	 */
	public function get_columns(): array {

		$columns = array(
			'email'   => __( 'E-mail', 'dsubscribers' ),
			'time'    => __( 'Created', 'dsubscribers' ),
			'actions' => __( 'Actions', 'dsubscribers' ),
		);

		return $columns;
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * @param string $search The search term.
	 * @return void
	 */
	public function prepare_items( string $search = '' ): void {
		global $wpdb;
		$table_name = $wpdb->prefix . 'dsubscribers';

		if ( $search ) {
			$query = $wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT * FROM $table_name WHERE email=%s",
				sanitize_text_field( wp_unslash( $search ) )
			);

			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			$orderby = ! empty( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : '';
			$order   = ! empty( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : 'ASC';
			// phpcs:enable

			if ( ! empty( $orderby ) && ! empty( $order ) ) {
				$query .= $wpdb->prepare( ' ORDER BY %s %s', $orderby, $order );
			}

			$query = $this->set_pagination( $wpdb, $query );

			// phpcs:disable WordPress.DB.DirectDatabaseQuery
			// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
			$this->items = $wpdb->get_results( $query );
			// phpcs:enable

		} else {
			$query = "SELECT * FROM $table_name ORDER BY id DESC";

			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			$orderby = ! empty( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : '';
			$order   = ! empty( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : 'ASC';
			// phpcs:enable

			if ( ! empty( $orderby ) & ! empty( $order ) ) {
				$query .= $wpdb->prepare( ' ORDER BY %s %s', $orderby, $order );
			}

			$query = $this->set_pagination( $wpdb, $query );

			// phpcs:disable WordPress.DB.DirectDatabaseQuery
			// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
			$this->items = $wpdb->get_results( $query );
			// phpcs:enable
		}
	}

	/**
	 * Generates the list table rows.
	 *
	 * @return void
	 */
	public function display_rows(): void {

		$records = $this->items;

		list( $columns, $hidden ) = $this->get_column_info();

		if ( ! empty( $records ) ) {
			foreach ( $records as $rec ) {

				echo wp_kses_post( '<tr id="record_' . $rec->id . '">' );

				foreach ( $columns as $column_name => $column_display_name ) {

					$class = "class='$column_name column-$column_name'";
					$style = '';

					if ( in_array( $column_name, $hidden, true ) ) {
						$style = ' style="display:none;"';
					}
					$attributes = $class . $style;

					// phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$paged = ! empty( $_GET['paged'] ) ? sanitize_text_field( wp_unslash( $_GET['paged'] ) ) : '';

					$editlink = sprintf(
						'<a href="?page=%s&action=%s&dsubscribers=%s&paged=%s">' . esc_html__( 'Edit', 'dsubscribers' ) . '</a>',
						// phpcs:ignore WordPress.Security.NonceVerification.Recommended
						sanitize_text_field( wp_unslash( $_REQUEST['page'] ?? '' ) ),
						'edit',
						(int) $rec->id,
						$paged
					);

					$deletelink = sprintf(
						'<a style="color:#ac0000;" href="?page=%s&action=delete&dsubscribers=%s&paged=%s&nonce=%s">' . esc_html__( 'Delete', 'dsubscribers' ) . '</a>',
						// phpcs:ignore WordPress.Security.NonceVerification.Recommended
						sanitize_text_field( wp_unslash( $_REQUEST['page'] ?? '' ) ),
						(int) $rec->id,
						$paged,
						wp_create_nonce( 'dsubscribers_delete_' . $rec->id )
					);

					// echo wp_nonce_url( $url, 'action' );

					switch ( $column_name ) {

						case 'email':
							echo wp_kses_post( '<td ' . $attributes . '>' . stripslashes( $rec->email ) . '</td>' );
							break;

						case 'time':
							echo wp_kses_post( '<td ' . $attributes . '>' . stripslashes( $rec->time ) . '</td>' );
							break;

						case 'actions':
							echo wp_kses_post( '<td ' . $attributes . '><strong>' . $editlink . '</strong> | <strong>' . $deletelink . '</strong></td>' );
							break;

					}
				}

				echo '</tr>';

			}
		}
	}

	/**
	 * Set Pagination for the given query.
	 *
	 * @param wpdb   $wpdb WordPress database class.
	 * @param string $query Database query.
	 *
	 * @return string|null
	 */
	private function set_pagination( wpdb $wpdb, string $query ): ?string {
		// phpcs:disable WordPress.DB.DirectDatabaseQuery
		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
		$totalitems = $wpdb->query( $query );
		// phpcs:enable

		$perpage = 5;
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$paged = ! empty( $_GET['paged'] ) ? sanitize_text_field( wp_unslash( $_GET['paged'] ) ) : '';

		if ( empty( $paged ) || ! is_numeric( $paged ) || $paged <= 0 ) {
			$paged = 1;
		}

		$totalpages = ceil( $totalitems / $perpage );

		if ( ! empty( $paged ) && ! empty( $perpage ) ) {
			$offset = ( $paged - 1 ) * $perpage;
			$query .= ' LIMIT ' . (int) $offset . ',' . (int) $perpage;
		}

		$this->set_pagination_args(
			array(
				'total_items' => $totalitems,
				'total_pages' => $totalpages,
				'per_page'    => $perpage,
			)
		);

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		return $query;
	}
}
