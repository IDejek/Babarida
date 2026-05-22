<?php
/**
 * Babarida Dive Center Theme Functions
 *
 * @package Babarida_Dive_Center
 * @version 1.0.0
 * @author Iqbal Tombinawa <tombinawaiqbal@gmail.com>
 */

defined('ABSPATH') || exit;

// Theme Constants
define('BABARIDA_VERSION', '1.0.0');
define('BABARIDA_DIR', get_template_directory());
define('BABARIDA_URI', get_template_directory_uri());
define('BABARIDA_INC', BABARIDA_DIR . '/inc/');
define('BABARIDA_ADMIN', BABARIDA_DIR . '/admin/');
define('BABARIDA_ASSETS', BABARIDA_URI . '/assets/');

/**
 * Theme Setup
 */
add_action('after_setup_theme', 'babarida_theme_setup');
function babarida_theme_setup() {

    // Title tag support
    add_theme_support('title-tag');

    // Post thumbnails
    add_theme_support('post-thumbnails');

    // HTML5 support
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
        'navigation-widgets',
    ));

    // Custom logo
    add_theme_support('custom-logo', array(
        'height'      => 80,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    // Block styles
    add_theme_support('wp-block-styles');

    // Responsive embeds
    add_theme_support('responsive-embeds');

    // Align wide
    add_theme_support('align-wide');

    // Custom background
    add_theme_support('custom-background', array(
        'default-color' => 'FFFFFF',
    ));

    // Editor color palette
    add_theme_support('editor-color-palette', array(
        array('name' => __('Primary Blue', 'babarida'),   'slug' => 'primary',   'color' => '#0077E6'),
        array('name' => __('Bright Blue', 'babarida'),    'slug' => 'bright',    'color' => '#0095FF'),
        array('name' => __('Deep Blue', 'babarida'),      'slug' => 'deep',      'color' => '#001A33'),
        array('name' => ('Yellow Accent', 'babarida'),    'slug' => 'accent',    'color' => '#FFB800'),
        array('name' => __('Success', 'babarida'),        'slug' => 'success',   'color' => '#10B981'),
        array('name' => __('Danger', 'babarida'),         'slug' => 'danger',    'color' => '#EF4444'),
    ));

    // Editor font sizes
    add_theme_support('editor-font-sizes', array(
        array('name' => __('Small', 'babarida'),       'slug' => 'small',  'size' => 13),
        array('name' => __('Normal', 'babarida'),      'slug' => 'normal', 'size' => 16),
        array('name' => __('Medium', 'babarida'),      'slug' => 'medium', 'size' => 20),
        array('name' => __('Large', 'babarida'),       'slug' => 'large',  'size' => 28),
        array('name' => __('Huge', 'babarida'),        'slug' => 'huge',   'size' => 42),
    ));

    // Load text domain
    load_theme_textdomain('babarida', BABARIDA_DIR . '/languages');

    // Register navigation menus
    register_nav_menus(array(
        'primary'      => __('Main Navigation', 'babarida'),
        'destinations' => __('Destinations Menu', 'babarida'),
        'footer'       => __('Footer Menu', 'babarida'),
        'mobile'       => __('Mobile Menu', 'babarida'),
    ));

    // Custom image sizes
    add_image_size('hero-large', 1920, 1080, true);
    add_image_size('hero-medium', 1200, 675, true);
    add_image_size('dest-card', 600, 800, true);
    add_image_size('boat-card', 600, 400, true);
    add_image_size('hotel-card', 600, 400, true);
    add_image_size('testi-avatar', 100, 100, true);
    add_image_size('gallery-thumb', 400, 300, true);
    add_image_size('gallery-full', 1200, 800, true);
}

/**
 * Enqueue Styles and Scripts
 */
add_action('wp_enqueue_scripts', 'babarida_enqueue_assets');
function babarida_enqueue_assets() {

    // Google Fonts
    wp_enqueue_style(
        'babarida-fonts',
        'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Inter:wght@300;400;500;600;700&display=swap',
        array(),
        null
    );

    // Font Awesome
    wp_enqueue_style(
        'babarida-icons',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
        array(),
        '6.5.1'
    );

    // Main stylesheet
    wp_enqueue_style(
        'babarida-style',
        get_stylesheet_uri(),
        array('babarida-fonts', 'babarida-icons'),
        BABARIDA_VERSION
    );

    // Main JS
    wp_enqueue_script(
        'babarida-app',
        BABARIDA_URI . '/src/js/app.js',
        array(),
        BABARIDA_VERSION,
        true
    );

    // Localize script data
    wp_localize_script('babarida-app', 'babaridaData', array(
        'ajaxUrl'  => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('babarida_nonce'),
        'siteUrl'  => home_url(),
        'themeUrl' => BABARIDA_URI,
        'i18n'     => array(
            'loading'    => __('Loading...', 'babarida'),
            'sending'    => __('Sending...', 'babarida'),
            'success'    => __('Success!', 'babarida'),
            'error'      => __('Error occurred.', 'babarida'),
            'required'   => __('Please fill in all required fields.', 'babarida'),
            'validEmail' => __('Please enter a valid email.', 'babarida'),
        ),
    ));

    // Conditionally load admin dashboard assets
    if (is_admin() && babarida_is_custom_admin_page()) {
        wp_enqueue_style(
            'babarida-admin-style',
            BABARIDA_URI . '/assets/css/admin-dashboard.css',
            array(),
            BABARIDA_VERSION
        );
        wp_enqueue_script(
            'chart-js',
            'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
            array(),
            '4.4.0',
            true
        );
        wp_enqueue_script(
            'babarida-admin-js',
            BABARIDA_URI . '/assets/js/admin-dashboard.js',
            array('chart-js'),
            BABARIDA_VERSION,
            true
        );
    }
}

/**
 * Check if current page is a custom admin page
 */
function babarida_is_custom_admin_page() {
    $screen = get_current_screen();
    if (!$screen) return false;
    $custom_pages = array(
        'toplevel_page_babarida-dashboard',
        'babarida-page_babarida-bookings',
        'babarida-page_babarida-reports',
        'babarida-page_babarida-pricing',
        'babarida-page_babarida-crm',
        'babarida-page_babarida-schedule',
        'babarida-page_babarida-partners',
        'babarida-page_babarida-activity-log',
        'babarida-page_babarida-seo',
        'babarida-page_babarida-weather',
        'babarida-page_babarida-security',
        'babarida-page_babarida-backup',
        'babarida-page_babarida-system-health',
        'babarida-page_babarida-chat',
        'babarida-page_babarida-media',
        'babarida-page_babarida-settings',
        'babarida-page_babarida-coupons',
        'babarida-page_babarida-export',
    );
    return in_array($screen->id, $custom_pages, true);
}

/**
 * Include Inc Files
 */
require_once BABARIDA_INC . 'class-babarida-cpt.php';
require_once BABARIDA_INC . 'class-babarida-taxonomies.php';
require_once BABARIDA_INC . 'class-babarida-menus.php';
require_once BABARIDA_INC . 'class-babarida-widgets.php';
require_once BABARIDA_INC . 'class-babarida-ajax.php';
require_once BABARIDA_INC . 'class-babarida-pricing.php';
require_once BABARIDA_INC . 'class-babarida-booking.php';
require_once BABARIDA_INC . 'class-babarida-crm.php';
require_once BABARIDA_INC . 'class-babarida-payments.php';
require_once BABARIDA_INC . 'class-babarida-weather.php';
require_once BABARIDA_INC . 'class-babarida-seo.php';
require_once BABARIDA_INC . 'class-babarida-security.php';
require_once BABARIDA_INC . 'class-babarida-roles.php';
require_once BABARIDA_INC . 'class-babarida-notifications.php';
require_once BABARIDA_INC . 'class-babarida-media.php';
require_once BABARIDA_INC . 'class-babarida-waiver.php';
require_once BABARIDA_INC . 'class-babarida-loyalty.php';
require_once BABARIDA_INC . 'class-babarida-chat.php';

/**
 * Include Admin Files
 */
if (is_admin()) {
    require_once BABARIDA_ADMIN . 'class-admin-dashboard.php';
    require_once BABARIDA_ADMIN . 'class-admin-reports.php';
    require_once BABARIDA_ADMIN . 'class-admin-booking-mgr.php';
    require_once BABARIDA_ADMIN . 'class-admin-pricing-mgr.php';
    require_once BABARIDA_ADMIN . 'class-admin-media-mgr.php';
    require_once BABARIDA_ADMIN . 'class-admin-seo-panel.php';
    require_once BABARIDA_ADMIN . 'class-admin-weather-panel.php';
    require_once BABARIDA_ADMIN . 'class-admin-security.php';
    require_once BABARIDA_ADMIN . 'class-admin-activity-log.php';
    require_once BABARIDA_ADMIN . 'class-admin-backup.php';
    require_once BABARIDA_ADMIN . 'class-admin-system-health.php';
    require_once BABARIDA_ADMIN . 'class-admin-chat.php';
    require_once BABARIDA_ADMIN . 'class-admin-partner-mgr.php';
    require_once BABARIDA_ADMIN . 'class-admin-coupon-mgr.php';
    require_once BABARIDA_ADMIN . 'class-admin-export.php';
}

/**
 * Register Sidebars / Widget Areas
 */
add_action('widgets_init', 'babarida_register_sidebars');
function babarida_register_sidebars() {
    register_sidebar(array(
        'name'          => __('Blog Sidebar', 'babarida'),
        'id'            => 'blog-sidebar',
        'description'   => __('Sidebar for blog pages.', 'babarida'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));
    register_sidebar(array(
        'name'          => __('Footer Column 1', 'babarida'),
        'id'            => 'footer-1',
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h5 class="footer-widget-title">',
        'after_title'   => '</h5>',
    ));
    register_sidebar(array(
        'name'          => __('Footer Column 2', 'babarida'),
        'id'            => 'footer-2',
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h5 class="footer-widget-title">',
        'after_title'   => '</h5>',
    ));
}

/**
 * Remove default WordPress emoji scripts for performance
 */
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('admin_print_styles', 'print_emoji_styles');

/**
 * Disable jQuery Migrate on frontend
 */
add_action('wp_default_scripts', 'babarida_dequeue_jquery_migrate');
function babarida_dequeue_jquery_migrate($scripts) {
    if (!is_admin() && isset($scripts->registered['jquery'])) {
        $scripts->registered['jquery']->deps = array_diff(
            $scripts->registered['jquery']->deps,
            array('jquery-migrate')
        );
    }
}

/**
 * Add custom body classes
 */
add_filter('body_class', 'babarida_body_classes');
function babarida_body_classes($classes) {
    $classes[] = 'babarida-theme';
    if (is_front_page()) {
        $classes[] = 'home-page';
    }
    if (is_singular('trip')) {
        $classes[] = 'single-trip';
    }
    if (is_singular('liveaboard')) {
        $classes[] = 'single-liveaboard';
    }
    if (is_post_type_archive(array('trip', 'liveaboard', 'hotel'))) {
        $classes[] = 'archive-product';
    }
    return $classes;
}

/**
 * Custom excerpt length
 */
add_filter('excerpt_length', 'babarida_excerpt_length');
function babarida_excerpt_length($length) {
    return 20;
}

/**
 * Custom excerpt more
 */
add_filter('excerpt_more', 'babarida_excerpt_more');
function babarida_excerpt_more($more) {
    return '...';
}

/**
 * Disable WordPress default gallery styling
 */
add_filter('use_default_gallery_style', '__return_false');

/**
 * Add async/defer to scripts
 */
add_filter('script_loader_tag', 'babarida_script_attributes', 10, 3);
function babarida_script_attributes($tag, $handle, $src) {
    $defer_scripts = array('babarida-app', 'babarida-admin-js');
    if (in_array($handle, $defer_scripts, true)) {
        $tag = str_replace(' src', ' defer src', $tag);
    }
    return $tag;
}

/**
 * Preload critical fonts
 */
add_action('wp_head', 'babarida_preload_fonts', 1);
function babarida_preload_fonts() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}

/**
 * Add meta viewport and charset (backup)
 */
add_action('wp_head', 'babarida_meta_tags', 0);
function babarida_meta_tags() {
    echo '<meta charset="' . get_bloginfo('charset') . '">' . "\n";
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">' . "\n";
}

/**
 * Custom WordPress login page styling
 */
add_action('login_enqueue_scripts', 'babarida_custom_login');
function babarida_custom_login() {
    wp_enqueue_style('babarida-login', BABARIDA_URI . '/assets/css/login.css', array(), BABARIDA_VERSION);
}
add_filter('login_headerurl', function() { return home_url(); });
add_filter('login_headertext', function() { return 'Babarida Dive Center'; });

/**
 * Remove WordPress version from head for security
 */
remove_action('wp_head', 'wp_generator');
add_filter('the_generator', '__return_empty_string');

/**
 * Disable REST API for non-logged users (security hardening)
 */
add_filter('rest_authentication_errors', 'babarida_restrict_rest_api');
function babarida_restrict_rest_api($result) {
    if (!is_user_logged_in() && !babarida_is_rest_allowed()) {
        return new WP_Error('rest_disabled', __('REST API disabled for non-authenticated users.', 'babarida'), array('status' => 401));
    }
    return $result;
}
function babarida_is_rest_allowed() {
    $allowed_routes = array('/wp/v2/posts', '/wp/v2/pages', '/babarida/v1/');
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    foreach ($allowed_routes as $route) {
        if (strpos($request_uri, $route) !== false) {
            return true;
        }
    }
    return false;
}

/**
 * Custom robots.txt output
 */
add_filter('robots_txt', 'babarida_robots_txt', 10, 2);
function babarida_robots_txt($output, $public) {
    if ($public) {
        $output  = "User-agent: *\n";
        $output .= "Allow: /\n";
        $output .= "Disallow: /wp-admin/\n";
        $output .= "Disallow: /wp-includes/\n";
        $output .= "Disallow: /wp-content/plugins/\n";
        $output .= "Disallow: /?s=\n";
        $output .= "Disallow: /search/\n";
        $output .= "Disallow: */trackback/\n";
        $output .= "Disallow: */feed/\n";
        $output .= "Disallow: */comments/\n";
        $output .= "Sitemap: " . home_url('/sitemap.xml') . "\n";
        $output .= "Sitemap: " . home_url('/sitemap_index.xml') . "\n";
    }
    return $output;
}

/**
 * Dynamic XML Sitemap
 */
add_action('init', 'babarida_sitemap_rewrite');
function babarida_sitemap_rewrite() {
    add_rewrite_rule('sitemap\.xml$', 'index.php?babarida_sitemap=1', 'top');
    add_rewrite_rule('sitemap_index\.xml$', 'index.php?babarida_sitemap_index=1', 'top');
}
add_action('parse_request', 'babarida_sitemap_handler');
function babarida_sitemap_handler($wp) {
    if (isset($wp->query_vars['babarida_sitemap']) || isset($wp->query_vars['babarida_sitemap_index'])) {
        babarida_generate_sitemap(isset($wp->query_vars['babarida_sitemap_index']));
        exit;
    }
}
add_filter('query_vars', function($vars) {
    $vars[] = 'babarida_sitemap';
    $vars[] = 'babarida_sitemap_index';
    return $vars;
});

function babarida_generate_sitemap($is_index = false) {
    header('Content-Type: application/xml; charset=' . get_bloginfo('charset'), true);

    if ($is_index) {
        echo '<?xml version="1.0" encoding="' . get_bloginfo('charset') . '"?>' . "\n";
        echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        echo '<sitemap><loc>' . home_url('/sitemap.xml') . '</loc><lastmod>' . gmdate('Y-m-d\TH:i:s+00:00') . '</lastmod></sitemap>' . "\n";
        echo '</sitemapindex>';
        return;
    }

    echo '<?xml version="1.0" encoding="' . get_bloginfo('charset') . '"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
    echo '        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n";
    echo '        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";

    // Homepage
    babarida_sitemap_add_url(home_url('/'), '1.0', 'daily');

    // Destination pages
    $destinations = get_terms(array('taxonomy' => 'destination', 'hide_empty' => true));
    if (!is_wp_error($destinations)) {
        foreach ($destinations as $dest) {
            babarida_sitemap_add_url(get_term_link($dest), '0.9', 'weekly');
        }
    }

    // Trip CPTs
    $trips = get_posts(array('post_type' => 'trip', 'numberposts' => -1, 'post_status' => 'publish'));
    foreach ($trips as $trip) {
        babarida_sitemap_add_url(get_permalink($trip->ID), '0.8', 'weekly', $trip->post_modified);
    }

    // Liveaboard CPTs
    $boats = get_posts(array('post_type' => 'liveaboard', 'numberposts' => -1, 'post_status' => 'publish'));
    foreach ($boats as $boat) {
        babarida_sitemap_add_url(get_permalink($boat->ID), '0.8', 'weekly', $boat->post_modified);
    }

    // Hotel CPTs
    $hotels = get_posts(array('post_type' => 'hotel', 'numberposts' => -1, 'post_status' => 'publish'));
    foreach ($hotels as $hotel) {
        babarida_sitemap_add_url(get_permalink($hotel->ID), '0.7', 'weekly', $hotel->post_modified);
    }

    // Blog posts
    $posts = get_posts(array('post_type' => 'post', 'numberposts' => -1, 'post_status' => 'publish'));
    foreach ($posts as $post) {
        babarida_sitemap_add_url(get_permalink($post->ID), '0.6', 'monthly', $post->post_modified);
    }

    // Pages
    $pages = get_posts(array('post_type' => 'page', 'numberposts' => -1, 'post_status' => 'publish'));
    foreach ($pages as $page) {
        babarida_sitemap_add_url(get_permalink($page->ID), '0.5', 'monthly', $page->post_modified);
    }

    echo '</urlset>';
}

function babarida_sitemap_add_url($loc, $priority = '0.5', $freq = 'monthly', $lastmod = '') {
    if (empty($lastmod)) {
        $lastmod = gmdate('Y-m-d\TH:i:s+00:00');
    } else {
        $lastmod = mysql2date('Y-m-d\TH:i:s+00:00', $lastmod);
    }
    echo '<url>' . "\n";
    echo '  <loc>' . esc_url($loc) . '</loc>' . "\n";
    echo '  <lastmod>' . $lastmod . '</lastmod>' . "\n";
    echo '  <changefreq>' . esc_html($freq) . '</changefreq>' . "\n";
    echo '  <priority>' . esc_html($priority) . '</priority>' . "\n";
    echo '</url>' . "\n";
}

/**
 * Flush rewrite rules on theme activation
 */
register_activation_hook(__FILE__, 'babarida_activate');
function babarida_activate() {
    babarida_theme_setup();
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'babarida_deactivate');
function babarida_deactivate() {
    flush_rewrite_rules();
}
