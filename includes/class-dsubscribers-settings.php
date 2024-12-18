<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class DSubscribers_Settings {

	private static $_instance = null;

	public $parent = null;
	public $base = '';
	public $settings = array();

	public function __construct ( $parent ) {

		$this->parent = $parent;

		// settings in wp_options table
		$this->base = 'dsubscribers_';

		add_action( 'admin_init', array( $this, 'init_settings' ) );
		add_action( 'admin_init' , array( $this, 'register_settings' ) );
		add_action( 'admin_menu' , array( $this, 'add_menu_item' ) );

		add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ) , array( $this, 'add_settings_link' ) );

	}

	public function init_settings () {

		$this->settings = $this->settings_fields();

	}

	public function add_menu_item () {
        $page = add_options_page(
                __('DSubscribers Settings', 'dsubscribers'),
                __('DSubscribers Settings', 'dsubscribers'),
                'manage_options',
                'dsubscribers_settings',
                array($this, 'settings_page')
        );

		add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
	}

	public function settings_assets () {}


	public function add_settings_link ( $links ) {

		$settings_link = '<a href="options-general.php?page=dsubscribers_settings">' . __( 'Settings', 'dsubscribers' ) . '</a>';
  		array_push( $links, $settings_link );
  		return $links;

	}

	private function settings_fields () {

		$settings['subscriber_email'] = array(

			'title'					=> __( 'Subscriber E-mail', 'dsubscribers' ),
			'description'			=> '',
			'fields'				=> array(

				array(
					'id' 			=> 'send_checkbox',
					'label'			=> __( 'Send E-mail to subscriber', 'dsubscribers' ),
					'description'	=> '',
					'type'			=> 'checkbox',
					'default'		=> ''
				),

				array(
					'id' 			=> 'message_block',
					'label'			=> __( 'Message' , 'dsubscribers' ),
					'description'	=> __( 'This box accepts HTML tags', 'dsubscribers' ),
					'type'			=> 'textarea',
					'default'		=> 'Thank you for subscribing!',
					'placeholder'	=> ''
				)

			)
		);

		$settings['messages'] = array(

			'title'					=> __( 'Form Messages', 'dsubscribers' ),
			'description'			=> '',
			'fields'				=> array(

				array(
					'id' 			=> 'subscribed_msg',
					'label'			=> __( 'Subscribed' , 'dsubscribers' ),
					'description'	=> '',
					'type'			=> 'text',
					'default'		=> 'Thank you for subscribing!',
					'placeholder'	=> __( 'Subscribed message', 'dsubscribers' )
				),

				array(
					'id' 			=> 'exists_msg',
					'label'			=> __( 'Exists' , 'dsubscribers' ),
					'description'	=> '',
					'type'			=> 'text',
					'default'		=> 'Sorry, this e-mail already exists',
					'placeholder'	=> __( 'Email already exists', 'dsubscribers' )
				),

				array(
					'id' 			=> 'unsubscribed_msg',
					'label'			=> __( 'Unsubscribed' , 'dsubscribers' ),
					'description'	=> '',
					'type'			=> 'text',
					'default'		=> 'Unsubscribed correctly',
					'placeholder'	=> __( 'Unsubscribed correctly', 'dsubscribers' )
				),

				array(
					'id' 			=> 'dont_exists_msg',
					'label'			=> __( 'Subscriber don\'t exists' , 'dsubscribers' ),
					'description'	=> '',
					'type'			=> 'text',
					'default'		=> 'Sorry, subscriber don\'t exists',
					'placeholder'	=> __( 'Subscriber don\'t exists', 'dsubscribers' )
				)

			)
		);

		$settings = apply_filters( 'dsubscribers_settings_fields', $settings );

		return $settings;

	}

	public function register_settings () {

		if( is_array( $this->settings ) ) {

			foreach( $this->settings as $section => $data ) {

				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), 'dsubscribers_settings' );

				foreach( $data['fields'] as $field ) {

					$validation = '';

					if( isset( $field['callback'] ) ) {

						$validation = $field['callback'];

					}

					$option_name = $this->base . $field['id'];
					register_setting( 'dsubscribers_settings', $option_name, $validation );

					add_settings_field( $field['id'], $field['label'], array( $this, 'display_field' ), 'dsubscribers_settings', $section, array( 'field' => $field ) );

				}

			}

		}

	}

	public function settings_section ( $section ) {

		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;

	}

	public function display_field ( $args ) {

		$field = $args['field'];

		$html = '';

		$option_name = $this->base . $field['id'];
		$option = get_option( $option_name );

		$data = '';

		if( isset( $field['default'] ) ) {

			$data = $field['default'];

			if( $option ) {

				$data = $option;

			}

		}

		switch( $field['type'] ) {

			case 'text':
			case 'password':
			case 'number':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . $data . '"/>' . "\n";
			break;

			case 'text_secret':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value=""/>' . "\n";
			break;

			case 'textarea':
				$html .= '<textarea id="' . esc_attr( $field['id'] ) . '" rows="5" cols="50" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '">' . $data . '</textarea><br/>'. "\n";
			break;

			case 'checkbox':
				$checked = '';
				if( $option && 'on' == $option ){
					$checked = 'checked="checked"';
				}
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" ' . $checked . '/>' . "\n";
			break;

			case 'checkbox_multi':
				foreach( $field['options'] as $k => $v ) {
					$checked = false;
					if( in_array( $k, $data ) ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="checkbox" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '[]" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
			break;

			case 'radio':
				foreach( $field['options'] as $k => $v ) {
					$checked = false;
					if( $k == $data ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="radio" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
			break;

			case 'select':
				$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '">';
				foreach( $field['options'] as $k => $v ) {
					$selected = false;
					if( $k == $data ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
			break;

			case 'select_multi':
				$html .= '<select name="' . esc_attr( $option_name ) . '[]" id="' . esc_attr( $field['id'] ) . '" multiple="multiple">';
				foreach( $field['options'] as $k => $v ) {
					$selected = false;
					if( in_array( $k, $data ) ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '" />' . $v . '</label> ';
				}
				$html .= '</select> ';
			break;

			case 'image':
				$image_thumb = '';
				if( $data ) {
					$image_thumb = wp_get_attachment_thumb_url( $data );
				}
				$html .= '<img id="' . $option_name . '_preview" class="image_preview" src="' . $image_thumb . '" /><br/>' . "\n";
				$html .= '<input id="' . $option_name . '_button" type="button" data-uploader_title="' . __( 'Upload an image' , 'dsubscribers' ) . '" data-uploader_button_text="' . __( 'Use image' , 'dsubscribers' ) . '" class="image_upload_button button" value="'. __( 'Upload new image' , 'dsubscribers' ) . '" />' . "\n";
				$html .= '<input id="' . $option_name . '_delete" type="button" class="image_delete_button button" value="'. __( 'Remove image' , 'dsubscribers' ) . '" />' . "\n";
				$html .= '<input id="' . $option_name . '" class="image_data_field" type="hidden" name="' . $option_name . '" value="' . $data . '"/><br/>' . "\n";
			break;

			case 'color':
				?><div class="color-picker" style="position:relative;">
			        <input type="text" name="<?php esc_attr_e( $option_name ); ?>" class="color" value="<?php esc_attr_e( $data ); ?>" />
			        <div style="position:absolute;background:#FFF;z-index:99;border-radius:100%;" class="colorpicker"></div>
			    </div>
			    <?php
			break;

		}

		switch( $field['type'] ) {

			case 'checkbox_multi':
			case 'radio':
			case 'select_multi':
				$html .= '<br/><span class="description">' . $field['description'] . '</span>';
			break;

			default:
				$html .= '<label for="' . esc_attr( $field['id'] ) . '"><span class="description">' . $field['description'] . '</span></label>' . "\n";
			break;
		}

		echo $html;

	}

	public function validate_field ( $data ) {

		if( $data && strlen( $data ) > 0 && $data != '' ) {
			$data = urlencode( strtolower( str_replace( ' ' , '-' , $data ) ) );
		}

		return $data;

	}

	public function settings_page () {

		$html = '<div class="wrap" id="dsubscribers_settings">' . "\n";
			$html .= '<h2>' . __( 'DSubscribers Settings' , 'dsubscribers' ) . '</h2>' . "\n";
			$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

				// Get settings fields
				ob_start();
				settings_fields( 'dsubscribers_settings' );
				do_settings_sections( 'dsubscribers_settings' );
				$html .= ob_get_clean();

				$html .= '<p class="submit">' . "\n";
					$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings' , 'dsubscribers' ) ) . '" />' . "\n";
				$html .= '</p>' . "\n";
			$html .= '</form>' . "\n";
		$html .= '</div>' . "\n";

		echo $html;

	}

	public static function instance ( $parent ) {

		if ( is_null( self::$_instance ) ) {

			self::$_instance = new self( $parent );

		}

		return self::$_instance;

	}

	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	}

	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	}

}
