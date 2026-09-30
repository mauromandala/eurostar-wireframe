<?php
// <Internal Doc Start>
/*
*
* @description: Archivi categoria, settore e catalogo: breadcrumb [es_breadcrumb_archivio], invito finale [es_term_cta], query dei Collection Loop (es_macchine_eurostar, es_macchine_squadron).
* @tags: eurostar, template, archivi
* @group: 
* @name: Eurostar archivi catalogo
* @type: PHP
* @status: published
* @created_by: 1
* @created_at: 2026-09-29 05:36:12
* @updated_at: 2026-09-29 10:41:20
* @is_valid: 1
* @updated_by: 1
* @priority: 13
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

// Archivi del catalogo (categoria macchina, settore, catalogo): breadcrumb, testi dei termini,
// invito finale, query dei Collection Loop. Etichette in WPML String Translation, contesto "Eurostar template".

if (!function_exists('es_t')) {
    function es_t($text)
    {
        return apply_filters('wpml_translate_single_string', $text, 'Eurostar template', $text);
    }
}

add_action('init', function () {
    if (get_option('es_archive_strings') === 'a4') {
        return;
    }
    $strings = [
        'Settori',
        'Non trovi la macchina giusta? Progettiamo su misura.',
        'Non trovi la configurazione giusta per la tua produzione? Progettiamo su misura.',
        'Le sfide del settore %s',
        'Linea Squadron',
        '— piccole e medie produzioni',
        'Scopri Squadron',
        'Tutte',
        'Usate',
        'Categorie macchine',
        'Nessuna macchina in questa categoria.',
        '1 macchina',
        '%d macchine',
    ];
    foreach ($strings as $s) {
        do_action('wpml_register_single_string', 'Eurostar template', $s, $s, false, 'it');
    }
    update_option('es_archive_strings', 'a4', false);
}, 20);

// Breadcrumb degli archivi: Home / Macchine / categoria, Home / Settori / settore, Home / Macchine.
// Attributi: tema="light" per le testate chiare delle pagine (Settori, Servizi, Chi siamo); corrente="…" per la voce finale.
add_shortcode('es_breadcrumb_archivio', function ($atts = []) {
    $atts = shortcode_atts(['tema' => 'dark', 'corrente' => '', 'genitore' => '', 'genitore_nome' => ''], $atts);
    $items = [[apply_filters('wpml_home_url', home_url('/')), es_t('Home')]];
    $aria_current = ' aria-current="page"';
    $current = '';
    if (is_tax('categoria_macchina')) {
        $items[] = [get_post_type_archive_link('macchina'), es_t('Macchine')];
        $current = single_term_title('', false);
    } elseif (is_tax('settore')) {
        $page = get_page_by_path('settori');
        if ($page) {
            $page_id = apply_filters('wpml_object_id', $page->ID, 'page', true);
            $items[] = [get_permalink($page_id), get_the_title($page_id)];
        }
        $current = single_term_title('', false);
    } elseif (is_post_type_archive('macchina')) {
        $current = es_t('Macchine');
    } elseif (is_page()) {
        // Pagine: Home / pagine genitori / pagina corrente.
        foreach (array_reverse(get_post_ancestors(get_queried_object_id())) as $ancestor) {
            $items[] = [get_permalink($ancestor), get_the_title($ancestor)];
        }
        // genitore="ID" (+ genitore_nome="…"): voce intermedia per pagine senza genitore in WordPress (Lavora con noi: Home / Azienda / …).
        if ($atts['genitore'] !== '') {
            $gid = (int) apply_filters('wpml_object_id', (int) $atts['genitore'], 'page', true);
            $items[] = [get_permalink($gid), $atts['genitore_nome'] !== '' ? $atts['genitore_nome'] : get_the_title($gid)];
        }
        $current = get_the_title(get_queried_object_id());
    } elseif (is_singular('post')) {
        // Articolo: Home / News / categoria (la categoria non è la pagina corrente: niente aria-current).
        // Caso studio: Home / Referenze / cliente.
        $pid = get_queried_object_id();
        if (function_exists('es_news_is_caso') && es_news_is_caso($pid)) {
            $page = get_page_by_path('referenze');
            $current = trim((string) get_field('cliente', $pid)) ?: get_the_title($pid);
        } else {
            $page = get_page_by_path('news');
            $cat = function_exists('es_news_categoria') ? es_news_categoria($pid) : null;
            $current = $cat ? $cat->name : get_the_title($pid);
            $aria_current = '';
        }
        if ($page) {
            $page_id = apply_filters('wpml_object_id', $page->ID, 'page', true);
            $items[] = [get_permalink($page_id), get_the_title($page_id)];
        }
    } elseif (is_singular('posizione_lavoro')) {
        // Scheda posizione: Home / Lavora con noi / posizione.
        $page = get_page_by_path('lavora-con-noi');
        if ($page) {
            $page_id = apply_filters('wpml_object_id', $page->ID, 'page', true);
            $items[] = [get_permalink($page_id), get_the_title($page_id)];
        }
        $current = get_the_title(get_queried_object_id());
    }
    // corrente="…": voce finale più breve del titolo della pagina (es. "Servizi" per "Servizi e post-vendita").
    if ($current !== '' && $atts['corrente'] !== '') {
        $current = $atts['corrente'];
    }
    if ($current === '') {
        return '';
    }
    $out = '<ol class="es-breadcrumb es-breadcrumb--' . ($atts['tema'] === 'light' ? 'light' : 'dark') . '" aria-label="' . esc_attr(es_t('Breadcrumb')) . '">';
    foreach ($items as $item) {
        $out .= '<li><a href="' . esc_url($item[0]) . '">' . esc_html($item[1]) . '</a></li><li aria-hidden="true">/</li>';
    }
    return $out . '<li' . $aria_current . '>' . esc_html($current) . '</li></ol>';
});

// WPML: il primo attributo dei contenitori atomic (usato per aria-label, es. nav "Vai al catalogo
// completo") entra nel pacchetto di traduzione del template. Priorità dopo la configurazione di WPML (20).
add_filter('wpml_elementor_widgets_to_translate', function ($widgets) {
    foreach (['e-div-block' => 'Div Block', 'e-flexbox' => 'Flexbox'] as $type => $label) {
        $field = ['field' => 'attributes>value>0>value>value>value', 'type' => $label . ': aria-label', 'editor_type' => 'LINE'];
        if (!isset($widgets[$type])) {
            $widgets[$type] = ['conditions' => ['widgetType' => $type], 'fields' => []];
        }
        if (!in_array($field['field'], array_column($widgets[$type]['fields'], 'field'), true)) {
            $widgets[$type]['fields'][] = $field;
        }
    }
    return $widgets;
}, 1001);

// Titolo dell'invito finale: campo del termine, altrimenti testo generico per tassonomia.
add_shortcode('es_term_cta', function () {
    $term = get_queried_object();
    $titolo = $term instanceof WP_Term ? get_field('titolo_cta', $term) : '';
    if ($titolo) {
        return esc_html($titolo);
    }
    return esc_html($term instanceof WP_Term && $term->taxonomy === 'settore'
        ? es_t('Non trovi la configurazione giusta per la tua produzione? Progettiamo su misura.')
        : es_t('Non trovi la macchina giusta? Progettiamo su misura.'));
});

// Categoria: H1 dal campo "titolo" (Linee complete), altrimenti il nome del termine.
add_shortcode('es_term_titolo', function () {
    $term = get_queried_object();
    if (!$term instanceof WP_Term) {
        return '';
    }
    return esc_html(get_field('titolo', $term) ?: $term->name);
});

// Categoria: testo "descrizione" (Linee complete: sotto "Esempi di layout linea"; Usate: riquadro senza macchine).
add_shortcode('es_term_descrizione', function () {
    $term = get_queried_object();
    $html = $term instanceof WP_Term ? get_field('descrizione', $term) : '';
    return $html ? '<div class="es-term-desc">' . wp_kses_post($html) . '</div>' : '';
});

// Linee complete: esempi di layout dal repeater "layout" (immagine, titolo, didascalia).
add_shortcode('es_layout_linee', function () {
    $term = get_queried_object();
    $rows = $term instanceof WP_Term ? (get_field('layout', $term) ?: []) : [];
    $out = '';
    foreach ($rows as $row) {
        $image = $row['immagine'] ?? null;
        $id = is_array($image) ? (int) $image['ID'] : (int) $image;
        if (!$id) {
            continue;
        }
        $out .= '<figure class="es-layout">'
            . wp_get_attachment_image($id, 'large', false, ['sizes' => '(max-width: 767px) 100vw, 628px'])
            . '<figcaption>';
        if (!empty($row['titolo'])) {
            $out .= '<span class="es-layout-title">' . esc_html($row['titolo']) . '</span>';
        }
        if (!empty($row['didascalia'])) {
            $out .= '<span class="es-layout-caption">' . esc_html($row['didascalia']) . '</span>';
        }
        $out .= '</figcaption></figure>';
    }
    return $out ? '<div class="es-layouts">' . $out . '</div>' : '';
});

// Settore: titolo "Le sfide del settore vino" (nome del termine in minuscolo).
add_shortcode('es_sfide_titolo', function () {
    $term = get_queried_object();
    if (!$term instanceof WP_Term) {
        return '';
    }
    return esc_html(sprintf(es_t('Le sfide del settore %s'), mb_strtolower($term->name)));
});

// Settore: paragrafi delle sfide dai campi sfide_1 e sfide_2 (una riga vuota separa i paragrafi).
add_shortcode('es_sfide', function () {
    $term = get_queried_object();
    if (!$term instanceof WP_Term) {
        return '';
    }
    $out = '';
    foreach (['sfide_1', 'sfide_2'] as $name) {
        $text = (string) get_field($name, $term, false);
        foreach (preg_split('/\R\s*\R/', trim($text)) as $paragraph) {
            if (trim($paragraph) !== '') {
                $out .= '<p>' . esc_html(trim($paragraph)) . '</p>';
            }
        }
    }
    return $out ? '<div class="es-sfide">' . $out . '</div>' : '';
});

// Settore: intestazione del gruppo "Linea Squadron" con link alla pagina Squadron.
add_shortcode('es_squadron_head', function () {
    $page = get_page_by_path('squadron');
    $url = $page ? get_permalink(apply_filters('wpml_object_id', $page->ID, 'page', true)) : '';
    $out = '<div class="es-group-head"><h3 class="es-group-title">' . esc_html(es_t('Linea Squadron'))
        . ' <span>' . esc_html(es_t('— piccole e medie produzioni')) . '</span></h3>';
    if ($url) {
        $out .= '<a class="es-textlink" href="' . esc_url($url) . '">' . esc_html(es_t('Scopri Squadron'))
            . ' <span class="es-arrow" aria-hidden="true">→</span></a>';
    }
    return $out . '</div>';
});

// Termine della linea nella lingua corrente, a partire dallo slug italiano (in EN gli slug cambiano).
if (!function_exists('es_linea_term_id')) {
    function es_linea_term_id($slug)
    {
        global $wpdb;
        $id = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT t.term_id FROM {$wpdb->terms} t
             JOIN {$wpdb->term_taxonomy} tt ON tt.term_id = t.term_id AND tt.taxonomy = 'linea'
             JOIN {$wpdb->prefix}icl_translations icl ON icl.element_id = tt.term_taxonomy_id
                AND icl.element_type = 'tax_linea' AND icl.language_code = 'it'
             WHERE t.slug = %s",
            $slug
        ));
        return $id ? (int) apply_filters('wpml_object_id', $id, 'linea', true) : 0;
    }
}

// Ordine della categoria (campo "ordine" del termine italiano) e suo slug italiano.
if (!function_exists('es_categoria_it')) {
    function es_categoria_it($term_id)
    {
        global $wpdb;
        $it_id = (int) apply_filters('wpml_object_id', $term_id, 'categoria_macchina', true, 'it');
        $slug = $wpdb->get_var($wpdb->prepare("SELECT slug FROM {$wpdb->terms} WHERE term_id = %d", $it_id));
        return ['slug' => (string) $slug, 'ordine' => (int) get_term_meta($it_id, 'ordine', true)];
    }
}

// Collection Loop con sorgente "Current Query": tutte le macchine della linea, nell'ordine
// del campo Ordine (menu_order). Query ID impostato nel loop del template.
// es_catalogo: tutte le macchine Eurostar raggruppate per categoria (ordine della categoria, poi menu_order).
foreach (['es_macchine_eurostar' => 'eurostar', 'es_macchine_squadron' => 'squadron', 'es_catalogo' => 'eurostar'] as $query_id => $linea) {
    add_action('elementor/query/' . $query_id, function ($query) use ($linea, $query_id) {
        $tax_query = (array) $query->get('tax_query');
        $tax_query[] = ['taxonomy' => 'linea', 'field' => 'term_id', 'terms' => es_linea_term_id($linea)];
        $query->set('tax_query', $tax_query);
        $query->set('post_type', 'macchina');
        $query->set('posts_per_page', 200);
        $query->set('nopaging', true);
        $query->set('orderby', ['menu_order' => 'ASC', 'title' => 'ASC']);
        $query->set('paged', 1);
        if ($query_id === 'es_catalogo') {
            $query->set('es_ordina_categoria', true);
        }
    });
}

add_filter('the_posts', function ($posts, $query) {
    if (!$query->get('es_ordina_categoria') || count($posts) < 2) {
        return $posts;
    }
    $key = function ($post) {
        $terms = get_the_terms($post->ID, 'categoria_macchina');
        $ordine = $terms && !is_wp_error($terms) ? es_categoria_it($terms[0]->term_id)['ordine'] : 99;
        return [$ordine, (int) $post->menu_order, $post->post_title];
    };
    usort($posts, function ($a, $b) use ($key) {
        return $key($a) <=> $key($b);
    });
    return $posts;
}, 10, 2);

// Catalogo: filtri per categoria (pulsanti con aria-pressed; Linee complete e Usate sono link alle
// loro pagine) + regione aria-live che annuncia il numero di risultati. Filtra le card in pagina.
add_shortcode('es_catalogo_filtri', function () {
    $terms = get_terms(['taxonomy' => 'categoria_macchina', 'hide_empty' => false]);
    if (is_wp_error($terms) || !$terms) {
        return '';
    }
    $items = [];
    foreach ($terms as $term) {
        $info = es_categoria_it($term->term_id);
        $items[] = [$info['ordine'], $term, $info['slug']];
    }
    usort($items, function ($a, $b) {
        return $a[0] <=> $b[0];
    });
    $out = '<div class="es-filterbar"><div class="es-filterbar-inner" role="group" aria-label="' . esc_attr(es_t('Categorie macchine')) . '">'
        . '<button type="button" class="es-filter" data-filter="all" aria-pressed="true">' . esc_html(es_t('Tutte')) . '</button>';
    foreach ($items as [$ordine, $term, $slug]) {
        $label = $slug === 'usate' ? es_t('Usate') : $term->name;
        if (in_array($slug, ['linee-complete', 'usate'], true)) {
            $out .= '<a class="es-filter" href="' . esc_url(get_term_link($term)) . '">' . esc_html($label) . '</a>';
        } else {
            $out .= '<button type="button" class="es-filter" data-filter="' . esc_attr($slug) . '" aria-pressed="false">' . esc_html($label) . '</button>';
        }
    }
    $GLOBALS['es_catalogo_filtri'] = true;
    return $out . '</div></div>';
});

add_shortcode('es_catalogo_vuoto', function () {
    return '<p class="es-catalog-empty" hidden>' . esc_html(es_t('Nessuna macchina in questa categoria.')) . '</p>'
        . '<p class="es-catalog-status screen-reader-text" role="status" aria-live="polite"'
        . ' data-one="' . esc_attr(es_t('1 macchina')) . '" data-many="' . esc_attr(es_t('%d macchine')) . '"></p>';
});

add_action('wp_footer', function () {
    if (empty($GLOBALS['es_catalogo_filtri'])) {
        return;
    }
    ?>
<script>
(function () {
    var bar = document.querySelector('.es-filterbar');
    if (!bar) return;
    // La barra resta visibile sotto l'header sticky (nel wireframe finiva sotto la nav).
    function stick() {
        var header = document.querySelector('.elementor-location-header');
        var top = 0;
        if (header && getComputedStyle(header).position === 'sticky') {
            top = header.offsetHeight + (parseFloat(getComputedStyle(header).top) || 0);
        }
        // Sticky sul wrapper del widget shortcode: il suo contenitore è il main, non la sola barra.
        (bar.closest('.elementor-widget') || bar).style.top = Math.max(0, top) + 'px';
    }
    // Fino a 1024px le voci stanno su una riga che scorre in orizzontale: sfumature ai bordi
    // quando c'è altro da vedere, e la voce scelta (più la successiva) portata in vista.
    var strip = bar.querySelector('.es-filterbar-inner');
    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    function fades() {
        var max = strip.scrollWidth - strip.clientWidth;
        bar.classList.toggle('can-scroll-left', strip.scrollLeft > 1);
        bar.classList.toggle('can-scroll-right', max > 1 && strip.scrollLeft < max - 1);
    }
    function reveal(el, withNext) {
        if (strip.scrollWidth <= strip.clientWidth) return;
        var fade = 40;
        var box = strip.getBoundingClientRect();
        var last = withNext && el.nextElementSibling ? el.nextElementSibling : el;
        var left = el.getBoundingClientRect().left - box.left;
        var right = last.getBoundingClientRect().right - box.left;
        var delta = 0;
        if (right > box.width - fade) delta = right - (box.width - fade);
        if (left - delta < fade) delta = left - fade;
        if (Math.abs(delta) > 1) strip.scrollBy({left: delta, behavior: reduce ? 'auto' : 'smooth'});
    }
    strip.addEventListener('scroll', fades, {passive: true});
    strip.addEventListener('focusin', function (e) {
        if (e.target.classList.contains('es-filter')) reveal(e.target, false);
    });
    stick();
    fades();
    window.addEventListener('load', function () { stick(); fades(); });
    window.addEventListener('resize', function () { stick(); fades(); });
    bar.addEventListener('click', function (e) {
        var btn = e.target.closest('button[data-filter]');
        if (!btn) return;
        reveal(btn, true);
        var filter = btn.getAttribute('data-filter');
        bar.querySelectorAll('button[data-filter]').forEach(function (b) {
            b.setAttribute('aria-pressed', b === btn ? 'true' : 'false');
        });
        var count = 0;
        document.querySelectorAll('.es-catalog-grid [role="listitem"]').forEach(function (item) {
            var tag = item.querySelector('[data-cat]');
            var show = filter === 'all' || (tag && tag.getAttribute('data-cat') === filter);
            item.hidden = !show;
            if (show) count++;
        });
        var empty = document.querySelector('.es-catalog-empty');
        if (empty) empty.hidden = count !== 0;
        var status = document.querySelector('.es-catalog-status');
        if (status) {
            status.textContent = count === 1 ? status.getAttribute('data-one') : status.getAttribute('data-many').replace('%d', count);
        }
    });
})();
</script>
    <?php
});
