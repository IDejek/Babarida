<?php
/**
 * CRM System — Customer Profiles
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

class Babarida_CRM {

    /**
     * Find customer by email
     */
    public static function find_customer($email) {
        $email = sanitize_email($email);
        $user  = get_user_by('email', $email);
        if ($user) {
            return self::format($user);
        }
        return false;
    }

    /**
     * Create customer from booking data
     */
    public static function create_customer($data) {
        $user_id = wp_insert_user(array(
            'user_login' => sanitize_text_field($data['email']),
            'user_email' => sanitize_email($data['email']),
            'first_name' => sanitize_text_field($data['first_name'] ?? ''),
            'last_name'  => sanitize_text_field($data['last_name'] ?? ''),
            'user_pass'  => wp_generate_password(16, true, true),
            'role'       => 'subscriber',
        ));

        if (is_wp_error($user_id)) {
            return false;
        }

        update_user_meta($user_id, '_customer_phone',          sanitize_text_field($data['phone'] ?? ''));
        update_user_meta($user_id, '_customer_nationality',    sanitize_text_field($data['nationality'] ?? ''));
        update_user_meta($user_id, '_customer_certification',  sanitize_text_field($data['certification'] ?? ''));
        update_user_meta($user_id, '_customer_favorite_dest',  sanitize_text_field($data['destination'] ?? ''));
        update_user_meta($user_id, '_customer_loyalty_points', 0);
        update_user_meta($user_id, '_customer_loyalty_level',  'bronze');
        update_user_meta($user_id, '_customer_total_spent',    0);
        update_user_meta($user_id, '_customer_total_trips',    0);
        update_user_meta($user_id, '_customer_created',        current_time('mysql'));

        return $user_id;
    }

    /**
     * Get booking history for a customer
     */
    public static function get_history($user_id) {
        $user = get_user_by('id', $user_id);
        if (!$user) return array();

        $bookings = get_posts(array(
            'post_type'   => 'booking',
            'numberposts' => -1,
            'meta_key'    => '_booking_email',
            'meta_value'  => $user->user_email,
            'orderby'     => 'date',
            'order'       => 'DESC',
        ));

        $history = array();
        foreach ($bookings as $b) {
            $history[] = Babarida_Booking::get($b->ID);
        }
        return $history;
    }

    /**
     * Add loyalty points
     */
    public static function add_loyalty_points($user_id, $amount_spent) {
        $current = (int) get_user_meta($user_id, '_customer_loyalty_points', true);
        $earned  = (int) ($amount_spent * 10);
        $total   = $current + $earned;
        update_user_meta($user_id, '_customer_loyalty_points', $total);

        // Determine level
        $level = 'bronze';
        if ($total >= 50000) $level = 'platinum';
        elseif ($total >= 20000) $level = 'gold';
        elseif ($total >= 5000)  $level = 'silver';
        update_user_meta($user_id, '_customer_loyalty_level', $level);

        // Update totals
        $spent = (float) get_user_meta($user_id, '_customer_total_spent', true) + floatval($amount_spent);
        $trips = (int) get_user_meta($user_id, '_customer_total_trips', true) + 1;
        update_user_meta($user_id, '_customer_total_spent', $spent);
        update_user_meta($user_id, '_customer_total_trips', $trips);

        return array('points' => $total, 'level' => $level, 'earned' => $earned);
    }

    /**
     * Get all customers (paginated)
     */
    public static function get_all($per_page = 20, $page = 1) {
        $users = get_users(array(
            'role'    => 'subscriber',
            'number'  => $per_page,
            'offset'  => ($page - 1) * $per_page,
            'orderby' => 'registered',
            'order'   => 'DESC',
        ));
        $out = array();
        foreach ($users as $u) {
            $out[] = self::format($u);
        }
        return $out;
    }

    /**
     * Count total customers
     */
    public static function count() {
        return count(get_users(array('role' => 'subscriber', 'fields' => 'ID')));
    }

    /**
     * Format user into customer array
     */
    private static function format($user) {
        return array(
            'id'             => $user->ID,
            'first_name'     => $user->first_name,
            'last_name'      => $user->last_name,
            'email'          => $user->user_email,
            'phone'          => get_user_meta($user->ID, '_customer_phone', true),
            'nationality'    => get_user_meta($user->ID, '_customer_nationality', true),
            'certification'  => get_user_meta($user->ID, '_customer_certification', true),
            'loyalty_points' => (int) get_user_meta($user->ID, '_customer_loyalty_points', true),
            'loyalty_level'  => get_user_meta($user->ID, '_customer_loyalty_level', true) ?: 'bronze',
            'total_spent'    => (float) get_user_meta($user->ID, '_customer_total_spent', true),
            'total_trips'    => (int) get_user_meta($user->ID, '_customer_total_trips', true),
            'created'        => get_user_meta($user->ID, '_customer_created', true) ?: $user->user_registered,
        );
    }
}
