<?php
/**
 * SEO Management
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

class Babarida_Seo {

    /**
     * Get meta description for current page
     */
    public static function get_meta_description() {
        // Check for custom SEO meta
        $custom = get_post_meta(get_the_ID(), '_seo_description', true);
        if ($custom) return $custom;

        if (is_front_page()) {
            return 'Babarida Dive Center – Premium diving, snorkeling, liveaboard, and water sports in Bunaken, Siladen, Bangka, and Lembeh, North Sulawesi, Indonesia.';
        }
        if (is_singular()) {
            return wp_trim_words(get_the_excerpt(), 30);
        }
        if (is_post_type_archive()) {
            $pt = get_post_type();
            $labels = array(
                'trip'         => 'Browse all dive trips and packages at Babarida Dive Center.',
                'liveaboard'   => 'Explore luxury liveaboard vessels for your North Sulawesi diving adventure.',
                'hotel'        => 'Recommended hotels and resorts near Bunaken, Siladen, Bangka, and Lembeh.',
                'water_sport'  => 'Water sports activities in North Sulawesi – jet ski, parasailing, kayaking and more.',
                'dive_course'  => 'SSI dive courses from Open Water to Divemaster at Babarida Dive Center.',
            );
            return $labels[$pt] ?? get_bloginfo('description');
        }
        return get_bloginfo('description');
    }

    /**
     * Get meta keywords
     */
    public static function get_meta_keywords() {
        $custom = get_post_meta(get_the_ID(), '_seo_keywords', true);
        if ($custom) return $custom;

        if (is_front_page()) {
            return 'Bunaken diving, Siladen snorkeling, Lembeh muck diving, Bangka liveaboard, North Sulawesi dive center, Indonesia diving, SSI courses, water sports Manado';
        }
        return '';
    }

    /**
     * Get robots meta
     */
    public static function get_robots_meta() {
        if (is_paged()) return 'noindex, follow';
        if (is_search()) return 'noindex, nofollow';
        if (is_404()) return 'noindex, nofollow';
        return 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
    }

    /**
     * Get canonical URL
     */
    public static function get_canonical_url() {
        if (is_singular()) {
            return wp_get_canonical_url();
        }
        if (is_front_page()) {
            return home_url('/');
        }
        if (is_post_type_archive()) {
            return get_post_type_archive_link(get_post_type());
        }
        return home_url($_SERVER['REQUEST_URI'] ?? '/');
    }

    /**
     * Get OG title
     */
    public static function get_og_title() {
        $custom = get_post_meta(get_the_ID(), '_seo_og_title', true);
        if ($custom) return $custom;
        if (is_front_page()) return 'Babarida Dive Center – Premium Diving in North Sulawesi';
        return get_the_title() . ' | ' . get_bloginfo('name');
    }

    /**
     * Get OG type
     */
    public static function get_og_type() {
        if (is_singular()) return 'article';
        return 'website';
    }

    /**
     * Get OG image
     */
    public static function get_og_image() {
        $custom = get_post_meta(get_the_ID(), '_seo_og_image', true);
        if ($custom) return wp_get_attachment_url($custom);

        if (is_singular() && has_post_thumbnail()) {
            $img_id = get_post_thumbnail_id();
            $src    = wp_get_attachment_image_src($img_id, 'hero-large');
            return $src ? $src[0] : '';
        }

        $default = get_option('babarida_default_og_image', '');
        return $default ? wp_get_attachment_url($default) : '';
    }

    /**
     * Get Organization Schema
     */
    public static function get_organization_schema() {
        return array(
            '@context' => 'https://schema.org',
            '@type'    => array('LocalBusiness', 'DiveCenter'),
            'name'     => get_bloginfo('name'),
            'url'      => home_url('/'),
            'logo'     => BABARIDA_URI . '/assets/img/logo.svg',
            'image'    => BABARIDA_URI . '/assets/img/hero-fallback.jpg',
            'description' => self::get_meta_description(),
            'telephone'=> '+62895801960359',
            'email'    => 'info@babaridadive.com',
            'address'  => array(
                '@type'           => 'PostalAddress',
                'streetAddress'   => 'Bunaken National Marine Park Area',
                'addressLocality' => 'Manado',
                'addressRegion'   => 'North Sulawesi',
                'postalCode'      => '95122',
                'addressCountry'  => 'ID',
            ),
            'geo' => array(
                '@type'     => 'GeoCoordinates',
                'latitude'  => '1.6231',
                'longitude' => '124.7636',
            ),
            'openingHoursSpecification' => array(
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'),
                'opens'  => '07:00',
                'closes' => '21:00',
            ),
            'priceRange' => '$$-$$$',
        );
    }

    /**
     * Get Breadcrumb Schema
     */
    public static function get_breadcrumb_schema() {
        $items = array(array(
            '@type' => 'ListItem',
            'position' => 1,
            'name'  => 'Home',
            'item'  => home_url('/'),
        ));

        if (is_singular()) {
            $items[] = array(
                '@type' => 'ListItem',
                'position' => 2,
                'name'  => get_the_title(),
                'item'  => get_permalink(),
            );
        } elseif (is_post_type_archive()) {
            $pt = get_post_type_object(get_post_type());
            if ($pt) {
                $items[] = array(
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name'  => $pt->labels->name,
                    'item'  => get_post_type_archive_link(get_post_type()),
                );
            }
        } elseif (is_tax()) {
            $term = get_queried_object();
            if ($term) {
                $items[] = array(
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name'  => $term->name,
                    'item'  => get_term_link($term),
                );
            }
        }

        return array(
            '@context' => 'https://schema.org',
            '@type'    => 'BreadcrumbList',
            'itemListElement' => $items,
        );
    }

    /**
     * Get FAQ Schema
     */
    public static function get_faq_schema() {
        $faqs = get_posts(array(
            'post_type'   => 'faq',
            'numberposts' => -1,
            'post_status' => 'publish',
            'orderby'     => 'menu_order',
            'order'       => 'ASC',
        ));

        if (empty($faqs)) return array();

        $entities = array();
        foreach ($faqs as $faq) {
            $entities[] = array(
                '@type'          => 'Question',
                'name'           => get_the_title($faq->ID),
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text'  => wp_strip_all_tags($faq->post_content),
                ),
            );
        }

        return array(
            '@context'       => 'https://schema.org',
            '@type'          => 'FAQPage',
            'mainEntity'     => $entities,
        );
    }
}

// Helper functions used in header
function babarida_get_meta_description() { return Babarida_Seo::get_meta_description(); }
function babarida_get_meta_keywords()     { return Babarida_Seo::get_meta_keywords(); }
function babarida_get_robots_meta()       { return Babarida_Seo::get_robots_meta(); }
function babarida_get_canonical_url()     { return Babarida_Seo::get_canonical_url(); }
function babarida_get_og_title()          { return Babarida_Seo::get_og_title(); }
function babarida_get_og_type()           { return Babarida_Seo::get_og_type(); }
function babarida_get_og_image()          { return Babarida_Seo::get_og_image(); }
function babarida_get_organization_schema() { return Babarida_Seo::get_organization_schema(); }
function babarida_get_breadcrumb_schema()  { return Babarida_Seo::get_breadcrumb_schema(); }
function babarida_get_faq_schema()         { return Babarida_Seo::get_faq_schema(); }
