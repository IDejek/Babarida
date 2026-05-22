<?php
/**
 * Header Template
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="scroll-smooth">
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="<?php echo esc_attr(babarida_get_meta_description()); ?>">
<meta name="keywords" content="<?php echo esc_attr(babarida_get_meta_keywords()); ?>">
<meta name="author" content="Iqbal Tombinawa">
<meta name="robots" content="<?php echo esc_attr(babarida_get_robots_meta()); ?>">
<link rel="canonical" href="<?php echo esc_url(babarida_get_canonical_url()); ?>">

<!-- Open Graph -->
<meta property="og:type" content="<?php echo esc_attr(babarida_get_og_type()); ?>">
<meta property="og:title" content="<?php echo esc_attr(babarida_get_og_title()); ?>">
<meta property="og:description" content="<?php echo esc_attr(babarida_get_meta_description()); ?>">
<meta property="og:url" content="<?php echo esc_url(babarida_get_canonical_url()); ?>">
<meta property="og:site_name" content="<?php bloginfo('name'); ?>">
<meta property="og:locale" content="en_US">
<meta property="og:locale:alternate" content="id_ID">
<?php $og_image = babarida_get_og_image(); if ($og_image) : ?>
<meta property="og:image" content="<?php echo esc_url($og_image); ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<?php endif; ?>

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo esc_attr(babarida_get_og_title()); ?>">
<meta name="twitter:description" content="<?php echo esc_attr(babarida_get_meta_description()); ?>">
<?php if ($og_image) : ?>
<meta name="twitter:image" content="<?php echo esc_url($og_image); ?>">
<?php endif; ?>

<!-- Google Search Console Verification -->
<?php $gsc_verify = get_option('babarida_gsc_verification', ''); if ($gsc_verify) : ?>
<meta name="google-site-verification" content="<?php echo esc_attr($gsc_verify); ?>">
<?php endif; ?>

<!-- Schema: LocalBusiness + DiveCenter -->
<script type="application/ld+json"><?php echo wp_json_encode(babarida_get_organization_schema()); ?></script>

<!-- Schema: BreadcrumbList -->
<script type="application/ld+json"><?php echo wp_json_encode(babarida_get_breadcrumb_schema()); ?></script>

<?php if (is_singular('faq') || is_page('faq')) : ?>
<!-- Schema: FAQPage -->
<script type="application/ld+json"><?php echo wp_json_encode(babarida_get_faq_schema()); ?></script>
<?php endif; ?>

<!-- PWA -->
<link rel="manifest" href="<?php echo esc_url(BABARIDA_URI . '/manifest.json'); ?>">
<meta name="theme-color" content="#0077E6">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

<title><?php wp_title('|', true, 'right'); bloginfo('name'); ?></title>

<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Skip Link -->
<a href="#main-content" class="skip-link"><?php esc_html_e('Skip to main content', 'babarida'); ?></a>

<!-- Preloader -->
<div id="preloader" role="status" aria-label="<?php esc_attr_e('Loading', 'babarida'); ?>">
    <div class="preloader-waves">
        <div class="preloader-wave">
            <svg viewBox="0 0 1440 200" preserveAspectRatio="none"><path fill="rgba(255,255,255,0.5)" d="M0,100 C360,200 720,0 1080,100 C1260,150 1380,120 1440,100 L1440,200 L0,200 Z"></path></svg>
        </div>
        <div class="preloader-wave">
            <svg viewBox="0 0 1440 200" preserveAspectRatio="none"><path fill="rgba(255,255,255,0.3)" d="M0,120 C240,40 480,180 720,100 C960,20 1200,160 1440,80 L1440,200 L0,200 Z"></path></svg>
        </div>
        <div class="preloader-wave">
            <svg viewBox="0 0 1440 200" preserveAspectRatio="none"><path fill="rgba(255,255,255,0.15)" d="M0,80 C180,160 360,40 540,120 C720,200 900,60 1080,140 C1260,220 1380,100 1440,120 L1440,200 L0,200 Z"></path></svg>
        </div>
    </div>
    <div class="preloader-logo">
        <div class="logo-icon"><i class="fa-solid fa-water"></i></div>
        <h1><?php bloginfo('name'); ?></h1>
        <p><?php esc_html_e('North Sulawesi, Indonesia', 'babarida'); ?></p>
    </div>
    <div class="preloader-bar"><div class="preloader-bar-fill"></div></div>
</div>

<!-- Ambient Bubbles -->
<div class="ambient-bubbles" aria-hidden="true" id="ambientBubbles"></div>

<!-- Top Header Bar -->
<div class="top-bar" id="topBar" role="banner">
    <div class="top-bar-inner">
        <a href="<?php echo esc_url(home_url('/checkin')); ?>" class="checkin-btn" aria-label="<?php esc_attr_e('Check In', 'babarida'); ?>">
            <i class="fa-solid fa-clipboard-check"></i> <?php esc_html_e('Check-In', 'babarida'); ?>
        </a>
        <div class="world-clocks" id="worldClocks" aria-label="<?php esc_attr_e('World clocks', 'babarida'); ?>">
            <div class="clock-item"><span class="clock-city">Manado</span><span class="clock-time" data-tz="Asia/Makassar">--:--</span></div>
            <div class="clock-item"><span class="clock-city">Jakarta</span><span class="clock-time" data-tz="Asia/Jakarta">--:--</span></div>
            <div class="clock-item"><span class="clock-city">Singapore</span><span class="clock-time" data-tz="Asia/Singapore">--:--</span></div>
            <div class="clock-item"><span class="clock-city">Dubai</span><span class="clock-time" data-tz="Asia/Dubai">--:--</span></div>
            <div class="clock-item"><span class="clock-city">London</span><span class="clock-time" data-tz="Europe/London">--:--</span></div>
            <div class="clock-item"><span class="clock-city">New York</span><span class="clock-time" data-tz="America/New_York">--:--</span></div>
            <div class="clock-item"><span class="clock-city">Tokyo</span><span class="clock-time" data-tz="Asia/Tokyo">--:--</span></div>
            <div class="clock-item"><span class="clock-city">Seoul</span><span class="clock-time" data-tz="Asia/Seoul">--:--</span></div>
        </div>
        <div class="top-bar-right">
            <a href="https://wa.me/62895801960359" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
            <a href="mailto:info@babaridadive.com" aria-label="<?php esc_attr_e('Email', 'babarida'); ?>"><i class="fa-solid fa-envelope"></i></a>
            <div class="lang-switch" role="group" aria-label="<?php esc_attr_e('Language switcher', 'babarida'); ?>">
                <button class="active" data-lang="en">EN</button>
                <button data-lang="id">ID</button>
            </div>
        </div>
    </div>
</div>

<!-- Main Header -->
<header class="main-header" id="mainHeader" role="navigation">
    <div class="main-header-inner">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo" aria-label="<?php esc_attr_e('Babarida Dive Center Home', 'babarida'); ?>">
            <?php if (has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
            <div class="logo-mark"><i class="fa-solid fa-water"></i></div>
            <div class="logo-text">
                <span class="logo-name"><?php bloginfo('name'); ?></span>
                <span class="logo-tagline"><?php esc_html_e('Bunaken &bull; Siladen &bull; Bangka &bull; Lembeh', 'babarida'); ?></span>
            </div>
            <?php endif; ?>
        </a>

        <nav class="main-nav" aria-label="<?php esc_attr_e('Main navigation', 'babarida'); ?>">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'nav-list',
                'items_wrap'     => '%3$s',
                'walker'         => new Babarida_Nav_Walker(),
                'fallback_cb'    => 'babarida_default_menu',
                'depth'          => 3,
            ));
            ?>
        </nav>

        <button class="mobile-toggle" id="mobileToggle" aria-label="<?php esc_attr_e('Toggle menu', 'babarida'); ?>" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobileMenu" role="dialog" aria-label="<?php esc_attr_e('Mobile navigation', 'babarida'); ?>">
    <?php
    wp_nav_menu(array(
        'theme_location' => 'mobile',
        'container'      => false,
        'menu_class'     => 'mobile-nav-list',
        'items_wrap'     => '%3$s',
        'walker'         => new Babarida_Mobile_Walker(),
        'fallback_cb'    => 'babarida_default_mobile_menu',
        'depth'          => 3,
    ));
    ?>
    <div class="mobile-menu-cta">
        <a href="<?php echo esc_url(home_url('/checkin')); ?>" class="btn btn-accent" style="width:100%;"><?php esc_html_e('Check-In', 'babarida'); ?></a>
        <a href="https://wa.me/62895801960359" class="btn btn-primary" style="width:100%;" target="_blank" rel="noopener">
            <i class="fa-brands fa-whatsapp"></i> <?php esc_html_e('WhatsApp Us', 'babarida'); ?>
        </a>
    </div>
</div>
