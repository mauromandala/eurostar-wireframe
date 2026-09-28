---
type: handoff
date: 2026-09-28
status: in corso
seq: 4
prev: docs/handoff/2026-09-28-avvio-sviluppo-wordpress-3.md
tags: [eurostar, wordpress, elementor-atomic, header-footer, wpml]
---

## Obiettivo

Replicare in WordPress + Elementor (staging `eurostar.demoengagemint.it`) il sito del wireframe statico (`http://localhost:4173`, questo repo), preciso al pixel, accessibile WCAG 2.1 AA, bilingue IT/EN con WPML. Costruzione mista: widget atomic + classi globali dove possibile, classic/shortcode FluentSnippets dove manca l'equivalente. Specifica master: `acf-elementor-mapping.md`.

## A che punto siamo

- **Fatti e validati (IT + EN)**: scheda pilota MEC LD (template 237 / EN 248); **header** (342 / EN 426) e **footer** (344 / EN 428), condizione tutto il sito. Pixel-identici al wireframe a 1440 e 1200; footer 5/3/2 colonne; hamburger ≤1200; tastiera ok (Esc su hamburger, mega-menu, ricerca). Dettagli nella sezione "Header e footer — stato" di `acf-elementor-mapping.md`.
- **Creati per header/footer**: 9 pagine IT (254–262) + EN (352–368, slug provvisori); 6 menu IT (40–45) + 6 EN (58–63); termini EN di tutte le categorie, settori e linea Squadron; 22 classi globali `es-footer-*` + `es-header`; blocco CSS "header e footer" nel Kit (ID 8); snippet FluentSnippets `2-eurostar-header-footer.php`.
- **Stato WPML verificato da DB**: header, footer, scheda, pagine EN = traduzione completata, non da aggiornare, non duplicati; pacchetti stringhe 100%.
- **Non iniziato**: Loop Item/card, archivi, pagine, news.

## Cosa abbiamo provato che NON ha funzionato

- `wpml_pb_finished_adding_string_translations` chiamato con 2 argomenti (come nella prova del 28/09 mattina) → fatal `ArgumentCountError` in `WPML_PB_Handle_Post_Body::copy()`. Serve `do_action(..., $post_en, $post_it, [])`.
- `novamira/elementor-edit-global-class` con solo la proprietà da cambiare → **sostituisce** tutte le proprietà della variante. Passare sempre l'elenco completo (leggerlo prima dal repository `Global_Classes_Repository::make()->all()`).
- Verifica di WPML → Traduzioni dall'admin nel pannello browser: richiede login, che non posso fare io.

## Problemi incontrati e come li abbiamo risolti

- **Contenitore atomic largo 100% in una riga flex** → i contenitori atomic hanno anche `.e-con` (`width:100%`) → `width:auto` nella classe globale.
- **Loghi enormi** → `.elementor img{height:auto}` (0,1,1) batte la classe singola → classe raddoppiata nel Kit (`.es-logo-main.es-logo-main`); per e-image nel footer `max-width:none` nella classe (altrimenti l'immagine è comprimibile e cambia le larghezze delle colonne fr).
- **Colonne footer diverse dal wireframe** → `min-width:0` sulle colonne e social con `flex-wrap` riducevano il min-content → colonne `min-width:auto`, social `nowrap` sopra 1024.
- **Proprietà persa creando classi** → in PHP `$reset + [...]` tiene le chiavi di sinistra: il padding di `es-footer-bottom` era sparito.
- **Tipi Style Schema**: `order` = `number`, `grid-column` = `$$type: span`, `text-align` solo `start|center|end|justify`, `e-div-block` tag non accetta `span`.
- **Focus non entra nel campo ricerca** → Hello dà `transition: all` agli input, la visibilità ereditata è ancora "hidden" → `transition:none` sull'input + `visibility 0s` all'apertura del pannello (bug presente anche nel wireframe, sul sito corretto).
- **Mega-menu 2–3px più alto** → nel wireframe eredita il font della barra alta → `.es-mega{font:12px/1.4 Roboto}`.

## Decisioni prese

- Header come **shortcode unico** `[es_header]` dentro un contenitore atomic (scartato: nav/mega classic di Elementor Pro — nessun equivalente atomic e markup diverso dal wireframe).
- Footer **atomic con classi globali** + shortcode solo per menu/social/torna su/WhatsApp (scartato: footer tutto shortcode — meno modificabile dall'editor).
- Voci di menu = **menu WordPress** con convenzioni (classi `es-mega-*`, descrizione e attributo title per le colonne del mega) così il cliente le gestisce da Aspetto → Menu.
- Footer 1024 a 3 colonne come da specifica, **non** 5 compresse come il wireframe.
- Selettore lingua footer di WPML **disattivato**.
- Traduzioni via funzioni WPML in codice (make_duplicate + reset flag + pacchetto stringhe), non dall'editor di traduzione.

## File toccati

- `acf-elementor-mapping.md` — nuova sezione "Header e footer — stato", ✅ al punto 2 dell'ordine di costruzione (commit `7bad1e0`, pushato).
- Staging (non nel repo): Kit 8 Custom CSS, template 342/344/426/428, classi globali, menu, pagine, termini, snippet FluentSnippets 2, stringhe WPML contesto "Eurostar template" (versione `hf1`).
- Sorgenti di lavoro nella scratchpad della sessione (non persistenti): `es-header-footer.php`, `kit-css-header-footer.css`, `hf-classes.php`, `hf-templates.php`, `hf-en.php`.

## Dove vogliamo andare

1. Costruisci i **Loop Item**: 4 varianti card del wireframe + card compatta Squadron (atomic + classi globali), confrontandole al pixel.
2. Poi il **template archivio categoria macchina** (unico per le 6 categorie, `taxonomy-macchina-*.html`), l'**archivio settore** (`taxonomy-settore*.html`) e il **catalogo** (`archive-macchine.html`), ciascuno con breakpoint, tastiera e versione EN.
3. Chiudi in parallelo: pulsanti della scheda macchina → pagina Contatti (260 / EN 364) invece di `/contatti/`; redirect delle singole Squadron alla pagina Squadron; se l'utente accede all'admin nel pannello browser, verifica WPML → Traduzioni (icone, editor di traduzione footer, String Translation).

## Da sapere prima di toccare qualcosa

- Operare con Novamira (`mcp__novamira-eurostar-demoeng__mcp-adapter-execute-ability`); file grandi via `novamira/create-upload-link` + `curl -X PUT` in `wp-content/uploads/es-import/` (.txt), `include`, poi cancellare.
- Snippet FluentSnippets PHP: il codice deve iniziare con `<?php`, altrimenti viene stampato in ogni pagina.
- Stringhe custom WPML: registrarle con sorgente `'it'`; dopo `icl_add_string_translation` rigenerare il `.mo` con `\WPML\Container\make(\WPML\ST\MO\File\Manager::class)->add('Eurostar template','en_US')`.
- Pagine EN: verificarle nel browser (il loopback del server restituisce l'IT). Gli avvisi `gzuncompress()` di WPML nelle esecuzioni sono innocui.
- Dopo creare/tradurre template Theme Builder: `get_conditions_manager()->get_cache()->regenerate()`.
- Testi EN scritti da me → da far rivedere; slug EN provvisori → referente SEO (non proporre scelte SEO all'utente).
- Il pulsante tondo in basso a destra sopra WhatsApp è il banner cookie del plugin, non un bug del footer.
- Repo: commit + push su `main` automatici (CLAUDE.md), trailer `Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>`.
