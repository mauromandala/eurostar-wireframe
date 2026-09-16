# Mappatura ACF + Elementor Pro — Categoria macchina, Categoria settore, Scheda macchina

Fonte di verità unica per lo sviluppo WordPress dei tre archetipi dinamici del catalogo — sostituisce i tre file Excel del PM (`SEZIONE MACCHINE.xlsx`, `SETTORI MACCHINE 14-09-26.xlsx`) come riferimento da dare in pasto a Novamira: ogni blocco del wireframe ha qui una controparte 1:1 in un campo ACF + widget Elementor Pro nativo, verificata contro i file HTML realmente pubblicati (non solo contro l'Excel). Nessuna soluzione custom dove esiste già un widget nativo.

## Principio guida

Dove il numero di elementi è **fisso e noto** (contenitori, stats, download) si usano campi ACF singoli o checkbox, non repeater — mantiene la struttura predicibile e i template Elementor più semplici da mantenere. Il repeater è riservato ai soli casi in cui il contenuto è realmente variabile in numero. Dove lo schema del PM differenzia i campi **per tipologia** (es. la tabella "Caratteristiche principali" ha 5 campi per le Riempitrici, 2 per le Tappatrici, 2 per le Sciacquatrici/Soffiatrici, nessuna per i Sistemi movimentazione contenitori), serve un field group ACF condizionale per tassonomia — non un unico group uguale per tutto il CPT.

---

## 1. Categoria macchina (`taxonomy-macchina-*.html`)

Fonte: `SEZIONE MACCHINE.xlsx`, foglio **IMPOSTAZIONE CATALOGO MACCHINE**, righe 3-4 (ordine voci catalogo + contenuto preview) e riga 7 (intestazioni per tipologia, condivise con la Scheda macchina in sezione 3).

CPT/tassonomia: termine della tassonomia **Categoria macchina** collegata al CPT `macchina` (Riempitrici, Tappatrici, Sciacquatrici/Soffiatrici, Sistemi movimentazione contenitori, Etichettatrici*, Linee complete, Usate). WordPress permette di agganciare campi ACF a un termine di tassonomia (Field Group con location "Taxonomy Term" = Categoria macchina): non serve un CPT separato.

| Blocco wireframe | Campo ACF | Widget Elementor Pro nativo | Note |
|---|---|---|---|
| H1 / intro categoria | Nome = term name nativo; intro = ACF Text (term field) | Heading / Text con Dynamic Tag → Taxonomy Term Field | |
| Ordine nel menu/catalogo | ACF Number (term field) | — (governa solo l'ordinamento della query, non un widget) | Dall'Excel: 1 Sciacquatrici/Soffiatrici, 2 Riempitrici, 3 Tappatrici, 4 Linee complete, 5 Usate — "Sistemi movimentazione contenitori" è una categoria aggiunta dopo lo schema Excel, ordine da confermare col PM |
| "La gamma {categoria}" — griglia macchine | Nessun campo diretto: relazione automatica (tutti i post CPT `macchina` con questo termine) | Loop Grid filtrato per tassonomia | |
| Campi mostrati sulla preview card | Nessun campo term-level: la preview pesca 2 dei campi già definiti sulla Scheda macchina (sez. 3), scelti per tipologia | Loop Item Template con Dynamic Tag condizionale | Dallo schema: **Riempitrici** → Prodotto + Velocità; **Tappatrici** → Tipologia chiusura + Contenitori/ora; **Sciacquatrici/Soffiatrici** → Contenitori + Bottiglie/ora; **Sistemi movimentazione contenitori** → nessun campo dati strutturato da pescare (la scheda macchina non ha Caratteristiche), la preview card usa solo nome + sottotitolo — 4 varianti di Loop Item Template |
| Categoria "Linee complete" | Solo descrizione + layout PDF (ACF Text/Wysiwyg + File field), **nessuna galleria e nessuna scheda tecnica** | Text Editor + Icon Box/Button per il PDF | Esplicitamente diversa dalle altre categorie nello schema Excel — non ha CPT `macchina` sotto, resta pagina a contenuto statico/term description. Testo definitivo del cliente impaginato (titolo, intro, claim di chiusura) |
| Categoria "Sistemi movimentazione contenitori" | Nessuno schema nell'Excel — categoria aggiunta dopo la consegna del file | Loop Grid filtrato per tassonomia, come le altre | 3 macchine (Sistema Neck Handling, Stelle universali, Stelle a geometria variabile), nessuna con scheda tecnica o CAD: solo descrizione + galleria, vedi sez. 3 |
| Categoria "Usate" | Non specificata nello schema Excel | — | Da chiarire con il PM se segue lo schema Riempitrici/Tappatrici/Sciacquatrici/Soffiatrici o ha una struttura propria |

---

## 2. Categoria settore (`taxonomy-settore*.html`)

Fonte: `SETTORI MACCHINE 14-09-26.xlsx` — foglio **SETTORI 14-09-26** (colonna A ordine, B nome, E intro, F sfide del settore) e foglio **Macchine per settore** (mapping macchina ↔ settore, righe 6-20).

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

Fonte: `SEZIONE MACCHINE.xlsx`, foglio IMPOSTAZIONE CATALOGO MACCHINE (schema campi) + foglio **INFO CATALOGO MACCHINE** (dati reali per macchina).

| Blocco wireframe | Campo ACF | Widget Elementor Pro nativo | Note |
|---|---|---|---|
| H1 / tagline | Titolo macchina = post title nativo del CPT `macchina`; tagline = ACF Text | Heading / Text Editor con Dynamic Tag → ACF Field | Il titolo resta il post title, nessun ACF necessario |
| Tag "Adatta per" | Riempitrici: nessun campo ACF, tassonomia **Settore** assegnata al post — valorizzata leggendo la colonna "Adatta per (settore)" dello schema Excel per macchina, non a discrezione. Tappatrici: ACF Text/Checkbox a scelte fisse (tipo di tappo — non esiste una mappatura a settore per questa tipologia nell'Excel). Sciacquatrici/Soffiatrici: campo assente, blocco omesso in pagina. Sistemi movimentazione contenitori: campo assente | Widget nativo Post Info (tipo "Terms") per le Riempitrici; Icon Box/Text per le Tappatrici | Il significato del campo cambia per tipologia, non è un'unica sorgente dati per tutte le macchine |
| Icone "Contenitori" | ACF Checkbox a scelte fisse (Vetro, Plastica, Lattina…) — **solo Riempitrici e Sciacquatrici/Soffiatrici** | Icon Box ripetuti in pagina, ciascuno con Display Conditions legata al valore checkbox | Su Tappatrici il campo "Contenitori" è stato rimosso ovunque (header e tabella Caratteristiche) su richiesta esplicita del cliente — non va registrato in ACF per quella tipologia. Set chiuso → basta la visibilità dinamica nativa, non serve loop |
| Stats strip in testata | ACF Group a schema fisso **per tipologia**, valori duplicati da "Caratteristiche principali": Riempitrici 4 campi (Velocità, Prodotto, Tipo riempimento, Materiale a contatto); Tappatrici 1 campo (Contenitori/ora); Sciacquatrici/Soffiatrici 2 campi (Velocità, Contenitori) | 1-4x StatBlock/Counter con Dynamic Tag | Non è un campo indipendente: rispecchia un sottoinsieme dei campi della tabella sottostante, per dare risalto visivo ai dati più cercati. Su Tappatrici "Tipologia chiusura" è stata tolta dalla testata (resta solo in tabella) e "Velocità" è stata rinominata "Contenitori/ora", su indicazione esplicita del cliente — la stessa informazione era già stata spostata in testata nel giro di modifiche precedente, quindi è un secondo cambiamento sullo stesso campo |
| Tabella "Caratteristiche principali" | ACF Group a schema fisso **per tipologia**: Riempitrici 5 campi (Tipo riempimento, Contenitori, Prodotto, Materiale a contatto, Velocità); Tappatrici 2 campi (Tipologia chiusura, Contenitori/ora — rinominato da "Velocità", "Contenitori" rimosso); Sciacquatrici/Soffiatrici 2 campi (Contenitori, Bottiglie/ora); Sistemi movimentazione contenitori: **nessuna tabella Caratteristiche** | Table widget nativo, o righe ripetute con Dynamic Tag | Il numero e il nome dei campi sono fissi ma diversi per Categoria macchina, quindi conviene un field group ACF condizionale per tassonomia, non un unico group per tutto il CPT. Non esiste e non va aggiunto un campo "Diametro-altezza contenitore" per le Sciacquatrici/Soffiatrici: richiesta esplicita del cliente di escluderlo, e non è mai stato presente nello schema Excel |
| Download center (PDF) | 1 ACF File field (solo scheda tecnica) per Riempitrici/Tappatrici/Sciacquatrici/Soffiatrici; **nessun Download center** per Sistemi movimentazione contenitori | Icon Box / Button con Dynamic Tag → URL del file | Formato scheda tecnica corretto da DWG a PDF. L'allegato CAD ("Layout CAD di ingombro") è stato rimosso su richiesta del cliente per tutte le Riempitrici, Tappatrici e Sciacquatrici/Soffiatrici: non va registrato come campo ACF per queste tipologie. Box "Dati chiave" e riferimento a persona specifica sono stati rimossi dal modulo preventivo |
| Modulo preventivo | — | Widget nativo Form | Nessun campo ACF "referente commerciale" nominativo: il form non preseleziona più una persona specifica |

### Varianti macchina non ancora scisse in pagine separate

Il foglio INFO CATALOGO MACCHINE elenca 6 varianti della famiglia **MEC ISO** (S, PS, SL, PSL, FS, DPS) con specifiche pressoché identiche tranne il nome — nel wireframe esiste una sola pagina `single-macchina-mec-iso.html`. Da chiarire con il PM se in produzione servono 6 post CPT distinti (stesso field group, valori quasi identici) o un unico post con un campo "varianti disponibili".

### Blocchi rimossi (non più nel wireframe, non serve mapparli)

Il blocco "Come funziona" (griglia a 4 card) sulle pagine categoria, i blocchi "Progettata internamente" e "Focus macchina" sulla scheda macchina, i tab "Dati tecnici"/"Contenitori" (residuo di design, mai funzionanti) e il caption ridondante "Dati tecnici di performance" sopra la tabella Caratteristiche sono stati rimossi perché non previsti dallo schema IMPOSTAZIONE CATALOGO MACCHINE — non richiedono field group ACF.

---

## Applicabilità e stato

Per il CPT Macchina la struttura è validata su tutte e 3 le tipologie coperte dallo schema Excel (17 istanze reali: MEC LD + 16 nuove schede), più le 3 istanze della categoria "Sistemi movimentazione contenitori" aggiunta dopo la consegna dell'Excel (20 istanze totali, con un fieldgroup volutamente più semplice — vedi sez. 3). Per il CPT Settore è validata su tutti e 10 i settori di lancio. Restano fuori dallo schema Excel — quindi non mappati qui — **Squadron** e **Linee complete**: per Squadron esistono dati reali per EXACTA/ATHENA nel foglio INFO CATALOGO MACCHINE non ancora impaginati nel wireframe (vedi handoff), per il resto della gamma Squadron (Volumetriche, Alto vuoto, Olympia, Weight filler, Evox/Evox Plus, Dosatore volumetrico) non esiste ancora nessun dato strutturato.

Questa stessa logica di mappatura (fisso → campo singolo/checkbox + widget statico; variabile → repeater + Loop Grid; condizionale per tipologia dove lo schema lo richiede) va replicata per gli altri archetipi del prototipo (`single-servizio.html`, `single-news-articolo.html`, `single-news-editoriale.html`) quando il PM fornirà uno schema Excel equivalente per quei contenuti.
