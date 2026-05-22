<?php
/**
 * Custom Post Types Registration
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

class Babarida_CPT {

    public function __construct() {
        add_action('init', array($this, 'register_post_types'));
    }

    public function register_post_types() {

        // Trip / Product CPT
        register_post_type('trip', array(
            'labels' => array(
                'name'               => __('Trips & Packages', 'babarida'),
                'singular_name'      => __('Trip', 'babarida'),
                'menu_name'          => __('Trips & Packages', 'babarida'),
                'add_new'            => __('Add Trip', 'babarida'),
                'add_new_item'       => __('Add New Trip', 'babarida'),
                'edit_item'          => __('Edit Trip', 'babarida'),
                'new_item'           => __('New Trip', 'babarida'),
                'view_item'          => __('View Trip', 'babarida'),
                'search_items'       => __('Search Trips', 'babarida'),
                'not_found'          => __('No trips found.', 'babarida'),
                'not_found_in_trash' => __('No trips found in trash.', 'babarida'),
                'all_items'          => __('All Trips', 'babarida'),
                'archives'           => __('Trip Archives', 'babarida'),
            ),
            'public'       => true,
            'has_archive'  => true,
            'rewrite'      => array('slug' => 'trips', 'with_front' => false),
            'supports'     => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'),
            'menu_icon'    => 'dashicons-clipboard',
            'menu_position'=> 5,
            'show_in_rest' => true,
        ));

        // Liveaboard CPT
        register_post_type('liveaboard', array(
            'labels' => array(
                'name'               => __('Liveaboards', 'babarida'),
                'singular_name'      => __('Liveaboard', 'babarida'),
                'menu_name'          => __('Liveaboards', 'babarida'),
                'add_new'            => __('Add Liveaboard', 'babarida'),
                'add_new_item'       => __('Add New Liveaboard', 'babarida'),
                'edit_item'          => __('Edit Liveaboard', 'babarida'),
                'view_item'          => __('View Liveaboard', 'babarida'),
                'all_items'          => __('All Liveaboards', 'babarida'),
            ),
            'public'       => true,
            'has_archive'  => true,
            'rewrite'      => array('slug' => 'liveaboards', 'with_front' => false),
            'supports'     => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'),
            'menu_icon'    => 'dashicons-anchor',
            'menu_position'=> 6,
            'show_in_rest' => true,
        ));

        // Hotel Partner CPT
        register_post_type('hotel', array(
            'labels' => array(
                'name'               => __('Hotel Partners', 'babarida'),
                'singular_name'      => __('Hotel', 'babarida'),
                'menu_name'          => __('Hotel Partners', 'babarida'),
                'add_new'            => __('Add Hotel', 'babarida'),
                'add_new_item'       => __('Add New Hotel', 'babarida'),
                'edit_item'          => __('Edit Hotel', 'babarida'),
                'all_items'          => __('All Hotels', 'babarida'),
            ),
            'public'       => true,
            'has_archive'  => true,
            'rewrite'      => array('slug' => 'hotels', 'with_front' => false),
            'supports'     => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'menu_icon'    => 'dashicons-building',
            'menu_position'=> 7,
            'show_in_rest' => true,
        ));

        // Testimonial CPT
        register_post_type('testimonial', array(
            'labels' => array(
                'name'               => __('Testimonials', 'babarida'),
                'singular_name'      => __('Testimonial', 'babarida'),
                'menu_name'          => __('Testimonials', 'babarida'),
                'add_new'            => __('Add Testimonial', 'babarida'),
                'add_new_item'       => __('Add New Testimonial', 'babarida'),
                'all_items'          => __('All Testimonials', 'babarida'),
            ),
            'public'       => true,
            'has_archive'  => false,
            'rewrite'      => array('slug' => 'testimonials', 'with_front' => false),
            'supports'     => array('title', 'editor', 'thumbnail', 'custom-fields'),
            'menu_icon'    => 'dashicons-format-quote',
            'menu_position'=> 8,
            'show_in_rest' => true,
        ));

        // Partner CPT
        register_post_type('partner', array(
            'labels' => array(
                'name'          => __('Partners', 'babarida'),
                'singular_name' => __('Partner', 'babarida'),
                'menu_name'     => __('Partners', 'babarida'),
                'add_new'       => __('Add Partner', 'babarida'),
                'all_items'     => __('All Partners', 'babarida'),
            ),
            'public'       => true,
            'has_archive'  => false,
            'rewrite'      => array('slug' => 'partners', 'with_front' => false),
            'supports'     => array('title', 'thumbnail', 'custom-fields'),
            'menu_icon'    => 'dashicons-handshake',
            'menu_position'=> 9,
            'show_in_rest' => true,
        ));

        // FAQ CPT
        register_post_type('faq', array(
            'labels' => array(
                'name'          => __('FAQs', 'babarida'),
                'singular_name' => __('FAQ', 'babarida'),
                'menu_name'     => __('FAQs', 'babarida'),
                'add_new'       => __('Add FAQ', 'babarida'),
                'all_items'     => __('All FAQs', 'babarida'),
            ),
            'public'       => true,
            'has_archive'  => false,
            'rewrite'      => array('slug' => 'faq', 'with_front' => false),
            'supports'     => array('title', 'editor', 'custom-fields', 'page-attributes'),
            'menu_icon'    => 'dashicons-editor-help',
            'menu_position'=> 10,
            'show_in_rest' => true,
        ));

        // Water Sport CPT
        register_post_type('water_sport', array(
            'labels' => array(
                'name'          => __('Water Sports', 'babarida'),
                'singular_name' => __('Water Sport', 'babarida'),
                'menu_name'     => __('Water Sports', 'babarida'),
                'add_new'       => __('Add Activity', 'babarida'),
                'all_items'     => __('All Activities', 'babarida'),
            ),
            'public'       => true,
            'has_archive'  => true,
            'rewrite'      => array('slug' => 'water-sports', 'with_front' => false),
            'supports'     => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'menu_icon'    => 'dashicons-admin-site',
            'menu_position'=> 11,
            'show_in_rest' => true,
        ));

        // Dive Course CPT
        register_post_type('dive_course', array(
            'labels' => array(
                'name'          => __('Dive Courses', 'babarida'),
                'singular_name' => __('Dive Course', 'babarida'),
                'menu_name'     => __('Dive Courses', 'babarida'),
                'add_new'       => __('Add Course', 'babarida'),
                'all_items'     => __('All Courses', 'babarida'),
            ),
            'public'       => true,
            'has_archive'  => true,
            'rewrite'      => array('slug' => 'dive-courses', 'with_front' => false),
            'supports'     => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'menu_icon'    => 'dashicons-welcome-learn-more',
            'menu_position'=> 12,
            'show_in_rest' => true,
        ));

        // Booking CPT
        register_post_type('booking', array(
            'labels' => array(
                'name'               => __('Bookings', 'babarida'),
                'singular_name'      => __('Booking', 'babarida'),
                'menu_name'          => __('Bookings', 'babarida'),
                'add_new'            => __('Add Booking', 'babarida'),
                'add_new_item'       => __('Add New Booking', 'babarida'),
                'edit_item'          => __('Edit Booking', 'babarida'),
                'view_item'          => __('View Booking', 'babarida'),
                'all_items'          => __('All Bookings', 'babarida'),
                'search_items'       => __('Search Bookings', 'babarida'),
            ),
            'public'       => false,
            'show_ui'      => true,
            'supports'     => array('title', 'editor', 'custom-fields'),
            'menu_icon'    => 'dashicons-calendar-alt',
            'menu_position'=> 4,
            'show_in_rest' => false,
            'capability_type' => 'booking',
            'map_meta_cap'    => true,
        ));
    }
}

new Babarida_CPT();
