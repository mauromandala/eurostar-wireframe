<?php
// Classi globali di Lavora con noi e della scheda posizione (valori dal wireframe). Salta quelle già esistenti.
if (!defined('ABSPATH')) { return; }

$VM = get_option('es_variabili_map');
$S = function ($v, $u = 'px') { return ['$$type' => 'size', 'value' => ['size' => $v, 'unit' => $u]]; };
$CU = function ($v) use ($S) { return $S($v, 'custom'); };
$T = function ($s) { return ['$$type' => 'string', 'value' => (string) $s]; };
$C = function ($n) use ($VM) { return ['$$type' => 'global-color-variable', 'value' => $VM[$n]]; };
$BG = function ($c) { return ['$$type' => 'background', 'value' => ['color' => $c]]; };
$Z0 = $S(0);
$ME = ['breakpoint' => 'mobile_extra', 'state' => null];
$PAD_BOX = $CU('clamp(28px,4vw,44px)');

$classes = [
    // Scheda posizione: corpo 1.6fr + riquadro candidatura 1fr, gap 72px; una colonna sotto 900px
    ['es-job-detail-grid', ['display' => $T('grid'), 'grid-template-columns' => $T('minmax(0,1.6fr) minmax(0,1fr)'), 'grid-auto-rows' => $S('', 'auto'), 'gap' => $S(72), 'align-items' => $T('start'), 'padding' => $Z0],
        [[$ME, ['grid-template-columns' => $T('minmax(0,1fr)'), 'gap' => $S(48)]]]],
    // Riquadro "Candidati per questa posizione": fondo Gray 050, fisso durante lo scorrimento sopra 900px
    ['es-job-sidebar', ['display' => $T('block'), 'padding' => $CU('clamp(32px,3.6vw,44px)'), 'background' => $BG($C('gray-050')), 'position' => $T('sticky'), 'inset-block-start' => $S(110)],
        [[$ME, ['position' => $T('static')]]]],
    // Riga del link "Vedi tutte le posizioni aperte" (filetto sopra)
    ['es-job-back-row', ['display' => $T('block'), 'margin' => ['$$type' => 'dimensions', 'value' => ['block-start' => $S(16), 'inline-end' => $Z0, 'block-end' => $Z0, 'inline-start' => $Z0]],
        'padding' => ['$$type' => 'dimensions', 'value' => ['block-start' => $S(32), 'inline-end' => $Z0, 'block-end' => $Z0, 'inline-start' => $Z0]],
        'border-width' => ['$$type' => 'border-width-v2', 'value' => ['block-start' => $S(1), 'inline-end' => $Z0, 'block-end' => $Z0, 'inline-start' => $Z0]],
        'border-style' => $T('solid'), 'border-color' => $C('bordo-sottile')]],
    // Candidatura spontanea: titolo e testo su 640px, riquadro bianco del form (560px di contenuto)
    ['es-inner-640', ['display' => $T('block'), 'width' => $S(100, '%'), 'max-width' => $S(640), 'margin' => ['$$type' => 'dimensions', 'value' => ['block-start' => $Z0, 'inline-end' => $S('', 'auto'), 'block-end' => $Z0, 'inline-start' => $S('', 'auto')]], 'padding' => $Z0]],
    // Link "Vedi tutte le posizioni aperte": misure, bordo e colori nella classe (il pulsante atomic ha padding 12/24 e bordo 0
    // di base, caricati dopo il Kit); nel CSS del Kit restano riempimento in hover e freccia
    ['es-job-back', ['display' => $T('inline-flex'), 'align-items' => $T('center'), 'gap' => $S(10), 'text-align' => $T('start'),
        'padding' => ['$$type' => 'dimensions', 'value' => ['block-start' => $S(16), 'inline-end' => $S(28), 'block-end' => $S(16), 'inline-start' => $S(28)]],
        'border-width' => $S(2), 'border-style' => $T('solid'), 'border-color' => $C('blue-600'), 'border-radius' => $Z0,
        'background' => $BG($C('bianco')), 'color' => $C('blue-600'),
        'font-family' => ['$$type' => 'global-font-variable', 'value' => $VM['font-titoli']], 'font-weight' => $T('700'), 'font-size' => $S(13),
        'line-height' => $CU('normal'), 'letter-spacing' => $S(0.1, 'em'), 'text-transform' => $T('uppercase'), 'text-decoration' => $T('none')]],
    ['es-form-box', ['display' => $T('block'), 'width' => $S(100, '%'), 'max-width' => $CU('calc(560px + 2 * clamp(28px,4vw,44px))'),
        'margin' => ['$$type' => 'dimensions', 'value' => ['block-start' => $Z0, 'inline-end' => $S('', 'auto'), 'block-end' => $Z0, 'inline-start' => $S('', 'auto')]],
        'padding' => $PAD_BOX, 'background' => $BG($C('bianco')), 'text-align' => $T('start')]],
];

$existing = [];
$list = wp_get_ability('novamira/elementor-list-global-classes')->execute([]);
foreach ($list['classes'] as $c) { $existing[$c['label']] = $c['id']; }

$create = wp_get_ability('novamira/elementor-create-global-class');
$out = [];
foreach ($classes as $def) {
    [$label, $styles] = $def;
    if (isset($existing[$label])) { $out[$label] = 'ESISTE ' . $existing[$label]; continue; }
    $in = ['label' => $label, 'styles' => $styles];
    $variants = [];
    foreach (($def[2] ?? []) as [$meta, $st]) { $variants[] = ['meta' => $meta, 'styles' => $st]; }
    if ($variants) { $in['variants'] = $variants; }
    $r = $create->execute($in);
    if (is_wp_error($r)) { $out[$label] = 'ERR ' . $r->get_error_message(); continue; }
    $out[$label] = !empty($r['success']) ? ($r['id'] ?? $r['class']['id'] ?? 'ok') : 'ERR ' . json_encode($r);
}
return $out;
