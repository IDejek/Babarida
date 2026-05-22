<?php
/**
 * Media Management Enhancements
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

class Babarida_Media {

    public function __construct() {
        // Auto-compress on upload
        add_filter('wp_handle_upload', array($this, 'auto_compress'), 10, 2);

        // Add custom media sizes to admin
        add_filter('image_size_names_choose', array($this, 'custom_sizes'));

        // Prevent large image uploads
        add_filter('upload_size_limit', array($this, 'limit_upload_size'));
    }

    /**
     * Attempt to compress uploaded images
     */
    public function auto_compress($upload) {
        if ($upload['type'] && strpos($upload['type'], 'image/') === 0) {
            $file = $upload['file'];
            // Check if imagick is available
            if (extension_loaded('imagick')) {
                try {
                    $image = new Imagick($file);
                    // Set quality to 80 for JPEG/WebP
                    if ($image->getImageFormat() === 'JPEG') {
                        $image->setImageCompressionQuality(80);
                        $image->stripImage();
                        $image->writeImage($file);
                    }
                    $image->clear();
                    $image->destroy();
                } catch (Exception $e) {
                    // Silently fail — original image preserved
                }
            }
        }
        return $upload;
    }

    /**
     * Add custom image sizes to media picker
     */
    public function custom_sizes($sizes) {
        return array_merge($sizes, array(
            'hero-large'  => __('Hero Large (1920x1080)', 'babarida'),
            'hero-medium' => __('Hero Medium (1200x675)', 'babarida'),
            'dest-card'   => __('Destination Card (600x800)', 'babarida'),
            'boat-card'   => __('Boat Card (600x400)', 'babarida'),
            'hotel-card'  => __('Hotel Card (600x400)', 'babarida'),
            'gallery-thumb'=> __('Gallery Thumb (400x300)', 'babarida'),
            'gallery-full' => __('Gallery Full (1200x800)', 'babarida'),
        ));
    }

    /**
     * Limit upload size to 10MB
     */
    public function limit_upload_size($size) {
        return 10 * 1024 * 1024; // 10MB
    }
}

new Babarida_Media();
