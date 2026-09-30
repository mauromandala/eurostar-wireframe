
// === Ricerca ===

add_action('init', function () {
    if (get_option('es_ricerca_strings') === 'r1') {
        return;
    }
    foreach (['Ricerca', 'Cerca nel sito','Risultati per «%s»', 'Nessun risultato', '1 risultato', '%d risultati',
        'Scrivi il nome di una macchina, un settore o un argomento.', 'Cerca', 'Tipi di risultato',
        'Macchine', 'Settori e categorie', 'Pagine', 'News', 'Posizioni aperte',
        'Settore', 'Categoria di macchine', 'Pagina', 'Posizione aperta',
        'Nessun risultato per «%s»', 'Da dove vuoi iniziare?',
        'Controlla di aver scritto bene o prova con un termine più generico: il nome di una macchina (per esempio MEC LD), un settore (vino, olio) o un tipo di macchina (riempitrice).',
        'Macchine per categoria', 'Settori', 'Vai al catalogo macchine'] as $s) {
        do_action('wpml_register_single_string', 'Eurostar template', $s, $s, false, 'it');
    }
    update_option('es_ricerca_strings', 'r1', false);
}, 20);

if (!function_exists('es_ricerca_norm')) {
    // Testo confrontabile: minuscole, senza accenti né tag, spazi compattati.
    function es_ricerca_norm($s)
    {
        return trim(preg_replace('/\s+/u', ' ', mb_strtolower(remove_accents(wp_strip_all_tags((string) $s)))));
    }
}

if (!function_exists('es_ricerca_parole')) {
    // Parole della ricerca senza articoli e preposizioni (IT/EN) e senza parole di una lettera.
    function es_ricerca_parole($q)
    {
        $stop = ['di', 'a', 'da', 'in', 'con', 'su', 'per', 'tra', 'fra', 'il', 'lo', 'la', 'i', 'gli', 'le', 'un', 'uno', 'una', 'e', 'o', 'ed',
            'del', 'dello', 'della', 'dei', 'degli', 'delle', 'al', 'allo', 'alla', 'ai', 'agli', 'alle', 'nel', 'nella', 'nei', 'nelle',
            'the', 'an', 'of', 'for', 'and', 'or', 'to', 'with', 'on', 'at', 'by'];
        $words = preg_split('/[\s,;:.\/\-–—"«»()]+/u', es_ricerca_norm($q), -1, PREG_SPLIT_NO_EMPTY);
        return array_values(array_unique(array_filter($words, fn($w) => mb_strlen($w) > 1 && !in_array($w, $stop, true))));
    }
}

if (!function_exists('es_ricerca_testo_elementor')) {
    // Testi visibili di una pagina Elementor (titoli, paragrafi, pulsanti), letti dai dati: molte pagine hanno post_content vuoto.
    function es_ricerca_testo_elementor($post_id)
    {
        $data = json_decode((string) get_post_meta($post_id, '_elementor_data', true), true);
        if (!is_array($data)) {
            return '';
        }
        $out = [];
        $walk = function ($els) use (&$walk, &$out) {
            foreach ($els as $e) {
                foreach (['title', 'paragraph', 'text', 'editor', 'title_text', 'description_text'] as $k) {
                    $v = $e['settings'][$k] ?? null;
                    if (is_array($v) && isset($v['value']) && is_string($v['value'])) {
                        $v = $v['value'];
                    }
                    if (is_string($v) && $v !== '') {
                        $out[] = wp_strip_all_tags($v);
                    }
                }
                if (!empty($e['elements'])) {
                    $walk($e['elements']);
                }
            }
        };
        $walk($data);
        return implode(' ', $out);
    }
}

if (!function_exists('es_ricerca_indice')) {
    // Indice dei contenuti cercabili nella lingua corrente: [tipo, id, titolo, testo] (titolo e testo già normalizzati).
    // In cache per lingua, svuotata quando si salva un contenuto, un termine o una pagina opzioni.
    function es_ricerca_indice()
    {
        $lang = (string) apply_filters('wpml_current_language', 'it');
        $key = 'es_ricerca_' . $lang;
        $idx = get_transient($key);
        if (is_array($idx)) {
            return $idx;
        }
        $idx = [];
        $q = ['post_status' => 'publish', 'numberposts' => -1, 'suppress_filters' => false];
        $names = fn($id, $tax) => implode(' ', wp_list_pluck(get_the_terms($id, $tax) ?: [], 'name'));
        $ordine = ['orderby' => ['menu_order' => 'ASC', 'title' => 'ASC']];
        // Macchine: prima la linea Eurostar, poi Squadron, ciascuna nell'ordine del campo Ordine.
        $macchine = get_posts($q + $ordine + ['post_type' => 'macchina']);
        $squadron = fn($p) => function_exists('es_term_slug_it') && es_term_slug_it($p->ID, 'linea') === 'squadron';
        usort($macchine, fn($a, $b) => [$squadron($a), $a->menu_order, $a->post_title] <=> [$squadron($b), $b->menu_order, $b->post_title]);
        foreach ($macchine as $p) {
            $t = ['macchina macchine machine machines', $names($p->ID, 'categoria_macchina'), $names($p->ID, 'settore'), $names($p->ID, 'linea')];
            foreach (['tipologia', 'descrizione', 'prodotto', 'prodotto_breve', 'contenitori', 'contenitori_ora', 'tecnologia_riempimento', 'tipologia_chiusura'] as $f) {
                $t[] = (string) get_post_meta($p->ID, $f, true);
            }
            $idx[] = ['macchina', $p->ID, es_ricerca_norm($p->post_title), es_ricerca_norm(implode(' ', $t))];
        }
        foreach (['settore' => 'settore settori sector sectors', 'categoria_macchina' => 'categoria macchine machine category'] as $tax => $extra) {
            $terms = get_terms(['taxonomy' => $tax, 'hide_empty' => false]);
            foreach (is_wp_error($terms) ? [] : $terms as $t) {
                $idx[] = [$tax, $t->term_id, es_ricerca_norm($t->name), es_ricerca_norm($extra . ' ' . get_term_meta($t->term_id, 'intro', true) . ' ' . $t->description)];
            }
        }
        $escluse = array_filter([(int) get_option('page_on_front')]);
        foreach (['conferma', 'sample-page'] as $slug) {
            if ($pg = get_page_by_path($slug)) {
                $escluse[] = (int) apply_filters('wpml_object_id', $pg->ID, 'page', true);
            }
        }
        foreach (get_posts($q + $ordine + ['post_type' => 'page']) as $p) {
            if (in_array($p->ID, $escluse, true)) {
                continue;
            }
            $idx[] = ['page', $p->ID, es_ricerca_norm($p->post_title), es_ricerca_norm(es_ricerca_testo_elementor($p->ID) . ' ' . $p->post_content)];
        }
        foreach (get_posts($q + ['post_type' => 'post', 'category__not_in' => [(int) get_option('default_category')]]) as $p) {
            $idx[] = ['post', $p->ID, es_ricerca_norm($p->post_title), es_ricerca_norm($p->post_excerpt . ' ' . $p->post_content . ' ' . $names($p->ID, 'category'))];
        }
        foreach (get_posts($q + ['post_type' => 'posizione_lavoro']) as $p) {
            $t = [];
            $campi = get_fields($p->ID) ?: [];
            array_walk_recursive($campi, function ($v) use (&$t) {
                if (is_string($v)) {
                    $t[] = $v;
                }
            });
            $idx[] = ['posizione_lavoro', $p->ID, es_ricerca_norm($p->post_title), es_ricerca_norm(implode(' ', $t))];
        }
        set_transient($key, $idx, DAY_IN_SECONDS);
        return $idx;
    }
}

$es_ricerca_svuota = function () {
    foreach (array_keys((array) apply_filters('wpml_active_languages', null, [])) ?: ['it'] as $l) {
        delete_transient('es_ricerca_' . $l);
    }
};
foreach (['save_post', 'deleted_post', 'trashed_post', 'created_term', 'edited_term', 'delete_term', 'acf/save_post'] as $hook) {
    add_action($hook, $es_ricerca_svuota);
}

if (!function_exists('es_ricerca')) {
    // Risultati della ricerca corrente per gruppo: ['macchine' => [id…], 'termini' => [[tax, id]…], 'pagine', 'news', 'posizioni'].
    // Ogni parola deve comparire nel titolo o nel testo (parole lunghe anche senza l'ultima lettera: riempitrice → riempitrici);
    // ordine: più parole nel titolo prima, poi l'ordine dell'indice (macchine per Ordine, news dalla più recente).
    function es_ricerca()
    {
        static $res = null;
        if ($res !== null) {
            return $res;
        }
        $res = ['q' => trim((string) get_search_query(false)), 'macchine' => [], 'termini' => [], 'pagine' => [], 'news' => [], 'posizioni' => [], 'totale' => 0];
        $parole = es_ricerca_parole($res['q']);
        if (!$parole) {
            return $res;
        }
        $trovati = [];
        foreach (es_ricerca_indice() as $n => [$tipo, $id, $titolo, $testo]) {
            $punti = 0;
            foreach ($parole as $w) {
                $varianti = mb_strlen($w) >= 5 ? [$w, mb_substr($w, 0, -1)] : [$w];
                $in_titolo = $in_testo = false;
                foreach ($varianti as $v) {
                    $in_titolo = $in_titolo || str_contains($titolo, $v);
                    $in_testo = $in_testo || str_contains($testo, $v);
                }
                if (!$in_titolo && !$in_testo) {
                    continue 2;
                }
                $punti += $in_titolo ? 10 : 1;
            }
            $trovati[] = [$punti, $n, $tipo, $id];
        }
        usort($trovati, fn($a, $b) => [$b[0], $a[1]] <=> [$a[0], $b[1]]);
        $gruppo = ['macchina' => 'macchine', 'settore' => 'termini', 'categoria_macchina' => 'termini', 'page' => 'pagine', 'post' => 'news', 'posizione_lavoro' => 'posizioni'];
        foreach ($trovati as [, , $tipo, $id]) {
            $res[$gruppo[$tipo]][] = $gruppo[$tipo] === 'termini' ? [$tipo, $id] : $id;
            $res['totale']++;
        }
        return $res;
    }
}

// Loop delle card macchina nel template dei risultati: solo le macchine trovate, nell'ordine dei risultati.
add_action('elementor/query/es_ricerca_macchine', function ($query) {
    $ids = es_ricerca()['macchine'];
    $query->set('s', '');
    $query->set('post_type', 'macchina');
    $query->set('post__in', $ids ?: [0]);
    $query->set('orderby', 'post__in');
    $query->set('nopaging', true);
    $query->set('posts_per_page', 200);
    $query->set('paged', 1);
});

if (!function_exists('es_ricerca_etichette')) {
    function es_ricerca_etichette()
    {
        return ['macchine' => es_t('Macchine'), 'termini' => es_t('Settori e categorie'), 'pagine' => es_t('Pagine'), 'news' => es_t('News'), 'posizioni' => es_t('Posizioni aperte')];
    }
}

if (!function_exists('es_ricerca_conteggio')) {
    function es_ricerca_conteggio($n)
    {
        return $n === 0 ? es_t('Nessun risultato') : ($n === 1 ? es_t('1 risultato') : sprintf(es_t('%d risultati'), $n));
    }
}

// Testata: titolo e sommario (tag dinamici nei widget atomic).
add_shortcode('es_ricerca_titolo', function () {
    $q = es_ricerca()['q'];
    return esc_html($q === '' ? es_t('Cerca nel sito') : sprintf(es_t('Risultati per «%s»'), $q));
});

add_shortcode('es_ricerca_sommario', function () {
    $r = es_ricerca();
    return esc_html($r['q'] === '' ? es_t('Scrivi il nome di una macchina, un settore o un argomento.') : es_ricerca_conteggio($r['totale']));
});

// Testata: campo per una nuova ricerca e, con più tipi di risultato, i link ai gruppi.
add_shortcode('es_ricerca_form', function () {
    $r = es_ricerca();
    $out = '<form role="search" class="es-sr-form" method="get" action="' . esc_url(apply_filters('wpml_home_url', home_url('/'))) . '">'
        . '<label for="es-sr-input" class="es-sr-label">' . esc_html(es_t('Cerca nel sito')) . '</label>'
        . '<div class="es-sr-field"><input id="es-sr-input" type="search" name="s" value="' . esc_attr($r['q']) . '" autocomplete="off">'
        . '<button type="submit">' . esc_html(es_t('Cerca')) . '</button></div></form>';
    $gruppi = array_filter(es_ricerca_etichette(), fn($k) => !empty($r[$k]), ARRAY_FILTER_USE_KEY);
    if (count($gruppi) > 1) {
        $out .= '<nav class="es-sr-jump" aria-label="' . esc_attr(es_t('Tipi di risultato')) . '"><ul role="list">';
        foreach ($gruppi as $k => $label) {
            $out .= '<li><a href="#es-sr-' . $k . '">' . esc_html($label) . ' <span>' . count($r[$k]) . '</span></a></li>';
        }
        $out .= '</ul></nav>';
    }
    return $out;
});

if (!function_exists('es_ricerca_titolo_gruppo')) {
    function es_ricerca_titolo_gruppo($k)
    {
        $n = count(es_ricerca()[$k]);
        return '<h2 class="es-sr-title">' . esc_html(es_ricerca_etichette()[$k]) . ' <span class="es-sr-count">' . esc_html(es_ricerca_conteggio($n)) . '</span></h2>';
    }
}

// Titolo del gruppo Macchine (sopra il loop delle card, nel template).
add_shortcode('es_ricerca_gruppo', function ($atts = []) {
    $k = shortcode_atts(['tipo' => 'macchine'], $atts)['tipo'];
    return !empty(es_ricerca()[$k] ?? []) ? es_ricerca_titolo_gruppo($k) : '';
});

if (!function_exists('es_ricerca_estratto')) {
    // Circa 200 caratteri del testo attorno alla prima parola trovata, con le parole evidenziate.
    function es_ricerca_estratto($testo, $parole, $max = 200)
    {
        $testo = trim(preg_replace('/\s+/u', ' ', html_entity_decode(wp_strip_all_tags($testo), ENT_QUOTES, 'UTF-8')));
        if ($testo === '') {
            return '';
        }
        $pos = 0;
        foreach ($parole as $w) {
            if (preg_match('/' . preg_quote($w, '/') . '/iu', $testo, $m, PREG_OFFSET_CAPTURE)) {
                $pos = mb_strlen(substr($testo, 0, $m[0][1]));
                break;
            }
        }
        $start = max(0, $pos - 60);
        if ($start > 0 && ($sp = mb_strpos($testo, ' ', $start)) !== false && $sp < $pos) {
            $start = $sp + 1;
        }
        $pezzo = mb_substr($testo, $start, $max);
        if ($start + $max < mb_strlen($testo)) {
            $pezzo = preg_replace('/\s+\S*$/u', '', $pezzo) . '…';
        }
        $pezzo = ($start > 0 ? '…' : '') . $pezzo;
        if (!$parole) {
            return esc_html($pezzo);
        }
        // Evidenziazione sul testo non ancora codificato (i pezzi dispari sono le parole trovate), poi codifica di ogni pezzo.
        $re = implode('|', array_map(fn($w) => preg_quote($w, '/'), $parole));
        $html = '';
        foreach (preg_split('/(' . $re . ')/iu', $pezzo, -1, PREG_SPLIT_DELIM_CAPTURE) as $i => $s) {
            $html .= $i % 2 ? '<mark>' . esc_html($s) . '</mark>' : esc_html($s);
        }
        return $html;
    }
}

if (!function_exists('es_ricerca_riga')) {
    function es_ricerca_riga($url, $tipo, $titolo, $estratto)
    {
        return '<li><a class="es-sr-row" href="' . esc_url($url) . '"><span class="es-sr-type">' . esc_html($tipo) . '</span>'
            . '<h3 class="es-sr-row-title">' . esc_html($titolo) . '</h3>'
            . ($estratto !== '' ? '<p class="es-sr-excerpt">' . $estratto . '</p>' : '') . '</a></li>';
    }
}

if (!function_exists('es_ricerca_termini_ordinati')) {
    // Termini della tassonomia nell'ordine del campo "ordine" del termine italiano.
    function es_ricerca_termini_ordinati($tax)
    {
        $terms = get_terms(['taxonomy' => $tax, 'hide_empty' => false]);
        if (is_wp_error($terms)) {
            return [];
        }
        $ord = fn($t) => (int) get_term_meta((int) apply_filters('wpml_object_id', $t->term_id, $tax, true, 'it'), 'ordine', true);
        usort($terms, fn($a, $b) => [$ord($a), $a->name] <=> [$ord($b), $b->name]);
        return $terms;
    }
}

// Risultati (tranne le card macchina, che sono nel loop del template) oppure lo stato vuoto con i suggerimenti.
add_shortcode('es_ricerca_risultati', function () {
    $r = es_ricerca();
    $parole = es_ricerca_parole($r['q']);
    $out = '';
    if ($r['termini']) {
        $out .= '<div class="es-sr-group" id="es-sr-termini">' . es_ricerca_titolo_gruppo('termini') . '<ul class="es-sr-list" role="list">';
        foreach ($r['termini'] as [$tax, $id]) {
            $t = get_term($id, $tax);
            $intro = (string) get_term_meta($id, 'intro', true) ?: $t->description;
            $out .= es_ricerca_riga(get_term_link($t), $tax === 'settore' ? es_t('Settore') : es_t('Categoria di macchine'), $t->name, es_ricerca_estratto($intro, $parole));
        }
        $out .= '</ul></div>';
    }
    if ($r['pagine']) {
        $out .= '<div class="es-sr-group" id="es-sr-pagine">' . es_ricerca_titolo_gruppo('pagine') . '<ul class="es-sr-list" role="list">';
        foreach ($r['pagine'] as $id) {
            $out .= es_ricerca_riga(get_permalink($id), es_t('Pagina'), get_the_title($id), es_ricerca_estratto(es_ricerca_testo_elementor($id) ?: get_post_field('post_content', $id), $parole));
        }
        $out .= '</ul></div>';
    }
    if ($r['news']) {
        $out .= '<div class="es-sr-group" id="es-sr-news">' . es_ricerca_titolo_gruppo('news') . '<ul class="es-news-grid" role="list">';
        foreach ($r['news'] as $id) {
            $out .= '<li>' . es_news_card(get_post($id), 'h3') . '</li>';
        }
        $out .= '</ul></div>';
    }
    if ($r['posizioni']) {
        $out .= '<div class="es-sr-group" id="es-sr-posizioni">' . es_ricerca_titolo_gruppo('posizioni') . '<ul class="es-sr-list" role="list">';
        foreach ($r['posizioni'] as $id) {
            $out .= es_ricerca_riga(get_permalink($id), es_t('Posizione aperta'), get_the_title($id), es_ricerca_estratto((string) get_field('la_posizione', $id), $parole));
        }
        $out .= '</ul></div>';
    }
    if ($r['totale'] > 0) {
        return '<div class="es-sr-results">' . $out . '</div>';
    }
    // Stato vuoto (nessun risultato o ricerca senza parole): suggerimenti per categoria e settore, link al catalogo.
    $liste = '';
    foreach (['categoria_macchina' => es_t('Macchine per categoria'), 'settore' => es_t('Settori')] as $tax => $label) {
        $terms = es_ricerca_termini_ordinati($tax);
        if (!$terms) {
            continue;
        }
        $liste .= '<div><h3 class="es-sr-suggest-title">' . esc_html($label) . '</h3><ul role="list">';
        foreach ($terms as $t) {
            $liste .= '<li><a href="' . esc_url(get_term_link($t)) . '">' . esc_html($t->name) . '</a></li>';
        }
        $liste .= '</ul></div>';
    }
    return '<div class="es-sr-empty">'
        . '<h2 class="es-sr-title">' . esc_html($r['q'] === '' ? es_t('Da dove vuoi iniziare?') : sprintf(es_t('Nessun risultato per «%s»'), $r['q'])) . '</h2>'
        . ($r['q'] === '' ? '' : '<p class="es-sr-hint">' . esc_html(es_t('Controlla di aver scritto bene o prova con un termine più generico: il nome di una macchina (per esempio MEC LD), un settore (vino, olio) o un tipo di macchina (riempitrice).')) . '</p>')
        . ($liste !== '' ? '<div class="es-sr-suggest">' . $liste . '</div>' : '')
        . '<p class="es-sr-cta-row"><a class="es-sr-cta" href="' . esc_url(get_post_type_archive_link('macchina')) . '">' . esc_html(es_t('Vai al catalogo macchine')) . '</a></p></div>';
});
// === fine Ricerca ===
