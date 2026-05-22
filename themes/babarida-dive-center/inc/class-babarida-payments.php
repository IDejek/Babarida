<?php
/**
 * Payment Gateway Integration
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

class Babarida_Payments {

    /**
     * Get all available gateways
     */
    public static function gateways() {
        return array(
            'midtrans'      => array('name' => 'Midtrans',  'enabled' => get_option('babarida_midtrans_enabled', 'no') === 'yes'),
            'xendit'        => array('name' => 'Xendit',    'enabled' => get_option('babarida_xendit_enabled', 'no') === 'yes'),
            'stripe'        => array('name' => 'Stripe',    'enabled' => get_option('babarida_stripe_enabled', 'no') === 'yes'),
            'paypal'        => array('name' => 'PayPal',    'enabled' => get_option('babarida_paypal_enabled', 'no') === 'yes'),
            'bank_transfer' => array('name' => __('Bank Transfer', 'babarida'), 'enabled' => true),
        );
    }

    /**
     * Create a payment record
     */
    public static function create($booking_id, $gateway_key, $amount, $currency = 'USD') {
        $booking = Babarida_Booking::get($booking_id);
        if (!$booking) {
            return array('success' => false, 'message' => __('Booking not found.', 'babarida'));
        }

        $payment_id = wp_insert_post(array(
            'post_title'  => sprintf('Payment: %s via %s', $booking['reference'], $gateway_key),
            'post_type'   => 'payment',
            'post_status' => 'publish',
            'meta_input'  => array(
                '_payment_booking_id' => $booking_id,
                '_payment_gateway'    => $gateway_key,
                '_payment_amount'     => floatval($amount),
                '_payment_currency'   => sanitize_text_field($currency),
                '_payment_status'     => 'pending',
                '_payment_created'    => current_time('mysql'),
            ),
        ));

        if (is_wp_error($payment_id)) {
            return array('success' => false, 'message' => __('Failed to create payment record.', 'babarida'));
        }

        // Dispatch to gateway
        switch ($gateway_key) {
            case 'midtrans':
                return self::process_midtrans($payment_id, $booking, $amount, $currency);
            case 'xendit':
                return self::process_xendit($payment_id, $booking, $amount, $currency);
            case 'stripe':
                return self::process_stripe($payment_id, $booking, $amount, $currency);
            case 'paypal':
                return self::process_paypal($payment_id, $booking, $amount, $currency);
            case 'bank_transfer':
                return self::process_bank_transfer($payment_id, $booking, $amount);
            default:
                return array('success' => false, 'message' => __('Unknown payment gateway.', 'babarida'));
        }
    }

    /**
     * Midtrans Integration
     */
    private static function process_midtrans($payment_id, $booking, $amount, $currency) {
        $server_key = get_option('babarida_midtrans_server_key', '');
        $is_prod    = get_option('babarida_midtrans_mode', 'sandbox') === 'production';
        $base_url   = $is_prod ? 'https://app.midtrans.com/snap/v1' : 'https://app.sandbox.midtrans.com/snap/v1';

        $payload = array(
            'transaction_details' => array(
                'order_id'     => $booking['reference'] . '-' . $payment_id,
                'gross_amount' => (int) round($amount),
            ),
            'customer_details' => array(
                'first_name' => $booking['first_name'],
                'last_name'  => $booking['last_name'],
                'email'      => $booking['email'],
                'phone'      => $booking['phone'],
            ),
            'callbacks' => array(
                'finish' => home_url('/booking/complete/?id=' . $payment_id),
                'error'   => home_url('/booking/error/?id=' . $payment_id),
                'pending' => home_url('/booking/pending/?id=' . $payment_id),
            ),
        );

        $response = wp_remote_post($base_url . '/transactions', array(
            'headers' => array(
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($server_key . ':'),
            ),
            'body'    => wp_json_encode($payload),
            'timeout' => 30,
        ));

        if (is_wp_error($response)) {
            return array('success' => false, 'message' => $response->get_error_message());
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['token'])) {
            update_post_meta($payment_id, '_payment_token', $body['token']);
            return array(
                'success'  => true,
                'gateway'  => 'midtrans',
                'token'    => $body['token'],
                'redirect' => false,
            );
        }

        return array('success' => false, 'message' => __('Midtrans error: ', 'babarida') . ($body['message'] ?? 'Unknown'));
    }

    /**
     * Xendit Integration
     */
    private static function process_xendit($payment_id, $booking, $amount, $currency) {
        $secret_key = get_option('babarida_xendit_secret_key', '');
        $is_prod    = get_option('babarida_xendit_mode', 'sandbox') === 'production';
        $base_url   = $is_prod ? 'https://api.xendit.co' : 'https://api.xendit.co';

        $payload = array(
            'external_id'          => $booking['reference'] . '-' . $payment_id,
            'amount'               => (int) round($amount),
            'payer_email'          => $booking['email'],
            'description'          => 'Booking ' . $booking['reference'],
            'success_redirect_url' => home_url('/booking/complete/?id=' . $payment_id),
            'failure_redirect_url' => home_url('/booking/error/?id=' . $payment_id),
        );

        $response = wp_remote_post($base_url . '/v2/invoices', array(
            'headers' => array(
                'Content-Type'  => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($secret_key . ':'),
            ),
            'body'    => wp_json_encode($payload),
            'timeout' => 30,
        ));

        if (is_wp_error($response)) {
            return array('success' => false, 'message' => $response->get_error_message());
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['invoice_url'])) {
            update_post_meta($payment_id, '_payment_xendit_id', $body['id']);
            return array(
                'success'  => true,
                'gateway'  => 'xendit',
                'redirect' => $body['invoice_url'],
            );
        }

        return array('success' => false, 'message' => __('Xendit error.', 'babarida'));
    }

    /**
     * Stripe Integration
     */
    private static function process_stripe($payment_id, $booking, $amount, $currency) {
        $secret_key = get_option('babarida_stripe_secret_key', '');

        $response = wp_remote_post('https://api.stripe.com/v1/payment_intents', array(
            'headers' => array('Authorization' => 'Basic ' . base64_encode($secret_key . ':')),
            'body'    => array(
                'amount'   => (int) round($amount * 100),
                'currency' => strtolower($currency),
                'metadata' => array('booking_ref' => $booking['reference'], 'payment_id' => $payment_id),
            ),
            'timeout' => 30,
        ));

        if (is_wp_error($response)) {
            return array('success' => false, 'message' => $response->get_error_message());
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['client_secret'])) {
            $publishable = get_option('babarida_stripe_publishable_key', '');
            update_post_meta($payment_id, '_payment_stripe_pi', $body['id']);
            return array(
                'success'     => true,
                'gateway'     => 'stripe',
                'client_secret' => $body['client_secret'],
                'publishable' => $publishable,
            );
        }

        return array('success' => false, 'message' => __('Stripe error.', 'babarida'));
    }

    /**
     * PayPal Integration
     */
    private static function process_paypal($payment_id, $booking, $amount, $currency) {
        $client_id = get_option('babarida_paypal_client_id', '');
        $is_prod   = get_option('babarida_paypal_mode', 'sandbox') === 'production';
        $base_url  = $is_prod ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';

        $response = wp_remote_post($base_url . '/v2/checkout/orders', array(
            'headers' => array(
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . self::get_paypal_access_token(),
            ),
            'body' => wp_json_encode(array(
                'intent'         => 'CAPTURE',
                'purchase_units' => array(array(
                    'reference_id' => $booking['reference'],
                    'amount'       => array('currency_code' => $currency, 'value' => number_format($amount, 2, '.', '')),
                    'description'  => 'Booking ' . $booking['reference'],
                )),
                'application_context' => array(
                    'return_url' => home_url('/booking/complete/?id=' . $payment_id),
                    'cancel_url' => home_url('/booking/cancel/?id=' . $payment_id),
                ),
            )),
            'timeout' => 30,
        ));

        if (is_wp_error($response)) {
            return array('success' => false, 'message' => $response->get_error_message());
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['id'])) {
            $approve_url = '';
            foreach ($body['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    $approve_url = $link['href'];
                    break;
                }
            }
            update_post_meta($payment_id, '_payment_paypal_order', $body['id']);
            return array('success' => true, 'gateway' => 'paypal', 'redirect' => $approve_url);
        }

        return array('success' => false, 'message' => __('PayPal error.', 'babarida'));
    }

    /**
     * Get PayPal access token
     */
    private static function get_paypal_access_token() {
        $client_id     = get_option('babarida_paypal_client_id', '');
        $client_secret = get_option('babarida_paypal_secret', '');
        $is_prod       = get_option('babarida_paypal_mode', 'sandbox') === 'production';
        $base_url      = $is_prod ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';

        $response = wp_remote_post($base_url . '/v1/oauth2/token', array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode($client_id . ':' . $client_secret),
                'Content-Type'  => 'application/x-www-form-urlencoded',
            ),
            'body' => 'grant_type=client_credentials',
        ));

        if (!is_wp_error($response)) {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            return $body['access_token'] ?? '';
        }
        return '';
    }

    /**
     * Bank Transfer — return bank details
     */
    private static function process_bank_transfer($payment_id, $booking, $amount) {
        $bank_details = get_option('babarida_bank_details', array());
        update_post_meta($payment_id, '_payment_status', 'awaiting_transfer');

        return array(
            'success'      => true,
            'gateway'      => 'bank_transfer',
            'bank_details' => $bank_details,
            'message'      => __('Please complete the bank transfer and upload proof.', 'babarida'),
        );
    }

    /**
     * Handle payment webhook callbacks
     */
    public static function handle_webhook($gateway) {
        $payload = file_get_contents('php://input');

        switch ($gateway) {
            case 'midtrans':
                return self::webhook_midtrans($payload);
            case 'xendit':
                return self::webhook_xendit($payload);
            case 'stripe':
                return self::webhook_stripe($payload);
            case 'paypal':
                return self::webhook_paypal($payload);
            default:
                return array('status' => 400);
        }
    }

    private static function webhook_midtrans($payload) {
        $data = json_decode($payload, true);
        if (!$data || !isset($data['transaction_id'])) return array('status' => 400);

        $order_id = $data['order_id'];
        $status   = $data['transaction_status'];

        if ($status === 'settlement' || $status === 'capture') {
            self::mark_payment_complete($order_id, $data['transaction_id']);
        } elseif ($status === 'cancel' || $status === 'deny' || $status === 'expire') {
            self::mark_payment_failed($order_id);
        }

        return array('status' => 200);
    }

    private static function webhook_xendit($payload) {
        $data = json_decode($payload, true);
        if (!$data || !isset($data['external_id'])) return array('status' => 400);

        if ($data['status'] === 'PAID') {
            self::mark_payment_complete($data['external_id'], $data['payment_id']);
        } elseif ($data['status'] === 'EXPIRED' || $data['status'] === 'FAILED') {
            self::mark_payment_failed($data['external_id']);
        }
        return array('status' => 200);
    }

    private static function webhook_stripe($payload) {
        $sig    = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $secret = get_option('babarida_stripe_webhook_secret', '');
        // In production: verify signature with webhook_secret
        $data = json_decode($payload, true);
        if (!$data) return array('status' => 400);

        if ($data['type'] === 'payment_intent.succeeded') {
            $pi = $data['data']['object'];
            self::mark_payment_complete($pi['metadata']['booking_ref'] ?? '', $pi['id']);
        }
        return array('status' => 200);
    }

    private static function webhook_paypal() {
        $payload = file_get_contents('php://input');
        $data    = json_decode($payload, true);
        if (!$data) return array('status' => 400);

        if (($data['event_type'] ?? '') === 'PAYMENT.CAPTURE.COMPLETED') {
            $ref = $data['resource']['purchase_units'][0]['reference_id'] ?? '';
            self::mark_payment_complete($ref, $data['resource']['id']);
        }
        return array('status' => 200);
    }

    /**
     * Mark payment as complete
     */
    private static function mark_payment_complete($order_ref, $txn_id) {
        // Find payment by order reference
        $payments = get_posts(array(
            'post_type'  => 'payment',
            'numberposts' => 1,
            's'          => $order_ref,
        ));
        if (empty($payments)) return;

        $pid = $payments[0]->ID;
        update_post_meta($pid, '_payment_status', 'completed');
        update_post_meta($pid, '_payment_txn_id', $txn_id);
        update_post_meta($pid, '_payment_completed', current_time('mysql'));

        $booking_id = get_post_meta($pid, '_payment_booking_id', true);
        if ($booking_id) {
            Babarida_Booking::update_status($booking_id, 'paid');
            update_post_meta($booking_id, '_booking_payment_method', get_post_meta($pid, '_payment_gateway', true));
            update_post_meta($booking_id, '_booking_paid', get_post_meta($pid, '_payment_amount', true));
        }
    }

    /**
     * Mark payment as failed
     */
    private static function mark_payment_failed($order_ref) {
        $payments = get_posts(array(
            'post_type'   => 'payment',
            'numberposts' => 1,
            's'           => $order_ref,
        ));
        if (empty($payments)) return;

        $pid = $payments[0]->ID;
        update_post_meta($pid, '_payment_status', 'failed');

        $booking_id = get_post_meta($pid, '_payment_booking_id', true);
        if ($booking_id) {
            Babarida_Booking::update_status($booking_id, 'cancelled');
        }
    }

    /**
     * Get currency conversion rates
     */
    public static function get_currency_rates() {
        $cached = get_transient('babarida_currency_rates');
        if ($cached !== false) return $cached;

        $rates = array(
            'USD' => 1,
            'EUR' => 0.92,
            'SGD' => 1.34,
            'AUD' => 1.53,
            'IDR' => 15800,
        );

        // Optionally fetch from API
        $api_key = get_option('babarida_currency_api_key', '');
        if ($api_key) {
            $response = wp_remote_get('https://open.er-api.com/v6/latest/USD', array('timeout' => 10));
            if (!is_wp_error($response)) {
                $body = json_decode(wp_remote_retrieve_body($response), true);
                if (isset($body['rates'])) {
                    $rates['EUR'] = $body['rates']['EUR'] ?? 0.92;
                    $rates['SGD'] = $body['rates']['SGD'] ?? 1.34;
                    $rates['AUD'] = $body['rates']['AUD'] ?? 1.53;
                    $rates['IDR'] = $body['rates']['IDR'] ?? 15800;
                }
            }
        }

        set_transient('babarida_currency_rates', $rates, 3600);
        return $rates;
    }

    /**
     * Convert amount
     */
    public static function convert($amount_usd, $to_currency) {
        $rates = self::get_currency_rates();
        $rate  = $rates[$to_currency] ?? 1;
        return round($amount_usd * $rate, $to_currency === 'IDR' ? 0 : 2);
    }
}
