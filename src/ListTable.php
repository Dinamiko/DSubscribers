<?php
declare( strict_types = 1 );

namespace Dinamiko\Dsubscribers;

use WP_List_Table;

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
	public function prepare_items( $search = null ): void {
		global $wpdb;
		$table_name = $wpdb->prefix . 'dsubscribers';

		if ( $search !== null ) {
			$query = $wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT * FROM $table_name WHERE email=%s",
				sanitize_text_field( wp_unslash( $search ) )
			);

			$orderby = ! empty( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'ASC';
			$order   = ! empty( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : '';
			if ( ! empty( $orderby ) & ! empty( $order ) ) {
				$query .= ' ORDER BY ' . $orderby . ' ' . $order;
			}

			$totalitems = $wpdb->query( $query );
			$perpage    = 5;
			$paged      = ! empty( $_GET['paged'] ) ? sanitize_text_field( $_GET['paged'] ) : '';

			if ( empty( $paged ) || ! is_numeric( $paged ) || $paged <= 0 ) {
				$paged = 1; }

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

			$this->items = $wpdb->get_results( $query );

		} else {

			$query = "SELECT * FROM $table_name ORDER BY id DESC";

			$orderby = ! empty( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'ASC';
			$order   = ! empty( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : '';
			if ( ! empty( $orderby ) & ! empty( $order ) ) {
				$query .= ' ORDER BY ' . $orderby . ' ' . $order; }

			$totalitems = $wpdb->query( $query );
			$perpage    = 5;
			$paged      = ! empty( $_GET['paged'] ) ? sanitize_text_field( $_GET['paged'] ) : '';

			if ( empty( $paged ) || ! is_numeric( $paged ) || $paged <= 0 ) {
				$paged = 1; }

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

			$this->items = $wpdb->get_results( $query );

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

				echo '<tr id="record_' . $rec->id . '">';

				foreach ( $columns as $column_name => $column_display_name ) {

					$class = "class='$column_name column-$column_name'";
					$style = '';

					if ( in_array( $column_name, $hidden ) ) {
						$style = ' style="display:none;"';
					}
					$attributes = $class . $style;

					$paged      = ! empty( $_GET['paged'] ) ? sanitize_text_field( $_GET['paged'] ) : '';
					$editlink   = sprintf( '<a href="?page=%s&action=%s&dsubscribers=%s&paged=%s">' . __( 'Edit', 'dsubscribers' ) . '</a>', $_REQUEST['page'], 'edit', (int) $rec->id, $paged );
					$deletelink = sprintf( '<a style="color:#ac0000;" href="?page=%s&action=%s&dsubscribers=%s&paged=%s">' . __( 'Delete', 'dsubscribers' ) . '</a>', $_REQUEST['page'], 'delete', (int) $rec->id, $paged );

					switch ( $column_name ) {

						case 'email':
							echo '<td ' . $attributes . '>' . stripslashes( $rec->email ) . '</td>';
							break;

						case 'time':
							echo '<td ' . $attributes . '>' . stripslashes( $rec->time ) . '</td>';
							break;

						case 'actions':
							echo '<td ' . $attributes . '><strong>' . $editlink . '</strong> | <strong>' . $deletelink . '</strong></td>';
							break;

					}
				}

				echo '</tr>';

			}
		}
	}
}
