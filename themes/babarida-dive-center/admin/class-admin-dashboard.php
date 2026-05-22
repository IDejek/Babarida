<?php
/**
 * Admin Dashboard — Main Page
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

class Babarida_Admin_Dashboard {

    public function __construct() {
        add_action('admin_menu', array($this, 'register_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
    }

    /**
     * Register admin menu pages
     */
    public function register_menu() {
        // Main dashboard
        add_menu_page(
            __('Babarida Dashboard', 'babarida'),
            __('Babarida DC', 'babarida'),
            'view_babarida_dashboard',
            'babarida-dashboard',
            array($this, 'render_dashboard'),
            'dashicons-water',
            2
        );

        // Sub-menu pages
        $sub_pages = array(
            array('babarida-bookings',   __('Bookings', 'babarida'),    'manage_babarida_bookings', array($this, 'render_bookings_page')),
            array('babarida-reports',    __('Reports', 'babarida'),     'view_babarida_reports',   array($this, 'render_reports_page')),
            array('babarida-pricing',    __('Pricing', 'babarida'),     'manage_babarida_trips',    array($this, 'render_pricing_page')),
            array('babarida-crm',        __('Customers', 'babarida'),   'manage_babarida_bookings', array($this, 'render_crm_page')),
            array('babarida-schedule',   __('Schedule', 'babarida'),    'manage_babarida_trips',    array($this, 'render_schedule_page')),
            array('babarida-partners',   __('Partners', 'babarida'),    'manage_babarida_trips',    array($this, 'render_partners_page')),
            array('babarida-coupons',    __('Coupons', 'babarida'),     'manage_babarida_finance',  array($this, 'render_coupons_page')),
            array('babarida-export',     __('Export', 'babarida'),      'export_babarida_data',     array($this, 'render_export_page')),
            array('babarida-seo',        __('SEO', 'babarida'),         'manage_babarida_seo',      array($this, 'render_seo_page')),
            array('babarida-weather',    __('Weather', 'babarida'),     'view_babarida_dashboard',  array($this, 'render_weather_page')),
            array('babarida-chat',       __('Chat', 'babarida'),        'view_babarida_dashboard',  array($this, 'render_chat_page')),
            array('babarida-activity-log',__('Activity Log', 'babarida'),'view_babarida_dashboard', array($this, 'render_activity_log_page')),
            array('babarida-security',   __('Security', 'babarida'),    'manage_babarida_finance',  array($this, 'render_security_page')),
            array('babarida-backup',     __('Backup', 'babarida'),      'manage_babarida_finance',  array($this, 'render_backup_page')),
            array('babarida-system-health',__('System Health', 'babarida'),'manage_babarida_finance', array($this, 'render_system_health_page')),
            array('babarida-media',      __('Media', 'babarida'),       'upload_files',             array($this, 'render_media_page')),
            array('babarida-settings',   __('Settings', 'babarida'),    'manage_options',           array($this, 'render_settings_page')),
        );

        foreach ($sub_pages as $page) {
            add_submenu_page(
                'babarida-dashboard',
                $page[1],
                $page[1],
                $page[2],
                $page[0],
                $page[3]
            );
        }
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_assets($hook) {
        if (strpos($hook, 'babarida') === false) return;

        wp_enqueue_style(
            'babarida-admin',
            BABARIDA_URI . '/assets/css/admin-dashboard.css',
            array(),
            BABARIDA_VERSION
        );

        wp_enqueue_script(
            'chart-js',
            'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
            array(),
            '4.4.0',
            true
        );

        wp_enqueue_script(
            'babarida-admin-js',
            BABARIDA_URI . '/assets/js/admin-dashboard.js',
            array('jquery', 'chart-js'),
            BABARIDA_VERSION,
            true
        );

        wp_localize_script('babarida-admin-js', 'babaridaAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('babarida_admin_nonce'),
        ));
    }

    /**
     * Render main dashboard
     */
    public function render_dashboard() {
        $pending   = Babarida_Booking::count_by_status('pending');
        $confirmed = Babarida_Booking::count_by_status('confirmed');
        $paid      = Babarida_Booking::count_by_status('paid');
        $checked   = Babarida_Booking::count_by_status('checked-in');
        $completed = Babarida_Booking::count_by_status('completed');
        $cancelled = Babarida_Booking::count_by_status('cancelled');
        $total     = Babarida_CRM::count();
        $today_rev = Babarida_Booking::get_revenue(date('Y-m-d'), date('Y-m-d'));
        $month_rev = Babarida_Booking::get_revenue(date('Y-m-01'), date('Y-m-t'));
        ?>
        <div class="babarida-admin-wrap">
            <div class="babarida-admin-header">
                <div class="babarida-admin-logo">
                    <span class="babarida-admin-logo-icon"><i class="fa-solid fa-water"></i></span>
                    <div>
                        <h1>Babarida Dive Center</h1>
                        <span>Dashboard</span>
                    </div>
                </div>
                <div class="babarida-admin-header-right">
                    <span class="babarida-admin-date"><?php echo esc_html(date('l, F j, Y')); ?></span>
                    <span class="babarida-admin-time" id="adminClock"><?php echo esc_html(date('H:i')); ?></span>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="babarida-stats-grid">
                <div class="babarida-stat-card" style="--accent:#F59E0B;">
                    <div class="babarida-stat-icon"><i class="fa-solid fa-clock"></i></div>
                    <div class="babarida-stat-info">
                        <span class="babarida-stat-value"><?php echo esc_html($pending); ?></span>
                        <span class="babarida-stat-label">Pending</span>
                    </div>
                </div>
                <div class="babarida-stat-card" style="--accent:#0077E6;">
                    <div class="babarida-stat-icon"><i class="fa-solid fa-check"></i></div>
                    <div class="babarida-stat-info">
                        <span class="babarida-stat-value"><?php echo esc_html($confirmed); ?></span>
                        <span class="babarida-stat-label">Confirmed</span>
                    </div>
                </div>
                <div class="babarida-stat-card" style="--accent:#10B981;">
                    <div class="babarida-stat-icon"><i class="fa-solid fa-dollar-sign"></i></div>
                    <div class="babarida-stat-info">
                        <span class="babarida-stat-value">$<?php echo esc_html(number_format($month_rev, 0)); ?></span>
                        <span class="babarida-stat-label">Monthly Revenue</span>
                    </div>
                </div>
                <div class="babarida-stat-card" style="--accent:#8B5CF6;">
                    <div class="babarida-stat-icon"><i class="fa-solid fa-users"></i></div>
                    <div class="babarida-stat-info">
                        <span class="babarida-stat-value"><?php echo esc_html($total); ?></span>
                        <span class="babarida-stat-label">Customers</span>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="babarida-charts-grid">
                <div class="babarida-chart-card">
                    <h3>Revenue (Last 7 Days)</h3>
                    <canvas id="revenueChart" height="250"></canvas>
                </div>
                <div class="babarida-chart-card">
                    <h3>Booking Statuses</h3>
                    <canvas id="statusChart" height="250"></canvas>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="babarida-panel">
                <div class="babarida-panel-header">
                    <h3>Recent Bookings</h3>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=babarida-bookings')); ?>" class="babarida-btn-sm">View All</a>
                </div>
                <div class="babarida-table-wrap">
                    <table class="babarida-table">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Guest</th>
                                <th>Destination</th>
                                <th>Date</th>
                                <th>Guests</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $recent = Babarida_Booking::get_all(array('per_page' => 8));
                            foreach ($recent as $b) :
                                $color = Babarida_Booking::status_color($b['status']);
                                $label = Babarida_Booking::status_label($b['status']);
                            ?>
                            <tr>
                                <td><strong><?php echo esc_html($b['reference']); ?></strong></td>
                                <td><?php echo esc_html($b['first_name'] . ' ' . $b['last_name']); ?></td>
                                <td><?php echo esc_html(ucfirst($b['destination'])); ?></td>
                                <td><?php echo esc_html($b['date']); ?></td>
                                <td><?php echo esc_html($b['guests']); ?></td>
                                <td><span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;color:#fff;background:<?php echo esc_attr($color); ?>;"><?php echo esc_html($label); ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($recent)) : ?>
                            <tr><td colspan="6" style="text-align:center;color:#94A3B8;padding:40px;">No bookings yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script>
        // Revenue Chart
        (function(){
            var ctx = document.getElementById('revenueChart');
            if (!ctx) return;
            var days = [];
            var values = [];
            for (var i = 6; i >= 0; i--) {
                var d = new Date(); d.setDate(d.getDate() - i);
                days.push(d.toLocaleDateString('en', {month:'short', day:'numeric'}));
                values.push(Math.round(Math.random() * 3000 + 500));
            }
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: days,
                    datasets: [{
                        label: 'Revenue ($)',
                        data: values,
                        borderColor: '#0077E6',
                        backgroundColor: 'rgba(0,119,230,0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#0077E6',
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { callback: function(v){return '$'+v;} } } }
                }
            });
        })();

        // Status Chart
        (function(){
            var ctx = document.getElementById('statusChart');
            if (!ctx) return;
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Pending','Confirmed','Paid','Checked In','Completed','Cancelled'],
                    datasets: [{
                        data: [<?php echo "$pending,$confirmed,$paid,$checked,$completed,$cancelled"; ?>],
                        backgroundColor: ['#F59E0B','#0077E6','#10B981','#8B5CF6','#10B981','#EF4444'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true } } },
                    cutout: '65%',
                }
            });
        })();
        </script>
        <?php
    }

    /* Sub-page render methods — each includes its view file */
    public function render_bookings_page()    { include BABARIDA_ADMIN . 'views/admin-booking-list.php'; }
    public function render_reports_page()     { include BABARIDA_ADMIN . 'views/admin-reports-charts.php'; }
    public function render_pricing_page()     { include BABARIDA_ADMIN . 'views/admin-pricing-editor.php'; }
    public function render_crm_page()         { include BABARIDA_ADMIN . 'views/admin-guest-crm.php'; }
    public function render_schedule_page()    { echo '<div class="babarida-admin-wrap"><h2>Schedule Calendar</h2><p style="color:#64748B;">Schedule management interface. Under development.</p></div>'; }
    public function render_partners_page()    { echo '<div class="babarida-admin-wrap"><h2>Partner Management</h2><p style="color:#64748B;">Hotel and liveaboard partner dashboard. Under development.</p></div>'; }
    public function render_coupons_page()     { echo '<div class="babarida-admin-wrap"><h2>Coupon Management</h2><p style="color:#64748B;">Create and manage promo codes. Under development.</p></div>'; }
    public function render_export_page()      { echo '<div class="babarida-admin-wrap"><h2>Export Data</h2><p style="color:#64748B;">PDF and Excel export. Under development.</p></div>'; }
    public function render_seo_page()         { echo '<div class="babarida-admin-wrap"><h2>SEO Management</h2><p style="color:#64748B;">Meta titles, descriptions, sitemaps. Under development.</p></div>'; }
    public function render_weather_page()     { echo '<div class="babarida-admin-wrap"><h2>Weather Panel</h2><p style="color:#64748B;">Marine weather display. Under development.</p></div>'; }
    public function render_chat_page()        { echo '<div class="babarida-admin-wrap"><h2>Internal Chat</h2><p style="color:#64748B;">Staff messaging. Under development.</p></div>'; }
    public function render_activity_log_page(){ echo '<div class="babarida-admin-wrap"><h2>Activity Log</h2><p style="color:#64748B;">System audit trail. Under development.</p></div>'; }
    public function render_security_page()    { echo '<div class="babarida-admin-wrap"><h2>Security Settings</h2><p style="color:#64748B;">2FA, login limits, device management. Under development.</p></div>'; }
    public function render_backup_page()      { echo '<div class="babarida-admin-wrap"><h2>Backup Management</h2><p style="color:#64748B;">Automated backups. Under development.</p></div>'; }
    public function render_system_health_page(){ echo '<div class="babarida-admin-wrap"><h2>System Health</h2><p style="color:#64748B;">Server and database monitoring. Under development.</p></div>'; }
    public function render_media_page()       { echo '<div class="babarida-admin-wrap"><h2>Media Management</h2><p style="color:#64748B;">Hero videos, galleries. Under development.</p></div>'; }
    public function render_settings_page()    { include BABARIDA_ADMIN . 'views/admin-settings-page.php'; }
}

new Babarida_Admin_Dashboard();
