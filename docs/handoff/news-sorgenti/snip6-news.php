
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
// Barra sticky a filo della nav: altezza header meno barra alta (non si legge lo spostamento dell'header, che il suo script
// imposta dopo: letto prima dava 42px di stacco). ?categoria=slug preseleziona un filtro (gli archivi di categoria di WordPress rimandano qui).
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
