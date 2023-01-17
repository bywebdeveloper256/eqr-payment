<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WC_Gateway_eqr_payment' ) ){

    class WC_Gateway_eqr_payment extends WC_Payment_Gateway{

        public function __construct()
        {
            $this->id                 = 'eqr_payment';
            $this->icon               = '';
            $this->has_fields         = false;
            $this->method_title       = __( "EQR Payment", 'woocommerce' );
            $this->method_description = __( "", 'woocommerce' );
            $this->supports           = array(
                'products',
                'refunds',
            );
            
            $this->init_form_fields();
            $this->init_settings();
            
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        }

        public function init_settings()
        {
            parent::init_settings();
            $this->enabled     = ! empty( $this->settings['enabled'] ) && 'yes' === $this->settings['enabled'] ? 'yes' : 'no';
            $this->title       = $this->get_option('title');
            $this->description = $this->get_option('description');
            $this->apikey      = $this->get_option('apikey');
        }

        public function init_form_fields()
        {
            $this->form_fields = array(

                'enabled'       => array(
                    'title'         => __( 'Enable/Disable', 'woocommerce' ),
                    'type'          => 'checkbox',
                    'label'         => __( 'Enable EQR payment method', 'woocommerce' ),
                    'default'       => 'yes'
                ),

                'title'         => array(
                    'title'         => __( 'Title', 'woocommerce' ),
                    'type'          => 'text',
                    'description'   => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
                    'default'       => __( 'EQR Payment', 'woocommerce' ),
                    'desc_tip'      => true,
                ),

                'apikey'         => array(
                    'title'         => __( 'API Key', 'woocommerce' ),
                    'type'          => 'text',
                    'description'   => __( 'Enter your API Key here.', 'woocommerce' ),
                    'default'       => '',
                    'desc_tip'      => true,
                ),

                'description'   => array(
                    'title'         => __( 'Customer Message', 'woocommerce' ),
                    'type'          => 'textarea',
                    'description'   => __( 'This text will be displayed on the checkout page.', 'woocommerce' ),
                    'default'       => '',
                    'desc_tip'      => true
                )
            );
        }

        function process_payment( $order_id ) {

            global $woocommerce;

            if( get_woocommerce_currency() == "EUR" ) {

                $order = new WC_Order( $order_id );

                $parameters = [
                    'custom'         => $order_id,
                    'currency_code'  => get_woocommerce_currency(),
                    'amount'         => $order->get_total(),
                    'details'        => $order->get_payment_method_title(),
                    'web_hook'       => EQR_WEB_HOOK,
                    'cancel_url'     => site_url('/checkout/'),
                    'success_url'    => $this->get_return_url( $order ),
                    'customer_email' => $order->get_billing_email(),
                ];
                
                $url = 'https://webapp.equityresidential.es/payment/process';
                
                $headers = [
                    "Accept: application/json;charset=utf-8",
                    "Authorization: Bearer " . $this->apikey,
                ];
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POSTFIELDS,  http_build_query($parameters));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = json_decode(curl_exec($ch), true);
                $error    = curl_error($ch);
                curl_close($ch);

                if( $response['code'] == 200 ) {
                    return array(
                        'result'   => 'success',
                        'redirect' => $response['url']
                    );
                }else{
                    wc_add_notice( __('Payment error: Payment could not be made.', 'woothemes'), 'error' );
                    return;
                }
            }else{
                wc_add_notice( __('Error: The selected currency is not compatible with EQR Payment.', 'woothemes'), 'error' );
                return;
            }
        }
    }
}