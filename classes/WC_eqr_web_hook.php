<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WC_eqr_web_hook' ) ){
    class WC_eqr_web_hook extends WC_Payment_Gateway{
        public function __construct()
        {
            $this->eqr_handler();
        }
            
        public function eqr_handler()
        {
            global $woocommerce;

            if( $_POST['code'] == 200 && $_POST['status'] == 'OK' )
            {
                $order = new WC_Order( $_POST['custom'] );

                $order->update_status( 'completed' );

                $order->payment_complete();

                $woocommerce->cart->empty_cart();
            }

            die();
        }
    }
}