<?php
declare( strict_types = 1 );

namespace Dinamiko\Dsubscribers;

use Dinamiko\Dsubscribers\Api\Subscriber;

class DSubscribers {
	/**
	 * Plugin instance.
	 *
	 * @var null
	 */
	private static $instance = null;

	/**
	 * Plugin file.
	 *
	 * @var string
	 */
	public $file;

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Plugin name.
	 *
	 * @var string
	 */
	private $token;

	/**
	 * DSubscriber constructor.
	 *
	 * @param string $file Plugin file.
	 * @param string $version Plugin version.
	 */
	public function __construct( $file = '', $version = '1.0' ) {
		$this->file    = $file;
		$this->version = $version;
		$this->token   = 'dsubscribers';

		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );

		add_shortcode( 'dsubscribers', array( $this, 'dsubscribers_shortcode' ) );

		add_action( 'wp_ajax_dsubscribers_ajax', array( $this, 'dsubscribers_ajax' ) );
		add_action( 'wp_ajax_nopriv_dsubscribers_ajax', array( $this, 'dsubscribers_ajax' ) );

		add_action( 'widgets_init', array( $this, 'register_dsubscribers_widget' ) );
	}

	/**
	 * Registers the widget.
	 *
	 * @return void
	 */
	public function register_dsubscribers_widget(): void {
		$widget = new Widget();
		register_widget( $widget );
	}

	/**
	 * Load localization.
	 *
	 * @return void
	 */
	public function load_localisation(): void {
		load_plugin_textdomain( 'dsubscribers', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	}

	/**
	 * Load plugin text domain.
	 *
	 * @return void
	 */
	public function load_plugin_textdomain(): void {

		$domain = 'dsubscribers';
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	}

	/**
	 * Handles the subscribe ajax request.
	 *
	 * @return void
	 */
	public function dsubscribers_ajax() {
		$dsubscribers_nonce = sanitize_text_field( wp_unslash( $_POST['dsubscribers_nonce'] ?? null ) );
		if ( ! isset( $dsubscribers_nonce ) || ! wp_verify_nonce( $dsubscribers_nonce, 'dsubscribers_form_action' ) ) {

			wp_send_json_error( 'Security check' );

		}

		global $wpdb;
		$table_name          = $wpdb->prefix . 'dsubscribers';
		$dsubscribers_action = sanitize_text_field( wp_unslash( $_POST['dsubscribers_action'] ?? '' ) );
		$dsubscribers_email  = sanitize_email( wp_unslash( $_POST['dsubscribers_email'] ?? '' ) );

		if ( $dsubscribers_action === 'unsubscribe' ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$row = $wpdb->get_row(
				$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"SELECT * FROM $table_name WHERE email=%s",
					$dsubscribers_email
				)
			);

			if ( $row ) {
				$id = $row->id;

				// phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$wpdb->delete( $wpdb->prefix . 'dsubscribers', array( 'ID' => $id ) );

				$result['type'] = 'success';
				$result['msg']  = '<span class="dsubscribers_success">' . get_option( 'dsubscribers_unsubscribed_msg', 'Unsubscribed correctly' ) . '</span>';

			} else {
				$result['type'] = 'error';
				$result['msg']  = '<span class="dsubscribers_error">' . get_option( 'dsubscribers_dont_exists_msg', 'Sorry, subscriber doesn\'t exists' ) . '</span>';
			}

			wp_send_json_success( $result );
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$emails = $wpdb->get_results( "SELECT * FROM {$table_name}" );

		foreach ( $emails as $email ) {
			if ( $email->email === $dsubscribers_email ) {

				$result['type'] = 'error';
				$result['msg']  = '<span class="dsubscribers_error">' . get_option( 'dsubscribers_exists_msg', 'Sorry, this e-mail already exists' ) . '</span>';

				wp_send_json_success( $result );

			}
		}

		$subscriber = new Subscriber();

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$inserted = $subscriber->create( $wpdb, $table_name, $dsubscribers_email );

		if ( $inserted ) {
			if ( get_option( 'dsubscribers_send_checkbox' ) === 'on' ) {

				$subject = 'The subject';

				$message = get_option( 'dsubscribers_message_block' );

				$headers = 'From: ' . get_bloginfo( 'name' ) . ' <' . get_bloginfo( 'admin_email' ) . '>';

				wp_mail( $dsubscribers_email, $subject, $message, $headers );
			}

			$result['type'] = 'success';
			$result['msg']  = '<span class="dsubscribers_success">' . get_option( 'dsubscribers_subscribed_msg', 'Thank you for subscribing!' ) . '</span>';

			wp_send_json_success( $result );
		}
	}

	/**
	 * Returns shortcode content.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function dsubscribers_shortcode( $atts ): string {

		$a = shortcode_atts(
			array(

				'action' => 'subscribe',
				'type'   => '',

			),
			$atts
		);

		$content = '<div id="dsubscribers-container">';

			$content .= '<p id="dsubscribers_msg"></p>';

		if ( $a['type'] === 'widget' ) {

			$content .= '<p id="dsubscribers_msg_widget"></p>';

			$content .= '<form id="form-validation-widget" class="form-container" data-action="">';

				$content .= '<input id="dsubscribers_email" type="email" name="email" placeholder="E-mail" required>';

				$content .= '<input type="submit" value="SUBMIT">';

			switch ( $a['action'] ) {

				case 'unsubscribe':
					$content .= '<input type="hidden" id="dsubscribers_action" name="dsubscribers_action" value="unsubscribe" />';
					break;

				default:
					$content .= '<input type="hidden" id="dsubscribers_action" name="dsubscribers_action" value="action_form" />';
					break;

			}

				$content .= wp_nonce_field( 'dsubscribers_form_action', 'dsubscribers_form_nonce' );

				$content .= '</form>';

		} else {

			if ( $a['action'] === 'unsubscribe' ) {

				$content .= '<p id="dsubscribers_unsubscribe_msg"></p>';
				$content .= '<form id="form-validation-unsubscribe" class="form-container">';

			} else {

				$content .= '<form id="form-validation" class="form-container">';

			}

				$content .= '<input id="dsubscribers_email" type="email" name="email" placeholder="E-mail" required>';

				$content .= '<input type="submit" value="SUBMIT">';

			switch ( $a['action'] ) {

				case 'unsubscribe':
					$content .= '<input type="hidden" id="dsubscribers_action" name="dsubscribers_action" value="unsubscribe" />';
					break;

				default:
					$content .= '<input type="hidden" id="dsubscribers_action" name="dsubscribers_action" value="action_form" />';
					break;

			}

				$content .= wp_nonce_field( 'dsubscribers_form_action', 'dsubscribers_form_nonce' );

				$content .= '</form>';

		}

		$content .= '</div>';

		return $content;
	}

	/**
	 * Returns a unique instance of this class.
	 *
	 * @param string $file Plugin file.
	 * @param string $version Plugin version.
	 *
	 * @return self|null
	 */
	public static function instance( $file = '', $version = '1.0.0' ) {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self( $file, $version );
		}

		return self::$instance;
	}
}
