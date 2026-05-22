<?php
/**
 * Sidebar Template
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

if (!is_active_sidebar('blog-sidebar')) return;
?>

<aside class="sidebar" role="complementary" aria-label="<?php esc_attr_e('Blog Sidebar', 'babarida'); ?>" style="padding-top:20px;">
    <?php dynamic_sidebar('blog-sidebar'); ?>
</aside>
