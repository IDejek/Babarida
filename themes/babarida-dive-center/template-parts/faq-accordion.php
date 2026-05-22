<?php
/**
 * FAQ Accordion Template Part
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

 $faqs = get_posts(array(
    'post_type'   => 'faq',
    'numberposts' => 10,
    'post_status' => 'publish',
    'orderby'     => 'menu_order',
    'order'       => 'ASC',
));

// Fallback FAQs
if (empty($faqs)) {
    $faqs = array();
    $fallback_faqs = array(
        array('q' => 'What diving destinations does Babarida Dive Center cover?', 'a' => 'We operate across four premier destinations: Bunaken National Marine Park, Siladen Island, Bangka Archipelago, and the world-famous Lembeh Strait — all located in North Sulawesi, Indonesia.'),
        array('q' => 'Do I need diving certification to join?', 'a' => 'For diving activities, a valid certification is required. However, we offer SSI Open Water courses for beginners, and snorkeling and water sports activities are available for everyone regardless of certification level.'),
        array('q' => 'What SSI courses do you offer?', 'a' => 'We offer the complete range of SSI programs: Try Scuba, Open Water Diver, Advanced Adventurer, Stress & Rescue, Dive Guide, Divemaster, and various specialty courses including Deep Diving, Night Diving, Nitrox, and Underwater Photography.'),
        array('q' => 'How do I get to Manado?', 'a' => 'Sam Ratulangi International Airport (MDC) in Manado has direct flights from Jakarta, Bali, Singapore, Kuala Lumpur, and Davao. We can arrange airport pickup and transfer to your hotel or directly to the harbor.'),
        array('q' => 'What is the best time to dive in North Sulawesi?', 'a' => 'Diving is excellent year-round. March to October offers the calmest seas and best visibility (20-40m). November to February can bring some rain and slightly reduced visibility, but this is peak season for rare critters in Lembeh.'),
        array('q' => 'Can I book a private liveaboard charter?', 'a' => 'Absolutely. All our vessels are available for private charter for groups, families, or special occasions. We can customize the itinerary, menu, and dive schedule to your preferences.'),
        array('q' => 'What safety measures do you have in place?', 'a' => 'Safety is our top priority. All boats carry emergency oxygen, first aid kits, and communication equipment. Our dive guides are SSI certified professionals with years of local experience. We maintain small guide-to-guest ratios.'),
    );
    foreach ($fallback_faqs as $f) {
        $faqs[] = (object) array('post_title' => $f['q'], 'post_content' => $f['a']);
    }
}
?>

<section class="section faq" id="faq" aria-label="Frequently Asked Questions">
    <div class="section-inner">
        <div class="faq-header">
            <div class="section-label reveal" style="justify-content:center;">Help Center</div>
            <h2 class="section-title reveal reveal-delay-1" style="text-align:center;">Frequently Asked Questions</h2>
        </div>
        <div class="faq-list reveal reveal-delay-2">
            <?php foreach ($faqs as $faq) : ?>
            <div class="faq-item">
                <button class="faq-question" aria-expanded="false">
                    <?php echo esc_html($faq->post_title); ?>
                    <span class="faq-icon"><i class="fa-solid fa-chevron-down"></i></span>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-inner"><?php echo esc_html($faq->post_content); ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
