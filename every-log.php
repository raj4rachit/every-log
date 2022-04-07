<?php
/*
Plugin Name: Every Log
Description: Every Log
Version: 1.0.0
Author: Techcnobrains
Author URI: https://technobrains.io/
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Basic plugin definitions 
 * 
 * @package EVERY LOG
 * @since 1.0.0
 */
if( !defined( 'EVERY_LOG_DIR' ) ) {
    define( 'EVERY_LOG_DIR', dirname( __FILE__ ) );      // Plugin dir
}
if( !defined( 'EVERY_LOG_VERSION' ) ) {
    define( 'EVERY_LOG_VERSION', '1.0.0' );      // Plugin Version
}
if( !defined( 'EVERY_LOG_NAME' ) ) {
    define( 'EVERY_LOG_NAME', 'Every Log' );      // Plugin Name
}
if( !defined( 'EVERY_LOG_INC_DIR' ) ) {
    define( 'EVERY_LOG_INC_DIR', EVERY_LOG_DIR.'/includes' );   // Plugin include dir
}
if( !defined( 'EVERY_LOG_ADMIN_DIR' ) ) {
    define( 'EVERY_LOG_ADMIN_DIR', EVERY_LOG_INC_DIR.'/admin' );  // Plugin admin dir
}
if( !defined( 'EVERY_LOG_VERSION' ) ){
    define( 'EVERY_LOG_VERSION' , '1.0.0' );  // Plugin Version
}

/**
 * Activation Hook
 *
 * Register plugin activation hook.
 *
 * @package EVERY_LOG_NAME
 * @since EVERY_LOG_VERSION
 */
register_activation_hook( __FILE__, 'every_log_install' );
function every_log_install(){
}

add_action( 'admin_init', 'every_log_check_if_woocommerce_installed' );
function every_log_check_if_woocommerce_installed() {
    if ( is_admin() && current_user_can( 'activate_plugins') && !is_plugin_active( 'woocommerce/woocommerce.php') ) {
        add_action( 'admin_notices', 'every_log_woocommerce_check_notice' );
        deactivate_plugins( plugin_basename( __FILE__) );
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }
    elseif ( is_admin() && is_plugin_active( 'woocommerce/woocommerce.php') ) {
        add_option('every_log_do_activation_redirect', true);
        add_action('admin_init', 'every_log_redirect');
    }
}

// Show dismissible error notice if WooCommerce is not present
function every_log_woocommerce_check_notice() {
    ?>
    <div class="alert alert-danger notice is-dismissible">
        <p>Sorry, but this plugin requires WooCommerce in order to work.
            So please ensure that WooCommerce is both installed and activated.
        </p>
    </div>
    <?php
}

/**
 * Redirection on Activation
 *
 * @package EVERY_LOG_NAME
 * @since EVERY_LOG_VERSION
 */
function every_log_redirect() {
    if (get_option('every_log_do_activation_redirect', false)) {
        delete_option('every_log_do_activation_redirect');
        wp_redirect("admin.php?page=api-license");
        exit;
    }
}

/**
 * Deactivation Hook
 *
 * Register plugin deactivation hook.
 *
 * @package EVERY_LOG_NAME
 * @since EVERY_LOG_VERSION
 */
register_deactivation_hook( __FILE__, 'every_log_uninstall');
function every_log_uninstall(){
}

// Admin class handles most of admin panel functionalities of plugin
include_once( EVERY_LOG_ADMIN_DIR.'/class-every-log-admin.php' );
$every_log_admin = new EveryLogAdmin();
$every_log_admin->add_hooks();