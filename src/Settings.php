<?php
declare( strict_types = 1 );

namespace Dinamiko\Dsubscribers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings {
	/**
	 * Class instance.
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
	 * Plugin base name.
	 *
	 * @var string
	 */
	private $base;

	/**
	 * Plugin settings.
	 *
	 * @var array
	 */
	private $settings = array();

	/**
	 * Settings constructor.
	 *
	 * @param string $file Plugin file.
	 */
	public function __construct( string $file ) {

		$this->file = $file;
		$this->base = 'dsubscribers_';

		add_action( 'admin_init', array( $this, 'init_settings' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );

		add_filter( 'plugin_action_links_' . plugin_basename( $this->file ), array( $this, 'add_settings_link' ) );
	}

	/**
	 * Initialize settings.
	 *
	 * @return void
	 */
	public function init_settings(): void {

		$this->settings = $this->settings_fields();
	}

	/**
	 * Add menu item.
	 *
	 * @return void
	 */
	public function add_menu_item() {
		add_options_page(
			__( 'DSubscribers Settings', 'dsubscribers' ),
			__( 'DSubscribers Settings', 'dsubscribers' ),
			'manage_options',
			'dsubscribers_settings',
			array( $this, 'settings_page' )
		);
	}

	/**
	 * Add settings link in Plugins page.
	 *
	 * @param array $links List of links.
	 * @return array
	 */
	public function add_settings_link( $links ) {
		if ( ! is_array( $links ) ) {
			return $links;
		}

		$links[] = '<a href="options-general.php?page=dsubscribers_settings">' . __( 'Settings', 'dsubscribers' ) . '</a>';

		return $links;
	}

	/**
	 * Returns plugin settings fields.
	 *
	 * @return array
	 */
	private function settings_fields(): array {

		$settings['subscriber_email'] = array(

			'title'       => __( 'Subscriber E-mail', 'dsubscribers' ),
			'description' => '',
			'fields'      => array(

				array(
					'id'          => 'send_checkbox',
					'label'       => __( 'Send E-mail to subscriber', 'dsubscribers' ),
					'description' => '',
					'type'        => 'checkbox',
					'default'     => '',
				),

				array(
					'id'          => 'message_block',
					'label'       => __( 'Message', 'dsubscribers' ),
					'description' => __( 'This box accepts HTML tags', 'dsubscribers' ),
					'type'        => 'textarea',
					'default'     => 'Thank you for subscribing!',
					'placeholder' => '',
				),

			),
		);

		$settings['messages'] = array(

			'title'       => __( 'Form Messages', 'dsubscribers' ),
			'description' => '',
			'fields'      => array(

				array(
					'id'          => 'subscribed_msg',
					'label'       => __( 'Subscribed', 'dsubscribers' ),
					'description' => '',
					'type'        => 'text',
					'default'     => 'Thank you for subscribing!',
					'placeholder' => __( 'Subscribed message', 'dsubscribers' ),
				),

				array(
					'id'          => 'exists_msg',
					'label'       => __( 'Exists', 'dsubscribers' ),
					'description' => '',
					'type'        => 'text',
					'default'     => 'Sorry, this e-mail already exists',
					'placeholder' => __( 'Email already exists', 'dsubscribers' ),
				),

				array(
					'id'          => 'unsubscribed_msg',
					'label'       => __( 'Unsubscribed', 'dsubscribers' ),
					'description' => '',
					'type'        => 'text',
					'default'     => 'Unsubscribed correctly',
					'placeholder' => __( 'Unsubscribed correctly', 'dsubscribers' ),
				),

				array(
					'id'          => 'dont_exists_msg',
					'label'       => __( 'Subscriber don\'t exists', 'dsubscribers' ),
					'description' => '',
					'type'        => 'text',
					'default'     => 'Sorry, subscriber don\'t exists',
					'placeholder' => __( 'Subscriber don\'t exists', 'dsubscribers' ),
				),

			),
		);

		return apply_filters( 'dsubscribers_settings_fields', $settings );
	}

	/**
	 * Register plugin settings.
	 *
	 * @return void
	 */
	public function register_settings(): void {

		if ( is_array( $this->settings ) ) {

			foreach ( $this->settings as $section => $data ) {

				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), 'dsubscribers_settings' );

				foreach ( $data['fields'] as $field ) {

					$validation = '';

					if ( isset( $field['callback'] ) ) {

						$validation = $field['callback'];

					}

					$option_name = $this->base . $field['id'];
					register_setting( 'dsubscribers_settings', $option_name, $validation );

					add_settings_field( $field['id'], $field['label'], array( $this, 'display_field' ), 'dsubscribers_settings', $section, array( 'field' => $field ) );

				}
			}
		}
	}

	/**
	 * Add settings section
	 *
	 * @param array $section Settings section.
	 * @return void
	 */
	public function settings_section( $section ) {
		if ( ! is_array( $section ) ) {
			return;
		}

		echo wp_kses_post( '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n" );
	}

	/**
	 * Renders the settings fields.
	 *
	 * @param array $args The fields to display.
	 * @return void
	 */
	public function display_field( $args ) {

		$field = $args['field'];

		$html = '';

		$option_name = $this->base . $field['id'];
		$option      = get_option( $option_name );

		$data = '';

		if ( isset( $field['default'] ) ) {

			$data = $field['default'];

			if ( $option ) {

				$data = $option;

			}
		}

		switch ( $field['type'] ) {

			case 'text':
			case 'password':
			case 'number':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . $data . '"/>' . "\n";
				break;

			case 'text_secret':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value=""/>' . "\n";
				break;

			case 'textarea':
				$html .= '<textarea id="' . esc_attr( $field['id'] ) . '" rows="5" cols="50" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '">' . $data . '</textarea><br/>' . "\n";
				break;

			case 'checkbox':
				$checked = '';
				if ( $option && 'on' === $option ) {
					$checked = 'checked="checked"';
				}
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" ' . $checked . '/>' . "\n";
				break;

			case 'checkbox_multi':
				foreach ( $field['options'] as $k => $v ) {
					$checked = false;
					if ( in_array( $k, $data, true ) ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="checkbox" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '[]" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
				break;

			case 'radio':
				foreach ( $field['options'] as $k => $v ) {
					$checked = false;
					if ( $k === $data ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="radio" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
				break;

			case 'select':
				$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '">';
				foreach ( $field['options'] as $k => $v ) {
					$selected = false;
					if ( $k === $data ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
				break;

			case 'select_multi':
				$html .= '<select name="' . esc_attr( $option_name ) . '[]" id="' . esc_attr( $field['id'] ) . '" multiple="multiple">';
				foreach ( $field['options'] as $k => $v ) {
					$selected = false;
					if ( in_array( $k, $data, true ) ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '" />' . $v . '</label> ';
				}
				$html .= '</select> ';
				break;

			case 'image':
				$image_thumb = '';
				if ( $data ) {
					$image_thumb = wp_get_attachment_thumb_url( $data );
				}
				$html .= '<img id="' . esc_attr( $option_name ) . '_preview" class="image_preview" src="' . esc_attr( $image_thumb ) . '" /><br/>' . "\n";
				$html .= '<input id="' . esc_attr( $option_name ) . '_button" type="button" data-uploader_title="' . __( 'Upload an image', 'dsubscribers' ) . '" data-uploader_button_text="' . __( 'Use image', 'dsubscribers' ) . '" class="image_upload_button button" value="' . __( 'Upload new image', 'dsubscribers' ) . '" />' . "\n";
				$html .= '<input id="' . esc_attr( $option_name ) . '_delete" type="button" class="image_delete_button button" value="' . __( 'Remove image', 'dsubscribers' ) . '" />' . "\n";
				$html .= '<input id="' . esc_attr( $option_name ) . '" class="image_data_field" type="hidden" name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $data ) . '"/><br/>' . "\n";
				break;

			case 'color':
				?><div class="color-picker" style="position:relative;">
					<input type="text" name="<?php esc_attr( $option_name ); ?>" class="color" value="<?php esc_attr( $data ); ?>" />
					<div style="position:absolute;background:#FFF;z-index:99;border-radius:100%;" class="colorpicker"></div>
				</div>
				<?php
				break;

		}

		switch ( $field['type'] ) {

			case 'checkbox_multi':
			case 'radio':
			case 'select_multi':
				$html .= '<br/><span class="description">' . esc_attr( $field['description'] ) . '</span>';
				break;

			default:
				$html .= '<label for="' . esc_attr( $field['id'] ) . '"><span class="description">' . esc_attr( $field['description'] ) . '</span></label>' . "\n";
				break;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $html;
	}

	/**
	 * Render settings page content.
	 *
	 * @return void
	 */
	public function settings_page(): void {

		$html      = '<div class="wrap" id="dsubscribers_settings">' . "\n";
			$html .= '<h2>' . __( 'DSubscribers Settings', 'dsubscribers' ) . '</h2>' . "\n";
			$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

				ob_start();
				settings_fields( 'dsubscribers_settings' );
				do_settings_sections( 'dsubscribers_settings' );
				$html .= ob_get_clean();

				$html     .= '<p class="submit">' . "\n";
					$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings', 'dsubscribers' ) ) . '" />' . "\n";
				$html     .= '</p>' . "\n";
			$html         .= '</form>' . "\n";
		$html             .= '</div>' . "\n";

        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $html;
	}

	/**
	 * Returns a unique instance of this class.
	 *
	 * @param string $file The plugin file.
	 * @return self|null
	 */
	public static function instance( string $file ) {

		if ( is_null( self::$instance ) ) {

			self::$instance = new self( $file );

		}

		return self::$instance;
	}
}
