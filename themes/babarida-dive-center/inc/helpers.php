<?php
/**
 * Helper Functions
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

/**
 * Calculate reading time
 */
function babarida_reading_time() {
    $content = get_post_field('post_content', get_the_ID());
    $words = str_word_count(strip_tags($content));
    $minutes = max(1, ceil($words / 200));
    return $minutes . ' min read';
}

/**
 * Get archive title
 */
function babarida_get_archive_title($post_type) {
    if ($post_type) {
        $obj = get_post_type_object($post_type);
        if ($obj) return $obj->labels->name;
    }
    return __('Archives', 'babarida');
}

/**
 * Safe escape for HTML attributes
 */
function babarida_esc($str) {
    return esc_html($str);
}

/**
 * Get option with fallback
 */
function babarida_opt($key, $default = '') {
    return get_option($key, $default);
}
