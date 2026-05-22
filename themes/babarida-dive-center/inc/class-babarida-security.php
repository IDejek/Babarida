<?php
/**
 * Security System
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

class Babarida_Security {

    /**
     * Log an activity
     */
    public static function log_activity($action, $description, $user_id = null) {
        $log_id = wp_insert_post(array(
            'post_title'  => $action . ' - ' . current_time('mysql'),
            'post_type'   => 'activity_log',
            'post_status' => 'publish',
            'meta_input'  => array(
                '_log_action'      => sanitize_text_field($action),
                '_log_description' => sanitize_text_field($description),
                '_log_user_id'     => $user_id ? absint($user_id) : get_current_user_id(),
                '_log_ip'          => sanitize_text_field(self::get_client_ip()),
                '_log_user_agent'  => sanitize_text_field($_SERVER['HTTP_USER_AGENT'] ?? ''),
                '_log_timestamp'   => current_time('mysql'),
            ),
        ));
        return $log_id;
    }

    /**
     * Get client IP
     */
    public static function get_client_ip() {
        $keys = array('HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR');
        foreach ($keys as $k) {
            if (!empty($_SERVER[$k])) {
                $ips = explode(',', $_SERVER[$k]);
                return trim($ips[0]);
            }
        }
        return '0.0.0.0';
    }

    /**
     * Limit login attempts
     */
    public static function check_login_limit($username) {
        $ip     = self::get_client_ip();
        $key    = 'babarida_login_attempts_' . md5($ip . $username);
        $attempts = (int) get_transient($key);

        if ($attempts >= 5) {
            $remaining = get_transient($key . '_lockout');
            if ($remaining !== false) {
                return array('blocked' => true, 'remaining' => $remaining);
            }
            // Set 15 min lockout
            set_transient($key . '_lockout', 900, 900);
            set_transient($key, 0, 900);
            return array('blocked' => true, 'remaining' => 900);
        }

        return array('blocked' => false, 'attempts' => $attempts);
    }

    /**
     * Record failed login
     */
    public static function record_failed_login($username) {
        $key = 'babarida_login_attempts_' . md5(self::get_client_ip() . $username);
        $attempts = (int) get_transient($key) + 1;
        set_transient($key, $attempts, 900);
        self::log_activity('failed_login', 'Failed login attempt for: ' . $username);
    }

    /**
     * Clear login attempts on success
     */
    public static function clear_login_attempts($username) {
        $key = 'babarida_login_attempts_' . md5(self::get_client_ip() . $username);
        delete_transient($key);
        delete_transient($key . '_lockout');
    }

    /**
     * Sanitize file uploads
     */
    public static function sanitize_upload($file) {
        // Check file type
        $allowed = array('jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx');
        $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed, true)) {
            return new WP_Error('invalid_file_type', __('This file type is not allowed.', 'babarida'));
        }

        // Check size (max 10MB)
        if ($file['size'] > 10 * 1024 * 1024) {
            return new WP_Error('file_too_large', __('File size exceeds 10MB limit.', 'babarida'));
        }

        return $file;
    }

    /**
     * Get activity logs
     */
    public static function get_logs($args = array()) {
        $parsed = array(
            'post_type'   => 'activity_log',
            'numberposts' => 50,
            'orderby'     => 'date',
            'order'       => 'DESC',
        );

        if (!empty($args['action'])) {
            $parsed['meta_key']   = '_log_action';
            $parsed['meta_value'] = sanitize_text_field($args['action']);
        }
        if (!empty($args['user_id'])) {
            $parsed['meta_key']   = '_log_user_id';
            $parsed['meta_value'] = absint($args['user_id']);
        }
        if (!empty($args['per_page'])) {
            $parsed['numberposts'] = absint($args['per_page']);
        }
        if (!empty($args['date_from'])) {
            $parsed['date_query'] = array(array('after' => sanitize_text_field($args['date_from'])));
        }

        $posts = get_posts($parsed);
        $logs  = array();
        foreach ($posts as $p) {
            $user = get_user_by('id', get_post_meta($p->ID, '_log_user_id', true));
            $logs[] = array(
                'id'          => $p->ID,
                'action'      => get_post_meta($p->ID, '_log_action', true),
                'description' => get_post_meta($p->ID, '_log_description', true),
                'user'        => $user ? $user->display_name : __('System', 'babarida'),
                'ip'          => get_post_meta($p->ID, '_log_ip', true),
                'timestamp'   => get_post_meta($p->ID, '_log_timestamp', true),
            );
        }
        return $logs;
    }
}

// Login attempt hooks
add_action('wp_login_failed', function($username) {
    Babarida_Security::record_failed_login($username);
});
add_action('wp_login', function($username) {
    Babarida_Security::clear_login_attempts($username);
}, 10, 1);
add_filter('wp_authenticate', function($user, $username, $password) {
    if ($username) {
        $check = Babarida_Security::check_login_limit($username);
        if ($check['blocked']) {
            wp_die(__('Too many login attempts. Please try again in 15 minutes.', 'babarida'));
        }
    }
    return $user;
}, 10, 3);

// Upload sanitization
add_filter('wp_handle_upload_prefilter', function($file) {
    return Babarida_Security::sanitize_upload($file);
});

// Remove WordPress version from scripts/styles
add_filter('style_loader_src', 'babarida_remove_version_qs', 9999);
add_filter('script_loader_src', 'babarida_remove_version_qs', 9999);
function babarida_remove_version_qs($src) {
    if (is_admin()) return $src;
    return remove_query_arg('ver', $src);
}

// Disable XML-RPC
add_filter('xmlrpc_enabled', '__return_false');

// Disable unneeded REST endpoints
add_filter('rest_endpoints', function($endpoints) {
    unset($endpoints['/wp/v2/users']);
    unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
    return $endpoints;
});
