
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
    // Card uguale a quella dell'archivio News (es_news_card, sezione News dello snippet; etichetta "Caso studio" al singolare)
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

add_action('init', function () {
    if (get_option('es_news_strings2') === 'n2') {
        return;
    }
    foreach (['Posizione nel carosello', 'News %1$d–%2$d di %3$d', 'News %1$d di %3$d'] as $s) {
        do_action('wpml_register_single_string', 'Eurostar template', $s, $s, false, 'it');
    }
    update_option('es_news_strings2', 'n2', false);
}, 20);
