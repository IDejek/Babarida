<?php
/**
 * Booking System
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

class Babarida_Booking {

    const STATUS_PENDING    = 'pending';
    const STATUS_CONFIRMED  = 'confirmed';
    const STATUS_PAID       = 'paid';
    const STATUS_CHECKED_IN = 'checked-in';
    const STATUS_COMPLETED  = 'completed';
    const STATUS_CANCELLED  = 'cancelled';

    /**
     * Get single booking by ID
     */
    public static function get($booking_id) {
        $post = get_post($booking_id);
        if (!$post || $post->post_type !== 'booking') {
            return false;
        }
        return self::format($post);
    }

    /**
     * Format booking post into array
     */
    private static function format($post) {
        return array(
            'id'             => $post->ID,
            'title'          => $post->post_title,
            'reference'      => get_post_meta($post->ID, '_booking_reference', true),
            'first_name'     => get_post_meta($post->ID, '_booking_first_name', true),
            'last_name'      => get_post_meta($post->ID, '_booking_last_name', true),
            'email'          => get_post_meta($post->ID, '_booking_email', true),
            'phone'          => get_post_meta($post->ID, '_booking_phone', true),
            'destination'    => get_post_meta($post->ID, '_booking_destination', true),
            'date'           => get_post_meta($post->ID, '_booking_date', true),
            'guests'         => get_post_meta($post->ID, '_booking_guests', true),
            'activity'       => get_post_meta($post->ID, '_booking_activity', true),
            'notes'          => get_post_meta($post->ID, '_booking_notes', true),
            'status'         => get_post_meta($post->ID, '_booking_status', true),
            'created'        => get_post_meta($post->ID, '_booking_created', true),
            'guide_id'       => get_post_meta($post->ID, '_booking_guide', true),
            'boat_id'        => get_post_meta($post->ID, '_booking_boat', true),
            'total'          => get_post_meta($post->ID, '_booking_total', true),
            'paid'           => get_post_meta($post->ID, '_booking_paid', true),
            'payment_method' => get_post_meta($post->ID, '_booking_payment_method', true),
        );
    }

    /**
     * Update booking status
     */
    public static function update_status($booking_id, $status) {
        $valid = array(
            self::STATUS_PENDING, self::STATUS_CONFIRMED, self::STATUS_PAID,
            self::STATUS_CHECKED_IN, self::STATUS_COMPLETED, self::STATUS_CANCELLED,
        );
        if (!in_array($status, $valid, true)) {
            return false;
        }

        $old_status = get_post_meta($booking_id, '_booking_status', true);
        update_post_meta($booking_id, '_booking_status', $status);

        // Log
        if (function_exists('Babarida_Security_log_activity')) {
            Babarida_Security::log_activity('booking_status_change', sprintf(
                'Booking %s: %s → %s',
                get_post_meta($booking_id, '_booking_reference', true),
                $old_status,
                $status
            ));
        }

        // Notify
        if (function_exists('Babarida_Notifications_send_status_update')) {
            $booking = self::get($booking_id);
            if ($booking) {
                Babarida_Notifications::send_status_update($booking, $status);
            }
        }

        return true;
    }

    /**
     * Assign guide
     */
    public static function assign_guide($booking_id, $guide_id) {
        update_post_meta($booking_id, '_booking_guide', absint($guide_id));
    }

    /**
     * Assign boat
     */
    public static function assign_boat($booking_id, $boat_id) {
        update_post_meta($booking_id, '_booking_boat', absint($boat_id));
    }

    /**
     * Query bookings with filters
     */
    public static function get_all($args = array()) {
        $parsed = array(
            'post_type'   => 'booking',
            'numberposts' => 20,
            'orderby'     => 'date',
            'order'       => 'DESC',
        );

        $meta_query = array();

        if (!empty($args['status'])) {
            $meta_query[] = array('key' => '_booking_status', 'value' => sanitize_text_field($args['status']));
        }
        if (!empty($args['date_from'])) {
            $meta_query[] = array('key' => '_booking_date', 'value' => sanitize_text_field($args['date_from']), 'compare' => '>=');
        }
        if (!empty($args['date_to'])) {
            $meta_query[] = array('key' => '_booking_date', 'value' => sanitize_text_field($args['date_to']), 'compare' => '<=');
        }
        if (!empty($args['search'])) {
            $parsed['s'] = sanitize_text_field($args['search']);
        }
        if (!empty($args['per_page'])) {
            $parsed['numberposts'] = absint($args['per_page']);
        }
        if (!empty($args['offset'])) {
            $parsed['offset'] = absint($args['offset']);
        }

        if (!empty($meta_query)) {
            $parsed['meta_query'] = $meta_query;
        }

        $posts    = get_posts($parsed);
        $bookings = array();
        foreach ($posts as $p) {
            $bookings[] = self::format($p);
        }
        return $bookings;
    }

    /**
     * Count bookings by status
     */
    public static function count_by_status($status = '') {
        $args = array(
            'post_type'   => 'booking',
            'numberposts' => -1,
            'fields'      => 'ids',
        );
        if ($status) {
            $args['meta_key']   = '_booking_status';
            $args['meta_value'] = sanitize_text_field($status);
        }
        return count(get_posts($args));
    }

    /**
     * Get human-readable status label
     */
    public static function status_label($status) {
        $map = array(
            'pending'    => __('Pending', 'babarida'),
            'confirmed'  => __('Confirmed', 'babarida'),
            'paid'       => __('Paid', 'babarida'),
            'checked-in' => __('Checked In', 'babarida'),
            'completed'  => __('Completed', 'babarida'),
            'cancelled'  => __('Cancelled', 'babarida'),
        );
        return $map[$status] ?? $status;
    }

    /**
     * Get status badge color
     */
    public static function status_color($status) {
        $map = array(
            'pending'    => '#F59E0B',
            'confirmed'  => '#0077E6',
            'paid'       => '#10B981',
            'checked-in' => '#8B5CF6',
            'completed'  => '#10B981',
            'cancelled'  => '#EF4444',
        );
        return $map[$status] ?? '#64748B';
    }

    /**
     * Get revenue for date range
     */
    public static function get_revenue($date_from, $date_to) {
        global $wpdb;
        $from = sanitize_text_field($date_from);
        $to   = sanitize_text_field($date_to);

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT pm1.meta_value as status, pm2.meta_value as total
             FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_booking_status'
             INNER JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_booking_total'
             INNER JOIN {$wpdb->postmeta} pm3 ON p.ID = pm3.post_id AND pm3.meta_key = '_booking_date'
             WHERE p.post_type = 'booking' AND p.post_status = 'publish'
             AND pm3.meta_value BETWEEN %s AND %s
             AND pm1.meta_value IN ('paid','completed','checked-in')",
            $from, $to
        ));

        $revenue = 0;
        foreach ($results as $r) {
            $revenue += floatval($r->total);
        }
        return $revenue;
    }
}
