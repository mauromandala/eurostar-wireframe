
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
    $default = (int) get_option('default_category');
    $out = '<div class="es-news-track" id="es-news-track" role="region" aria-label="' . esc_attr(es_t('Ultime news')) . '" tabindex="0">';
    foreach ($q->posts as $post) {
        $cat = '';
        foreach (get_the_category($post->ID) as $c) {
            if ((int) $c->term_id !== $default) {
                $cat = $c->name;
                break;
            }
        }
        $img = has_post_thumbnail($post)
            ? get_the_post_thumbnail($post, 'medium_large', ['alt' => '', 'loading' => 'lazy', 'sizes' => '(max-width: 760px) 100vw, 33vw'])
            : '';
        $out .= '<a class="es-art" href="' . esc_url(get_permalink($post)) . '">'
            . '<div class="es-art-frame" aria-hidden="true">' . $img . '</div>'
            . '<div class="es-art-body">'
            . '<div class="es-art-meta">' . ($cat !== '' ? '<span>' . esc_html($cat) . '</span>' : '')
            . '<span class="es-art-date">' . esc_html(date_i18n('F Y', get_post_time('U', false, $post))) . '</span></div>'
            . '<h3 class="es-art-title">' . esc_html(get_the_title($post)) . '</h3>'
            . '<p class="es-art-excerpt">' . esc_html(wp_strip_all_tags(get_the_excerpt($post))) . '</p>'
            . '</div></a>';
    }
    $out .= '</div>';
    $out .= "<script>document.addEventListener('click',function(e){var b=e.target.closest&&e.target.closest('.es-news-nav');if(!b)return;"
        . "var t=document.getElementById(b.getAttribute('aria-controls'));if(!t)return;var c=t.querySelector('.es-art');"
        . "var s=c?c.getBoundingClientRect().width+24:t.clientWidth;"
        . "var r=window.matchMedia&&window.matchMedia('(prefers-reduced-motion: reduce)').matches;"
        . "t.scrollBy({left:s*parseInt(b.dataset.dir,10),behavior:r?'auto':'smooth'});});</script>";
    return $out;
});
