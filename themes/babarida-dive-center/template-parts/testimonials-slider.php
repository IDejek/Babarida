<?php
/**
 * Testimonials Slider Template Part
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

 $testimonials = get_posts(array(
    'post_type'   => 'testimonial',
    'numberposts' => 10,
    'post_status' => 'publish',
    'orderby'     => 'menu_order',
    'order'       => 'ASC',
));

// Fallback testimonials if none in CPT
if (empty($testimonials)) {
    $testimonials = array();
    $fallbacks = array(
        array('name'=>'Markus Weber','country'=>'Germany','flag'=>'de','city'=>'Munich','text'=>'Absolutely world-class diving. The Bunaken wall was breathtaking — reef sharks, sea turtles, and schools of barracuda in one dive. The crew was incredibly professional.','img'=>'person-german-diver','stars'=>5),
        array('name'=>'Yuki Tanaka','country'=>'Japan','flag'=>'jp','city'=>'Tokyo','text'=>'Lembeh Strait exceeded all expectations. Our guide found a hairy frogfish, blue-ringed octopus, and mimic octopus in one afternoon. Unforgettable!','img'=>'person-japanese-diver','stars'=>5),
        array('name'=>'Sarah Mitchell','country'=>'Australia','flag'=>'au','city'=>'Sydney','text'=>'The liveaboard on the Phinisi was the highlight of our entire Indonesia trip. Luxury comfort combined with incredible diving — already planning our return!','img'=>'person-australian-diver','stars'=>5),
        array('name'=>'Ji-hoon Park','country'=>'South Korea','flag'=>'kr','city'=>'Seoul','text'=>'Completed my SSI Open Water certification with Babarida. The instructors were patient, thorough, and made learning fun. Highly recommended for beginners!','img'=>'person-korean-diver','stars'=>4.5),
    );
    foreach ($fallbacks as $f) {
        $testimonials[] = (object) array(
            'post_title' => $f['name'],
            'post_content' => $f['text'],
            'filter_stars' => $f['stars'],
            'filter_country' => $f['country'],
            'filter_city' => $f['city'],
            'filter_flag' => $f['flag'],
            'filter_img' => $f['img'],
        );
    }
}
?>

<section class="section testimonials" id="testimonials" aria-label="Testimonials">
    <div class="section-inner">
        <div class="testimonials-header">
            <div class="section-label reveal" style="justify-content:center;">Reviews</div>
            <h2 class="section-title reveal reveal-delay-1" style="text-align:center;">What Our Divers Say</h2>
        </div>
        <div class="testi-slider reveal reveal-delay-2">
            <div class="testi-track" id="testiTrack">
                <?php foreach ($testimonials as $t) :
                    $stars = isset($t->filter_stars) ? $t->filter_stars : get_post_meta($t->ID, '_testi_stars', true);
                    $stars = $stars ? floatval($stars) : 5;
                    $country = isset($t->filter_country) ? $t->filter_country : get_post_meta($t->ID, '_testi_country', true);
                    $city = isset($t->filter_city) ? $t->filter_city : get_post_meta($t->ID, '_testi_city', true);
                    $flag = isset($t->filter_flag) ? $t->filter_flag : get_post_meta($t->ID, '_testi_flag', true);
                    $img_seed = isset($t->filter_img) ? $t->filter_img : 'diver-' . $t->ID;
                    $avatar_id = get_post_meta($t->ID, '_testi_avatar', true);
                ?>
                <div class="testi-card">
                    <div class="testi-card-inner">
                        <div class="testi-stars">
                            <?php for ($si = 0; $si < floor($stars); $si++) : ?><i class="fa-solid fa-star"></i><?php endfor; ?>
                            <?php if ($stars - floor($stars) >= 0.5) : ?><i class="fa-solid fa-star-half-stroke"></i><?php endif; ?>
                        </div>
                        <p class="testi-text"><?php echo esc_html($t->post_content); ?></p>
                        <div class="testi-author">
                            <?php if ($avatar_id) : ?>
                                <?php echo wp_get_attachment_image($avatar_id, 'testi-avatar', false, array('class' => 'testi-avatar')); ?>
                            <?php else : ?>
                                <img src="https://picsum.photos/seed/<?php echo esc_attr($img_seed); ?>/100/100.jpg" alt="<?php echo esc_attr($t->post_title); ?>" class="testi-avatar" loading="lazy" width="100" height="100">
                            <?php endif; ?>
                            <div class="testi-author-info">
                                <h5><?php echo esc_html($t->post_title); ?></h5>
                                <span>
                                    <?php if ($flag) : ?><img src="https://flagcdn.com/w20/<?php echo esc_attr($flag); ?>.png" alt="<?php echo esc_attr($country); ?>" width="20" height="15" style="border-radius:2px;"> <?php endif; ?>
                                    <?php echo esc_html($city . ', ' . $country); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="testi-nav">
                <button class="testi-nav-btn" id="testiPrev" aria-label="Previous review"><i class="fa-solid fa-chevron-left"></i></button>
                <div class="testi-dots" id="testiDots"></div>
                <button class="testi-nav-btn" id="testiNext" aria-label="Next review"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
        </div>
    </div>
</section>
