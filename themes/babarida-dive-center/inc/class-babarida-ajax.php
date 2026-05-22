<?php
/**
 * AJAX Handlers
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

class Babarida_Ajax {

    public function __construct() {
        // Frontend AJAX actions
        add_action('wp_ajax_babarida_submit_booking', array($this, 'submit_booking'));
        add_action('wp_ajax_nopriv_babarida_submit_booking', array($this, 'submit_booking'));
        add_action('wp_ajax_babarida_filter_trips', array($this, 'filter_trips'));
        add_action('wp_ajax_nopriv_babarida_filter_trips', array($this, 'filter_trips'));
        add_action('wp_ajax_babarida_check_availability', array($this, 'check_availability'));
        add_action('wp_ajax_nopriv_babarida_check_availability', array($this, 'check_availability'));
        add_action('wp_ajax_babarida_chat_reply', array($this, 'chat_reply'));
        add_action('wp_ajax_nopriv_babarida_chat_reply', array($this, 'chat_reply'));
        add_action('wp_ajax_babarida_subscribe_newsletter', array($this, 'subscribe_newsletter'));
        add_action('wp_ajax_nopriv_babarida_subscribe_newsletter', array($this, 'subscribe_newsletter'));
        add_action('wp_ajax_babarida_checkin_submit', array($this, 'checkin_submit'));
        add_action('wp_ajax_nopriv_babarida_checkin_submit', array($this, 'checkin_submit'));
    }

    /**
     * Submit Booking
     */
    public function submit_booking() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'babarida_nonce')) {
            wp_send_json_error(array('message' => __('Security verification failed.', 'babarida')));
        }

        // Sanitize inputs
        $data = array(
            'first_name' => sanitize_text_field($_POST['first_name'] ?? ''),
            'last_name'  => sanitize_text_field($_POST['last_name'] ?? ''),
            'email'      => sanitize_email($_POST['email'] ?? ''),
            'phone'      => sanitize_text_field($_POST['phone'] ?? ''),
            'destination'=> sanitize_text_field($_POST['destination'] ?? ''),
            'date'       => sanitize_text_field($_POST['date'] ?? ''),
            'guests'     => absint($_POST['guests'] ?? 1),
            'activity'   => sanitize_text_field($_POST['activity'] ?? ''),
            'notes'      => sanitize_textarea_field($_POST['notes'] ?? ''),
        );

        // Validate
        if (empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) || empty($data['phone']) || empty($data['destination']) || empty($data['date'])) {
            wp_send_json_error(array('message' => __('Please fill in all required fields.', 'babarida')));
        }
        if (!is_email($data['email'])) {
            wp_send_json_error(array('message' => __('Please enter a valid email address.', 'babarida')));
        }

        // Create booking post
        $booking_id = wp_insert_post(array(
            'post_title'  => sprintf(__('Booking: %s %s - %s', 'babarida'), $data['first_name'], $data['last_name'], $data['destination']),
            'post_type'   => 'booking',
            'post_status' => 'publish',
            'meta_input'  => array(
                '_booking_first_name' => $data['first_name'],
                '_booking_last_name'  => $data['last_name'],
                '_booking_email'      => $data['email'],
                '_booking_phone'      => $data['phone'],
                '_booking_destination'=> $data['destination'],
                '_booking_date'       => $data['date'],
                '_booking_guests'     => $data['guests'],
                '_booking_activity'   => $data['activity'],
                '_booking_notes'      => $data['notes'],
                '_booking_status'     => 'pending',
                '_booking_reference'  => babarida_generate_booking_ref(),
                '_booking_created'    => current_time('mysql'),
            ),
        ));

        if (is_wp_error($booking_id)) {
            wp_send_json_error(array('message' => __('Failed to create booking. Please try again.', 'babarida')));
        }

        // Send notifications
        Babarida_Notifications::send_booking_confirmation($booking_id, $data);

        wp_send_json_success(array(
            'message'     => __('Booking inquiry sent successfully! We\'ll contact you within 24 hours.', 'babarida'),
            'booking_id'  => $booking_id,
        ));
    }

    /**
     * Filter Trips (AJAX)
     */
    public function filter_trips() {
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'babarida_nonce')) {
            wp_send_json_error('Security failed.');
        }

        $filter = sanitize_text_field($_POST['filter'] ?? 'all');
        $dest   = sanitize_text_field($_POST['destination'] ?? '');
        $date   = sanitize_text_field($_POST['date'] ?? '');
        $price_min = floatval($_POST['price_min'] ?? 0);
        $price_max = floatval($_POST['price_max'] ?? 99999);

        $args = array(
            'post_type'   => 'trip',
            'numberposts' => -1,
            'post_status' => 'publish',
        );

        $tax_query = array();
        if ($filter !== 'all') {
            $tax_query[] = array(
                'taxonomy' => 'activity',
                'field'    => 'slug',
                'terms'    => $filter,
            );
        }
        if ($dest) {
            $tax_query[] = array(
                'taxonomy' => 'destination',
                'field'    => 'slug',
                'terms'    => $dest,
            );
        }
        if (!empty($tax_query)) {
            $args['tax_query'] = $tax_query;
        }

        $meta_query = array();
        if ($price_min > 0 || $price_max < 99999) {
            $meta_query[] = array(
                'key'     => '_trip_price',
                'value'   => array($price_min, $price_max),
                'compare' => 'BETWEEN',
                'type'    => 'NUMERIC',
            );
        }
        if (!empty($meta_query)) {
            $args['meta_query'] = $meta_query;
        }

        $trips = get_posts($args);
        $html = '';

        foreach ($trips as $trip) {
            $price = get_post_meta($trip->ID, '_trip_price', true);
            $html .= '<article class="boat-card reveal">';
            if (has_post_thumbnail($trip->ID)) {
                $html .= '<div class="boat-card-img">' . get_the_post_thumbnail($trip->ID, 'boat-card') . '</div>';
            }
            $html .= '<div class="boat-card-body">';
            $html .= '<h3 class="boat-card-name"><a href="' . get_permalink($trip->ID) . '" style="color:inherit;">' . esc_html($trip->post_title) . '</a></h3>';
            $html .= '<p style="font-size:0.82rem; color:var(--gray-500); margin-bottom:12px;">' . wp_trim_words(get_the_excerpt($trip->ID), 15) . '</p>';
            $html .= '<div class="boat-card-footer"><div class="boat-price">From <strong>$' . esc_html($price) . '</strong></div>';
            $html .= '<a href="#bookingModal" class="btn btn-primary btn-sm">Book</a></div>';
            $html .= '</div></article>';
        }

        if (empty($html)) {
            $html = '<div style="text-align:center; padding:60px 0; grid-column:1/-1;"><p style="color:var(--gray-400);">No trips match your criteria.</p></div>';
        }

        wp_send_json_success(array('html' => $html));
    }

    /**
     * Check Availability
     */
    public function check_availability() {
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'babarida_nonce')) {
            wp_send_json_error('Security failed.');
        }

        $date   = sanitize_text_field($_POST['date'] ?? '');
        $trip_id = absint($_POST['trip_id'] ?? 0);

        // Check existing bookings for this date and trip
        $existing = get_posts(array(
            'post_type'  => 'booking',
            'meta_key'   => '_booking_date',
            'meta_value' => $date,
            'numberposts' => -1,
            'post_status' => array('publish', 'pending', 'confirmed', 'paid'),
            'fields'     => 'ids',
        ));

        $max_guests = get_post_meta($trip_id, '_trip_max_guests', true);
        $max_guests = $max_guests ? intval($max_guests) : 20;

        $booked_guests = 0;
        foreach ($existing as $bid) {
            $booked_guests += intval(get_post_meta($bid, '_booking_guests', true));
        }

        $available = max(0, $max_guests - $booked_guests);

        wp_send_json_success(array(
            'available'    => $available,
            'max_guests'   => $max_guests,
            'booked'       => $booked_guests,
            'is_available' => $available > 0,
        ));
    }

    /**
     * AI Chat Reply
     */
    public function chat_reply() {
        $message = sanitize_text_field($_POST['message'] ?? '');
        if (empty($message)) {
            wp_send_json_error('Empty message.');
        }

        $reply = Babarida_Chat::generate_reply($message);
        wp_send_json_success(array('reply' => $reply));
    }

    /**
     * Newsletter Subscribe
     */
    public function subscribe_newsletter() {
        $email = sanitize_email($_POST['email'] ?? '');
        if (!is_email($email)) {
            wp_send_json_error(array('message' => __('Invalid email address.', 'babarida')));
        }

        $subscribers = get_option('babarida_newsletter_subscribers', array());
        if (!in_array($email, $subscribers, true)) {
            $subscribers[] = $email;
            update_option('babarida_newsletter_subscribers', $subscribers);
        }

        wp_send_json_success(array('message' => __('Welcome aboard! You\'re now subscribed.', 'babarida')));
    }

    /**
     * Check-in Submit
     */
    public function checkin_submit() {
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'babarida_nonce')) {
            wp_send_json_error('Security failed.');
        }

        $reference = sanitize_text_field($_POST['reference'] ?? '');
        $email     = sanitize_email($_POST['email'] ?? '');

        if (empty($reference) || empty($email)) {
            wp_send_json_error(array('message' => __('Please fill in all fields.', 'babarida')));
        }

        $bookings = get_posts(array(
            'post_type'  => 'booking',
            'meta_query' => array(
                'relation' => 'AND',
                array('key' => '_booking_reference', 'value' => $reference),
                array('key' => '_booking_email', 'value' => $email),
            ),
            'numberposts' => 1,
        ));

        if (empty($bookings)) {
            wp_send_json_error(array('message' => __('Booking not found. Please check your reference and email.', 'babarida')));
        }

        update_post_meta($bookings[0]->ID, '_booking_status', 'checked-in');

        wp_send_json_success(array(
            'message' => __('Check-in successful! Welcome aboard.', 'babarida'),
            'status'  => 'checked-in',
        ));
    }
}

new Babarida_Ajax();

/**
 * Generate booking reference
 */
function babarida_generate_booking_ref() {
    return 'BDC-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
}
