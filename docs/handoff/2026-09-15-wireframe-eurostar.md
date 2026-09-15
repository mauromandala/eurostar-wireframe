---
type: handoff
date: 2026-09-15
status: in corso
seq: 1
prev: nessuno
tags: [eurostar, wireframe, wordpress-handoff, design-tokens]
---

## Obiettivo

Portare il wireframe Hi-Fi statico di Eurostar (bottling & packaging solutions) a uno stato completo e validato — testi definitivi approvati dal cliente e design brand integrato — così che sia pronto per l'handoff a uno sviluppo reale su WordPress + Elementor Pro + ACF Pro, eseguito via Novamira (plugin che collega Claude Code a WordPress con abilities dirette su ACF/Elementor/Gutenberg). Il cliente ha tempi stretti e vuole rivedere tutto sul wireframe prima che si passi alla costruzione del sito vero.

## A che punto siamo

- Tutte le pagine del sito (23 pagine + 2 nuove: Squadron, Lavora con noi) hanno i testi definitivi dal documento cliente inseriti, verificati in browser e pubblicati su GitHub Pages.
- Menu, footer e mega-menu sono allineati su tutte le pagine (Tappatrici riattivato, Etichettatrici escluso, perimetro settori a 6 voci di lancio).
- Dati di contatto ufficiali uniformati ovunque (indirizzo, email `eurostarinfo@eurostar.it` + `aftersales@eurostar.it` dedicata ai post-vendita, telefono, P.IVA/CF).
- Form Contatti e form "Lavora con noi" ricostruiti con i campi richiesti, CTA "Candidati per questa posizione" che preseleziona la posizione nel form.
- Pagina Squadron con le due macchine reali (ATHENA EP ISO, EXACTA EP ISO) e placeholder immagine.
- Palette brand di Ilaria (Proposta 1) integrata in `colors.css` e verificata visivamente su Home, Contatti, Chi siamo — nessuna rottura.
- Documento di handoff tecnico (`handoff-wordpress-elementor.html`) e riepilogo testi (`riepilogo-testi.html`) pubblicati nel repo, inclusa una sezione che mappa le abilities Novamira verificate (127 disponibili su un'installazione di prova) alle sezioni del piano di sviluppo.
- **In arrivo ora**: Mauro sta per allegare un nuovo giro di modifiche al wireframe da parte di Serena (contatto cliente) — non ancora ricevuto in questa sessione.

## Cosa abbiamo provato che NON ha funzionato

- Aprire via `WebFetch` il PDF "Referenze — Casi studio" su link SharePoint privato di Serena: risposta HTTP 403 Forbidden, richiede autenticazione che non abbiamo. Va richiesto come allegato diretto, non come link cloud privato.
- Screenshot del Browser pane per verificare sezioni scrollate in fondo pagina: più volte il pannello ha restituito schermate bianche o la vista di un tab sbagliato dopo `scroll_to` su un ref stale. La verifica strutturale via `find`/`read_page`/`get_page_text` è risultata più affidabile degli screenshot in questi casi.

## Problemi incontrati e come li abbiamo risolti

- **Margin collapse su un pannello con overlap negativo** (sezione "Progettiamo soluzioni" in home, primissima fase del lavoro sui testi): un `margin-top` negativo sul contenitore collassava con la sezione padre, coprendo il taglio diagonale della hero. Risolto con `display:flow-root` sul padre (blocca il collasso senza clippare l'overlap voluto, a differenza di `overflow:hidden`).
- **Cache del browser di anteprima**: dopo un edit, ricaricare la stessa URL a volte mostrava ancora contenuto vecchio. Soluzione: `navigate` con `force:true` o un query param (`?v=...`) per bypassare la cache.
- **`git diff` come garanzia di consegna pulita**: quando Ilaria ha rimandato i token colore, invece di fidarsi della nota di accompagnamento abbiamo fatto un `diff` riga per riga contro l'originale — ha confermato che aveva toccato solo `colors.css`, nessuna variabile rinominata. Utile rifarlo per ogni consegna futura di design.

## Decisioni prese

- **Un solo esempio popolato per macchina/settore/servizio** (MEC LD, Vino, Assistenza tecnica) invece di duplicare pagine per tutti i contenuti già pronti nel documento cliente — scelto per contenere lo sforzo finché il cliente non conferma di volerli tutti pubblicati. Scartata l'opzione di creare da subito tutte le pagine mancanti.
- **Tappatrici riattivato, Etichettatrici escluso** in footer/menu/filtri, su indicazione esplicita di Serena — scartata la linea precedente (entrambi nascosti in attesa di conferma) perché superata da una risposta più recente del cliente.
- **Perimetro settori**: ancora sospeso — nel documento cliente ci sono due indicazioni in conflitto (§5.1 propone di aggiungere Cosmetico/Chimico/Farmaceutico, il Footer conferma invece il perimetro a 6). Scartata l'idea di decidere da soli; si aspetta la risposta di Serena.
- **Consegna design solo su file di codice (CSS), non export grafici**: chiesto esplicitamente a Ilaria di non mandare Figma/screenshot ma i file token direttamente, per evitare una fase di reinterpretazione manuale in handoff.

## File toccati

- `home.html`, `page-chi-siamo.html`, `archive-macchine.html`, `taxonomy-macchina-*.html` (6 file), `single-macchina.html`, `archive-settori.html`, `taxonomy-settore.html`, `archive-servizi.html`, `single-servizio.html`, `page-referenze.html`, `archive-news.html`, `single-news-*.html` (2 file), `page-contatti.html`, `404.html`, `page-cataloghi.html`, `page-conferma.html` — testi definitivi, menu/footer allineati, dati di contatto.
- `page-squadron.html` (nuova) — pagina creata da zero, poi aggiornate le due macchine reali ATHENA EP ISO / EXACTA EP ISO.
- `page-lavora-con-noi.html` (nuova) — pagina posizioni aperte + form candidatura verso `eurostarinfo@eurostar.it`, CTA di preselezione posizione.
- `_ds/eurostar-design-system-8ffca847-51ad-4b4a-a5ee-a2536d8a406f/tokens/colors.css` — sostituito con la Proposta 1 di Ilaria (solo valori, variabili invariate).
- `riepilogo-testi.html`, `handoff-wordpress-elementor.html` (nuovi) — documentazione di stato e di handoff tecnico, pubblicati e linkati da `index.html`.
- `CLAUDE.md` (nuovo) — convenzioni di progetto, regola di auto-commit, struttura pagine, decisioni di scope.

## Dove vogliamo andare

Ricevi l'allegato con le ulteriori modifiche al wireframe da parte di Serena (Mauro sta per condividerlo). Applica le modifiche richieste seguendo lo stesso schema già rodato in questa sessione: distingui subito cosa è implementabile ora, cosa è bloccato da materiali/immagini mancanti, e cosa richiede prima un chiarimento col cliente — poi procedi con le modifiche senza blocchi, verificando ogni volta nel Browser pane prima di committare.

Dopo quel giro, i punti ancora aperti restano: risposta di Serena sul perimetro settori, PDF Referenze (richiedere come allegato diretto, non link SharePoint), materiali da Ilaria (planisfero interventi, foto team/officina) e — se e quando disponibile — il collegamento di Novamira all'installazione WordPress reale di Eurostar per iniziare l'implementazione vera e propria.

## Da sapere prima di toccare qualcosa

- **Commit + push automatici**: dopo ogni modifica va fatto `git commit` e `git push origin main` senza chiedere conferma — è una regola permanente di questo repo (vedi `CLAUDE.md`).
- Header e footer sono duplicati identici in ogni file HTML (nessun sistema di include): ogni modifica a nav/footer va propagata a mano o via script Python su tutti i file — pattern già usato più volte in questa sessione, cercare gli script inline nella cronologia se serve rifarlo.
- Verificare sempre le modifiche visive nel Browser pane (`preview_start` con nome `static`, porta 4173) prima di considerarle concluse, con `navigate ... force:true` per evitare la cache.
- Il file `Eurostar News Hi-Fi (standalone).html` (21 MB) è un backup/export legacy non linkato da nessuna pagina — ignorarlo, non è parte del sito attivo.
