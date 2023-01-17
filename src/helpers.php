<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function EQRP_init_eqr_payment_class() {
    require MAIN_FOLDER_PATH . 'classes/WC_Gateway_eqr_payment.php';
}

function EQRP_add_eqr_payment_class( $methods ) {
    $methods[] = 'WC_Gateway_eqr_payment'; 
    return $methods;
}

function EQRP_web_hook() {
    require MAIN_FOLDER_PATH . 'classes/WC_eqr_web_hook.php';
    new WC_eqr_web_hook;
}
