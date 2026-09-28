---
type: handoff
date: 2026-09-28
status: in corso
seq: 3
prev: docs/handoff/2026-09-24-avvio-sviluppo-wordpress-2.md
tags: [eurostar, wordpress, wpml, elementor-atomic, menu-order]
---

## Obiettivo

Portare Eurostar dal wireframe statico Hi-Fi all'implementazione reale su WordPress + Elementor Pro + ACF Pro via Novamira, con WPML installato fin dall'inizio (non a posteriori) per gestire IT/EN. Stesso obiettivo di fondo degli handoff precedenti della catena `avvio-sviluppo-wordpress` (seq 1-2): il wireframe è maturo, il lavoro ora è costruire il sito vero.

## A che punto siamo

- **WPML: acquisto e attivazione in corso lato utente** — non ancora installato sul sito staging (`eurostar.demoengagemint.it`). Il blocco descritto nell'handoff `modifiche-pm-25-09` (licenza/zip aggiornato mancante, gli zip su iCloud erano 3.x e incompatibili con WP 7.1) è in via di risoluzione: l'utente sta procedendo con l'acquisto/attivazione direttamente su wpml.org.
- **Aggiunta una voce esplicita alla checklist WPML**: verificare in *WPML → Support*, appena installato, se la compatibilità copre anche i widget **atomic** di Elementor Pro 4.x o solo la struttura classic/legacy — da fare **prima** della scheda pilota MEC LD, non dopo aver costruito le 21 schede macchina. Prima di questa sessione la scelta "atomic" era data per assunta nell'obiettivo dell'handoff `modifiche-pm-25-09`, senza un checkpoint esplicito di verifica.
- **Documentato in `acf-elementor-mapping.md` il meccanismo di ordinamento macchine per categoria**: risposta a una domanda diretta dell'utente su come replicare in WordPress l'ordine custom del wireframe (quello deciso dal cliente per ogni categoria).
- Registrazione CPT/tassonomie (`macchina`, `settore`, `categoria_macchina`): non ancora iniziata, resta in attesa dell'installazione WPML (ordine di lavoro già deciso in seq 2: WPML prima, CPT dopo).
- Le 7 domande aperte per Serena (da `modifiche-pm-25-09`) non sono state affrontate in questa sessione — filone indipendente, ancora in sospeso.
- **2 file modificati e non committati** in questo momento: `acf-elementor-mapping.md`, `docs/wpml-setup-checklist.md` (vedi File toccati).

## Cosa abbiamo provato che NON ha funzionato

nessuno

## Problemi incontrati e come li abbiamo risolti

nessuno

## Decisioni prese

- **Verifica atomic vs classic resa un checkpoint esplicito** nella checklist, invece di restare implicita nel controllo generico di compatibilità Elementor Pro/ACF Pro: il rischio concreto è costruire tutte le schede in atomic e scoprire solo dopo che il supporto WPML per gli widget atomic (il sistema più recente di Elementor Pro 4.x, meno maturo del classic) è incompleto. Se manca compatibilità piena, si passa a classic prima della scheda pilota, non a schede già costruite.
- **Ordine macchine per categoria: confermato `menu_order` nativo di WordPress**, non un nuovo campo ACF. Scartato un ipotetico campo numerico custom: sarebbe stato ridondante con un meccanismo già nativo di WP, già usato con lo stesso pattern per l'ordine di Categoria macchina e Settore (campo `ordine` sul termine) — coerenza nel modello dati, zero plugin di riordino aggiuntivi.
- **Recupero zip/licenza WPML lasciato esplicitamente all'utente**: richiede l'account wpml.org del cliente, a cui non c'è accesso diretto — non una scelta tra alternative, un vincolo di accesso.

## File toccati

- `docs/wpml-setup-checklist.md` (non committato): aggiunta voce in sezione 2 (righe 20-21) — verifica esplicita compatibilità atomic vs classic in *WPML → Support*, prima di procedere.
- `acf-elementor-mapping.md` (non committato): espansa la riga sul campo Ordine macchina in sezione 3 (riga 60) — spiegato il meccanismo `menu_order` + Loop Grid filtrata per categoria, e il limite noto (non basta se una macchina finisse in più categorie con ordini diversi — non è il caso dei dati Excel attuali).

## Dove vogliamo andare

Attendere conferma da parte dell'utente che l'acquisto/attivazione WPML è completata.

Appena WPML è installato sul sito:
1. Seguire `docs/wpml-setup-checklist.md` dalla sezione "1. Prima di installare" (confermare piano Multilingual CMS + moduli String Translation/Translation Management/ACF Multilingual, decidere struttura URL `/en/...`, decidere slug tradotti o identici)
2. Sezione 2: verifica compatibilità in *WPML → Support*, **incluso il nuovo controllo atomic vs classic** — se manca compatibilità piena sugli atomic, decidere lì di costruire in classic
3. Registrare CPT `macchina`/`settore`/tassonomia `categoria_macchina` da `acf-elementor-mapping.md`, marcarli "Translatable" (sezioni 3-4 della checklist)
4. Costruire la scheda pilota MEC LD (template + classe hover `es-btn` da verificare sul markup effettivo dei widget, atomic o classic a seconda dell'esito del punto 2)

In parallelo, quando si riprende il lato contenuti: le 7 domande aperte a Serena da `modifiche-pm-25-09` (card Servizi cliccabili, capacità ATHENA/EXACTA, valvole S/PS/DPS, Neck Handling, filtro News "Casi studio", errore PDF Twist Rinser "mod. MEC SI", schede Squadron mancanti).

## Da sapere prima di toccare qualcosa

- **WPML non è ancora installato** al momento di questo handoff — non dare per scontato che i passi 3+ della checklist siano già eseguibili, verificare prima lo stato reale del plugin (`wp plugin list` via l'ability Novamira `run-wp-cli`, non `execute-php`).
- **2 modifiche non committate** in questo repo (vedi File toccati) — fare `git status` prima di assumere che l'ultimo commit (`0fd85c3`) rifletta lo stato attuale dei file.
- Il repo può essere toccato in parallelo da un'altra chat ("Eurostar: wireframe") — controllare sempre `git log --oneline -5` prima di assumere che lo stato descritto qui sia ancora quello attuale.
- Per le convenzioni di base del repo wireframe (preview su `http://localhost:4173` mai `file://`, criterio nativo-Elementor-Pro-prima-del-custom, server preview già attivo sulla 4173) restano validi gli handoff precedenti della catena.
