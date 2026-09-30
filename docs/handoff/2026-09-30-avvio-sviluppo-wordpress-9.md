---
type: handoff
date: 2026-09-30
status: in corso
seq: 9
prev: docs/handoff/2026-09-30-avvio-sviluppo-wordpress-8.md
tags: [eurostar, wordpress, home-proposta-grafica, test-finali]
---

## Obiettivo

Portare il sito WordPress + Elementor dello staging (`eurostar.demoengagemint.it`) al lancio: fedele al wireframe (`http://localhost:4173`, questo repo) e, dove l'utente lo decide, alla proposta grafica di Ilaria; accessibile WCAG 2.1 AA, bilingue IT/EN con WPML. Stato completo in `acf-elementor-mapping.md` (sezioni "… — stato" in fondo).

## A che punto siamo

- **Fatto oggi e validato**: template dei risultati di ricerca IT + EN (855 / 857); test finali al pixel su 24 pagine a 375/768/1024/1280/1440, tastiera (scansione di 23 pagine + 29/29 prove di interazione) e zoom 200%, con le correzioni emerse (card macchina, 404/Conferma, consenso dei form, mega-menu, menu mobile, "Torna su", focus sotto l'header).
- **Fatto oggi, verificato solo a occhio**: Home rifatta sulla proposta grafica (testata con video + sezione bottiglia, IT + EN). Foto a 1440/768/390 coerenti con la proposta, EN controllata nel browser. **Mancano** confronto al pixel con la proposta, tastiera e zoom 200% su questa sezione.
- **Non fatto**: scanner Ally (lo lancia l'utente dall'admin), VoiceOver sulla scheda MEC LD (utente). Schede macchina e settori EN: in attesa dei testi di chi traduce (decisione utente, non scriverli).

## Cosa abbiamo provato che NON ha funzionato

- Dire al grafico che serviva una bottiglia "col vetro davvero trasparente": sbagliato. `bottle.png` della proposta (corpo opaco) è quella dello screenshot approvato; verificato ricreando la proposta nel browser a 1440 e a 1000 densità 2. Dal grafico non serve nulla.
- Testata Home all'80% dello schermo (wireframe) con la bottiglia della proposta: a 768 e 390 la bottiglia non saliva nella testata (sotto i pulsanti non c'era spazio). Serve l'altezza della proposta: schermo meno header, contenuto al 43%.
- Pulsante pausa del video in basso a destra: finiva sotto i pulsanti cookie e WhatsApp. Spostato in basso a sinistra.
- Script della Home eseguito subito dentro lo shortcode della testata: gli elementi della sezione bottiglia non esistono ancora nel DOM. Si avvia a DOM pronto.
- `document.activeElement` nei test di tastiera: il banner cookie (Cookiez) è in shadow DOM, bisogna scendere in `shadowRoot.activeElement`. Cliccare "Rifiuta" e non ricaricare lascia il punto di partenza del Tab in fondo alla pagina (falso 404 con un solo Tab).
- Heredoc della shell con variabili JS (`$dy`) dentro script Playwright: la shell le sostituisce; usare `<<'EOF'`.

## Problemi incontrati e come li abbiamo risolti

- **Prima pagina senza stili dopo `clear_cache()` di Elementor** (due falsi allarmi nei confronti): dopo ogni modifica al Kit aprire qualche pagina con `wp_remote_get` prima dei test.
- **Regola del breadcrumb degli articoli** (`li:last-child:not(:has(a))`) più specifica della variante scura: voce corrente Navy su fondo scuro in tutte le testate scure. Limitata a `.es-breadcrumb--light`.
- **Colore link del Kit** (`.elementor-kit-8 a`) batte `.es-sr-cta`: classe raddoppiata.
- **Card macchina**: il wireframe (MachineCard del design system) ha `flex:1` senza `min-width:0`; tolto `min-width:0`.
- **404/Conferma**: `min-height:64vh` del wireframe è `content-box`; sullo staging `calc(64vh + 2 * clamp(90px,16vh,180px))`.
- **Focus sotto l'header fisso** a zoom 200%: `scroll-padding-top` dallo script dell'header (header visibile + barra filtri) e scorrimento all'inizio delle card alte col focus da tastiera; ancore da `scroll-margin-top:110px` a `34px` (stessa posizione).
- **Selettore `.elementor-mark-required`** non presente sul contenitore dei campi del form: l'asterisco del consenso usa solo `.elementor-field-required`.

## Decisioni prese

- Ricerca: risultati raggruppati (macchine con il loop e la card del catalogo, settori/categorie, pagine, news, posizioni), indice per lingua in cache; scartata la pagina grezza di Hello.
- Sample Page nel cestino; singolo servizio **non si fa** (restano le card di Servizi); traduzioni EN di macchine e settori **in attesa** di chi traduce.
- Home: si segue la proposta grafica per testata (video) e sezione bottiglia (scartati: solo la sezione; tutta la Home della proposta). Numeri **attuali del sito** (1996, 2.000+, 100+, 100%), non quelli della proposta (30+, 10+/100+, 24h). Etichette in Gray 500 (il #888B8D della proposta non arriva a 4,5:1). Pulsante pausa aggiunto (WCAG 2.2.2), parallasse della proposta tenuta ma spenta con "riduci movimento".
- Pagina "Immagini sito Eurostar" per il grafico aggiornata (versione 4): tolte bottiglia e testata Home (159 file, 22 sfondi), aggiunte in "Già disponibili".

## File toccati

- `docs/handoff/ricerca-sorgenti/` (nuovo): generatore del template di ricerca, blocco dello snippet 6, CSS del Kit, traduzioni EN.
- `docs/handoff/test-finali/` (nuovo): `confronto.js` (pixel wireframe↔staging), `tastiera.js`, `interazioni.js`, `zoom.js`, sonde, report JSON, `sorgenti/snip2-tastiera.js`, README.
- `docs/handoff/home-sorgenti/`: `gen_home.py` (widget bottiglia `hm-bottle-img` e shortcode `hm-hero-video`), `snip6-home-bottiglia.php`, `kit-home-bottiglia.css` (nuovi).
- `docs/handoff/news-sorgenti/kit-news.css`: breadcrumb limitato alla variante chiara.
- `wordpress/snippets/2-…`, `4-…`, `6-…` riesportati; `wordpress/kit-custom-css.css` (nuovo: copia completa del CSS del Kit).
- `acf-elementor-mapping.md`: sezioni Ricerca, Test finali (pixel, tastiera, zoom), Home con video e bottiglia, decisioni 30/09.
- Staging: template 855/857; pagine 740/742; snippet 2, 4, 6; Kit; classi `es-sec-404`/`es-sec-confirm`; media 861–865; Sample Page (ID 2) nel cestino. Copie di sicurezza in opzioni `es_*_backup_*` (elenco nella specifica).

## Dove vogliamo andare

1. Verifica la nuova sezione della Home contro la proposta: adatta `docs/handoff/test-finali/confronto.js` usando come riferimento la proposta (`Drive/…/Ilaria/Eurostar Sito PROPOSTA 1/Eurostar Homepage.dc.html`, servita con `python3 -m http.server` nella sua cartella) a 1440/1280/1000/768/390, poi `tastiera.js`, `interazioni.js` (aggiungi pausa/riproduci del video) e `zoom.js` sulla Home IT ed EN.
2. Quando l'utente lo chiede: guidarlo nello scanner Ally e in VoiceOver; caricare le traduzioni EN di macchine e settori quando arrivano (procedura EN handoff 6/7).

## Da sapere prima di toccare qualcosa

- **Comunicare in italiano.** Vale il "Da sapere" dei handoff 6, 7 e 8 (Novamira, procedura EN, variabili globali, link di tipo query, classi `.elementor .etichetta`).
- **Playwright senza installazioni**: `PW` = `playwright-core` del tema marienklinik in `~/Local Sites/…/node_modules/playwright-core`, `CHROME` = `~/Library/Caches/ms-playwright/chromium_headless_shell-1223/chrome-headless-shell-mac-arm64/chrome-headless-shell` (dettagli in `docs/handoff/test-finali/README.md`). Il banner cookie va rifiutato ("Rifiuta" in IT, **"Deny"** in EN) e la pagina ricaricata.
- Nel CSS del Kit usare selettori `.elementor .classe.classe` (0,3,0) per battere classi globali e loro varianti mobile.
- La Home dipende dallo script dentro `[es_home_hero_video]` (snippet 6): se si toglie lo shortcode, sparisce anche il posizionamento di bottiglia, frase e numeri.
- Dopo modifiche strutturali alla Home IT rifare la EN (make_duplicate → traduzioni del pacchetto → `wpml_pb_finished_adding_string_translations` → titolo/slug "Home"/`home` → cache condizioni in richiesta separata).
- Avvisi `gzuncompress()` di WPML innocui. Non inviare form di prova senza chiedere.
