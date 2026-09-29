<?php
// Classi globali della pagina Cataloghi (valori dal wireframe page-cataloghi.html). Salta quelle già esistenti.
if (!defined('ABSPATH')) { return; }

$S = function ($v) { return ['$$type' => 'size', 'value' => ['size' => $v, 'unit' => 'px']]; };
$T = function ($s) { return ['$$type' => 'string', 'value' => (string) $s]; };

$classes = [
    // Colonna dei gruppi (catalogo generale, cataloghi per categoria): 56px tra i gruppi
    ['es-catalog-groups', ['display' => $T('flex'), 'flex-direction' => $T('column'), 'gap' => $S(56)]],
    ['es-catalog-group', ['display' => $T('block'), 'padding' => $S(0)]],
];

$existing = [];
$list = wp_get_ability('novamira/elementor-list-global-classes')->execute([]);
foreach ($list['classes'] as $c) { $existing[$c['label']] = $c['id']; }

$create = wp_get_ability('novamira/elementor-create-global-class');
$out = [];
foreach ($classes as [$label, $styles]) {
    if (isset($existing[$label])) { $out[$label] = 'ESISTE ' . $existing[$label]; continue; }
    $r = $create->execute(['label' => $label, 'styles' => $styles]);
    if (is_wp_error($r)) { $out[$label] = 'ERR ' . $r->get_error_message(); continue; }
    $out[$label] = !empty($r['success']) ? ($r['id'] ?? $r['class']['id'] ?? 'ok') : 'ERR ' . json_encode($r);
}
return $out;
