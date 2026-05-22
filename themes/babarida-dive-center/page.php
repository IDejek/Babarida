<?php
/**
 * Page Template
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

get_header();
?>

<main id="main-content" class="section" style="padding-top:140px;">
    <div class="section-inner" style="max-width:800px;">
        <?php while (have_posts()) : the_post(); ?>
            <article <?php post_class(); ?>>
                <?php if (has_post_thumbnail()) : ?>
                <div style="margin-bottom:40px; border-radius:var(--radius-xl); overflow:hidden; box-shadow:var(--shadow-lg);">
                    <?php the_post_thumbnail('gallery-full', array('style' => 'width:100%;height:auto;')); ?>
                </div>
                <?php endif; ?>

                <h1 style="font-family:var(--font-display); font-size:clamp(2rem,4vw,2.8rem); font-weight:700; color:var(--blue-deep); line-height:1.15; margin-bottom:24px;">
                    <?php the_title(); ?>
                </h1>

                <div style="font-size:1.05rem; color:var(--gray-700); line-height:1.9;">
                    <?php the_content(); ?>
                </div>

                <?php
                wp_link_pages(array(
                    'before' => '<div class="page-links" style="margin-top:32px;">',
                    'after'  => '</div>',
                ));
                ?>
            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
