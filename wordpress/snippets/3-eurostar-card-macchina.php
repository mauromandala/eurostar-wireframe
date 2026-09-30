<?php
// <Internal Doc Start>
/*
*
* @description: Card macchina: riga dati per categoria [es_card_stats]; modelli Squadron linkati all ancora nella pagina Squadron (singola reindirizzata).
* @tags: eurostar, template, card
* @group: 
* @name: Eurostar card macchina
* @type: PHP
* @status: published
* @created_by: 1
* @created_at: 2026-09-29 05:30:37
* @updated_at: 2026-09-29 05:30:37
* @is_valid: 1
* @updated_by: 1
* @priority: 12
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

// Card macchina (riga dati per categoria) e link dei modelli Squadron alla pagina Squadron.
// Etichette in WPML String Translation, contesto "Eurostar template".

if (!function_exists('es_t')) {
    function es_t($text)
    {
        return apply_filters('wpml_translate_single_string', $text, 'Eurostar template', $text);
    }
}

add_action('init', function () {
    if (get_option('es_card_strings') === 'c1') {
        return;
    }
    foreach (['Tipo contenitore', 'Contenitori/ora', 'Prodotto', 'Tipo di chiusura', 'Contenitori', 'Cambio formato'] as $s) {
        do_action('wpml_register_single_string', 'Eurostar template', $s, $s, false, 'it');
    }
    update_option('es_card_strings', 'c1', false);
}, 20);

// Slug italiano (lingua di origine) del termine, così la mappa vale anche per i termini tradotti.
if (!function_exists('es_term_slug_it')) {
    function es_term_slug_it($post_id, $taxonomy)
    {
        $terms = get_the_terms($post_id, $taxonomy);
        if (!$terms || is_wp_error($terms)) {
            return '';
        }
        global $wpdb;
        $id = apply_filters('wpml_object_id', $terms[0]->term_id, $taxonomy, true, 'it');
        // Lettura diretta: sul front-end EN WPML riconvertirebbe get_term() nel termine tradotto.
        $slug = $wpdb->get_var($wpdb->prepare("SELECT slug FROM {$wpdb->terms} WHERE term_id = %d", $id));
        return $slug ?: $terms[0]->slug;
    }
}

// I due dati della card dipendono dalla categoria (acf-elementor-mapping.md, sez. 1).
add_shortcode('es_card_stats', function () {
    $id = get_the_ID();
    $map = [
        'sciacquatrici' => [['contenitori', 'Tipo contenitore'], ['contenitori_ora', 'Contenitori/ora']],
        'riempitrici' => [['prodotto_breve', 'Prodotto'], ['contenitori_ora', 'Contenitori/ora']],
        'tappatrici' => [['tipologia_chiusura', 'Tipo di chiusura'], ['contenitori_ora', 'Contenitori/ora']],
        'movimentazione' => [['contenitori', 'Contenitori'], ['cambio_formato', 'Cambio formato']],
    ];
    $cat = es_term_slug_it($id, 'categoria_macchina');
    if (!$id || !isset($map[$cat])) {
        return '';
    }
    $out = '';
    foreach ($map[$cat] as $stat) {
        $value = get_field($stat[0], $id);
        if (!$value) {
            continue;
        }
        $out .= '<div class="es-mcard-stat"><p class="es-mcard-stat-v">' . esc_html($value) . '</p><p class="es-mcard-stat-l">' . esc_html(es_t($stat[1])) . '</p></div>';
    }
    // data-cat (slug italiano) serve al filtro del catalogo.
    return $out ? '<div class="es-mcard-stats" data-cat="' . esc_attr($cat) . '">' . $out . '</div>' : '';
});

// Squadron: nessuna pagina singola al lancio (mapping 3.3). Il permalink del modello è
// l'ancora nella pagina Squadron della sua lingua; la singola reindirizza lì.
if (!function_exists('es_squadron_url')) {
    function es_squadron_url($post)
    {
        $post = get_post($post);
        if (!$post || $post->post_type !== 'macchina' || es_term_slug_it($post->ID, 'linea') !== 'squadron') {
            return '';
        }
        $page = get_page_by_path('squadron');
        if (!$page) {
            return '';
        }
        $lang = apply_filters('wpml_post_language_details', null, $post->ID);
        $code = is_array($lang) && !empty($lang['language_code']) ? $lang['language_code'] : null;
        $page_id = apply_filters('wpml_object_id', $page->ID, 'page', true, $code);
        return get_permalink($page_id) . '#' . $post->post_name;
    }
}

add_filter('post_type_link', function ($link, $post) {
    if ($post->post_type !== 'macchina' || is_admin()) {
        return $link;
    }
    return es_squadron_url($post) ?: $link;
}, 10, 2);

add_action('template_redirect', function () {
    if (!is_singular('macchina')) {
        return;
    }
    $url = es_squadron_url(get_queried_object());
    if ($url) {
        wp_safe_redirect($url, 302);
        exit;
    }
});