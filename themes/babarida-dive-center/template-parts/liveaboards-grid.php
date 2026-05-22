<?php
/**
 * Liveaboards Grid Template Part
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

 $boats = get_posts(array(
    'post_type'   => 'liveaboard',
    'numberposts' => 6,
    'post_status' => 'publish',
    'orderby'     => 'menu_order',
    'order'       => 'ASC',
));
?>

<section class="section liveaboards" id="liveaboards" aria-label="Liveaboards">
    <div class="section-inner">
        <div class="liveaboards-header">
            <div>
                <div class="section-label reveal">At Sea</div>
                <h2 class="section-title reveal reveal-delay-1">Our Liveaboards</h2>
                <p class="section-subtitle reveal reveal-delay-2">Cruise through North Sulawesi's finest dive sites aboard our luxury vessels.</p>
            </div>
            <a href="<?php echo esc_url(get_post_type_archive_link('liveaboard')); ?>" class="btn btn-outline reveal reveal-delay-2" style="border-color:var(--gray-200);color:var(--gray-600);">View All Boats <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <div class="boat-grid">
            <?php
            if (!empty($boats)) :
                $delay = 1;
                foreach ($boats as $boat) :
                    $price   = get_post_meta($boat->ID, '_liveaboard_price', true);
                    $cabins  = get_post_meta($boat->ID, '_liveaboard_cabins', true);
                    $guests  = get_post_meta($boat->ID, '_liveaboard_guests', true);
                    $length  = get_post_meta($boat->ID, '_liveaboard_length', true);
                    $nights  = get_post_meta($boat->ID, '_liveaboard_nights', true);
                    $route   = get_post_meta($boat->ID, '_liveaboard_route', true);
                    $badge   = get_post_meta($boat->ID, '_liveaboard_badge', true);
            ?>
            <article class="boat-card reveal reveal-delay-<?php echo $delay; ?>">
                <div class="boat-card-img">
                    <?php if (has_post_thumbnail($boat->ID)) : ?>
                        <?php the_post_thumbnail($boat->ID, 'boat-card'); ?>
                    <?php else : ?>
                        <img src="https://picsum.photos/seed/boat-<?php echo $boat->ID; ?>/600/400.jpg" alt="<?php echo esc_attr($boat->post_title); ?>" loading="lazy" width="600" height="400">
                    <?php endif; ?>
                    <?php if ($badge) : ?>
                    <span class="boat-card-badge" <?php echo $badge === 'Popular' ? 'style="background:var(--blue-primary);color:var(--white);"' : ''; ?>><?php echo esc_html($badge); ?></span>
                    <?php endif; ?>
                </div>
                <div class="boat-card-body">
                    <h3 class="boat-card-name"><a href="<?php echo esc_url(get_permalink($boat->ID)); ?>" style="color:inherit;"><?php echo esc_html($boat->post_title); ?></a></h3>
                    <div class="boat-card-route"><i class="fa-solid fa-location-dot"></i> <?php echo esc_html($route ?: wp_trim_words($boat->post_excerpt, 8)); ?></div>
                    <div class="boat-specs">
                        <div class="boat-spec"><i class="fa-solid fa-bed"></i> <?php echo esc_html($cabins ?: '4'); ?> Cabins</div>
                        <div class="boat-spec"><i class="fa-solid fa-user-group"></i> <?php echo esc_html($guests ?: '8'); ?> Guests</div>
                        <div class="boat-spec"><i class="fa-solid fa-ruler-horizontal"></i> <?php echo esc_html($length ?: '24m'); ?> Length</div>
                        <div class="boat-spec"><i class="fa-solid fa-calendar"></i> <?php echo esc_html($nights ?: '3'); ?> Nights</div>
                    </div>
                    <div class="boat-card-footer">
                        <div class="boat-price">From <strong>$<?php echo esc_html(number_format($price ?: 1200, 0)); ?></strong> / person</div>
                        <a href="#bookingModal" class="btn btn-primary btn-sm">Book Now</a>
                    </div>
                </div>
            </article>
            <?php
                $delay++;
                endforeach;
            else :
            ?>
            <div style="grid-column:1/-1;text-align:center;padding:60px 0;color:var(--gray-400);">
                <p>Liveaboard listings coming soon.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
