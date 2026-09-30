# Snippet PHP del sito WordPress (FluentSnippets)

Copia esatta dei file che FluentSnippets esegue sullo staging (`wp-content/fluent-snippet-storage/`), esportata il 30/09/2026. Sono la parte "logica" del sito: i template del Theme Builder e le pagine Elementor contengono widget Shortcode (`[es_…]`) che richiamano le funzioni qui dentro.

**La fonte di verità resta lo staging**: dopo ogni modifica a uno snippet va riesportato qui (stessa procedura, vedi sotto) e committato, così il repo mostra cosa è cambiato.

| File | Dove si usa | Shortcode e funzioni |
|---|---|---|
| `1-eurostar-scheda-macchina-shortcode.php` | Template "Scheda macchina" (237 / EN 248) | `es_breadcrumb`, `es_adatta_per`, `es_contenitori`, `es_descrizione`, `es_caratteristiche`, `es_download`, `es_galleria`, `es_cta_titolo` |
| `2-eurostar-header-footer.php` | Template Header (342 / EN 426) e Footer (344 / EN 428) | `es_header` (barra alta, nav, mega-menu, ricerca, lingua, hamburger), `es_menu_links`, `es_social`, `es_back_to_top`, `es_whatsapp_float`; script dell'header fisso (`top` = −altezza della barra alta) e dei mega-menu |
| `3-eurostar-card-macchina.php` | Componente "Card macchina" (448) | `es_card_stats` (riga dati per categoria); link dei modelli Squadron all'ancora della pagina Squadron |
| `4-eurostar-archivi-catalogo.php` | Template categoria (490/496), settore (503/504), catalogo (512/513), Linee complete (524/529), Usate (525/532) e pagine | `es_breadcrumb_archivio` (anche pagine, posizioni, articoli, casi studio e risultati di ricerca), `es_catalogo_filtri`, `es_catalogo_vuoto`, `es_term_cta`, `es_term_titolo`, `es_term_descrizione`, `es_layout_linee`, `es_sfide`, `es_sfide_titolo`, `es_squadron_head`; query dei Collection Loop `es_macchine_eurostar` / `es_macchine_squadron` |
| `5-eurostar-pagina-squadron.php` | Pagina Squadron (258 / EN 360) | `es_squadron_gamma`, `es_squadron_resto`, `es_squadron_settori` |
| `6-eurostar-pagine.php` | Pagine e template aggiunti dal 29/09 | Contatti/Conferma (`es_contatti_info`, `es_conferma_urgenze`), Settori (`es_settori_griglia`), Servizi (`es_aftersales`, `es_aftersales_cta`), Referenze e Home (`es_clienti`, `es_clienti_nastro`), Home News (`es_news_home`, `es_news_nav`), Home testata video e bottiglia (`es_home_hero_video`, con lo script di bottiglia, lente e parallasse), Cataloghi (`es_cataloghi`), Lavora con noi (`es_posizioni`, `es_posizione_kicker`, `es_posizione_meta`, `es_posizione_corpo`), News (`es_news_filtri`, `es_news_griglia`, `es_articolo_meta`, `es_articolo_immagine`, `es_articolo_corpo`, `es_articolo_correlati`, `es_caso_meta`, `es_caso_aside`), Risultati di ricerca 855/857 (`es_ricerca_titolo`, `es_ricerca_sommario`, `es_ricerca_form`, `es_ricerca_gruppo`, `es_ricerca_risultati`; query del loop `es_ricerca_macchine`); filtro ACF per un solo elenco Clienti/Cataloghi in tutte le lingue; archivi di categoria → News filtrata |

Tutte le etichette sono in WPML String Translation, contesto "Eurostar template" (funzione `es_t()`, definita nello snippet 6 e usata anche dagli altri).

## Regole per modificarli

- Si modifica il file sullo staging mantenendo il blocco di intestazione di FluentSnippets (`// <Internal Doc Start>` … `// <Internal Doc End>`) e il `<?php` iniziale, poi si rigenera l'indice con `\FluentSnippets\App\Helpers\Helper::cacheSnippetIndex()`. Un nuovo shortcode è disponibile dalla richiesta successiva.
- Prima di salvare, controllare la sintassi sul server (`token_get_all($codice, TOKEN_PARSE)`): un errore di sintassi può bloccare il sito.
- Se uno snippet viene disattivato, i widget che lo usano mostrano lo shortcode tra parentesi quadre o niente.

## Come riesportarli

Da `novamira/execute-php`: zip dei file di `fluent-snippet-storage/` (escluso `index.php`) in `wp-content/uploads/` con nome casuale, download con `curl`, confronto delle checksum MD5, cancellazione dello zip dal server.
