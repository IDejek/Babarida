<?php
/**
 * Custom Nav Walker for Mega Menu
 *
 * @package Babarida_Dive_Center
 */

defined('ABSPATH') || exit;

/**
 * Desktop Mega Menu Walker
 */
class Babarida_Nav_Walker extends Walker_Nav_Menu {

    private $in_mega = false;

    function start_lvl(&$output, $depth = 0, $args = null) {
        if ($depth === 0) {
            $output .= '<div class="mega-menu" role="menu"><div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">';
            $this->in_mega = true;
        } else {
            $output .= '';
        }
    }

    function end_lvl(&$output, $depth = 0, $args = null) {
        if ($depth === 0 && $this->in_mega) {
            $output .= '</div></div>';
            $this->in_mega = false;
        }
    }

    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));

        if ($depth === 0) {
            $active = in_array('current-menu-item', $classes, true) ? ' active' : '';
            $has_children = in_array('menu-item-has-children', $classes, true);
            $output .= '<li class="nav-item">';
            $output .= '<a href="' . esc_url($item->url) . '" class="nav-link' . $active . '"' . $this->link_attributes($item) . '>';
            $output .= esc_html($item->title);
            if ($has_children) {
                $output .= ' <i class="fa-solid fa-chevron-down"></i>';
            }
            $output .= '</a>';
        } elseif ($depth === 1 && $this->in_mega) {
            $icon = get_post_meta($item->ID, '_menu_item_icon', true);
            $desc = get_post_meta($item->ID, '_menu_item_desc', true);
            $icon = $icon ? $icon : 'fa-solid fa-circle-dot';
            $desc = $desc ? $desc : '';
            // Check if this should span full width (last item with "info" in class)
            $span = in_array('span-full', $classes, true) ? ' style="grid-column: span 2;"' : '';
            $output .= '<a href="' . esc_url($item->url) . '" class="mega-menu-link" role="menuitem"' . $span . '>';
            $output .= '<i class="' . esc_attr($icon) . '"></i>';
            $output .= '<div class="mm-text">';
            $output .= '<span class="mm-title">' . esc_html($item->title) . '</span>';
            if ($desc) {
                $output .= '<span class="mm-desc">' . esc_html($desc) . '</span>';
            }
            $output .= '</div></a>';
        }
    }

    function end_el(&$output, $item, $depth = 0, $args = null) {
        if ($depth === 0) {
            $output .= '</li>';
        }
    }

    private function link_attributes($item) {
        $attr = '';
        if (!empty($item->attr_title)) {
            $attr .= ' title="' . esc_attr($item->attr_title) . '"';
        }
        if (!empty($item->target)) {
            $attr .= ' target="' . esc_attr($item->target) . '"';
        }
        if (!empty($item->xfn)) {
            $attr .= ' rel="' . esc_attr($item->xfn) . '"';
        }
        return $attr;
    }
}

/**
 * Mobile Menu Walker
 */
class Babarida_Mobile_Walker extends Walker_Nav_Menu {

    function start_lvl(&$output, $depth = 0, $args = null) {
        if ($depth === 0) {
            $output .= '<div class="mobile-sub-menu">';
        }
    }

    function end_lvl(&$output, $depth = 0, $args = null) {
        if ($depth === 0) {
            $output .= '</div>';
        }
    }

    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $has_children = in_array('menu-item-has-children', $classes, true);

        if ($depth === 0) {
            if ($has_children) {
                $output .= '<li class="mobile-nav-item">';
                $output .= '<button class="mobile-nav-link" data-submenu="mobile-sub-' . $item->ID . '">';
                $output .= esc_html($item->title);
                $output .= ' <i class="fa-solid fa-chevron-down"></i>';
                $output .= '</button>';
            } else {
                $output .= '<li class="mobile-nav-item">';
                $output .= '<a href="' . esc_url($item->url) . '" class="mobile-nav-link">' . esc_html($item->title) . '</a>';
            }
        } else {
            $output .= '<a href="' . esc_url($item->url) . '" class="mobile-sub-link"><span></span>' . esc_html($item->title) . '</a>';
        }
    }

    function end_el(&$output, $item, $depth = 0, $args = null) {
        if ($depth === 0) {
            $output .= '</li>';
        }
    }
}

/**
 * Default menu fallback
 */
function babarida_default_menu() {
    echo '<ul class="nav-list">';
    echo '<li class="nav-item"><a href="' . esc_url(home_url('/')) . '" class="nav-link active">Home</a></li>';
    echo '<li class="nav-item"><a href="#destinations" class="nav-link">Destinations</a></li>';
    echo '<li class="nav-item"><a href="#liveaboards" class="nav-link">Liveaboards</a></li>';
    echo '<li class="nav-item"><a href="#pricing" class="nav-link">Pricing</a></li>';
    echo '<li class="nav-item"><a href="#faq" class="nav-link">FAQ</a></li>';
    echo '<li class="nav-item"><a href="' . esc_url(home_url('/checkin')) . '" class="nav-link" style="color:var(--yellow-accent);font-weight:700;">Check-In</a></li>';
    echo '</ul>';
}

function babarida_default_mobile_menu() {
    echo '<ul class="mobile-nav-list">';
    echo '<li class="mobile-nav-item"><a href="' . esc_url(home_url('/')) . '" class="mobile-nav-link">Home</a></li>';
    echo '<li class="mobile-nav-item"><a href="#destinations" class="mobile-nav-link">Destinations</a></li>';
    echo '<li class="mobile-nav-item"><a href="#liveaboards" class="mobile-nav-link">Liveaboards</a></li>';
    echo '<li class="mobile-nav-item"><a href="#faq" class="mobile-nav-link">FAQ</a></li>';
    echo '</ul>';
}
