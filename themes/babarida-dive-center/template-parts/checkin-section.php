<?php
/**
 * Check-In Section Template Part
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;
?>

<section class="section" id="checkin" style="background:var(--blue-ice);padding:60px 24px;" aria-label="Check In">
    <div class="section-inner" style="max-width:600px;text-align:center;">
        <div class="section-label reveal" style="justify-content:center;">Quick Access</div>
        <h2 class="section-title reveal reveal-delay-1" style="font-size:1.8rem;">Guest Check-In</h2>
        <p class="section-subtitle centered reveal reveal-delay-2" style="margin-bottom:32px;">Already have a booking? Enter your details below for fast check-in, or contact us for assistance.</p>
        <form id="checkinForm" class="reveal reveal-delay-3" novalidate style="display:flex;flex-direction:column;gap:14px;max-width:420px;margin:0 auto;">
            <input type="text" class="form-input" placeholder="Booking Reference / Name" aria-label="Booking reference" required>
            <input type="email" class="form-input" placeholder="Email Address" aria-label="Email" required>
            <button type="submit" class="btn btn-accent">Check In <i class="fa-solid fa-arrow-right"></i></button>
        </form>
    </div>
</section>
