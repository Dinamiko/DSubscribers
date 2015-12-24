<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class DSubscribers {

	private static $_instance = null;

	public $settings = null;
	public $_version;
	public $_token;
	public $file;
	public $dir;
	public $assets_dir;
	public $assets_url;
	public $script_suffix;

	public function __construct ( $file = '', $version = '1.0' ) {

		$this->_version = $version;
		$this->_token = 'dsubscribers';

		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		register_activation_hook( $this->file, array( $this, 'install' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );

		// custom wp table
		register_activation_hook( $this->file, array( $this, 'dsubscribers_database_install' ) );

		// TODO
		//add_action( 'plugins_loaded', array( $this, 'dsubscribers_update_db_check' ), 10, 1 );

		add_action('wp_head', array( $this, 'dsubscribers_ajaxurl' ) );	

		add_shortcode( 'dsubscribers', array( $this, 'dsubscribers_shortcode' ) );
	
		add_action('wp_ajax_dsubscribers_ajax', array( $this, 'dsubscribers_ajax' ) );
		add_action('wp_ajax_nopriv_dsubscribers_ajax', array( $this, 'dsubscribers_ajax' ) );

		add_filter('wp_mail_content_type', create_function('', 'return "text/html";'));

		add_action( 'widgets_init', array( $this, 'register_dsubscribers_widget' ) );

		// sanitizes dsubscribers options
		add_action( 'init', array( $this, 'dsubscribers_sanitize_options' ) );


	}

	/**
	* adds filter pre_update_option_{option}
	*/
	public function dsubscribers_sanitize_options() {

		add_filter( 'pre_update_option_dsubscribers_send_checkbox', array( $this, 'dsubscribers_update_field_dsubscribers_send_checkbox'), 10, 2 );
		add_filter( 'pre_update_option_dsubscribers_message_block', array( $this, 'dsubscribers_update_field_dsubscribers_message_block'), 10, 2 );
		add_filter( 'pre_update_option_dsubscribers_subscribed_msg', array( $this, 'dsubscribers_update_field_dsubscribers_subscribed_msg'), 10, 2 );
		add_filter( 'pre_update_option_dsubscribers_exists_msg', array( $this, 'dsubscribers_update_field_dsubscribers_exists_msg'), 10, 2 );
		add_filter( 'pre_update_option_dsubscribers_unsubscribed_msg', array( $this, 'dsubscribers_update_field_dsubscribers_unsubscribed_msg'), 10, 2 );
		add_filter( 'pre_update_option_dsubscribers_dont_exists_msg', array( $this, 'dsubscribers_update_field_dsubscribers_dont_exists_msg'), 10, 2 );
		
	}

	/**
	* sanitizes dsubscribers_send_checkbox option
	*/
	public function dsubscribers_update_field_dsubscribers_send_checkbox( $new_value, $old_value ) {
		$new_value = sanitize_text_field( $new_value );	
		return $new_value;
	}

	/**
	* sanitizes dsubscribers_message_block option
	*/
	public function dsubscribers_update_field_dsubscribers_message_block( $new_value, $old_value ) {
	
		$arr = array(
		    'a' => array(
		        'href' => array(),
		        'title' => array()
		    ),
		    'br' => array(),
		    'em' => array(),
		    'strong' => array(),
		    'p' => array(),
		    'h1' => array(),
		    'h2' => array(),
		    'h3' => array(),
		    'h4' => array(),
		);

		$new_value = wp_kses( $new_value, $arr );		

		return $new_value;
	}

	/**
	* sanitizes dsubscribers_subscribed_msg option
	*/
	public function dsubscribers_update_field_dsubscribers_subscribed_msg( $new_value, $old_value ) {
		$new_value = sanitize_text_field( $new_value );	
		return $new_value;
	}

	/**
	* sanitizes dsubscribers_exists_msg option
	*/
	public function dsubscribers_update_field_dsubscribers_exists_msg( $new_value, $old_value ) {
		$new_value = sanitize_text_field( $new_value );	
		return $new_value;
	}

	/**
	* sanitizes dsubscribers_unsubscribed_msg option
	*/
	public function dsubscribers_update_field_dsubscribers_unsubscribed_msg( $new_value, $old_value ) {
		$new_value = sanitize_text_field( $new_value );	
		return $new_value;
	}

	/**
	* sanitizes dsubscribers_dont_exists_msg option
	*/
	public function dsubscribers_update_field_dsubscribers_dont_exists_msg( $new_value, $old_value ) {
		$new_value = sanitize_text_field( $new_value );	
		return $new_value;
	}

	public function register_dsubscribers_widget() {

		require_once( 'class-dsubscribers-widget.php' );

	    register_widget( 'Dsubscribers_Widget' );

	}

	public function enqueue_styles () {
		wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-frontend' );
	} 

	public function enqueue_scripts () {

		wp_register_script( $this->_token . '-validate', esc_url( $this->assets_url ) . 'js/jquery.validate.min.js', array( 'jquery' ), $this->_version, true );
		wp_enqueue_script( $this->_token . '-validate' );

		wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'js/frontend.js', array( 'jquery' ), $this->_version, true );
		wp_enqueue_script( $this->_token . '-frontend' );
		
	}  

	public function load_localisation () {
		load_plugin_textdomain( 'dsubscribers', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} 

	public function load_plugin_textdomain () {

	    $domain = 'dsubscribers';
	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );

	}

	public function dsubscribers_database_install() {

		global $wpdb;
	  	global $jal_db_version;

	   	$table_name = $wpdb->prefix . "dsubscribers";

		$sql = "CREATE TABLE $table_name (
			  		id mediumint(9) NOT NULL AUTO_INCREMENT,
			  		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			  		email VARCHAR(200) DEFAULT '' NOT NULL,
					UNIQUE KEY id (id)
				);";


	  	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	  	dbDelta( $sql );
	 
	  	add_option( "jal_db_version", $jal_db_version );

	}

	/* TODO
	public function dsubscribers_update_db_check() {

    	global $jal_db_version;

	    if (get_site_option( 'jal_db_version' ) != $jal_db_version) {

	        jal_install();

	    }

	}
	*/

	public function dsubscribers_ajaxurl() { ?>	

		<script type="text/javascript">

			var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';

		</script>
	
	<?php } 

	public function dsubscribers_ajax () {

 		//$nonce = $_REQUEST['dsubscribers_nonce'];

		if ( ! isset( $_POST['dsubscribers_nonce'] ) || ! wp_verify_nonce( $_POST['dsubscribers_nonce'], 'dsubscribers_form_action' ) ) {
		//if ( ! wp_verify_nonce( $nonce, 'dsubscribers_nonce' ) ) {   

		    die( 'Security check' ); 

		} else {

			$dsubscribers_action = sanitize_text_field( $_POST['dsubscribers_action'] );
			//$dsubscribers_email = wp_kses($_POST['dsubscribers_email']);
			$dsubscribers_email = sanitize_email( $_POST['dsubscribers_email'] );

			switch ( $dsubscribers_action ) {

				case 'unsubscribe':

					global $wpdb;
					$table_name = $wpdb->prefix . "dsubscribers";

					// get id from email
					$row = $wpdb->get_row("SELECT * FROM $table_name WHERE email='$dsubscribers_email'");

					if( $row ) {					

						$id = $row->id;

						$wpdb->delete( 'wp_dsubscribers', array( 'ID' => $id ) );

						$msg = 'unsubscribed';

						$result['type'] = 'success';
			      		$result['msg'] = '<span class="dsubscribers_success">'. get_option( 'dsubscribers_unsubscribed_msg', 'Unsubscribed correctly' ) .'</span>';

						$result = json_encode( $result );
						echo $result;
							
						die();


					} else {

						$result['type'] = 'error';
			      		$result['msg'] = '<span class="dsubscribers_error">'. get_option( 'dsubscribers_dont_exists_msg', 'Sorry, subscriber doesn\'t exists' ) .'</span>';

						$result = json_encode( $result );
						echo $result;

						die();

					}			
				
				default:

					global $wpdb;
					$table_name = $wpdb->prefix . "dsubscribers";

					$emails = $wpdb->get_results( "SELECT * FROM $table_name" );
						
					// if email exists -> redirect and show message exists

					foreach ( $emails as $email ) {

						if ( $email->email == $dsubscribers_email ) {
							
							$result['type'] = 'error';
			      			$result['msg'] = '<span class="dsubscribers_error">'. get_option( 'dsubscribers_exists_msg', 'Sorry, this e-mail already exists' ) .'</span>';

							$result = json_encode( $result );
							echo $result;
							
							die();																	

						} 

					}

					// if email don't exists -> insert data to wp_dsubscribers table 
					// and redirect with message created

					$inserted = $wpdb->insert (

						$table_name,

						array(
								'email' => $dsubscribers_email,
								'time' => date( "Y-m-d h:i:s", time() ),
							),

						array(
								'%s',
								'%s'
							)

					);
							
					if( $inserted ){

						$insert_id = $wpdb->insert_id;

						// if option send email checked in settings

						if ( get_option( 'dsubscribers_send_checkbox' ) == 'on' ) {

							$subject = 'The subject';

							$message = get_option( 'dsubscribers_message_block' );
								
							$headers = 'From: '. get_bloginfo( 'name' ) .' <'. get_bloginfo( 'admin_email' ) .'>';

							wp_mail( $dsubscribers_email, $subject, $message, $headers );

						}
					
						$result['type'] = 'success';
			      		$result['msg'] = '<span class="dsubscribers_success">'. get_option( 'dsubscribers_subscribed_msg', 'Thank you for subscribing!' ) .'</span>';

						$result = json_encode( $result );
						echo $result;
							
						die();
							
					}

					break;

			}

		}

	}

	/**
	* [dsubscribers]
	*/
	public function dsubscribers_shortcode ( $atts ) {

		$a = shortcode_atts( array(

	        'action' => 'subscribe',
	        'type' => ''

	    ), $atts );
		
		/*
		$nonce = wp_create_nonce("dsubscribers_nonce");
		$link = admin_url('admin-ajax.php?action=dsubscribers_ajax');
		*/

		$content = '<div id="dsubscribers-container">';

			$content .= '<p id="dsubscribers_msg"></p>';

			if( $a['type'] == 'widget') {

				$content .= '<p id="dsubscribers_msg_widget"></p>';

				//$content .= '<form id="form-validation-widget" class="form-container" method="post" action="'. $link .'">';
				$content .= '<form id="form-validation-widget" class="form-container" data-action="">';			

					// TODO escape
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

				    //$content .= '<input type="hidden" id="dsubscribers_nonce" name="dsubscribers_nonce" value="'. $nonce .'" />';
				    $content .= wp_nonce_field( 'dsubscribers_form_action', 'dsubscribers_form_nonce' );

				$content .= '</form>';


			} else {

				if( $a['action'] == 'unsubscribe' ) {

					$content .= '<p id="dsubscribers_unsubscribe_msg"></p>';
					$content .= '<form id="form-validation-unsubscribe" class="form-container">';

				} else {

					$content .= '<p id="dsubscribers_msg"></p>';
					$content .= '<form id="form-validation" class="form-container">';

				}

				//$content .= '<form id="form-validation" class="form-container" method="post" action="'. $link .'">';			
				//$content .= '<form id="form-validation" class="form-container">';
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

				    //$content .= '<input type="hidden" id="dsubscribers_nonce" name="dsubscribers_nonce" value="'. $nonce .'" />';
				    $content .= wp_nonce_field( 'dsubscribers_form_action', 'dsubscribers_form_nonce' );

				$content .= '</form>';			

			}

		$content .= '</div>';

		return $content;

	}

	public function install () {

		// plugin version
		$this->_log_version_number();

	}

	public static function instance ( $file = '', $version = '1.0.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}
		return self::$_instance;
	} 

	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} 

	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	}

	private function _log_version_number () {
		update_option( $this->_token . '_version', $this->_version );
	} 

}