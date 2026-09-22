---
type: handoff
date: 2026-09-22
status: pronto per revisione
seq: 1
prev: nessuno
tags: [eurostar, wireframe, lavora-con-noi, ferrero-careers]
---

## Obiettivo

Il cliente aveva chiesto esplicitamente (documento "Modifiche sito 19.9" di Serena) un restyling della sezione "Lavora con noi" in stile Ferrero Careers: posizioni aperte in riquadri cliccabili con pagina di dettaglio, invece dell'elenco/accordion esistente. Nella sessione del 20/09 questo era stato implementato solo a metà (accordion `<details>/<summary>` invece dei riquadri veri con pagina dedicata — vedi handoff seq 6). Questa sessione completa l'intervento come realmente richiesto dal cliente, così il wireframe rispecchia fedelmente la richiesta prima dell'handoff a sviluppo WordPress+Elementor via Novamira.

## A che punto siamo

- `page-lavora-con-noi.html` ha ora 5 card cliccabili in stile Ferrero Careers (titolo, reparto, Rif./Job ID, sede e tipo contratto con icona, "Dettagli →" con freccia cerchiata), tutte con bordo 2px nel colore action-primary del sito.
- Ogni card porta a una pagina di dettaglio dedicata (`single-posizione-lavoro-*.html`, 5 file), ciascuna con: hero con meta strip, corpo (La posizione, Attività principali, Requisiti richiesti, Requisiti preferenziali, Cosa offriamo), form di candidatura specifico per quella posizione (niente più tendina "Posizione di interesse" condivisa), bottone "Vedi tutte le posizioni aperte" per tornare all'elenco.
- Il form "Candidatura spontanea" resta solo sulla pagina elenco (decisione esplicita dell'utente), con un campo libero "Ambito di interesse" al posto della vecchia tendina.
- Solo 2 posizioni hanno contenuto reale da Serena (Area Manager, Elettricista industriale/Programmatore PLC); le altre 3 (Addetto Ufficio Tecnico, Operaio specializzato — Assemblaggio meccanico, Customer Service Specialist) sono state scritte da zero per completare la struttura su richiesta esplicita dell'utente ("aggiungi qualche altra posizione... per avere un quadro chiaro della struttura") — contenuto plausibile ma non validato dal cliente.
- Documentazione tecnica aggiornata e coerente con lo stato reale: `acf-elementor-mapping.md` (nuova sezione 4, CPT "Posizione di lavoro"), `handoff-wordpress-elementor.html` (righe 169-171 e tabella CPT), `riepilogo-testi.html`, `index.html`. Verificato a fine sessione che la documentazione riflette correttamente tutti gli aspetti strutturali.
- Tutto committato e pushato su `main` (5 commit, da `371b8eb` a `0f7ab94`).

## Cosa abbiamo provato che NON ha funzionato

- **Verificare gli stili aprendo i file `.html` direttamente da filesystem (`file://`) nel browser di anteprima**: il design system (colori, `--action-primary`, ecc.) non viene eseguito in quella modalità — tutte le variabili CSS risultano vuote e i colori appaiono neri/di default, dando l'impressione (falsa) che il CSS non funzioni. Bisogna sempre verificare sul server locale (`http://localhost:4173/...`), mai su `file://`.
- **Primo tentativo di spostare il link "Vedi tutte le posizioni aperte" fuori dalla sezione grigia**: ho sostituito il blocco con uno script Python che ha lasciato un `</div>` di troppo (HTML sbilanciato, 54 aperture/55 chiusure). Corretto in un secondo passaggio verificando il bilanciamento dei tag con un conteggio grep prima/dopo su tutti e 5 i file.
- **Mettere il divider/bottone di ritorno con `grid-column:1 / -1`** (pensando di farlo occupare tutta la riga sotto la griglia a due colonne): il cliente lo ha corretto, voleva che restasse confinato alla sola colonna della descrizione. Rimosso `grid-column:1/-1` e lasciato l'auto-placement naturale della griglia (il div senza span va a finire nella prima colonna).

## Problemi incontrati e come li abbiamo risolti

- **Bordo colorato sulle card non visibile nello screenshot di verifica**: il bordo era scritto correttamente nel CSS ma il browser di anteprima stava caricando i file via `file://`, che non esegue il design system (vedi sopra). Diagnosticato controllando `getComputedStyle` via `javascript_tool`: `--action-primary` risultava stringa vuota su `file://` e valorizzata (`#25387E`) sul server locale. Da allora, verifica sempre su `localhost:4173`.
- **Blocco grigio vuoto sotto il bottone di ritorno, prima del footer**: la sezione "Torna alle posizioni" aveva un proprio `background:var(--surface-tint)` con solo il link dentro, creando una fascia colorata ingiustificata. Risolto eliminando la sezione a sé e integrando il link come elemento della stessa griglia della scheda (con un separatore sottile `border-top`, non un intero blocco colorato).
- **HTML sbilanciato dopo la prima modifica via script** (vedi sopra, sezione "non ha funzionato"): risolto ricontrollando il conteggio di `<div>`/`</div>` e `<section>`/`</section>` su tutti e 5 i file prima di committare.

## Decisioni prese

- **5 posizioni impaginate invece di 2**: scelto di completare la struttura con 3 esempi aggiuntivi (reparti e tipi di contratto diversi: indeterminato, determinato, ibrido) per mostrare come si comporta la griglia — richiesta esplicita dell'utente. Scartata l'idea di lasciarle come card non cliccabili/segnaposto: l'utente ha poi chiesto di completarle con lo stesso layout delle altre due, per uniformità.
- **Contenuto delle 3 posizioni aggiuntive scritto internamente, non richiesto al cliente**: per rispettare il ritmo della sessione (l'utente voleva vedere subito la struttura completa). Segnalato chiaramente in `riepilogo-testi.html` e `acf-elementor-mapping.md` come "da validare con il cliente prima del lancio" — scartata l'opzione di presentarlo come contenuto definitivo.
- **Form "Candidatura spontanea" resta sulla pagina elenco**, non dentro le singole schede: decisione esplicita dell'utente, perché non è legato a una posizione specifica.
- **Sidebar del form di candidatura resa `sticky`** nella scheda di dettaglio (resta visibile durante lo scroll su desktop, `static` sotto i 900px): scelta per migliorare l'usabilità dato che la descrizione della posizione può essere lunga.

## File toccati

- `page-lavora-con-noi.html` — accordion sostituito da 5 card cliccabili stile Ferrero Careers con bordo blu; form specifico per posizione rimosso, resta solo "Candidatura spontanea" con campo libero.
- `single-posizione-lavoro-area-manager.html`, `single-posizione-lavoro-elettricista-plc.html` (nuovi in questa serie di modifiche, contenuto reale) — scheda di dettaglio con corpo completo + form di candidatura dedicato.
- `single-posizione-lavoro-addetto-ufficio-tecnico.html`, `single-posizione-lavoro-operaio-assemblaggio.html`, `single-posizione-lavoro-customer-service.html` (nuovi, contenuto scritto per la sessione) — stesse caratteristiche dei due sopra.
- `acf-elementor-mapping.md` — nuova sezione 4 "Posizione di lavoro" (CPT singolo, campi ACF, note sul cambio di pattern).
- `handoff-wordpress-elementor.html` — righe 169-171 (pattern Ferrero Careers, form per posizione, form spontanea) e riga CPT "Posizione di lavoro" nella tabella sezione 03.
- `riepilogo-testi.html` — riga "Lavora con noi" riscritta per riflettere il nuovo layout e segnalare le 3 posizioni non validate dal cliente.
- `index.html` — aggiunte le 5 schede posizione nell'elenco archetipi.

## Dove vogliamo andare

Nessun blocco di lavoro pianificato: si torna alla **modalità reattiva generale** del progetto — correggere/segnalare incoerenze di contenuto, layout o comportamento man mano che l'utente le nota, esattamente come da handoff seq 6 (`docs/handoff/2026-09-20-wireframe-eurostar-6.md`).

Quando si toccherà di nuovo qualcosa di strutturale (nuovi campi, componenti, comportamento), continuare ad aggiornare in coda `acf-elementor-mapping.md` e `handoff-wordpress-elementor.html`, criterio nativo-Elementor-Pro-prima-del-custom già in uso.

Punto aperto da tenere a mente: le 3 posizioni di esempio (Addetto Ufficio Tecnico, Operaio specializzato — Assemblaggio meccanico, Customer Service Specialist) hanno contenuto non validato dal cliente — se arriva materiale reale da Serena/PM per queste o altre posizioni, sostituirlo mantenendo lo stesso template.

## Da sapere prima di toccare qualcosa

- **Mai verificare gli stili aprendo gli `.html` via `file://` nel browser di anteprima**: il design system non si esegue e i colori/variabili CSS sembrano rotti quando non lo sono. Usare sempre `http://localhost:4173/...`.
- **Dopo qualsiasi modifica strutturale via script (sed/python) su più file HTML, ricontrollare il bilanciamento di `<div>`/`</div>` e `<section>`/`</section>`** con un conteggio grep prima di committare — in questa sessione uno script ha lasciato un tag di troppo, individuato solo con questo controllo.
- Vedi handoff seq 1-6 per le convenzioni di base restanti (commit/push automatici, `navigate ... force:true` per bypassare la cache, griglie CSS con `repeat(N,1fr)` esplicito invece di `auto-fit`).
