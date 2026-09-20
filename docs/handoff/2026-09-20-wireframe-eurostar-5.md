---
type: handoff
date: 2026-09-20
status: in corso
seq: 5
prev: docs/handoff/2026-09-16-wireframe-eurostar-4.md
tags: [eurostar, wireframe, documentazione-wordpress, revisione-ui]
---

## Obiettivo

Portare il wireframe Hi-Fi statico di Eurostar a uno stato completo e validato — testi definitivi approvati dal cliente, design brand integrato, documentazione tecnica affidabile — così che sia pronto per l'handoff a uno sviluppo reale su WordPress + Elementor Pro + ACF Pro, eseguito via Novamira. Il cliente ha tempi stretti e vuole rivedere tutto sul wireframe prima che si passi alla costruzione del sito vero. (Stesso obiettivo di fondo dell'handoff seq 4, invariato.)

## A che punto siamo

- Le 5 risposte di Serena alla mail di chiarimento (seq 4) sono arrivate e sono state applicate tutte: Settori separati (Vino/Liquori) confermati com'erano, "Adatta per"/"Velocità" Riempitrici verificati contro l'Excel (già allineati, nessuna modifica), struttura campi Tappatrici riscritta secondo lo schema esatto del cliente, anno matricole corretto a 1996.
- Applicate anche le richieste aggiuntive arrivate nella stessa mail: categoria "Sciacquatrici" rinominata in "Sciacquatrici/Soffiatrici" sitewide, nuova 7ª categoria macchine "Sistemi movimentazione contenitori" (3 macchine senza scheda tecnica/CAD: Sistema Neck Handling, Stelle universali, Stelle a geometria variabile), rimosso l'allegato CAD da tutte le 17 schede Riempitrici/Tappatrici/Sciacquatrici, pagina "Linee complete" impostata con il testo definitivo del cliente.
- Documentazione tecnica (`acf-elementor-mapping.md`, `handoff-wordpress-elementor.html`, `CLAUDE.md`, `riepilogo-testi.html`, `index.html`) riallineata a tutte le modifiche sopra, inclusi due conteggi rimasti disallineati scoperti in un secondo giro di verifica.
- Dopo la chiusura di quel blocco, sessione proseguita in modalità reattiva su segnalazioni puntuali dell'utente: CTA "Hai già una macchina Eurostar?" spostata dall'hero della Home a una top bar dedicata su Servizi; sede storica di fondazione uniformata a San Marzano Oliveto (era "Santo Stefano Belbo" in una pagina); doppia linea sotto "Caratteristiche" rimossa da tutte le 17 schede macchina; catalogo macchine (`archive-macchine.html`) e archivio News (`archive-news.html`) convertiti da tab-che-navigano a **filtro in pagina** sulla stessa griglia.
- Stabilito un criterio permanente per la documentazione WordPress+Elementor: preferire sempre widget nativi Elementor Pro + Dynamic Tag su campo ACF Pro rispetto a snippet custom, e usare l'etichetta "Custom" solo quando non esiste davvero un equivalente nativo/Pro. Verificato che la tabella componenti in `handoff-wordpress-elementor.html` già rispetta questo criterio (solo 2 righe su ~15 sono "Custom", entrambe genuinamente senza alternativa nativa).

## Cosa abbiamo provato che NON ha funzionato

- **Passare un attributo custom (`data-cat`) direttamente su un `<x-import>` di `MachineCard`**: il componente React sottostante (`_ds_bundle.js`) distrugge solo le prop esplicite che si aspetta (image, name, subtitle, stat1, stat2, href) e non inoltra nulla di arbitrario al nodo `<a>` renderizzato — l'attributo custom viene silenziosamente ignorato. Risolto avvolgendo ogni `<x-import>` in un `<div class="es-machine-item" data-cat="..." style="display:contents">`: il wrapper porta il dato per il filtro JS, `display:contents` lo rende trasparente al layout della grid CSS. Per le card News invece non è servito nessun wrapper, perché sono `<a class="es-art">` scritte a mano, non un componente — l'attributo va benissimo direttamente su di esse.

## Problemi incontrati e come li abbiamo risolti

- **Doppia linea orizzontale sotto "Caratteristiche" su tutte le schede macchina**: il div del tab "Caratteristiche" ha un `border-bottom`, e la prima riga della tabella sottostante aveva anche un `border-top` — due linee ravvicinate invece di una. Risolto rimuovendo il `border-top` solo dalla prima `<tr>` di ciascuna tabella (17 file), lasciando invariato il border-top delle righe successive che serve da separatore tra loro.
- **Tab "Casi studio" mancante su archive-news.html**: la card "Cantine Ferrari" aveva badge "Caso studio" ma i tab filtro esistenti erano solo Guide tecniche/Aggiornamenti/Fiere ed eventi — nessun tab corrispondente. Confermato con l'utente di aggiungere il 4° tab invece di nascondere la card o forzarla in una categoria sbagliata.
- **Riferimenti a "17 istanze / 16 varianti" rimasti disallineati in `handoff-wordpress-elementor.html`** dopo l'aggiunta della categoria Sistemi movimentazione contenitori (che porta il totale a 20): individuati con una seconda passata di grep dedicata dopo il primo giro di aggiornamento documentazione, corretti a 19/20.

## Decisioni prese

- **Linee complete e Usate restano tab-link (non diventano filtro) nel catalogo macchine**: a differenza di Sciacquatrici/Soffiatrici, Riempitrici, Tappatrici e Sistemi movimentazione contenitori, queste due categorie non hanno mai avuto schede macchina singole (Linee complete è testo/layout, Usate non ha uno schema dati) — quindi non c'è nulla da filtrare in griglia. Scartata l'opzione di farli comunque diventare filtro con risultato vuoto: sarebbe stata un'esperienza confusa senza nessun beneficio.
- **Riferimenti a "Canelli" in page-chi-siamo.html lasciati invariati** nonostante la richiesta di uniformare le sedi a San Marzano Oliveto: sono nomi propri di un'associazione reale (ATPICA) e di eventi locali (Assedio di Canelli, Canelli Beer Festival), non sedi Eurostar — cambiarli avrebbe reso il testo falso. Corretta solo la frase sulla nascita dell'azienda nel 1996, che citava erroneamente "Santo Stefano Belbo".
- **Barra CTA assistenza tenuta su una sola pagina (Servizi), non duplicata anche in Home**: prima esisteva solo nell'hero della Home; spostata su Servizi come top bar a richiesta dell'utente, e rimossa dalla Home per evitare il messaggio doppio.

## File toccati

- `single-macchina*.html` (17 file: Riempitrici, Tappatrici, Sciacquatrici/Soffiatrici) — struttura campi Tappatrici, rimozione CAD, rimozione doppia linea Caratteristiche.
- `single-macchina-neck-handling.html`, `single-macchina-stelle-universali.html`, `single-macchina-stelle-variabile.html` (nuovi) — le 3 macchine della categoria Sistemi movimentazione contenitori, senza scheda tecnica/CAD.
- `taxonomy-macchina-movimentazione.html` (nuovo) — pagina categoria dedicata.
- `taxonomy-macchina-sciacquatrici.html`, tutte le pagine con nav/footer — rename in "Sciacquatrici/Soffiatrici" (54 file).
- `taxonomy-macchina-linee-complete.html` — testo definitivo del cliente.
- `archive-macchine.html` — griglia completata con le 12 card mancanti (Tappatrici, Sciacquatrici, Movimentazione) avvolte in wrapper `data-cat`, tab convertiti in filtro JS in pagina.
- `archive-news.html` — stessa logica di filtro in pagina sui tab, aggiunto il tab "Casi studio".
- `archive-servizi.html` — anno matricole 1996, nuova top bar CTA assistenza sotto il menu.
- `single-servizio.html` — anno matricole 1996.
- `home.html` — rimossa la CTA assistenza duplicata dall'hero.
- `page-chi-siamo.html` — corretta la sede di fondazione a San Marzano Oliveto.
- `acf-elementor-mapping.md`, `handoff-wordpress-elementor.html`, `CLAUDE.md`, `riepilogo-testi.html`, `index.html` — riallineati a tutte le modifiche sopra, incluso il criterio "nativo Elementor Pro + ACF Pro prima del custom" e la documentazione dei due filtri in pagina.

## Dove vogliamo andare

Non c'è un blocco di lavoro pianificato in attesa: si procede in modalità reattiva, correggendo/segnalando incoerenze di contenuto o UI man mano che l'utente le nota (come in questa sessione: indirizzi, doppie linee, comportamento dei filtri). Ogni volta che si tocca qualcosa di strutturale (nuovi campi, nuovi componenti, nuovo comportamento JS), aggiornare sempre `acf-elementor-mapping.md` e `handoff-wordpress-elementor.html` in coda, applicando il criterio nativo-Elementor-Pro-prima-del-custom appena stabilito.

Restano bloccati in attesa del cliente (invariati da seq 4): testimonianza cliente, 8–10 case study Referenze, 3 articoli News, alcuni dati tecnici macchina (produttività complessiva, numero valvole, varianti MEC ISO), dati ufficiali di contatto, gamma esatta e velocità Squadron oltre EXACTA/ATHENA.

## Da sapere prima di toccare qualcosa

- Vedi handoff seq 1-4 per le convenzioni di base (commit/push automatici, verifica in Browser pane su porta 4173, nav/footer duplicati senza include, `navigate ... force:true` per bypassare la cache).
- **`MachineCard` (componente `x-import`) non inoltra attributi HTML arbitrari al nodo renderizzato**: solo image/name/subtitle/stat1/stat2/href sono supportati. Per aggiungere qualunque dato accessorio (come il filtro categoria) serve avvolgere l'`<x-import>` in un wrapper `<div style="display:contents">` con l'attributo sopra. Le card scritte a mano (es. News) non hanno questo problema.
- **Il filtro in pagina di Catalogo macchine e News è puro JS del wireframe**, non va confuso con l'equivalente reale in produzione: in Elementor Pro va fatto con Query Filter nativo collegato al campo tassonomia, non replicando lo snippet.
- **Criterio documentazione**: prima di scrivere "Custom" in `handoff-wordpress-elementor.html`, verificare sempre se esiste un widget Elementor Pro + Dynamic Tag ACF che coprirebbe il caso — "Custom" è l'etichetta di ultima istanza, non quella di default.
