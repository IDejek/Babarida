<?php
/**
 * Footer Template
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;
?>

<!-- Floating Contact Buttons -->
<div class="floating-btns" id="floatingBtns" aria-label="<?php esc_attr_e('Quick actions', 'babarida'); ?>">
    <button class="float-btn float-btn-top" id="backToTop" data-tooltip="<?php esc_attr_e('Back to top', 'babarida'); ?>" aria-label="<?php esc_attr_e('Back to top', 'babarida'); ?>">
        <i class="fa-solid fa-chevron-up"></i>
    </button>
    <a href="#bookingModal" class="float-btn float-btn-book" data-tooltip="<?php esc_attr_e('Book Now', 'babarida'); ?>" aria-label="<?php esc_attr_e('Book now', 'babarida'); ?>">
        <i class="fa-solid fa-calendar-check"></i>
    </a>
    <a href="mailto:info@babaridadive.com" class="float-btn float-btn-email" data-tooltip="<?php esc_attr_e('Email Us', 'babarida'); ?>" aria-label="<?php esc_attr_e('Send email', 'babarida'); ?>">
        <i class="fa-solid fa-envelope"></i>
    </a>
    <a href="https://wa.me/62895801960359" target="_blank" rel="noopener" class="float-btn float-btn-whatsapp" data-tooltip="WhatsApp" aria-label="<?php esc_attr_e('Chat on WhatsApp', 'babarida'); ?>">
        <i class="fa-brands fa-whatsapp"></i>
    </a>
</div>

<!-- Currency Switcher -->
<div class="currency-switcher" id="currencySwitcher">
    <button class="currency-btn" id="currencyBtn" aria-label="<?php esc_attr_e('Change currency', 'babarida'); ?>">
        <i class="fa-solid fa-coins"></i> <span id="currencyLabel">USD</span> <i class="fa-solid fa-chevron-down" style="font-size:0.55rem;"></i>
    </button>
</div>

<!-- Weather Widget -->
<div class="weather-widget" id="weatherWidget" role="complementary" aria-label="<?php esc_attr_e('Weather information', 'babarida'); ?>">
    <div class="weather-widget-header">
        <span><?php esc_html_e('Manado Weather', 'babarida'); ?></span>
        <button class="weather-widget-close" id="weatherClose" aria-label="<?php esc_attr_e('Close weather', 'babarida'); ?>"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="weather-main">
        <div class="weather-icon" id="weatherIcon">⛅</div>
        <div>
            <div class="weather-temp" id="weatherTemp">30°</div>
            <div class="weather-desc" id="weatherDesc"><?php esc_html_e('Partly Cloudy', 'babarida'); ?></div>
        </div>
    </div>
    <div class="weather-details">
        <div class="weather-detail"><i class="fa-solid fa-water"></i> <span id="waterTemp">29°C</span></div>
        <div class="weather-detail"><i class="fa-solid fa-eye"></i> <span id="visibility">25m</span></div>
        <div class="weather-detail"><i class="fa-solid fa-wind"></i> <span id="windSpeed">12 km/h</span></div>
        <div class="weather-detail"><i class="fa-solid fa-droplet"></i> <span id="humidity">78%</span></div>
    </div>
</div>

<!-- AI Chat Assistant -->
<button class="ai-chat-toggle" id="aiChatToggle" aria-label="<?php esc_attr_e('Open chat assistant', 'babarida'); ?>">
    <span class="chat-pulse"></span>
    <i class="fa-solid fa-comments"></i>
</button>
<div class="ai-chat-panel" id="aiChatPanel" role="dialog" aria-label="<?php esc_attr_e('Chat assistant', 'babarida'); ?>">
    <div class="ai-chat-header">
        <div class="ai-avatar"><i class="fa-solid fa-robot"></i></div>
        <div class="ai-info">
            <h5><?php esc_html_e('Babarida AI Assistant', 'babarida'); ?></h5>
            <span><?php esc_html_e('Online now', 'babarida'); ?></span>
        </div>
    </div>
    <div class="ai-chat-messages" id="chatMessages">
        <div class="chat-msg bot"><?php esc_html_e('Welcome to Babarida Dive Center! I can help you with destinations, pricing, booking, diving courses, and more. How can I assist you today?', 'babarida'); ?></div>
    </div>
    <div class="chat-quick-replies" id="quickReplies">
        <button class="quick-reply-btn" data-msg="<?php esc_attr_e('Tell me about Bunaken diving', 'babarida'); ?>"><?php esc_html_e('Bunaken Diving', 'babarida'); ?></button>
        <button class="quick-reply-btn" data-msg="<?php esc_attr_e('What liveaboards are available?', 'babarida'); ?>"><?php esc_html_e('Liveaboards', 'babarida'); ?></button>
        <button class="quick-reply-btn" data-msg="<?php esc_attr_e('How much does a day trip cost?', 'babarida'); ?>"><?php esc_html_e('Pricing', 'babarida'); ?></button>
        <button class="quick-reply-btn" data-msg="<?php esc_attr_e('Do you offer SSI courses?', 'babarida'); ?>"><?php esc_html_e('SSI Courses', 'babarida'); ?></button>
        <button class="quick-reply-btn" data-msg="<?php esc_attr_e('I want to book a trip', 'babarida'); ?>"><?php esc_html_e('Book Now', 'babarida'); ?></button>
    </div>
    <div class="ai-chat-input">
        <input type="text" id="chatInput" placeholder="<?php esc_attr_e('Type your question...', 'babarida'); ?>" aria-label="<?php esc_attr_e('Chat message', 'babarida'); ?>">
        <button id="chatSend" aria-label="<?php esc_attr_e('Send message', 'babarida'); ?>"><i class="fa-solid fa-paper-plane"></i></button>
    </div>
</div>

<!-- Booking Modal -->
<div class="modal-overlay" id="bookingModal" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Booking form', 'babarida'); ?>">
    <div class="modal">
        <div class="modal-header">
            <h3><?php esc_html_e('Book Your Dive Adventure', 'babarida'); ?></h3>
            <button class="modal-close" id="modalClose" aria-label="<?php esc_attr_e('Close booking form', 'babarida'); ?>"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <form id="bookingForm" novalidate>
                <?php wp_nonce_field('babarida_booking', 'booking_nonce'); ?>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="bookFirstName"><?php esc_html_e('First Name *', 'babarida'); ?></label>
                        <input type="text" class="form-input" id="bookFirstName" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="bookLastName"><?php esc_html_e('Last Name *', 'babarida'); ?></label>
                        <input type="text" class="form-input" id="bookLastName" name="last_name" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="bookEmail"><?php esc_html_e('Email *', 'babarida'); ?></label>
                        <input type="email" class="form-input" id="bookEmail" name="email" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="bookPhone"><?php esc_html_e('WhatsApp / Phone *', 'babarida'); ?></label>
                        <input type="tel" class="form-input" id="bookPhone" name="phone" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="bookDestination"><?php esc_html_e('Destination *', 'babarida'); ?></label>
                    <select class="form-select" id="bookDestination" name="destination" required>
                        <option value=""><?php esc_html_e('Select a destination', 'babarida'); ?></option>
                        <?php
                        $dests = get_terms(array('taxonomy' => 'destination', 'hide_empty' => false));
                        if (!is_wp_error($dests)) {
                            foreach ($dests as $d) {
                                echo '<option value="' . esc_attr($d->slug) . '">' . esc_html($d->name) . '</option>';
                            }
                        }
                        ?>
                        <option value="liveaboard"><?php esc_html_e('Liveaboard Cruise', 'babarida'); ?></option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="bookDate"><?php esc_html_e('Preferred Date *', 'babarida'); ?></label>
                        <input type="date" class="form-input" id="bookDate" name="date" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="bookGuests"><?php esc_html_e('Number of Guests', 'babarida'); ?></label>
                        <select class="form-select" id="bookGuests" name="guests">
                            <?php for ($g = 1; $g <= 20; $g++) : ?>
                            <option value="<?php echo $g; ?>" <?php echo $g === 2 ? 'selected' : ''; ?>><?php echo $g; ?> <?php echo $g === 1 ? esc_html__('Guest', 'babarida') : esc_html__('Guests', 'babarida'); ?></option>
                            <?php endfor; ?>
                            <option value="21"><?php esc_html_e('21+ Guests', 'babarida'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="bookActivity"><?php esc_html_e('Activity', 'babarida'); ?></label>
                    <select class="form-select" id="bookActivity" name="activity">
                        <option value=""><?php esc_html_e('Select activity', 'babarida'); ?></option>
                        <?php
                        $activities = get_terms(array('taxonomy' => 'activity', 'hide_empty' => false));
                        if (!is_wp_error($activities)) {
                            foreach ($activities as $a) {
                                echo '<option value="' . esc_attr($a->slug) . '">' . esc_html($a->name) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="bookNotes"><?php esc_html_e('Special Requests', 'babarida'); ?></label>
                    <textarea class="form-textarea" id="bookNotes" name="notes" placeholder="<?php esc_attr_e('Certification level, dietary requirements, special occasions...', 'babarida'); ?>"></textarea>
                </div>
                <p class="form-note"><?php esc_html_e("We'll confirm availability and send you a detailed quote within 24 hours via email and WhatsApp.", 'babarida'); ?></p>
                <button type="submit" class="btn btn-accent" style="width:100%; margin-top:8px;">
                    <?php esc_html_e('Send Booking Inquiry', 'babarida'); ?> <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer" aria-live="polite"></div>

<footer class="footer" role="contentinfo">
    <div class="footer-inner">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo" aria-label="<?php esc_attr_e('Babarida Dive Center', 'babarida'); ?>">
                    <div class="logo-mark"><i class="fa-solid fa-water"></i></div>
                    <div class="logo-text">
                        <span class="logo-name"><?php bloginfo('name'); ?></span>
                        <span class="logo-tagline"><?php esc_html_e('Bunaken &bull; Siladen &bull; Bangka &bull; Lembeh', 'babarida'); ?></span>
                    </div>
                </a>
                <p><?php esc_html_e('Premium diving experiences in North Sulawesi, Indonesia. Exploring the world\'s most biodiverse marine environments since 2009.', 'babarida'); ?></p>
                <div class="footer-social">
                    <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                    <a href="#" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="#" aria-label="TripAdvisor"><i class="fa-solid fa-star"></i></a>
                </div>
            </div>

            <div class="footer-col">
                <h5><?php esc_html_e('Destinations', 'babarida'); ?></h5>
                <ul>
                    <?php
                    $footer_dests = get_terms(array('taxonomy' => 'destination', 'hide_empty' => false));
                    if (!is_wp_error($footer_dests)) {
                        foreach ($footer_dests as $fd) {
                            echo '<li><a href="' . esc_url(get_term_link($fd)) . '">' . esc_html($fd->name) . '</a></li>';
                        }
                    }
                    ?>
                </ul>
            </div>

            <div class="footer-col">
                <h5><?php esc_html_e('Services', 'babarida'); ?></h5>
                <ul>
                    <li><a href="<?php echo esc_url(home_url('/liveaboards')); ?>"><?php esc_html_e('Liveaboards', 'babarida'); ?></a></li>
                    <li><a href="#"><?php esc_html_e('Day Trips', 'babarida'); ?></a></li>
                    <li><a href="#"><?php esc_html_e('Dive Safaris', 'babarida'); ?></a></li>
                    <li><a href="#"><?php esc_html_e('SSI Courses', 'babarida'); ?></a></li>
                    <li><a href="#"><?php esc_html_e('Snorkeling', 'babarida'); ?></a></li>
                    <li><a href="#"><?php esc_html_e('Water Sports', 'babarida'); ?></a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h5><?php esc_html_e('Company', 'babarida'); ?></h5>
                <ul>
                    <li><a href="<?php echo esc_url(home_url('/about')); ?>"><?php esc_html_e('About Us', 'babarida'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/blog')); ?>"><?php esc_html_e('Blog', 'babarida'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/faq')); ?>"><?php esc_html_e('FAQ', 'babarida'); ?></a></li>
                    <li><a href="#"><?php esc_html_e('Partners', 'babarida'); ?></a></li>
                    <li><a href="#"><?php esc_html_e('Careers', 'babarida'); ?></a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h5><?php esc_html_e('Contact', 'babarida'); ?></h5>
                <div class="footer-contact-item"><i class="fa-brands fa-whatsapp"></i><span>+62 895 8019 60359</span></div>
                <div class="footer-contact-item"><i class="fa-solid fa-envelope"></i><span><?php echo esc_html(get_option('admin_email', 'info@babaridadive.com')); ?></span></div>
                <div class="footer-contact-item"><i class="fa-solid fa-location-dot"></i><span><?php esc_html_e('Bunaken National Marine Park Area, Manado, North Sulawesi, Indonesia', 'babarida'); ?></span></div>
                <div class="footer-contact-item"><i class="fa-solid fa-clock"></i><span><?php esc_html_e('Open daily: 07:00 - 21:00 WITA', 'babarida'); ?></span></div>
            </div>
        </div>

        <div class="footer-bottom">
            <span>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php esc_html_e('All rights reserved.', 'babarida'); ?></span>
            <span>
                <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>"><?php esc_html_e('Privacy Policy', 'babarida'); ?></a> &middot;
                <a href="<?php echo esc_url(home_url('/terms')); ?>"><?php esc_html_e('Terms of Service', 'babarida'); ?></a> &middot;
                <a href="<?php echo esc_url(home_url('/cookies')); ?>"><?php esc_html_e('Cookie Policy', 'babarida'); ?></a>
            </span>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
