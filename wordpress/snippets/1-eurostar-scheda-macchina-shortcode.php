<?php
// <Internal Doc Start>
/*
*
* @description: Shortcode della scheda macchina (breadcrumb, Adatta per, contenitori, descrizione, caratteristiche, download, galleria). Etichette traducibili in WPML String Translation, contesto "Eurostar template".
* @tags: eurostar, template
* @group: 
* @name: Eurostar scheda macchina shortcode
* @type: PHP
* @status: published
* @created_by: 1
* @created_at: 2026-09-28 15:28:24
* @updated_at: 2026-09-28 15:28:24
* @is_valid: 1
* @updated_by: 1
* @priority: 10
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

// Etichette fisse della scheda macchina, traducibili in WPML String Translation (contesto "Eurostar template").
if (!function_exists('es_t')) {
    function es_t($text)
    {
        return apply_filters('wpml_translate_single_string', $text, 'Eurostar template', $text);
    }
}

if (!function_exists('es_register_template_strings')) {
    function es_register_template_strings()
    {
        $version = 'v3';
        if (get_option('es_template_strings') === $version) {
            return;
        }
        $strings = [
            'Home', 'Macchine', 'Breadcrumb', 'Adatta per', 'Adatta per (tappo)', 'Contenitori',
            'Vetro', 'PET', 'HDPE', 'Lattina alluminio', 'Tipologia di valvole isobariche',
            'Tipologia chiusura', 'Tecnologia di riempimento', 'Prodotto da riempire',
            'Campo di produzione della gamma', 'Cambio formato', 'contenitori/ora', 'Download',
            'Scheda tecnica', 'Galleria immagini', 'Immagine', 'Configura la tua',
        ];
        foreach ($strings as $s) {
            do_action('wpml_register_single_string', 'Eurostar template', $s, $s, false, 'it');
        }
        update_option('es_template_strings', $version, false);
    }
}
did_action('init') ? es_register_template_strings() : add_action('init', 'es_register_template_strings', 20);

add_shortcode('es_breadcrumb', function () {
    $id = get_the_ID();
    if (!$id) {
        return '';
    }
    $items = [
        [apply_filters('wpml_home_url', home_url('/')), es_t('Home')],
        [get_post_type_archive_link(get_post_type($id)), es_t('Macchine')],
    ];
    $terms = get_the_terms($id, 'categoria_macchina');
    if ($terms && !is_wp_error($terms)) {
        $items[] = [get_term_link($terms[0]), $terms[0]->name];
    }
    $out = '<ol class="es-breadcrumb" aria-label="' . esc_attr(es_t('Breadcrumb')) . '">';
    foreach ($items as $item) {
        $out .= '<li><a href="' . esc_url($item[0]) . '">' . esc_html($item[1]) . '</a></li><li aria-hidden="true">/</li>';
    }
    return $out . '<li aria-current="page">' . esc_html(get_the_title($id)) . '</li></ol>';
});

add_shortcode('es_adatta_per', function () {
    $id = get_the_ID();
    $chips = [];
    $chiusura = get_field('tipologia_chiusura', $id);
    if ($chiusura) {
        $label = es_t('Adatta per (tappo)');
        $terms = get_the_terms($id, 'categoria_macchina');
        $chips[] = [$terms && !is_wp_error($terms) ? get_term_link($terms[0]) : '', $chiusura];
    } else {
        $label = es_t('Adatta per');
        $terms = wp_get_object_terms($id, 'settore', ['orderby' => 'term_order']);
        if (!is_wp_error($terms)) {
            foreach ($terms as $t) {
                $chips[] = [get_term_link($t), $t->name];
            }
        }
    }
    if (!$chips) {
        return '';
    }
    $out = '<div class="es-adatta"><span class="es-meta-label">' . esc_html($label) . '</span>';
    foreach ($chips as $c) {
        $out .= '<a class="es-chip" href="' . esc_url($c[0]) . '">' . esc_html($c[1]) . '</a>';
    }
    return $out . '</div>';
});

add_shortcode('es_contenitori', function () {
    $id = get_the_ID();
    $tipi = get_field('contenitori_tipi', $id) ?: [];
    $dicitura = get_field('contenitori', $id);
    if (!$tipi && !$dicitura) {
        return '';
    }
    $icons = [
        'vetro' => ['Vetro', '<path d="M9 2h6v3.5l1.5 2.5v12a2 2 0 0 1-2 2h-5a2 2 0 0 1-2-2V8l1.5-2.5V2z"/><path d="M9 2h6"/>'],
        'pet' => ['PET', '<path d="M10 2h4v2l1.5 2v3l-1 1.25 1 1.25v2.5l-1 1.25 1 1.25V20a2 2 0 0 1-2 2h-3a2 2 0 0 1-2-2v-3.5l1-1.25-1-1.25v-2.5l1-1.25-1-1.25V6L10 4V2z"/>'],
        'hdpe' => ['HDPE', '<path d="M8 2h4v3h2.5A3.5 3.5 0 0 1 18 8.5V20a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V8.5L8 5V2z"/><path d="M14 5v5h4"/>'],
        'lattina' => ['Lattina alluminio', '<path d="M7 5.5L8 3h8l1 2.5v13L16 21H8l-1-2.5z"/><path d="M7 5.5h10M7 18.5h10"/>'],
    ];
    $row = '<span class="es-meta-label">' . esc_html(es_t('Contenitori')) . '</span>';
    foreach ($tipi as $tipo) {
        $key = is_array($tipo) ? $tipo['value'] : $tipo;
        if (!isset($icons[$key])) {
            continue;
        }
        $name = es_t($icons[$key][0]);
        $row .= '<span class="es-contenitori-icon" title="' . esc_attr($name) . '"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" role="img" aria-label="' . esc_attr($name) . '">' . $icons[$key][1] . '</svg></span>';
    }
    if (!$tipi && $dicitura) {
        return '<div class="es-contenitori"><div class="es-contenitori-row">' . $row . '<span class="es-contenitori-text">' . esc_html($dicitura) . '</span></div></div>';
    }
    $out = '<div class="es-contenitori"><div class="es-contenitori-row">' . $row . '</div>';
    if ($dicitura) {
        $out .= '<p class="es-contenitori-text">' . esc_html($dicitura) . '</p>';
    }
    return $out . '</div>';
});

add_shortcode('es_descrizione', function () {
    $id = get_the_ID();
    $html = get_field('descrizione', $id);
    $valvole = get_field('tipologia_valvole', $id);
    $out = $html ? wp_kses_post($html) : '';
    if ($valvole) {
        $out .= '<p>' . esc_html(es_t('Tipologia di valvole isobariche')) . ': ' . esc_html($valvole) . '</p>';
    }
    return $out ? '<div class="es-descrizione">' . $out . '</div>' : '';
});

add_shortcode('es_caratteristiche', function () {
    $id = get_the_ID();
    $rows = [
        ['tipologia_chiusura', 'Tipologia chiusura', ''],
        ['tecnologia_riempimento', 'Tecnologia di riempimento', ''],
        ['contenitori', 'Contenitori', ''],
        ['prodotto', 'Prodotto da riempire', ''],
        ['contenitori_ora', 'Campo di produzione della gamma', 'contenitori/ora'],
        ['cambio_formato', 'Cambio formato', ''],
    ];
    $out = '';
    foreach ($rows as $r) {
        $value = get_field($r[0], $id);
        if (!$value) {
            continue;
        }
        $suffix = $r[2] ? ' ' . es_t($r[2]) : '';
        $out .= '<tr><th scope="row">' . esc_html(es_t($r[1])) . '</th><td>' . esc_html($value . $suffix) . '</td></tr>';
    }
    return $out ? '<table class="es-spec-table"><tbody>' . $out . '</tbody></table>' : '';
});

add_shortcode('es_download', function () {
    $id = get_the_ID();
    $file = get_field('scheda_tecnica', $id);
    if (!$file || empty($file['url'])) {
        return '';
    }
    $path = get_attached_file($file['ID']);
    $size = '';
    if ($path && file_exists($path)) {
        $bytes = filesize($path);
        $size = $bytes >= 1048576 ? number_format($bytes / 1048576, 1, '.', '') . ' MB' : max(1, (int) round($bytes / 1024)) . ' KB';
    }
    $label = es_t('Scheda tecnica') . ' ' . get_the_title($id) . ' (PDF' . ($size ? ', ' . $size : '') . ')';
    return '<h2 class="es-download-title">' . esc_html(es_t('Download')) . '</h2><div class="es-download"><a href="' . esc_url($file['url']) . '" class="es-dl-link" target="_blank" rel="noopener"><span aria-hidden="true">↓</span> ' . esc_html($label) . '</a></div>';
});

add_shortcode('es_galleria', function () {
    $id = get_the_ID();
    $images = get_field('galleria', $id) ?: [];
    if (!$images) {
        return '<div class="es-gallery es-gallery--vuota"><div class="es-gallery-main"><div class="es-gallery-frame" aria-hidden="true"></div></div></div>';
    }
    $GLOBALS['es_gallery_used'] = true;
    $first = $images[0];
    $out = '<div class="es-gallery"><div class="es-gallery-main"><img class="es-gallery-img" src="' . esc_url($first['sizes']['large'] ?? $first['url']) . '" alt="' . esc_attr($first['alt']) . '"></div>';
    if (count($images) > 1) {
        $out .= '<div class="es-gallery-thumbs" role="tablist" aria-label="' . esc_attr(es_t('Galleria immagini') . ' ' . get_the_title($id)) . '">';
        foreach ($images as $i => $img) {
            $alt = $img['alt'] ?: es_t('Immagine') . ' ' . ($i + 1);
            $out .= '<button type="button" class="es-gallery-thumb' . ($i === 0 ? ' is-active' : '') . '" role="tab" aria-selected="' . ($i === 0 ? 'true' : 'false') . '" aria-label="' . esc_attr($alt) . '" data-src="' . esc_url($img['sizes']['large'] ?? $img['url']) . '" data-alt="' . esc_attr($img['alt']) . '"><img src="' . esc_url($img['sizes']['thumbnail'] ?? $img['url']) . '" alt=""></button>';
        }
        $out .= '</div>';
    }
    return $out . '</div>';
});

add_action('wp_footer', function () {
    if (empty($GLOBALS['es_gallery_used'])) {
        return;
    }
    echo "<script>document.addEventListener('click',function(e){var t=e.target.closest&&e.target.closest('.es-gallery-thumb');if(!t)return;var g=t.closest('.es-gallery');if(!g)return;g.querySelectorAll('.es-gallery-thumb').forEach(function(x){x.classList.remove('is-active');x.setAttribute('aria-selected','false');});t.classList.add('is-active');t.setAttribute('aria-selected','true');var img=g.querySelector('.es-gallery-img');if(img){img.src=t.getAttribute('data-src');img.alt=t.getAttribute('data-alt')||'';}});</script>";
});

add_shortcode('es_cta_titolo', function () {
    $id = get_the_ID();
    $titolo = get_field('titolo_cta', $id);
    return esc_html($titolo ?: es_t('Configura la tua') . ' ' . get_the_title($id));
});
