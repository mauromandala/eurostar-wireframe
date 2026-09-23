---
type: handoff
date: 2026-09-23
status: pronto per revisione
seq: 1
prev: nessuno
tags: [eurostar, wordpress, wpml, multilingua, transizione]
---

## Obiettivo

Portare Eurostar dal wireframe statico Hi-Fi all'implementazione reale su WordPress + Elementor Pro + ACF Pro via Novamira. Il wireframe è ormai maturo — più giri di revisione con Serena (PM cliente) chiusi, documentazione tecnica di riferimento pronta — quindi il focus del progetto si sposta dal contenuto/layout del wireframe alla costruzione del sito vero.

## A che punto siamo

- Wireframe considerato maturo per l'handoff a sviluppo: ultimo giro di revisione cliente chiuso il 23/09 (`docs/handoff/2026-09-23-revisione-feedback-serena.md`), documentazione tecnica di riferimento pronta e aggiornata (`acf-elementor-mapping.md` mappa 1:1 CPT/tassonomie/campi ACF/widget Elementor per i 3 archetipi dinamici del catalogo; `handoff-wordpress-elementor.html` per il quadro generale).
- Preparata `docs/wpml-setup-checklist.md`: checklist di setup WPML per IT/EN, con tabella di sincronizzazione Translate/Copy campo per campo sui field group ACF esistenti (inclusa l'attenzione al relationship field settore→macchine, il punto più delicato).
- Restano 2 punti minori aperti dal giro di revisione del 23/09, **volutamente non chiusi ora** per dare priorità all'avvio dello sviluppo reale (vedi "Dove vogliamo andare" del file precedente): decisione su `single-servizio.html` (pagina orfana, non più linkata da nessuna pagina del sito) e invio dell'email di recap a Serena (bozza pronta in chat, mai salvata su file).
- Sviluppo WordPress reale: **non ancora iniziato**. L'installazione collegata a Novamira (`novamira-eurostar-demoeng`, eurostar.demoengagemint.it) risultava, all'ultima verifica in questo progetto, un'installazione WordPress sostanzialmente vuota (solo contenuti di default) — va riverificata prima di partire, potrebbe essere cambiata nel frattempo.

## Cosa abbiamo provato che NON ha funzionato

Nessuno — sessione breve, solo consulenza sulla scelta del plugin multilingua e stesura della checklist, nessuna strada scartata dopo un tentativo fallito.

## Problemi incontrati e come li abbiamo risolti

Nessuno rilevante in questa sessione.

## Decisioni prese

- **WPML confermato come plugin di traduzione**, scartate le alternative valutate (TranslatePress, Weglot): con solo 2 lingue (IT/EN, confermate dall'utente) il suo limite principale — duplicare ogni contenuto per lingua — pesa poco, e ha moduli ufficiali maturi per Elementor Pro + ACF Pro, lo stack già scelto per questo progetto. TranslatePress resta un'alternativa valida se in futuro la duplicazione dei contenuti (~24 schede macchina + 10 settori) diventasse un problema di manutenzione.
- **Nessuna automazione "a bottone" della traduzione tramite Novamira**: verificato che non esiste un'integrazione dedicata (le 127 abilities coprono ACF/Elementor/Gutenberg/Yoast/WooCommerce-check, non plugin di traduzione). Automazione possibile solo via scripting mirato (`execute-php`/`run-wp-cli`) caso per caso, non un flusso pronto — da valutare se vale lo sforzo quando si arriva a quel punto.
- **I 2 punti sospesi della revisione Serena restano aperti deliberatamente**: l'utente ha scelto esplicitamente di dare priorità all'avvio dello sviluppo WordPress reale piuttosto che chiuderli ora.

## File toccati

- `docs/wpml-setup-checklist.md` (nuovo) — checklist di setup WPML IT/EN: prerequisiti, configurazione base, trattamento di CPT `macchina`/`settore` e tassonomia `Categoria macchina`, sincronizzazione campo per campo su ACF, integrazione Elementor, stringhe di tema/form, menu/URL, SEO, traduzione automatica opzionale (ATE) con esclusione dei nomi macchina dalla coda automatica, checklist di test pre-go-live.

## Dove vogliamo andare

Avviare lo sviluppo WordPress reale, usando `acf-elementor-mapping.md` come fonte di verità per CPT/tassonomie/campi/widget — non ridiscutere la struttura da zero, è già validata contro il wireframe pubblicato.

Installare e configurare WPML **fin dall'inizio dello sviluppo**, seguendo `docs/wpml-setup-checklist.md` — non aspettare che il sito sia costruito per collegare la traduzione ai field group ACF e ai template Elementor, costa molto di più a posteriori.

Quando si torna al lato contenuti/wireframe: chiudere i 2 punti sospesi da `2026-09-23-revisione-feedback-serena.md` (decisione su `single-servizio.html`, invio email di recap a Serena).

## Da sapere prima di toccare qualcosa

- **Questo repo viene toccato in parallelo da un'altra chat** ("Eurostar: wireframe"), che ha continuato a fare commit indipendentemente più volte — inclusa una volta durante questa stessa sessione di lavoro (commit `1f98ad0`, comparso senza che questa chat ne sapesse nulla). Prima di assumere che lo stato del repo sia quello descritto nell'ultimo handoff letto, controllare sempre `git log --oneline -10` e confrontare con l'hash citato nell'handoff.
- L'installazione WordPress reale collegata a Novamira risultava vuota all'ultima verifica in questo filone di lavoro — riverificare con `discover-abilities`/`execute-php` prima di dare per scontato lo stato, potrebbe essere cambiato.
- Per le convenzioni di base del repo wireframe (commit/push automatici, verifica su `http://localhost:4173` mai `file://`, criterio nativo-Elementor-Pro-prima-del-custom) restano validi gli handoff precedenti (seq 1-6, `revisione-feedback-serena`, `lavora-con-noi-ferrero`) finché si lavora ancora sul wireframe statico.
