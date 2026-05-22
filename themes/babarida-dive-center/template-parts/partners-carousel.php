<?php
/**
 * Partners Carousel Template Part
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

 $partners = get_posts(array(
    'post_type'   => 'partner',
    'numberposts' => 16,
    'post_status' => 'publish',
    'orderby'     => 'menu_order',
    'order'       => 'ASC',
));
?>

<section class="partners" id="partners" aria-label="Partners">
    <div class="partners-header">
        <div class="section-label reveal" style="justify-content:center;">Trusted By</div>
        <h2 class="section-title reveal reveal-delay-1" style="text-align:center;">Our Partners</h2>
    </div>
    <div class="partner-carousel reveal reveal-delay-2">
        <div class="partner-track" id="partnerTrack">
            <?php
            $partner_seeds = array('ssi-logo','padi-logo','tripadvisor-logo','booking-logo','garuda-logo','sulawesi-tourism','marine-logo','dive-insurance');
            $logos = array();
            foreach ($partners as $p) {
                if (has_post_thumbnail($p->ID)) {
                    $logos[] = wp_get_attachment_image($p->ID, array(160, 50), false, array('class' => 'partner-logo', 'loading' => 'lazy'));
                } else {
                    $seed = array_shift($partner_seeds) ?: 'partner-' . $p->ID;
                    $logos[] = '<img src="https://picsum.photos/seed/' . $seed . '/160/50.jpg" alt="' . esc_attr($p->post_title) . '" class="partner-logo" loading="lazy" width="160" height="50">';
                }
            }
            // Duplicate for infinite scroll
            $all_logos = array_merge($logos, $logos);
            foreach ($all_logos as $logo) echo $logo;
            ?>
        </div>
    </div>
</section>
