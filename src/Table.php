<?php
declare( strict_types = 1 );

namespace Dinamiko\Dsubscribers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Table {

	/**
	 * The instance of this class.
	 *
	 * @var null
	 */
	private static $instance = null;

	/**
	 * Table constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_dsubscribers_menu_page' ) );

		add_action( 'init', array( $this, 'dsubscribers_update' ) );
		add_action( 'init', array( $this, 'dsubscribers_delete' ) );

		add_action( 'init', array( $this, 'dsubscribers_export' ) );
	}

	/**
	 * Registers DSubscribers menu page.
	 *
	 * @return void
	 */
	public function register_dsubscribers_menu_page() {

		add_menu_page( 'DSubscribers', 'DSubscribers', 'manage_options', 'dsubscribers', array( $this, 'dsubscribers_menu_page' ), 'dashicons-groups' );
	}

	/**
	 * Add DSubscribers menu page.
	 *
	 * @return void
	 */
	public function dsubscribers_menu_page() {
		?>
		<div class="wrap">

		<h2 style="position:relative;width:100%;float:left;margin-bottom:15px;">DSubscribers

			<a style="position:absolute;top:10px;right:15px;" class="button-primary" href="admin.php?page=dsubscribers&action=export">Export (.csv)</a>

		</h2>


		<?php
        // phpcs:disable WordPress.Security.NonceVerification.Recommended
		$dsubscribers = sanitize_text_field( wp_unslash( $_GET['dsubscribers'] ?? '' ) );
		$action       = sanitize_text_field( wp_unslash( $_GET['action'] ?? '' ) );
        // phpcs:enable
		if ( $dsubscribers && $action === 'edit' ) {

			$id = intval( $dsubscribers );

			global $wpdb;
			$table_name = $wpdb->prefix . 'dsubscribers';

            // phpcs:disable WordPress.DB.DirectDatabaseQuery
            // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id=%d", $id ) );
            // phpcs:enable
			?>

			<form id="dsubscribers-form" method="post">

			<label><?php esc_html__( 'Email', 'dsubscribers' ); ?>:</label>
			<input type="text" name="email" id="email" value="<?php echo esc_attr( $row->email ); ?>" />

			<div style="float:left; width:100%;margin-top:20px;">
				<input type="hidden" name="dsubscribers_id" value="<?php echo esc_attr( $row->id ); ?>" />
				<input type="submit" class="button-primary" value="Save"></input>
			</div>

			</form>

		<?php } ?>

		<?php

		$wp_list_table = new ListTable();

		$search_term = sanitize_text_field( wp_unslash( $_POST['s'] ?? '' ) );
		if ( $search_term ) {

			$wp_list_table->prepare_items( $search_term );

		} else {

			$wp_list_table->prepare_items();

		}

		?>



		<form method="post">

			<input type="hidden" name="page" value="
			<?php
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			echo esc_attr( sanitize_text_field( wp_unslash( $_REQUEST['page'] ?? '' ) ) );
			?>
			" />

			<?php $wp_list_table->search_box( 'Search', 'dsubscribers-id' ); ?>

		</form>

		<?php $wp_list_table->display(); ?>

		</div>

		<?php
	}

	/**
	 * Updates subscriber in the database.
	 *
	 * @return void
	 */
	public function dsubscribers_update() {
		$dsubscribers_id = sanitize_text_field( wp_unslash( $_POST['dsubscribers_id'] ?? '' ) );
		if ( ! $dsubscribers_id ) {
			return;
		}

		$id    = intval( $dsubscribers_id );
		$email = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );

		global $wpdb;
		$table_name = $wpdb->prefix . 'dsubscribers';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->update(
			$table_name,
			array( 'email' => $email ),
			array( 'ID' => $id ),
			array( '%s' ),
			array( '%d' )
		);

		$paged = ! empty( $_GET['paged'] ) ? sanitize_text_field( wp_unslash( $_GET['paged'] ) ) : '';
		header( "Location:admin.php?page=dsubscribers&paged=$paged" );
	}

	/**
	 * Deletes subscriber from the database.
	 *
	 * @return void
	 */
	public function dsubscribers_delete() {

		$dsubscribers = sanitize_text_field( wp_unslash( $_GET['dsubscribers'] ?? '' ) );
		$action       = sanitize_text_field( wp_unslash( $_GET['action'] ?? '' ) );
		if ( $dsubscribers && $action === 'delete' ) {

			global $wpdb;
			$id         = intval( $dsubscribers );
			$table_name = $wpdb->prefix . 'dsubscribers';

            // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->delete( $table_name, array( 'ID' => $id ), array( '%d' ) );

			$paged = sanitize_text_field( wp_unslash( $_GET['paged'] ?? '' ) );
			header( "Location:admin.php?page=dsubscribers&paged=$paged" );

		}
	}

	/**
	 *  Export database data to .csv file
	 *  based on: https://wordpress.org/plugins/export-users-to-csv/
	 *
	 * @return void
	 */
	public function dsubscribers_export() {

		$action = sanitize_text_field( wp_unslash( $_GET['action'] ?? '' ) );
		if ( $action === 'export' ) {

			$filename = 'dsubscribers-' . gmdate( 'Y-m-d' ) . '.csv';

			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			header( 'Content-Type: text/csv; charset=' . get_option( 'blog_charset' ), true );

			echo 'email' . "\n";

			global $wpdb;
			$table_name = $wpdb->prefix . 'dsubscribers';

            // phpcs:disable WordPress.DB.DirectDatabaseQuery
            // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$emails = $wpdb->get_results( "SELECT * FROM $table_name" );
            // phpcs:enable

			foreach ( $emails as $email ) {

				echo esc_html( $email->email . "\n" );

			}

			exit;

		}
	}

	/**
	 * Returns a unique instance of this class.
	 *
	 * @return self|null
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) {

			self::$instance = new self();

		}

		return self::$instance;
	}
}
