<?php
/*
 * Plugin Name: DSubscribers
 * Version: 1.2.2
 * Description: Manage subscribers from your site with ease
 * Author: Emili Castells
 * Author URI: https://dinamiko.dev
 * Requires at least: 3.9
 * Tested up to: 6.7
 * Text Domain: dsubscribers
 * Domain Path: /languages
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
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
