<?php
/**
 * Membership & Loyalty System
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

class Babarida_Loyalty {

    const LEVELS = array(
        'bronze'    => array('name' => 'Bronze Diver',  'min_points' => 0,     'discount' => 0),
        'silver'    => array('name' => 'Silver Diver',  'min_points' => 5000,  'discount' => 0.03),
        'gold'      => array('name' => 'Gold Diver',    'min_points' => 20000, 'discount' => 0.05),
        'platinum'  => array('name' => 'Platinum Diver','min_points' => 50000, 'discount' => 0.08),
    );

    /**
     * Get level info
     */
    public static function get_level($level_key) {
        return self::LEVELS[$level_key] ?? self::LEVELS['bronze'];
    }

    /**
     * Determine level from points
     */
    public static function determine_level($points) {
        foreach (array_reverse(self::LEVELS, true) as $key => $level) {
            if ($points >= $level['min_points']) {
                return $key;
            }
        }
        return 'bronze';
    }

    /**
     * Get loyalty discount for user
     */
    public static function get_discount($user_id) {
        $points = (int) get_user_meta($user_id, '_customer_loyalty_points', true);
        $level  = self::determine_level($points);
        return self::LEVELS[$level]['discount'];
    }

    /**
     * Redeem points for discount
     */
    public static function redeem_points($user_id, $points_to_redeem) {
        $current = (int) get_user_meta($user_id, '_customer_loyalty_points', true);
        if ($points_to_redeem > $current) {
            return array('success' => false, 'message' => __('Insufficient points.', 'babarida'));
        }

        $new_points = $current - $points_to_redeem;
        update_user_meta($user_id, '_customer_loyalty_points', $new_points);
        update_user_meta($user_id, '_customer_loyalty_level', self::determine_level($new_points));

        // 100 points = $1 discount
        $discount_value = $points_to_redeem / 100;

        return array(
            'success' => true,
            'discount'=> round($discount_value, 2),
            'remaining'=> $new_points,
            'level'   => self::determine_level($new_points),
        );
    }

    /**
     * Get progress to next level
     */
    public static function get_progress($user_id) {
        $points = (int) get_user_meta($user_id, '_customer_loyalty_points', true);
        $current_level = self::determine_level($points);

        $levels = array_keys(self::LEVELS);
        $current_idx = array_search($current_level, $levels);
        $next_idx    = $current_idx + 1;

        if ($next_idx >= count($levels)) {
            return array('current' => $current_level, 'next' => null, 'progress' => 100);
        }

        $next_level      = $levels[$next_idx];
        $next_min        = self::LEVELS[$next_level]['min_points'];
        $current_min     = self::LEVELS[$current_level]['min_points'];
        $range           = $next_min - $current_min;
        $progress_points = $points - $current_min;
        $percentage      = min(100, round(($progress_points / $range) * 100));

        return array(
            'current'     => $current_level,
            'next'        => $next_level,
            'progress'    => $percentage,
            'points_needed'=> max(0, $next_min - $points),
        );
    }
}
