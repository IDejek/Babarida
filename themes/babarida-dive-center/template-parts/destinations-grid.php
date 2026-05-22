<?php
/**
 * Destinations Grid Template Part
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

 $destinations = get_terms(array(
    'taxonomy'   => 'destination',
    'hide_empty' => false,
    'orderby'    => 'menu_order',
    'order'      => 'ASC',
));

 $dest_data = array(
    'bunaken'  => array('desc' => 'Famous vertical walls, crystal-clear waters, and over 390 species of coral in the heart of the marine park.', 'img' => 'bunaken-wall-dive-blue'),
    'siladen'  => array('desc' => 'A pristine island paradise with powdery white sand, vibrant shallow reefs, and exceptional snorkeling.', 'img' => 'siladen-beach-turquoise'),
    'bangka'   => array('desc' => 'Dramatic underwater landscapes, soft coral gardens, rare critters in an untouched archipelago.', 'img' => 'bangka-rocky-coast-dive'),
    'lembeh'   => array('desc' => 'The world capital of muck diving — home to the rarest and most bizarre marine creatures on Earth.', 'img' => 'lembeh-muck-dive-critter'),
);
?>

<section class="section destinations" id="destinations" aria-label="Destinations">
    <div class="section-inner">
        <div class="destinations-header">
            <div class="section-label reveal" style="justify-content:center;">Our Destinations</div>
            <h2 class="section-title reveal reveal-delay-1" style="text-align:center;">Four Worlds of Wonder</h2>
            <p class="section-subtitle centered reveal reveal-delay-2">Choose your destination and start planning your next trip across North Sulawesi's most extraordinary marine environments.</p>
        </div>
        <div class="dest-grid">
            <?php
            if (!is_wp_error($destinations)) :
                $delay = 1;
                foreach ($destinations as $dest) :
                    $slug = $dest->slug;
                    $data = isset($dest_data[$slug]) ? $dest_data[$slug] : array('desc' => $dest->description, 'img' => 'ocean-reef-generic');
                    $img_id = get_term_meta($dest->term_id, 'destination_image', true);
            ?>
            <article class="dest-card reveal reveal-delay-<?php echo $delay; ?>">
                <?php if ($img_id) : ?>
                    <?php echo wp_get_attachment_image($img_id, 'dest-card', false, array('loading' => 'lazy', 'style' => 'width:100%;height:100%;object-fit:cover;')); ?>
                <?php else : ?>
                    <img src="https://picsum.photos/seed/<?php echo esc_attr($data['img']); ?>/600/800.jpg" alt="<?php echo esc_attr($dest->name); ?>" loading="lazy" width="600" height="800">
                <?php endif; ?>
                <div class="dest-card-overlay">
                    <h3 class="dest-card-name"><?php echo esc_html($dest->name); ?></h3>
                    <p class="dest-card-desc"><?php echo esc_html($data['desc']); ?></p>
                    <a href="<?php echo esc_url(get_term_link($dest)); ?>" class="dest-card-cta">Explore <?php echo esc_html($dest->name); ?> <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </article>
            <?php
                $delay++;
                endforeach;
            endif;
            ?>
        </div>
    </div>
</section>
