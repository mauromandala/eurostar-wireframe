---
type: handoff
date: 2026-09-29
status: in corso
seq: 5
prev: docs/handoff/2026-09-28-avvio-sviluppo-wordpress-4.md
tags: [eurostar, wordpress, elementor-atomic, archivi, pagine]
---

## Obiettivo

Replicare in WordPress + Elementor (staging `eurostar.demoengagemint.it`) il sito del wireframe statico (`http://localhost:4173`, questo repo), preciso al pixel, accessibile WCAG 2.1 AA, bilingue IT/EN con WPML. Atomic + classi globali dove possibile, shortcode FluentSnippets / widget classic dove manca l'equivalente. Specifica master e stato dettagliato: `acf-elementor-mapping.md` (sezioni "… — stato" in fondo).

## A che punto siamo

- **Fatti e validati IT + EN** (confronto al pixel con il wireframe a 1440/768/375, nessuno scroll orizzontale a 320): header, footer, scheda macchina; **card** (componenti atomic 448 macchina, 450 Squadron compatta); archivi **categoria** (490/496), **settore** (503/504), **catalogo** con filtro (512/513), **Linee complete** (524/529), **Usate** (525/532); pagine **Squadron** (258/360), **Contatti** (260/364), **Conferma** (574/586).
- **Contenuti**: 35 macchine pubblicate; intro/sfide/`titolo_cta` di categorie e settori caricati dal wireframe; 14 modelli Squadron EN creati; EN di categorie, Wine e pagine scritto da me (**da far rivedere**). Le altre macchine non hanno EN (le pagine EN mostrano solo MEC LD).
- **Form Contatti** testato con invio reale (autorizzato): redirect, Invii, email OK. Destinatario staging `mauromandala@gmail.com` (IT+EN); nome mittente Email Deliverability = "Sito Eurostar".
- **Non iniziato**: pagine Home, Servizi, Chi siamo, Referenze, Cataloghi, **Settori (elenco)**, Lavora con noi + posizioni, 404; News.

## Cosa abbiamo provato che NON ha funzionato

- Template **Loop Item** separati: in Elementor 4.3 atomic la card è un componente dentro `e-collection-loop`; 4 varianti card inutili (una card + `[es_card_stats]` per categoria).
- `posts_per_page = -1` nell'hook `elementor/query/{id}` → `LIMIT 0, -1`, nessun risultato: usare `posts_per_page 200` + `nopaging`.
- **Attributi atomic dinamici** (aria-label da shortcode): salvando da Elementor diventano "Array". Serve attributo statico + campo WPML (filtro `wpml_elementor_widgets_to_translate` a priorità 1001, poi `do_action('wpml_elementor_auto_config_clear_cache')`).
- **Form atomic** (`e-form`): niente redirect né reCAPTCHA → usato il Form classic di Elementor Pro.
- Impostare `email_reply_to` via set-content: il validatore accetta solo vuoto → scriverlo direttamente in `_elementor_data`.
- `get_term()` sul termine IT dal front-end EN: WPML lo riconverte nel termine EN → per lo slug IT query diretta su `wp_terms`.

## Problemi incontrati e come li abbiamo risolti

- **Contenitori atomic con 10px di padding** (loop, loop item, stato vuoto) → classi `es-loop`/`es-loop-item` (padding 0).
- **Righe della griglia tutte uguali** (`grid-auto-rows:1fr` del layout del loop) → `grid-auto-rows:auto` nelle classi griglia.
- **Duplicato WPML renderizzato due volte** (finito nella location "popup") → rigenerare la cache condizioni Theme Builder in una **richiesta separata** dopo `make_duplicate`.
- **Condizioni dei template duplicati** ereditano l'ID del termine IT → reimpostarle sul termine EN.
- **Duplicati WPML degli allegati senza file** → copiare `_wp_attached_file` e `_wp_attachment_metadata`.
- **Regole di widget/classi globali che battono il Kit** (`.elementor .classe` 0,2,0; form `…elementor-button[type=submit]:hover` 0,6,0) → classi ripetute nel Kit. Caso reale: testo bianco in hover su "Invia richiesta".
- **CSS del Kit non aggiornato nel browser** → ricaricare con query param nuovo dopo `files_manager->clear_cache()`.
- **Email con mittente "Email Deliverability"**: il plugin impone il suo nome → opzione `site_mailer_from_name`. Aggiunge anche Reply-To = email admin.
- Logo che finiva sotto l'hamburger < 355px → altezze loghi con `clamp()` nel Kit.

## Decisioni prese

- Ordine macchine nei settori = **ordine delle categorie** (`menu_order`), non quello per settore del wireframe (scartato: campo per settore da allineare a mano). Stessa logica per le pillole "Settori serviti" di Squadron.
- Filtri catalogo **su una riga scorrevole** a ogni larghezza, sfumature ai bordi, al clic mostra la voce successiva, sticky sotto l'header (scartato: righe a capo del wireframe).
- Una sola card macchina per tutte le categorie (scartato: 4 Loop Item).
- Squadron: link dei modelli = ancora nella pagina Squadron + redirect 302 delle singole; raggruppamenti con campo `gruppo_squadron`.
- Nuovi campi ACF: categoria `titolo`, `claim`, `titolo_cta`; settore `titolo_cta`; macchina Squadron `gruppo_squadron`.
- Form: classic Elementor Pro + honeypot; destinatario staging mauromandala@gmail.com (memoria salvata); mittente "Sito Eurostar".
- Tolti dal wireframe: pulsante "Mostra altre macchine", nota "Altri esempi di layout…", segnaposto reCAPTCHA/errore demo/nota nel form.

## File toccati

- `acf-elementor-mapping.md` — sezioni stato: Card e archivi, Catalogo, Linee complete e Usate, Squadron, Contatti e Conferma; decisioni; lacune (loghi mobile). Commit fino a `75b48b5`, pushato.
- Staging (non nel repo): snippet FluentSnippets 3 `card-macchina`, 4 `archivi-catalogo`, 5 `pagina-squadron`, 6 `pagine`; Kit 8 Custom CSS (blocchi card, archivi, catalogo, Squadron, Contatti/Conferma); ~60 classi globali `es-*`; template 490–532; componenti 448/450; pagine 258/260/360/364/574/586; campi ACF; allegati 516/517 (+ EN 527/528).
- Sorgenti di lavoro nella scratchpad (non persistenti): `es-archivi.php`, `es-squadron.php`, `es-pagine.php`, JSON delle pagine. **Fonte di verità = file sullo staging** (`wp-content/fluent-snippet-storage/`).

## Dove vogliamo andare

1. Costruisci la pagina **Settori (elenco)** (`archive-settori.html` → pagina 255 / EN "Sectors"): misura il wireframe, riusa hero e classi esistenti, card dei settori dai termini `settore` (ordine campo `ordine`), versione EN, verifica al pixel e breakpoint.
2. Poi le altre pagine del punto 5: Home, Servizi, Chi siamo, Referenze, Cataloghi, Lavora con noi + posizioni (form con destinatario mauromandala@gmail.com), 404.

## Da sapere prima di toccare qualcosa

- Operare con Novamira (`mcp__novamira-eurostar-demoeng__mcp-adapter-execute-ability`). Snippet: modificare il file in `fluent-snippet-storage` mantenendo il blocco doc, poi `Helper::cacheSnippetIndex()`; il codice deve iniziare con `<?php`. File grandi: `novamira/create-upload-link` + `curl -X PUT` in `wp-content/uploads/es-import/` e **cancellarli solo dopo aver verificato `success`**.
- Pagine: modello `elementor_header_footer`, contenitore `main` con `_cssid` `content`. Struttura atomic generata con Python in JSON e caricata con `novamira/elementor-set-content`.
- EN: `make_duplicate` + `delete_post_meta(_icl_lang_duplicate_of)` + `icl_add_string_translation` + `do_action('wpml_pb_finished_adding_string_translations', $en, $it, [])`; poi rigenerare la cache condizioni in richiesta separata. Stringhe snippet: contesto "Eurostar template", sorgente `it`, poi rigenerare il `.mo`. Verificare le pagine EN nel browser (il loopback restituisce IT).
- Link verso pagine: usare link atomic di tipo **query** (ID pagina): WPML traduce l'ID da solo.
- Non inviare form di prova senza chiedere. Testi EN miei → da far rivedere; slug EN provvisori → referente SEO. Gli avvisi `gzuncompress()` di WPML sono innocui.
- Repo: commit + push automatici su `main`, trailer `Co-Authored-By: Claude Opus 5.5 <noreply@anthropic.com>`.
