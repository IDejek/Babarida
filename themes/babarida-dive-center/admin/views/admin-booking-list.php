<?php
/**
 * Admin Booking List
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

// Handle status change
if (isset($_GET['action']) && isset($_GET['booking_id']) && isset($_GET['status']) && wp_verify_nonce($_GET['_wpnonce'] ?? '', 'babarida_booking_action')) {
    Babarida_Booking::update_status(absint($_GET['booking_id']), sanitize_text_field($_GET['status']));
    echo '<div class="notice notice-success"><p>Booking status updated.</p></div>';
}

// Handle delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['booking_id']) && wp_verify_nonce($_GET['_wpnonce'] ?? '', 'babarida_booking_action')) {
    wp_delete_post(absint($_GET['booking_id']), true);
    echo '<div class="notice notice-success"><p>Booking deleted.</p></div>';
}

// Filters
 $filter_status = isset($_GET['filter_status']) ? sanitize_text_field($_GET['filter_status']) : '';
 $filter_date   = isset($_GET['filter_date']) ? sanitize_text_field($_GET['filter_date']) : '';
 $filter_search = isset($_GET['filter_search']) ? sanitize_text_field($_GET['filter_search']) : '';

 $args = array();
if ($filter_status) $args['status'] = $filter_status;
if ($filter_date)   $args['date_from'] = $filter_date;
if ($filter_search) $args['search'] = $filter_search;

 $bookings = Babarida_Booking::get_all($args);
 $statuses = array('pending','confirmed','paid','checked-in','completed','cancelled');
 $nonce = wp_create_nonce('babarida_booking_action');
?>

<div class="babarida-admin-wrap">
    <div class="babarida-panel-header">
        <h2>Bookings</h2>
        <a href="<?php echo esc_url(admin_url('post-new.php?post_type=booking')); ?>" class="button button-primary" style="padding:6px 16px;">
            <i class="fa-solid fa-plus" style="margin-right:4px;"></i> Add Booking
        </a>
    </div>

    <!-- Filters -->
    <div class="babarida-panel" style="margin-bottom:16px;padding:16px 20px;">
        <form method="get" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <input type="hidden" name="page" value="babarida-bookings">
            <div>
                <label style="display:block;font-size:0.75rem;color:#64748B;margin-bottom:4px;">Status</label>
                <select name="filter_status" style="padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;min-width:150px;">
                    <option value="">All Statuses</option>
                    <?php foreach ($statuses as $s) : ?>
                    <option value="<?php echo esc_attr($s); ?>" <?php selected($filter_status, $s); ?>><?php echo esc_html(Babarida_Booking::status_label($s)); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="display:block;font-size:0.75rem;color:#64748B;margin-bottom:4px;">Date From</label>
                <input type="date" name="filter_date" value="<?php echo esc_attr($filter_date); ?>" style="padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;">
            </div>
            <div>
                <label style="display:block;font-size:0.75rem;color:#64748B;margin-bottom:4px;">Search</label>
                <input type="text" name="filter_search" value="<?php echo esc_attr($filter_search); ?>" placeholder="Name, email, reference..." style="padding:8px 12px;border:1px solid #E2E8F0;border-radius:6px;min-width:220px;">
            </div>
            <button type="submit" class="button" style="padding:8px 16px;">Filter</button>
            <a href="<?php echo esc_url(admin_url('admin.php?page=babarida-bookings')); ?>" class="button" style="padding:8px 16px;">Reset</a>
        </form>
    </div>

    <!-- Table -->
    <div class="babarida-panel">
        <div class="babarida-table-wrap">
            <table class="babarida-table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Guest</th>
                        <th>Email</th>
                        <th>Destination</th>
                        <th>Date</th>
                        <th>Guests</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $b) :
                        $color = Babarida_Booking::status_color($b['status']);
                        $label = Babarida_Booking::status_label($b['status']);
                    ?>
                    <tr>
                        <td><strong style="font-size:0.82rem;"><?php echo esc_html($b['reference']); ?></strong></td>
                        <td><?php echo esc_html($b['first_name'] . ' ' . $b['last_name']); ?></td>
                        <td style="font-size:0.8rem;color:#64748B;"><?php echo esc_html($b['email']); ?></td>
                        <td><?php echo esc_html(ucfirst($b['destination'])); ?></td>
                        <td style="white-space:nowrap;"><?php echo esc_html($b['date']); ?></td>
                        <td style="text-align:center;"><?php echo esc_html($b['guests']); ?></td>
                        <td style="font-weight:600;"><?php echo $b['total'] ? '$' . number_format($b['total'], 2) : '—'; ?></td>
                        <td>
                            <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:0.7rem;font-weight:600;color:#fff;background:<?php echo esc_attr($color); ?>;white-space:nowrap;">
                                <?php echo esc_html($label); ?>
                            </span>
                        </td>
                        <td style="white-space:nowrap;">
                            <select onchange="if(this.value){window.location.href=this.value;}" style="padding:4px 6px;border:1px solid #E2E8F0;border-radius:4px;font-size:0.75rem;cursor:pointer;">
                                <option value="">Change...</option>
                                <?php foreach ($statuses as $s) : ?>
                                <?php if ($s !== $b['status']) : ?>
                                <option value="<?php echo esc_url(add_query_arg(array('action'=>'status','booking_id'=>$b['id'],'status'=>$s,'_wpnonce'=>$nonce), admin_url('admin.php?page=babarida-bookings'))); ?>">
                                    <?php echo esc_html(Babarida_Booking::status_label($s)); ?>
                                </option>
                                <?php endif; ?>
                                <?php endforeach; ?>
                                <option value="<?php echo esc_url(add_query_arg(array('action'=>'delete','booking_id'=>$b['id'],'_wpnonce'=>$nonce), admin_url('admin.php?page=babarida-bookings'))); ?>" style="color:#EF4444;">Delete</option>
                            </select>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($bookings)) : ?>
                    <tr><td colspan="9" style="text-align:center;color:#94A3B8;padding:48px;">No bookings found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
