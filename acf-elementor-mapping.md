# Mappatura ACF + Elementor Pro — Categoria macchina, Categoria settore, Scheda macchina

Fonte di verità unica per lo sviluppo WordPress dei tre archetipi dinamici del catalogo — riferimento da dare in pasto a Novamira: ogni blocco del wireframe ha qui una controparte 1:1 in un campo ACF + widget Elementor Pro nativo, verificata contro i file HTML realmente pubblicati (non solo contro l'Excel). Nessuna soluzione custom dove esiste già un widget nativo. Dati macchina/settore aggiornati secondo `Eurostar - Schema dati catalogo (per PM).xlsx` (15-09), che sostituisce a sua volta `SEZIONE MACCHINE.xlsx` e `SETTORI MACCHINE 14-09-26.xlsx`, e secondo le modifiche testuali di `Modifiche sito 19.9` (Serena).

## Principio guida

Dove il numero di elementi è **fisso e noto** (contenitori, stats, download) si usano campi ACF singoli o checkbox, non repeater — mantiene la struttura predicibile e i template Elementor più semplici da mantenere. Il repeater è riservato ai soli casi in cui il contenuto è realmente variabile in numero. Dove lo schema del PM differenzia i campi **per tipologia** (es. la tabella "Caratteristiche principali" ha 5 campi per le Riempitrici, 2 per le Tappatrici, 2 per le Sciacquatrici/Soffiatrici, nessuna per i Sistemi movimentazione contenitori), serve un field group ACF condizionale per tassonomia — non un unico group uguale per tutto il CPT.

---

## 1. Categoria macchina (`taxonomy-macchina-*.html`)

Fonte: `Eurostar - Schema dati catalogo (per PM).xlsx`, foglio **Categoria macchina** (ordine voci catalogo, intro, campi preview per tipologia).

CPT/tassonomia: termine della tassonomia **Categoria macchina** collegata al CPT `macchina` (Riempitrici, Tappatrici, Sciacquatrici/Soffiatrici, Sistemi movimentazione contenitori, Etichettatrici*, Linee complete, Usate). WordPress permette di agganciare campi ACF a un termine di tassonomia (Field Group con location "Taxonomy Term" = Categoria macchina): non serve un CPT separato.

| Blocco wireframe | Campo ACF | Widget Elementor Pro nativo | Note |
|---|---|---|---|
| H1 / intro categoria | Nome = term name nativo; intro = ACF Text (term field) | Heading / Text con Dynamic Tag → Taxonomy Term Field | |
| Ordine nel menu/catalogo | ACF Number (term field) | — (governa solo l'ordinamento della query, non un widget) | Dall'Excel: 1 Sciacquatrici/Soffiatrici, 2 Riempitrici, 3 Tappatrici, 4 Linee complete, 5 Usate — "Sistemi movimentazione contenitori" è una categoria aggiunta dopo lo schema Excel, ordine da confermare col PM |
| "La gamma {categoria}" — griglia macchine | Nessun campo diretto: relazione automatica (tutti i post CPT `macchina` con questo termine) | Loop Grid filtrato per tassonomia | |
| Link alla categoria — **due comportamenti diversi** | — | Mega-menu Macchine e footer → link diretto alla pagina della singola categoria (archivio per quel termine di tassonomia). Tab categoria dentro `archive-macchine.html` (catalogo generale) → **non sono link**, filtrano in pagina la stessa Loop Grid senza reload (vedi riga "Filtri catalogo/tab categoria" in sez. 02) | Le due cose sembrano ridondanti ma non lo sono: dal mega-menu/footer l'utente arriva già sulla pagina categoria filtrata; dal catalogo generale filtra senza uscire dalla pagina. Eccezione: i tab "Linee complete" e "Usate" nel catalogo restano link alla pagina dedicata anche lì, perché non hanno CPT Macchina sotto da filtrare |
| Campi mostrati sulla preview card | Nessun campo term-level: la preview pesca 2 dei campi già definiti sulla Scheda macchina (sez. 3), scelti per tipologia | Loop Item Template con Dynamic Tag condizionale | Confermato dal cliente (Modifiche sito 19.9): **Riempitrici** → nome, tipologia, prodotto da riempire, campo di produzione gamma; **Tappatrici** → nome, tipologia, tipo di chiusura, campo di produzione gamma; **Sciacquatrici/Soffiatrici** → contenitori, campo di produzione gamma (ora 2 macchine: MEC SI + Twist Rinser); **Sistemi movimentazione contenitori** → nessun campo dati strutturato da pescare (la scheda macchina non ha Caratteristiche), la preview card usa solo nome + sottotitolo — 4 varianti di Loop Item Template |
| Categoria "Linee complete" | Solo descrizione + layout PDF (ACF Text/Wysiwyg + File field), **nessuna galleria e nessuna scheda tecnica** | Text Editor + Icon Box/Button per il PDF | Esplicitamente diversa dalle altre categorie nello schema Excel — non ha CPT `macchina` sotto, resta pagina a contenuto statico/term description. Testo definitivo del cliente impaginato (titolo, intro, claim di chiusura) |
| Categoria "Sistemi movimentazione contenitori" | Nessuno schema nell'Excel — categoria aggiunta dopo la consegna del file | Loop Grid filtrato per tassonomia, come le altre | 3 macchine (Sistema Neck Handling, Stelle universali, Stelle a geometria variabile), nessuna con scheda tecnica o CAD: solo descrizione + galleria, vedi sez. 3 |
| Categoria "Usate" | Non specificata nello schema Excel | — | Da chiarire con il PM se segue lo schema Riempitrici/Tappatrici/Sciacquatrici/Soffiatrici o ha una struttura propria |

---

## 2. Categoria settore (`taxonomy-settore*.html`)

Fonte: `Eurostar - Schema dati catalogo (per PM).xlsx`, foglio **Categoria settore** (ordine, nome, intro hero, 2 paragrafi sfide, fino a 3 macchine correlate per settore) — testi allineati alla revisione del 19-09 (Modifiche sito 19.9).

CPT: **Settore** come CPT singolo (non termine di tassonomia) — decisione già presa in seq 1 del progetto, perché il contenuto per settore è troppo ricco per un semplice termine (intro, due paragrafi di sfide, relazione a macchine multiple).

| Blocco wireframe | Campo ACF | Widget Elementor Pro nativo | Note |
|---|---|---|---|
| H1 / intro (hero) | Nome = post title; intro = ACF Text | Heading / Text con Dynamic Tag → ACF Field | Colonna E dell'Excel |
| Ordine di visualizzazione | ACF Number | — (governa l'ordinamento in archivio/menu) | Colonna A: 1 Acqua, 2 Bevande e succhi, 3 Birra, 4 Vino, 5 Liquori, 6 Olio alimentare, 7 Alimenti e condimenti, 8 Cosmetica, 9 Detergenza, 10 Chimico/farmaceutico/sanitario |
| "Le sfide del settore" | ACF Wysiwyg o 2 campi Text (un campo per paragrafo) | Text Editor con Dynamic Tag | Colonna F, sempre 2 paragrafi nell'Excel per tutti i 10 settori |
| "Le nostre macchine" | ACF Relationship multipla → CPT Macchina | Loop Grid pilotato dalla relazione (non da tassonomia) | Popolata dal foglio "Macchine per settore": fino a 3 macchine EUROSTAR per settore, tutte già coperte dalle 17 schede reali coperte dallo schema Excel (Riempitrici/Tappatrici/Sciacquatrici/Soffiatrici — le 3 macchine di Sistemi movimentazione contenitori non hanno relazione a Settore) |
| Blocco Configurazioni/Contenitori/Chiusure | Non presente nell'Excel per nessun settore tranne il testo storico del wireframe su Vino | — | Omesso nelle 9 pagine settore create dai dati Excel — testo specifico-vino (chiusure in sughero) non generalizzabile senza dati forniti dal PM |

---

## 3. Scheda macchina (`single-macchina*.html`)

Fonte: `Eurostar - Schema dati catalogo (per PM).xlsx`, fogli **Macchina - Riempitrici / Tappatrici / Sciacquatrici / Squadron / Sistemi di movimentazione** (una riga per macchina reale, dati verificati contro il wireframe pubblicato).

| Blocco wireframe | Campo ACF | Widget Elementor Pro nativo | Note |
|---|---|---|---|
| H1 / tagline | Titolo macchina = post title nativo del CPT `macchina`; tagline = ACF Text | Heading / Text Editor con Dynamic Tag → ACF Field | Il titolo resta il post title, nessun ACF necessario |
| Tag "Adatta per" | Riempitrici: nessun campo ACF, tassonomia **Settore** assegnata al post — valorizzata leggendo la colonna "Adatta per (settore)" dello schema Excel per macchina, non a discrezione. Tappatrici: ACF Text/Checkbox a scelte fisse (tipo di tappo — non esiste una mappatura a settore per questa tipologia nell'Excel). Sciacquatrici/Soffiatrici: campo assente, blocco omesso in pagina. Sistemi movimentazione contenitori: campo assente | Widget nativo Post Info (tipo "Terms") per le Riempitrici; Icon Box/Text per le Tappatrici | Il significato del campo cambia per tipologia, non è un'unica sorgente dati per tutte le macchine |
| Icone "Contenitori" | ACF Checkbox a scelte fisse (Vetro, Plastica, Lattina…) — **solo Riempitrici e Sciacquatrici/Soffiatrici** | Icon Box ripetuti in pagina, ciascuno con Display Conditions legata al valore checkbox | Su Tappatrici il campo "Contenitori" è stato rimosso ovunque (header e tabella Caratteristiche) su richiesta esplicita del cliente — non va registrato in ACF per quella tipologia. Set chiuso → basta la visibilità dinamica nativa, non serve loop |
| Stats strip in testata | ACF Group a schema fisso **per tipologia**, valori duplicati da "Caratteristiche principali": Riempitrici 3 campi (Velocità, Prodotto, Tipo riempimento); Tappatrici 1 campo (Contenitori/ora); Sciacquatrici/Soffiatrici 2 campi (Velocità, Contenitori) | 1-4x StatBlock/Counter con Dynamic Tag | Non è un campo indipendente: rispecchia un sottoinsieme dei campi della tabella sottostante, per dare risalto visivo ai dati più cercati. Su Tappatrici "Tipologia chiusura" è stata tolta dalla testata (resta solo in tabella) e "Velocità" è stata rinominata "Contenitori/ora", su indicazione esplicita del cliente. Il campo "Materiale a contatto prodotto" **non va inserito**: risposta definitiva del cliente al punto aperto del PM (per 9 riempitrici su 10 duplicava il valore di "Contenitori") |
| Tabella "Caratteristiche principali" | ACF Group a schema fisso **per tipologia**: Riempitrici 4 campi (Tipo riempimento, Contenitori, Prodotto, Velocità — "Materiale a contatto" escluso su indicazione del cliente); Tappatrici 2 campi (Tipologia chiusura, Contenitori/ora — rinominato da "Velocità", "Contenitori" rimosso); Sciacquatrici/Soffiatrici 2 campi (Contenitori, Bottiglie/ora); Sistemi movimentazione contenitori: **nessuna tabella Caratteristiche** | Table widget nativo, o righe ripetute con Dynamic Tag | Il numero e il nome dei campi sono fissi ma diversi per Categoria macchina, quindi conviene un field group ACF condizionale per tassonomia, non un unico group per tutto il CPT. Non esiste e non va aggiunto un campo "Diametro-altezza contenitore" per le Sciacquatrici/Soffiatrici: richiesta esplicita del cliente di escluderlo, e non è mai stato presente nello schema Excel |
| Download center (PDF) | 1 ACF File field (solo scheda tecnica) per Riempitrici/Tappatrici/Sciacquatrici/Soffiatrici; **nessun Download center** per Sistemi movimentazione contenitori | Icon Box / Button con Dynamic Tag → URL del file | Formato scheda tecnica corretto da DWG a PDF. L'allegato CAD ("Layout CAD di ingombro") è stato rimosso su richiesta del cliente per tutte le Riempitrici, Tappatrici e Sciacquatrici/Soffiatrici: non va registrato come campo ACF per queste tipologie. Box "Dati chiave" e riferimento a persona specifica sono stati rimossi dal modulo preventivo |
| Modulo preventivo | — | Widget nativo Form | Nessun campo ACF "referente commerciale" nominativo: il form non preseleziona più una persona specifica |

### Varianti macchina MEC ISO — deciso

Il foglio INFO CATALOGO MACCHINE elenca 6 varianti della famiglia **MEC ISO** (S, PS, SL, PSL, FS, DPS) con specifiche pressoché identiche tranne il nome. Risposta definitiva del cliente al punto aperto del PM: **un solo post** CPT Macchina, non 6 post distinti. Le 6 varianti vanno gestite con un campo ACF aggiuntivo "Varianti disponibili" (repeater o testo) sulla singola scheda `single-macchina-mec-iso.html`, non con 6 field group/post separati.

### Blocchi rimossi (non più nel wireframe, non serve mapparli)

Il blocco "Come funziona" (griglia a 4 card) sulle pagine categoria, i blocchi "Progettata internamente" e "Focus macchina" sulla scheda macchina, i tab "Dati tecnici"/"Contenitori" (residuo di design, mai funzionanti) e il caption ridondante "Dati tecnici di performance" sopra la tabella Caratteristiche sono stati rimossi perché non previsti dallo schema IMPOSTAZIONE CATALOGO MACCHINE — non richiedono field group ACF.

---

## Applicabilità e stato

Per il CPT Macchina la struttura è validata su tutte e 4 le tipologie coperte dallo schema dati (21 istanze reali: 10 Riempitrici, 6 Tappatrici, 2 Sciacquatrici/Soffiatrici — MEC SI e la nuova Twist Rinser — e 3 Sistemi movimentazione contenitori, categoria aggiunta dopo la consegna del primo Excel). Per il CPT Settore è validata su tutti e 10 i settori di lancio, con tutti i testi allineati all'ultimo Excel "Schema dati catalogo" del 19-09. Restano fuori dallo schema — quindi non ancora mappati come CPT qui — **Squadron** e **Linee complete**: per Squadron, ATHENA ed EXACTA hanno dati tecnici reali e sono ora impaginati come contenuto statico nella pagina `page-squadron.html` (non ancora come post CPT Macchina con pagina singola dedicata); il resto della gamma Squadron (Olympia A/SA, Olympia AV A/SA, VOL, VOL.L Grandi formati, Easykeg, Evox/Evox Plus, Riempitrice a peso, Dosatore volumetrico) ha solo i settori di applicazione noti, nessun dato tecnico strutturato — impaginato come card sintetiche, non come schede complete.

Questa stessa logica di mappatura (fisso → campo singolo/checkbox + widget statico; variabile → repeater + Loop Grid; condizionale per tipologia dove lo schema lo richiede) va replicata per gli altri archetipi del prototipo (`single-servizio.html`, `single-news-articolo.html`, `single-news-editoriale.html`) quando il PM fornirà uno schema Excel equivalente per quei contenuti.
