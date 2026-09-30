
// === Home: testata con video e bottiglia (proposta grafica "Eurostar Sito PROPOSTA 1", 30/09) ===

add_action('init', function () {
    if (get_option('es_home_video_strings') === 'v1') {
        return;
    }
    foreach (['Metti in pausa il video di sfondo', 'Riproduci il video di sfondo'] as $s) {
        do_action('wpml_register_single_string', 'Eurostar template', $s, $s, false, 'it');
    }
    update_option('es_home_video_strings', 'v1', false);
}, 20);

// Testata della Home: video dei macchinari con i filtri e le velature della proposta, lettera "A" del marchio in sovrapposizione
// e pulsante pausa/riproduci (WCAG 2.2.2: il video parte da solo e dura più di 5 secondi). Con "riduci movimento" il video
// non parte: resta l'immagine fissa e il pulsante permette di avviarlo. Sotto 900px si carica la versione a 960px.
// Contiene anche lo script della sezione "Progettiamo soluzioni" (bottiglia, lente, movimento allo scorrimento).
add_shortcode('es_home_hero_video', function () {
    $v_lg = wp_get_attachment_url(862);
    $v_sm = wp_get_attachment_url(863);
    $poster = wp_get_attachment_url(864);
    $lettera = wp_get_attachment_url(865);
    if (!$v_lg) {
        return '';
    }
    $pausa = es_t('Metti in pausa il video di sfondo');
    $play = es_t('Riproduci il video di sfondo');
    $out = '<div class="es-hero-media" aria-hidden="true">'
        . '<video class="es-hero-video" muted loop playsinline preload="none" poster="' . esc_url($poster) . '" data-src-lg="' . esc_url($v_lg) . '" data-src-sm="' . esc_url($v_sm ?: $v_lg) . '"></video>'
        . '<span class="es-hero-shade"></span><span class="es-hero-vignette"></span>'
        . ($lettera ? '<img class="es-hero-letter" src="' . esc_url($lettera) . '" alt="" width="1920" height="1080" decoding="async">' : '')
        . '</div>'
        . '<button type="button" class="es-hero-play" aria-label="' . esc_attr($play) . '" data-label-pause="' . esc_attr($pausa) . '" data-label-play="' . esc_attr($play) . '">'
        . '<svg class="es-hero-play-i" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path d="M8 5v14l11-7z" fill="currentColor"/></svg>'
        . '<svg class="es-hero-pause-i" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path d="M7 5h4v14H7zM13 5h4v14h-4z" fill="currentColor"/></svg>'
        . '</button>';
    $out .= <<<'JS'
<script>
(function(){
function init(){
var rm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
// Video della testata
var v = document.querySelector('.es-hero-video'), b = document.querySelector('.es-hero-play');
function stato(on){ if(!b) return; b.classList.toggle('is-playing', on); b.setAttribute('aria-label', on ? b.dataset.labelPause : b.dataset.labelPlay); }
function carica(){ if (v && !v.getAttribute('src')) { v.src = window.innerWidth <= 900 ? v.dataset.srcSm : v.dataset.srcLg; } }
function avvia(){ carica(); var p = v.play(); if (p && p.then) p.then(function(){ stato(true); }, function(){ stato(false); }); else stato(true); }
if (v) {
  if (!rm) avvia(); else stato(false);
  b && b.addEventListener('click', function(){ if (v.paused) avvia(); else { v.pause(); stato(false); } });
}
// Sezione "Progettiamo soluzioni": la bottiglia sale nella testata (vetro sotto i pulsanti), la frase resta sulla parte bianca
// centrata sul corpo della bottiglia, i numeri le stanno attorno; dentro il corpo una copia ingrandita e sfocata della frase fa da lente.
// Stesse proporzioni e formule dello script della proposta (_layoutBottle, parallasse).
var hero = document.querySelector('.es-home-hero'), cta = document.querySelector('.es-home-btns'), box = document.querySelector('.es-home-bottle'),
    wrap = document.querySelector('.es-home-eng-wrap'), claim = document.querySelector('.es-home-claim'), disc = document.querySelector('.es-home-discover');
if (!hero || !cta || !box || !wrap || !claim) return;
if (!claim.querySelector('.es-home-claim-dot')) { claim.innerHTML = claim.innerHTML.replace(/\.\s*$/, '<span class="es-home-claim-dot">.</span>'); }
var lens = document.createElement('div'); lens.className = 'es-home-lens'; lens.setAttribute('aria-hidden', 'true');
var lt = document.createElement('div'); lt.className = 'es-home-lens-text'; lt.textContent = claim.textContent.replace(/\.\s*$/, '');
lens.appendChild(lt); box.appendChild(lens);
var hdr = document.querySelector('.elementor-location-header');
function layout(){
  if (hdr) document.documentElement.style.setProperty('--es-hdr-h', hdr.offsetHeight + 'px');
  var h = box.offsetWidth; if (!h) return;
  var ctaBottom = cta.getBoundingClientRect().bottom - hero.getBoundingClientRect().top;
  var rise = hero.offsetHeight - (ctaBottom + 28) + h * 0.077 - h * 0.12;
  rise = Math.max(h * 0.1, Math.min(rise, h * 0.7));
  var tSeam = Math.round(Math.max(140, (h - rise) * 0.45));
  var desk = window.innerWidth > 1240, dp = disc ? parseFloat(getComputedStyle(disc).paddingTop) || 0 : 0;
  wrap.style.setProperty('--es-rise', Math.round(rise) + 'px');
  wrap.style.setProperty('--es-tseam', tSeam + 'px');
  wrap.style.setProperty('--es-lens-top', (tSeam + Math.round(rise)) + 'px');
  wrap.style.paddingTop = Math.max(0, Math.round(h - rise) + 240 - (desk ? dp : 0)) + 'px';
}
layout();
window.addEventListener('resize', layout); window.addEventListener('load', layout);
document.fonts && document.fonts.ready && document.fonts.ready.then(layout);
var img = box.querySelector('img'); if (img && !img.complete) img.addEventListener('load', layout);
if (rm) return;
// Parallasse (disattivata con "riduci movimento"): bottiglia 0,2 (max 200px), frase 0,06 (max 60), numeri 0,05–0,12;
// la lente segue la frase, non la bottiglia.
var stats = [['.es-home-stat-tl', .05, 70], ['.es-home-stat-bl', .1, 130], ['.es-home-stat-tr', .07, 95], ['.es-home-stat-br', .12, 160]].map(function(s){ return [document.querySelector(s[0]), s[1], s[2]]; });
var tick = false;
function par(){
  tick = false; var y = window.scrollY, pb = Math.min(y * .2, 200), ph = Math.min(y * .06, 60), desk = window.innerWidth > 1240;
  wrap.style.setProperty('--es-pb', pb.toFixed(1) + 'px'); wrap.style.setProperty('--es-ph', ph.toFixed(1) + 'px'); wrap.style.setProperty('--es-lens-shift', (ph - pb).toFixed(1) + 'px');
  stats.forEach(function(s){ if (s[0]) s[0].style.setProperty('--es-ps', desk ? Math.min(y * s[1], s[2]).toFixed(1) + 'px' : '0px'); });
}
window.addEventListener('scroll', function(){ if (!tick) { tick = true; requestAnimationFrame(par); } }, { passive: true });
par();
}
if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
</script>
JS;
    return $out;
});
// === fine Home: testata con video e bottiglia ===
