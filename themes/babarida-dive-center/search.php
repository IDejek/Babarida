<?php
/**
 * Search Results Template
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

get_header();
?>

<main id="main-content" class="section" style="padding-top:140px;">
    <div class="section-inner" style="max-width:800px;">
        <div style="margin-bottom:40px;">
            <h1 class="section-title">
                <?php printf(esc_html__('Search Results for: %s', 'babarida'), '<span style="color:var(--blue-primary);">' . esc_html(get_search_query()) . '</span>'); ?>
            </h1>
        </div>

        <?php if (have_posts()) : ?>
            <div style="display:flex; flex-direction:column; gap:24px;">
                <?php while (have_posts()) : the_post(); ?>
                    <article <?php post_class(); ?> style="padding:24px; background:var(--white); border-radius:var(--radius-lg); box-shadow:var(--shadow-sm); border:1px solid var(--gray-100); transition:all 0.2s;">
                        <h3 style="font-family:var(--font-display); font-size:1.2rem; font-weight:700; color:var(--blue-deep); margin-bottom:8px;">
                            <a href="<?php the_permalink(); ?>" style="color:inherit;"><?php the_title(); ?></a>
                        </h3>
                        <p style="font-size:0.85rem; color:var(--gray-500); line-height:1.6;">
                            <?php echo wp_trim_words(get_the_excerpt(), 25); ?>
                        </p>
                        <a href="<?php the_permalink(); ?>" style="font-size:0.78rem; font-weight:600; color:var(--blue-primary);"><?php esc_html_e('View', 'babarida'); ?> <i class="fa-solid fa-arrow-right" style="font-size:0.6rem;"></i></a>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <div style="text-align:center; padding:60px 0;">
                <i class="fa-solid fa-magnifying-glass" style="font-size:2.5rem; color:var(--gray-300); margin-bottom:16px;"></i>
                <p style="color:var(--gray-400);"><?php esc_html_e('No results found. Try different keywords.', 'babarida'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
