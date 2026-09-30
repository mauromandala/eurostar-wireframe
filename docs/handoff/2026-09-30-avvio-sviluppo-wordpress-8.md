---
type: handoff
date: 2026-09-30
status: in corso
seq: 8
prev: docs/handoff/2026-09-29-avvio-sviluppo-wordpress-7.md
tags: [eurostar, wordpress, elementor-atomic, news, ricerca]
---

## Obiettivo

Replicare in WordPress + Elementor (staging `eurostar.demoengagemint.it`) il sito del wireframe statico (`http://localhost:4173`, questo repo) al pixel, accessibile WCAG 2.1 AA, bilingue IT/EN con WPML, pronto per il lancio. Classi globali atomic collegate alle variabili, shortcode FluentSnippets dove serve un dato. Specifica e stato: `acf-elementor-mapping.md` (sezioni "… — stato" in fondo).

## A che punto siamo

- **Fatte e validate al pixel** (1440/768/375, 320 senza scroll): tutte le pagine del wireframe tranne il singolo servizio (non linkato, da decidere). Dal handoff 7: Home (740/742, pagina iniziale), Cataloghi (261/366), Lavora con noi (262/368) + 5 posizioni + template "Scheda posizione" (785/789), News (259/362) + template "Articolo" (849) e "Caso studio" (851) con 16 contenuti IT.
- **Ritocchi 30/09 su segnalazione utente**: card "Scopri le macchine per" in Home (icona centrata, "Esplora" a sinistra); effetto al passaggio del mouse delle card news esteso a card macchina, Squadron e settore; barra filtri News a filo della nav; carosello News in Home con contatore a pallini e senza card vicine visibili ai lati.
- **Snippet**: i 6 snippet FluentSnippets sono ora nel repo in `wordpress/snippets/` con README (fonte di verità resta lo staging).
- **Non fatto**: template dei risultati di ricerca; test finali (pixel 1024/1280, tastiera, zoom 200%, scanner Ally, VoiceOver); 20 schede macchina EN e 9 settori EN; articoli News EN (decisione utente: solo IT per ora).

## Cosa abbiamo provato che NON ha funzionato

- Confronto al pixel solo sui riquadri degli elementi: non vede testo centrato dentro un riquadro largo né pseudo-elementi spostati (caso card "Scopri le macchine per"). Ora si confronta anche la posizione del testo con `Range.getBoundingClientRect()`.
- Leggere i fogli di stile da JS con `r.cssRules ? [...r.cssRules] : [r]`: nei Chrome recenti anche le regole normali hanno `cssRules` (vuoto, CSS annidato) e vengono saltate. Usare `r instanceof CSSStyleRule`.
- Test del carosello/scorrimento con la scheda del browser in background o nell'iframe nascosto: lo scorrimento `smooth` resta fermo e i risultati sono falsi. Portare la scheda in primo piano (o usare `behavior:'instant'`).
- `::file-selector-button` nel CSS del Kit: Elementor sostituisce la parola `selector` con `.elementor-kit-8` (anche nei commenti). Usare `::-webkit-file-upload-button`.
- `email_reply_to: "email"` nel form con `elementor-set-content`: rifiutato dal validatore (opzioni generate a runtime). Si scrive dopo nei dati.

## Problemi incontrati e come li abbiamo risolti

- **Pseudo-elementi sui contenitori atomic**: Elementor usa `::before` dei contenitori per lo sfondo (`position:absolute`) → un'icona disegnata con `::before` finisce nell'angolo. Correzione: `position:static;inset:auto` sulla regola.
- **Pulsanti atomic (`e-button`)**: hanno `text-align:center`, padding 12/24 e bordo 0 di base, caricati dopo il Kit → le regole del Kit a pari specificità perdono. Mettere misure/bordo/allineamento nella classe globale.
- **Sticky sotto l'header**: lo script dell'header imposta `top:-42px` (barra alta) dopo gli script in pagina → calcolare la posizione come `header.offsetHeight - barra alta`, non leggendo `top`.
- **Carosello con padding laterale per l'ombra**: mostrava pezzi delle card vicine. Padding solo sopra/sotto.
- **Classi usate solo nel CSS del Kit ma messe nel JSON della pagina** (es. `es-job-back`): la sostituzione `@etichetta` fallisce se la classe globale non esiste → crearla prima.

## Decisioni prese

- **Cataloghi**: pagina opzioni ACF "Cataloghi", righe visibili solo con il PDF; PDF EN con ripiego sull'IT (scartati: righe sempre visibili, PDF segnaposto).
- **News**: categorie del documento del cliente (scartate le 3 del wireframe e l'accorpamento in 5); solo italiano per ora (scartati traduzione mia e IT anche in EN); date provvisorie per 6 articoli (scartati "solo anno" e "aspettare"); immagine POWERFILL sull'art. 12.
- **Casi studio**: articoli nella categoria "Casi studio" con template dedicato e breadcrumb Referenze (non un tipo di contenuto a parte); ultima citazione del cliente spostata nel riquadro "La voce del cliente".
- **Lavora con noi**: posizioni come tipo di contenuto con template unico; form di candidatura con CV allegato, destinatario mauromandala@gmail.com; nota "Proposta da confermare…" del wireframe non riportata.
- **Carosello Home**: contatore a pallini (una posizione per scatto delle frecce), non numerico — interpretazione di "a ." dell'utente, da cambiare se intendeva "1 / 4".
- **Hello world!** nel cestino (richiesta utente).

## File toccati

- `acf-elementor-mapping.md`: sezioni stato Home (aggiornata), Cataloghi, Lavora con noi, News e casi studio, effetto hover card, snippet nel repo.
- `docs/handoff/home-sorgenti/`: CSS e snippet della Home aggiornati (card, carosello, pallini).
- `docs/handoff/cataloghi-sorgenti/`, `lavora-sorgenti/`, `news-sorgenti/` (nuovi): generatori JSON, classi, CSS del Kit, codice snippet, traduzioni EN, dati (posizioni, testi news originali e `news-it.json`).
- `docs/handoff/kit-card-hover.css` (nuovo): effetto hover delle card.
- `wordpress/snippets/` (nuovo): i 6 snippet completi + README.
- Staging: snippet 4 e 6, CSS del Kit (copie di sicurezza in opzioni `es_kit_css_backup_*`, `es_snip6_backup_*`, `es_snip4_backup_*`), classi globali nuove, pagine 259/261/262 e EN, template 785/789/849/851, 5+5 posizioni, 16 articoli, 12 immagini (811–822), campi ACF "Articolo", "Caso studio", "Cataloghi".

## Dove vogliamo andare

1. Costruisci il template dei risultati di ricerca (Theme Builder `search-results`, IT + EN): l'header ha il pannello di ricerca ma il wireframe non ha la pagina. Disegnalo sul modello dell'archivio News (testata scura con "Risultati per «…»", card macchina/pagina/articolo, stato vuoto con link al catalogo), accessibile, poi verificalo a 1440/768/375/320.
2. Poi i test finali (punto 7 della specifica): confronto al pixel a 1024 e 1280 delle pagine principali, navigazione completa da tastiera (menu, mega-menu, hamburger, filtri, carosello, form), zoom 200%, scanner Ally dall'admin, VoiceOver sulla scheda MEC LD.
3. Dopo: 20 schede macchina EN e 9 settori EN; decisione sul singolo servizio.

## Da sapere prima di toccare qualcosa

- **Comunicare in italiano con l'utente.**
- Vale tutto il "Da sapere" dei handoff 6 e 7 (Novamira, procedura EN con `make_duplicate`, variabili globali, link di tipo query, classi `.elementor .etichetta` a specificità 0,2,0, breakpoint del Kit).
- Snippet: dopo ogni modifica sullo staging riesportarlo in `wordpress/snippets/` (procedura nel README) e controllare la sintassi con `token_get_all(..., TOKEN_PARSE)` prima di salvare.
- Da chiedere al cliente (aperti): date vere dei 6 articoli, immagini dei casi studio e di "Brevetti pionieri", testo per CFIA Rennes, sigla "VP-PP", "circa 60 Paesi" vs "oltre 100", PDF dei cataloghi, validazione delle 3 posizioni scritte per il wireframe, destinatario reale delle candidature.
- Non inviare form di prova senza chiedere.
