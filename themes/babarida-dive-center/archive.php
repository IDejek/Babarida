<?php
/**
 * Archive Template
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

get_header();

 $post_type = get_post_type();
 $archive_title = babarida_get_archive_title($post_type);
?>

<main id="main-content" class="section" style="padding-top:140px;">
    <div class="section-inner">
        <div style="text-align:center; margin-bottom:48px;">
            <div class="section-label" style="justify-content:center;"><?php echo esc_html($post_type ? $post_type : 'Archive'); ?></div>
            <h1 class="section-title" style="text-align:center;"><?php echo esc_html($archive_title); ?></h1>
        </div>

        <!-- Filters -->
        <?php if (in_array($post_type, array('trip', 'liveaboard', 'hotel'))) : ?>
        <div class="pricing-controls reveal" style="margin-bottom:40px;">
            <?php
            $tax = $post_type === 'trip' ? 'activity' : ($post_type === 'liveaboard' ? 'boat_type' : '');
            if ($tax) :
                $terms = get_terms(array('taxonomy' => $tax, 'hide_empty' => true));
                if (!is_wp_error($terms)) :
                    echo '<button class="pricing-filter-btn active" data-filter="all">' . esc_html__('All', 'babarida') . '</button>';
                    foreach ($terms as $term) :
                        echo '<button class="pricing-filter-btn" data-filter="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</button>';
                    endforeach;
                endif;
            endif;
            ?>
        </div>
        <?php endif; ?>

        <?php if (have_posts()) : ?>
            <div class="boat-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('template-parts/card', $post_type); ?>
                <?php endwhile; ?>
            </div>

            <div style="display:flex; justify-content:center; margin-top:48px;">
                <?php
                the_posts_pagination(array(
                    'mid_size'  => 2,
                    'prev_text' => '<i class="fa-solid fa-chevron-left"></i>',
                    'next_text' => '<i class="fa-solid fa-chevron-right"></i>',
                ));
                ?>
            </div>
        <?php else : ?>
            <div style="text-align:center; padding:80px 0;">
                <i class="fa-solid fa-magnifying-glass" style="font-size:3rem; color:var(--gray-300); margin-bottom:16px;"></i>
                <p style="font-size:1.1rem; color:var(--gray-400);"><?php esc_html_e('No items found.', 'babarida'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
