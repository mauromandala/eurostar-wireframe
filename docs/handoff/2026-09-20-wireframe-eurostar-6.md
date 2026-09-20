---
type: handoff
date: 2026-09-20
status: in corso
seq: 6
prev: docs/handoff/2026-09-20-wireframe-eurostar-5.md
tags: [eurostar, wireframe, modifiche-19-9, documentazione-wordpress]
---

## Obiettivo

Portare il wireframe Hi-Fi statico di Eurostar a uno stato completo e validato — testi definitivi approvati dal cliente, design brand integrato, documentazione tecnica affidabile — così che sia pronto per l'handoff a uno sviluppo reale su WordPress + Elementor Pro + ACF Pro, eseguito via Novamira. Il cliente ha tempi stretti e vuole rivedere tutto sul wireframe prima che si passi alla costruzione del sito vero. (Stesso obiettivo di fondo degli handoff seq 1-5, invariato.)

## A che punto siamo

- Applicato integralmente il documento "Modifiche sito 19.9" di Serena (Word + PDF) incrociato con il nuovo Excel "Eurostar - Schema dati catalogo (per PM).xlsx", in 10 fasi sequenziali (Fase 1 sostituzioni sitewide → Fase 10 documentazione), tutte concluse e pushate.
- Fasi 1-5 (sessione mattutina): sostituzioni terminologiche sitewide (CTA, "in loco", "flussimetri", Capitale Sociale, Squadron nel mega-menu), Home, Chi siamo (storia, sostenibilità, team riordinato), Squadron (nuovo posizionamento, dati reali ATHENA/EXACTA, card sintetiche resto gamma), Macchine (riordino Riempitrici, nuova macchina Twist Rinser, CTA Sistemi movimentazione).
- Fasi 6-10 (sessione pomeridiana): Settori (riordino card, 6 dei 10 testi riallineati all'Excel), Servizi (percorso riscritto, nuove sezioni Mappa interventi/Volti del servizio, WhatsApp reale sitewide), Lavora con noi (nuovo layout a box espandibili stile Ferrero), Referenze (parete loghi clienti al posto dei case study fittizi), documentazione (`acf-elementor-mapping.md`, `handoff-wordpress-elementor.html`, `index.html`, `CLAUDE.md` aggiornati; `riepilogo-testi.html` riscritto integralmente perché era rimasto a uno stadio molto più vecchio del progetto).
- Dopo la Fase 10, ulteriore giro di aggiornamento documentazione WordPress+Elementor specifico per le Fasi 6-9 (nuovi pattern: accordion Lavora con noi, parete loghi Referenze, fieldgroup "Pagina Servizi overview").
- Individuato e corretto un bug critico: un comando di test lanciato a inizio sessione (`perl -pi -e 's/foo/bar/'` su `home.html`, per verificare che perl funzionasse) aveva corrotto silenziosamente `<footer>`/`</footer>` in `<barter>`/`</barter>`, mandando in crash il rendering dell'intero footer della Home. Rimasto nascosto per ore perché non dava errori console. Corretto e verificato sia in locale sia sul sito pubblicato.

## Cosa abbiamo provato che NON ha funzionato

- **Comando di test `perl -pi -e 's/foo/bar/'` lanciato direttamente su un file reale (`home.html`)** invece che su un file temporaneo/scratchpad: ha sostituito silenziosamente "foo" con "bar" ovunque comparisse come sottostringa, corrompendo `footer` in `barter`. Lezione: qualsiasi comando di test per verificare la sintassi di uno strumento va lanciato su un file usa-e-getta nello scratchpad, mai su un file del progetto, anche se il pattern sembra innocuo.
- **`for f in $(grep -l ...)` con command substitution in un ciclo bash**: in un paio di occasioni ha fallito silenziosamente o dato errori strani (`perl` non trovava il file, o falliva a metà lista) mentre lo stesso identico comando dentro un `while IFS= read -r f; do ... done < <(grep -l ...)` funzionava sempre in modo affidabile. Da preferire sempre la seconda forma per iterare su liste di file ottenute da grep.
- **`sed`/`perl` con delimitatore `#` su pattern che contengono `href="#"`**: il carattere `#` nel testo da cercare confonde il delimitatore `s#...#...#`. Va usato un delimitatore diverso (es. `|`) quando il pattern contiene `#`.

## Problemi incontrati e come li abbiamo risolti

- **Griglia team (Chi siamo) e griglia Settori non rispettavano l'ordine a righe richiesto dal cliente**: con `grid-template-columns:repeat(auto-fit,minmax(...))` il browser infilava più elementi per riga di quanti previsti su schermi larghi (es. 5 invece di 4 nel team, rompendo "famiglia riga 1, commerciali riga 2"). Risolto forzando `repeat(4,1fr)` con breakpoint responsive dedicati, invece di lasciare decidere all'auto-fit.
- **Bug del footer Home (`<barter>`)**: vedi sopra. Root cause diagnosticata controllando `document.querySelectorAll('footer').length` (restituiva 0) e poi `git show` sui commit recenti per isolare quando il tag si era corrotto.
- **Card "Componenti originali" doveva avere un bottone "Invia" ma era wrappata in un `<a>` di card** (non si può nidificare un bottone/link dentro un altro link): risolto convertendo quella singola card da `<a class="es-svc">` a `<div class="es-svc">` con bottone esplicito dentro, seguendo lo stesso pattern già usato dalla card "Manutenzione programmata".

## Decisioni prese

- **Layout Lavora con noi con `<details>/<summary>` nativi HTML** invece di JS custom per l'espansione dei box posizione: scelto per restare senza dipendenze aggiuntive, coerente con l'approccio "keep it simple" del resto del wireframe; in produzione mappa 1:1 sull'Accordion/Toggle widget nativo di Elementor.
- **Referenze: parete loghi testuali (nome azienda) al posto di loghi immagine reali**, in attesa dei file da Serena — scartata l'opzione di lasciare la sezione case study com'era (fittizia) perché il cliente ha chiesto esplicitamente di poter lanciare subito con questa forma più semplice.
- **`riepilogo-testi.html` riscritto da zero invece che aggiornato con patch puntuali**: era troppo indietro rispetto allo stato reale (risaliva a prima della riattivazione Tappatrici, prima dei 10 settori) per poterlo raddrizzare con modifiche incrementali senza introdurre incoerenze.
- **MEC ISO: un solo post CPT con campo "varianti disponibili"**, non 6 post separati per le varianti S/PS/SL/PSL/FS/DPS — risposta definitiva del cliente al punto aperto del PM, documentata in `acf-elementor-mapping.md`.
- **Campo "Materiale a contatto prodotto" escluso dalle Riempitrici** — per 9 riempitrici su 10 duplicava il valore di "Contenitori"; risposta definitiva del cliente.

## File toccati

- `home.html` — testi Fase 2, striscia Squadron, mega-menu Athena, **fix critico tag `<footer>`**.
- `page-chi-siamo.html` — riscrittura pressoché integrale (Fase 3), fix griglia team a 4 colonne.
- `page-squadron.html` — riscrittura pressoché integrale (Fase 4), dati ATHENA/EXACTA, card gamma sintetiche.
- `taxonomy-macchina-riempitrici.html`, `archive-macchine.html`, `taxonomy-macchina-movimentazione.html`, `single-macchina-neck-handling.html`, `single-macchina-stelle-universali.html`, `single-macchina-stelle-variabile.html` — Fase 5: riordino gamma, CTA configuratore.
- `single-macchina-twist-rinser.html` (nuovo) — nuova macchina Sciacquatrici/Soffiatrici, clonata da `single-macchina-mec-si.html` e ripopolata con dati reali dall'Excel.
- `taxonomy-macchina-sciacquatrici.html` — aggiunta card Twist Rinser.
- `archive-settori.html`, `taxonomy-settore*.html` (10 file) — Fase 6: riordino card (griglia 4 colonne fisse), 6 testi riallineati.
- `archive-servizi.html`, `single-servizio.html` — Fase 7: percorso riscritto, nuove sezioni, card servizi aggiornate.
- `page-lavora-con-noi.html` — Fase 8: layout `<details>/<summary>` per le posizioni aperte.
- `page-referenze.html` — Fase 9: parete loghi al posto dei case study.
- Tutti i 54 file HTML con footer/WhatsApp — numero WhatsApp reale `+39 345 345 0371` collegato sitewide.
- `acf-elementor-mapping.md`, `handoff-wordpress-elementor.html`, `index.html`, `CLAUDE.md`, `riepilogo-testi.html` — documentazione tecnica riallineata in due passate (dopo Fase 5 e dopo Fase 9).

## Dove vogliamo andare

Nessun blocco di lavoro pianificato in attesa: si procede in **modalità reattiva**, correggendo/segnalando incoerenze di contenuto, layout o comportamento man mano che l'utente le nota — esattamente come nel giro di correzioni di questa sessione (ordine griglie, bug del footer). Nessuna fase residua dal piano Serena 19-09: tutte e 10 le fasi sono chiuse.

Ogni volta che si tocca qualcosa di strutturale (nuovi campi, nuovi componenti, nuovo comportamento JS/layout), aggiornare sempre `acf-elementor-mapping.md` e `handoff-wordpress-elementor.html` in coda, applicando il criterio nativo-Elementor-Pro-prima-del-custom già stabilito nelle sessioni precedenti.

Restano bloccati in attesa del cliente: testimonianza cliente, loghi reali e case study completi per Referenze, 3 articoli News, alcuni dati tecnici macchina (produttività complessiva, numero valvole), dati ufficiali di contatto, gamma esatta e velocità Squadron oltre EXACTA/ATHENA. Dettaglio completo in `riepilogo-testi.html`.

## Da sapere prima di toccare qualcosa

- **Mai lanciare comandi di test su file reali del progetto**, nemmeno per verificare la sintassi di uno strumento (`perl`, `sed`, ecc.) — usare sempre lo scratchpad di sessione. Il bug del footer di questa sessione è nato esattamente così.
- **Per iterare su liste di file da `grep -l` in bash, usare `while IFS= read -r f; do ... done < <(grep -l ...)`**, non `for f in $(grep -l ...)`: quest'ultima forma ha fallito silenziosamente più volte in questa sessione.
- **Con `sed`/`perl`, se il pattern di ricerca contiene `#` (es. `href="#"`), non usare `#` come delimitatore** dell'espressione `s###`: va sostituito con un altro carattere (es. `|`).
- **Qualsiasi griglia CSS che deve rispettare un ordine a righe preciso va forzata con `repeat(N,1fr)` esplicito**, non lasciata a `auto-fit`/`auto-fill`: il browser può inserire più elementi per riga di quanti previsti su schermi larghi, rompendo il raggruppamento voluto (già successo due volte: team in Chi siamo, card in Settori).
- Vedi handoff seq 1-5 per le convenzioni di base restanti (commit/push automatici, verifica in Browser pane su porta 4173, `navigate ... force:true` per bypassare la cache, `MachineCard` che non inoltra attributi HTML arbitrari).
