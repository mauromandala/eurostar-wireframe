<?php
// <Internal Doc Start>
/*
*
* @description: Header (barra alta, nav, mega-menu, ricerca, selettore lingua) e footer (link, social, torna su, WhatsApp) come nel wireframe. Voci da Aspetto > Menu, etichette in WPML String Translation.
* @tags: eurostar, header, footer
* @group: 
* @name: Eurostar header footer
* @type: PHP
* @status: published
* @created_by: 1
* @created_at: 2026-09-28 18:43:06
* @updated_at: 2026-09-28 18:43:06
* @is_valid: 1
* @updated_by: 1
* @priority: 11
* @run_at: all
* @load_as_file: 
* @load_in_block_editor: 
* @condition: {"status":"no","run_if":"assertive","items":[[]]}
*/
?>
<?php if (!defined("ABSPATH")) { return;} // <Internal Doc End> ?>
<?php
if (!defined('ABSPATH')) {
    return;
}

// Header e footer del sito: markup, comportamento e accessibilità come nel wireframe.
// Voci dai menu WordPress (Aspetto > Menu), etichette in WPML String Translation (contesto "Eurostar template").

if (!function_exists('es_t')) {
    function es_t($text)
    {
        return apply_filters('wpml_translate_single_string', $text, 'Eurostar template', $text);
    }
}

add_action('after_setup_theme', function () {
    register_nav_menus([
        'es-principale' => 'Eurostar: menu principale',
        'es-mega-macchine' => 'Eurostar: mega-menu Macchine',
        'es-mega-settori' => 'Eurostar: mega-menu Settori',
        'es-footer-azienda' => 'Eurostar: footer Azienda',
        'es-footer-macchine' => 'Eurostar: footer Macchine',
        'es-footer-settori' => 'Eurostar: footer Settori',
    ]);
});

if (!function_exists('es_register_hf_strings')) {
    function es_register_hf_strings()
    {
        $version = 'hf1';
        if (get_option('es_hf_strings') === $version) {
            return;
        }
        $strings = [
            'Rispondiamo entro 24 ore lavorative — assistenza tecnica IT/EN', 'Catalogo', 'Selettore lingua',
            'Apri la ricerca', 'Ricerca nel sito', 'Cerca nel sito', 'Cerca macchine, settori…', 'Avvia la ricerca',
            'Menu principale', 'Apri il menu', 'Chiudi il menu', 'Mostra sottomenu', 'Sottomenu',
            'Torna in alto', 'Scrivici su WhatsApp', 'Social',
        ];
        foreach ($strings as $s) {
            do_action('wpml_register_single_string', 'Eurostar template', $s, $s, false, 'it');
        }
        update_option('es_hf_strings', $version, false);
    }
}
did_action('init') ? es_register_hf_strings() : add_action('init', 'es_register_hf_strings', 20);

if (!function_exists('es_menu_tree')) {
    function es_menu_tree($location)
    {
        $locations = get_nav_menu_locations();
        if (empty($locations[$location])) {
            return [];
        }
        $items = wp_get_nav_menu_items($locations[$location]);
        if (!$items) {
            return [];
        }
        _wp_menu_item_classes_by_context($items);
        $tree = [];
        foreach ($items as $item) {
            if ((int) $item->menu_item_parent === 0) {
                $item->es_children = [];
                $tree[$item->ID] = $item;
            }
        }
        foreach ($items as $item) {
            if ((int) $item->menu_item_parent !== 0 && isset($tree[$item->menu_item_parent])) {
                $tree[$item->menu_item_parent]->es_children[] = $item;
            }
        }
        return array_values($tree);
    }
}

if (!function_exists('es_item_is_current')) {
    function es_item_is_current($item)
    {
        return in_array('current-menu-item', (array) $item->classes, true);
    }
}

if (!function_exists('es_item_is_section')) {
    function es_item_is_section($item)
    {
        $classes = (array) $item->classes;
        if (es_item_is_current($item) || in_array('current-menu-ancestor', $classes, true) || in_array('current-menu-parent', $classes, true)) {
            return true;
        }
        if (in_array('es-mega-macchine', $classes, true)) {
            return is_singular('macchina') || is_post_type_archive('macchina') || is_tax('categoria_macchina');
        }
        if (in_array('es-mega-settori', $classes, true)) {
            return is_tax('settore');
        }
        return false;
    }
}

if (!function_exists('es_lang_switcher')) {
    function es_lang_switcher()
    {
        $langs = apply_filters('wpml_active_languages', null, ['skip_missing' => 0, 'orderby' => 'custom']);
        if (!$langs) {
            return '';
        }
        $default = apply_filters('wpml_default_language', null);
        uasort($langs, fn($a, $b) => ($b['language_code'] === $default) <=> ($a['language_code'] === $default));
        $names = ['it' => 'Italiano', 'en' => 'English'];
        $parts = [];
        foreach ($langs as $l) {
            $code = $l['language_code'];
            $label = $names[$code] ?? $l['native_name'];
            $attrs = ' href="' . esc_url($l['url']) . '" lang="' . esc_attr($code) . '" hreflang="' . esc_attr($code) . '" aria-label="' . esc_attr($label) . '"';
            $attrs .= $l['active'] ? ' aria-current="true" class="es-lang is-active"' : ' class="es-lang"';
            $parts[] = '<a' . $attrs . '>' . esc_html($code) . '</a>';
        }
        return '<nav class="es-lang-switcher" aria-label="' . esc_attr(es_t('Selettore lingua')) . '">' . implode('<span aria-hidden="true">-</span>', $parts) . '</nav>';
    }
}

if (!function_exists('es_mega_panel')) {
    function es_mega_panel($location, $panel_id, $label)
    {
        $cols = es_menu_tree($location);
        if (!$cols) {
            return '';
        }
        $out = '<div class="es-mega" id="' . esc_attr($panel_id) . '" role="region" aria-label="' . esc_attr(es_t('Sottomenu') . ' ' . $label) . '"><div class="es-mega-inner">';
        foreach ($cols as $i => $col) {
            $is_featured = in_array('es-mega-featured', (array) $col->classes, true);
            $link_label = $col->attr_title ?: $col->title;
            if ($is_featured) {
                $thumb = ($col->type === 'post_type' && has_post_thumbnail($col->object_id)) ? get_the_post_thumbnail($col->object_id, 'medium', ['alt' => '']) : '';
                $out .= '<div class="es-mega-col es-mega-featured"><div class="es-mega-frame" aria-hidden="true">' . $thumb . '</div>';
                $out .= '<h4>' . esc_html($col->title) . '</h4>';
                if ($col->description) {
                    $out .= '<p>' . esc_html($col->description) . '</p>';
                }
                $out .= '<a href="' . esc_url($col->url) . '" class="es-textlink">' . esc_html($col->attr_title ?: $col->title) . ' <span class="es-arrow" aria-hidden="true">→</span></a></div>';
                continue;
            }
            $out .= '<div class="es-mega-col"><h3>' . esc_html($col->title) . '</h3>';
            if ($col->es_children) {
                $out .= '<ul class="es-mega-list">';
                foreach ($col->es_children as $child) {
                    $cur = es_item_is_current($child) ? ' aria-current="page"' : '';
                    $out .= '<li><a href="' . esc_url($child->url) . '"' . $cur . '>' . esc_html($child->title) . '</a></li>';
                }
                $out .= '</ul>';
            } else {
                if ($col->description) {
                    $out .= '<p>' . esc_html($col->description) . '</p>';
                }
                if ($col->url && $col->url !== '#') {
                    $out .= '<a href="' . esc_url($col->url) . '" class="es-textlink">' . esc_html($link_label) . ' <span class="es-arrow" aria-hidden="true">→</span></a>';
                }
            }
            $out .= '</div>';
        }
        return $out . '</div></div>';
    }
}

add_shortcode('es_header', function () {
    $home = apply_filters('wpml_home_url', home_url('/'));
    $cataloghi = get_page_by_path('cataloghi');
    $cataloghi_url = $cataloghi ? get_permalink(apply_filters('wpml_object_id', $cataloghi->ID, 'page', true)) : '#';
    $logo = wp_get_attachment_image(252, 'full', false, ['class' => 'es-logo-main', 'alt' => 'Eurostar', 'loading' => 'eager']);
    $logo30 = wp_get_attachment_image(253, 'full', false, ['class' => 'es-logo-30', 'alt' => '30 anni, 1996-2026', 'loading' => 'eager']);
    $items = es_menu_tree('es-principale');
    $mega = ['es-mega-macchine' => ['es-mega-panel', 'es-mega-trigger'], 'es-mega-settori' => ['es-mega-panel-settori', 'es-mega-trigger-settori']];
    $panels = '';
    $links = '';
    foreach ($items as $item) {
        $classes = (array) $item->classes;
        $current = es_item_is_current($item) ? ' aria-current="page"' : '';
        if (in_array('es-navlink-cta', $classes, true)) {
            $links .= '<a class="es-navlink-cta" href="' . esc_url($item->url) . '"' . $current . '>' . esc_html($item->title) . '</a>';
            continue;
        }
        $active = es_item_is_section($item) ? ' active' : '';
        $a = '<a class="es-navlink' . $active . '" href="' . esc_url($item->url) . '"' . $current . '>' . esc_html($item->title) . '</a>';
        $mega_key = array_values(array_intersect(array_keys($mega), $classes))[0] ?? null;
        if ($mega_key) {
            [$panel_id, $trigger_id] = $mega[$mega_key];
            $panels .= es_mega_panel($mega_key, $panel_id, $item->title);
            $links .= '<span class="es-navlink-group">' . $a . '<button type="button" class="es-mega-caret" id="' . $trigger_id . '" aria-expanded="false" aria-haspopup="true" aria-controls="' . $panel_id . '" aria-label="' . esc_attr(es_t('Mostra sottomenu') . ' ' . $item->title) . '">__CARET__</button></span>';
        } else {
            $links .= $a;
        }
    }
    $out = '<div class="es-header-top"><div class="es-util">';
    $out .= '<div class="es-util-support"><span class="es-util-dot" aria-hidden="true"></span><span class="es-util-text">' . esc_html(es_t('Rispondiamo entro 24 ore lavorative — assistenza tecnica IT/EN')) . '</span></div>';
    $out .= '<div class="es-util-right"><a href="' . esc_url($cataloghi_url) . '" class="es-util-link">__SVG_CATALOGO__' . esc_html(es_t('Catalogo')) . '</a><span class="es-util-divider" aria-hidden="true"></span>' . es_lang_switcher() . '<span class="es-util-divider" aria-hidden="true"></span>';
    $out .= '<div class="es-search-wrap"><button type="button" class="es-search-toggle" id="es-search-trigger" aria-expanded="false" aria-haspopup="true" aria-controls="es-search-panel" aria-label="' . esc_attr(es_t('Apri la ricerca')) . '">__SVG_SEARCH__</button>';
    $out .= '<div class="es-search-panel" id="es-search-panel" role="region" aria-label="' . esc_attr(es_t('Ricerca nel sito')) . '"><form role="search" method="get" action="' . esc_url($home) . '"><label for="es-search-input" class="es-sr-only">' . esc_html(es_t('Cerca nel sito')) . '</label><input id="es-search-input" type="search" name="s" placeholder="' . esc_attr(es_t('Cerca macchine, settori…')) . '"><button type="submit" class="es-search-submit" aria-label="' . esc_attr(es_t('Avvia la ricerca')) . '">__SVG_SUBMIT__</button></form></div></div>';
    $out .= '</div></div>' . $panels . '</div>';
    $out .= '<nav class="es-nav" aria-label="' . esc_attr(es_t('Menu principale')) . '"><a href="' . esc_url($home) . '" class="es-logo-link">' . $logo . '<span class="es-logo-divider" aria-hidden="true"></span>' . $logo30 . '</a>';
    $out .= '<button type="button" class="es-nav-toggle" id="es-nav-toggle" aria-expanded="false" aria-controls="es-nav-links" aria-label="' . esc_attr(es_t('Apri il menu')) . '" data-label-open="' . esc_attr(es_t('Apri il menu')) . '" data-label-close="' . esc_attr(es_t('Chiudi il menu')) . '"><span aria-hidden="true"></span><span aria-hidden="true"></span><span aria-hidden="true"></span></button>';
    $out .= '<div class="es-nav-links" id="es-nav-links">' . $links . '</div></nav>';
    $GLOBALS['es_header_used'] = true;
    return str_replace(['__CARET__', '__SVG_CATALOGO__', '__SVG_SEARCH__', '__SVG_SUBMIT__'], ['<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>', '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M3 7a1 1 0 0 1 1-1h5l2 2h9a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7z"/></svg>', '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>', '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>'], $out);
});

add_shortcode('es_menu_links', function ($atts) {
    $atts = shortcode_atts(['location' => '', 'strong' => ''], $atts);
    $out = '';
    foreach (es_menu_tree($atts['location']) as $item) {
        $cur = es_item_is_current($item) ? ' aria-current="page"' : '';
        $out .= '<a href="' . esc_url($item->url) . '"' . $cur . '>' . esc_html($item->title) . '</a>';
    }
    return $out ? '<div class="es-footer-links' . ($atts['strong'] ? ' es-footer-links--strong' : '') . '">' . $out . '</div>' : '';
});

add_shortcode('es_social', function () {
    $social = [
        'linkedin' => ['LinkedIn', '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4.98 3.5C4.98 4.88 3.87 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1s2.48 1.12 2.48 2.5zM.24 8.25h4.5V23h-4.5V8.25zM8.5 8.25h4.31v2.02h.06c.6-1.13 2.06-2.32 4.24-2.32 4.54 0 5.38 2.99 5.38 6.88V23h-4.5v-6.9c0-1.64-.03-3.76-2.29-3.76-2.29 0-2.64 1.79-2.64 3.64V23H8.5V8.25z"/></svg>'],
        'whatsapp' => ['WhatsApp', '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2c-5.52 0-10 4.48-10 10 0 1.77.46 3.44 1.27 4.89L2 22l5.25-1.28A9.96 9.96 0 0 0 12.04 22c5.52 0 10-4.48 10-10s-4.48-10-10-10zm0 18.06c-1.56 0-3.03-.42-4.3-1.15l-.31-.18-3.12.76.78-3.05-.2-.32A7.98 7.98 0 0 1 4.04 12c0-4.42 3.58-8 8-8s8 3.58 8 8-3.58 8.04-8 8.04zm4.4-6c-.24-.12-1.42-.7-1.64-.78-.22-.08-.38-.12-.54.12-.16.24-.62.78-.76.94-.14.16-.28.18-.52.06-.24-.12-1.02-.38-1.94-1.2-.72-.64-1.2-1.44-1.34-1.68-.14-.24-.02-.37.1-.49.11-.11.24-.28.36-.42.12-.14.16-.24.24-.4.08-.16.04-.3-.02-.42-.06-.12-.54-1.3-.74-1.78-.2-.47-.4-.4-.54-.41h-.46c-.16 0-.42.06-.64.3-.22.24-.84.82-.84 2s.86 2.32.98 2.48c.12.16 1.7 2.6 4.12 3.64.58.25 1.03.4 1.38.51.58.18 1.11.16 1.53.1.47-.07 1.42-.58 1.62-1.14.2-.56.2-1.04.14-1.14-.06-.1-.22-.16-.46-.28z"/></svg>'],
        'youtube' => ['YouTube', '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23.5 6.2s-.2-1.6-.9-2.3c-.9-.9-1.9-.9-2.4-1C16.9 2.6 12 2.6 12 2.6h0s-4.9 0-8.2.3c-.5 0-1.5.1-2.4 1-.7.7-.9 2.3-.9 2.3S.2 8.1.2 10v1.9c0 1.9.3 3.8.3 3.8s.2 1.6.9 2.3c.9.9 2.1.9 2.6 1 1.9.2 8 .3 8 .3s4.9 0 8.2-.3c.5 0 1.5-.1 2.4-1 .7-.7.9-2.3.9-2.3s.3-1.9.3-3.8V10c0-1.9-.3-3.8-.3-3.8zM9.7 14.1V7.9l6.4 3.1-6.4 3.1z"/></svg>'],
        'facebook' => ['Facebook', '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22 12a10 10 0 1 0-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.4h-1.2c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.4v7A10 10 0 0 0 22 12z"/></svg>'],
        'instagram' => ['Instagram', '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.2c3.2 0 3.6 0 4.9.1 1.2.1 2 .3 2.4.5.6.2 1 .5 1.5 1 .4.4.7.9 1 1.5.2.4.4 1.2.5 2.4.1 1.3.1 1.7.1 4.9s0 3.6-.1 4.9c-.1 1.2-.3 2-.5 2.4-.2.6-.5 1-1 1.5-.4.4-.9.7-1.5 1-.4.2-1.2.4-2.4.5-1.3.1-1.7.1-4.9.1s-3.6 0-4.9-.1c-1.2-.1-2-.3-2.4-.5-.6-.2-1-.5-1.5-1-.4-.4-.7-.9-1-1.5-.2-.4-.4-1.2-.5-2.4C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.9c.1-1.2.3-2 .5-2.4.2-.6.5-1 1-1.5.4-.4.9-.7 1.5-1 .4-.2 1.2-.4 2.4-.5C8.4 2.2 8.8 2.2 12 2.2zm0 1.8c-3.1 0-3.5 0-4.7.1-1 0-1.6.2-1.9.4-.5.2-.8.4-1.2.8-.4.4-.6.7-.8 1.2-.1.3-.3.9-.4 1.9C3 9.5 3 9.9 3 13s0 3.5.1 4.7c0 1 .2 1.6.4 1.9.2.5.4.8.8 1.2.4.4.7.6 1.2.8.3.1.9.3 1.9.4C8.5 21 8.9 21 12 21s3.5 0 4.7-.1c1 0 1.6-.2 1.9-.4.5-.2.8-.4 1.2-.8.4-.4.6-.7.8-1.2.1-.3.3-.9.4-1.9.1-1.2.1-1.6.1-4.7s0-3.5-.1-4.7c0-1-.2-1.6-.4-1.9-.2-.5-.4-.8-.8-1.2-.4-.4-.7-.6-1.2-.8-.3-.1-.9-.3-1.9-.4C15.5 4 15.1 4 12 4zm0 3.6a4.4 4.4 0 1 1 0 8.8 4.4 4.4 0 0 1 0-8.8zm0 1.8a2.6 2.6 0 1 0 0 5.2 2.6 2.6 0 0 0 0-5.2zm5.6-2a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/></svg>'],
        'tiktok' => ['TikTok', '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16.6 5.8a4.3 4.3 0 0 1-3-3.8h-3v13.4a2.6 2.6 0 1 1-1.8-2.5V9.6a5.9 5.9 0 1 0 4.8 5.8V9.1a7.3 7.3 0 0 0 4.3 1.4V7.1a4.3 4.3 0 0 1-1.3-1.3z"/></svg>']
    ];
    $urls = ['whatsapp' => 'https://wa.me/393453450371'];
    $out = '<div class="es-social">';
    foreach ($social as $key => [$label, $svg]) {
        $url = $urls[$key] ?? '#';
        $ext = $url !== '#' ? ' target="_blank" rel="noopener"' : '';
        $out .= '<a href="' . esc_url($url) . '"' . $ext . ' aria-label="' . esc_attr($label) . '" class="es-social-link">' . $svg . '</a>';
    }
    return $out . '</div>';
});

add_shortcode('es_back_to_top', function () {
    $GLOBALS['es_header_used'] = true;
    return '<a href="#" id="es-back-to-top" class="es-back-to-top" aria-label="' . esc_attr(es_t('Torna in alto')) . '"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 15l-6-6-6 6"/></svg></a>';
});

add_shortcode('es_whatsapp_float', function () {
    $label = es_t('Scrivici su WhatsApp');
    return '<a href="https://wa.me/393453450371" target="_blank" rel="noopener" class="es-wa-float" aria-label="' . esc_attr($label) . '" title="' . esc_attr($label) . '"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2c-5.52 0-10 4.48-10 10 0 1.77.46 3.44 1.27 4.89L2 22l5.25-1.28A9.96 9.96 0 0 0 12.04 22c5.52 0 10-4.48 10-10s-4.48-10-10-10zm0 18.06c-1.56 0-3.03-.42-4.3-1.15l-.31-.18-3.12.76.78-3.05-.2-.32A7.98 7.98 0 0 1 4.04 12c0-4.42 3.58-8 8-8s8 3.58 8 8-3.58 8.04-8 8.04zm4.4-6c-.24-.12-1.42-.7-1.64-.78-.22-.08-.38-.12-.54.12-.16.24-.62.78-.76.94-.14.16-.28.18-.52.06-.24-.12-1.02-.38-1.94-1.2-.72-.64-1.2-1.44-1.34-1.68-.14-.24-.02-.37.1-.49.11-.11.24-.28.36-.42.12-.14.16-.24.24-.4.08-.16.04-.3-.02-.42-.06-.12-.54-1.3-.74-1.78-.2-.47-.4-.4-.54-.41h-.46c-.16 0-.42.06-.64.3-.22.24-.84.82-.84 2s.86 2.32.98 2.48c.12.16 1.7 2.6 4.12 3.64.58.25 1.03.4 1.38.51.58.18 1.11.16 1.53.1.47-.07 1.42-.58 1.62-1.14.2-.56.2-1.04.14-1.14-.06-.1-.22-.16-.46-.28z"/></svg></a>';
});

add_action('wp_footer', function () {
    if (empty($GLOBALS['es_header_used'])) {
        return;
    }
    ?>
<script>
(function(){
  var hdr = document.querySelector('.elementor-location-header');
  function setTop(){ var top = document.querySelector('.es-header-top .es-util'); if (hdr && top) { hdr.style.top = (-top.offsetHeight) + 'px'; } }
  setTop(); window.addEventListener('resize', setTop);
  function closeMega(panel, focus){ panel.classList.remove('is-open'); var t = document.querySelector('.es-mega-caret[aria-controls="' + panel.id + '"]'); if (t) { t.setAttribute('aria-expanded','false'); if (focus) t.focus(); } }
  document.addEventListener('click', function(e){
    var btn = e.target.closest && e.target.closest('#es-nav-toggle');
    if (btn) { var p = document.getElementById('es-nav-links'); var open = p.classList.toggle('is-open'); btn.setAttribute('aria-expanded', open ? 'true' : 'false'); btn.setAttribute('aria-label', open ? btn.dataset.labelClose : btn.dataset.labelOpen); return; }
    var caret = e.target.closest && e.target.closest('.es-mega-caret');
    if (caret) {
      var panel = document.getElementById(caret.getAttribute('aria-controls')); if (!panel) return;
      var nav = caret.closest('nav'); var opening = !panel.classList.contains('is-open');
      document.querySelectorAll('.es-mega.is-open').forEach(function(p){ if (p !== panel) closeMega(p, false); });
      if (opening) { panel.style.top = nav.getBoundingClientRect().bottom + 'px'; panel.classList.add('is-open'); caret.setAttribute('aria-expanded','true'); } else { closeMega(panel, false); }
      return;
    }
    document.querySelectorAll('.es-mega.is-open').forEach(function(p){ if (!p.contains(e.target)) closeMega(p, false); });
    var st = e.target.closest && e.target.closest('#es-search-trigger'); var sp = document.getElementById('es-search-panel');
    if (st && sp) { var o = !sp.classList.contains('is-open'); sp.classList.toggle('is-open', o); st.setAttribute('aria-expanded', o ? 'true' : 'false'); if (o) { var i = sp.querySelector('input'); if (i) i.focus(); } return; }
    if (sp && sp.classList.contains('is-open') && !sp.contains(e.target)) { sp.classList.remove('is-open'); var t2 = document.getElementById('es-search-trigger'); if (t2) t2.setAttribute('aria-expanded','false'); }
    var top = e.target.closest && e.target.closest('#es-back-to-top');
    if (top) { e.preventDefault(); var rm = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches; window.scrollTo({ top: 0, behavior: rm ? 'auto' : 'smooth' }); }
  });
  document.addEventListener('keydown', function(e){
    if (e.key !== 'Escape') return;
    var p = document.getElementById('es-nav-links'), b = document.getElementById('es-nav-toggle');
    if (p && p.classList.contains('is-open')) { p.classList.remove('is-open'); b.setAttribute('aria-expanded','false'); b.setAttribute('aria-label', b.dataset.labelOpen); b.focus(); }
    document.querySelectorAll('.es-mega.is-open').forEach(function(x){ closeMega(x, true); });
    var sp = document.getElementById('es-search-panel'); if (sp && sp.classList.contains('is-open')) { sp.classList.remove('is-open'); var t = document.getElementById('es-search-trigger'); t.setAttribute('aria-expanded','false'); t.focus(); }
  });
  window.addEventListener('resize', function(){ var nav = document.querySelector('.es-nav'); if (!nav) return; document.querySelectorAll('.es-mega.is-open').forEach(function(p){ p.style.top = nav.getBoundingClientRect().bottom + 'px'; }); });
})();
</script>
    <?php
});
