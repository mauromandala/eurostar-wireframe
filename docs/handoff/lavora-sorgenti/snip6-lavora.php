
// === Lavora con noi ===

add_action('init', function () {
    if (get_option('es_lavora_strings') === 'l1') {
        return;
    }
    foreach (['Rif. %s', 'Dettagli', 'Tempo indeterminato', 'Tempo determinato', 'Stage', 'Sede', 'Contratto',
        'Attività principali', 'Requisiti richiesti', 'Requisiti preferenziali', 'Cosa offriamo', 'Posizioni aperte'] as $s) {
        do_action('wpml_register_single_string', 'Eurostar template', $s, $s, false, 'it');
    }
    update_option('es_lavora_strings', 'l1', false);
}, 20);

if (!function_exists('es_job_icon')) {
    // Icone del wireframe: sede (segnaposto), contratto (valigetta), freccia della card.
    function es_job_icon($tipo)
    {
        $paths = [
            'sede' => '<path d="M12 22s7-7.58 7-12.5A7 7 0 0 0 5 9.5C5 14.42 12 22 12 22z"/><circle cx="12" cy="9.5" r="2.5"/>',
            'contratto' => '<path d="M3 7a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v11a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7z"/><path d="M8 6V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v1"/>',
        ];
        if ($tipo === 'freccia') {
            return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
        }
        return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">' . $paths[$tipo] . '</svg>';
    }
}

if (!function_exists('es_job_meta')) {
    // Righe "sede" e "contratto" (card e testata della scheda). Il contratto è un select ACF: etichetta tradotta in String Translation.
    function es_job_meta($post_id)
    {
        $out = '';
        $sede = trim((string) get_field('sede', $post_id));
        if ($sede !== '') {
            $out .= '<span class="es-job-meta-row">' . es_job_icon('sede') . '<span class="screen-reader-text">' . esc_html(es_t('Sede')) . ': </span>' . esc_html($sede) . '</span>';
        }
        $contratto = get_field('tipo_contratto', $post_id);
        if ($contratto) {
            $out .= '<span class="es-job-meta-row">' . es_job_icon('contratto') . '<span class="screen-reader-text">' . esc_html(es_t('Contratto')) . ': </span>' . esc_html(es_t($contratto)) . '</span>';
        }
        return $out;
    }
}

// Lavora con noi: card delle posizioni pubblicate nella lingua corrente, nell'ordine del campo "Ordine" (menu_order).
// Senza posizioni lo shortcode non stampa nulla e il CSS del Kit nasconde la sezione "Posizioni aperte".
add_shortcode('es_posizioni', function () {
    $posts = get_posts([
        'post_type' => 'posizione_lavoro',
        'post_status' => 'publish',
        'numberposts' => -1,
        'orderby' => ['menu_order' => 'ASC', 'title' => 'ASC'],
        'suppress_filters' => false,
    ]);
    if (!$posts) {
        return '';
    }
    $out = '<ul class="es-job-grid" role="list">';
    foreach ($posts as $post) {
        $reparto = trim((string) get_field('reparto', $post->ID));
        $rif = trim((string) get_field('rif', $post->ID));
        $out .= '<li><a class="es-job-card" href="' . esc_url(get_permalink($post)) . '">'
            . '<h3>' . esc_html(get_the_title($post)) . '</h3>'
            . ($reparto !== '' ? '<span class="es-job-dept">' . esc_html($reparto) . '</span>' : '')
            . ($rif !== '' ? '<span class="es-job-id">' . esc_html(sprintf(es_t('Rif. %s'), $rif)) . '</span>' : '')
            . '<span class="es-job-meta">' . es_job_meta($post->ID) . '</span>'
            . '<span class="es-job-footer"><span>' . esc_html(es_t('Dettagli')) . '</span><span class="es-job-arrow" aria-hidden="true">' . es_job_icon('freccia') . '</span></span>'
            . '</a></li>';
    }
    return $out . '</ul>';
});

// Scheda posizione: kicker "Reparto · Rif. XX" (tag dinamico shortcode nel paragrafo della testata).
add_shortcode('es_posizione_kicker', function () {
    $id = get_the_ID();
    $parts = array_filter([trim((string) get_field('reparto', $id)), ($rif = trim((string) get_field('rif', $id))) !== '' ? sprintf(es_t('Rif. %s'), $rif) : '']);
    return esc_html(implode(' · ', $parts));
});

// Scheda posizione: sede e contratto sotto il titolo.
add_shortcode('es_posizione_meta', function () {
    $meta = es_job_meta(get_the_ID());
    return $meta !== '' ? '<div class="es-job-meta-strip">' . $meta . '</div>' : '';
});

// Scheda posizione: testo introduttivo ("La posizione") e sezioni a elenco; le sezioni vuote non compaiono.
add_shortcode('es_posizione_corpo', function () {
    $id = get_the_ID();
    $out = '';
    $lead = trim(wp_strip_all_tags((string) get_field('la_posizione', $id)));
    if ($lead !== '') {
        $out .= '<p class="es-job-lead">' . esc_html($lead) . '</p>';
    }
    $sezioni = '';
    foreach (['attivita' => 'Attività principali', 'requisiti' => 'Requisiti richiesti', 'requisiti_preferenziali' => 'Requisiti preferenziali', 'cosa_offriamo' => 'Cosa offriamo'] as $campo => $titolo) {
        $voci = array_filter(array_map(fn($r) => trim((string) ($r['voce'] ?? '')), get_field($campo, $id) ?: []));
        if (!$voci) {
            continue;
        }
        $sezioni .= '<h3>' . esc_html(es_t($titolo)) . '</h3><ul>';
        foreach ($voci as $voce) {
            $sezioni .= '<li>' . esc_html($voce) . '</li>';
        }
        $sezioni .= '</ul>';
    }
    return $out . ($sezioni !== '' ? '<div class="es-job-section">' . $sezioni . '</div>' : '');
});
