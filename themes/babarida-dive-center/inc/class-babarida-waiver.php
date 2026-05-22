<?php
/**
 * Digital Waiver Signature System
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

class Babarida_Waiver {

    /**
     * Create a waiver record
     */
    public static function create($booking_id, $data) {
        $waiver_id = wp_insert_post(array(
            'post_title'  => sprintf(__('Waiver: %s', 'babarida'), $data['full_name']),
            'post_type'   => 'waiver',
            'post_status' => 'publish',
            'meta_input'  => array(
                '_waiver_booking_id'   => absint($booking_id),
                '_waiver_full_name'    => sanitize_text_field($data['full_name']),
                '_waiver_email'        => sanitize_email($data['email']),
                '_waiver_phone'        => sanitize_text_field($data['phone']),
                '_waiver_dob'          => sanitize_text_field($data['dob']),
                '_waiver_certification'=> sanitize_text_field($data['certification'] ?? ''),
                '_waiver_cert_number'  => sanitize_text_field($data['cert_number'] ?? ''),
                '_waiver_emergency_name'  => sanitize_text_field($data['emergency_name'] ?? ''),
                '_waiver_emergency_phone' => sanitize_text_field($data['emergency_phone'] ?? ''),
                '_waiver_signature_data' => wp_kses_post($data['signature'] ?? ''),
                '_waiver_ip'           => Babarida_Security::get_client_ip(),
                '_waiver_signed_at'    => current_time('mysql'),
                '_waiver_status'       => 'signed',
            ),
        ));
        return $waiver_id;
    }

    /**
     * Check if booking has signed waiver
     */
    public static function is_signed($booking_id) {
        $waivers = get_posts(array(
            'post_type'   => 'waiver',
            'numberposts' => 1,
            'meta_key'    => '_waiver_booking_id',
            'meta_value'  => $booking_id,
            'fields'      => 'ids',
        ));
        return !empty($waivers);
    }

    /**
     * Generate waiver PDF (using HTML-to-PDF approach)
     */
    public static function generate_pdf($waiver_id) {
        $post = get_post($waiver_id);
        if (!$post || $post->post_type !== 'waiver') return false;

        $data = array(
            'full_name'       => get_post_meta($waiver_id, '_waiver_full_name', true),
            'email'           => get_post_meta($waiver_id, '_waiver_email', true),
            'phone'           => get_post_meta($waiver_id, '_waiver_phone', true),
            'dob'             => get_post_meta($waiver_id, '_waiver_dob', true),
            'certification'   => get_post_meta($waiver_id, '_waiver_certification', true),
            'cert_number'     => get_post_meta($waiver_id, '_waiver_cert_number', true),
            'emergency_name'  => get_post_meta($waiver_id, '_waiver_emergency_name', true),
            'emergency_phone' => get_post_meta($waiver_id, '_waiver_emergency_phone', true),
            'signed_at'       => get_post_meta($waiver_id, '_waiver_signed_at', true),
            'signature'       => get_post_meta($waiver_id, '_waiver_signature_data', true),
        );

        ob_start();
        ?>
        <!DOCTYPE html><html><head><meta charset="UTF-8">
        <style>
            body { font-family: 'Helvetica Neue', Arial, sans-serif; padding: 40px; color: #1E293B; line-height: 1.6; }
            h1 { font-size: 24px; color: #001A33; margin-bottom: 8px; }
            h2 { font-size: 16px; color: #0077E6; margin: 24px 0 12px; }
            .subtitle { color: #64748B; font-size: 13px; margin-bottom: 32px; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
            td { padding: 8px 0; border-bottom: 1px solid #E2E8F0; font-size: 13px; }
            td:first-child { color: #64748B; width: 40%; }
            .terms { font-size: 12px; color: #475569; margin-bottom: 32px; }
            .terms p { margin-bottom: 10px; }
            .signature-area { margin-top: 40px; border-top: 2px solid #E2E8F0; padding-top: 20px; }
        </style>
        </head><body>
        <h1>Babarida Dive Center</h1>
        <p class="subtitle">Liability Release & Assumption of Risk Agreement</p>

        <table>
            <tr><td>Full Name</td><td><?php echo esc_html($data['full_name']); ?></td></tr>
            <tr><td>Email</td><td><?php echo esc_html($data['email']); ?></td></tr>
            <tr><td>Phone</td><td><?php echo esc_html($data['phone']); ?></td></tr>
            <tr><td>Date of Birth</td><td><?php echo esc_html($data['dob']); ?></td></tr>
            <tr><td>Certification</td><td><?php echo esc_html($data['certification'] ?: 'N/A'); ?></td></tr>
            <tr><td>Cert. Number</td><td><?php echo esc_html($data['cert_number'] ?: 'N/A'); ?></td></tr>
            <tr><td>Emergency Contact</td><td><?php echo esc_html($data['emergency_name'] . ' — ' . $data['emergency_phone']); ?></td></tr>
        </table>

        <h2>Terms & Conditions</h2>
        <div class="terms">
            <p>I acknowledge that diving, snorkeling, and water sports activities involve inherent risks including but not limited to: drowning, decompression sickness, equipment failure, marine life encounters, and weather-related hazards.</p>
            <p>I certify that I am in good physical condition and have no medical conditions that would prohibit my participation. I agree to follow all instructions given by Babarida Dive Center staff and guides.</p>
            <p>I release Babarida Dive Center, its owners, employees, guides, and partners from any and all liability for injury, death, property loss, or damages arising from my participation in these activities.</p>
            <p>I understand that I am solely responsible for my own safety and that no guarantee or assurance has been made to me regarding the outcome of any activity.</p>
        </div>

        <div class="signature-area">
            <p style="font-size:13px;"><strong>Digital Signature:</strong></p>
            <?php if ($data['signature']) : ?>
            <img src="<?php echo esc_attr($data['signature']); ?>" style="max-height:80px; margin:10px 0;" alt="Signature">
            <?php endif; ?>
            <p style="font-size:12px; color:#94A3B8;">Signed: <?php echo esc_html($data['signed_at']); ?></p>
        </div>
        </body></html>
        <?php
        return ob_get_clean();
    }
}
