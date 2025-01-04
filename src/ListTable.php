<?php
declare( strict_types=1 );

namespace Dinamiko\Dsubscribers;

use Dinamiko\Dsubscribers\Api\SubscriberRepository;
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
		return [
			'email'   => __( 'E-mail', 'dsubscribers' ),
			'time'    => __( 'Created', 'dsubscribers' ),
			'actions' => __( 'Actions', 'dsubscribers' ),
		];
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * @param string $search The search term.
	 *
	 * @return void
	 */
	public function prepare_items( string $search = '' ): void {
		$repository = new SubscriberRepository();

		$total_items = count( $repository->subscribers() );
		$per_page    = 2;
		$args        = [];
		$paged       = sanitize_text_field( wp_unslash( $_GET['paged'] ?? 1 ) );

		if ( $paged ) {
			$offset        = ( $paged - 1 ) * $per_page;
			$args['limit'] = (int) $offset . ',' . $per_page;
		}

		$items = $repository->subscribers( $args, $search );

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'total_pages' => ceil( $total_items / $per_page ),
				'per_page'    => $per_page,
			)
		);

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->items = $items;
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
}
