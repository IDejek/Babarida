<?php
/**
 * Custom Taxonomies
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

class Babarida_Taxonomies {

    public function __construct() {
        add_action('init', array($this, 'register_taxonomies'));
    }

    public function register_taxonomies() {

        // Destination Taxonomy
        register_taxonomy('destination', array('trip', 'liveaboard', 'hotel', 'water_sport', 'dive_course'), array(
            'labels' => array(
                'name'          => __('Destinations', 'babarida'),
                'singular_name' => __('Destination', 'babarida'),
                'menu_name'     => __('Destinations', 'babarida'),
                'add_new_item'  => __('Add Destination', 'babarida'),
                'edit_item'     => __('Edit Destination', 'babarida'),
                'all_items'     => __('All Destinations', 'babarida'),
            ),
            'public'       => true,
            'hierarchical' => true,
            'rewrite'      => array('slug' => 'destination', 'with_front' => false),
            'show_in_rest' => true,
            'show_admin_column' => true,
        ));

        // Activity Type Taxonomy
        register_taxonomy('activity', array('trip'), array(
            'labels' => array(
                'name'          => __('Activity Types', 'babarida'),
                'singular_name' => __('Activity Type', 'babarida'),
                'menu_name'     => __('Activity Types', 'babarida'),
                'add_new_item'  => __('Add Activity Type', 'babarida'),
                'all_items'     => __('All Activity Types', 'babarida'),
            ),
            'public'       => true,
            'hierarchical' => false,
            'rewrite'      => array('slug' => 'activity', 'with_front' => false),
            'show_in_rest' => true,
            'show_admin_column' => true,
        ));

        // Boat Type Taxonomy
        register_taxonomy('boat_type', array('liveaboard'), array(
            'labels' => array(
                'name'          => __('Boat Types', 'babarida'),
                'singular_name' => __('Boat Type', 'babarida'),
                'add_new_item'  => __('Add Boat Type', 'babarida'),
                'all_items'     => __('All Boat Types', 'babarida'),
            ),
            'public'       => true,
            'hierarchical' => false,
            'rewrite'      => array('slug' => 'boat-type', 'with_front' => false),
            'show_in_rest' => true,
            'show_admin_column' => true,
        ));

        // Season Taxonomy
        register_taxonomy('season', array('trip', 'liveaboard'), array(
            'labels' => array(
                'name'          => __('Seasons', 'babarida'),
                'singular_name' => __('Season', 'babarida'),
                'add_new_item'  => __('Add Season', 'babarida'),
                'all_items'     => __('All Seasons', 'babarida'),
            ),
            'public'       => false,
            'hierarchical' => false,
            'show_ui'      => true,
            'show_admin_column' => true,
        ));

        // Certification Level Taxonomy
        register_taxonomy('certification', array('dive_course'), array(
            'labels' => array(
                'name'          => __('Certification Levels', 'babarida'),
                'singular_name' => __('Certification Level', 'babarida'),
                'add_new_item'  => __('Add Level', 'babarida'),
                'all_items'     => __('All Levels', 'babarida'),
            ),
            'public'       => true,
            'hierarchical' => true,
            'rewrite'      => array('slug' => 'certification', 'with_front' => false),
            'show_in_rest' => true,
            'show_admin_column' => true,
        ));
    }
}

new Babarida_Taxonomies();
