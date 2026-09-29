---
type: handoff
date: 2026-09-29
status: in corso
seq: 7
prev: docs/handoff/2026-09-29-avvio-sviluppo-wordpress-6.md
tags: [eurostar, wordpress, elementor-atomic, home]
---

## Obiettivo

Replicare in WordPress + Elementor (staging `eurostar.demoengagemint.it`) il sito del wireframe statico (`http://localhost:4173`, questo repo) al pixel, accessibile WCAG 2.1 AA, bilingue IT/EN con WPML. Classi globali atomic collegate alle variabili, shortcode FluentSnippets dove serve un dato. Specifica e stato: `acf-elementor-mapping.md` (sezioni "… — stato" in fondo). Ora si costruisce la **Home** (`home.html`).

## A che punto siamo

- **Pronto sullo staging per la Home (non ancora visibile: la pagina non esiste)**:
  - 84 classi globali nuove (`es-home-*`, `es-link-arrow`, `es-ico-sciacquatura/riempimento/tappatura/linee`), tutte con variabili; soglia 900px del wireframe = variante `mobile_extra`.
  - Snippet `6-eurostar-pagine.php`: shortcode `[es_clienti_nastro]` (nastro dalla pagina opzioni "Clienti", copia `aria-hidden`, pulsante pausa/play con `aria-pressed`), `[es_news_nav]` (frecce) e `[es_news_home]` (carosello degli ultimi 6 articoli con categoria, esclusa "Senza categoria"). Verificati: il nastro esce con 29 nomi, le news sono vuote.
  - CSS del Kit: blocco "Eurostar — Home" in fondo (freccia `→`/`↗` via `::after`, icone con maschera SVG, nastro, carosello news, sezione news nascosta con `:not(:has(.es-news-track))`).
- **Non fatto**: creazione della pagina Home e caricamento del JSON; verifica al pixel; EN; Impostazioni → Lettura; link "Torna alla home"; aggiornamento `acf-elementor-mapping.md`.
- Invariato dal handoff 6: Cataloghi, Lavora con noi + 5 posizioni, News (modello), immagini.

## Cosa abbiamo provato che NON ha funzionato

- Creare tutte le classi in una sola `execute-php`: **timeout a 30s** (ogni `elementor-create-global-class` riscrive tutte le classi, ~1,5s l'una). Serve `set_time_limit(300)`; lo script salta le classi già esistenti, quindi si può rilanciare.
- `text-align: left` nelle classi: rifiutato, l'enum è `start|center|end|justify`.
- `border-width` con tipo `dimensions` (bordo su un solo lato): rifiutato, serve `border-width-v2` con `block-start/inline-end/block-end/inline-start`.
- Leggere le classi da `_elementor_global_classes` del Kit: vuoto. Mappa etichetta → ID in post meta `_elementor_global_classes_labels` del Kit (post 8); definizioni complete con l'ability `novamira/elementor-list-global-classes`.

## Problemi incontrati e come li abbiamo risolti

- **Classi con molte proprietà** → script PHP caricato con `novamira/create-upload-link` in `wp-content/novamira-sandbox/` (solo lì si possono caricare `.php`) ed eseguito con `include`.
- **Markup delle card news**: `h3` dentro `span` non è valido → card = `a > div.es-art-frame + div.es-art-body (div.es-art-meta, h3, p)`.

## Decisioni prese

- **News in Home (utente, 29/09)**: carosello dagli articoli WordPress, sezione nascosta finché non ci sono articoli con categoria. Scartati "rimandare" e "3 segnaposto statici" (le news del wireframe sono inventate, compreso il caso "Cantine Ferrari" non autorizzato).
- Tolto il pulsante "Pausa video" della testata: nel wireframe non c'è un video, un pulsante che non comanda nulla è un problema di accessibilità.
- Logo Squadron: riquadro 120×40 con velatura bianca al 10% (nessun logo in Libreria); da sostituire con il file.
- Riquadri foto (bottiglia, blocchi diagonali, macchine in evidenza, avatar fondatore): fondo pieno Gray 150 / Blue 600, niente tratteggio (come nelle altre pagine).
- Link alle categorie e al catalogo = URL assoluti (come nei template esistenti; in EN vanno tradotti nel pacchetto); link a pagine/post = tipo query (ID) così WPML li traduce: Contatti 260, Servizi 256, Chi siamo 254, Settori 255, Squadron 258, News 259, SKILLFILL 167.
- "Schede tecniche" di MEC SI → categoria Sciacquatrici (come il wireframe), SKILLFILL → scheda, ATHENA → pagina Squadron.
- Barra clienti sotto 767px in colonna (etichetta sopra il nastro) per evitare lo scroll orizzontale: da verificare contro il wireframe a 375.
- Colonne "Macchine in evidenza": classe unica `es-home-feat-col` + stili locali sulla prima (niente bordo, padding a sinistra 0) e sull'ultima (padding a destra 0), con variante `mobile_extra` padding 0.

## File toccati

- `docs/handoff/home-sorgenti/` (nuovo, in questo repo): `gen_home.py` (genera il JSON Elementor della Home IT; classi scritte come `"@etichetta"`, da sostituire con gli ID sul server), `home-classes.php` (script che ha creato le 84 classi), `kit-home.css` (blocco CSS aggiunto al Kit), `snip6-home.php` (codice aggiunto allo snippet 6).
- Staging: `wp-content/fluent-snippet-storage/6-eurostar-pagine.php` (backup nell'opzione `es_snip6_backup_home`); CSS del Kit 8 (backup nell'opzione `es_kit_css_backup_home`); 84 classi globali; opzione `es_home_strings` = `h1` (5 stringhe registrate in "Eurostar template": pausa/riprendi loghi, news precedenti/successive, "Ultime news").
- File ancora in `wp-content/novamira-sandbox/`: `home-classes.php`, `snip6-home.php`, `kit-home.css` → da cancellare quando la Home è validata.

## Dove vogliamo andare

1. Genera il JSON (`python3 docs/handoff/home-sorgenti/gen_home.py home-it.json`), caricalo con `create-upload-link` e, da PHP: sostituisci ogni `"@etichetta"` con l'ID letto da `_elementor_global_classes_labels` (post 8); crea la pagina "Home" (slug `home`, pubblicata, modello `elementor_header_footer`, IT); scrivi i dati con l'ability `novamira/elementor-set-content` (controlla prima lo schema con `get-ability-info`); poi `wp_update_post` sulla pagina per registrare il pacchetto WPML.
2. Controlla che `e-paragraph` conservi gli `<span>` della citazione ("progettiamo e realizziamo", "continuità, efficienza e qualità" in Blue 600); se li toglie, usa `<strong>` e adatta il CSS del Kit.
3. Verifica al pixel contro il wireframe a 1440/768/375 e `scrollWidth` a 320: testata (80vh, taglio 84%), bottiglia e 4 numeri agli angoli (sopra 900px, sovrapposti di 180px alla testata), freccia dei link (`margin-left: .3em` da tarare), nastro, blocchi diagonali, colonne in evidenza.
4. EN: `make_duplicate` → procedura del handoff 6 (ripristino titolo/slug EN, traduzioni del pacchetto, URL `/en/…` con gli slug EN delle categorie, cache condizioni in richiesta separata); stringhe dello snippet in String Translation + rigenerazione `.mo`.
5. Imposta la Home in Impostazioni → Lettura (IT; per l'EN WPML abbina la traduzione) e aggiorna i link "Torna alla home" (template 404 650/652, Conferma 574/586).
6. Aggiorna `acf-elementor-mapping.md` (sezione "Pagina Home — stato", ordine di costruzione), commit + push; poi Cataloghi e Lavora con noi.

## Da sapere prima di toccare qualcosa

- **Comunicare in italiano con l'utente.**
- Tutto quello che serve è già nel handoff 6, sezione "Da sapere prima di toccare qualcosa" (Novamira, snippet, EN pagine, variabili, link query, verifica al pixel, testi EN da far rivedere, form di prova solo su richiesta).
- Classi globali: nel HTML escono con l'etichetta come nome della classe, come `.elementor .etichetta` (0,2,0); le regole del Kit che devono vincerle vanno raddoppiate (`.es-link-arrow.es-link-arrow`). I contenitori atomic hanno 10px di padding e `width:100%` di default: nelle classi scritti `padding:0` e, nelle righe flex, `width:auto`.
- Breakpoint del Kit: mobile 767, mobile_extra 900, tablet 1024, tablet_extra 1200.
- `home-classes.php` salta le classi che esistono già: rilanciarlo non crea doppioni.
