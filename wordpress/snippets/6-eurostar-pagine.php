<?php
// <Internal Doc Start>
/*
*
* @description: Pagine Contatti, Conferma, Settori e Servizi: recapiti [es_contatti_info], frase urgenze [es_conferma_urgenze], griglia dei settori [es_settori_griglia], recapiti post-vendita [es_aftersales], [es_aftersales_cta], clienti [es_clienti]; Home: nastro clienti [es_clienti_nastro], news [es_news_nav] [es_news_home]; Cataloghi: righe dei PDF [es_cataloghi]; Lavora con noi: card [es_posizioni], scheda posizione [es_posizione_kicker] [es_posizione_meta] [es_posizione_corpo]; News: archivio [es_news_filtri] [es_news_griglia], articolo [es_articolo_meta] [es_articolo_immagine] [es_articolo_corpo] [es_articolo_correlati], caso studio [es_caso_meta] [es_caso_aside].
* @tags: eurostar, pagine
* @group: 
* @name: Eurostar pagine
* @type: PHP
* @status: published
* @created_by: 1
* @created_at: 2026-09-29 07:05:57
* @updated_at: 2026-09-29 13:27:05
* @is_valid: 1
* @updated_by: 1
* @priority: 15
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

// Pagine (Contatti, Conferma, Settori, Servizi): recapiti in un solo punto e frasi con link.
// Etichette in WPML String Translation, contesto "Eurostar template".

if (!function_exists('es_t')) {
    function es_t($text)
    {
        return apply_filters('wpml_translate_single_string', $text, 'Eurostar template', $text);
    }
}

if (!function_exists('es_recapiti')) {
    function es_recapiti()
    {
        return [
            'indirizzo' => ['Regione Leiso, 86', '14050 San Marzano Oliveto (AT), Italia'],
            'telefono' => ['+39 0141 856032', '+390141856032'],
            'email' => 'eurostarinfo@eurostar.it',
            'aftersales' => 'aftersales@eurostar.it',
            'whatsapp' => ['+39 345 345 0371', '393453450371'],
        ];
    }
}

add_action('init', function () {
    if (get_option('es_pagine_strings') === 'p2') {
        return;
    }
    foreach (['14050 San Marzano Oliveto (AT), Italia', 'WhatsApp', 'Per urgenze puoi contattarci anche telefonicamente allo %s.', 'Oppure scrivi direttamente a %s'] as $s) {
        do_action('wpml_register_single_string', 'Eurostar template', $s, $s, false, 'it');
    }
    update_option('es_pagine_strings', 'p2', false);
}, 20);

// Contatti: indirizzo, telefono, email, WhatsApp.
add_shortcode('es_contatti_info', function () {
    $r = es_recapiti();
    return '<div class="es-contact-info">'
        . '<p>' . esc_html($r['indirizzo'][0]) . '<br>' . esc_html(es_t($r['indirizzo'][1])) . '</p>'
        . '<p><a href="tel:' . esc_attr($r['telefono'][1]) . '">' . esc_html($r['telefono'][0]) . '</a></p>'
        . '<p><a href="mailto:' . esc_attr($r['email']) . '">' . esc_html($r['email']) . '</a></p>'
        . '<p>' . esc_html(es_t('WhatsApp')) . ': <a href="https://wa.me/' . esc_attr($r['whatsapp'][1]) . '" target="_blank" rel="noopener">'
        . esc_html($r['whatsapp'][0]) . '</a></p>'
        . '</div>';
});

// Conferma: "Per urgenze puoi contattarci anche telefonicamente allo +39 …".
add_shortcode('es_conferma_urgenze', function () {
    $r = es_recapiti();
    $link = '<a href="tel:' . esc_attr($r['telefono'][1]) . '">' . esc_html($r['telefono'][0]) . '</a>';
    return '<p class="es-conferma-urgenze">' . sprintf(esc_html(es_t('Per urgenze puoi contattarci anche telefonicamente allo %s.')), $link) . '</p>';
});

// Settori: card dei termini "settore" della lingua corrente, nell'ordine del campo Ordine.
// Immagine = campo "immagine" del termine (se manca nella traduzione, quella del termine italiano);
// senza immagine resta il fondo Gray 150 pieno, come le card macchina.
add_shortcode('es_settori_griglia', function () {
    $terms = get_terms(['taxonomy' => 'settore', 'hide_empty' => false]);
    if (is_wp_error($terms) || !$terms) {
        return '';
    }
    usort($terms, function ($a, $b) {
        return ((int) get_term_meta($a->term_id, 'ordine', true) <=> (int) get_term_meta($b->term_id, 'ordine', true))
            ?: strcmp($a->name, $b->name);
    });
    $out = '<ul class="es-sectors-grid" role="list">';
    foreach ($terms as $term) {
        $image = get_field('immagine', $term);
        if (!$image) {
            $it = apply_filters('wpml_object_id', $term->term_id, 'settore', false, 'it');
            $image = $it && $it !== $term->term_id ? get_field('immagine', 'settore_' . $it) : null;
        }
        $media = $image
            ? wp_get_attachment_image($image['ID'], 'large', false, [
                'alt' => '',
                'loading' => 'lazy',
                'sizes' => '(max-width: 560px) 100vw, (max-width: 900px) 50vw, 25vw',
            ])
            : '';
        $out .= '<li><a class="es-sec" href="' . esc_url(get_term_link($term)) . '">'
            . '<span class="es-sec-media" aria-hidden="true">' . $media . '</span>'
            . '<span class="es-sec-name">' . esc_html($term->name) . '</span></a></li>';
    }
    return $out . '</ul>';
});

// Servizi, card post-vendita: "Richiedi assistenza · aftersales@… · +39 …" con email e telefono cliccabili.
// L'azione è un attributo (entra nel pacchetto WPML della pagina); telefono="1" aggiunge il numero.
add_shortcode('es_aftersales', function ($atts = []) {
    $atts = shortcode_atts(['azione' => '', 'telefono' => ''], $atts);
    $r = es_recapiti();
    $parts = [];
    if ($atts['azione'] !== '') {
        $parts[] = esc_html($atts['azione']);
    }
    $parts[] = '<a href="mailto:' . esc_attr($r['aftersales']) . '">' . esc_html($r['aftersales']) . '</a>';
    if ($atts['telefono']) {
        $parts[] = '<a href="tel:' . esc_attr($r['telefono'][1]) . '">' . esc_html($r['telefono'][0]) . '</a>';
    }
    return '<p class="es-aftersales">' . implode(' · ', $parts) . '</p>';
});

// Servizi, invito finale: "Oppure scrivi direttamente a aftersales@…".
add_shortcode('es_aftersales_cta', function () {
    $r = es_recapiti();
    $link = '<a href="mailto:' . esc_attr($r['aftersales']) . '">' . esc_html($r['aftersales']) . '</a>';
    return '<p class="es-aftersales-cta">' . sprintf(esc_html(es_t('Oppure scrivi direttamente a %s')), $link) . '</p>';
});

// Clienti e Cataloghi: un solo elenco per tutte le lingue. ACF Multilingual aggiungerebbe la lingua all'ID delle opzioni
// (es_clienti_en) e in inglese l'elenco risulterebbe vuoto: lettura e salvataggio restano su es_clienti.
add_filter('acf/validate_post_id', function ($post_id, $original) {
    return in_array($original, ['es_clienti', 'es_cataloghi'], true) ? $original : $post_id;
}, 99, 2);

// Referenze: tessere dei clienti dalla pagina opzioni ACF "Clienti" (post_id es_clienti, repeater clienti: nome, logo).
// Con il logo caricato mostra l'immagine (alt = nome), altrimenti il nome come nel wireframe.
add_shortcode('es_clienti', function () {
    $clienti = function_exists('get_field') ? get_field('clienti', 'es_clienti') : [];
    if (!$clienti) {
        return '';
    }
    $out = '<ul class="es-client-grid" role="list">';
    foreach ($clienti as $cliente) {
        $nome = trim($cliente['nome'] ?? '');
        if ($nome === '') {
            continue;
        }
        $logo = !empty($cliente['logo'])
            ? wp_get_attachment_image((int) $cliente['logo'], 'medium', false, ['alt' => $nome, 'loading' => 'lazy', 'class' => 'es-logo-img'])
            : '';
        $out .= '<li class="es-logo-tile">' . ($logo ?: esc_html($nome)) . '</li>';
    }
    return $out . '</ul>';
});

// === Home ===

add_action('init', function () {
    if (get_option('es_home_strings') === 'h1') {
        return;
    }
    foreach (['Metti in pausa lo scorrimento dei loghi', 'Riprendi lo scorrimento dei loghi', 'News precedenti', 'News successive', 'Ultime news'] as $s) {
        do_action('wpml_register_single_string', 'Eurostar template', $s, $s, false, 'it');
    }
    update_option('es_home_strings', 'h1', false);
}, 20);

// Home, nastro clienti: stessa fonte di Referenze (pagina opzioni "Clienti"), scorrimento continuo con copia aria-hidden,
// pausa al passaggio del mouse e pulsante pausa/play (WCAG 2.2.2). Con il logo caricato mostra l'immagine (alt = nome).
add_shortcode('es_clienti_nastro', function () {
    $clienti = function_exists('get_field') ? get_field('clienti', 'es_clienti') : [];
    if (!$clienti) {
        return '';
    }
    $items = '';
    foreach ($clienti as $cliente) {
        $nome = trim($cliente['nome'] ?? '');
        if ($nome === '') {
            continue;
        }
        $logo = !empty($cliente['logo'])
            ? wp_get_attachment_image((int) $cliente['logo'], 'medium', false, ['alt' => $nome, 'loading' => 'lazy', 'class' => 'es-logo-img'])
            : '';
        $items .= '<li class="es-logo">' . ($logo ?: esc_html($nome)) . '</li>';
    }
    $pausa = es_t('Metti in pausa lo scorrimento dei loghi');
    $riprendi = es_t('Riprendi lo scorrimento dei loghi');
    $out = '<div class="es-marquee-wrap">'
        . '<div class="es-marquee"><div class="es-marquee-track">'
        . '<ul class="es-marquee-list" role="list">' . $items . '</ul>'
        . '<ul class="es-marquee-list" aria-hidden="true">' . preg_replace('/ alt="[^"]*"/', ' alt=""', $items) . '</ul>'
        . '</div></div>'
        . '<button type="button" class="es-marquee-pause" aria-pressed="false" aria-label="' . esc_attr($pausa) . '"'
        . ' data-label-pausa="' . esc_attr($pausa) . '" data-label-riprendi="' . esc_attr($riprendi) . '">'
        . '<svg class="es-ico-pause" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>'
        . '<svg class="es-ico-play" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>'
        . '</button></div>';
    static $script = false;
    if (!$script) {
        $script = true;
        $out .= "<script>document.addEventListener('click',function(e){var b=e.target.closest&&e.target.closest('.es-marquee-pause');if(!b)return;"
            . "var t=b.parentNode.querySelector('.es-marquee-track');var p=t.classList.toggle('is-paused');"
            . "b.setAttribute('aria-pressed',p?'true':'false');b.setAttribute('aria-label',p?b.dataset.labelRiprendi:b.dataset.labelPausa);});</script>";
    }
    return $out;
});

// Home, news: ultimi articoli con una categoria (esclusa quella predefinita "Senza categoria").
// Senza articoli lo shortcode non stampa nulla e il CSS del Kit nasconde l'intera sezione.
if (!function_exists('es_news_home_query')) {
    function es_news_home_query()
    {
        static $q = null;
        if ($q === null) {
            $q = new WP_Query([
                'post_type' => 'post',
                'post_status' => 'publish',
                'posts_per_page' => 6,
                'ignore_sticky_posts' => true,
                'category__not_in' => [(int) get_option('default_category')],
                'no_found_rows' => true,
            ]);
        }
        return $q;
    }
}

add_shortcode('es_news_nav', function () {
    if (!es_news_home_query()->have_posts()) {
        return '';
    }
    $arrow = function ($d) {
        return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="' . $d . '"/></svg>';
    };
    return '<div class="es-news-navs">'
        . '<button type="button" class="es-news-nav" data-dir="-1" aria-controls="es-news-track" aria-label="' . esc_attr(es_t('News precedenti')) . '">' . $arrow('M15 18l-6-6 6-6') . '</button>'
        . '<button type="button" class="es-news-nav" data-dir="1" aria-controls="es-news-track" aria-label="' . esc_attr(es_t('News successive')) . '">' . $arrow('M9 18l6-6-6-6') . '</button>'
        . '</div>';
});

add_shortcode('es_news_home', function () {
    $q = es_news_home_query();
    if (!$q->have_posts()) {
        return '';
    }
    // Card uguale a quella dell'archivio News (etichetta "Caso studio" al singolare per i casi studio)
    $out = '<div class="es-news-track" id="es-news-track" role="region" aria-label="' . esc_attr(es_t('Ultime news')) . '" tabindex="0">';
    foreach ($q->posts as $post) {
        $out .= es_news_card($post, 'h3');
    }
    $out .= '</div>';
    // Contatore a pallini: uno per ogni posizione delle frecce (4 con 3 card visibili, 6 su mobile), costruito dallo script
    $out .= '<div class="es-news-dots" role="group" aria-label="' . esc_attr(es_t('Posizione nel carosello')) . '"'
        . ' data-label="' . esc_attr(es_t('News %1$d–%2$d di %3$d')) . '" data-label-one="' . esc_attr(es_t('News %1$d di %3$d')) . '" hidden></div>';
    // Frecce (una card alla volta, attenuate all'inizio e alla fine) e pallini (aria-current sulla posizione attuale)
    $out .= '<script>' . '(function(){var t=document.getElementById(\'es-news-track\');if(!t)return;
var dots=document.querySelector(\'.es-news-dots\'),navs=[].slice.call(document.querySelectorAll(\'.es-news-nav[aria-controls="es-news-track"]\')),cards=[].slice.call(t.querySelectorAll(\'.es-art\'));
var r=window.matchMedia&&window.matchMedia(\'(prefers-reduced-motion: reduce)\').matches;
function step(){return cards.length?cards[0].getBoundingClientRect().width+24:t.clientWidth;}
function total(){return Math.max(1,Math.round((t.scrollWidth-t.clientWidth)/step())+1);}
function cur(){return Math.min(total()-1,Math.round(t.scrollLeft/step()));}
function go(i){t.scrollTo({left:i*step(),behavior:r?\'auto\':\'smooth\'});}
function update(){var i=cur(),n=total();if(dots)[].forEach.call(dots.children,function(b,k){b.setAttribute(\'aria-current\',k===i?\'true\':\'false\');});navs.forEach(function(b){var end=b.dataset.dir===\'1\'?i>=n-1:i<=0;b.setAttribute(\'aria-disabled\',end?\'true\':\'false\');});}
function build(){if(!dots)return update();var n=total(),vis=cards.length-n+1;dots.innerHTML=\'\';dots.hidden=n<2;for(var k=0;k<n;k++){var b=document.createElement(\'button\');b.type=\'button\';b.className=\'es-news-dot\';b.setAttribute(\'aria-controls\',\'es-news-track\');var l=vis>1?dots.dataset.label:dots.dataset.labelOne;b.setAttribute(\'aria-label\',l.replace(\'%1$d\',k+1).replace(\'%2$d\',k+vis).replace(\'%3$d\',cards.length));(function(k){b.addEventListener(\'click\',function(){go(k);});})(k);dots.appendChild(b);}update();}
navs.forEach(function(b){b.addEventListener(\'click\',function(){if(b.getAttribute(\'aria-disabled\')===\'true\')return;go(Math.max(0,Math.min(total()-1,cur()+parseInt(b.dataset.dir,10))));});});
var tm;t.addEventListener(\'scroll\',function(){clearTimeout(tm);tm=setTimeout(update,60);},{passive:true});
window.addEventListener(\'resize\',build);build();})();' . '</script>';
    return $out;
});

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

// === News ===

add_action('init', function () {
    if (get_option('es_news_strings') === 'n1') {
        return;
    }
    foreach (['Categorie news', 'Tutte', 'Caso studio', '%d min di lettura', 'Mostra altri articoli', 'Nessun articolo in questa categoria.',
        'Nessun articolo pubblicato.', '1 articolo', '%d articoli', 'Settore · %s', 'Installazione · %s', 'La voce del cliente',
        'Macchine nel progetto', 'Fonte: %s'] as $s) {
        do_action('wpml_register_single_string', 'Eurostar template', $s, $s, false, 'it');
    }
    update_option('es_news_strings', 'n1', false);
}, 20);

if (!function_exists('es_news_categorie')) {
    // Categorie delle news nell'ordine del campo "ordine" (esclusa la categoria predefinita "Senza categoria").
    function es_news_categorie($solo_con_articoli = true)
    {
        $terms = get_terms(['taxonomy' => 'category', 'hide_empty' => $solo_con_articoli, 'exclude' => [(int) get_option('default_category')]]);
        if (is_wp_error($terms)) {
            return [];
        }
        usort($terms, fn($a, $b) => ((int) get_term_meta($a->term_id, 'ordine', true) <=> (int) get_term_meta($b->term_id, 'ordine', true)) ?: strcmp($a->name, $b->name));
        return $terms;
    }
}

if (!function_exists('es_news_categoria')) {
    // Categoria principale dell'articolo (la prima diversa da "Senza categoria").
    function es_news_categoria($post_id)
    {
        foreach (get_the_category($post_id) as $c) {
            if ((int) $c->term_id !== (int) get_option('default_category')) {
                return $c;
            }
        }
        return null;
    }
}

if (!function_exists('es_news_is_caso')) {
    function es_news_is_caso($post_id)
    {
        $c = es_news_categoria($post_id);
        return $c && apply_filters('wpml_object_id', $c->term_id, 'category', true, 'it') == (get_term_by('slug', 'casi-studio', 'category')->term_id ?? 0);
    }
}

if (!function_exists('es_news_etichetta')) {
    // Etichetta della card: "Caso studio" al singolare per i casi studio, altrimenti il nome della categoria.
    function es_news_etichetta($post_id)
    {
        $c = es_news_categoria($post_id);
        if (!$c) {
            return '';
        }
        return es_news_is_caso($post_id) ? es_t('Caso studio') : $c->name;
    }
}

if (!function_exists('es_news_card')) {
    // Card articolo (stessa struttura del carosello in Home). $titolo_tag: h2 nell'archivio, h3 nei correlati;
    // $estratto: testo breve sotto il titolo; $settore: per i correlati dei casi studio l'etichetta è il settore.
    function es_news_card($post, $titolo_tag = 'h2', $estratto = true, $settore = false)
    {
        $img = has_post_thumbnail($post)
            ? get_the_post_thumbnail($post, 'medium_large', ['alt' => '', 'loading' => 'lazy', 'sizes' => '(max-width: 760px) 100vw, 33vw'])
            : '';
        $cat = es_news_categoria($post->ID);
        $label = $settore ? (string) get_field('settore', $post->ID) : es_news_etichetta($post->ID);
        $meta = '<div class="es-art-meta">' . ($label !== '' ? '<span>' . esc_html($label) . '</span>' : '')
            . ($settore ? '' : '<span class="es-art-date">' . esc_html(date_i18n('F Y', get_post_time('U', false, $post))) . '</span>') . '</div>';
        $testo = $estratto ? trim((string) $post->post_excerpt) : '';
        return '<a class="es-art" href="' . esc_url(get_permalink($post)) . '" data-cat="' . esc_attr($cat ? $cat->slug : '') . '">'
            . '<div class="es-art-frame" aria-hidden="true">' . $img . '</div>'
            . '<div class="es-art-body">' . $meta
            . '<' . $titolo_tag . ' class="es-art-title">' . esc_html(get_the_title($post)) . '</' . $titolo_tag . '>'
            . ($testo !== '' ? '<p class="es-art-excerpt">' . esc_html($testo) . '</p>' : '')
            . '</div></a>';
    }
}

// Archivio News, barra dei filtri (widget a sé sotto il main, come il catalogo, per restare sticky sotto l'header).
add_shortcode('es_news_filtri', function () {
    $terms = es_news_categorie();
    if (!$terms) {
        return '';
    }
    $out = '<div class="es-filterbar es-news-filterbar"><div class="es-filterbar-inner" role="group" aria-label="' . esc_attr(es_t('Categorie news')) . '">'
        . '<button type="button" class="es-filter" data-filter="all" aria-pressed="true">' . esc_html(es_t('Tutte')) . '</button>';
    foreach ($terms as $t) {
        $out .= '<button type="button" class="es-filter" data-filter="' . esc_attr($t->slug) . '" aria-pressed="false">' . esc_html($t->name) . '</button>';
    }
    return $out . '</div></div>';
});

// Archivio News, griglia: tutti gli articoli (esclusi quelli solo in "Senza categoria"), dal più recente.
// Ne mostra 12, "Mostra altri articoli" ne aggiunge 12 alla volta; con un filtro attivo si vedono tutti quelli della categoria.
// ?categoria=slug preseleziona un filtro (gli archivi di categoria di WordPress rimandano qui).
add_shortcode('es_news_griglia', function () {
    $posts = get_posts([
        'post_type' => 'post', 'post_status' => 'publish', 'numberposts' => -1, 'suppress_filters' => false,
        'category__not_in' => [(int) get_option('default_category')],
    ]);
    if (!$posts) {
        return '<p class="es-news-none">' . esc_html(es_t('Nessun articolo pubblicato.')) . '</p>';
    }
    $out = '<ul class="es-news-grid" role="list" id="es-news-grid">';
    foreach ($posts as $i => $p) {
        $out .= '<li' . ($i >= 12 ? ' hidden' : '') . '>' . es_news_card($p) . '</li>';
    }
    $out .= '</ul><p class="es-news-empty" hidden>' . esc_html(es_t('Nessun articolo in questa categoria.')) . '</p>'
        . '<p class="es-news-status screen-reader-text" role="status" aria-live="polite" data-one="' . esc_attr(es_t('1 articolo')) . '" data-many="' . esc_attr(es_t('%d articoli')) . '"></p>';
    if (count($posts) > 12) {
        $out .= '<div class="es-news-more-row"><button type="button" class="es-news-more" aria-controls="es-news-grid">' . esc_html(es_t('Mostra altri articoli')) . '</button></div>';
    }
    $out .= "<script>(function(){var grid=document.getElementById('es-news-grid');if(!grid)return;var bar=document.querySelector('.es-news-filterbar');"
        . "var items=[].slice.call(grid.children),more=document.querySelector('.es-news-more'),empty=document.querySelector('.es-news-empty'),status=document.querySelector('.es-news-status'),shown=12,filter='all';"
        . "function render(){var n=0;items.forEach(function(li){var a=li.firstElementChild,ok=filter==='all'||a.getAttribute('data-cat')===filter;var vis=ok&&(filter!=='all'||n<shown);if(ok)n++;li.hidden=!vis;});"
        . "if(more)more.parentNode.hidden=!(filter==='all'&&items.length>shown);empty.hidden=n>0;status.textContent=n===1?status.dataset.one:status.dataset.many.replace('%d',n);}"
        . "function stick(){var h=document.querySelector('.elementor-location-header');var top=0;if(h&&getComputedStyle(h).position==='sticky'){var u=h.querySelector('.es-header-top .es-util');top=h.offsetHeight-(u?u.offsetHeight:0);}if(bar)(bar.closest('.elementor-widget')||bar).style.top=Math.max(0,top)+'px';}"
        . "function select(f){filter=f;if(bar)[].forEach.call(bar.querySelectorAll('[data-filter]'),function(b){b.setAttribute('aria-pressed',b.getAttribute('data-filter')===f?'true':'false');});render();}"
        . "var strip=bar&&bar.querySelector('.es-filterbar-inner');function fades(){if(!strip)return;var max=strip.scrollWidth-strip.clientWidth;bar.classList.toggle('can-scroll-left',strip.scrollLeft>1);bar.classList.toggle('can-scroll-right',max>1&&strip.scrollLeft<max-1);}"
        . "if(bar){bar.addEventListener('click',function(e){var b=e.target.closest('[data-filter]');if(b){select(b.getAttribute('data-filter'));b.scrollIntoView({block:'nearest',inline:'nearest'});}});stick();window.addEventListener('load',stick);fades();strip.addEventListener('scroll',fades,{passive:true});window.addEventListener('resize',function(){stick();fades();});}"
        . "if(more)more.addEventListener('click',function(){var first=shown;shown+=12;render();var li=items[first];var a=li&&li.querySelector('a');if(a)a.focus();});"
        . "var q=new URLSearchParams(location.search).get('categoria');if(q&&bar&&bar.querySelector('[data-filter=\"'+q.replace(/[^a-z0-9-]/g,'')+'\"]'))select(q);else render();})();</script>";
    return $out;
});

// Gli archivi di categoria di WordPress (senza template) rimandano all'archivio News con il filtro preselezionato.
add_action('template_redirect', function () {
    if (!is_category()) {
        return;
    }
    $news = get_page_by_path('news');
    if (!$news) {
        return;
    }
    $url = get_permalink(apply_filters('wpml_object_id', $news->ID, 'page', true));
    wp_safe_redirect(add_query_arg('categoria', get_queried_object()->slug, $url), 302);
    exit;
});

// Articolo: categoria (link all'archivio filtrato), data, minuti di lettura (campo, altrimenti calcolati a 200 parole/minuto).
add_shortcode('es_articolo_meta', function () {
    $id = get_the_ID();
    $cat = es_news_categoria($id);
    $min = (int) get_field('tempo_lettura', $id);
    if (!$min) {
        $min = max(1, (int) round(str_word_count(wp_strip_all_tags(get_post_field('post_content', $id))) / 200));
    }
    return '<div class="es-news-meta">' . ($cat ? '<span class="es-news-meta-cat">' . esc_html(es_news_etichetta($id)) . '</span>' : '')
        . '<span>' . esc_html(get_the_date('F Y', $id)) . '</span>'
        . '<span>' . esc_html(sprintf(es_t('%d min di lettura'), $min)) . '</span></div>';
});

// Caso studio: data, settore, luogo dell'installazione.
add_shortcode('es_caso_meta', function () {
    $id = get_the_ID();
    $parts = [get_the_date('F Y', $id)];
    if ($s = trim((string) get_field('settore', $id))) {
        $parts[] = sprintf(es_t('Settore · %s'), $s);
    }
    if ($l = trim((string) get_field('luogo', $id))) {
        $parts[] = sprintf(es_t('Installazione · %s'), $l);
    }
    return '<div class="es-news-meta es-case-meta"><span>' . implode('</span><span>', array_map('esc_html', $parts)) . '</span></div>';
});

// Articolo e caso studio: immagine in evidenza 16:7 (riquadro Gray 150 se manca), con il testo alternativo della Libreria.
add_shortcode('es_articolo_immagine', function () {
    $id = get_the_ID();
    $img = has_post_thumbnail($id)
        ? get_the_post_thumbnail($id, 'full', ['loading' => 'eager', 'fetchpriority' => 'high', 'sizes' => '(max-width: 1440px) 100vw, 1360px'])
        : '';
    return '<div class="es-news-hero-img' . ($img ? '' : ' is-empty') . '"' . ($img ? '' : ' aria-hidden="true"') . '>' . $img . '</div>';
});

// Articolo e caso studio: testo dell'articolo, numeri in evidenza (casi studio) e fonte in piccolo in fondo.
add_shortcode('es_articolo_corpo', function () {
    $id = get_the_ID();
    $html = apply_filters('the_content', get_post_field('post_content', $id));
    $numeri = array_filter(get_field('numeri', $id) ?: [], fn($r) => trim((string) ($r['valore'] ?? '')) !== '');
    if ($numeri) {
        $html .= '<dl class="es-case-stats">';
        foreach ($numeri as $r) {
            $html .= '<div><dt>' . esc_html($r['etichetta'] ?? '') . '</dt><dd>' . esc_html($r['valore']) . '</dd></div>';
        }
        $html .= '</dl>';
    }
    if ($fonte = trim((string) get_field('fonte', $id))) {
        $html .= '<p class="es-news-fonte">' . esc_html(sprintf(es_t('Fonte: %s'), $fonte)) . '</p>';
    }
    return '<div class="es-news-body">' . $html . '</div>';
});

// Caso studio, colonna laterale: "La voce del cliente" e "Macchine nel progetto" (se compilati).
add_shortcode('es_caso_aside', function () {
    $id = get_the_ID();
    $out = '';
    $v = get_field('voce_cliente', $id) ?: [];
    if (!empty($v['testo'])) {
        $foto = !empty($v['foto']) ? wp_get_attachment_image((int) $v['foto'], 'thumbnail', false, ['alt' => '']) : '';
        $out .= '<figure class="es-case-voice"><p class="es-case-label">' . esc_html(es_t('La voce del cliente')) . '</p>'
            . '<blockquote><p>«' . esc_html($v['testo']) . '»</p></blockquote>'
            . '<figcaption><span class="es-case-avatar' . ($foto ? '' : ' is-empty') . '" aria-hidden="true">' . $foto . '</span>'
            . '<span><span class="es-case-name">' . esc_html($v['nome'] ?? '') . '</span><span class="es-case-role">' . esc_html($v['ruolo'] ?? '') . '</span></span></figcaption></figure>';
    }
    $macchine = array_filter((array) get_field('macchine', $id));
    if ($macchine) {
        $out .= '<div class="es-case-machines"><p class="es-case-label">' . esc_html(es_t('Macchine nel progetto')) . '</p><ul role="list">';
        foreach ($macchine as $mid) {
            $mid = apply_filters('wpml_object_id', (int) $mid, 'macchina', true);
            $thumb = has_post_thumbnail($mid) ? get_the_post_thumbnail($mid, 'thumbnail', ['alt' => '']) : '';
            $out .= '<li><a class="es-case-machine" href="' . esc_url(get_permalink($mid)) . '"><span class="es-case-thumb' . ($thumb ? '' : ' is-empty') . '" aria-hidden="true">' . $thumb . '</span>'
                . '<span class="es-case-machine-name">' . esc_html(get_the_title($mid)) . '</span></a></li>';
        }
        $out .= '</ul></div>';
    }
    return $out !== '' ? '<div class="es-case-aside">' . $out . '</div>' : '';
});

// Articolo e caso studio, "Continua a leggere": gli ultimi 3 dello stesso tipo (casi studio con casi studio, articoli con articoli).
add_shortcode('es_articolo_correlati', function () {
    $id = get_the_ID();
    $caso = es_news_is_caso($id);
    $casi = get_term_by('slug', 'casi-studio', 'category');
    $casi_id = $casi ? (int) apply_filters('wpml_object_id', $casi->term_id, 'category', true) : 0;
    $args = ['post_type' => 'post', 'post_status' => 'publish', 'numberposts' => 3, 'post__not_in' => [$id], 'suppress_filters' => false];
    if ($caso) {
        $args['category__in'] = [$casi_id];
    } else {
        $args['category__not_in'] = array_filter([(int) get_option('default_category'), $casi_id]);
    }
    $posts = get_posts($args);
    if (!$posts) {
        return '';
    }
    $out = '<ul class="es-news-related' . ($caso ? ' es-news-related--case' : '') . '" role="list">';
    foreach ($posts as $p) {
        $out .= '<li>' . es_news_card($p, 'h3', false, $caso) . '</li>';
    }
    return $out . '</ul>';
});

add_action('init', function () {
    if (get_option('es_news_strings2') === 'n2') {
        return;
    }
    foreach (['Posizione nel carosello', 'News %1$d–%2$d di %3$d', 'News %1$d di %3$d'] as $s) {
        do_action('wpml_register_single_string', 'Eurostar template', $s, $s, false, 'it');
    }
    update_option('es_news_strings2', 'n2', false);
}, 20);


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

