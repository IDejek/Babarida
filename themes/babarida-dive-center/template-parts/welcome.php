<?php
/**
 * Welcome Section Template Part
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;
?>

<section class="section welcome" id="welcome" aria-label="Welcome">
    <div class="section-inner">
        <div class="welcome-grid">
            <div class="welcome-image reveal">
                <?php
                $welcome_img = get_option('babarida_welcome_image', '');
                if ($welcome_img) {
                    echo wp_get_attachment_image($welcome_img, 'dest-card', false, array('style' => 'width:100%;height:100%;object-fit:cover;'));
                } else {
                    echo '<img src="https://picsum.photos/seed/diver-coral-reef-bunaken/800/1000.jpg" alt="Diver exploring vibrant coral reef" loading="lazy" width="800" height="1000">';
                }
                ?>
                <div class="welcome-image-badge">
                    <div class="badge-number">15+</div>
                    <div class="badge-text">Years of diving<br>excellence in North Sulawesi</div>
                </div>
            </div>
            <div class="welcome-text">
                <div class="section-label reveal">About Us</div>
                <h2 class="section-title reveal reveal-delay-1">Welcome to Babarida Dive Center</h2>
                <blockquote class="reveal reveal-delay-2">"The quality of your diving adventure depends on who guides you!"</blockquote>
                <p class="reveal reveal-delay-2">Our team is intimately familiar with Bunaken, Siladen, Bangka, and Lembeh and has worked together for years, creating safe, smooth, and unforgettable experiences for divers of all levels.</p>
                <p class="reveal reveal-delay-3">We offer world-class experiences in two of the most biodiverse marine areas on the planet.</p>
                <div class="welcome-offers reveal reveal-delay-3">
                    <div class="welcome-offer-item"><i class="fa-solid fa-ship"></i> Liveaboard Cruises</div>
                    <div class="welcome-offer-item"><i class="fa-solid fa-route"></i> Dive Safaris</div>
                    <div class="welcome-offer-item"><i class="fa-solid fa-parachute-box"></i> Water Sports</div>
                    <div class="welcome-offer-item"><i class="fa-solid fa-sun"></i> Day Trips</div>
                    <div class="welcome-offer-item"><i class="fa-solid fa-graduation-cap"></i> SSI Courses</div>
                    <div class="welcome-offer-item"><i class="fa-solid fa-mask-snorkel"></i> Snorkeling</div>
                </div>
                <a href="#destinations" class="btn btn-primary reveal reveal-delay-4">Choose Your Destination <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</section>
