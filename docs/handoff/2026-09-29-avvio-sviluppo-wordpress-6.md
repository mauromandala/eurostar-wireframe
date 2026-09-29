---
type: handoff
date: 2026-09-29
status: in corso
seq: 6
prev: docs/handoff/2026-09-29-avvio-sviluppo-wordpress-5.md
tags: [eurostar, wordpress, elementor-atomic, pagine, immagini]
---

## Obiettivo

Replicare in WordPress + Elementor (staging `eurostar.demoengagemint.it`) il sito del wireframe statico (`http://localhost:4173`, questo repo), preciso al pixel, accessibile WCAG 2.1 AA, bilingue IT/EN con WPML. Atomic + classi globali dove possibile, shortcode FluentSnippets dove serve un dato. Specifica e stato dettagliato: `acf-elementor-mapping.md` (sezioni "… — stato" in fondo).

## A che punto siamo

- **Fatti e validati IT + EN** (confronto al pixel col wireframe a 1440/768/375, nessuno scroll orizzontale a 320): header, footer, scheda macchina, card, archivi categoria/settore, catalogo, Linee complete, Usate, Squadron, Contatti, Conferma; **oggi**: Settori (255/354), Servizi (256/356), Chi siamo (254/352), template 404 (650/652), Referenze (257/358).
- **Immagini**: documento per la grafica pubblicato (artifact "Immagini sito Eurostar", https://claude.ai/artifact/7UE3SavgXo8g9stu4kTsJ4): 162 file + news, formati, pixel, pesi, nomi file. Cartella Drive creata e condivisa: `Paolo Scagliola/Eurostar/Ilaria/Immagini sito/` (01-sfondi-testate … 09-news, `04-macchine/<slug>/descrizioni.txt`). Email a Ilaria preparata (l'invio lo fa l'utente). Sorgente del documento solo nella scratchpad (non persistente): se va modificato, rileggerlo con `Artifact read`.
- **Non iniziato**: Home, Cataloghi, Lavora con noi + 5 posizioni; News (modello dati da definire). Sfondi fotografici delle testate scure e riquadri immagine: solo segnaposto Gray 150 finché non arrivano le foto.

## Cosa abbiamo provato che NON ha funzionato

- `make_duplicate` su una traduzione esistente: sovrascrive **titolo e slug** EN con quelli IT → rileggerli prima e riscriverli dopo con `$wpdb->update`.
- `get_field('clienti','es_clienti')` in EN: ACF Multilingual legge `es_clienti_en` (vuoto) → filtro `acf/validate_post_id` che forza `es_clienti`.
- Screenshot di una scheda del browser in background dopo uno scroll: esce bianco → portarla in primo piano (`tabs_select`) o misurare via JS.
- Aprire un file locale `file://` dalla scratchpad nel browser: finisce in un pannello snapshot separato, non nella scheda.

## Problemi incontrati e come li abbiamo risolti

- **Pagina scritta con `elementor-set-content` senza pacchetto WPML** → `wp_update_post(['ID'=>…])` sulla pagina IT registra le stringhe (poi leggerle da `icl_strings` per `string_package_id`).
- **Titolo H1 Chi siamo più largo dello schermo sotto ~470px** ("DELL'IMBOTTIGLIAMENTO" a 38px, sfora anche il wireframe) → variante locale mobile `font-size:min(38px, calc(8.8vw - 4px))`.
- **Tessere clienti 88 vs 122px**: il wireframe non usa `border-box` (min-height 88 + padding + bordo) → nel sito `min-height:122px`.
- **Numeri e kicker dentro un blocco** (tappe Servizi, etichette card): `e-paragraph` con tag `span` riproduce la riga di testo del wireframe al pixel.
- **Token `--gray-200` inesistente** nel design system (404 nero nel wireframe) → `#D3D7E2`.

## Decisioni prese

- Ordine settori nella griglia = campo Ordine (non l'ordine del wireframe), come per macchine e pillole.
- Soglia 560px del wireframe → breakpoint Mobile 767 (corretta anche la griglia Settori).
- Tolti dal wireframe: riquadro tratteggiato vuoto in Servizi (−54px), fascia "Testimonianza cliente oscurata" in Chi siamo (−326,8px; da aggiungere con citazione reale).
- Card post-vendita Servizi: 1 colonna sotto 767 (il wireframe sfora); email/telefono aftersales cliccabili (nel wireframe testo), dati in `es_recapiti()`.
- Clienti: pagina opzioni ACF "Clienti" (repeater nome + logo) come unica fonte per Referenze e futuro nastro Home, scartati array PHP e CPT.
- Breadcrumb `[es_breadcrumb_archivio]`: attributi `tema="light"` e `corrente="…"`.
- Immagini: WebP q80 sRGB alle dimensioni esatte (2× schermo), foto principali macchine scontornate su trasparente, loghi/planisfero SVG; testate 2560×1440 con area sicura centrale ~1400×770.

## File toccati

- `acf-elementor-mapping.md` — sezioni stato Settori, Servizi, Chi siamo, 404, Referenze; ordine di costruzione aggiornato. Commit fino a `1faaa46`, pushato.
- Staging (non nel repo): snippet `4-eurostar-archivi-catalogo.php` (breadcrumb `tema`/`corrente`), `6-eurostar-pagine.php` (`[es_settori_griglia]`, `[es_aftersales]`, `[es_aftersales_cta]`, `[es_clienti]`, filtro ACF); Kit 8 Custom CSS (blocchi Settori, Servizi, Referenze); ~45 classi globali nuove (`es-sec-hero-light`, `es-h1-hero`, `es-stats-lg`, `es-svc*`, `es-team*`, `es-media-*`, `es-404*`, `es-link-u`, `es-sec-cta-sm`…); pagine 254–257 e EN; template 650/652; opzioni ACF "Clienti" (655, gruppo 656).
- Drive: `Ilaria/Immagini sito/` con sottocartelle e `descrizioni.txt`.

## Dove vogliamo andare

1. Costruisci la **Home** (`home.html`): testata 80vh (sfondo foto), sezione "Progettiamo soluzioni" (bottiglia al centro + 4 numeri agli angoli sopra 900px), nastro clienti scorrevole da `[es_clienti]`/pagina opzioni, bivio "Trova la tua soluzione", fascia Squadron (logo), blocchi diagonali, citazione, "Macchine in evidenza", news (modello non definito: valutare segnaposto o rimandare), invito finale; poi imposta la pagina come Home in Impostazioni → Lettura e aggiorna i link "Torna alla home" (404, Conferma).
2. Poi **Cataloghi** (servono i PDF) e **Lavora con noi** + 5 posizioni (form con destinatario mauromandala@gmail.com, campo nascosto con il titolo della posizione).
3. Quando arrivano le immagini nella cartella Drive: livello foto nelle testate scure + campi immagine testata per categorie/settori, poi sostituire i riquadri `es-media-*`/`es-svc-media`/`es-map` con widget Immagine.

## Da sapere prima di toccare qualcosa

- Operare con Novamira (`mcp__novamira-eurostar-demoeng__mcp-adapter-execute-ability`); le abilities si possono chiamare anche da PHP con `wp_get_ability('…')->execute($input)` (utile per creare molte classi in una volta). JSON grandi: `novamira/create-upload-link` + `curl -X PUT`, applicare da PHP, cancellare il file solo dopo `success`.
- Snippet: modificare il file in `wp-content/fluent-snippet-storage/` mantenendo il blocco doc e `<?php` iniziale, poi `\FluentSnippets\App\Helpers\Helper::cacheSnippetIndex()`; il nuovo shortcode è disponibile solo dalla richiesta successiva.
- EN pagine: `make_duplicate` → `delete_post_meta(_icl_lang_duplicate_of)` → `icl_add_string_translation` → `do_action('wpml_pb_finished_adding_string_translations', $en, $it, [])` → ripristino titolo/slug EN → in richiesta separata rigenerare la cache condizioni Theme Builder. Stringhe snippet: contesto "Eurostar template", poi `\WPML\Container\make(\WPML\ST\MO\File\Manager::class)->add('Eurostar template','en_US')`.
- Link a pagine: tipo **query** (ID) così WPML li traduce; i link URL finiscono nel pacchetto e vanno tradotti a mano.
- Pagine: modello `elementor_header_footer`, contenitore `main` con `_cssid` `content`. Verifica al pixel: misurare con JS le coordinate relative al `main` in entrambe le schede (wireframe `seed`, staging `tab-1`) a 1440/768/375 e `scrollWidth` a 320; ripristinare i viewport a fine verifica.
- Testi EN scritti da me → da far rivedere; slug/titoli EN e SEO → referente SEO. Non inviare form di prova senza chiedere. Avvisi `gzuncompress()` di WPML innocui.
- Repo: commit + push su `main`, trailer `Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>`.
