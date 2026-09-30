<?php
// Classi globali dei template News (articolo e caso studio). Salta quelle già esistenti.
if (!defined('ABSPATH')) { return; }

$VM = get_option('es_variabili_map');
$S = function ($v, $u = 'px') { return ['$$type' => 'size', 'value' => ['size' => $v, 'unit' => $u]]; };
$CU = function ($v) use ($S) { return $S($v, 'custom'); };
$Z = function ($n) use ($VM) { return ['$$type' => 'global-size-variable', 'value' => $VM[$n]]; };
$T = function ($s) { return ['$$type' => 'string', 'value' => (string) $s]; };
$C = function ($n) use ($VM) { return ['$$type' => 'global-color-variable', 'value' => $VM[$n]]; };
$F = function ($n) use ($VM) { return ['$$type' => 'global-font-variable', 'value' => $VM[$n]]; };
$BG = function ($c) { return ['$$type' => 'background', 'value' => ['color' => $c]]; };
$D = function ($t, $r, $b, $l) { return ['$$type' => 'dimensions', 'value' => ['block-start' => $t, 'inline-end' => $r, 'block-end' => $b, 'inline-start' => $l]]; };
$Z0 = $S(0);
$ME = ['breakpoint' => 'mobile_extra', 'state' => null];
$TITLE = function ($size, $lh) use ($F, $T, $C, $CU, $S, $D, $Z0) {
    return ['font-family' => $F('font-titoli'), 'font-weight' => $T('900'), 'font-size' => $size, 'line-height' => $CU($lh), 'color' => $C('navy-800'),
        'text-transform' => $T('uppercase'), 'max-width' => $CU('22ch'), 'margin' => $D($Z0, $Z0, $S(20), $Z0)];
};

$classes = [
    // Testata chiara dell'articolo e del caso studio (breadcrumb, titolo, meta)
    ['es-news-head', ['display' => $T('block'), 'padding' => $D($CU('clamp(40px,7vh,72px)'), $Z('sezione-x'), $CU('clamp(24px,4vh,32px)'), $Z('sezione-x')), 'background' => $BG($C('bianco'))]],
    ['es-case-head', ['display' => $T('block'), 'padding' => $D($CU('clamp(36px,6vh,72px)'), $Z('sezione-x'), $CU('clamp(24px,4vh,36px)'), $Z('sezione-x')), 'background' => $BG($C('bianco'))]],
    ['es-h1-article', $TITLE($Z('testo-display-1'), '0.92') + ['letter-spacing' => $S(-0.02, 'em')]],
    ['es-h1-case', $TITLE($Z('testo-display-2'), '0.94')],
    // Fascia dell'immagine in evidenza (sotto la testata)
    ['es-img-band', ['display' => $T('block'), 'padding' => $D($Z0, $Z('sezione-x'), $Z0, $Z('sezione-x')), 'background' => $BG($C('bianco'))]],
    ['es-img-band-case', ['display' => $T('block'), 'padding' => $D($Z0, $Z('sezione-x'), $CU('clamp(40px,6vh,64px)'), $Z('sezione-x')), 'background' => $BG($C('bianco'))]],
    // Caso studio: sezione senza padding sopra, griglia 70/30 (una colonna sotto 900px)
    ['es-sec-flush', ['display' => $T('block'), 'padding' => $D($Z0, $Z('sezione-x'), $Z('sezione-y'), $Z('sezione-x')), 'background' => $BG($C('bianco'))]],
    // Colonna del testo del caso studio (titoli di sezione più grandi nel CSS del Kit)
    ['es-case-body', ['display' => $T('block'), 'padding' => $Z0, 'min-width' => $Z0]],
    ['es-case-grid', ['display' => $T('grid'), 'grid-template-columns' => $T('minmax(0,70fr) minmax(0,30fr)'), 'grid-auto-rows' => $S('', 'auto'), 'gap' => $CU('clamp(32px,4vw,64px)'), 'align-items' => $T('start'), 'padding' => $Z0],
        [[$ME, ['grid-template-columns' => $T('minmax(0,1fr)')]]]],
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
