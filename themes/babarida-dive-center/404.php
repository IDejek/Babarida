<?php
/**
 * 404 Error Page
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

get_header();
?>

<main id="main-content" class="section" style="padding-top:140px; min-height:70vh; display:flex; align-items:center;">
    <div class="section-inner" style="text-align:center; max-width:600px;">
        <div style="font-family:var(--font-display); font-size:8rem; font-weight:700; color:var(--blue-light); line-height:1; margin-bottom:16px;">404</div>
        <h1 style="font-family:var(--font-display); font-size:2rem; font-weight:700; color:var(--blue-deep); margin-bottom:16px;">
            <?php esc_html_e('Page Not Found', 'babarida'); ?>
        </h1>
        <p style="font-size:1.05rem; color:var(--gray-500); line-height:1.7; margin-bottom:32px;">
            <?php esc_html_e('The page you\'re looking for doesn\'t exist or has been moved. Let\'s get you back on track.', 'babarida'); ?>
        </p>
        <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary"><?php esc_html_e('Go Home', 'babarida'); ?></a>
            <a href="<?php echo esc_url(home_url('/blog')); ?>" class="btn btn-secondary"><?php esc_html_e('View Blog', 'babarida'); ?></a>
        </div>
    </div>
</main>

<?php get_footer(); ?>
