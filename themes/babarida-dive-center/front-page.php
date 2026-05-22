<?php
/**
 * Front Page Template
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

get_header();
?>

<main id="main-content">

    <!-- Hero Section -->
    <?php get_template_part('template-parts/hero'); ?>

    <!-- Welcome Section -->
    <?php get_template_part('template-parts/welcome'); ?>

    <!-- Destinations Section -->
    <?php get_template_part('template-parts/destinations-grid'); ?>

    <!-- Liveaboards Section -->
    <?php get_template_part('template-parts/liveaboards-grid'); ?>

    <!-- Hotel Partners Section -->
    <?php get_template_part('template-parts/hotels-grid'); ?>

    <!-- Monthly Pricing Section -->
    <?php get_template_part('template-parts/pricing-table'); ?>

    <!-- Interactive Map Section -->
    <?php get_template_part('template-parts/interactive-map'); ?>

    <!-- Testimonials Section -->
    <?php get_template_part('template-parts/testimonials-slider'); ?>

    <!-- Partners Section -->
    <?php get_template_part('template-parts/partners-carousel'); ?>

    <!-- FAQ Section -->
    <?php get_template_part('template-parts/faq-accordion'); ?>

    <!-- Newsletter Section -->
    <?php get_template_part('template-parts/newsletter'); ?>

    <!-- Check-In Section -->
    <?php get_template_part('template-parts/checkin-section'); ?>

</main>

<?php get_footer(); ?>
