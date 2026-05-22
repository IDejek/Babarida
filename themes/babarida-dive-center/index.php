<?php
/**
 * Main Index Template (fallback)
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

get_header();
?>

<main id="main-content" class="section">
    <div class="section-inner">
        <?php if (is_home() && !is_front_page()) : ?>
            <div class="section-label" style="justify-content:center;"><?php esc_html_e('Blog', 'babarida'); ?></div>
            <h1 class="section-title" style="text-align:center; margin-bottom:48px;"><?php esc_html_e('Latest Stories', 'babarida'); ?></h1>
        <?php endif; ?>

        <?php if (have_posts()) : ?>
            <div class="blog-grid" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap:28px;">
                <?php while (have_posts()) : the_post(); ?>
                    <article <?php post_class('blog-card'); ?> style="background:var(--white); border-radius:var(--radius-xl); overflow:hidden; box-shadow:var(--shadow-md); border:1px solid var(--gray-100); transition:all 0.3s ease;">
                        <?php if (has_post_thumbnail()) : ?>
                        <a href="<?php the_permalink(); ?>" style="display:block; height:220px; overflow:hidden;">
                            <?php the_post_thumbnail('boat-card', array('style' => 'width:100%;height:100%;object-fit:cover;transition:transform 0.6s ease;')); ?>
                        </a>
                        <?php endif; ?>
                        <div style="padding:24px;">
                            <div style="font-size:0.7rem; color:var(--gray-400); margin-bottom:8px; text-transform:uppercase; letter-spacing:0.1em; font-weight:600;">
                                <?php echo get_the_date(); ?> &middot; <?php the_category(', '); ?>
                            </div>
                            <h3 style="font-family:var(--font-display); font-size:1.15rem; font-weight:700; color:var(--blue-deep); margin-bottom:10px; line-height:1.3;">
                                <a href="<?php the_permalink(); ?>" style="color:inherit; transition:color 0.2s;"><?php the_title(); ?></a>
                            </h3>
                            <p style="font-size:0.85rem; color:var(--gray-500); line-height:1.6; margin-bottom:16px;">
                                <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                            </p>
                            <a href="<?php the_permalink(); ?>" style="font-size:0.8rem; font-weight:600; color:var(--blue-primary); display:inline-flex; align-items:center; gap:6px;">
                                <?php esc_html_e('Read More', 'babarida'); ?> <i class="fa-solid fa-arrow-right" style="font-size:0.65rem;"></i>
                            </a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <div style="display:flex; justify-content:center; gap:8px; margin-top:48px;">
                <?php
                the_posts_pagination(array(
                    'mid_size'  => 2,
                    'prev_text' => '<i class="fa-solid fa-chevron-left"></i>',
                    'next_text' => '<i class="fa-solid fa-chevron-right"></i>',
                    'class'     => 'pagination',
                ));
                ?>
            </div>
        <?php else : ?>
            <div style="text-align:center; padding:80px 0;">
                <i class="fa-solid fa-file-circle-question" style="font-size:3rem; color:var(--gray-300); margin-bottom:16px;"></i>
                <p style="font-size:1.1rem; color:var(--gray-400);"><?php esc_html_e('No posts found.', 'babarida'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
