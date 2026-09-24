---
type: handoff
date: 2026-09-24
status: in corso
seq: 2
prev: docs/handoff/2026-09-23-avvio-sviluppo-wordpress.md
tags: [eurostar, wordpress, wpml, wp-cli, multilingua]
---

## Obiettivo

Portare Eurostar dal wireframe statico Hi-Fi all'implementazione reale su WordPress + Elementor Pro + ACF Pro via Novamira, con WPML installato fin dall'inizio (non a posteriori) per gestire IT/EN. Il wireframe è maturo, la mappatura tecnica (`acf-elementor-mapping.md`) è pronta: il lavoro ora è costruire il sito vero, non ridiscutere la struttura.

## A che punto siamo

- **WP-CLI ora funzionante sul VPS**: era rotto (mancava `php` nel PATH dell'utente di sistema del sito, sintomo tipico Plesk). Richiesta girata al sistemista, fix applicato (symlink/PATH verso `/opt/plesk/php/8.4/bin/php`), verificato con `wp --info` tramite l'ability Novamira `run-wp-cli` → PHP 8.4.25, WP-CLI 2.12.0, exit code 0. Questo sblocca le operazioni lunghe (search-replace dominio, import, backup) che via web venivano interrotte dal limite di 300 secondi.
- **Stato reale dell'installazione WordPress verificato** (`eurostar.demoengagemint.it`, via `wp core version`/`wp plugin list`/`wp theme list`/`wp post list`):
  - WordPress 7.1.2, tema `hello-elementor` 3.5.1 attivo
  - Plugin già attivi e corretti per lo stack previsto: **ACF Pro** 6.8.7, **Elementor** 4.2.4 + **Elementor Pro** 4.2.3, **Yoast SEO** 28.5 (più Novamira/Novamira Pro, e plugin di contorno: cache, backup, accessibilità, cookie, login limiter, image optimization, site mailer, google site kit)
  - **WPML non è installato** — nessuna traccia nella lista plugin
  - Contenuti: sostanzialmente vuoto, solo i default WordPress ("Hello world!", "Sample Page", "Privacy Policy" in bozza, una pagina Elementor #9 in bozza). Nessun CPT `macchina`/`settore` ancora registrato.
  - Diversi plugin hanno un aggiornamento disponibile (Elementor/Elementor Pro, Novamira/Novamira Pro, Image Optimization, Limit Login Attempts, Google Site Kit, Angie) — non ancora valutato se aggiornare prima di iniziare a costruire.
- Sviluppo WordPress reale (CPT/tassonomie/campi/template): **non ancora iniziato**, solo verifica dello stato di partenza in questa sessione.

## Cosa abbiamo provato che NON ha funzionato

Nessuno.

## Problemi incontrati e come li abbiamo risolti

- **WP-CLI non partiva**: lanciato dal plugin Novamira restituiva `/usr/bin/env: 'php': No such file or directory`. Causa: `php` non era nel PATH dell'utente di sistema del sito (setup Plesk, PHP vive in `/opt/plesk/php/8.4/bin/php`, non in `/usr/bin/php`). Risolto dal sistemista rendendo `php` disponibile in quel PATH. Verificato con `php -v` (8.4.25) e poi con `wp --info` lanciato tramite l'ability Novamira `run-wp-cli` (non solo dall'utente del sistemista via SSH, ma anche dal canale che useremo davvero per operare) — entrambe le verifiche confermano il fix. Se in futuro WP-CLI torna a fallire con lo stesso errore su questo server, il problema è quasi certamente lo stesso (PATH utente PHP-FPM, non l'installazione di WP-CLI).

## Decisioni prese

- **Ordine di lavoro confermato: WPML prima, CPT/tassonomie dopo** (non il contrario). L'utente ha corretto una mia ipotesi iniziale (registrare prima i CPT così WPML li trova già in *Post Types Translation*) — la scelta esplicita è installare e configurare WPML come primo passo concreto dello sviluppo reale, poi procedere con la struttura ACF/CPT.

## File toccati

Nessuno in questa sessione (solo verifiche in lettura/diagnostica via WP-CLI sul sito remoto, nessuna modifica al repo locale).

## Dove vogliamo andare

Installare e configurare WPML per primo, seguendo `docs/wpml-setup-checklist.md` a partire dalla sezione "1. Prima di installare" (confermare licenza Multilingual CMS + moduli String Translation/Translation Management/ACF Multilingual, decidere struttura URL `/en/...`, decidere se slug tradotti).

Poi registrare i CPT/tassonomie (`macchina`, `settore`, tassonomia `Categoria macchina`) da `acf-elementor-mapping.md`, e da lì seguire la sezione 3-4 della checklist WPML per marcarli "Translatable" man mano che vengono creati.

Valutare se aggiornare i plugin con update disponibile (Elementor/Elementor Pro in particolare) prima di iniziare a costruire, per non lavorare su versioni che cambiano durante lo sviluppo.

Quando si torna al lato contenuti/wireframe: restano sospesi i 2 punti da `2026-09-23-revisione-feedback-serena.md` (decisione su `single-servizio.html`, invio email di recap a Serena) — non riaperti in questa sessione, stessa scelta di priorità della sessione precedente.

## Da sapere prima di toccare qualcosa

- **WP-CLI ora funziona** tramite l'ability Novamira `run-wp-cli` (args come array, es. `["plugin", "list", "--format=json"]`) — preferirlo a `execute-php` per qualunque operazione che sia già un comando `wp` standard, invece di reinventarla in PHP.
- L'output stderr di `run-wp-cli` contiene sistematicamente 3 righe di errore innocue su abilities `mcp-adapter/*` non esistenti (rumore di logging del plugin, non un fallimento reale) — non farsi fuorviare, controllare `exit_code`/`success`, non la sola presenza di testo in stderr.
- Per le convenzioni di base del repo wireframe (commit/push automatici, verifica su `http://localhost:4173` mai `file://`, criterio nativo-Elementor-Pro-prima-del-custom) restano validi gli handoff precedenti (seq 1-6, `revisione-feedback-serena`, `lavora-con-noi-ferrero`, `avvio-sviluppo-wordpress` seq 1) finché si lavora ancora sul wireframe statico.
- Questo repo può essere toccato in parallelo da un'altra chat ("Eurostar: wireframe") — controllare sempre `git log --oneline -5` prima di assumere che lo stato descritto qui sia ancora quello attuale.
