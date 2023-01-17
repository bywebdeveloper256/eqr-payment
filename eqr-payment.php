<?php
/**
 * The plugin EQR Payment
 *
 * Plugin Name:       EQR Payment
 * Description:       EQR Payment
 * Version:           1.0.0
 * Author:            Raul Barroso
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! defined( 'MAIN_FILE_PATH' ) ) define( 'MAIN_FILE_PATH', __FILE__ );

if ( ! defined( 'MAIN_FOLDER_URL' ) ) define( 'MAIN_FOLDER_URL', plugins_url( '/', __FILE__ ) );

if ( ! defined( 'MAIN_FOLDER_PATH' ) ) define( 'MAIN_FOLDER_PATH', plugin_dir_path( __FILE__ ) );

if ( ! defined( 'EQR_WEB_HOOK' ) ) define( 'EQR_WEB_HOOK', site_url( '/wc-api/eqr_payment/' ) );

require MAIN_FOLDER_PATH . 'vendor/autoload.php';

add_action( 'plugins_loaded', 'EQRP_init_eqr_payment_class' );

add_filter( 'woocommerce_payment_gateways', 'EQRP_add_eqr_payment_class' );

add_action('woocommerce_api_eqr_payment', 'EQRP_web_hook' );