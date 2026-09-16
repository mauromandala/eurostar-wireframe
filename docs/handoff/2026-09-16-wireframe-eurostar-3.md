---
type: handoff
date: 2026-09-16
status: in corso
seq: 3
prev: docs/handoff/2026-09-15-wireframe-eurostar-2.md
tags: [eurostar, wireframe, catalogo-macchine, wordpress-handoff]
---

## Obiettivo

Continuazione seq 2: allineare il wireframe allo schema del catalogo macchine definito da Serena in SEZIONE MACCHINE.xlsx (foglio "IMPOSTAZIONE CATALOGO MACCHINE") e SETTORI MACCHINE 14-09-26.xlsx, sia nella struttura sia nei dati reali delle singole macchine.

## A che punto siamo

Lavoro svolto in questa sessione, commit `680e123` → `b9bcf36` (10 commit, tutti pushati su `main`):

1. **Applicate le risposte di Serena** al giro di domande precedente: Settori confermati a 10 (Vino e Liquori distinti), Exacta in evidenza in home al posto della card Squadron generica, proposta di routing form (email + oggetto + redirect a pagina conferma) implementata su Contatti e Lavora con noi, fix di un pannello "Vino" duplicato nel mega-menu che avevo introdotto per errore.
2. **Rimossi i blocchi non previsti dallo schema**: sezione "Come funziona" (griglia a 4 card) da tutte le pagine categoria macchina, sezioni "Progettata internamente" e "Focus macchina" dalla scheda MEC LD, tab "Dati tecnici"/"Contenitori" non funzionanti (residuo di design), box "Dati chiave" e riferimento a persona specifica (Maria Cristina Masoero) dal modulo preventivo. Sostituita anche la griglia "Le sfide del settore" (Vino) con la prosa a due paragrafi dell'Excel.
3. **Corretti i dati macchina con la fonte reale** (SEZIONE MACCHINE.xlsx): Tappatrici aveva 6 modelli completamente fittizi (CAP RS, CAP CORK...) sostituiti con i 6 reali (GEMINI/F-IES, GEMINI/R-IES, GEMINI RF-IES, EAGLE/VA-IES, EAGLE PK, EAGLE C); Riempitrici ampliate da 6 a 10 card (aggiunte MAXIMA, MEC VOL, CANFILL, DUALFILL); Sciacquatrice rinominata da "Sciacquatrice SI" a "MEC SI"; preview card di ogni categoria ora mostrano i campi giusti per tipologia (Riempitrici: prodotto/velocità, Tappatrici: tipo chiusura/velocità, Sciacquatrici: tipo contenitore/velocità) invece dei dati generici precedenti.
4. **Scheda MEC LD allineata punto per punto allo schema**: tag "Adatta per" corretti sulla mappatura macchina-settore reale, aggiunta riga "Tipo riempimento" e poi "Velocità" nella tabella Caratteristiche principali, rimossa "Sollevamento bottiglie" (non previsto), formato allegato CAD corretto da DWG a PDF.
5. **Ridotto il font delle due caratteristiche nella card macchina** (componente `StatBlock` condiviso, size "sm"): da `clamp(22px,2.2vw,30px)` a `clamp(11px,1.1vw,13px)` — applicato a tutte le card macchina del sito, non solo a una pagina.
6. **Create 16 nuove schede singola macchina** con lo stesso schema di MEC LD, dati reali dall'Excel: 9 Riempitrici (MEC LP, MEC AV, MEC VOL, MAXIMA, MEC ISO famiglia S/PS/SL/PSL/FS/DPS, CANFILL, DUALFILL, SKILLFILL, POWERFILL), 6 Tappatrici, 1 Sciacquatrice (MEC SI). Ogni tipologia usa la tabella "Caratteristiche principali" esatta dello schema (5 campi riempitrici, 3 tappatrici, 2 sciacquatrici). Tutte le card di catalogo e pagine categoria ora hanno l'`href` corretto verso la scheda giusta (prima solo MEC LD era cliccabile).

Tutto verificato visivamente nel browser locale (porta 4173) prima di ogni commit, nessun errore console.

## Decisioni prese in questa sessione

- **Perimetro settori risolto**: Serena ha confermato di usare l'ultimo Excel come fonte, quindi 10 settori (non più 6), con Vino e Liquori come pagine/voci distinte. Questo chiude il conflitto aperto fin dalla seq 1.
- **Un solo esempio popolato per settore resta valido**: nonostante l'espansione a 10 settori nel menu/footer/archivio, la pagina di dettaglio (`taxonomy-settore.html`) resta popolata solo per Vino — stessa logica "un esempio" del resto del sito, non ancora estesa.
- **Schede macchina: la decisione "un solo esempio" è stata esplicitamente superata** su richiesta diretta — ora ci sono 17 schede popolate (MEC LD + 16 nuove) invece di una sola. Riguarda però solo Riempitrici/Tappatrici/Sciacquatrici (le tipologie coperte dallo schema IMPOSTAZIONE CATALOGO MACCHINE); Squadron e Linee complete non sono toccate da questa decisione.
- **"Adatta per" cambia significato per tipologia**, seguendo lo schema alla lettera: per le Riempitrici indica il settore industriale (Vino, Acqua...); per le Tappatrici indica il tipo di tappo gestito (non esiste un'omologa mappatura a settore nell'Excel per le tappatrici); per le Sciacquatrici il campo non esiste proprio e va omesso.

## File toccati

- Tutte le pagine categoria macchina (`taxonomy-macchina-*.html`, 6 file) e `taxonomy-settore.html`: rimozione blocchi extra-schema, dati macchina corretti.
- `archive-macchine.html`: preview card corrette, href aggiunti.
- `single-macchina.html` (MEC LD): allineata punto per punto allo schema (vedi punto 4 sopra).
- 16 nuovi file `single-macchina-{slug}.html` (elenco completo nel commit `b9bcf36`).
- `home.html`, `page-contatti.html`, `page-lavora-con-noi.html`, `page-squadron.html`, `archive-settori.html` e footer/mega-menu sitewide: espansione settori a 10, Exacta in home, routing form.
- `_ds/.../\_ds_bundle.js`: ridotto il font size dello StatBlock "sm" (componente condiviso, non un file per-pagina).

## Dove vogliamo andare

- **Squadron** (Exacta EP ISO, Athena EP ISO) e **Linee complete**: non coperte dallo schema IMPOSTAZIONE CATALOGO MACCHINE mappato finora. Da chiedere a Mauro/Serena se serve uno schema analogo per queste, o se restano come sono oggi (pagina Squadron unica con le due macchine, pagina categoria Linee complete con le 3 configurazioni).
- Verificare se l'utente vuole aggiornare anche `index.html` con i link alle 16 nuove pagine (oggi l'indice elenca solo le pagine "storiche" del sito).
- Punti aperti ereditati dalla seq 2 non ancora risolti: lingue Elisabetta Dridini, foto team/officina (Ilaria), sopralluogo/installazione/tempistiche servizio, "Volti del servizio", numero WhatsApp reale, schede tecniche complete gamma Squadron ("La gamma": Volumetriche, Alto vuoto, Olympia, Weight filler, Evox/Evox Plus, Dosatore volumetrico e tappatore pneumatico).

## Da sapere prima di toccare qualcosa

- Vedi handoff seq 1 e 2 per le convenzioni di base (commit/push automatici, verifica in Browser pane su porta 4173, nav/footer duplicati senza include).
- **Nota su questa sessione**: il lavoro descritto qui è stato fatto per errore in una chat diversa da quella dedicata al progetto ("Eurostar: wireframe") — nessun impatto sul repo (tutto è comunque commit+pushato su `main`), ma se la sessione "giusta" non ha questo contesto in memoria, questo file è il modo per recuperarlo rapidamente.
- Per rigenerare o duplicare pagine macchina in futuro, lo script Python usato in questa sessione (template-based, sostituzione blocchi esatti) non è stato salvato nel repo — era one-off in `/private/tmp/...`. Se serve rifare un'operazione simile, ricostruire lo script leggendo `single-macchina.html` come template e i dati da SEZIONE MACCHINE.xlsx.
