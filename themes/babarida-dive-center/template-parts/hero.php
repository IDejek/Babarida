<?php
/**
 * Hero Section Template Part
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

 $hero_video = get_option('babarida_hero_video', 'https://cdn.coverr.co/videos/coverr-underwater-coral-reef-1567/1080p.mp4');
 $hero_image = get_option('babarida_hero_image', '');
 $hero_fallback = $hero_image ? wp_get_attachment_url($hero_image) : 'https://picsum.photos/seed/bunaken-aerial-ocean/1920/1080.jpg';
?>

<section class="hero" id="hero" aria-label="Hero">
    <div class="hero-video-bg">
        <?php if ($hero_video) : ?>
        <video autoplay muted loop playsinline poster="<?php echo esc_url($hero_fallback); ?>">
            <source src="<?php echo esc_url($hero_video); ?>" type="video/mp4">
        </video>
        <?php endif; ?>
    </div>
    <div class="hero-fallback-bg" style="background-image:url('<?php echo esc_url($hero_fallback); ?>');" aria-hidden="true"></div>
    <div class="hero-gradient-bottom" aria-hidden="true"></div>

    <div class="hero-content">
        <h1 class="hero-title"><?php bloginfo('name'); ?></h1>
        <p class="hero-slogan">"The quality of your dive adventure depends on who guides you!"</p>
        <div class="hero-cta-row">
            <a href="#destinations" class="btn btn-accent btn-lg">Explore Destinations</a>
            <a href="#liveaboards" class="btn btn-outline btn-lg">View Liveaboards</a>
        </div>
    </div>

    <div class="hero-scroll" aria-hidden="true">
        <span>Scroll</span>
        <div class="scroll-line"></div>
    </div>
</section>
