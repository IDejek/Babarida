<?php
/**
 * Search Form
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label for="search-field" class="screen-reader-text"><?php esc_html_e('Search', 'babarida'); ?></label>
    <div style="display:flex; gap:8px;">
        <input type="search" id="search-field" class="form-input" placeholder="<?php esc_attr_e('Search destinations, trips, courses...', 'babarida'); ?>" value="<?php echo esc_attr(get_search_query()); ?>" name="s" required>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-magnifying-glass"></i></button>
    </div>
</form>
