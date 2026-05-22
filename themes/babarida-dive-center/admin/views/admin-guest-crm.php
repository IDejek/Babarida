<?php
/**
 * Admin CRM — Customer Profiles
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

 $page = isset($_GET['crm_page']) ? max(1, absint($_GET['crm_page'])) : 1;
 $per_page = 20;
 $customers = Babarida_CRM::get_all($per_page, $page);
 $total = Babarida_CRM::count();
 $total_pages = ceil($total / $per_page);

 $level_colors = array('bronze'=>'#CD7F32','silver'=>'#94A3B8','gold'=>'#F59E0B','platinum'=>'#8B5CF6');
?>

<div class="babarida-admin-wrap">
    <div class="babarida-panel-header">
        <h2>Customers (CRM)</h2>
        <span style="color:#64748B;font-size:0.85rem;"><?php echo esc_html($total); ?> total customers</span>
    </div>

    <div class="babarida-panel">
        <div class="babarida-table-wrap">
            <table class="babarida-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Nationality</th>
                        <th>Certification</th>
                        <th>Loyalty</th>
                        <th>Points</th>
                        <th>Total Spent</th>
                        <th>Trips</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $c) :
                        $lc = $level_colors[$c['loyalty_level']] ?? '#64748B';
                    ?>
                    <tr>
                        <td><strong><?php echo esc_html($c['first_name'] . ' ' . $c['last_name']); ?></strong></td>
                        <td style="font-size:0.8rem;color:#64748B;"><?php echo esc_html($c['email']); ?></td>
                        <td style="font-size:0.82rem;"><?php echo esc_html($c['phone']); ?></td>
                        <td><?php echo esc_html($c['nationality'] ?: '—'); ?></td>
                        <td style="font-size:0.82rem;"><?php echo esc_html($c['certification'] ?: '—'); ?></td>
                        <td>
                            <span style="display:inline-block;padding:2px 10px;border-radius:20px;font-size:0.68rem;font-weight:700;color:#fff;background:<?php echo esc_attr($lc); ?>;text-transform:capitalize;">
                                <?php echo esc_html($c['loyalty_level']); ?>
                            </span>
                        </td>
                        <td style="font-weight:600;"><?php echo esc_html(number_format($c['loyalty_points'])); ?></td>
                        <td style="font-weight:600;">$<?php echo esc_html(number_format($c['total_spent'], 0)); ?></td>
                        <td style="text-align:center;"><?php echo esc_html($c['total_trips']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($customers)) : ?>
                    <tr><td colspan="9" style="text-align:center;color:#94A3B8;padding:48px;">No customers found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($total_pages > 1) : ?>
    <div style="display:flex;justify-content:center;gap:4px;margin-top:16px;">
        <?php for ($p = 1; $p <= $total_pages; $p++) : ?>
        <a href="<?php echo esc_url(add_query_arg(array('page'=>'babarida-crm','crm_page'=>$p), admin_url('admin.php'))); ?>"
           class="button <?php echo $p === $page ? 'button-primary' : ''; ?>" style="padding:4px 12px;font-size:0.82rem;">
            <?php echo esc_html($p); ?>
        </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>
