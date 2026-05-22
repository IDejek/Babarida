<?php
/**
 * Admin Pricing Editor
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

// Handle save
if (isset($_POST['babarida_save_pricing']) && wp_verify_nonce($_POST['_wpnonce'] ?? '', 'babarida_pricing_nonce')) {
    $overrides = $_POST['price_override'] ?? array();
    foreach ($overrides as $key => $val) {
        if ($val !== '' && is_numeric($val)) {
            update_option('babarida_pricing_' . sanitize_text_field($key), floatval($val));
        } else {
            delete_option('babarida_pricing_' . sanitize_text_field($key));
        }
    }
    echo '<div class="notice notice-success"><p>Pricing overrides saved.</p></div>';
    delete_transient('babarida_currency_rates');
}

 $pricing_table = Babarida_Pricing::get_pricing_table();
 $products = array('day_trip','liveaboard','course');
 $product_labels = array('day_trip' => 'Day Trip (2 Dives)', 'liveaboard' => 'Liveaboard (3N)', 'course' => 'SSI Open Water');
?>

<div class="babarida-admin-wrap">
    <div class="babarida-panel-header">
        <h2>Dynamic Pricing</h2>
    </div>

    <div class="babarida-panel" style="margin-bottom:16px;">
        <p style="color:#64748B;margin:0;">Base prices are calculated automatically by season. Enter values below to override specific month/product combinations. Leave blank to use the default.</p>
    </div>

    <form method="post">
        <?php wp_nonce_field('babarida_pricing_nonce', '_wpnonce'); ?>
        <div class="babarida-panel">
            <div class="babarida-table-wrap">
                <table class="babarida-table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Season</th>
                            <?php foreach ($product_labels as $pl) : ?>
                            <th><?php echo esc_html($pl); ?></th>
                            <th>Override</th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pricing_table as $row) : ?>
                        <tr>
                            <td style="font-weight:600;"><?php echo esc_html($row['month']); ?></td>
                            <td><span class="season-badge <?php echo esc_attr($row['season_class']); ?>"><?php echo esc_html($row['season_label']); ?></span></td>
                            <?php foreach ($products as $prod) : ?>
                            <td style="font-weight:600;color:#0077E6;">$<?php echo esc_html(number_format($row[$prod], 0)); ?></td>
                            <td>
                                <input type="number" name="price_override[<?php echo esc_attr($row['month'] . '_' . $prod); ?>]" class="babarida-input" style="width:100px;padding:4px 8px;font-size:0.82rem;" placeholder="$" step="1" min="0"
                                    value="<?php echo esc_attr(get_option('babarida_pricing_' . $row['month'] . '_' . $prod, '')); ?>">
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div style="margin-top:16px;text-align:right;">
            <button type="submit" name="babarida_save_pricing" value="1" class="button button-primary" style="padding:10px 32px;">
                <i class="fa-solid fa-floppy-disk" style="margin-right:6px;"></i> Save Pricing
            </button>
        </div>
    </form>
</div>

<style>
.season-badge { display:inline-block;padding:2px 10px;border-radius:20px;font-size:0.7rem;font-weight:600; }
.season-low { background:#D1FAE5; color:#059669; }
.season-high { background:#FEF3C7; color:#D97706; }
.season-peak { background:#FEE2E2; color:#DC2626; }
</style>
