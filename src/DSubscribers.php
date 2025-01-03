<?php
declare( strict_types = 1 );

namespace Dinamiko\Dsubscribers;

use Dinamiko\Dsubscribers\Api\SubscriberRepository;
use Exception;

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
	 * Handles ajax subscription request.
	 *
	 * @return void
	 */
	public function dsubscribers_ajax() {
		$dsubscribers_nonce = sanitize_text_field( wp_unslash( $_POST['dsubscribers_nonce'] ?? null ) );
		if ( ! isset( $dsubscribers_nonce ) || ! wp_verify_nonce( $dsubscribers_nonce, 'dsubscribers_form_action' ) ) {
			wp_send_json_error( 'Security check' );
		}

		$dsubscribers_action = sanitize_text_field( wp_unslash( $_POST['dsubscribers_action'] ?? '' ) );
		$dsubscribers_email  = sanitize_email( wp_unslash( $_POST['dsubscribers_email'] ?? '' ) );

		if ( $dsubscribers_action === 'unsubscribe' ) {
			try {
				$subscriber_repository = new SubscriberRepository();
				$subscriber_repository->unsubscribe( $dsubscribers_email );
			} catch ( Exception $exception ) {
				$result['type'] = 'error';
				$result['msg']  = '<span class="dsubscribers_error">' . get_option( 'dsubscribers_dont_exists_msg', 'Sorry, email doesn\'t exists' ) . '</span>';

				wp_send_json_error( $result );
			}

			$result['type'] = 'success';
			$result['msg']  = '<span class="dsubscribers_success">' . get_option( 'dsubscribers_unsubscribed_msg', 'Unsubscribed correctly' ) . '</span>';

			wp_send_json_success( $result );
		}

		try {
			$subscriber_repository = new SubscriberRepository();
			$subscriber_repository->subscribe( $dsubscribers_email );

			$result['type'] = 'success';
			$result['msg']  = '<span class="dsubscribers_success">' . get_option( 'dsubscribers_subscribed_msg', 'Thank you for subscribing!' ) . '</span>';

			wp_send_json_success( $result );
		} catch ( Exception $exception ) {
			$result['type'] = 'error';
			$result['msg']  = '<span class="dsubscribers_error">' . get_option( 'dsubscribers_exists_msg', 'Sorry, this e-mail already exists' ) . '</span>';

			wp_send_json_error( $result );
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
