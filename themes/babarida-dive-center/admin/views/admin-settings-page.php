<?php
/**
 * Admin Settings Page
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

// Handle save
if (isset($_POST['babarida_save_settings']) && wp_verify_nonce($_POST['_wpnonce'] ?? '', 'babarida_settings_nonce')) {
    // General
    update_option('babarida_company_name', sanitize_text_field($_POST['company_name'] ?? ''));
    update_option('babarida_company_email', sanitize_email($_POST['company_email'] ?? ''));
    update_option('babarida_company_phone', sanitize_text_field($_POST['company_phone'] ?? ''));
    update_option('babarida_whatsapp_number', sanitize_text_field($_POST['whatsapp_number'] ?? ''));

    // SEO
    update_option('babarida_gsc_verification', sanitize_text_field($_POST['gsc_verification'] ?? ''));
    update_option('babarida_default_og_image', absint($_POST['default_og_image'] ?? 0));

    // Weather
    update_option('babarida_weather_api_key', sanitize_text_field($_POST['weather_api_key'] ?? ''));

    // WhatsApp API
    update_option('babarida_wa_api_enabled', sanitize_text_field($_POST['wa_api_enabled'] ?? 'no'));
    update_option('babarida_wa_api_url', esc_url_raw($_POST['wa_api_url'] ?? ''));
    update_option('babarida_wa_api_key', sanitize_text_field($_POST['wa_api_key'] ?? ''));

    // Payment gateways
    update_option('babarida_midtrans_enabled', sanitize_text_field($_POST['midtrans_enabled'] ?? 'no'));
    update_option('babarida_midtrans_server_key', sanitize_text_field($_POST['midtrans_server_key'] ?? ''));
    update_option('babarida_midtrans_mode', sanitize_text_field($_POST['midtrans_mode'] ?? 'sandbox'));

    update_option('babarida_xendit_enabled', sanitize_text_field($_POST['xendit_enabled'] ?? 'no'));
    update_option('babarida_xendit_secret_key', sanitize_text_field($_POST['xendit_secret_key'] ?? ''));
    update_option('babarida_xendit_mode', sanitize_text_field($_POST['xendit_mode'] ?? 'sandbox'));

    update_option('babarida_stripe_enabled', sanitize_text_field($_POST['stripe_enabled'] ?? 'no'));
    update_option('babarida_stripe_publishable_key', sanitize_text_field($_POST['stripe_publishable_key'] ?? ''));
    update_option('babarida_stripe_secret_key', sanitize_text_field($_POST['stripe_secret_key'] ?? ''));

    update_option('babarida_paypal_enabled', sanitize_text_field($_POST['paypal_enabled'] ?? 'no'));
    update_option('babarida_paypal_client_id', sanitize_text_field($_POST['paypal_client_id'] ?? ''));
    update_option('babarida_paypal_secret', sanitize_text_field($_POST['paypal_secret'] ?? ''));
    update_option('babarida_paypal_mode', sanitize_text_field($_POST['paypal_mode'] ?? 'sandbox'));

    // Bank transfer details
    $bank = array(
        'bank_name'   => sanitize_text_field($_POST['bank_name'] ?? ''),
        'account_name'=> sanitize_text_field($_POST['bank_account_name'] ?? ''),
        'account_no'  => sanitize_text_field($_POST['bank_account_no'] ?? ''),
        'branch'      => sanitize_text_field($_POST['bank_branch'] ?? ''),
    );
    update_option('babarida_bank_details', $bank);

    echo '<div class="notice notice-success"><p>Settings saved successfully.</p></div>';
}

 $company = get_option('babarida_company_name', get_bloginfo('name'));
 $email   = get_option('babarida_company_email', get_option('admin_email'));
 $phone   = get_option('babarida_company_phone', '+62 895 8019 60359');
 $wa      = get_option('babarida_whatsapp_number', '62895801960359');
?>

<div class="babarida-admin-wrap">
    <div class="babarida-panel-header">
        <h2>Settings</h2>
    </div>

    <form method="post" action="">
        <?php wp_nonce_field('babarida_settings_nonce', '_wpnonce'); ?>

        <!-- General -->
        <div class="babarida-panel" style="margin-bottom:24px;">
            <h3 style="margin-bottom:20px;">General</h3>
            <div class="babarida-form-row">
                <div class="babarida-form-group">
                    <label>Company Name</label>
                    <input type="text" name="company_name" value="<?php echo esc_attr($company); ?>" class="babarida-input">
                </div>
                <div class="babarida-form-group">
                    <label>Email</label>
                    <input type="email" name="company_email" value="<?php echo esc_attr($email); ?>" class="babarida-input">
                </div>
            </div>
            <div class="babarida-form-row">
                <div class="babarida-form-group">
                    <label>Phone</label>
                    <input type="text" name="company_phone" value="<?php echo esc_attr($phone); ?>" class="babarida-input">
                </div>
                <div class="babarida-form-group">
                    <label>WhatsApp Number</label>
                    <input type="text" name="whatsapp_number" value="<?php echo esc_attr($wa); ?>" class="babarida-input">
                </div>
            </div>
        </div>

        <!-- SEO -->
        <div class="babarida-panel" style="margin-bottom:24px;">
            <h3 style="margin-bottom:20px;">SEO & Verification</h3>
            <div class="babarida-form-group">
                <label>Google Search Console Verification Code</label>
                <input type="text" name="gsc_verification" value="<?php echo esc_attr(get_option('babarida_gsc_verification', '')); ?>" class="babarida-input" placeholder="e.g., abc123xyz456">
            </div>
            <div class="babarida-form-group">
                <label>Default OG Image ID</label>
                <input type="number" name="default_og_image" value="<?php echo esc_attr(get_option('babarida_default_og_image', 0)); ?>" class="babarida-input">
                <small style="color:#94A3B8;">WordPress media attachment ID</small>
            </div>
        </div>

        <!-- Weather API -->
        <div class="babarida-panel" style="margin-bottom:24px;">
            <h3 style="margin-bottom:20px;">Weather API</h3>
            <div class="babarida-form-group">
                <label>OpenWeatherMap API Key</label>
                <input type="text" name="weather_api_key" value="<?php echo esc_attr(get_option('babarida_weather_api_key', '')); ?>" class="babarida-input">
            </div>
        </div>

        <!-- Payment Gateways -->
        <div class="babarida-panel" style="margin-bottom:24px;">
            <h3 style="margin-bottom:20px;">Payment Gateways</h3>

            <h4 style="margin:16px 0 12px;">Midtrans</h4>
            <div class="babarida-form-row">
                <div class="babarida-form-group">
                    <label>Enabled</label>
                    <select name="midtrans_enabled" class="babarida-input">
                        <option value="no" <?php selected(get_option('babarida_midtrans_enabled', 'no'), 'no'); ?>>No</option>
                        <option value="yes" <?php selected(get_option('babarida_midtrans_enabled', 'no'), 'yes'); ?>>Yes</option>
                    </select>
                </div>
                <div class="babarida-form-group">
                    <label>Mode</label>
                    <select name="midtrans_mode" class="babarida-input">
                        <option value="sandbox" <?php selected(get_option('babarida_midtrans_mode', 'sandbox'), 'sandbox'); ?>>Sandbox</option>
                        <option value="production" <?php selected(get_option('babarida_midtrans_mode', 'sandbox'), 'production'); ?>>Production</option>
                    </select>
                </div>
            </div>
            <div class="babarida-form-group">
                <label>Server Key</label>
                <input type="password" name="midtrans_server_key" value="<?php echo esc_attr(get_option('babarida_midtrans_server_key', '')); ?>" class="babarida-input">
            </div>

            <h4 style="margin:24px 0 12px;">Xendit</h4>
            <div class="babarida-form-row">
                <div class="babarida-form-group">
                    <label>Enabled</label>
                    <select name="xendit_enabled" class="babarida-input">
                        <option value="no" <?php selected(get_option('babarida_xendit_enabled', 'no'), 'no'); ?>>No</option>
                        <option value="yes" <?php selected(get_option('babarida_xendit_enabled', 'no'), 'yes'); ?>>Yes</option>
                    </select>
                </div>
                <div class="babarida-form-group">
                    <label>Mode</label>
                    <select name="xendit_mode" class="babarida-input">
                        <option value="sandbox" <?php selected(get_option('babarida_xendit_mode', 'sandbox'), 'sandbox'); ?>>Sandbox</option>
                        <option value="production" <?php selected(get_option('babarida_xendit_mode', 'sandbox'), 'production'); ?>>Production</option>
                    </select>
                </div>
            </div>
            <div class="babarida-form-group">
                <label>Secret Key</label>
                <input type="password" name="xendit_secret_key" value="<?php echo esc_attr(get_option('babarida_xendit_secret_key', '')); ?>" class="babarida-input">
            </div>

            <h4 style="margin:24px 0 12px;">Stripe</h4>
            <div class="babarida-form-group">
                <label>Enabled</label>
                <select name="stripe_enabled" class="babarida-input">
                    <option value="no" <?php selected(get_option('babarida_stripe_enabled', 'no'), 'no'); ?>>No</option>
                    <option value="yes" <?php selected(get_option('babarida_stripe_enabled', 'no'), 'yes'); ?>>Yes</option>
                </select>
            </div>
            <div class="babarida-form-row">
                <div class="babarida-form-group">
                    <label>Publishable Key</label>
                    <input type="text" name="stripe_publishable_key" value="<?php echo esc_attr(get_option('babarida_stripe_publishable_key', '')); ?>"
