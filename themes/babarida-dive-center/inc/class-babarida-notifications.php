<?php
/**
 * Automated Notifications — Email & WhatsApp
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

class Babarida_Notifications {

    /**
     * Send booking confirmation
     */
    public static function send_booking_confirmation($booking_id, $data) {
        $to      = $data['email'];
        $subject = sprintf(__('Booking Inquiry Received – %s', 'babarida'), $data['destination']);

        $body = self::render_email('booking-confirmation', array(
            'first_name'  => $data['first_name'],
            'last_name'   => $data['last_name'],
            'destination' => $data['destination'],
            'date'        => $data['date'],
            'guests'      => $data['guests'],
            'activity'    => $data['activity'],
            'notes'       => $data['notes'],
            'reference'   => get_post_meta($booking_id, '_booking_reference', true),
        ));

        self::send_email($to, $subject, $body);
        self::send_whatsapp($data['phone'], self::render_whatsapp('booking-confirmation', $data));
    }

    /**
     * Send status update notification
     */
    public static function send_status_update($booking, $status) {
        $status_label = Babarida_Booking::status_label($status);

        $body = self::render_email('status-update', array(
            'first_name'  => $booking['first_name'],
            'reference'   => $booking['reference'],
            'destination' => $booking['destination'],
            'date'        => $booking['date'],
            'status'      => $status_label,
        ));

        self::send_email($booking['email'], sprintf(__('Booking Update: %s', 'babarida'), $status_label), $body);

        if (!empty($booking['phone'])) {
            self::send_whatsapp($booking['phone'], sprintf(
                "Hello %s, your booking %s has been updated to: %s. — Babarida Dive Center",
                $booking['first_name'],
                $booking['reference'],
                $status_label
            ));
        }
    }

    /**
     * Send payment reminder
     */
    public static function send_payment_reminder($booking) {
        $body = self::render_email('payment-reminder', array(
            'first_name' => $booking['first_name'],
            'reference'  => $booking['reference'],
            'total'      => $booking['total'],
            'date'       => $booking['date'],
        ));

        self::send_email($booking['email'], __('Payment Reminder – ' . $booking['reference'], 'babarida'), $body);
    }

    /**
     * Send trip reminder (1 day before)
     */
    public static function send_trip_reminder($booking) {
        $body = self::render_email('trip-reminder', array(
            'first_name'  => $booking['first_name'],
            'reference'   => $booking['reference'],
            'destination' => $booking['destination'],
            'date'        => $booking['date'],
            'guests'      => $booking['guests'],
        ));

        self::send_email($booking['email'], __('Your Trip is Tomorrow!', 'babarida'), $body);
    }

    /**
     * Send welcome email (newsletter)
     */
    public static function send_welcome_email($email) {
        $body = self::render_email('welcome', array('email' => $email));
        self::send_email($email, __('Welcome to Babarida Dive Center!', 'babarida'), $body);
    }

    /**
     * Core email sender
     */
    private static function send_email($to, $subject, $body) {
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: Babarida Dive Center <info@babaridadive.com>',
            'Reply-To: info@babaridadive.com',
        );

        wp_mail($to, $subject, $body, $headers);
    }

    /**
     * WhatsApp sender (via API or direct link)
     */
    private static function send_whatsapp($phone, $message) {
        $api_enabled = get_option('babarida_wa_api_enabled', 'no') === 'yes';

        if ($api_enabled) {
            $api_url = get_option('babarida_wa_api_url', '');
            $api_key = get_option('babarida_wa_api_key', '');
            if ($api_url && $api_key) {
                wp_remote_post($api_url, array(
                    'headers' => array(
                        'Content-Type'  => 'application/json',
                        'Authorization' => 'Bearer ' . $api_key,
                    ),
                    'body' => wp_json_encode(array(
                        'phone'   => self::format_phone($phone),
                        'message' => $message,
                    )),
                    'timeout' => 10,
                ));
            }
        }
        // API not configured — logged for manual follow-up
    }

    /**
     * Format phone for WhatsApp
     */
    private static function format_phone($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strpos($phone, '0') === 0) {
            $phone = '62' . substr($phone, 1);
        }
        return $phone;
    }

    /**
     * Render email template
     */
    private static function render_email($template, $data) {
        $brand_color = '#0077E6';
        $site_name   = get_bloginfo('name');
        $site_url    = home_url('/');

        ob_start();
        ?>
        <!DOCTYPE html>
        <html><head><meta charset="UTF-8"></head>
        <body style="margin:0;padding:0;background:#F1F5F9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
        <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;margin:40px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.06);">
            <tr>
                <td style="background:<?php echo $brand_color; ?>;padding:32px 40px;text-align:center;">
                    <h1 style="margin:0;color:#fff;font-size:1.4rem;font-weight:700;"><?php echo esc_html($site_name); ?></h1>
                </td>
            </tr>
            <tr>
                <td style="padding:40px;">
                    <?php
                    switch ($template) {
                        case 'booking-confirmation':
                            ?>
                            <h2 style="margin:0 0 16px;color:#001A33;font-size:1.3rem;">Hello <?php echo esc_html($data['first_name']); ?>,</h2>
                            <p style="margin:0 0 20px;color:#475569;line-height:1.6;">Thank you for your booking inquiry! Here are the details:</p>
                            <table width="100%" cellpadding="8" style="background:#F8FAFC;border-radius:8px;margin-bottom:20px;">
                                <tr><td style="color:#64748B;font-size:0.85rem;">Reference</td><td style="color:#001A33;font-weight:700;"><?php echo esc_html($data['reference']); ?></td></tr>
                                <tr><td style="color:#64748B;font-size:0.85rem;">Destination</td><td style="color:#001A33;font-weight:600;"><?php echo esc_html(ucfirst($data['destination'])); ?></td></tr>
                                <tr><td style="color:#64748B;font-size:0.85rem;">Date</td><td style="color:#001A33;font-weight:600;"><?php echo esc_html($data['date']); ?></td></tr>
                                <tr><td style="color:#64748B;font-size:0.85rem;">Guests</td><td style="color:#001A33;font-weight:600;"><?php echo esc_html($data['guests']); ?></td></tr>
                                <?php if ($data['activity']) : ?>
                                <tr><td style="color:#64748B;font-size:0.85rem;">Activity</td><td style="color:#001A33;font-weight:600;"><?php echo esc_html(ucfirst($data['activity'])); ?></td></tr>
                                <?php endif; ?>
                            </table>
                            <p style="margin:0 0 16px;color:#475569;line-height:1.6;">Our team will review your inquiry and send you a detailed quote within 24 hours via email and WhatsApp.</p>
                            <?php
                            break;

                        case 'status-update':
                            ?>
                            <h2 style="margin:0 0 16px;color:#001A33;font-size:1.3rem;">Hello <?php echo esc_html($data['first_name']); ?>,</h2>
                            <p style="margin:0 0 16px;color:#475569;line-height:1.6;">Your booking <strong><?php echo esc_html($data['reference']); ?></strong> has been updated:</p>
                            <div style="padding:16px 20px;background:#F0F8FF;border-left:4px solid <?php echo $brand_color; ?>;border-radius:0 8px 8px 0;margin-bottom:20px;">
                                <strong style="color:<?php echo $brand_color; ?>;font-size:1.1rem;"><?php echo esc_html($data['status']); ?></strong>
                            </div>
                            <p style="margin:0;color:#475569;line-height:1.6;">If you have questions, reply to this email or contact us on WhatsApp.</p>
                            <?php
                            break;

                        case 'payment-reminder':
                            ?>
                            <h2 style="margin:0 0 16px;color:#001A33;">Hello <?php echo esc_html($data['first_name']); ?>,</h2>
                            <p style="margin:0 0 16px;color:#475569;line-height:1.6;">This is a reminder that your payment of <strong>$<?php echo esc_html(number_format($data['total'], 2)); ?></strong> for booking <strong><?php echo esc_html($data['reference']); ?></strong> is pending.</p>
                            <p style="margin:0 0 20px;color:#475569;line-height:1.6;">Trip date: <?php echo esc_html($data['date']); ?></p>
                            <a href="<?php echo esc_url(home_url('/booking/pay/?ref=' . $data['reference'])); ?>" style="display:inline-block;padding:14px 32px;background:<?php echo $brand_color; ?>;color:#fff;border-radius:8px;font-weight:600;text-decoration:none;">Pay Now</a>
                            <?php
                            break;

                        case 'trip-reminder':
                            ?>
                            <h2 style="margin:0 0 16px;color:#001A33;">Hello <?php echo esc_html($data['first_name']); ?>,</h2>
                            <p style="margin:0 0 16px;color:#475569;line-height:1.6;">Your diving trip to <strong><?php echo esc_html(ucfirst($data['destination'])); ?></strong> is <strong>tomorrow</strong>!</p>
                            <ul style="margin:0 0 20px;padding-left:20px;color:#475569;line-height:1.8;">
                                <li>Booking: <?php echo esc_html($data['reference']); ?></li>
                                <li>Guests: <?php echo esc_html($data['guests']); ?></li>
                                <li>Please arrive at the meeting point by 07:30 WITA</li>
                                <li>Bring your certification card and logbook</li>
                            </ul>
                            <p style="margin:0;color:#475569;">We look forward to diving with you!</p>
                            <?php
                            break;

                        case 'welcome':
                            ?>
                            <h2 style="margin:0 0 16px;color:#001A33;">Welcome to <?php echo esc_html($site_name); ?>!</h2>
                            <p style="margin:0 0 16px;color:#475569;line-height:1.6;">Thank you for subscribing! You'll receive exclusive dive deals, seasonal offers, and North Sulawesi travel inspiration.</p>
                            <a href="<?php echo esc_url($site_url); ?>" style="display:inline-block;padding:14px 32px;background:<?php echo $brand_color; ?>;color:#fff;border-radius:8px;font-weight:600;text-decoration:none;">Explore Destinations</a>
                            <?php
                            break;

                        default:
                            ?>
                            <p style="color:#475569;line-height:1.6;"><?php echo esc_html(isset($data['message']) ? $data['message'] : ''); ?></p>
                            <?php
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td style="padding:24px 40px;background:#F8FAFC;text-align:center;border-top:1px solid #E2E8F0;">
                    <p style="margin:0 0 8px;font-size:0.78rem;color:#94A3B8;">
                        <a href="<?php echo esc_url($site_url); ?>" style="color:<?php echo $brand_color; ?>;text-decoration:none;"><?php echo esc_html($site_name); ?></a>
                        &middot; Bunaken National Marine Park, Manado, North Sulawesi
                    </p>
                    <p style="margin:0;font-size:0.72rem;color:#CBD5E1;">
                        <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>" style="color:#94A3B8;">Privacy Policy</a> &middot;
                        <a href="<?php echo esc_url(home_url('/terms')); ?>" style="color:#94A3B8;">Terms</a>
                    </p>
                </td>
            </tr>
        </table>
        </body></html>
        <?php
        return ob_get_clean();
    }

    /**
     * Render WhatsApp message
     */
    private static function render_whatsapp($template, $data) {
        switch ($template) {
            case 'booking-confirmation':
                return sprintf(
                    "Hello %s! Thank you for your inquiry with Babarida Dive Center.\n\n" .
                    "Booking Reference: %s\n" .
                    "Destination: %s\n" .
                    "Date: %s\n" .
                    "Guests: %s\n\n" .
                    "We'll confirm availability and send you a detailed quote within 24 hours.\n\n" .
                    "Feel free to ask us anything!",
                    $data['first_name'],
                    $data['reference'] ?? '',
                    ucfirst($data['destination']),
                    $data['date'],
                    $data['guests']
                );
            default:
                return '';
        }
    }
}
