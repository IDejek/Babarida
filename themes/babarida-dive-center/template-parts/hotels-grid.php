<?php
/**
 * Hotels Grid Template Part
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

 $hotels = get_posts(array(
    'post_type'   => 'hotel',
    'numberposts' => 6,
    'post_status' => 'publish',
    'orderby'     => 'menu_order',
    'order'       => 'ASC',
));
?>

<section class="section hotels" id="hotels" aria-label="Hotel Partners">
    <div class="section-inner">
        <div class="hotels-header">
            <div class="section-label reveal" style="justify-content:center;">Stay With Us</div>
            <h2 class="section-title reveal reveal-delay-1" style="text-align:center;">Recommended Hotels in Manado</h2>
            <p class="section-subtitle centered reveal reveal-delay-2">Handpicked accommodations to complete your diving holiday experience.</p>
        </div>
        <div class="hotel-grid">
            <?php
            if (!empty($hotels)) :
                $delay = 1;
                foreach ($hotels as $hotel) :
                    $price  = get_post_meta($hotel->ID, '_hotel_price', true);
                    $stars  = get_post_meta($hotel->ID, '_hotel_stars', true);
                    $loc    = get_post_meta($hotel->ID, '_hotel_location', true);
                    $amen   = get_post_meta($hotel->ID, '_hotel_amenities', true);
                    $amen_arr = $amen ? explode(',', $amen) : array();
            ?>
            <article class="hotel-card reveal reveal-delay-<?php echo $delay; ?>">
                <div class="hotel-card-img">
                    <?php if (has_post_thumbnail($hotel->ID)) : ?>
                        <?php the_post_thumbnail($hotel->ID, 'hotel-card'); ?>
                    <?php else : ?>
                        <img src="https://picsum.photos/seed/hotel-<?php echo $hotel->ID; ?>/600/400.jpg" alt="<?php echo esc_attr($hotel->post_title); ?>" loading="lazy" width="600" height="400">
                    <?php endif; ?>
                    <?php if ($stars) : ?>
                    <div class="hotel-stars">
                        <?php for ($s = 0; $s < intval($stars); $s++) : ?><i class="fa-solid fa-star"></i><?php endfor; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="hotel-card-body">
                    <h3 class="hotel-card-name"><?php echo esc_html($hotel->post_title); ?></h3>
                    <div class="hotel-card-location"><i class="fa-solid fa-location-dot"></i> <?php echo esc_html($loc ?: 'Manado, North Sulawesi'); ?></div>
                    <?php if (!empty($amen_arr)) : ?>
                    <div class="hotel-card-amenities">
                        <?php foreach (array_slice($amen_arr, 0, 4) as $am) : ?>
                        <span class="hotel-amenity"><?php echo esc_html(trim($am)); ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <div class="hotel-card-footer">
                        <div class="hotel-price-from">From <strong>$<?php echo esc_html(number_format($price ?: 80, 0)); ?></strong> / night</div>
                        <a href="#bookingModal" class="btn btn-primary btn-sm">Inquire</a>
                    </div>
                </div>
            </article>
            <?php $delay++; endforeach; else : ?>
            <div style="grid-column:1/-1;text-align:center;padding:60px 0;color:var(--gray-400);"><p>Hotel listings coming soon.</p></div>
            <?php endif; ?>
        </div>
    </div>
</section>
