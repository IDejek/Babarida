<?php
/**
 * Admin Reports & Analytics
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

 $period = isset($_GET['period']) ? sanitize_text_field($_GET['period']) : 'monthly';

// Revenue data
 $revenue_data = array();
if ($period === 'daily') {
    for ($i = 29; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-{$i} days"));
        $revenue_data[] = array('label' => date('M j', strtotime($d)), 'value' => Babarida_Booking::get_revenue($d, $d));
    }
} elseif ($period === 'weekly') {
    for ($i = 11; $i >= 0; $i--) {
        $start = date('Y-m-d', strtotime("monday this week -{$i} weeks"));
        $end   = date('Y-m-d', strtotime("sunday this week -{$i} weeks"));
        $revenue_data[] = array('label' => date('M j', strtotime($start)), 'value' => Babarida_Booking::get_revenue($start, $end));
    }
} else {
    for ($i = 11; $i >= 0; $i--) {
        $m = date('Y-m-01', strtotime("-{$i} months"));
        $e = date('Y-m-t', strtotime($m));
        $revenue_data[] = array('label' => date('M Y', strtotime($m)), 'value' => Babarida_Booking::get_revenue($m, $e));
    }
}

// Destination stats
 $dest_stats = array();
 $dests = get_terms(array('taxonomy' => 'destination', 'hide_empty' => true));
if (!is_wp_error($dests)) {
    foreach ($dests as $d) {
        $count = get_posts(array('post_type'=>'booking','numberposts'=>-1,'fields'=>'ids','meta_key'=>'_booking_destination','meta_value'=>$d->slug));
        $dest_stats[] = array('name' => $d->name, 'count' => count($count));
    }
}

// Top destinations by revenue
 $all_bookings = Babarida_Booking::get_all(array('per_page' => 200));
 $dest_revenue = array();
foreach ($all_bookings as $b) {
    $d = ucfirst($b['destination']);
    if (!isset($dest_revenue[$d])) $dest_revenue[$d] = 0;
    $dest_revenue[$d] += floatval($b['total']);
}
arsort($dest_revenue);

 $total_revenue = array_sum(array_column($revenue_data, 'value'));
 $avg_booking  = count($all_bookings) > 0 ? $total_revenue / count($all_bookings) : 0;
?>

<div class="babarida-admin-wrap">
    <div class="babarida-panel-header">
        <h2>Reports & Analytics</h2>
        <div style="display:flex;gap:4px;">
            <?php foreach (array('daily','weekly','monthly') as $p) : ?>
            <a href="<?php echo esc_url(add_query_arg(array('page'=>'babarida-reports','period'=>$p), admin_url('admin.php'))); ?>"
               class="button <?php echo $period === $p ? 'button-primary' : ''; ?>" style="padding:4px 14px;font-size:0.78rem;text-transform:capitalize;">
                <?php echo esc_html($p); ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="babarida-stats-grid" style="margin-bottom:24px;">
        <div class="babarida-stat-card" style="--accent:#0077E6;">
            <div class="babarida-stat-icon"><i class="fa-solid fa-chart-line"></i></div>
            <div class="babarida-stat-info">
                <span class="babarida-stat-value">$<?php echo esc_html(number_format($total_revenue, 0)); ?></span>
                <span class="babarida-stat-label">Total Revenue</span>
            </div>
        </div>
        <div class="babarida-stat-card" style="--accent:#10B981;">
            <div class="babarida-stat-icon"><i class="fa-solid fa-receipt"></i></div>
            <div class="babarida-stat-info">
                <span class="babarida-stat-value"><?php echo esc_html(count($all_bookings)); ?></span>
                <span class="babarida-stat-label">Total Bookings</span>
            </div>
        </div>
        <div class="babarida-stat-card" style="--accent:#F59E0B;">
            <div class="babarida-stat-icon"><i class="fa-solid fa-calculator"></i></div>
            <div class="babarida-stat-info">
                <span class="babarida-stat-value">$<?php echo esc_html(number_format($avg_booking, 0)); ?></span>
                <span class="babarida-stat-label">Avg. Booking Value</span>
            </div>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="babarida-panel" style="margin-bottom:24px;">
        <h3 style="margin-bottom:16px;">Revenue Trend</h3>
        <canvas id="reportRevenueChart" height="300"></canvas>
    </div>

    <div class="babarida-charts-grid">
        <!-- Destination Popularity -->
        <div class="babarida-chart-card">
            <h3>Destination Popularity</h3>
            <canvas id="destPopChart" height="250"></canvas>
        </div>
        <!-- Revenue by Destination -->
        <div class="babarida-chart-card">
            <h3>Revenue by Destination</h3>
            <canvas id="destRevChart" height="250"></canvas>
        </div>
    </div>
</div>

<script>
(function(){
    // Revenue Trend
    var ctx1 = document.getElementById('reportRevenueChart');
    if (ctx1) {
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: <?php echo wp_json_encode(array_column($revenue_data, 'label')); ?>,
                datasets: [{
                    label: 'Revenue ($)',
                    data: <?php echo wp_json_encode(array_column($revenue_data, 'value')); ?>,
                    backgroundColor: 'rgba(0,119,230,0.7)',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { callback: function(v){return '$'+v;} } } }
            }
        });
    }

    // Destination Popularity
    var ctx2 = document.getElementById('destPopChart');
    if (ctx2) {
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: <?php echo wp_json_encode(array_column($dest_stats, 'name')); ?>,
                datasets: [{
                    data: <?php echo wp_json_encode(array_column($dest_stats, 'count')); ?>,
                    backgroundColor: ['#0077E6','#FFB800','#10B981','#8B5CF6'],
                    borderWidth: 0,
                }]
            },
            options: { responsive: true, plugins: { legend: { position:'bottom', labels:{usePointStyle:true,padding:12} } }, cutout:'60%' }
        });
    }

    // Revenue by Destination
    var ctx3 = document.getElementById('destRevChart');
    if (ctx3 && <?php echo json_encode(!empty($dest_revenue)); ?>) {
        var names = <?php echo wp_json_encode(array_keys($dest_revenue)); ?>;
        var vals = <?php echo wp_json_encode(array_values($dest_revenue)); ?>;
        new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: names,
                datasets: [{
                    data: vals,
                    backgroundColor: ['#0077E6','#FFB800','#10B981','#8B5CF6','#EF4444','#F97316'],
                    borderRadius: 6,
                }]
            },
            options: { responsive: true, indexAxis: 'y', plugins:{legend:{display:false}}, scales:{x:{ticks:{callback:function(v){return '$'+v;}}}} }
        });
    }
})();
</script>
