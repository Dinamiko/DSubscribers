<?php
/*
 * Plugin Name: DSubscribers
 * Version: 1.2.1
 * Plugin URI: http://wp.dinamiko.com/demos/dsubscribers
 * Description: Manage subscribers from your site with ease
 * Author: Emili Castells
 * Author URI: http://www.dinamiko.com
 * Requires at least: 3.9
 * Tested up to: 4.8
 */

if ( ! defined( 'ABSPATH' ) ) exit;

require_once( 'includes/class-dsubscribers.php' );
require_once( 'includes/class-dsubscribers-settings.php' );
require_once( 'includes/class-dsubscribers-table.php' );

function DSubscribers () {

	$instance = DSubscribers::instance( __FILE__, '1.2.1' );

	if( is_null( $instance->settings ) ) {

		$instance->settings = DSubscribers_Settings::instance( $instance );

	}

	$instance->table = DSubscribers_Table::instance( $instance );

	return $instance;

}

DSubscribers();
