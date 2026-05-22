<?php
/**
 * Dynamic Pricing Engine
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

class Babarida_Pricing {

    /**
     * Get season for a given month number (1-12)
     */
    public static function get_season($month) {
        if (in_array((int) $month, array(6, 7, 8), true)) return 'peak';
        if (in_array((int) $month, array(3, 4, 5, 9, 10), true)) return 'high';
        return 'low';
    }

    /**
     * Get base prices by season
     */
    public static function get_base_prices($season) {
        $prices = array(
            'low'  => array(
                'day_trip' => 65, 'liveaboard_3n' => 1800, 'liveaboard_5n' => 2800,
                'liveaboard_7n' => 3800, 'ssi_ow' => 380, 'ssi_aow' => 300,
                'snorkeling' => 40, 'water_sport' => 25,
            ),
            'high' => array(
                'day_trip' => 85, 'liveaboard_3n' => 2200, 'liveaboard_5n' => 3400,
                'liveaboard_7n' => 4600, 'ssi_ow' => 420, 'ssi_aow' => 340,
                'snorkeling' => 55, 'water_sport' => 30,
            ),
            'peak' => array(
                'day_trip' => 110, 'liveaboard_3n' => 2800, 'liveaboard_5n' => 4200,
                'liveaboard_7n' => 5600, 'ssi_ow' => 480, 'ssi_aow' => 380,
                'snorkeling' => 70, 'water_sport' => 35,
            ),
        );
        return $prices[$season] ?? $prices['low'];
    }

    /**
     * Get price for a specific date and product type
     */
    public static function get_price($date, $product_type) {
        $month  = (int) date('n', strtotime($date));
        $season = self::get_season($month);
        $prices = self::get_base_prices($season);

        // Check for admin price override
        $custom = get_option('babarida_pricing_' . $date . '_' . $product_type);
        if ($custom !== false && $custom !== '') {
            return floatval($custom);
        }

        return $prices[$product_type] ?? 0;
    }

    /**
     * Apply coupon code
     */
    public static function apply_coupon($code, $subtotal) {
        $code    = strtoupper(sanitize_text_field($code));
        $coupons = get_option('babarida_coupons', array());

        if (!isset($coupons[$code])) {
            return array('valid' => false, 'message' => __('Invalid coupon code.', 'babarida'));
        }

        $coupon = $coupons[$code];

        if (!empty($coupon['expiry']) && strtotime($coupon['expiry']) < current_time('timestamp')) {
            return array('valid' => false, 'message' => __('This coupon has expired.', 'babarida'));
        }

        if (!empty($coupon['usage_limit']) && intval($coupon['used'] ?? 0) >= intval($coupon['usage_limit'])) {
            return array('valid' => false, 'message' => __('This coupon has reached its usage limit.', 'babarida'));
        }

        $discount = 0;
        if ($coupon['type'] === 'percentage') {
            $discount = $subtotal * (floatval($coupon['value']) / 100);
        } elseif ($coupon['type'] === 'fixed') {
            $discount = min(floatval($coupon['value']), $subtotal);
        }

        return array(
            'valid'    => true,
            'discount' => round($discount, 2),
            'total'    => round($subtotal - $discount, 2),
            'message'  => sprintf(__('Coupon applied! You saved $%s.', 'babarida'), number_format($discount, 2)),
        );
    }

    /**
     * Get group discount percentage
     */
    public static function get_group_discount($guest_count) {
        if ($guest_count >= 10) return 0.15;
        if ($guest_count >= 6)  return 0.10;
        if ($guest_count >= 4)  return 0.05;
        return 0;
    }

    /**
     * Get early bird discount percentage
     */
    public static function get_early_bird_discount($booking_date, $trip_date) {
        $days = (strtotime($trip_date) - strtotime($booking_date)) / 86400;
        if ($days >= 90) return 0.10;
        if ($days >= 60) return 0.05;
        return 0;
    }

    /**
     * Generate 24-month pricing table data
     */
    public static function get_pricing_table() {
        $data        = array();
        $now         = new DateTime();
        $month_names = array(
            'January','February','March','April','May','June',
            'July','August','September','October','November','December'
        );
        $season_labels = array(
            'low'  => __('Low Season', 'babarida'),
            'high' => __('High Season', 'babarida'),
            'peak' => __('Peak Season', 'babarida'),
        );
        $season_classes = array(
            'low'  => 'season-low',
            'high' => 'season-high',
            'peak' => 'season-peak',
        );

        for ($m = 0; $m < 24; $m++) {
            $date      = clone $now;
            $date->modify('+' . $m . ' months');
            $month_num = (int) $date->format('n');
            $year      = (int) $date->format('Y');
            $season    = self::get_season($month_num);
            $prices    = self::get_base_prices($season);

            $data[] = array(
                'month'        => $month_names[$month_num - 1] . ' ' . $year,
                'season'       => $season,
                'season_label' => $season_labels[$season],
                'season_class' => $season_classes[$season],
                'day_trip'     => $prices['day_trip'],
                'liveaboard'   => $prices['liveaboard_3n'],
                'course'       => $prices['ssi_ow'],
            );
        }
        return $data;
    }
}
