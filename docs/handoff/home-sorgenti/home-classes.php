<?php
// Classi globali della Home (valori dal wireframe home.html), collegate alle variabili globali.
if (!defined('ABSPATH')) { return; }

$VM = get_option('es_variabili_map');
$S = function ($v) {
    if (is_array($v)) { return $v; }
    if (is_int($v) || is_float($v)) { return ['$$type' => 'size', 'value' => ['size' => $v, 'unit' => 'px']]; }
    if (preg_match('/^(-?[\d.]+)(px|em|rem|%|vh|vw|ch)$/', $v, $m)) {
        return ['$$type' => 'size', 'value' => ['size' => (float) $m[1] == (int) $m[1] ? (int) $m[1] : (float) $m[1], 'unit' => $m[2]]];
    }
    if ($v === 'auto') { return ['$$type' => 'size', 'value' => ['size' => '', 'unit' => 'auto']]; }
    return ['$$type' => 'size', 'value' => ['size' => $v, 'unit' => 'custom']];
};
$Z = function ($n) use ($VM) { return ['$$type' => 'global-size-variable', 'value' => $VM[$n]]; };
$C = function ($n) use ($VM) { return ['$$type' => 'global-color-variable', 'value' => $VM[$n]]; };
$L = function ($c) { return ['$$type' => 'color', 'value' => $c]; };
$F = function ($n) use ($VM) { return ['$$type' => 'global-font-variable', 'value' => $VM[$n === 'titoli' ? 'font-titoli' : 'font-testo']]; };
$T = function ($s) { return ['$$type' => 'string', 'value' => (string) $s]; };
$N = function ($n) { return ['$$type' => 'number', 'value' => $n]; };
$D = function ($t, $r, $b, $l) use ($S) {
    return ['$$type' => 'dimensions', 'value' => ['block-start' => $S($t), 'inline-end' => $S($r), 'block-end' => $S($b), 'inline-start' => $S($l)]];
};
$BW = function ($t, $r, $b, $l) use ($S) {
    return ['$$type' => 'border-width-v2', 'value' => ['block-start' => $S($t), 'inline-end' => $S($r), 'block-end' => $S($b), 'inline-start' => $S($l)]];
};
$BG = function ($color, $overlays = []) {
    $v = ['color' => $color];
    if ($overlays) { $v['background-overlay'] = ['$$type' => 'background-overlay', 'value' => $overlays]; }
    return ['$$type' => 'background', 'value' => $v];
};
$GRAD = function ($angle, $stops) use ($N, $L) {
    $st = [];
    foreach ($stops as [$c, $o]) { $st[] = ['$$type' => 'color-stop', 'value' => ['color' => $L($c), 'offset' => $N($o)]]; }
    return ['$$type' => 'background-gradient-overlay', 'value' => ['type' => ['$$type' => 'string', 'value' => 'linear'], 'angle' => $N($angle), 'stops' => ['$$type' => 'gradient-color-stop', 'value' => $st]]];
};
$MOVE = function ($x, $y) use ($S) {
    return ['$$type' => 'transform', 'value' => ['transform-functions' => ['$$type' => 'transform-functions', 'value' => [
        ['$$type' => 'transform-move', 'value' => ['x' => $S($x), 'y' => $S($y), 'z' => $S('0px')]],
    ]]]];
};
$FLEX = function ($g, $s, $b) use ($N, $S) { return ['$$type' => 'flex', 'value' => ['flexGrow' => $N($g), 'flexShrink' => $N($s), 'flexBasis' => $S($b)]]; };
$SX = 'sezione-x';
$PAD0 = $S(0);
$TXT = function ($font, $weight, $size, $lh, $color, $extra = []) use ($F, $T, $S) {
    return array_merge([
        'font-family' => $F($font), 'font-weight' => $T($weight), 'font-size' => is_array($size) ? $size : $S($size),
        'line-height' => $S($lh), 'color' => $color,
    ], $extra);
};
$UP = function ($ls) use ($S, $T) { return ['letter-spacing' => $S($ls), 'text-transform' => $T('uppercase')]; };
$MB = function ($t, $b) use ($D) { return ['margin' => $D($t, 0, $b, 0)]; };
$ME = ['breakpoint' => 'mobile_extra', 'state' => null];
$MO = ['breakpoint' => 'mobile', 'state' => null];
$HOV = ['breakpoint' => 'desktop', 'state' => 'hover'];
$Y_LG = 'clamp(96px,16vh,190px)';
$W72 = $L('rgba(255,255,255,0.72)');
$W50 = $L('rgba(255,255,255,0.5)');
$W40 = $L('rgba(255,255,255,0.4)');

$classes = [
    // Testata: foto (per ora velatura Gray 150 al 50%) + sfumatura scura; taglio diagonale in basso sopra 900px
    ['es-home-hero', [
        'display' => $T('flex'), 'flex-direction' => $T('row'), 'align-items' => $T('center'), 'justify-content' => $T('center'),
        'min-height' => $S('80vh'), 'overflow' => $T('hidden'),
        'padding' => $D('100px', $Z($SX), '260px', $Z($SX)),
        'clip-path' => $T('polygon(0 0,100% 0,100% 84%,0 100%)'),
        'background' => $BG($C('navy-800'), [
            $GRAD(90, [['rgba(10,10,10,0.94)', 0], ['rgba(10,10,10,0.55)', 72]]),
            $GRAD(90, [['rgba(227,230,238,0.5)', 0], ['rgba(227,230,238,0.5)', 100]]),
        ]),
    ], [[$ME, ['padding' => $D('100px', $Z($SX), '100px', $Z($SX)), 'clip-path' => $T('none')]]]],
    ['es-home-hero-inner', ['display' => $T('block'), 'width' => $S('100%'), 'max-width' => $Z('contenuto-max'), 'margin' => $D(0, 'auto', 0, 'auto'), 'padding' => $PAD0, 'text-align' => $T('center')]],
    ['es-home-h1', $TXT('titoli', '900', 'clamp(28px,3.4vw,52px)', '0.92', $C('bianco'), $UP('-0.02em') + ['max-width' => $S('30ch'), 'margin' => $D(0, 'auto', 0, 'auto')])],
    ['es-home-hero-lead', $TXT('testo', '300', 'clamp(13px,1.2vw,16px)', '1.65', $L('rgba(255,255,255,0.85)'), ['max-width' => $S('560px'), 'margin' => $D('28px', 'auto', 0, 'auto')])],
    ['es-home-btns', ['display' => $T('flex'), 'flex-direction' => $T('row'), 'flex-wrap' => $T('wrap'), 'justify-content' => $T('center'), 'gap' => $S(16), 'margin' => $D('36px', 0, 0, 0), 'padding' => $PAD0]],

    // "Progettiamo soluzioni": bottiglia al centro, claim sopra, 4 numeri agli angoli (sopra 900px), in colonna sotto
    ['es-home-engineer', ['display' => $T('flex'), 'flex-direction' => $T('column'), 'position' => $T('relative'), 'padding' => $PAD0, 'background' => $BG($C('bianco'))]],
    ['es-home-eng-wrap', [
        'position' => $T('relative'), 'display' => $T('flex'), 'flex-direction' => $T('column'), 'align-items' => $T('center'), 'gap' => $S(28),
        'width' => $S('100%'), 'max-width' => $Z('contenuto-max'), 'min-height' => $S('680px'),
        'margin' => $D('-180px', 'auto', 0, 'auto'), 'padding' => $D(0, $Z($SX), 0, $Z($SX)),
    ], [[$ME, ['min-height' => $S('auto'), 'margin' => $D(0, 'auto', 0, 'auto'), 'padding' => $D('40px', $Z($SX), '40px', $Z($SX))]]]],
    ['es-home-bottle', [
        'position' => $T('absolute'), 'inset-block-start' => $S(0), 'inset-inline-start' => $S('50%'), 'transform' => $MOVE('-50%', '0px'),
        'width' => $S('clamp(160px,14vw,210px)'), 'height' => $S('85%'), 'z-index' => $N(2), 'padding' => $PAD0,
        'background' => $BG($C('gray-150')),
    ], [[$ME, ['position' => $T('relative'), 'inset-block-start' => $S('auto'), 'inset-inline-start' => $S('auto'), 'transform' => $MOVE('0px', '0px'), 'width' => $S('140px'), 'height' => $S('390px')]]]],
    ['es-home-claim', $TXT('titoli', '900', 'clamp(40px,6vw,80px)', '1.05', $C('navy-800'), $UP('-0.02em') + [
        'text-align' => $T('center'), 'max-width' => $S('20ch'), 'margin' => $S(0),
        'position' => $T('absolute'), 'inset-block-start' => $S('50%'), 'inset-inline-start' => $S('50%'), 'transform' => $MOVE('-50%', '-50%'), 'z-index' => $N(1),
    ]), [[$ME, ['position' => $T('relative'), 'inset-block-start' => $S('auto'), 'inset-inline-start' => $S('auto'), 'transform' => $MOVE('0px', '0px'), 'font-size' => $S('clamp(30px,9vw,48px)'), 'max-width' => $S('16ch')]]]],
    ['es-home-stats', ['display' => $T('contents'), 'padding' => $PAD0],
        [[$ME, ['display' => $T('grid'), 'grid-template-columns' => $T('1fr 1fr'), 'grid-auto-rows' => $S('auto'), 'gap' => $S('28px 20px'), 'width' => $S('100%'), 'max-width' => $S('420px')]]]],
    ['es-home-stat', ['display' => $T('block'), 'position' => $T('absolute'), 'z-index' => $N(3), 'text-align' => $T('start'), 'padding' => $PAD0, 'width' => $S('auto')],
        [[$ME, ['position' => $T('static'), 'text-align' => $T('center')]]]],
    ['es-home-stat-tl', ['inset-inline-start' => $Z($SX), 'inset-block-start' => $S('230px')]],
    ['es-home-stat-tr', ['inset-inline-end' => $Z($SX), 'inset-block-start' => $S('130px')]],
    ['es-home-stat-bl', ['inset-inline-start' => $Z($SX), 'inset-block-start' => $S('72%')]],
    ['es-home-stat-br', ['inset-inline-end' => $Z($SX), 'inset-block-start' => $S('80%')]],
    ['es-home-stat-value', $TXT('titoli', '800', 'clamp(34px,3.9vw,44px)', '1', $C('navy-800'), ['display' => $T('block'), 'margin' => $S(0)])],
    ['es-home-stat-label', $TXT('testo', '400', 12, '1.4', $C('gray-500'), $UP('0.1em') + ['display' => $T('block'), 'margin' => $D('6px', 0, 0, 0)])],
    ['es-home-discover', ['display' => $T('block'), 'width' => $S('100%'), 'max-width' => $Z('contenuto-max'), 'margin' => $D(0, 'auto', 0, 'auto'), 'padding' => $D('clamp(48px,7vh,80px)', $Z($SX), $Y_LG, $Z($SX))]],
    ['es-home-discover-title', $TXT('titoli', '700', 14, 'normal', $C('gray-500'), $UP('0.1em') + $MB(0, '32px'))],
    ['es-home-discover-grid', ['display' => $T('grid'), 'grid-template-columns' => $T('repeat(auto-fit,minmax(min(220px,100%),1fr))'), 'grid-auto-rows' => $S('auto'), 'gap' => $S('clamp(28px,3vw,40px)'), 'padding' => $PAD0]],
    ['es-home-discover-item', ['display' => $T('flex'), 'flex-direction' => $T('column'), 'padding' => $PAD0, 'min-width' => $S(0)]],
    ['es-home-discover-icon', [
        'display' => $T('flex'), 'align-items' => $T('center'), 'justify-content' => $T('center'), 'flex' => $FLEX(0, 0, 'auto'),
        'width' => $S('52px'), 'height' => $S('52px'), 'padding' => $PAD0, 'margin' => $MB(0, '20px')['margin'],
        'border-width' => $S(1), 'border-style' => $T('solid'), 'border-color' => $C('bordo-sottile'), 'color' => $C('blue-600'),
    ]],
    ['es-ico-sciacquatura', ['display' => $T('flex')]],
    ['es-ico-riempimento', ['display' => $T('flex')]],
    ['es-ico-tappatura', ['display' => $T('flex')]],
    ['es-ico-linee', ['display' => $T('flex')]],
    ['es-home-discover-h', $TXT('titoli', '800', 16, 'normal', $C('navy-800'), $UP('0.04em') + $MB(0, '10px'))],
    ['es-home-discover-text', $TXT('testo', '400', 14, '1.5', $C('gray-500'), $MB(0, '16px') + ['flex' => $FLEX(1, 1, '0%')])],
    // Link testuale con freccia (es-textlink del wireframe): freccia disegnata dal CSS del Kit
    ['es-link-arrow', $TXT('titoli', '700', 13, 'normal', $C('blue-600'), $UP('0.08em') + [
        'display' => $T('inline'), 'text-decoration' => $T('none'), 'padding' => $PAD0, 'border-width' => $S(0), 'border-radius' => $S(0),
        'background' => $BG($L('transparent')),
    ]), [[$HOV, ['color' => $C('navy-800')]]]],

    // Nastro clienti
    ['es-home-trust', ['display' => $T('block'), 'padding' => $D('36px', $Z($SX), '36px', $Z($SX)), 'background' => $BG($C('navy-800')),
        'border-width' => $BW('1px', 0, 0, 0), 'border-style' => $T('solid'), 'border-color' => $W40]],
    ['es-home-trust-inner', ['display' => $T('flex'), 'flex-direction' => $T('row'), 'align-items' => $T('center'), 'gap' => $S(40), 'width' => $S('100%'), 'max-width' => $Z('contenuto-max'), 'margin' => $D(0, 'auto', 0, 'auto'), 'padding' => $PAD0],
        [[$MO, ['flex-direction' => $T('column'), 'align-items' => $T('stretch'), 'gap' => $S(20)]]]],
    ['es-home-trust-label', $TXT('titoli', '600', 11, '1', $W50, $UP('0.2em') + ['flex' => $FLEX(0, 0, 'auto'), 'margin' => $S(0)])],

    // Bivio "Trova la tua soluzione"
    ['es-home-bivi-grid', ['display' => $T('grid'), 'grid-template-columns' => $T('repeat(auto-fit,minmax(min(320px,100%),1fr))'), 'grid-auto-rows' => $S('auto'), 'gap' => $S(24), 'margin' => $D('32px', 0, 0, 0), 'padding' => $PAD0]],
    ['es-home-bivio', ['display' => $T('block'), 'padding' => $S('clamp(28px,3vw,44px)'), 'background' => $BG($C('gray-100'))], [[$HOV, ['background' => $BG($C('bianco'))]]]],
    ['es-home-bivio-kicker', $TXT('titoli', '600', 11, '1', $C('blue-600'), $UP('0.2em') + ['margin' => $S(0)])],
    ['es-home-bivio-title', $TXT('titoli', '900', $Z('testo-h2'), '0.96', $C('navy-800'), ['text-transform' => $T('uppercase'), 'margin' => $D('14px', 0, '12px', 0)])],
    ['es-home-bivio-text', $TXT('testo', '300', $Z('testo-body-md'), '1.6', $C('gray-500'), $MB(0, '22px') + ['max-width' => $S('44ch')])],

    // Fascia Squadron
    ['es-home-sq', ['display' => $T('block'), 'padding' => $D('clamp(40px,6vh,64px)', $Z($SX), 'clamp(40px,6vh,64px)', $Z($SX)), 'background' => $BG($C('navy-900'))]],
    ['es-home-sq-inner', ['display' => $T('flex'), 'flex-direction' => $T('row'), 'align-items' => $T('center'), 'justify-content' => $T('space-between'), 'flex-wrap' => $T('wrap'), 'gap' => $S(24),
        'width' => $S('100%'), 'max-width' => $Z('contenuto-max'), 'margin' => $D(0, 'auto', 0, 'auto'), 'padding' => $PAD0]],
    ['es-home-sq-left', ['display' => $T('flex'), 'flex-direction' => $T('row'), 'align-items' => $T('center'), 'flex-wrap' => $T('wrap'), 'gap' => $S(24), 'width' => $S('auto'), 'padding' => $PAD0]],
    ['es-home-sq-logo', ['width' => $S('120px'), 'height' => $S('40px'), 'flex' => $FLEX(0, 0, 'auto'), 'padding' => $PAD0, 'background' => $BG($L('rgba(255,255,255,0.1)'))]],
    ['es-home-sq-title', $TXT('titoli', '800', $Z('testo-h3'), '1.1', $C('bianco'), $UP('-0.02em') + ['margin' => $S(0)])],

    // Blocchi diagonali Servizi / Innovazione (taglio 56px, una colonna sotto 900px: prima il testo, poi la foto)
    ['es-home-diag-wrap', ['display' => $T('block'), 'width' => $S('100%'), 'max-width' => $Z('contenuto-max'), 'margin' => $D(0, 'auto', 'clamp(32px,5vh,56px)', 'auto'), 'padding' => $PAD0]],
    ['es-home-diag', ['display' => $T('grid'), 'grid-template-columns' => $T('1fr 1fr'), 'grid-auto-rows' => $S('auto'), 'align-items' => $T('stretch'), 'gap' => $S(0), 'min-height' => $S('420px'), 'padding' => $PAD0],
        [[$ME, ['grid-template-columns' => $T('1fr'), 'min-height' => $S(0)]]]],
    ['es-home-diag-panel', ['display' => $T('flex'), 'flex-direction' => $T('column'), 'justify-content' => $T('center'), 'min-width' => $S(0),
        'padding' => $D('clamp(40px,6vw,72px)', 'clamp(28px,5vw,72px)', 'clamp(40px,6vw,72px)', 'clamp(28px,5vw,72px)'), 'background' => $BG($C('bianco'))],
        [[$ME, ['order' => $N(1)]]]],
    ['es-home-diag-media', ['display' => $T('block'), 'padding' => $PAD0, 'min-width' => $S(0), 'overflow' => $T('hidden'), 'background' => $BG($C('gray-150'))],
        [[$ME, ['order' => $N(2), 'min-height' => $S('280px')]]]],
    ['es-home-diag-left', ['position' => $T('relative'), 'z-index' => $N(1), 'clip-path' => $T('polygon(0 0,100% 0,calc(100% - 56px) 100%,0 100%)')],
        [[$ME, ['clip-path' => $T('none')]]]],
    ['es-home-diag-right', ['margin' => $D(0, 0, 0, '-56px'), 'clip-path' => $T('polygon(56px 0,100% 0,100% 100%,0 100%)')],
        [[$ME, ['margin' => $S(0), 'clip-path' => $T('none')]]]],
    ['es-home-diag-h2', $TXT('titoli', '900', 'clamp(28px,3.4vw,44px)', '1.1', $C('navy-800'), ['text-transform' => $T('uppercase'), 'margin' => $D('16px', 0, '20px', 0)])],
    ['es-home-diag-text', $TXT('testo', '300', $Z('testo-body-lg'), '1.65', $C('gray-500'), $MB(0, '28px') + ['max-width' => $S('42ch')])],
    ['es-home-diag-cta', ['align-self' => $T('flex-start')]],

    // Citazione
    ['es-home-quote-inner', ['display' => $T('block'), 'width' => $S('100%'), 'max-width' => $S('820px'), 'margin' => $D(0, 'auto', 0, 'auto'), 'padding' => $PAD0, 'text-align' => $T('center')]],
    ['es-home-quote-kicker', $TXT('titoli', '700', 13, 'normal', $C('gray-500'), $UP('0.14em') + ['display' => $T('block')] + $MB(0, '20px'))],
    ['es-home-quote-lead', $TXT('titoli', '900', 'clamp(24px,2.4vw,32px)', '1.35', $C('navy-800'), $MB(0, '32px'))],
    ['es-home-quote', $TXT('testo', '600', 20, '1.4', $C('navy-800'), ['font-style' => $T('italic')] + $MB(0, '28px'))],
    ['es-home-author', ['display' => $T('flex'), 'flex-direction' => $T('row'), 'align-items' => $T('center'), 'justify-content' => $T('center'), 'gap' => $S(14), 'margin' => $MB(0, '48px')['margin'], 'padding' => $PAD0]],
    ['es-home-avatar', ['width' => $S('48px'), 'height' => $S('48px'), 'flex' => $FLEX(0, 0, 'auto'), 'border-radius' => $S('50%'), 'overflow' => $T('hidden'), 'padding' => $PAD0, 'background' => $BG($C('gray-150'))]],
    ['es-home-author-text', $TXT('testo', '400', 14, '1.5', $C('gray-500'), ['margin' => $S(0)])],

    // Macchine in evidenza
    ['es-home-feat', ['display' => $T('block'), 'padding' => $D($Z('sezione-y'), $Z($SX), $Z('sezione-y'), $Z($SX)), 'background' => $BG($C('navy-950'))]],
    ['es-home-feat-h2', $TXT('titoli', '900', 'clamp(32px,4vw,52px)', '1.05', $C('bianco'), ['text-transform' => $T('uppercase'), 'margin' => $D('16px', 0, '20px', 0)])],
    ['es-home-feat-lead', $TXT('testo', '300', $Z('testo-body-lg'), '1.65', $C('bianco-72'), $MB(0, '56px') + ['max-width' => $S('64ch')])],
    ['es-home-feat-grid', ['display' => $T('grid'), 'grid-template-columns' => $T('1fr 1fr 1fr'), 'grid-auto-rows' => $S('auto'), 'align-items' => $T('stretch'), 'gap' => $S(0), 'padding' => $PAD0],
        [[$ME, ['grid-template-columns' => $T('1fr'), 'gap' => $S(48)]]]],
    ['es-home-feat-col', ['display' => $T('block'), 'min-width' => $S(0),
        'padding' => $D(0, 'clamp(24px,3vw,40px)', 0, 'clamp(24px,3vw,40px)'),
        'border-width' => $BW(0, 0, 0, '1px'), 'border-style' => $T('solid'), 'border-color' => $W40],
        [[$ME, ['padding' => $S(0), 'border-width' => $S(0), 'min-height' => $S('360px')]]]],
    ['es-home-feat-label', $TXT('titoli', '700', 12, 'normal', $W50, $UP('0.14em') + ['display' => $T('block')] + $MB(0, '24px'))],
    ['es-home-feat-media', ['display' => $T('block'), 'width' => $S('100%'), 'height' => $S('clamp(220px,24vw,300px)'), 'overflow' => $T('hidden'), 'padding' => $PAD0, 'margin' => $MB(0, '28px')['margin'], 'background' => $BG($C('blue-600'))]],
    ['es-home-feat-h3', $TXT('titoli', '900', 26, '0.96', $C('bianco'), ['text-transform' => $T('uppercase')] + $MB(0, '6px'))],
    ['es-home-feat-sub', $TXT('testo', '400', 12, '1.4', $W50, $UP('0.1em') + ['display' => $T('block')] + $MB(0, '16px'))],
    ['es-home-feat-text', $TXT('testo', '300', $Z('testo-body-md'), '1.6', $C('bianco-72'), $MB(0, '28px') + ['max-width' => $S('38ch')])],

    // News (sezione nascosta dal CSS del Kit finché non ci sono articoli)
    ['es-home-news', ['display' => $T('block'), 'overflow' => $T('hidden'), 'padding' => $D($Z('sezione-y'), $Z($SX), $Z('sezione-y'), $Z($SX)), 'background' => $BG($C('gray-050'))]],
    ['es-home-news-head', ['display' => $T('flex'), 'flex-direction' => $T('row'), 'align-items' => $T('flex-end'), 'justify-content' => $T('space-between'), 'flex-wrap' => $T('wrap'), 'gap' => $S(24), 'margin' => $MB(0, '32px')['margin'], 'padding' => $PAD0]],
    ['es-home-news-titles', ['display' => $T('block'), 'width' => $S('auto'), 'padding' => $PAD0]],
    ['es-home-news-actions', ['display' => $T('flex'), 'flex-direction' => $T('row'), 'align-items' => $T('center'), 'gap' => $S(24), 'width' => $S('auto'), 'padding' => $PAD0]],

    // Invito finale
    ['es-home-cta', ['display' => $T('block'), 'text-align' => $T('center'), 'padding' => $D($Y_LG, $Z($SX), $Y_LG, $Z($SX)), 'background' => $BG($C('navy-900'))]],
    ['es-home-cta-h2', $TXT('titoli', '900', $Z('testo-display-1'), '0.92', $C('bianco'), $UP('-0.02em') + $MB(0, '32px'))],
];

$existing = [];
$list = wp_get_ability('novamira/elementor-list-global-classes')->execute([]);
foreach ($list['classes'] as $c) { $existing[$c['label']] = $c['id']; }

$create = wp_get_ability('novamira/elementor-create-global-class');
$out = [];
foreach ($classes as $def) {
    [$label, $styles] = $def;
    $variants = [];
    foreach (($def[2] ?? []) as [$meta, $st]) { $variants[] = ['meta' => $meta, 'styles' => $st]; }
    if (isset($existing[$label])) { $out[$label] = 'ESISTE ' . $existing[$label]; continue; }
    $in = ['label' => $label, 'styles' => $styles];
    if ($variants) { $in['variants'] = $variants; }
    $r = $create->execute($in);
    if (is_wp_error($r)) { $out[$label] = 'ERR ' . $r->get_error_message(); continue; }
    $out[$label] = !empty($r['success']) ? ($r['id'] ?? $r['class']['id'] ?? json_encode($r)) : 'ERR ' . json_encode($r);
}
return $out;
