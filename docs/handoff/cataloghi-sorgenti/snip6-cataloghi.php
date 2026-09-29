
// === Cataloghi ===

add_action('init', function () {
    if (get_option('es_cataloghi_strings') === 'c1') {
        return;
    }
    foreach (['Scarica', 'Vedi la categoria', 'PDF, %s', 'in italiano'] as $s) {
        do_action('wpml_register_single_string', 'Eurostar template', $s, $s, false, 'it');
    }
    update_option('es_cataloghi_strings', 'c1', false);
}, 20);

// Cataloghi: una riga per PDF dalla pagina opzioni ACF "Cataloghi" (post_id es_cataloghi, un solo elenco per tutte le lingue).
// La riga compare solo se c'è il file; in inglese usa il PDF inglese se caricato, altrimenti quello italiano ("in italiano").
// Formato e peso letti dal file. Senza righe lo shortcode non stampa nulla e il CSS del Kit nasconde gruppo e sezione.
if (!function_exists('es_catalogo_riga')) {
    function es_catalogo_riga($titolo, $descrizione, $file_it, $file_en, $link = '')
    {
        $en = apply_filters('wpml_current_language', null) === 'en';
        $file = $en && $file_en ? (int) $file_en : (int) $file_it;
        $url = $file ? wp_get_attachment_url($file) : '';
        if (!$url || $titolo === '') {
            return '';
        }
        $path = get_attached_file($file);
        $size = $path && file_exists($path) ? size_format(filesize($path), 1) : '';
        $meta = $size !== '' ? sprintf(es_t('PDF, %s'), $size) : 'PDF';
        $solo_it = $en && !$file_en;
        if ($solo_it) {
            $meta .= ', ' . es_t('in italiano');
        }
        $descrizione = trim((string) $descrizione);
        $icona = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">'
            . '<path d="M6 2h9l5 5v15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1z"/><path d="M14 2v6h6"/></svg>';
        $out = '<li class="es-catalog-row">'
            . '<span class="es-catalog-icon" aria-hidden="true">' . $icona . '</span>'
            . '<div class="es-catalog-info"><h3 class="es-catalog-title">' . esc_html($titolo) . '</h3>'
            . '<p class="es-catalog-meta">' . ($descrizione !== '' ? esc_html($descrizione) . ' · ' : '') . esc_html($meta) . '</p></div>';
        if ($link) {
            $out .= '<a class="es-catalog-dl es-catalog-cat" href="' . esc_url($link) . '">' . esc_html(es_t('Vedi la categoria'))
                . '<span class="screen-reader-text">: ' . esc_html($titolo) . '</span></a>';
        }
        $out .= '<a class="es-catalog-dl" href="' . esc_url($url) . '" type="application/pdf" download' . ($solo_it ? ' hreflang="it"' : '') . '>'
            . '<span aria-hidden="true">↓</span> ' . esc_html(es_t('Scarica'))
            . '<span class="screen-reader-text"> ' . esc_html($titolo) . ' (' . esc_html($meta) . ')</span></a>'
            . '</li>';
        return $out;
    }
}

// [es_cataloghi tipo="generale"] = catalogo generale; [es_cataloghi] = cataloghi per categoria
// (titolo e link "Vedi la categoria" dal termine nella lingua corrente).
add_shortcode('es_cataloghi', function ($atts = []) {
    if (!function_exists('get_field')) {
        return '';
    }
    $atts = shortcode_atts(['tipo' => 'categorie'], $atts);
    $en = apply_filters('wpml_current_language', null) === 'en';
    $rows = '';
    if ($atts['tipo'] === 'generale') {
        $g = get_field('generale', 'es_cataloghi') ?: [];
        $titolo = $en && !empty($g['titolo_en']) ? $g['titolo_en'] : ($g['titolo'] ?? '');
        $rows = es_catalogo_riga($titolo, $en ? ($g['descrizione_en'] ?? '') : ($g['descrizione'] ?? ''), $g['file'] ?? 0, $g['file_en'] ?? 0);
    } else {
        foreach (get_field('cataloghi', 'es_cataloghi') ?: [] as $c) {
            $id = (int) ($c['categoria'] ?? 0);
            $id = $id ? (int) apply_filters('wpml_object_id', $id, 'categoria_macchina', true) : 0;
            $term = $id ? get_term($id, 'categoria_macchina') : null;
            if (!$term || is_wp_error($term)) {
                continue;
            }
            $rows .= es_catalogo_riga($term->name, $en ? ($c['descrizione_en'] ?? '') : ($c['descrizione'] ?? ''), $c['file'] ?? 0, $c['file_en'] ?? 0, get_term_link($term));
        }
    }
    return $rows === '' ? '' : '<ul class="es-catalog-list" role="list">' . $rows . '</ul>';
});
