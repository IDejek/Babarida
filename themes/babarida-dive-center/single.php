<?php
/**
 * Single Post Template
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
                <div style="margin-bottom:24px;">
                    <?php
                    $cats = get_the_category();
                    if ($cats) {
                        echo '<span style="font-size:0.72rem; font-weight:600; text-transform:uppercase; letter-spacing:0.12em; color:var(--blue-primary);">';
                        foreach ($cats as $cat) {
                            echo '<a href="' . esc_url(get_category_link($cat->term_id)) . '" style="color:inherit;">' . esc_html($cat->name) . '</a> ';
                        }
                        echo '</span>';
                    }
                    ?>
                </div>

                <h1 style="font-family:var(--font-display); font-size:clamp(2rem,4vw,2.8rem); font-weight:700; color:var(--blue-deep); line-height:1.15; margin-bottom:16px;">
                    <?php the_title(); ?>
                </h1>

                <div style="display:flex; align-items:center; gap:16px; margin-bottom:32px; font-size:0.82rem; color:var(--gray-400);">
                    <span><i class="fa-regular fa-calendar" style="margin-right:4px;"></i> <?php echo get_the_date(); ?></span>
                    <span><i class="fa-regular fa-user" style="margin-right:4px;"></i> <?php the_author(); ?></span>
                    <span><i class="fa-regular fa-clock" style="margin-right:4px;"></i> <?php echo babarida_reading_time(); ?></span>
                </div>

                <?php if (has_post_thumbnail()) : ?>
                <div style="margin-bottom:40px; border-radius:var(--radius-xl); overflow:hidden; box-shadow:var(--shadow-lg);">
                    <?php the_post_thumbnail('gallery-full', array('style' => 'width:100%;height:auto;')); ?>
                </div>
                <?php endif; ?>

                <div style="font-size:1.05rem; color:var(--gray-700); line-height:1.9;">
                    <?php the_content(); ?>
                </div>

                <?php
                wp_link_pages(array(
                    'before' => '<div class="page-links" style="margin-top:32px; padding-top:24px; border-top:1px solid var(--gray-100);">',
                    'after'  => '</div>',
                ));
                ?>

                <!-- Tags -->
                <?php
                $tags = get_the_tags();
                if ($tags) :
                ?>
                <div style="margin-top:40px; padding-top:24px; border-top:1px solid var(--gray-100); display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                    <i class="fa-solid fa-tags" style="color:var(--gray-400); font-size:0.8rem;"></i>
                    <?php foreach ($tags as $tag) : ?>
                        <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" style="padding:4px 12px; background:var(--gray-50); border-radius:var(--radius-full); font-size:0.75rem; color:var(--gray-500); transition:all 0.2s;">
                            <?php echo esc_html($tag->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Post Navigation -->
                <div style="margin-top:48px; display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                    <div>
                        <?php
                        $prev = get_previous_post();
                        if (!empty($prev)) :
                        ?>
                        <a href="<?php echo esc_url(get_permalink($prev->ID)); ?>" style="display:block; padding:20px; background:var(--gray-50); border-radius:var(--radius-lg); transition:all 0.2s;">
                            <span style="font-size:0.7rem; color:var(--gray-400); text-transform:uppercase; letter-spacing:0.1em;"><i class="fa-solid fa-arrow-left" style="margin-right:4px;"></i> <?php esc_html_e('Previous', 'babarida'); ?></span>
                            <span style="display:block; font-size:0.9rem; font-weight:600; color:var(--blue-deep); margin-top:4px;"><?php echo esc_html(wp_trim_words($prev->post_title, 8)); ?></span>
                        </a>
                        <?php endif; ?>
                    </div>
                    <div style="text-align:right;">
                        <?php
                        $next = get_next_post();
                        if (!empty($next)) :
                        ?>
                        <a href="<?php echo esc_url(get_permalink($next->ID)); ?>" style="display:block; padding:20px; background:var(--gray-50); border-radius:var(--radius-lg); transition:all 0.2s;">
                            <span style="font-size:0.7rem; color:var(--gray-400); text-transform:uppercase; letter-spacing:0.1em;"><?php esc_html_e('Next', 'babarida'); ?> <i class="fa-solid fa-arrow-right" style="margin-left:4px;"></i></span>
                            <span style="display:block; font-size:0.9rem; font-weight:600; color:var(--blue-deep); margin-top:4px;"><?php echo esc_html(wp_trim_words($next->post_title, 8)); ?></span>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Comments -->
                <?php if (comments_open() || get_comments_number()) : ?>
                    <div style="margin-top:60px;">
                        <?php comments_template(); ?>
                    </div>
                <?php endif; ?>

            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
