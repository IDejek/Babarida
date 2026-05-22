<?php
/**
 * Custom Widgets
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

class Babarida_Recent_Trips_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'babarida_recent_trips',
            __('Recent Trips', 'babarida'),
            array('description' => __('Display recent trip packages.', 'babarida'))
        );
    }

    public function widget($args, $instance) {
        $count = isset($instance['count']) ? absint($instance['count']) : 5;
        $trips = get_posts(array(
            'post_type'   => 'trip',
            'numberposts' => $count,
            'post_status' => 'publish',
        ));

        echo $args['before_widget'];
        if (!empty($instance['title'])) {
            echo $args['before_title'] . esc_html($instance['title']) . $args['after_title'];
        }

        if (!empty($trips)) {
            echo '<ul style="list-style:none; display:flex; flex-direction:column; gap:12px;">';
            foreach ($trips as $trip) {
                echo '<li style="padding:12px; background:var(--gray-50); border-radius:var(--radius-md);">';
                echo '<a href="' . esc_url(get_permalink($trip->ID)) . '" style="font-size:0.85rem; font-weight:600; color:var(--blue-deep); display:block;">' . esc_html($trip->post_title) . '</a>';
                if (has_post_thumbnail($trip->ID)) {
                    echo '<a href="' . esc_url(get_permalink($trip->ID)) . '" style="display:block; margin-top:8px; border-radius:var(--radius-sm); overflow:hidden; height:80px;">';
                    echo get_the_post_thumbnail($trip->ID, 'gallery-thumb', array('style' => 'width:100%;height:100%;object-fit:cover;'));
                    echo '</a>';
                }
                echo '</li>';
            }
            echo '</ul>';
        }

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Recent Trips', 'babarida');
        $count  = isset($instance['count']) ? $instance['count'] : 5;
        echo '<p><label for="' . $this->get_field_id('title') . '">' . esc_html__('Title:', 'babarida') . '</label>';
        echo '<input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . esc_attr($title) . '"></p>';
        echo '<p><label for="' . $this->get_field_id('count') . '">' . esc_html__('Number of trips:', 'babarida') . '</label>';
        echo '<input class="widefat" id="' . $this->get_field_id('count') . '" name="' . $this->get_field_name('count') . '" type="number" value="' . esc_attr($count) . '" min="1" max="20"></p>';
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['count'] = absint($new_instance['count']);
        return $instance;
    }
}

class Babarida_Contact_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'babarida_contact',
            __('Contact Info', 'babarida'),
            array('description' => __('Display contact information.', 'babarida'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        if (!empty($instance['title'])) {
            echo $args['before_title'] . esc_html($instance['title']) . $args['after_title'];
        }
        echo '<div style="display:flex; flex-direction:column; gap:12px;">';
        echo '<div style="display:flex; align-items:center; gap:10px; font-size:0.85rem;"><i class="fa-brands fa-whatsapp" style="color:var(--blue-primary);"></i> +62 895 8019 60359</div>';
        echo '<div style="display:flex; align-items:center; gap:10px; font-size:0.85rem;"><i class="fa-solid fa-envelope" style="color:var(--blue-primary);"></i> info@babaridadive.com</div>';
        echo '<div style="display:flex; align-items:flex-start; gap:10px; font-size:0.85rem;"><i class="fa-solid fa-location-dot" style="color:var(--blue-primary); margin-top:3px;"></i> Bunaken National Marine Park, Manado</div>';
        echo '</div>';
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Contact Us', 'babarida');
        echo '<p><label for="' . $this->get_field_id('title') . '">' . esc_html__('Title:', 'babarida') . '</label>';
        echo '<input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . esc_attr($title) . '"></p>';
    }

    public function update($new_instance, $old_instance) {
        return array('title' => sanitize_text_field($new_instance['title']));
    }
}

// Register widgets
add_action('widgets_init', function() {
    register_widget('Babarida_Recent_Trips_Widget');
    register_widget('Babarida_Contact_Widget');
});
