<?php
/**
 * Pricing Table Template Part
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;
?>

<section class="section pricing" id="pricing" aria-label="Monthly Pricing">
    <div class="section-inner">
        <div class="pricing-header">
            <div class="section-label reveal" style="justify-content:center;">Transparent Pricing</div>
            <h2 class="section-title reveal reveal-delay-1" style="text-align:center;">Monthly Price List</h2>
            <p class="section-subtitle centered reveal reveal-delay-2">Plan ahead with our 24-month pricing schedule. Seasonal rates reflect demand and conditions.</p>
        </div>
        <div class="pricing-controls reveal reveal-delay-2">
            <button class="pricing-filter-btn active" data-filter="all">All Packages</button>
            <button class="pricing-filter-btn" data-filter="day-trip">Day Trips</button>
            <button class="pricing-filter-btn" data-filter="liveaboard">Liveaboards</button>
            <button class="pricing-filter-btn" data-filter="course">SSI Courses</button>
        </div>
        <div class="pricing-table-wrap reveal reveal-delay-3">
            <table class="pricing-table" role="table">
                <thead>
                    <tr>
                        <th scope="col">Month</th>
                        <th scope="col">Season</th>
                        <th scope="col">Day Trip (2 Dives)</th>
                        <th scope="col">Liveaboard (3N)</th>
                        <th scope="col">SSI Open Water</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody id="pricingTableBody">
                    <!-- Populated by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</section>
