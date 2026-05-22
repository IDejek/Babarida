<?php
/**
 * AI Chat Assistant + Internal Admin Chat
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

class Babarida_Chat {

    /**
     * Generate chat reply based on message content
     */
    public static function generate_reply($message) {
        $lower = strtolower($message);

        $responses = array(
            array(
                'keywords' => array('bunaken'),
                'reply' => 'Bunaken National Marine Park is famous for its spectacular vertical walls dropping to over 200 meters, with visibility often exceeding 30 meters. You\'ll encounter reef sharks, sea turtles, massive schools of barracuda, and over 390 coral species. We offer day trips, dive & stay packages, and liveaboard routes through Bunaken. Would you like to see our Bunaken packages?',
            ),
            array(
                'keywords' => array('liveaboard'),
                'reply' => 'We have three liveaboard vessels: Babarida Phinisi I (our flagship, 8 cabins, from $2,400/person for 3 nights), Babarida Explorer (6 cabins, Lembeh specialist, from $1,600/person), and Babarida Catamaran (4 cabins, Bunaken-Siladen cruise, from $980/person). All include meals, dives, and equipment. Would you like to book one?',
            ),
            array(
                'keywords' => array('price', 'cost', 'how much', 'rate', 'fee'),
                'reply' => 'Our day trips start from $65/person (2 dives) in low season. Liveaboards range from $980 to $2,400 per person depending on the vessel and season. SSI Open Water courses start at $380. Check our Monthly Price List section for the full 24-month schedule with seasonal rates!',
            ),
            array(
                'keywords' => array('ssi', 'course', 'certif', 'learn'),
                'reply' => 'We offer the full SSI curriculum: Try Scuba ($50), Open Water Diver ($380-$480), Advanced Adventurer ($300), Stress & Rescue ($350), Dive Guide ($500+), and Divemaster programs. We also have specialty courses in Deep Diving, Night & Limited Visibility, Nitrox, and Underwater Photography. All courses include certification fees and equipment.',
            ),
            array(
                'keywords' => array('book', 'reserve', 'reservation'),
                'reply' => 'Great choice! You can fill out our booking inquiry form by clicking the "Book Now" button, or contact us directly on WhatsApp at +62 895 8019 60359 for instant assistance. We typically confirm availability within a few hours!',
            ),
            array(
                'keywords' => array('lembeh'),
                'reply' => 'Lembeh Strait is the world\'s muck diving capital! Famous for rare critters like the hairy frogfish, mimic octopus, blue-ringed octopus, rhinopias, and countless nudibranch species. Best for macro photography enthusiasts. We have dedicated Lembeh liveaboard trips and day trip packages.',
            ),
            array(
                'keywords' => array('siladen'),
                'reply' => 'Siladen Island offers some of the best snorkeling and shallow diving in the area. Crystal-clear waters, pristine coral gardens, and a beautiful white sand beach make it perfect for families and non-divers too. Our Siladen day trips include 2-3 dives or a full snorkeling program.',
            ),
            array(
                'keywords' => array('bangka'),
                'reply' => 'Bangka Island features dramatic underwater landscapes with soft coral gardens, pinnacles, underwater caves, and excellent macro life. It\'s less visited than Bunaken, offering a more exclusive experience. Our Bangka liveaboard route is one of our most popular multi-day trips.',
            ),
            array(
                'keywords' => array('weather', 'condition', 'visibility', 'wind', 'water temp'),
                'reply' => 'North Sulawesi enjoys warm tropical weather year-round (28-32°C). Water temperature is 27-30°C. Best visibility (20-40m) is March-October. Check our weather widget on the left for current conditions! Even during rainy season (Nov-Feb), diving in Lembeh remains excellent.',
            ),
            array(
                'keywords' => array('hotel', 'stay', 'accommodat', 'resort', 'room'),
                'reply' => 'We partner with hotels ranging from $55/night eco-lodges on Bunaken to $180/night boutique resorts on Siladen, and $120/night luxury resorts in Manado. All partner hotels offer dive package deals. Would you like me to help you find the right accommodation?',
            ),
            array(
                'keywords' => array('snorkel'),
                'reply' => 'Snorkeling is available at all four destinations! Siladen and Bunaken offer the clearest waters with vibrant shallow reefs. Our snorkeling day trips include equipment, guide, boat transfer, and lunch starting from $40/person. Perfect for non-divers in your group!',
            ),
            array(
                'keywords' => array('water sport', 'jet ski', 'parasail', 'kayak', 'paddle'),
                'reply' => 'We offer exciting water sports including jet skiing, banana boat rides, parasailing, kayaking, and stand-up paddleboarding. Water sports are available at Bunaken and Siladen. Packages start from $25 per activity. Great for adding some adrenaline to your dive trip!',
            ),
            array(
                'keywords' => array('transfer', 'airport', 'pick up', 'transport', 'how to get'),
                'reply' => 'Sam Ratulangi International Airport (MDC) in Manado has direct flights from Jakarta, Bali, Singapore, Kuala Lumpur, and Davao. We can arrange airport pickup and transfer to your hotel or directly to the harbor. Transfer from airport to Bunaken harbor takes about 45 minutes.',
            ),
            array(
                'keywords' => array('when', 'best time', 'season', 'month'),
                'reply' => 'Diving is excellent year-round in North Sulawesi! March to October offers the calmest seas and best visibility (20-40m). June-August is peak season with the highest prices. November to February can bring some rain and slightly reduced visibility, but this is peak season for rare critters in Lembeh. We recommend booking 2-3 months in advance for peak season.',
            ),
            array(
                'keywords' => array('group', 'family', 'corporate', 'team'),
                'reply' => 'We love hosting groups! Groups of 4+ get 5% discount, 6+ get 10%, and 10+ get 15% off. We can customize itineraries for families, corporate team building, dive clubs, and photography groups. Contact us on WhatsApp for a tailored group quote!',
            ),
            array(
                'keywords' => array('safety', 'safe', 'insurance', 'emergency'),
                'reply' => 'Safety is our top priority. All boats carry emergency oxygen, first aid kits, and communication equipment. Our dive guides are SSI certified professionals with years of local experience. We maintain small guide-to-guest ratios and conduct thorough briefings. We recommend dive insurance — we can help arrange coverage through our insurance partners.',
            ),
            array(
                'keywords' => array('equipment', 'gear', 'rental', 'bcd', 'regulator'),
                'reply' => 'We provide full diving equipment rental included in our day trip and liveaboard packages: BCD, regulator, wetsuit (3mm), mask, fins, dive computer, and weight belt. All equipment is well-maintained and regularly serviced. You\'re welcome to bring your own gear — we offer a $5 discount per dive if you use your own complete set.',
            ),
            array(
                'keywords' => array('night dive', 'night'),
                'reply' => 'Night dives are available at Bunaken and Siladen! Experience the reef transform after dark — watch sleeping parrotfish, hunting moray eels, bioluminescent plankton, and nocturnal critters. Night dive add-on is $15/person. Available on request for certified divers with night diving experience.',
            ),
            array(
                'keywords' => array('photo', 'video', 'underwater', 'camera', 'gopro'),
                'reply' => 'We offer underwater photography services! Our guides can shoot photos and short videos during your dives using professional equipment. Photo package: $30/dive including edited photos delivered via Google Drive. We also rent GoPros for $20/day. For serious photographers, our Lembeh trips are perfect for macro photography.',
            ),
            array(
                'keywords' => array('hello', 'hi', 'hey', 'good morning', 'good afternoon', 'good evening'),
                'reply' => 'Hello and welcome to Babarida Dive Center! How can I help you plan your diving adventure in North Sulawesi today?',
            ),
            array(
                'keywords' => array('thank', 'thanks', 'great', 'awesome', 'perfect'),
                'reply' => 'You\'re welcome! If you have any more questions about diving in Bunaken, Siladen, Bangka, or Lembeh, don\'t hesitate to ask. We\'re here to help make your trip unforgettable! 🌊',
            ),
        );

        // Find best matching response
        $best_match = null;
        $best_score = 0;

        foreach ($responses as $r) {
            $score = 0;
            foreach ($r['keywords'] as $kw) {
                if (strpos($lower, $kw) !== false) {
                    $score += strlen($kw); // Longer keyword matches score higher
                }
            }
            if ($score > $best_score) {
                $best_score = $score;
                $best_match = $r['reply'];
            }
        }

        if ($best_match) {
            return $best_match;
        }

        return 'Thanks for your question! For detailed information, I\'d recommend speaking with our team directly on WhatsApp (+62 895 8019 60359) or email (info@babaridadive.com). You can also browse our destination sections, pricing table, or FAQ for more details. Is there anything specific about diving in North Sulawesi I can help with?';
    }

    /**
     * Internal admin chat — save message
     */
    public static function save_message($from_user_id, $to_user_id, $message, $booking_id = 0) {
        return wp_insert_post(array(
            'post_title'  => 'Chat: ' . $from_user_id . ' → ' . $to_user_id,
            'post_type'   => 'internal_chat',
            'post_status' => 'publish',
            'meta_input'  => array(
                '_chat_from'     => absint($from_user_id),
                '_chat_to'       => absint($to_user_id),
                '_chat_message'  => sanitize_textarea_field($message),
                '_chat_booking'  => absint($booking_id),
                '_chat_timestamp'=> current_time('mysql'),
                '_chat_read'     => 'no',
            ),
        ));
    }

    /**
     * Get chat messages between two users
     */
    public static function get_messages($user_a, $user_b, $booking_id = 0) {
        $args = array(
            'post_type'   => 'internal_chat',
            'numberposts' => 100,
            'orderby'     => 'date',
            'order'       => 'ASC',
            'meta_query'  => array(
                'relation' => 'AND',
                array(
                    'relation' => 'OR',
                    array('key' => '_chat_from', 'value' => $user_a),
                    array('key' => '_chat_to', 'value' => $user_a),
                ),
                array(
                    'relation' => 'OR',
                    array('key' => '_chat_from', 'value' => $user_b),
                    array('key' => '_chat_to', 'value' => $user_b),
                ),
            ),
        );

        if ($booking_id) {
            $args['meta_query'][] = array('key' => '_chat_booking', 'value' => absint($booking_id));
            $args['meta_query']['relation'] = 'AND';
        }

        $posts = get_posts($args);
        $msgs  = array();
        foreach ($posts as $p) {
            $msgs[] = array(
                'id'        => $p->ID,
                'from'      => get_post_meta($p->ID, '_chat_from', true),
                'to'        => get_post_meta($p->ID, '_chat_to', true),
                'message'   => get_post_meta($p->ID, '_chat_message', true),
                'timestamp' => get_post_meta($p->ID, '_chat_timestamp', true),
                'read'      => get_post_meta($p->ID, '_chat_read', true),
            );
        }
        return $msgs;
    }

    /**
     * Mark messages as read
     */
    public static function mark_read($from_user_id, $to_user_id) {
        $msgs = get_posts(array(
            'post_type'   => 'internal_chat',
            'numberposts' => -1,
            'fields'      => 'ids',
            'meta_query'  => array(
                'relation' => 'AND',
                array('key' => '_chat_from', 'value' => $from_user_id),
                array('key' => '_chat_to', 'value' => $to_user_id),
                array('key' => '_chat_read', 'value' => 'no'),
            ),
        ));
        foreach ($msgs as $mid) {
            update_post_meta($mid, '_chat_read', 'yes');
        }
    }
}
