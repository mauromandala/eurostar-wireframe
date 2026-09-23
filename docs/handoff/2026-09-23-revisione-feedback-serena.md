---
type: handoff
date: 2026-09-23
status: in corso
seq: 1
prev: nessuno
tags: [eurostar, wireframe, revisione-cliente, cta, servizi, documentazione-wordpress]
---

## Obiettivo

Portare il wireframe Hi-Fi statico di Eurostar a uno stato validato dal cliente prima dell'handoff a sviluppo reale su WordPress + Elementor Pro + ACF Pro, eseguito via Novamira. Questa sessione copre il giro di revisione di Serena (PM cliente) sul lavoro di ieri (restyling "Lavora con noi" stile Ferrero Careers, handoff seq precedente), con l'urgenza di condividere il wireframe con il cliente finale "entro la mattina presto".

## A che punto siamo

- Tutti i punti sollevati da Serena in due giri di feedback (via screenshot ed email) sono stati verificati nel codice e risolti dove applicabile. Nessun punto resta bloccato in attesa di materiale dal cliente eccetto i loghi Referenze (Serena li recupera uno a uno nei prossimi giorni) e i 3 articoli News.
- **Bug reali trovati e corretti** (non solo richieste di stile): la card Twist Rinser non mostrava i dati per un problema nel componente MachineCard (si aspettava un oggetto `{value,label}`, non una stringa); il numero WhatsApp in Contatti mostrava per errore il numero fisso; un `</div>` orfano pre-esistente in `archive-servizi.html` sbilanciava l'HTML.
- **Terminologia "contenitori/ora" uniformata sitewide** al posto di "BPH"/"Velocità"/"Bottiglie/ora", su tutte le schede macchina, anteprime e form Contatti.
- **CTA finali generiche uniformate a "Contattaci"** su Home, Chi siamo, Squadron, Referenze, Cataloghi, Servizi (overview e single) — restano invariate le CTA contestuali specifiche (es. "Richiedi preventivo" sulle schede macchina).
- **Pagina "Linee complete"**: le 3 MachineCard segnaposto sostituite con i 2 layout tecnici reali del cliente (da PDF a immagine).
- **Pagina Servizi**: rimosso il bottone "Invia" isolato dalla card Ricambi; rimossi anche gli ultimi 2 link rimasti verso `single-servizio.html` (dalle card "Assistenza tecnica" e "Assistenza da remoto") — ora **nessuna** delle 4 card è cliccabile, su richiesta esplicita del cliente.
- **Pagina Referenze**: rimosso il paragrafo "Stiamo raccogliendo i casi studio...".
- Documentazione tecnica (`acf-elementor-mapping.md`, `handoff-wordpress-elementor.html`, `riepilogo-testi.html`, `CLAUDE.md`) aggiornata ad ogni fix strutturale, e corrette anche 2 imprecisioni pre-esistenti che non riflettevano più il codice reale (mappatura "Linee complete" e comportamento clic delle card Servizi).
- **Email di recap a Serena**: bozze scritte e via via aggiornate in chat per i fix fino al commit `d11770b`, ma non ancora inviata (l'utente non usa un tool email in questa sessione, copia/incolla manualmente) — gli ultimi 3 commit (uniformazione CTA Servizi, rimozione secondo link Assistenza tecnica) non sono ancora nella bozza.

## Cosa abbiamo provato che NON ha funzionato

Nessuno. Sessione di correzioni mirate, nessuna strada scartata degna di nota.

## Problemi incontrati e come li abbiamo risolti

- **Card Twist Rinser senza dati in anteprima**: il componente `MachineCard` (in `_ds_bundle.js`) legge `stat1.value`/`stat1.label`, quindi si aspetta un **oggetto**, non una stringa. Le altre card passano `stat1="{{ nomeVar }}"` con `nomeVar` definita come oggetto `{value,label}` in uno script `<x-dc>` a fine pagina; chi ha aggiunto Twist Rinser aveva invece scritto `stat1="Lattine in alluminio/PET"` diretto — stringa, quindi `.value`/`.label` undefined e riga vuota. Corretto in `archive-macchine.html` e `taxonomy-macchina-sciacquatrici.html` aggiungendo le variabili `trCont`/`trVel` e usando lo stesso pattern `{{ }}` delle altre card.
- **WhatsApp mostrava il numero fisso**: in `page-contatti.html` il link `wa.me/393453450371` (corretto) aveva come testo visibile `+39 0141 856032` (il fisso, sbagliato). Corretto il testo a `+39 345 345 0371`.
- **`</div>` orfano in `archive-servizi.html`**: scoperto verificando il bilanciamento HTML dopo una modifica di routine (rimozione bottone "Invia"). Con un parser Python (`html.parser`) si è isolato un `</div>` in eccesso dopo il box vuoto "Note aggiuntive" nella sezione Assistenza — preesistente, non introdotto in questa sessione. Rimosso, verificato che l'intero documento ora chiude tutti i tag.
- **Documentazione disallineata dal codice reale, due volte**: la riga "Linee complete" in `acf-elementor-mapping.md` diceva ancora "solo PDF scaricabile, nessuna galleria" dopo che il codice era già passato a un repeater di immagini; la riga sulle card Servizi in `handoff-wordpress-elementor.html` diceva "solo la prima card linka" quando in realtà 2 card puntavano allo stesso placeholder. Corrette entrambe leggendo il codice riga per riga, non fidandosi della doc precedente.

## Decisioni prese

- **Nessuna card di `archive-servizi.html` resta cliccabile** (scartata l'opzione di lasciare "Assistenza tecnica" collegata a `single-servizio.html` come unica card cliccabile): il cliente ha chiesto esplicitamente di togliere anche quel link. La pagina overview Servizi è ora un punto d'arrivo unico, senza anticipare pagine di dettaglio.
- **CTA finali generiche unificate a "Contattaci"** (scartati i testi differenziati "Parla con un commerciale", "Vieni a trovarci", "Contatta un tecnico", "Contatta il reparto post vendita"): il cliente vuole che il labeling comunichi solo l'azione (andare al modulo Contatti), non il contesto. Le CTA legate a un prodotto/servizio specifico (es. "Richiedi preventivo" sulle 21 schede macchina) restano invariate — ambito confermato esplicitamente con l'utente prima di procedere, per non uniformare troppo.
- **"Produttività complessiva, numero valvole" tolto dai punti aperti**: verificato che non è mai esistito un campo del genere nello schema Riempitrici (4 campi fissi: Tipo riempimento, Contenitori, Prodotto, Contenitori/ora). Era una nota residua di una fase precedente, non un punto aperto reale — rimossa da `riepilogo-testi.html` e `CLAUDE.md` invece di continuare a trascinarla.
- **Case study completi Referenze e gamma Squadron oltre ATHENA/EXACTA**: riclassificati da "in attesa di materiale dal cliente" a "sospesi a tempo indeterminato per scelta di scope" — distinzione confermata da Serena, importante perché cambia la natura del blocco (non tecnico, di prodotto).

## File toccati

- `home.html`, `page-chi-siamo.html`, `page-squadron.html`, `page-referenze.html`, `page-cataloghi.html` — CTA finali uniformate a "Contattaci"; footer con link Squadron mancante aggiunto su tutte le pagine col footer standard (~59 file, non elencati singolarmente).
- `page-contatti.html` — fix numero WhatsApp mostrato; label campo form "Contenitori/ora indicativi" (era "BPH...").
- `archive-servizi.html` — bottone "Invia" rimosso dalla card Ricambi; card "Assistenza tecnica" e "Assistenza da remoto" non più cliccabili; CTA finale → "Contattaci"; fix `</div>` orfano.
- `single-servizio.html` — CTA finale → "Contattaci". Ora orfana: non più linkata da `archive-servizi.html`, resta raggiungibile solo da `index.html`.
- `taxonomy-macchina-linee-complete.html` — 3 MachineCard segnaposto sostituite da 2 `<figure>` con i layout reali (`assets/layout-linea-completa-1.png`, `-2.png`, estratti dai PDF del cliente via `pdftoppm`); variabili ACF-style `ln1v/ln1p/ln2v/ln2p/ln3v/ln3p` rimosse perché orfane.
- `archive-macchine.html`, `taxonomy-macchina-sciacquatrici.html` — fix bug Twist Rinser (variabili `trCont`/`trVel` aggiunte).
- ~30 file (`single-macchina-*.html`, `taxonomy-macchina-*.html`, `taxonomy-settore-*.html`, `archive-macchine.html`) — terminologia "BPH"/"Velocità"/"Bottiglie/ora" → "Contenitori/ora" ovunque tranne `single-news-articolo.html` (articolo editoriale che spiega il termine BPH al lettore, lasciato intenzionalmente).
- `acf-elementor-mapping.md`, `handoff-wordpress-elementor.html`, `riepilogo-testi.html`, `CLAUDE.md` — allineati a ogni fix sopra; corrette 2 imprecisioni pre-esistenti (mappatura Linee complete, comportamento clic card Servizi).
- `assets/layout-linea-completa-1.png`, `-2.png` — nuovi, immagini reali estratte dai PDF forniti dal cliente.

## Dove vogliamo andare

Decidi cosa fare di `single-servizio.html`, ora orfana (raggiungibile solo da `index.html`): lasciarla come pagina di riferimento per lo sviluppo, o valutare la rimozione/un nuovo modo di mostrare un esempio di scheda servizio.

Poi aggiorna la bozza email a Serena con gli ultimi 3 fix non ancora inclusi (bottone "Invia" rimosso, CTA Servizi → "Contattaci", secondo link "Assistenza tecnica" rimosso) e mandala — è in ritardo rispetto alla richiesta di Serena di condividere il wireframe "entro la mattina presto".

## Da sapere prima di toccare qualcosa

- **Il pattern MachineCard richiede sempre variabili oggetto `{{ nomeVar }}`, mai stringhe dirette** in `stat1`/`stat2`: il componente legge `.value`/`.label`, una stringa letterale rende la riga silenziosamente vuota senza errori console. Controllare sempre che ogni nuova card segua questo pattern (variabile definita in `<script type="text/x-dc">` a fine file).
- **Verificare sempre il bilanciamento HTML dopo modifiche strutturali** con un parser vero (es. `python3 -c "from html.parser import HTMLParser..."`), non solo con `grep -o "<div" | wc -l`: quest'ultimo conta anche occorrenze in stringhe/commenti e può nascondere o falsare mismatch reali, come successo con l'orfano in `archive-servizi.html`.
- Vedi handoff seq precedenti (`docs/handoff/2026-09-20-wireframe-eurostar-6.md`, `2026-09-22-lavora-con-noi-ferrero.md`) per le convenzioni di base: commit/push automatici, verifica su `http://localhost:4173` (mai `file://`), `navigate ... force:true` per bypassare la cache, criterio nativo-Elementor-Pro-prima-del-custom nella documentazione.
