<?php
/**
 * Plugin Name: Babarida Book CPT & Metaboxes
 * Plugin URI: https://babaridadive.com
 * Description: Registers custom post types, metaboxes, taxonomies, shortcodes, and AJAX handlers for Babarida Dive Center.
 * Version: 1.0.0
 * Author: Iqbal Tombinawa
 * Author URI: https://babaridadive.com
 * License: GPL v2 or later
 * Requires at least: 6.0
 * Requires PHP: 8.0
 */

defined('ABSPATH') || exit;

class Babarida_Book_CPT_Plugin {

    public function __construct() {
        // Load after theme (priority 20)
        add_action('after_setup_theme', array($this, 'init'), 20);
    }

    public function init() {
        // Register metaboxes
        add_action('add_meta_boxes', array($this, 'register_metaboxes'));

        // Save metaboxes
        add_action('save_post', array($this, 'save_metaboxes'), 10, 2);

        // Add menu icon support for CPTs (if theme doesn't handle it)
        add_filter('post_type_labels_' . 'payment', array($this, 'payment_labels'));
        add_filter('post_type_labels_' . 'activity_log', array($this, 'activity_log_labels'));
        add_filter('post_type_labels_' . 'internal_chat', array($this, 'internal_chat_labels'));
        add_filter('post_type_labels_' . 'waiver', array($this, 'waiver_labels'));
        add_filter('post_type_labels_' . 'newsletter_subscriber', array($this, 'subscriber_labels'));

        // Register shortcodes
        add_action('init', array($this, 'register_shortcodes'));

        // AJAX handlers are in the theme's class-babarida-ajax.php
        // This plugin ensures CPTs are registered even without theme activation
        add_action('init', array($this, 'ensure_cpts_registered'), 1);

        // REST API support for CPTs
        add_action('init', array($this, 'add_rest_support'), 5);

        // Add thumbnail support for non-public CPTs
        add_action('after_setup_theme', array($this, 'add_theme_support'));
    }

    /**
     * Register metaboxes for all CPTs
     */
    public function register_metaboxes() {

        // Trip / Product Metabox
        add_meta_box('babarida_trip_details', __('Trip Details', 'babarida'), array($this, 'trip_metabox_callback'), 'trip', 'normal', 'high');

        // Liveaboard Metabox
        add_meta_box('babarida_liveaboard_details', __('Liveaboard Details', 'babarida'), array($this, 'liveaboard_metabox_callback'), 'liveaboard', 'normal', 'high');

        // Hotel Metabox
        add_meta_box('babarida_hotel_details', __('Hotel Details', 'babarida'), array($this, 'hotel_metabox_callback'), 'hotel', 'normal', 'high');

        // Testimonial Metabox
        add_meta_box('babarida_testimonial_details', __('Testimonial Details', 'babarida'), array($this, 'testimonial_metabox_callback'), 'testimonial', 'side', 'high');

        // FAQ Metabox (built-in WordPress uses excerpt)
        // Already handled by content editor

        // Booking Metabox
        add_meta_box('babarida_booking_details', __('Booking Details', 'babarida'), array($this, 'booking_metabox_callback'), 'booking', 'normal', 'high');

        // Water Sport Metabox
        add_meta_box('babarida_watersport_details', __('Activity Details', 'babarida'), array($this, 'watersport_metabox_callback'), 'water_sport', 'normal', 'high');

        // Dive Course Metabox
        add_meta_box('babarida_course_details', __('Course Details', 'babarida'), array($this, 'course_metabox_callback'), 'dive_course', 'normal', 'high');

        // Destination term meta
        add_action('destination_edit_form_fields', array($this, 'destination_term_fields'), 10, 2);
        add_action('edited_destination', array($this, 'save_destination_meta'), 10, 2);
        add_action('create_term', array($this, 'create_destination_term_meta'), 10, 3);

        // Menu item meta (icon + description for mega menu)
        add_action('wp_nav_menu_item_custom_fields', array($this, 'menu_item_fields'));
        add_action('wp_update_nav_menu_item', array($this, 'save_menu_item_meta'), 10, 3);

        // Hero section admin control
        add_action('customize_register', array($this, 'customize_hero_options'));

        // Gallery support for posts
        add_post_type_support('trip', array('gallery'));
        add_post_type_support('liveaboard', array('gallery'));
        add_post_type_support('hotel', array('gallery'));
        add_post_type_support('water_sport', array('gallery'));
        add_post_type_support('dive_course', array('gallery'));
    }

    /**
     * Trip Metabox Callback
     */
    public function trip_metabox_callback($post) {
        wp_nonce_field('babarida_trip_nonce', 'babarida_trip_nonce_field');
        $values = array(
            '_trip_price'        => get_post_meta($post->ID, '_trip_price', true),
            '_trip_price_idr'    => get_post_meta($post->ID, '_trip_price_idr', true),
            '_trip_duration'     => get_post_meta($post->ID, '_trip_duration', true),
            '_trip_max_guests'   => get_post_meta($post->ID, '_trip_max_guests', true),
            '_trip_includes'     => get_post_meta($post->ID, '_trip_includes', true),
            '_trip_excludes'     => get_post_meta($post->ID, '_trip_excludes', true),
            '_trip_itinerary'    => get_post_meta($post->ID, '_trip_itinerary', true),
            '_trip_gallery'     => get_post_meta($post->ID, '_trip_gallery', true),
            '_trip_video'       => get_post_meta($post->ID, '_trip_video', true),
            '_trip_season_tag'  => get_post_meta($post->ID, '_trip_season_tag', true),
            '_trip_badge_text'  => get_post_meta($post->ID, '_trip_badge_text', true),
            '_trip_badge_color' => get_post_meta($post->ID, '_trip_badge_color', true),
        );
        $this->render_metabox_fields($post, 'trip', $values);
        ?>
        <div class="babarida-metabox-section">
            <h4 style="margin:16px 0 10px;font-size:0.85rem;font-weight:600;color:#001A33;">Media</h4>
            <div class="babarida-mb-field">
                <label><?php esc_html_e('Video URL (optional)', 'babarida'); ?></label>
                <input type="url" name="_trip_video" value="<?php echo esc_attr($values['_trip_video']); ?>" class="large-text" style="width:100%;">
            </div>
            <div class="babarida-mb-field">
                <label><?php esc_html_e('Season Badge Text', 'babarida'); ?></label>
                <input type="text" name="_trip_badge_text" value="<?php echo esc_attr($values['_trip_badge_text']); ?>" style="width:100%;">
            </div>
            <div class="babarida-mb-field">
                <label><?php esc_html_e('Badge Color', 'babarida'); ?></label>
                <input type="color" name="_trip_badge_color" value="<?php echo esc_attr($values['_trip_badge_color']); ?>" style="width:60px;height:36px;padding:2px;border:1px solid #E2E8F0;border-radius:6px;cursor:pointer;">
            </div>
        </div>
        <?php
    }

    /**
     * Liveaboard Metabox Callback
     */
    public function liveaboard_metabox_callback($post) {
        $values = array(
            '_liveaboard_price'    => get_post_meta($post->ID, '_liveaboard_price', true),
            '_liveaboard_price_idr'=> get_post_meta($post->ID, '_liveaboard_price_idr', true),
            '_liveaboard_cabins'  => get_post_meta($post->ID, '_liveaboard_cabins', true),
            '_liveaboard_guests'  => get_post_meta($post->ID, '_liveaboard_guests', true),
            '_liveaboard_length'  => get_post_meta($post->ID, '_liveaboard_length', true),
            '_liveaboard_nights'  => get_post_meta($post->ID, '_liveaboard_nights', true),
            '_liveaboard_route'   => get_post_meta($post->ID, '_liveaboard_route', true),
            '_liveaboard_badge'  => get_post_meta($post->ID, '_liveaboard_badge', true),
            '_liveaboard_specifications' => get_post_meta($post->ID, '_liveaboard_specifications', true),
            '_liveaboard_deck_plan' => get_post_meta($post->ID, '_liveaboard_deck_plan', true),
            '_liveaboard_video'  => get_post_meta($post->ID, '_liveaboard_video', true),
        );
        $this->render_metabox_fields($post, 'liveaboard', $values);
        ?>
        <div class="babarida-metabox-section">
            <h4 style="margin:16px 0 10px;font-size:0.85rem;font-weight:600;color:#001A33;">Additional Info</h4>
            <div class="babarida-mb-field">
                <label><?php esc_html_e('Specifications (e.g., Engine: Yanmar 300HP, Speed: 10 knots)', 'babarida'); ?></label>
                <textarea name="_liveaboard_specifications" rows="3" style="width:100%;padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;"><?php echo esc_textarea($values['_liveaboard_specifications']); ?></textarea>
            </div>
            <div class="babarida-mb-field">
                <label><?php esc_html_e('Video URL', 'babarida'); ?></label>
                <input type="url" name="_liveaboard_video" value="<?php echo esc_attr($values['_liveaboard_video']); ?>" style="width:100%;padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;">
            </div>
        </div>
        <?php
    }

    /**
     * Hotel Metabox Callback
     */
    public function hotel_metabox_callback($post) {
        $values = array(
            '_hotel_price'      => get_post_meta($post->ID, '_hotel_price', true),
            '_hotel_stars'      => get_post_meta($post->ID, '_hotel_stars', true),
            '_hotel_location'   => get_post_meta($post->ID, '_hotel_location', true),
            '_hotel_amenities'  => get_post_meta($post->ID, '_hotel_amenities', true),
            '_hotel_room_types' => get_post_meta($post->ID, '_hotel_room_types', true),
            '_hotel_map_lat'   => get_post_meta($post->ID, '_hotel_map_lat', true),
            '_hotel_map_lng'   => get_post_meta($post->ID, '_hotel_map_lng', true),
        );
        ?>
        <div class="babarida-metabox-section">
            <h4 style="margin:0 0 10px;font-size:0.85rem;font-weight:600;color:#001A33;">Hotel-Specific Fields</h4>
            <div class="babarida-mb-field">
                <label><?php esc_html_e('Star Rating', 'babarida'); ?></label>
                <select name="_hotel_stars" style="width:100%;padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;">
                    <option value="" <?php selected(!$values['_hotel_stars'], true); ?>>Select</option>
                    <?php for ($s = 1; $s <= 5; $s++) : ?>
                    <option value="<?php echo $s; ?>" <?php selected(intval($values['_hotel_stars']) === $s, true); ?>><?php echo str_repeat('★', $s); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="babarida-mb-field">
                <label><?php esc_html_e('Amenities (comma separated)', 'babarida'); ?></label>
                <input type="text" name="_hotel_amenities" value="<?php echo esc_attr($values['_hotel_amenities']); ?>" placeholder="Pool, Spa, WiFi, Restaurant, Transfer..." style="width:100%;padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;">
            </div>
            <div class="babarida-mb-field">
                <label><?php esc_html_e('Room Types (comma separated)', 'babarida'); ?></label>
                <input type="text" name="_hotel_room_types" value="<?php echo esc_attr($values['_hotel_room_types']); ?>" placeholder="Standard, Deluxe, Suite, Family..." style="width:100%;padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;">
            </div>
            <div class="babarida-mb-row">
                <div class="babarida-mb-field">
                    <label><?php esc_html_e('Map Latitude', 'babarida'); ?></label>
                    <input type="text" name="_hotel_map_lat" value="<?php echo esc_attr($values['_hotel_map_lat']); ?>" placeholder="1.4748" style="width:100%;padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;">
                </div>
                <div class="babarida-mb-field">
                    <label><?php esc_html_e('Map Longitude', 'babarida'); ?></label>
                    <input type="text" name="_hotel_map_lng" value="<?php echo esc_attr($values['_hotel_map_lng']); ?>" placeholder="124.8421" style="width:100%;padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;">
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Testimonial Metabox Callback
     */
    public function testimonial_metabox_callback($post) {
        $values = array(
            '_testi_stars'    => get_post_meta($post->ID, '_testi_stars', true),
            '_testi_country'  => get_post_meta($post->ID, '_testi_country', true),
            '_testi_city'     => get_post_meta($post->ID, '_testi_city', true),
            '_testi_flag'     => get_post_meta($post->ID, '_testi_flag', true),
            '_testi_avatar'   => get_post_meta($post->ID, '_testi_avatar', true),
        );
        ?>
        <div class="babarida-metabox-section">
            <div class="babarida-mb-field">
                <label><?php esc_html_e('Star Rating', 'babarida'); ?></label>
                <input type="number" name="_testi_stars" value="<?php echo esc_attr($values['_testi_stars']); ?>" min="1" max="5" step="0.5" style="width:80px;padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;">
            </div>
            <div class="babarida-mb-row">
                <div class="babarida-mb-field">
                    <label><?php esc_html_e('Country', 'babarida'); ?></label>
                    <input type="text" name="_testi_country" value="<?php echo esc_attr($values['_testi_country']); ?>" placeholder="Germany" style="width:100%;padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;">
                </div>
                <div class="babarida-mb-field">
                    <label><?php esc_html_e('City', 'babarida'); ?></label>
                    <input type="text" name="_testi_city" value="<?php echo esc_attr($values['_testi_city']); ?>" placeholder="Munich" style="width:100%;padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;">
                </div>
            </div>
            <div class="babarida-mb-row">
                <div class="babarida-mb-field">
                    <label><?php esc_html_e('Country Flag Code (2-letter)', 'babarida'); ?></label>
                    <input type="text" name="_testi_flag" value="<?php echo esc_attr($values['_testi_flag']); ?>" placeholder="de" maxlength="2" style="width:80px;padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;text-transform:uppercase;">
                </div>
                <div class="babarida-mb-field">
                    <label><?php esc_html_e('Avatar Image ID', 'babarida'); ?></label>
                    <input type="number" name="_testi_avatar" value="<?php echo esc_attr($values['_testi_avatar']); ?>" style="width:100%;padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;">
                    <small style="display:block;color:#94A3B8;margin-top:2px;">WordPress media attachment ID</small>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Booking Metabox Callback
     */
    public function booking_metabox_callback($post) {
        $values = array(
            '_booking_total'       => get_post_meta($post->ID, '_booking_total', true),
            '_booking_paid'        => get_post_meta($post->ID, '_booking_paid', true),
            '_booking_payment_method'=> get_post_meta($post->ID, '_booking_payment_method', true),
            '_booking_guide'       => get_post_meta($post->ID, '_booking_guide', true),
            '_booking_boat'        => get_post_meta($post->ID, '_booking_boat', true),
            '_booking_notes_admin' => get_post_meta($post->ID, '_booking_notes_admin', true),
        );
        ?>
        <div class="babarida-metabox-section">
            <h4 style="margin:0 0 10px;font-size:0.85rem;font-weight:600;color:#001A33;">Booking Management</h4>
            <div class="babarida-mb-row">
                <div class="babarida-mb-field">
                    <label><?php esc_html_e('Total Amount ($)', 'babarida'); ?></label>
                    <input type="number" name="_booking_total" value="<?php echo esc_attr($values['_booking_total']); ?>" step="0.01" min="0" style="width:100%;padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;">
                </div>
                <div class="babarida->_booking_paid_field">
                    <label><?php esc_html_e('Amount Paid ($)', 'babarida'); ?></label>
                    <input type="number" name="_booking_paid" value="<?php echo esc_attr($values['_booking_paid']); ?>" step="0.01" min="0" style="width:100%;padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;">
                </div>
            </div>
            <div class="babarida-mb-row">
                <div class="babarida-mb-field">
                    <label><?php esc_html_e('Payment Method', 'babarida'); ?></label>
                    <select name="_booking_payment_method" style="width:100%;padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;">
                        <option value="" <?php selected(!$values['_booking_payment_method'], true); ?>>None</option>
                        <option value="midtrans" <?php selected($values['_booking_payment_method'] === 'midtrans', true); ?>>Midtrans</option>
                        <option value="xendit" <?php selected($values['_booking_payment_method'] === 'xendit', true); ?>>Xendit</option>
                        <option value="stripe" <?php selected($values['_booking_payment_method'] === 'stripe', true); ?>>Stripe</option>
                        <option value="paypal" <?php selected($values['_booking_payment_method'] === 'paypal', true); ?>>PayPal</option>
                        <option value="bank_transfer" <?php selected($values['_booking_payment_method'] === 'bank_transfer', true); ?>>Bank Transfer</option>
                    </select>
                </div>
                <div class="babarida-mb-field">
                    <label><?php esc_html_e('Assigned Guide (User ID)', 'babarida'); ?></label>
                    <input type="number" name="_booking_guide" value="<?php echo esc_attr($values['_booking_guide']); ?>" style="width:100%;padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;">
                </div>
            </div>
            <div class="babarida-mb-field">
                <label><?php esc_html_e('Assigned Boat (Post ID)', 'babarida'); ?></label>
                <input type="number" name="_booking_boat" value="<?php echo esc_attr($values['_booking_boat']); ?>" style="width:100%;padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;">
            </div>
            <div class="babarida-mb-field">
                <label><?php esc_html_e('Admin Notes (internal)', 'babarida'); ?></label>
                <textarea name="_booking_notes_admin" rows="3" style="width:100%;padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;"><?php echo esc_textarea($values['_booking_notes_admin']); ?></textarea>
            </div>
        </div>
        <?php
    }

    /**
     * Water Sport Metabox Callback
     */
    public function watersport_metabox_callback($post) {
        $values = array(
            '_ws_price'       => get_post_meta($post->ID, '_ws_price', true),
            '_ws_duration'    => get_post_meta($post->ID, '_ws_duration', true),
            '_ws_min_age'    => get_post_meta($post->ID, '_ws_min_age', true),
            '_ws_difficulty'  => get_post_meta($post->ID, '_ws_difficulty', true),
            '_ws_includes'   => get_post_meta($post->ID, '_ws_includes', true),
        );
        $this->render_metabox_fields($post, 'ws', $values);
    }

    /**
     * Dive Course Metabox Callback
     */
    public function course_metabox_callback($post) {
        $values = array(
            '_course_price'        => get_post_meta($post->ID, '_course_price', true),
            '_course_duration'     => get_post_meta($post->ID, '_course_duration', true),
            '_course_level'       => get_post_meta($post->ID, '_course_level', true),
            '_course_prerequisites'=> get_post_meta($post->ID, '_course_prerequisites', true),
            '_course_includes'     => get_post_meta($post->ID, '_course_includes', true),
            '_course_certification'=> get_post_meta($post->ID, '_course_certification', true),
            '_course_max_students' => get_post_meta($post->ID, '_course_max_students', true),
        );
        $this->render_metabox_fields($post, 'course', $values);
        ?>
        <div class="babarida-metabox-section">
            <div class="babarida-mb-field">
                <label><?php esc_html_e('Prerequisites', 'babarida'); ?></label>
                <textarea name="_course_prerequisites" rows="2" style="width:100%;padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;"><?php echo esc_textarea($values['_course_prerequisites']); ?></textarea>
            </div>
            <div class="babarida-mb-field">
                <label><?php esc_html_e('Certification Earned', 'babarida'); ?></label>
                <input type="text" name="_course_certification" value="<?php echo esc_attr($values['_course_certification']); ?>" style="width:100%;padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;">
            </div>
            <div class="babarida-mb-field">
                <label><?php_e('Max Students per Course', 'babarida'); ?></label>
                <input type="number" name="_course_max_students" value="<?php echo esc_attr($values['_course_max_students']); ?>" min="1" style="width:100px;padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;">
            </div>
        </div>
        <?php
    }

    /**
     * Generic metabox field renderer
     */
    private function render_metabox_fields($post, $prefix, $values) {
        $field_map = array(
            'trip' => array(
                array('key' => '_trip_price', 'label' => 'Price (USD)', 'type' => 'number', 'step' => '1', 'min' => '0', 'placeholder' => '85'),
                array('key' => '_trip_price_idr', 'label' => 'Price (IDR)', 'type' => 'number', 'step' => '1000', 'min' => '0', 'placeholder' => '1000000'),
                array('key' => '_trip_duration', 'label' => 'Duration (hours)', 'type' => 'text', 'placeholder' => '8 hours'),
                array('key' => '_trip_max_guests', 'label' => 'Max Guests', 'type' => 'number', 'min' => '1', 'placeholder' => '16'),
                array('key' => '_trip_includes', 'label' => 'Includes (comma separated)', 'type' => 'text', 'placeholder' => 'Equipment, lunch, guide, boat transfer'),
                array('key' => '_trip_excludes', 'label' => 'Excludes (comma separated)', 'type' => 'text', 'placeholder' => 'Flights, insurance, alcoholic beverages'),
                array('key' => '_trip_itinerary', 'label' => 'Itinerary (one per line: Day 1: ...)', 'type' => 'textarea', 'rows' => '8'),
                array('key' => '_trip_season_tag', 'label' => 'Season Tag', 'type' => 'select', 'options' => array('' => 'Default', 'peak' => 'Peak', 'high' => 'High', 'low' => 'Low', 'promo' => 'Promo', 'early_bird' => 'Early Bird', 'last_minute' => 'Last Minute')),
            ),
            'liveaboard' => array(
                array('key' => '_liveaboard_price', 'label' => 'Price per person (USD)', 'type' => 'number', 'step' => '1', 'min' => '0', 'placeholder' => '2400'),
                array('key' => '_liveaboard_price_idr', 'label' => 'Price per person (IDR)', 'type' => 'number', 'step' => '100000', 'min' => '0', 'placeholder' => '38000000'),
                array('key' => '_liveaboard_cabins', 'label' => 'Number of Cabins', 'type' => 'number', 'min' => '1', 'placeholder' => '8'),
                array('key' => '_liveaboard_guests', 'label' => 'Max Guests', 'type' => 'number', 'min' => '1', 'placeholder' => '16'),
                array('key' => '_liveaboard_length', 'label' => 'Boat Length (meters)', 'type' => 'text', 'placeholder' => '32'),
                array('key' => '_liveaboard_nights', 'label' => 'Trip Duration (nights)', 'type' => 'number', 'min' => '1', 'placeholder' => '3'),
                array('key' => '_liveaboard_route', 'label' => 'Route Description', 'type' => 'text', 'placeholder' => 'Bunaken → Bangka → Lembeh'),
                array('key' => '_liveaboard_badge', 'label' => 'Badge Text', 'type' => 'text', 'placeholder' => 'Flagship'),
            ),
            'ws' => array(
                array('key' => '_ws_price', 'label' => 'Price per person (USD)', 'type' => 'number', 'step' => '1', 'min' => '0', 'placeholder' => '35'),
                array('key' => '_ws_duration', 'label' => 'Duration (minutes)', 'type' => 'text', 'placeholder' => '30'),
                array('key' => '_ws_min_age', 'label' => 'Minimum Age', 'type' => 'number', 'min' => '1', 'placeholder' => '10'),
                array('key' => '_ws_difficulty', 'label' => 'Difficulty Level', 'type' => 'select', 'options' => array('beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced')),
                array('key' => '_ws_includes', 'label' => 'Includes', 'type' => 'text', 'placeholder' => 'Equipment, safety briefing, refreshments'),
            ),
            'course' => array(
                array('key' => '_course_price', 'label' => 'Price (USD)', 'type' => 'number', 'step' => '1', 'min' => '0', 'placeholder' => '380'),
                array('key' => '_course_duration', 'label' => 'Duration (days)', 'type' => 'text', 'placeholder' => '3-4 days'),
                array('key' => '_course_level', 'label' => 'SSI Level', 'type' => 'select', 'options' => array('try_scuba' => 'Try Scuba', 'open_water' => 'Open Water Diver', 'advanced' => 'Advanced Adventurer', 'stress_rescue' => 'Stress & Rescue', 'dive_guide' => 'Dive Guide', 'divemaster' => 'Divemaster')),
                array('key' => '_course_includes', 'label' => 'Includes (comma separated)', 'type' => 'text', 'placeholder' => 'Certification fees, equipment, logbook'),
            ),
        );

        $fields = isset($field_map[$prefix]) ? $field_map[$prefix] : array();

        echo '<div class="babarida-metabox-section">';
        foreach ($fields as $field) {
            $val = isset($values[$field['key']]) ? $values[$field['key']] : '';
            echo '<div class="babarida-mb-field">';
            echo '<label for="' . esc_attr($field['key']) . '">' . esc_html($field['label']) . '</label>';

            if ($field['type'] === 'textarea') {
                echo '<textarea id="' . esc_attr($field['key']) . '" name="' . esc_attr($field['key']) . '" rows="' . esc_attr($field['rows'] ?? 4) . '" style="width:100%;padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;">' . esc_textarea($val) . '</textarea>';
            } elseif ($field['type'] === 'select') {
                echo '<select id="' . esc_attr($field['key']) . '" name="' . esc_attr($field['key']) . '" style="width:100%;padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;">';
                foreach ($field['options'] as $opt_val => $opt_label) {
                    echo '<option value="' . esc_attr($opt_val) . '"' . selected($val === (string)$opt_val, true) . '>' . esc_html($opt_label) . '</option>';
                }
                echo '</select>';
            } else {
                $extra = '';
                if (isset($field['step'])) $extra .= ' step="' . esc_attr($field['step']) . '"';
                if (isset($field['min'])) $extra .= ' min="' . esc_attr($field['min']) . '"';
                if (isset($field['max'])) $extra .= ' max="' . esc_attr($field['max']) . '"';
                echo '<input id="' . esc_attr($field['key']) . '" type="' . esc_attr($field['type']) . '" name="' . esc_attr($field['key']) . '" value="' . esc_attr($val) . '" placeholder="' . esc_attr($field['placeholder']) . '"' . $extra . ' style="width:100%;padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;">';
            }
            echo '</div>';
        }
        echo '</div>';
    }

    /**
     * Save all metaboxes
     */
    public function save_metaboxes($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!wp_verify_nonce($_POST['babarida_trip_nonce_field'] ?? '', 'babarida_trip_nonce')) return;

        $fields = array(
            'trip' => array('_trip_price','_trip_price_idr','_trip_duration','_trip_max_guests','_trip_includes','_trip_excludes','_trip_itinerary','_trip_video','_trip_season_tag','_trip_badge_text','_trip_badge_color'),
            'liveaboard' => array('_liveaboard_price','_liveaboard_price_idr','_liveaboard_cabins','_liveaboard_guests','_liveaboard_length','_liveaboard_nights','_liveaboard_route','_liveaboard_badge','_liveaboard_specifications','_liveaboard_deck_plan','_liveaboard_video'),
            'hotel' => array('_hotel_price','_hotel_stars','_hotel_location','_hotel_amenities','_hotel_room_types','_hotel_map_lat','_hotel_map_lng'),
            'testimonial' => array('_testi_stars','_testi_country','_testi_city','_testi_flag','_testi_avatar'),
            'booking' => array('_booking_total','_booking_paid','_booking_payment_method','_booking_guide','_booking_boat','_booking_notes_admin'),
            'ws' => array('_ws_price','_ws_duration','_ws_min_age','_ws_difficulty','_ws_includes'),
            'course' => array('_course_price','_course_duration','_course_level','_course_prerequisites','_course_includes','_course_certification','_course_max_students'),
        );

        $post_type = get_post_type($post_id);

        foreach ($fields as $prefix => $keys) {
            if ($prefix !== $post_type) continue;
            foreach ($keys as $key) {
                if (isset($_POST[$key])) {
                    $value = sanitize_text_field($_POST[$key]);
                    if (is_numeric($value)) $value = floatval($value);
                    update_post_meta($post_id, $key, $value);
                }
            }
        }
    }

    /**
     * Destination term extra fields
     */
    public function destination_term_fields($term) {
        $image_id = get_term_meta($term->term_id, 'destination_image', true);
        $description = get_term_meta($term->term_id, 'destination_desc', true);
        $map_lat = get_term_meta($term->term_id, 'destination_lat', true);
        $map_lng = get_term_meta($term->term_id, 'destination_lng', true);
        $gallery_ids = get_term_meta($term->term_id, 'destination_gallery', true);
        ?>
        <div class="form-field">
            <label><?php esc_html_e('Destination Image', 'babarida'); ?></label>
            <input type="hidden" name="term_meta[destination_image]" value="<?php echo esc_attr($image_id); ?>">
            <button type="button" class="button button-small" id="dest-upload-btn" style="margin-top:4px;"><?php esc_html_e('Upload Image', 'babarida'); ?></button>
            <?php if ($image_id) : ?>
            <div style="margin-top:8px;">
                <img src="<?php echo esc_url(wp_get_attachment_url($image_id)); ?>" style="max-width:300px;height:auto;border-radius:8px;border:1px solid #E2E8F0;">
            </div>
            <?php endif; ?>
        </div>
        <div class="form-field">
            <label><?php esc_html_e('Short Description', 'babarida'); ?></label>
            <textarea name="term_meta[destination_desc]" rows="3" style="width:95%;padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;"><?php echo esc_textarea($description); ?></textarea>
        </div>
        <div class="babarida-mb-row">
            <div class="form-field">
                <label><?php esc_html_e('Map Latitude', 'babarida'); ?></label>
                <input type="text" name="term_meta[destination_lat]" value="<?php echo esc_attr($map_lat); ?>" placeholder="1.4748" style="width:100%;padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;">
            </div>
            <div class="form-field">
                <label><?php esc_html_e('Map Longitude', 'babarida'); ?></label>
                <input type="text" name="term_meta[destination_lng]" value="<?php echo esc_attr($map_lng); ?>" placeholder="124.8421" style="width:100%;padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;font-size:13px;">
            </div>
        </div>
        <div class="form-field">
            <label><?php esc_html_e('Gallery Image IDs (comma separated)', 'babarida'); ?></label>
            <input type="text" name="term_meta[destination_gallery]" value="<?php echo esc_attr(is_array($gallery_ids) ? implode(',', $gallery_ids) : $gallery_ids); ?>" placeholder="123, 456, 789" style="width:95%;padding:8px 12px;border:1px solid #E2E8F2;border-radius:6px;font-size:13px;">
        </div>
        <script>
        jQuery(document).ready(function($) {
            var mediaUploader;
            $('#dest-upload-btn').on('click', function(e) {
                e.preventDefault();
                if (mediaUploader) { mediaUploader.open(); return; }
                mediaUploader = wp.media.frames.post.id;
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first();
                    $('input[name="term_meta[destination_image]"]').val(attachment.id);
                    $('#dest-upload-btn').after('<div style="margin-top:8px;"><img src="' + attachment.url + '" style="max-width:300px;height:auto;border-radius:8px;border:1px solid #E2E8F0;"></div>');
                    mediaUploader.close();
                });
            });
        });
        </script>
        <?php
    }

    /**
     * Save destination term meta
     */
    public function save_destination_meta($term_id) {
        if (!isset($_POST['term_meta'])) return;
        foreach ($_POST['term_meta'] as $key => $val) {
        $val = sanitize_text_field($val);
        if ($key === 'destination_image' && is_numeric($val)) $val = intval($val);
        update_term_meta($term_id, $key, $val);
    }

    public function create_destination_term_meta($term_id) {
        if (!isset($_POST['term_meta'])) return;
        $this->save_destination_meta($term_id);
    }

    /**
     * Add custom fields to nav menu items
     */
    public function menu_item_fields($item) {
        $icon = get_post_meta($item->ID, '_menu_item_icon', true);
        $desc = get_post_meta($item->ID, '_menu_item_desc', true);
        ?>
        <p class="field-description description description-thin">
            <label for="menu-item-icon-<?php echo $item->ID; ?>"><?php esc_html_e('Icon Class (FontAwesome)', 'babarida'); ?></label>
            <input type="text" id="menu-item-icon-<?php echo $item->ID; ?>" name="menu-item-icon-<?php echo $item->ID; ?>" value="<?php echo esc_attr($icon); ?>" style="width:100%;padding:6px 10px;border:1px solid #E2E8F0;border-radius:6px;font-size:12px;" placeholder="fa-solid fa-anchor">
        </p>
        <p class="field-description description-thin">
            <label for="menu-item-desc-<?php echo $item->ID; ?>"><?php esc_html_e('Description', 'babarida'); ?></label>
            <input type="text" id="menu-item-desc-<?php echo $item->ID; ?>" name="menu-item-desc-<?php echo $item->ID; ?>" value="<?php echo esc_attr($desc); ?>" style="width:100%;padding:6px 10px;border:1px solid #E2E8F0;border-radius:6px;font-size:12px;" placeholder="Short description">
        </p>
        <?php
    }

    /**
     * Save menu item meta
     */
    public function save_menu_item_meta($menu_id) {
        if (!isset($_POST['menu-item-icon-' . $menu_id])) return;
        update_post_meta($menu_id, '_menu_item_icon', sanitize_text_field($_POST['menu-item-icon-' . $menu_id]));
        update_post_meta($menu_id, '_menu_item_desc', sanitize_text_field($_POST['menu-item-desc-' . $menu_id]));
    }

    /**
     * Ensure CPTs are registered (fallback)
     */
    public function ensure_cpts_registered() {
        $cpts = array(
            'payment'        => array('name' => 'Payments', 'singular' => 'Payment', 'public' => false, 'show_ui' => true, 'supports' => array('title','editor','custom-fields')),
            'activity_log'   => array('name' => 'Activity Log', 'singular' => 'Activity Log', 'public' => false, 'show_ui' => true, 'supports' => array('title','editor')),
            'internal_chat'  => array('name' => 'Internal Chat', 'singular' => 'Chat Message', 'public' => false, 'show_ui' => true, 'supports' => array('title','editor')),
            'waiver'       => array('name' => 'Waiver', 'singular' => 'Waiver', 'public' => false, 'show_ui' => true, 'supports' => array('title','editor','custom-fields','page-attributes')),
            'newsletter_subscriber' => array('name' => 'Newsletter Subscriber', 'singular' => 'Subscriber', 'public' => false, 'show_ui' => false),
        );
        foreach ($cpts as $slug => $args) {
            if (!post_type_exists($slug)) {
                register_post_type($slug, $args);
            }
        }
    }

    /**
     * Add REST API support for public CPTs
     */
    public function add_rest_support() {
        $public_cpts = array('trip','liveaboard','hotel','testimonial','partner','faq','water_sport','dive_course');
        foreach ($public_cpts as $cpt) {
            add_post_type_support($cpt, 'editor', 'wp excerpt', 'thumbnail', 'custom-fields');
        }
    }

    /**
     * Add theme supports for additional post types
     */
    public function add_theme_support() {
        $types = array('payment','activity_log','internal_chat','waiver');
        foreach ($types as $t) {
            if (post_type_exists($t)) {
                add_post_type_support($t, 'thumbnail');
            }
        }
    }

    /**
     * Custom post type labels
     */
    public function payment_labels($labels) {
        $labels->name = 'Payments';
        $labels->all_items = 'All Payments';
        return $labels;
    }

    public function activity_log_labels($labels) {
        $labels->name = 'Activity Log';
        $labels->all_items = 'All Logs';
        return $labels;
    }

    public function internal_chat_labels($labels) {
        $labels->name = 'Chat Messages';
        $labels->all_items = 'All Messages';
        return $labels;
    }

    public function waiver_labels($labels) {
        $labels->name = 'Waivers';
        $labels->all_items = 'All Waivers';
        return $labels;
    }

    public function subscriber_labels($labels) {
        $labels->name = 'Newsletter';
        return $labels;
    }

    /**
     * Register shortcodes
     */
    public function register_shortcodes() {
        // These shortcodes call the theme's template parts
        add_shortcode('babarida_destinations', array($this, 'shortcode_destinations'));
        add_shortcode('babarida_liveaboards', array($this, 'shortcode_liveaboards'));
        add_shortcode('babarida_pricing', array($this, 'shortcode_pricing'));
        add_shortcode('babarida_testimonials', array($this, 'shortcode_testimonials'));
        add_shortcode('babarida_faq', array($this, 'shortcode_faq'));
        add_shortcode('babarida_weather', array($this, 'shortcode_weather'));
        add_shortcode('babarida_booking_form', array($this, 'shortcode_booking_form'));
        add_shortcode('babarida_checkin_form', array($this, 'shortcode_checkin_form'));
    }

    /**
     * Shortcode: Destinations
     */
    public function shortcode_destinations($atts) {
        $atts = shortcode_atts(array('count' => 4, 'columns' => 4));
        $dests = get_terms(array('taxonomy' => 'destination', 'hide_empty' => false, 'number' => intval($atts['count']), 'orderby' => 'menu_order'));
        if (is_wp_error($dests)) return '';

        ob_start();
        echo '<div style="display:grid;grid-template-columns:repeat(' . intval($atts['columns']) . ', 1fr);gap:20px;">';
        foreach ($dests as $dest) {
            $img_id = get_term_meta($dest->term_id, 'destination_image', true);
            $desc = $dest->description ? wp_trim_words($dest->description, 20) : '';
            $link = get_term_link($dest);
            echo '<div style="position:relative;border-radius:16px;overflow:hidden;aspect-ratio:3/4;cursor:pointer;transition:transform 0.6s ease;">';
            if ($img_id) {
                echo '<a href="' . esc_url($link) . '"><img src="' . esc_url(wp_get_attachment_url($img_id)) . '" alt="' . esc_attr($dest->name) . '" loading="lazy" style="width:100%;height:100%;object-fit:cover;display:block;transition:transform 0.6s ease;"></a>';
            }
            echo '<div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,26,51,0.85) 0%,rgba(0,61,102,0.1) 50%,transparent 100%);display:flex;flex-direction:column;justify-content:flex-end;padding:24px;pointer-events:none;">';
            echo '<h3 style="font-family:Playfair Display,Georgia,serif;font-size:1.4rem;font-weight:700;color:#fff;margin:0 0 6px;">' . esc_html($dest->name) . '</h3>';
            if ($desc) echo '<p style="font-size:0.8rem;color:rgba(255,255,255,0.7);max-height:0;overflow:hidden;transition:max-height 0.4s ease;opacity:0;" onmouseover="this.style.maxHeight=\'80px\';this.style.opacity=\'1\';" onmouseout="this.style.maxHeight=\'0px\';this.style.opacity=\'0\';">' . esc_html($desc) . '</p>';
            echo '<a href="' . esc_url($link) . '" style="display:inline-flex;align-items:center;gap:6px;margin-top:12px;font-size:0.78rem;font-weight:600;color:#FFB800;transition:all 0.3s;pointer-events:auto;text-decoration:none;transform:translateY(10px);opacity:0;" onmouseover="this.style.transform=\'translateY(0)\';this.style.opacity=\'1\';" onmouseout="this.style.transform=\'translateY(10px)\';this.style.opacity=\'0\';">Explore ' . esc_html($dest->name) . ' <i class="fa-solid fa-arrow-right" style="font-size:0.65rem;"></i></a>';
            echo '</div></div></div>';
        }
        echo '</div>';
        return ob_get_clean();
    }

    /**
     * Shortcode: Liveaboards
     */
    public function shortcode_liveaboards($atts) {
        $atts = shortcode_atts(array('count' => 3, 'columns' => 3));
        $boats = get_posts(array('post_type' => 'liveaboard', 'numberposts' => intval($atts['count']), 'post_status' => 'publish'));
        if (empty($boats)) return '<p style="text-align:center;color:#94A3B8;padding:40px;">Liveaboard listings coming soon.</p>';

        ob_start();
        echo '<div style="display:grid;grid-template-columns:repeat(' . intval($atts['columns']) . ', 1fr);gap:24px;">';
        foreach ($boats as $boat) {
            $price = get_post_meta($boat->ID, '_liveaboard_price', true) ?: 1200;
            $route = get_post_meta($boat->ID, '_liveaboard_route', true) ?: wp_trim_words($boat->post_excerpt, 8);
            $badge = get_post_meta($boat->ID, '_liveaboard_badge', true);
            echo '<div style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 10px 15px rgba(0,0,0,0.06);border:1px solid #F1F5F9;transition:all 0.3s;" onmouseover="this.style.transform=\'translateY(-8px)\';this.style.boxShadow=\'0 20px 25px rgba(0,0,0,0.08)\';">';

            echo '<div style="height:220px;overflow:hidden;">';
            if (has_post_thumbnail($boat->ID)) {
                the_post_thumbnail($boat->ID, array(600, 400, true));
            } else {
                echo '<img src="https://picsum.photos/seed/boat-' . $boat->ID . '/600/400.jpg" alt="' . esc_attr($boat->post_title) . '" loading="lazy" width="600" height="400" style="width:100%;height:100%;object-fit:cover;">';
            }
            echo '</div>';

            if ($badge) echo '<span style="position:absolute;top:16px;left:16px;padding:5px 14px;background:' . ($badge === 'Popular' ? '#0077E6' : '#FFB800') . ';color:' . ($badge === 'Popular' ? '#fff' : '#001A33') . ';font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;border-radius:20px;position:absolute;top:16px;left:16px;">' . esc_html($badge) . '</span>';

            echo '<div style="padding:24px;">';
            echo '<h3 style="font-family:Playfair Display,Georgia,serif;font-size:1.2rem;font-weight:700;color:#001A33;margin-bottom:8px;"><a href="' . esc_url(get_permalink($boat->ID)) . '" style="color:inherit;text-decoration:none;">' . esc_html($boat->post_title) . '</a></h3>';
            echo '<p style="font-size:0.82rem;color:#64748B;margin-bottom:16px;"><i class="fa-solid fa-location-dot" style="color:#0077E6;margin-right:4px;font-size:0.7rem;"></i>' . esc_html($route) . '</p>';
            echo '<a href="#bookingModal" class="btn btn-primary btn-sm" style="width:100%;justify-content:center;">Book Now</a>';
            echo '<div style="padding-top:16px;border-top:1px solid #F1F5F9;display:flex;align-items:center;justify-content:space-between;">';
            echo '<div style="font-size:0.75rem;color:#94A3B8;">From <strong style="font-size:1.15rem;color:#0077E6;">$' . number_format($price) . '</strong> / person</div>';
            echo '</div></div></div></div>';
        }
        echo '</div>';
        return ob_get_clean();
    }

    /**
     * Shortcode: Pricing Table
     */
    public function shortcode_pricing($atts) {
        ob_start();
        echo '<div style="max-width:900px;margin:0 auto;">';
        echo '<table style="width:100%;border-collapse:collapse;border-radius:12px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.06);border:1px solid #F1F5F9;">';
        echo '<thead><tr style="background:#001A33;color:#fff;">';
        echo '<th style="padding:12px 16px;font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;text-align:left;white-space:nowrap;">Month</th>';
        echo '<th style="padding:12px 16px;font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;">Season</th>';
        echo '<th style="padding:12px 16px;font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;">Day Trip</th>';
        echo '<th style="padding:12px 16px;font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;">Liveaboard 3N</th>';
        echo '<th style="padding:12px 16px;font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;">SSI Open Water</th>';
        echo '</tr></thead><tbody>';

        for ($m = 0; $m < 12; $m++) {
            $date = new DateTime();
            $date->modify('+' . $m . ' months');
            $monthNum = (int) $date->format('n');
            $year = (int) $date->format('Y');
            $monthNames = array('January','February','March','April','May','June','July','August','September','October','November','December');

            if ($monthNum >= 6 && $monthNum <= 8) { $season = 'peak'; $sLabel = 'Peak'; $sClass = '#FEE2E2'; $sColor = '#DC2626'; $dt = 110; $lb = 2800; $cr = 480; }
            elseif (($monthNum >= 3 && $monthNum <= 5) || ($monthNum >= 9 && $monthNum <= 10)) { $season = 'high'; $sLabel = 'High'; $sClass = '#FEF3C7'; $sColor = '#D97706'; $dt = 85; $lb = 2200; $cr = 420; }
            else { $season = 'low'; $sLabel = 'Low'; $sClass = '#D1FAE5'; $sColor = '#059669'; $dt = 65; $lb = 1800; $cr = 380; }

            // Check for overrides
            $dt_o = get_option('babarida_pricing_' . $monthNames[$monthNum-1] . '_day_trip');
            $lb_o = get_option('babarida_pricing_' . $monthNames[$monthNum-1] . '_liveaboard_3n');
            $cr_o = get_option('babarida_pricing_' . $monthNames[$monthNum-1] . '_ssi_ow');
            $dt_display = $dt_o ? '$' . number_format(floatval($dt_o)) : '$' . number_format($dt);
            $lb_display = $lb_o ? '$' . number_format(floatval($lb_o)) : '$' . number_format($lb);
            $cr_display = $cr_o ? '$' . number_format(floatval($cr_o)) : '$' . number_format($cr);

            echo '<tr style="transition:background 0.2s;">';
            echo '<td style="padding:12px 16px;font-weight:600;font-size:0.85rem;color:#001A33;">' . $monthNames[$monthNum-1] . ' ' . $year . '</td>';
            echo '<td style="padding:10px 16px;"><span style="display:inline-block;padding:2px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;color:' . $sColor . ';background:' . $sClass . ';white-space:nowrap;">' . $sLabel . '</span></td>';
            echo '<td style="padding:12px 16px;font-weight:700;color:#0077E6;">' . $dt_display . '</td>';
            echo '<td style="padding:12px 16px;font-weight:700;color:#0077E6;">' . $lb_display . '</td>';
            echo '<td style="padding:12px 16px;font-weight:700;color:#0077E6;">' . $cr_display . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table></div>';
        return ob_get_clean();
    }

    /**
     * Shortcode: Testimonials
     */
    public function shortcode_testimonials($atts) {
        $atts = shortcode_atts(array('count' => 4));
        $testimonials = get_posts(array('post_type' => 'testimonial', 'numberposts' => intval($atts['count']), 'post_status' => 'publish'));
        if (empty($testimonials)) return '<p style="text-align:center;color:#94A3B8;">No testimonials yet.</p>';

        ob_start();
        echo '<div style="max-width:800px;margin:0 auto;">';
        foreach ($testimonials as $t) {
            $stars = floatval(get_post_meta($t->ID, '_testi_stars', true) ?: 5);
            $country = get_post_meta($t->ID, '_testi_country', true);
            $city = get_post_meta($t->ID, '_testi_city', true);
            $flag = get_post_meta($t->ID, '_testi_flag', true);

            echo '<div style="background:#fff;border-radius:16px;padding:32px;box-shadow:0 10px 15px rgba(0,0,0,0.06);border:1px solid #F1F5F9;margin-bottom:20px;position:relative;">';
            echo '<div style="display:flex;gap:12px;margin-bottom:16px;">';
            for ($si = 0; $si < floor($stars); $si++) echo '<i class="fa-solid fa-star" style="color:#FFB800;font-size:0.85rem;"></i>';
            if ($stars - floor($stars) >= 0.5) echo '<i class="fa-solid fa-star-half-stroke" style="color:#FFB800;font-size:0.85rem;"></i>';
            echo '</div>';
            echo '<p style="font-size:1.05rem;color:#475569;line-height:1.8;">' . esc_html($t->post_content) . '</p>';
            echo '<div style="display:flex;align-items:center;gap:14px;">';
            $avatar_id = get_post_meta($t->ID, '_testi_avatar', true);
            if ($avatar_id) {
                echo '<img src="' . esc_url(wp_get_attachment_url($avatar_id)) . '" alt="' . esc_attr($t->post_title) . '" style="width:52px;height:52px;border-radius:50%;border:3px solid #E8F4FD;object-fit:cover;">';
            }
            echo '<div><h5 style="font-size:0.9rem;font-weight:700;color:#001A33;margin:0 0 2px;">' . esc_html($t->post_title) . '</h5>';
            if ($city) {
                echo '<span style="font-size:0.75rem;color:#94A3B8;display:flex;align-items:center;gap:6px;margin-top:2px;">';
                if ($flag) echo '<img src="https://flagcdn.com/w20/' . esc_attr($flag) . '.png" alt="' . esc_attr($country) . '" width="20" height="15" style="border-radius:2px;"> ';
                echo esc_html($city . ', ' . esc_html($country);
                echo '</span>';
            }
            echo '</div></div></div></div>';
        }
        echo '</div>';
        return ob_get_clean();
    }

    /**
     * Shortcode: FAQ
     */
    public function shortcode_faq($atts) {
        $faqs = get_posts(array('post_type' => 'faq', 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC'));
        if (empty($faqs)) return '<p style="text-align:center;color:#94A3B8;">No FAQs yet.</p>';

        ob_start();
        echo '<div style="max-width:780px;margin:0 auto;display:flex;flex-direction:column;gap:12px;">';
        foreach ($faqs as $faq) {
            echo '<div class="faq-item"><button class="faq-question" aria-expanded="false">' . esc_html($faq->post_title) . '<span class="faq-icon"><i class="fa-solid fa-chevron-down"></i></span></button><div class="faq-answer"><div class="faq-answer-inner">' . esc_html($faq->post_content) . '</div></div></div>';
        }
        echo '</div>';
        return ob_get_clean();
    }

    /**
     * Shortcode: Weather
     */
    public function shortcode_weather() {
        $data = Babarida_Weather::get_current();
        ob_start();
        echo '<div style="max-width:400px;margin:0 auto;padding:24px;background:#fff;border-radius:16px;box-shadow:0 4px 12px rgba(0,0,0,0.06);border:1px solid #F1F5F9;">';
        echo '<div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;">';
        echo '<div style="font-size:2rem;color:#0077E6;">' . $data['icon_emoji'] . '</div>';
        echo '<div><div style="font-size:1.5rem;font-weight:700;color:#001A33;line-height:1;">' . $data['temp'] . '&deg;</div>';
        echo '<div style="font-size:0.82rem;color:#64748B;">' . $data['description'] . '</div></div>';
        echo '</div>';
        echo '<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">';
        echo '<div style="display:flex;align-items:center;gap:6px;font-size:0.75rem;color:#475569;"><i class="fa-solid fa-water" style="color:#0077E6;font-size:0.7rem;"></i> ' . $data['water_temp'] . '°C</div>';
        echo '<div style="display:flex;align-items:center;gap:6px;font-size:0.75rem;color:#475569;"><i class="fa-solid fa-eye" style="color:#0077E6;font-size:0.7rem;"></i> ' . $data['dive_visibility'] . '</div>';
        echo '<div style="display:flex;align-items:center;gap:6px;font-size:0.75rem;color:#475569;"><i class="fa-solid fa-wind" style="color:#0077E6;font-size:0.7rem;"></i> ' . $data['wind_speed'] . '</div>';
        echo '<div style="display:flex;align-items:center;gap:6px;font-size:0.75rem;color:#475569;"><i class="fa-solid fa-droplet" style="color:#0077E6;font-size:0.7rem;"></i> ' . $data['humidity'] . '%</div>';
        echo '</div>';
        $cond = $data['dive_conditions'];
        $condColors = array('excellent' => '#10B981', 'good' => '#0077E6', 'fair' => '#F59E0B', 'poor' => '#EF4444');
        echo '<div style="text-align:center;margin-top:12px;padding:10px;background:' . ($condColors[$cond] ?? '#0077E6') . ';border-radius:8px;color:#fff;font-size:0.78rem;font-weight:600;">Dive Conditions: ' . ucfirst($cond) . '</div>';
        echo '</div>';
        return ob_get_clean();
    }

    /**
     * Shortcode: Booking Form
     */
    public function shortcode_booking_form() {
        ob_start();
        include BABARIDA_DIR . '/template-parts/checkin-section.php';
        return ob_get_clean();
    }

    /**
     * Shortcode: Check-in Form
     */
    public function shortcode_checkin_form() {
        ob_start();
        echo '<div style="max-width:600px;margin:0 auto;padding:60px 24px;background:var(--blue-ice);border-radius:16px;">';
        echo '<h2 style="font-family:Playfair Display,Georgia,serif;font-size:1.8rem;font-weight:700;color:#001A33;text-align:center;margin-bottom:16px;">Guest Check-In</h2>';
        echo '<p style="text-align:center;color:#64748B;margin-bottom:32px;">Already have a booking? Enter your details for fast check-in.</p>';
        echo '<form id="checkin-form-' . mt_rand() . '" method="post" action="' . esc_url(admin_url('admin-post.php')) . '>';
        echo '<input type="hidden" name="action" value="babarida_shortcode_checkin">';
        echo '<input type="text" placeholder="Booking Reference / Name" class="form-input" style="width:100%;margin-bottom:14px;padding:12px 16px;border:1px solid #E2E8F0;border-radius:10px;font-size:0.88rem;">';
        echo '<input type="email" placeholder="Email Address" class="form-input" style="width:100%;margin-bottom:14px;padding:12px 16px;border:1px solid #E2E8F0;border-radius:10px;font-size:0.88rem;">';
        echo '<button type="submit" class="btn btn-accent" style="width:100%;padding:14px;font-size:0.92rem;">Check In <i class="fa-solid fa-arrow-right" style="margin-left:6px;"></i></button>';
        echo '</form></div>';
        return ob_get_clean();
    }

    /**
     * Handle shortcode check-in
     */
    public function shortcode_checkin() {
        if (!isset($_POST['action']) return;
        if ($_POST['action'] !== 'babarida_shortcode_checkin') return;

        $reference = sanitize_text_field($_POST['reference'] ?? '');
        $email = sanitize_email($_POST['email'] ?? '');
        if (empty($reference) || empty($email)) return;

        $bookings = get_posts(array(
            'post_type'  => 'booking',
            'numberposts' => 1,
            'meta_key'    => '_booking_reference',
            'meta_value'  => $reference,
        ));

        if (empty($bookings)) return '<p style="text-align:center;color:#EF4444;padding:20px;">Booking not found.</p>';

        update_post_meta($bookings[0]->ID, '_booking_status', 'checked-in');
        return '<p style="text-align:center;color:#10B981;padding:20px;">Check-in successful!</p>';
    }
}

new Babarida_Book_CPT_Plugin();
