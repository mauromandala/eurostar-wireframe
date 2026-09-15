---
type: handoff
date: 2026-09-15
status: in corso
seq: 2
prev: docs/handoff/2026-09-15-wireframe-eurostar.md
tags: [eurostar, wireframe, wordpress-handoff, design-tokens]
---

## Obiettivo

Continuazione dell'handoff seq 1: applicare al wireframe il giro di modifiche post-check inviato da Serena (contatto cliente), ricevuto durante questa sessione come cartella "Modifiche 15-09" (2 xlsx + 1 pdf/docx) via Google Drive, e produrre l'elenco delle domande ancora aperte per il project manager.

## A che punto siamo

- Materiale di Serena analizzato integralmente (Modifiche sito post check cliente.pdf/docx, SEZIONE MACCHINE.xlsx, SETTORI MACCHINE 14-09-26.xlsx) e prodotto un report Word per il PM (fuori da questo repo, su Drive) con la stessa distinzione fatto/da chiarire.
- Applicate al wireframe tutte le modifiche non bloccate da materiali mancanti o da conflitti di scope — vedi commit `a825b44` ("Applica il giro di modifiche post check cliente (Serena, 15/09)"): Home (paesi 60+, statistica 100%, griglia macchine in evidenza), Chi siamo (lingue uniformate per tutto il team), Servizi (riscritta con "Il percorso in 7 tappe" + strip statistiche a 5 voci), Lavora con noi (bullet Area Manager), Contatti (campo Paese, riferimento WhatsApp), Squadron (rimossa dicitura form unico), Settore Vino (etichetta "Le nostre macchine"), ordine catalogo macchine sitewide, pulsante WhatsApp flottante sitewide.
- Confermato via browser locale (porta 4173) che tutte le pagine toccate renderizzano correttamente, nessun errore console.
- **Non toccato, per scelta esplicita** (vedi sezione "Decisioni prese"): perimetro settori (resta a 6 sezioni di lancio), catalogo Tappatrici tecnico completo per 7 modelli (dati pronti in SEZIONE MACCHINE.xlsx ma non impaginati, stesso approccio "un solo esempio" già in uso), restyling Lavora con noi in stile Ferrero (la pagina attuale con card + CTA "Candidati" è già un'approssimazione ragionevole, non rifatta da zero), duplicazione pagine macchina/settore aggiuntive.

## Decisioni prese

- **Non risolvere da soli il conflitto sul perimetro settori.** Il documento di Serena stesso è ambiguo: il testo prosa cita 9 settori (Vino e Liquori accorpati), mentre l'Excel "SETTORI MACCHINE 14-09-26" fornisce testo pronto per 10 settori distinti, includendo Cosmetica/Detergenza/Chimico-Farmaceutico-Sanitario — cioè proprio i 3 settori che l'handoff precedente (seq 1) segnalava come esclusi dal perimetro di lancio su indicazione del documento originale. Scartata l'idea di espandere il sito a 10 pagine settore sulla sola base dell'Excel: la domanda resta aperta per il PM/cliente, vedi elenco sotto.
- **WhatsApp button con numero segnaposto.** Il numero usato (+39 0141 856032) è lo stesso fisso già in uso nel footer per telefono/email — un prefisso 0141 è tipicamente un fisso, non detto sia abilitato a WhatsApp. Implementata comunque la UI (pulsante flottante + riferimento in pagina Contatti) con tooltip "numero da confermare", per non bloccare il lavoro strutturale in attesa del numero giusto.
- **Un solo esempio popolato anche per il nuovo contenuto Servizi.** Non creata una seconda/terza pagina `single-servizio.html` per Ricambi e Remote view: aggiornato solo il testo delle 3 card sulla pagina overview, in coerenza con la decisione già presa in seq 1 (MEC LD, Vino, Assistenza tecnica come unici esempi impaginati).

## File toccati

- `home.html`, `page-chi-siamo.html`, `archive-servizi.html`, `single-servizio.html`, `page-lavora-con-noi.html`, `page-contatti.html`, `page-squadron.html`, `taxonomy-settore.html` — modifiche di contenuto specifiche per pagina.
- Tutte le 24 pagine del sito (script Python one-off, non salvato nel repo) — swap ordine Sciacquatrici/Riempitrici in mega-menu/filtri/footer, iniezione pulsante WhatsApp flottante prima della chiusura di `</footer>`.
- `docs/handoff/2026-09-15-wireframe-eurostar-2.md` (questo file).

## Domande aperte per il project manager / cliente

1. Homepage — quale macchina Squadron inserire in evidenza nella griglia (oggi generica, nessun modello specifico)? Candidate naturali: ATHENA EP ISO o EXACTA EP ISO, già pubblicate su `page-squadron.html`.
2. Team — lingue parlate da Elisabetta Dridini (oggi "da confermare" in pagina).
3. Team — foto reali del team allargato commerciali e dell'officina (oggi solo placeholder `[IMG: ...]`).
4. Servizi — il sopralluogo (tappa 01) è sempre in presenza o anche in videochiamata quando la distanza non lo consente?
5. Servizi — l'installazione (tappa 06) è sempre seguita da tecnici Eurostar diretti o anche da agenti/distributori locali nei mercati più lontani?
6. Servizi — tempistiche indicative per le 7 fasi del percorso (anche generiche)?
7. Servizi — contenuto "Mappa interventi nel mondo": dati anonimi o clienti nominati (con quali autorizzazioni)?
8. Servizi — contenuto "Volti del servizio": chi e con quali testi/foto?
9. Contatti — il numero WhatsApp deve essere diverso dal fisso +39 0141 856032 attualmente mostrato (che potrebbe non essere abilitato a WhatsApp)?
10. Contatti — l'icona/pulsante WhatsApp deve sostituire le icone social esistenti o restare in aggiunta?
11. Contatti — per ciascun modulo del sito: email di destinazione e oggetto delle richieste, e cosa vede l'utente dopo l'invio (pagina di ringraziamento e/o email di conferma)?
12. **Settori — il conflitto già emerso in seq 1 non è ancora risolto, anzi il nuovo materiale lo rinforza**: pubblichiamo 6 settori (perimetro di lancio attuale) o 10 (testo pronto nell'ultimo Excel, inclusi Cosmetica/Detergenza/Chimico-Farmaceutico-Sanitario)? Se 10, Vino e Liquori restano pagine separate o si uniscono?
13. Settori — piccola discrepanza tra i due fogli dell'ultimo Excel sulla macchina Squadron "VOL.L Grandi formati" per i settori Cosmetica/Detergenza/Chimico-Farmaceutico-Sanitario (presente in un foglio, assente nell'altro) — da verificare se e quando quei settori entreranno in perimetro.
14. Catalogo — servono le schede tecniche complete per le altre macchine Squadron citate in "La gamma" (Volumetriche, Alto vuoto, Olympia, Weight filler, Evox/Evox Plus, Dosatore volumetrico e tappatore pneumatico)? Oggi solo Athena/Exacta sono impaginate.
15. Catalogo — confermare se il "no immagini e no schede tecniche" richiesto per le Linee complete vale anche per il campo "Allegati" della struttura standard di scheda macchina.

## Dove vogliamo andare

Girare questo elenco al project manager insieme al report Word già condiviso su Drive. Priorità più alta: il punto 12 (perimetro settori), perché condiziona una quantità di lavoro strutturale (numero di pagine, voci di menu/footer, filtri) molto maggiore degli altri punti aperti. Una volta arrivate le risposte, tornare su questo repo per: eventuali nuove pagine settore, popolamento schede macchina aggiuntive se il cliente conferma di volerle tutte pubblicate, e la sostituzione dei placeholder immagine/numero non appena i materiali arrivano.

## Da sapere prima di toccare qualcosa

- Vedi handoff seq 1 per le convenzioni di base del repo (commit/push automatici, nav/footer duplicati senza include, verifica in Browser pane su porta 4173, cache del preview).
- Lo script Python usato per le modifiche sitewide di questa sessione non è stato salvato nel repo (era one-off, usava stringhe esatte del markup); se serve rifare uno swap o un'iniezione sitewide simile, ricostruire lo script con lo stesso pattern (trova le due stringhe esatte adiacenti, sostituiscile in ordine inverso, su tutte le pagine "vere" del sito elencate in questo file).
