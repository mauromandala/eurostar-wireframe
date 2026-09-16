---
type: handoff
date: 2026-09-16
status: in corso — bloccato in parte sulle risposte del PM
seq: 4
prev: docs/handoff/2026-09-16-wireframe-eurostar-3.md
tags: [eurostar, wireframe, documentazione-wordpress, modifiche-cliente]
---

## Obiettivo

Portare il wireframe Hi-Fi statico di Eurostar a uno stato completo e validato — testi definitivi approvati dal cliente, design brand integrato, documentazione tecnica affidabile — così che sia pronto per l'handoff a uno sviluppo reale su WordPress + Elementor Pro + ACF Pro, eseguito via Novamira. Il cliente ha tempi stretti e vuole rivedere tutto sul wireframe prima che si passi alla costruzione del sito vero.

## A che punto siamo

- Tutti i 10 settori di lancio sono popolati con contenuti reali (non solo Vino come nella seq 3), con link propagati su menu/footer/catalogo di tutte le pagine.
- `acf-elementor-mapping.md` è ora la fonte unica di verità per i 3 archetipi (Categoria macchina, Categoria settore, Scheda macchina), sostituendo gli Excel del PM come riferimento per Novamira.
- Creato e condiviso con il PM (via Google Drive, stessa cartella di Serena) un Excel di sintesi (`Eurostar - Schema dati catalogo (per PM).xlsx`) con la stessa struttura a colonne che diventerà i campi ACF — il PM continuerà a lavorare lì invece che sui suoi due file originali.
- Ricevuto un nuovo documento di modifiche di Serena ("Modifiche sito post check cliente - 02.docx"): analizzato interamente e sintetizzato in `docs/handoff/2026-09-16-sintesi-modifiche-post-check-02.md`, con distinzione tra già fatto / pronto da implementare / bloccato da materiali mancanti / in conflitto con decisioni già prese.
- Applicate tutte le modifiche "verdi" (senza conflitti) di quel documento: Servizi (percorso a 8 tappe, 4 schede servizio, blocchi lingua/garanzia), Squadron (copy definitiva di 3 sezioni), Catalogo (testi introduttivi, rename "La nostra gamma").
- Testata scheda macchina uniformata su tutte le 17 istanze (MEC LD allineata alle altre; "Tipologia chiusura" spostata in header per le Tappatrici).
- Font size dei componenti StatBlock ridotti su richiesta esplicita dell'utente dopo revisione visiva: "md" (testata scheda macchina) e "lg" (strip numeri Servizi/Referenze/News/Servizio).
- **Email già inviata al PM** con le 5 domande di chiarimento sui punti rossi della sintesi (Settori, struttura dati Riempitrici, Tappatrici, anno matricole). Si aspetta risposta.

## Cosa abbiamo provato che NON ha funzionato

- **Aggiungere una riga di testo+contatto come elemento "fratello" fuori da una card in una griglia CSS**: veniva trattata come una cella a sé nella griglia invece che finire in fondo alla card, producendo un layout visibilmente sbagliato (segnalato dall'utente con screenshot). Corretto spostando il contenuto *dentro* il div della card, dopo il paragrafo descrittivo.
- **Aggiungere una sezione standalone tra l'hero e la sezione successiva su una pagina con clip-path/posizionamento assoluto**: il banner "Hai già una macchina Eurostar?" inserito come `<section>` a sé tra l'hero (con `clip-path` diagonale) e la sezione "Progettiamo soluzioni" (con immagine posizionata `absolute`) ha rotto il layout, creando una barra grigia fuori posto. Corretto spostando il CTA *dentro* l'hero stesso, sotto i pulsanti principali — su pagine con hero "scenografici" (clip-path, elementi assoluti), non inserire mai nuove sezioni tra hero e blocco successivo: va tutto dentro l'hero o dopo il blocco successivo, mai in mezzo.
- **Usare `max-width:Nch` su un paragrafo dentro un contenitore già a larghezza massima gestita dal design system**: ripetuto due volte in sessioni diverse (prima su `taxonomy-settore.html`, poi di nuovo su `page-squadron.html` per un testo aggiunto nella stessa sessione) — il paragrafo si comprime in una colonna stretta invece di occupare il container. Attenzione a non applicare `max-width` per "abitudine" su testo nuovo se il contenitore padre ha già un `max-width` proprio.

## Problemi incontrati e come li abbiamo risolti

- **Testata scheda macchina con campi non allineati allo schema**: MEC LD (creata prima dello schema Excel) mostrava 4 campi diversi da tutte le altre 16 schede. Risolto allineandola.
- **"Tipologia chiusura" duplicata su Tappatrici**: appariva sia come tag con icona sia come StatBlock in testata con lo stesso valore. Risolto sostituendo lo StatBlock duplicato con "Tipologia chiusura" (prima assente dalla testata).
- **Campo "Materiale a contatto prodotto" ambiguo**: su 9 riempitrici su 10 ripeteva lo stesso valore di "Contenitori" (materiale del contenitore), mentre su MEC LD conteneva un dato diverso (AISI 304, materiale macchina). Non risolto — segnalato esplicitamente al PM come punto 2 nell'email di chiarimento, in attesa di risposta prima di decidere quale interpretazione è corretta.
- **Font size sproporzionati**: sia lo StatBlock "md" (testata scheda macchina, ~28-40px) sia il "lg" (strip numeri, ~40-74px) erano visibilmente troppo grandi rispetto al resto della pagina — segnalati dall'utente con screenshot. Ridotti rispettivamente a `clamp(17px,1.7vw,22px)` e `clamp(32px,4vw,59px)` (quest'ultimo -20% netto). Sono componenti condivisi: la riduzione si applica a tutte le pagine che usano quella dimensione, non solo a quella segnalata.

## Decisioni prese

- **Contraddizioni con lavoro già fatto non si risolvono in autonomia**: quando "Modifiche sito post check cliente - 02.docx" contraddiceva decisioni già prese (9 settori con Vino&Liquori uniti contro i 10 già implementati; cambio di semantica di "Adatta per"/"Velocità" sulle Riempitrici; anno matricole 1996 vs 2016; duplicazione "Tipologia chiusura" dopo il fix del giorno precedente), si è scelto di **non applicarle e chiedere conferma esplicita al PM** via email, invece di indovinare l'interpretazione corretta. Scartata l'opzione di applicare tutto alla lettera, che avrebbe rischiato di disfare lavoro già condiviso col PM nell'Excel.
- **Excel per il PM come sostituto dei suoi file originali, non come export una tantum**: si è deciso che il PM continuerà a lavorare direttamente sul nuovo file (`Eurostar - Schema dati catalogo (per PM).xlsx`), non più su `SEZIONE MACCHINE.xlsx`/`SETTORI MACCHINE 14-09-26.xlsx`. Scartata l'idea di tenere gli Excel del PM come riferimento primario e il nuovo file solo come documentazione parallela, perché avrebbe reintrodotto il rischio di disallineamento già visto due volte.
- **Il file per il PM resta su Google Drive, non su Git**: Git non offre "tempo reale" a un PM non tecnico e i diff su `.xlsx` sono illeggibili. Scartata l'idea di versionare quel file come riferimento primario in git (resta comunque copiato anche nel repo per tracciabilità, ma la copia di lavoro è su Drive).

## File toccati

- `taxonomy-settore-*.html` (9 nuovi file) — pagine settore popolate con contenuti reali dall'Excel.
- `index.html`, `archive-settori.html` e nav/footer di tutte le pagine — link ai 10 settori propagati.
- `acf-elementor-mapping.md` — riscritto per intero, ora copre i 3 archetipi con dati verificati contro il sito pubblicato.
- `handoff-wordpress-elementor.html` — sezioni 01 (palette), 03 (CPT/tassonomie), 04 (campi ACF Macchina/Settore/Categoria macchina), 06 (template) allineate; conteggio pagine corretto (44→53).
- `single-macchina.html` + 6 file `single-macchina-gemini-*`/`eagle-*` — testata uniformata, "Tipologia chiusura" spostata.
- `archive-servizi.html` — percorso 8 tappe, 4 schede servizio (contatto dentro la card, griglia 2 colonne), blocchi lingua/garanzia, strip numeri (anno matricole lasciato a 2016, in attesa di conferma).
- `page-squadron.html` — copy definitiva "Perché Squadron"/"La gamma"/"Quando passare a Eurostar", fix larghezza paragrafi.
- `home.html` — CTA "Hai già una macchina Eurostar?" spostato dentro l'hero, sotto i pulsanti, con spaziatura e layout a colonna.
- `archive-macchine.html`, `taxonomy-macchina-riempitrici/tappatrici/sciacquatrici.html` — testi introduttivi e rename "La nostra gamma".
- `_ds/.../\_ds_bundle.js` — StatBlock "md" e "lg" ridotti (componenti condivisi, non file per-pagina).
- `docs/handoff/2026-09-16-sintesi-modifiche-post-check-02.md` (nuovo) — analisi punto per punto del documento di Serena.
- `docs/handoff/Eurostar - Schema dati catalogo (per PM).xlsx` (nuovo, copiato anche su Drive) — Excel di sintesi per il PM.

## Dove vogliamo andare

Aspetta la risposta del PM all'email con i 5 punti di chiarimento (Settori 9 vs 10, semantica "Adatta per"/"Velocità" Riempitrici, "Tipologia chiusura" Tappatrici, anno matricole 1996 vs 2016). Appena arriva:

1. Applica le risposte seguendo lo stesso schema già rodato (distingui subito implementabile / bloccato / da chiarire ulteriormente).
2. Se il perimetro Settori cambia (9 invece di 10), aggiorna anche l'Excel già condiviso col PM e `acf-elementor-mapping.md` di conseguenza.
3. Se la struttura "Adatta per"/"Velocità" delle Riempitrici cambia, applicala su tutte e 10 le Riempitrici (non solo MEC LD) e aggiorna la sezione 3 di `acf-elementor-mapping.md`.

Nel frattempo, restano punti aperti indipendenti dalla risposta del PM: foto team/officina, planisfero interventi, "Volti del servizio", lingue di Elisabetta Dridini, dati completi gamma Squadron oltre EXACTA/ATHENA (Volumetriche, Alto vuoto, Olympia, Weight filler, Evox/Evox Plus, Dosatore volumetrico).

## Da sapere prima di toccare qualcosa

- Vedi handoff seq 1-3 per le convenzioni di base (commit/push automatici, verifica in Browser pane su porta 4173, nav/footer duplicati senza include, `navigate ... force:true` per bypassare la cache).
- **StatBlock è un componente condiviso** in `_ds_bundle.js` con 3 taglie (`sm`/`md`/`lg`): una modifica alla taglia si propaga a tutte le pagine che la usano, non solo a quella che si sta modificando in quel momento — verificare sempre l'impatto con `grep` prima di cambiare i valori.
- **Non inserire mai nuove `<section>` tra un hero con `clip-path` e il blocco successivo**: il layout si rompe (vedi sezione "Cosa non ha funzionato"). Contenuti aggiuntivi in quell'area vanno dentro l'hero stesso.
- Il PDF/Word "Modifiche sito post check cliente - 02.docx" usa il colore rosso nel testo per marcare le parti "nuove/da applicare" quando è presente un pattern "testo attuale > sostituire con"; il nero è usato sia per didascalie di struttura sia per contenuto normale non in formato diff — non è un segnale affidabile di "già fatto vs da fare" da solo, va sempre incrociato con lo stato reale del sito.
