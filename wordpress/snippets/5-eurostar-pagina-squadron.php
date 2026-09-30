<?php
// <Internal Doc Start>
/*
*
* @description: Pagina Squadron: card estese [es_squadron_gamma], resto della gamma raggruppato [es_squadron_resto], settori serviti [es_squadron_settori]; ancore #slug dei modelli.
* @tags: eurostar, template, squadron
* @group: 
* @name: Eurostar pagina Squadron
* @type: PHP
* @status: published
* @created_by: 1
* @created_at: 2026-09-29 06:56:11
* @updated_at: 2026-09-29 06:56:11
* @is_valid: 1
* @updated_by: 1
* @priority: 14
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

// Pagina Squadron: card estese dei modelli con descrizione (ATHENA, EXACTA), "Resto della gamma"
// raggruppato con il campo "gruppo_squadron", pillole dei settori serviti. Ogni modello ha l'ancora
// #slug a cui puntano i link delle card Squadron (snippet "Eurostar card macchina").
// Etichette in WPML String Translation, contesto "Eurostar template".

if (!function_exists('es_t')) {
    function es_t($text)
    {
        return apply_filters('wpml_translate_single_string', $text, 'Eurostar template', $text);
    }
}

add_action('init', function () {
    if (get_option('es_squadron_strings') === 's1') {
        return;
    }
    foreach (['Tecnologia', 'Campo di produzione'] as $s) {
        do_action('wpml_register_single_string', 'Eurostar template', $s, $s, false, 'it');
    }
    update_option('es_squadron_strings', 's1', false);
}, 20);

// Modelli Squadron della lingua corrente, nell'ordine del campo Ordine.
if (!function_exists('es_squadron_modelli')) {
    function es_squadron_modelli()
    {
        $linea = function_exists('es_linea_term_id') ? es_linea_term_id('squadron') : 0;
        if (!$linea) {
            return [];
        }
        return get_posts([
            'post_type' => 'macchina',
            'posts_per_page' => -1,
            'orderby' => ['menu_order' => 'ASC', 'title' => 'ASC'],
            'tax_query' => [['taxonomy' => 'linea', 'field' => 'term_id', 'terms' => $linea]],
            'suppress_filters' => false,
        ]);
    }
}

if (!function_exists('es_squadron_settori')) {
    function es_squadron_settori($post_id)
    {
        $terms = wp_get_object_terms($post_id, 'settore', ['orderby' => 'term_order']);
        return is_wp_error($terms) ? [] : $terms;
    }
}

// Card estese: i modelli che hanno la descrizione tecnica.
add_shortcode('es_squadron_gamma', function () {
    $out = '';
    foreach (es_squadron_modelli() as $p) {
        $descrizione = get_field('descrizione', $p->ID);
        if (!$descrizione) {
            continue;
        }
        $media = has_post_thumbnail($p) ? get_the_post_thumbnail($p, 'large', ['sizes' => '(max-width: 767px) 100vw, 628px']) : '';
        $out .= '<div class="es-sq-card" id="' . esc_attr($p->post_name) . '">'
            . '<div class="es-sq-media"' . ($media ? '' : ' aria-hidden="true"') . '>' . $media . '</div>'
            . '<div class="es-sq-body"><h3 class="es-sq-name">' . esc_html(get_the_title($p)) . '</h3>';
        if ($tipologia = get_field('tipologia', $p->ID)) {
            $out .= '<p class="es-sq-type">' . esc_html($tipologia) . '</p>';
        }
        $out .= '<div class="es-sq-desc">' . wp_kses_post($descrizione);
        if ($valvole = get_field('tipologia_valvole', $p->ID)) {
            $out .= '<p>' . esc_html(es_t('Tipologia di valvole isobariche')) . ': ' . esc_html($valvole) . '</p>';
        }
        $out .= '</div>';
        $settori = wp_list_pluck(es_squadron_settori($p->ID), 'name');
        $ora = get_field('contenitori_ora', $p->ID);
        $rows = [
            ['Adatta per', implode(' / ', $settori)],
            ['Contenitori', get_field('contenitori', $p->ID)],
            ['Prodotto', get_field('prodotto', $p->ID)],
            ['Tecnologia', get_field('tecnologia_riempimento', $p->ID)],
            ['Campo di produzione', $ora ? $ora . ' ' . es_t('contenitori/ora') : ''],
        ];
        $table = '';
        foreach ($rows as $row) {
            if ($row[1]) {
                $table .= '<tr><th scope="row">' . esc_html(es_t($row[0])) . '</th><td>' . esc_html($row[1]) . '</td></tr>';
            }
        }
        if ($table) {
            $out .= '<table class="es-sq-table"><tbody>' . $table . '</tbody></table>';
        }
        $file = get_field('scheda_tecnica', $p->ID);
        if ($file && !empty($file['url'])) {
            $path = get_attached_file($file['ID']);
            $size = '';
            if ($path && file_exists($path)) {
                $bytes = filesize($path);
                $size = $bytes >= 1048576 ? number_format($bytes / 1048576, 1, '.', '') . ' MB' : max(1, (int) round($bytes / 1024)) . ' KB';
            }
            $label = es_t('Scheda tecnica') . ' ' . get_the_title($p) . ' (PDF' . ($size ? ', ' . $size : '') . ')';
            $out .= '<div class="es-sq-dl"><a href="' . esc_url($file['url']) . '" class="es-dl-link" target="_blank" rel="noopener">'
                . '<span aria-hidden="true">↓</span> ' . esc_html($label) . '</a></div>';
        }
        $out .= '</div></div>';
    }
    return $out ? '<div class="es-sq-cards">' . $out . '</div>' : '';
});

// Resto della gamma: modelli senza descrizione, raggruppati per "gruppo_squadron" (titolo del gruppo)
// o singoli (nome del modello). Settori: unione, nell'ordine del primo modello.
add_shortcode('es_squadron_resto', function () {
    $groups = [];
    foreach (es_squadron_modelli() as $p) {
        if (get_field('descrizione', $p->ID)) {
            continue;
        }
        $label = trim((string) get_field('gruppo_squadron', $p->ID));
        $key = $label !== '' ? 'g:' . $label : 'p:' . $p->ID;
        if (!isset($groups[$key])) {
            $groups[$key] = ['label' => $label !== '' ? $label : get_the_title($p), 'slugs' => [], 'settori' => []];
        }
        $groups[$key]['slugs'][] = $p->post_name;
        foreach (es_squadron_settori($p->ID) as $t) {
            $groups[$key]['settori'][$t->term_id] = $t->name;
        }
    }
    $out = '';
    foreach ($groups as $g) {
        $slugs = $g['slugs'];
        $out .= '<div class="es-sq-item" id="' . esc_attr(array_shift($slugs)) . '">';
        foreach ($slugs as $slug) {
            $out .= '<span class="es-sq-anchor" id="' . esc_attr($slug) . '"></span>';
        }
        $out .= '<h4 class="es-sq-item-name">' . esc_html($g['label']) . '</h4>';
        if ($g['settori']) {
            $out .= '<p class="es-sq-item-sectors">' . esc_html(implode(' / ', $g['settori'])) . '</p>';
        }
        $out .= '</div>';
    }
    return $out ? '<div class="es-sq-items">' . $out . '</div>' : '';
});

// Settori serviti: settori di tutti i modelli Squadron, nell'ordine del campo Ordine del settore.
add_shortcode('es_squadron_settori', function () {
    $terms = [];
    foreach (es_squadron_modelli() as $p) {
        foreach (es_squadron_settori($p->ID) as $t) {
            $terms[$t->term_id] = $t;
        }
    }
    if (!$terms) {
        return '';
    }
    $ordine = function ($t) {
        $it = (int) apply_filters('wpml_object_id', $t->term_id, 'settore', true, 'it');
        return (int) get_term_meta($it, 'ordine', true);
    };
    usort($terms, function ($a, $b) use ($ordine) {
        return $ordine($a) <=> $ordine($b);
    });
    $out = '';
    foreach ($terms as $t) {
        $out .= '<a class="es-chip es-chip--lg" href="' . esc_url(get_term_link($t)) . '">' . esc_html($t->name) . '</a>';
    }
    return '<div class="es-chips">' . $out . '</div>';
});
