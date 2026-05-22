<?php
/**
 * Custom User Roles
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

class Babarida_Roles {

    public function __construct() {
        add_action('init', array($this, 'register_roles'), 10);
        add_filter('user_has_cap', array($this, 'map_capabilities'), 10, 4);
    }

    public function register_roles() {
        // General Manager
        remove_role('babarida_manager');
        add_role('babarida_manager', __('General Manager', 'babarida'), array(
            'read'                   => true,
            'edit_posts'             => true,
            'upload_files'           => true,
            'view_babarida_dashboard'=> true,
            'view_babarida_reports'  => true,
            'manage_babarida_trips'  => true,
            'manage_babarida_bookings'=> true,
        ));

        // Booking Staff
        remove_role('babarida_booking_staff');
        add_role('babarida_booking_staff', __('Booking Staff', 'babarida'), array(
            'read'                     => true,
            'edit_posts'               => true,
            'upload_files'             => true,
            'view_babarida_dashboard'  => true,
            'manage_babarida_bookings' => true,
        ));

        // Dive Guide
        remove_role('babarida_dive_guide');
        add_role('babarida_dive_guide', __('Dive Guide', 'babarida'), array(
            'read'                    => true,
            'view_babarida_dashboard' => true,
            'view_own_trips'          => true,
        ));

        // Hotel Partner
        remove_role('babarida_hotel_partner');
        add_role('babarida_hotel_partner', __('Hotel Partner', 'babarida'), array(
            'read'                    => true,
            'upload_files'            => true,
            'view_babarida_dashboard' => true,
            'manage_own_hotel'        => true,
        ));

        // Liveaboard Partner
        remove_role('babarida_liveaboard_partner', __('Liveaboard Partner', 'babarida'), array(
            'read'                    => true,
            'upload_files'            => true,
            'view_babarida_dashboard' => true,
            'manage_own_boats'        => true,
        ));

        // Content Editor
        remove_role('babarida_content_editor');
        add_role('babarida_content_editor', __('Content Editor', 'babarida'), array(
            'read'                   => true,
            'edit_posts'             => true,
            'edit_others_posts'      => true,
            'edit_private_posts'     => true,
            'publish_posts'          => true,
            'delete_posts'           => true,
            'upload_files'           => true,
            'view_babarida_dashboard'=> true,
            'manage_babarida_seo'    => true,
        ));

        // Finance Staff
        remove_role('babarida_finance');
        add_role('babarida_finance', __('Finance Staff', 'babarida'), array(
            'read'                    => true,
            'view_babarida_dashboard' => true,
            'view_babarida_reports'   => true,
            'manage_babarida_finance' => true,
            'export_babarida_data'    => true,
        ));
    }

    /**
     * Map custom capabilities to WordPress capabilities
     */
    public function map_capabilities($allcaps, $caps, $args, $user) {
        // Super admin / administrator gets everything
        if (in_array('administrator', $user->roles, true)) {
            $allcaps['view_babarida_dashboard'] = true;
            $allcaps['view_babarida_reports']   = true;
            $allcaps['manage_babarida_trips']   = true;
            $allcaps['manage_babarida_bookings']= true;
            $allcaps['manage_babarida_seo']     = true;
            $allcaps['manage_babarida_finance'] = true;
            $allcaps['export_babarida_data']    = true;
            return $allcaps;
        }

        $role = $user->roles[0] ?? '';

        // General Manager inherits reports + trips
        if ($role === 'babarida_manager') {
            $allcaps['manage_babarida_bookings'] = true;
        }

        return $allcaps;
    }

    /**
     * Get all custom roles
     */
    public static function get_roles() {
        return array(
            'babarida_manager'          => __('General Manager', 'babarida'),
            'babarida_booking_staff'    => __('Booking Staff', 'babarida'),
            'babarida_dive_guide'       => __('Dive Guide', 'babarida'),
            'babarida_hotel_partner'    => __('Hotel Partner', 'babarida'),
            'babarida_liveaboard_partner'=> __('Liveaboard Partner', 'babarida'),
            'babarida_content_editor'   => __('Content Editor', 'babarida'),
            'babarida_finance'          => __('Finance Staff', 'babarida'),
        );
    }

    /**
     * Get role display name
     */
    public static function role_label($role) {
        $roles = self::get_roles();
        return $roles[$role] ?? $role;
    }
}

new Babarida_Roles();
