# Mappatura ACF + Elementor Pro — modello dati catalogo

Fonte di verità per lo sviluppo WordPress del catalogo (macchine, categorie, settori, linea Squadron). Ogni blocco del wireframe ha qui una controparte 1:1 in un campo ACF + widget Elementor Pro nativo (atomic dove disponibile). Aggiornata al 28/09 sui dati di `Eurostar - Schema dati catalogo_VER 25.09.xlsx` (versione del 26/09), `Modifiche sito 25.9.docx` e risposte email di Serena del 26/09, che sostituiscono lo schema del 15-09 e le modifiche del 19.9.

## Principio guida

- **Un solo CPT `macchina`** per Eurostar e Squadron: nessuna eccezione scheda per scheda. Le differenze tra tipologie si gestiscono con field group ACF condizionali per Categoria macchina, non con template o post type diversi.
- Dove il numero di elementi è **fisso e noto** (contenitori, download, righe della tabella) si usano campi singoli o checkbox, non repeater. Il repeater è riservato ai contenuti realmente variabili in numero (layout Linee complete, galleria).
- Ogni dato si inserisce **una volta sola**: se compare sia in testata sia in tabella Caratteristiche (Contenitori, Contenitori/ora, Prodotto), il campo è uno e il template lo richiama in due punti.

## Schema riassuntivo

| Oggetto WP | Tipo | Rappresenta | Pagine wireframe |
|---|---|---|---|
| `macchina` | CPT | Ogni macchina Eurostar e Squadron (21 Eurostar + 14 Squadron) | `single-macchina*.html`, card in categorie/settori/home, `page-squadron.html` |
| `categoria_macchina` | Tassonomia gerarchica su `macchina` | Sciacquatrici/Soffiatrici, Riempitrici, Tappatrici, Linee complete, Sistemi movimentazione contenitori, Usate | `taxonomy-macchina-*.html`, mega-menu, filtri catalogo |
| `settore` | Tassonomia su `macchina` (con campi ACF sul termine) | I 10 settori di lancio | `taxonomy-settore*.html`, pillole "Adatta per", mega-menu |
| `linea` | Tassonomia su `macchina` | Eurostar / Squadron | Raggruppamento in "Le nostre macchine" dei settori, pagina Squadron |

Settore passa da CPT (vecchia versione di questo file) a **tassonomia con campi sul termine**: così "Le nostre macchine" si ricava in automatico dai settori assegnati a ogni macchina, senza una relazione da tenere allineata a mano in due punti. Intro e sfide stanno comodamente in campi ACF del termine.

---

## 1. Categoria macchina (`taxonomy-macchina-*.html`)

Field group ACF con location "Taxonomy Term = Categoria macchina".

| Blocco wireframe | Campo | Widget Elementor | Note |
|---|---|---|---|
| H1 | Term name nativo | Heading + Dynamic Tag | |
| Intro hero | `intro` — Textarea | Text Editor + Dynamic Tag | Colonna "Intro (hero)" |
| Ordine menu/catalogo | `ordine` — Number | — (ordina query e menu) | 1 Sciacquatrici/Soffiatrici, 2 Riempitrici, 3 Tappatrici, 4 Linee complete, 5 Sistemi movimentazione contenitori, 6 Usate (confermato PM 25/09) |
| Griglia "La gamma …" | Nessuno: query sui post `macchina` con questo termine, `linea` = Eurostar | Loop Grid | Ordinata per `menu_order` (colonna "Ordine" dell'Excel) |
| Campi mostrati in card | Nessuno a livello di termine: la card legge i campi della macchina | Loop Item Template per tipologia | Sciacquatrici → Contenitori + Contenitori/ora; Riempitrici → Prodotto breve + Contenitori/ora; Tappatrici → Tipologia chiusura + Contenitori/ora; Sistemi movimentazione → Contenitori + Cambio formato |
| Linee complete | `descrizione` — Wysiwyg + `layout` — Repeater (immagine, titolo, didascalia) | Text Editor + Loop/Gallery | Nessuna macchina sotto: pagina di categoria a contenuto proprio |
| Usate | `descrizione` — Wysiwyg | Text Editor + CTA | Parco variabile nel tempo, nessuna scheda popolata al lancio |

Link alla categoria: da mega-menu e footer è un link all'archivio del termine; nel catalogo generale (`archive-macchine.html`) i tab filtrano la Loop Grid in pagina (Taxonomy Filter), tranne Linee complete e Usate che restano link.

---

## 2. Settore (`taxonomy-settore*.html`)

Field group ACF con location "Taxonomy Term = Settore".

| Blocco wireframe | Campo | Widget Elementor | Note |
|---|---|---|---|
| H1 | Term name nativo | Heading + Dynamic Tag | |
| Intro hero | `intro` — Textarea | Text Editor + Dynamic Tag | |
| Le sfide del settore | `sfide_1`, `sfide_2` — Textarea | 2 Text Editor | Sempre 2 paragrafi per tutti i 10 settori |
| Immagine hero | `immagine` — Image | Background dinamico | |
| Ordine | `ordine` — Number | — | Ordine sito: Vino, Birra, Liquori, Bevande e succhi, Acqua, Olio alimentare, Alimenti e condimenti, Cosmetica, Detergenza, Chimico/farmaceutico/sanitario — **non** quello della colonna Ordine dell'Excel (decisione utente) |
| Le nostre macchine — Eurostar | Nessuno: query `macchina` con questo `settore` e `linea` = Eurostar | Loop Grid a card | Tutte le macchine indicate dalla PM, niente selezione manuale "max 3" |
| Le nostre macchine — Linea Squadron | Nessuno: stessa query con `linea` = Squadron | Loop Grid compatta (nome + tipologia) + link a pagina Squadron | Mostrata solo se la query ha risultati |

---

## 3. Macchina (`single-macchina*.html`)

Il post `macchina` ha un field group **comune** e field group **condizionali per Categoria macchina**. Ordine = `menu_order` nativo (colonna "Ordine" dell'Excel: 2.1 → 1, 2.2 → 2…), non un campo ACF: si imposta nel riquadro Attributi pagina/post standard di WP, niente plugin di riordino aggiuntivo. La Loop Grid di ogni categoria (vedi 1.) filtra già per `categoria_macchina` = quel termine, poi ordina per Menu Order ascendente — quindi i valori devono essere coerenti solo *all'interno* di ciascuna categoria, non univoci sull'intero catalogo. Vale finché ogni macchina ha una categoria primaria unica (dati Excel attuali); se in futuro una macchina comparisse in più categorie con ordini diversi, `menu_order` da solo non basterebbe (è un valore per post, non per categoria).

### 3.1 Campi comuni (tutte le macchine)

| Blocco wireframe | Campo | Widget Elementor | Note |
|---|---|---|---|
| H1 | Post title | Heading + Dynamic Tag | |
| Sottotitolo | `tipologia` — Text | Heading H2/Text + Dynamic Tag | Colonna "Tipologia (sottotitolo)" |
| Linea | Tassonomia `linea` | — | Governa raggruppamenti e template (vedi 3.3) |
| Categoria | Tassonomia `categoria_macchina` | Breadcrumb | |
| Galleria | `galleria` — Gallery | Image Carousel/Gallery | Miniature 01–04 del wireframe |
| Descrizione tecnica | `descrizione` — Wysiwyg | Text Editor + Dynamic Tag | |
| Contenitori (icone) | `contenitori_tipi` — Checkbox a scelte fisse: Vetro, PET, HDPE, Lattina | Icon Box con Display Conditions sul valore | Solo icone per i materiali selezionati |
| Contenitori (dicitura) | `contenitori` — Text | Text in testata **e** riga "Contenitori" in tabella | Dicitura unica (Serena, punto 4) — un campo, due posizioni |
| Download | `scheda_tecnica` — File (PDF) | Button/Icon Box + Dynamic Tag URL, nascosto se vuoto | Titolo sezione "Download". Nessun CAD |
| CTA | — | Button "Richiedi un preventivo" + "Contattaci" | Niente form laterale. Proposta aperta: precompilare l'oggetto del form Contatti con il nome macchina via query string |
| Card | `prodotto_breve` — Text | Loop Item | Versione corta del prodotto per le card (es. "Liquidi piatti") |

### 3.2 Campi per Categoria macchina

| Categoria | Testata (Sezione intro) | Tabella Caratteristiche | Campi specifici |
|---|---|---|---|
| **Sciacquatrici/Soffiatrici** | Contenitori, Contenitori/ora | Contenitori, Campo di produzione della gamma | `contenitori_ora` — Text |
| **Riempitrici** | Adatta per (settori), Contenitori, Contenitori/ora, Prodotto | Tecnologia di riempimento, Contenitori, Prodotto da riempire, Campo di produzione della gamma | `contenitori_ora` — Text; `prodotto` — Textarea; `tecnologia_riempimento` — Text; `tipologia_valvole` — Text (solo isobariche, es. MEC ISO "S - FS - PS - SL - PSL - DPS - DPSL") |
| **Tappatrici** | Adatta per (tipologia chiusura), Contenitori/ora | Tipologia chiusura, Campo di produzione della gamma | `tipologia_chiusura` — Text (valori specifici per modello, es. "Capsule alluminio ROPP, TALOG", "Tappi corona Ø 26 e/o Ø 29 mm": non riducibili a una lista fissa) → pillola `es-chip` in testata + riga tabella; `contenitori_ora` — Text. Nessun campo Contenitori |
| **Sistemi movimentazione contenitori** | Contenitori | Contenitori, Cambio formato | `cambio_formato` — Text. Nessun Contenitori/ora, nessun Download al momento |

"Adatta per" delle Riempitrici = termini `settore` assegnati al post (widget Post Info → Terms, stile `es-chip`), valorizzati dalla colonna "Adatta per (settore)". Lo stesso dato alimenta "Le nostre macchine" dei settori: una sola sorgente.

"Contenitori/ora" (testata) e "Campo di produzione della gamma" (tabella) sono **lo stesso campo**: in tabella il template aggiunge "contenitori/ora" dopo il valore. Niente sigla BPH.

Campi esclusi su indicazione del cliente (non registrarli): Materiale a contatto prodotto, Diametro/altezza contenitore, Layout CAD, referente commerciale nominativo. Le varianti MEC ISO sono un solo post: le varianti sono il campo `tipologia_valvole`.

### 3.3 Linea Squadron

Stesso CPT e stessi field group (i modelli sono riempitrici/uniblocchi → categoria Riempitrici), con `linea` = Squadron.

| Modello | Dati disponibili | Resa |
|---|---|---|
| ATHENA, EXACTA | Completi: tipologia, settori, contenitori, descrizione, tecnologia, prodotto, capacità, valvole, PDF | Card estesa in `page-squadron.html` con link "Scheda tecnica (PDF)" |
| Olympia A/SA, Olympia AV A/SA, VOL, VOL.L Grandi formati, Easykeg, Evox/Evox Plus/Evox CM, Riempitrice a peso, Dosatore volumetrico e tappatore | Solo nome + settori | Card sintetica in `page-squadron.html` e nel gruppo "Linea Squadron" dei settori |

Al lancio i post Squadron **non hanno pagina singola pubblica** (il wireframe non la prevede): il link della card porta all'ancora del modello in pagina Squadron. Se in futuro arrivano i dati completi basta attivare il template singolo, il modello dati è già pronto. Gamma e velocità degli altri modelli: sospese su indicazione del cliente (23/09).

### 3.4 Template Theme Builder

- **Single macchina**: un template unico, blocchi con Display Conditions per categoria (righe tabella, pillole Adatta per, icone contenitori, Download se il file esiste). Condizione: `macchina` con `linea` = Eurostar.
- **Archivio categoria**: un template per termine standard + template dedicati per Linee complete e Usate.
- **Archivio settore**: un template unico.
- **Loop Item**: 4 varianti card (una per categoria, vedi sez. 1) + card compatta Squadron.
- **Hover**: classe globale `es-btn` + varianti, `es-textlink`/`es-arrow`, `es-chip` in Site Settings > Custom CSS (da `assets/es-hover.css`); da verificare sul markup dei widget atomic nella scheda pilota MEC LD.

---

## 4. Posizione di lavoro (`page-lavora-con-noi.html` + `single-posizione-lavoro-*.html`)

CPT: **Posizione di lavoro** come CPT singolo (non termine di tassonomia) — ogni posizione ha contenuto proprio (descrizione, attività, requisiti, cosa offriamo) troppo ricco per un termine di tassonomia, e serve una pagina singola dedicata con URL propria in cui far confluire la candidatura specifica.

Introdotto in questa sessione sostituendo il precedente pattern ad accordion (`<details>/<summary>` in pagina) con riquadri cliccabili in stile Ferrero Careers che portano a una pagina di dettaglio dedicata, su richiesta esplicita del cliente/PM.

| Elemento | Campo ACF | Componente Elementor | Note |
|---|---|---|---|
| Riquadro posizione in `archive`/listing (`page-lavora-con-noi.html`) | Titolo = post title CPT; Reparto/dipartimento = ACF Text; Rif./Job ID = ACF Text; Sede = ACF Text; Tipo di contratto = ACF Select (Tempo indeterminato / determinato / stage, ecc.) | Loop Grid con Loop Item cliccabile (intera card come link alla singola) — icone sede/contratto da Icon List o SVG statico via HTML widget | Nel wireframe è un `<a class="es-job-card">` con hover (`translateY` + ombra) e freccia cerchiata in basso a destra; in Elementor l'intera Loop Item va resa cliccabile con link dinamico al post CPT |
| H1 / meta strip in hero (`single-posizione-lavoro-*.html`) | Titolo = post title; Reparto, Rif., Sede, Tipo di contratto = stessi campi del punto sopra | Heading + Icon List con Dynamic Tag → ACF Field | Stessa logica header/hero già in uso per `single-macchina*.html` e `single-servizio.html` |
| Corpo scheda (La posizione, Attività principali, Requisiti richiesti, Requisiti preferenziali, Cosa offriamo) | ACF Wysiwyg/Repeater per sezione (una lista per sezione, numero di voci variabile) | Text Editor / Icon List ripetuta via Loop | Contenuto realmente variabile in numero di voci → repeater, non campi singoli, coerente col principio guida in cima a questo file |
| Form di candidatura per la posizione | Nessun campo ACF: il form invia a `page-conferma.html` con oggetto contenente il titolo della posizione (variabile letta dal post, non più da un menu a tendina) | Elementor Pro Form widget, con campo nascosto "Posizione" precompilato dal Dynamic Tag del post title | **Cambio rispetto a prima**: il form non è più unico e condiviso in fondo a `page-lavora-con-noi.html` con un menu a tendina "Posizione di interesse" — ora ogni posizione ha il proprio form, scoperto sulla sua pagina singola, senza bisogno di far scegliere la posizione all'utente |
| Form "Candidatura spontanea" | Nessun campo ACF: form statico, non legato a un post CPT | Elementor Pro Form widget su pagina "Lavora con noi" | Resta sulla pagina listing (decisione esplicita del cliente/PM di questa sessione): serve per chi non trova una posizione aperta in linea con il proprio profilo, quindi non ha senso spostarlo dentro una singola posizione |

Applicabilità e stato: validata su 5 istanze popolate nel wireframe (Area Manager e Elettricista industriale/Programmatore PLC, contenuto reale da Serena; Addetto Ufficio Tecnico, Operaio specializzato — Assemblaggio meccanico e Customer Service Specialist, contenuto plausibile scritto per mostrare la struttura su più reparti/tipi di contratto, da validare con il cliente prima del lancio). Job ID (`Rif. EU-2026-0x`) è un segnaposto di formato, non un dato fornito dal cliente.

---

## Applicabilità e stato

Modello validato su tutte le 21 macchine Eurostar del wireframe (10 Riempitrici, 6 Tappatrici, 2 Sciacquatrici/Soffiatrici, 3 Sistemi movimentazione contenitori), sui 10 settori e sui 14 modelli Squadron, contro l'Excel del 25/09 (273/273 campi verificati). Da provare su WordPress con la scheda pilota MEC LD prima di caricare il resto.

Punti aperti con la PM che possono toccare i campi: capacità ATHENA (Excel "fino a 850" vs PDF "da definire in offerta") ed EXACTA (range 200–600?), valvole Squadron (Excel "S - PS - DPS" vs PDF solo "S"), "Cambio formato" del Neck Handling, schede di dettaglio Servizi (se sì → CPT `servizio` a sé, fuori da questo file).

Stessa logica (fisso → campo singolo; variabile → repeater; condizionale per tipologia) per gli altri archetipi (`single-servizio.html`, `single-news-*.html`) quando arriveranno i contenuti definitivi.

---

## Stato implementazione su WordPress (staging, 28/09)

Tutto registrato da ACF Pro (menu ACF → Tipi di contenuto / Tassonomie / Gruppi di campi), niente codice custom: modificabile dall'admin.

| Oggetto | URL | Note |
|---|---|---|
| CPT `macchina` | `/macchine/` (archivio), `/macchine/mec-ld/` | supports: titolo, immagine in evidenza, attributi (menu_order), revisioni. Niente editor: la descrizione è il campo ACF |
| CPT `posizione_lavoro` | `/lavora-con-noi/<posizione>/` | Nessun archivio: il listing è la pagina "Lavora con noi" |
| `categoria_macchina` | `/categoria-macchina/riempitrici/` | 6 termini: sciacquatrici, riempitrici, tappatrici, linee-complete, movimentazione, usate ("Macchine usate") |
| `settore` | `/settori/vino/` | 10 termini, slug come i file del wireframe |
| `linea` | — (non pubblica) | eurostar, squadron |

Permalink del sito impostati su `/%postname%/` (erano "semplici") e `.htaccess` scritto con il blocco standard di WordPress: senza, nginx di Plesk restituiva 404 su ogni URL parlante.

Field group (chiavi `group_es_*`): Categoria macchina, Settore, Macchina — dati comuni, Macchina — Contenitori (Sciacquatrici, Riempitrici, Movimentazione), Macchina — Capacità (Sciacquatrici, Riempitrici, Tappatrici), Macchina — Riempitrici, Macchina — Tappatrici, Macchina — Sistemi movimentazione, Posizione di lavoro. I gruppi condizionali usano la regola "Termine dell'articolo = categoria" e compaiono appena si spunta la categoria.

Kit Elementor: 4 colori e 4 tipografie di sistema + 14 colori e 17 tipografie custom dai token del design system; testo, link e H1-H4 del Kit collegati ai globali. Larghezza container del Kit = 1360px (`--space-content-max`).

Layout a due livelli, come nel wireframe (`tokens/spacing.css`): sezione esterna a tutta larghezza (sfondo da bordo a bordo) con padding laterale `--space-section-x` clamp(20px,6vw,80px) e verticale `--space-section-y` clamp(64px,10vh,140px) / `-lg` clamp(96px,16vh,190px); dentro, contenuto centrato a 1360px, o 1180px (`--space-content-max-narrow`) nelle sezioni strette. Il padding di sezione e la variante 1180 **non** vanno nel padding di default del Kit, che si applicherebbe anche ai container annidati: si impostano sui container di sezione nei template (classe/preset, dopo la verifica atomic/classic). **Non ancora fatti** (dipendono dalla verifica atomic/classic): variabili/classi globali v4, CSS hover `es-btn` (porting di `assets/es-hover.css` sul markup reale dei widget), header/footer, template.

Scheda pilota MEC LD: post in bozza con tutti i campi; mancano PDF scheda tecnica (mai ricevuto) e foto della galleria.

---

## Responsive

Rilevato sul wireframe (media query su tutte le pagine + prova in browser a 375/768/1024px, 28/09).

### Breakpoint Elementor (Kit, impostati)

| Breakpoint Elementor | Valore | Soglia wireframe |
|---|---|---|
| Tablet extra | ≤ 1200px | Nessuna nel wireframe: soglia del menu hamburger (vedi lacune) |
| Tablet | ≤ 1024px | 1024 — nel wireframe soglia del menu hamburger, anticipata a 1200 |
| Mobile extra | ≤ 900px | 900 — layout a colonna singola |
| Mobile | ≤ 767px | 560 del wireframe, applicata a 767 (nessun breakpoint in più) |

Sistema di breakpoint unico per widget atomic e classic: non dipende dalla verifica del punto 4.

### Comportamenti definiti dal wireframe

| Soglia | Elemento | Comportamento |
|---|---|---|
| Tablet extra (1200; 1024 nel wireframe) | Header | Menu → hamburger con pannello a tutto schermo (fondo Navy 950, voci 16px con divisori, CTA Contattaci a tutta larghezza in fondo); mega-menu e relativi caret nascosti |
| Mobile extra (900) | Testata scheda macchina (`es-product-hero`) | 2 colonne → 1 (testo sopra, galleria sotto) |
| Mobile extra (900) | Sidebar posizione di lavoro | Da sticky a statica |
| Mobile extra (900) | Home — blocco diagonale | 1 colonna, niente taglio diagonale; pannello testo sopra, immagine sotto (min 280px) |
| Mobile extra (900) | Home — macchine in evidenza | 1 colonna, gap 48px, senza bordo sinistro |
| Mobile extra (900) | Griglia settori, griglia team (Chi siamo) | 2 colonne |
| Mobile (767) | Barra alta — link assistenza | Nascosto |
| Mobile (767) | Griglia settori, griglia team | 1 colonna |
| Oltre 900 | Home — sezione "ingegnere" | Composizione assoluta (bottiglia centrale, claim, 4 statistiche agli angoli); sotto i 900 le statistiche tornano nel flusso |
| Tutte | Titoli, testi, spaziature | Fluidi con `clamp()` (preset tipografici del Kit, padding sezione `--space-section-x/y`): nessuna variante per breakpoint da impostare a mano |
| Tutte | Griglie di card | `repeat(auto-fill/auto-fit, minmax(220–340px, 1fr))`: vanno a capo da sole. In Elementor: Loop Grid/griglia con colonne per breakpoint equivalenti, o griglia CSS auto-fit su atomic |

Miniature galleria: restano 4 in riga anche su mobile (voluto).

### Lacune del wireframe — da risolvere nei template WordPress (decisione 28/09)

Nel wireframe queste parti non hanno regole responsive e su mobile si rompono (scroll orizzontale fino a ~600px): **non si corregge il wireframe**, si risolvono direttamente nei template.

| Elemento | Problema nel wireframe | Soluzione nei template |
|---|---|---|
| Footer (5 colonne, tutte le pagine) | Resta a 5 colonne: pagina larga ~970px a 375, ~995px a 768; già a 1025 la griglia sfora il container (950px su 902) | Vedi "Footer — specifica" sotto (validata con prototipo CSS in browser, 28/09) |
| Header — menu desktop | Tra 1025 e ~1180px voci + "Contattaci" non ci stanno: sfora di ~110px a 1025, ~20px a 1140; entra da ~1180 | **Hamburger fino a 1200px** (decisione 28/09): breakpoint "tablet extra" = 1200 attivo nel Kit, da usare come soglia del menu nel template header. Il pannello hamburger è lo stesso del wireframe sotto i 1024 |
| Corpo scheda macchina 65/35 (descrizione + "Richiedi un preventivo") | A 375 resta 183/99px | 1 colonna sotto i 900: box preventivo sotto la descrizione, non più sticky |
| Corpo news editoriale 70/30 | A 375 resta 209/89px | 1 colonna sotto i 900, colonna laterale sotto |
| Form a 2 colonne (Contatti, candidatura posizioni) | Campi larghi 71–90px a 375 | Campi a tutta larghezza su mobile |

### Footer — specifica

Blocchi: **Marchio** (logo + 30 anni, sedi operativa e legale), **Azienda** (4 link), **Macchine** (7), **Settori** (10), **Contatti** (email, telefono, 6 icone social).

Vincoli di larghezza: "eurostarinfo@eurostar.it" (~200px) e "Sciacquatrici/Soffiatrici" (~190px) non vanno a capo; le 6 icone social in riga occupano ~266px.

| Breakpoint | Griglia | Disposizione |
|---|---|---|
| Desktop (> 1024) | 5 colonne `1.4fr 1fr 1fr 1fr 1fr` | Marchio · Azienda · Macchine · Settori · Contatti (come il wireframe) |
| Tablet e mobile extra (768–1024) | 3 colonne uguali | Riga 1: Marchio (2 colonne) · Contatti — Riga 2: Azienda · Macchine · Settori |
| Mobile (≤ 767) | 2 colonne uguali | Marchio (tutta riga) → Macchine (tutta riga) → Azienda · Settori → Contatti (tutta riga) |

Su tutti i breakpoint la riga delle icone social va a capo (`flex-wrap: wrap`): è quello che a 1025 fa rientrare le 5 colonne nel container, e a 768 le icone si dispongono su due righe (4 + 2). Contatti sale in prima riga su tablet e Macchine prima di Azienda su mobile: in Elementor si ottiene con l'ordine per breakpoint dei container, senza duplicare blocchi.

Verificato con il prototipo a 375, 768, 900, 1025, 1280 e 1440px: nessuno scroll orizzontale, nessun testo fuori dalla propria cella.

---

## Accessibilità (WCAG)

Requisito del cliente: sito accessibile secondo WCAG (richiesta: 2.0). Livello di riferimento: **AA**. Versione: vedi "Decisioni aperte" in fondo alla sezione.

### Già impostato sul sito (28/09)

- `<html lang="it-IT">` (lingua WordPress it_IT); viewport senza blocco dello zoom.
- Landmark del tema Hello: `header`, `main#content`, `footer`.
- **Ally** di Elementor (`pojo-accessibility` 4.1.4, abbonamento fino al 06/2027) attivo: widget di regolazione sul front-end, skip link "Salta al contenuto" verso `#content`, modulo di correzione automatica (`remediation-module.js`), scanner Assistant nella dashboard.
- Kit → Custom CSS: focus da tastiera visibile su tutti gli elementi interattivi (`outline: 2px solid currentColor; outline-offset: 2px`) e `prefers-reduced-motion` che annulla transizioni e animazioni.
- Contrasto palette verificato (rapporti calcolati): tutte le coppie testo/sfondo usate nel wireframe ≥ 4,5:1 (testo body 8,2:1, footer 72% su Navy 950 10,2:1, copyright 50% su Navy 950 5,3:1). Unico colore sotto soglia: **Gray 400** (3,4:1 su bianco), mai usato per testo nel wireframe → rinominato nel Kit "solo bordi/decorazioni, NON per testo".
- Breakpoint e specifiche responsive (sezione Responsive): nessuno scroll orizzontale fino a 375px — per il reflow a 320px (criterio 1.4.10, WCAG 2.1) i calcoli sul footer tornano, da provare sui template.

### Pattern del wireframe da mantenere nei template

Il wireframe è già costruito in modo accessibile; i template devono conservarlo, non semplificarlo:

| Pattern | Nel wireframe | Nei template |
|---|---|---|
| Un solo H1 per pagina, titoli in ordine | 59/59 pagine | H1 = titolo post/termine; kicker e label non vanno fatti con tag heading |
| Skip link | `.es-skip-link` su 58 pagine | Quello di Ally (verificarne la raggiungibilità con Tab sui template: nel DOM ha `tabindex="-1"`, e il banner cookie prende il focus per primo) |
| Menu e mega-menu | `aria-expanded` + `aria-controls` sui caret, chiusura con Esc | Da riverificare sul widget menu scelto (atomic/classic), inclusa la navigazione da tastiera del pannello hamburger |
| Breadcrumb | `<ol>` con `aria-label="Breadcrumb"` | Idem (widget Breadcrumb di Yoast o Elementor) + `aria-current="page"` sull'ultima voce |
| Tab (Caratteristiche, filtri News) | `role="tablist"`/`role="tab"` + `aria-selected` | Idem; i filtri News devono annunciare il cambio di risultati |
| Tabelle caratteristiche | `<th scope="row">` | Tabella HTML vera, non griglia di div |
| Icone decorative (contenitori, frecce, social) | `aria-hidden="true"`; icone contenitori con `role="img"` + `aria-label` | Idem; le icone social hanno `aria-label` (LinkedIn, WhatsApp…) |
| Link download | Tipo e peso nel testo: "Scheda tecnica MEC LD (PDF, 2.4 MB)" | Idem, generato dal campo file ACF |
| Form | 14/14 campi con `<label for>`; focus 2px | Elementor Form: label visibili (non solo placeholder), errori testuali associati al campo, campi obbligatori indicati anche a testo |
| Galleria | Miniature `role="tab"` con `aria-label` | Galleria navigabile da tastiera, testo alternativo per ogni immagine |

### Regole di contenuto

- **Testo alternativo**: ogni immagine caricata (galleria macchina, immagine settore, layout Linee complete) va compilata con il testo alternativo nella Libreria media — ACF ed Elementor lo leggono da lì. Immagini puramente decorative: alt vuoto.
- **PDF schede tecniche**: sono documenti esterni; per WCAG vanno resi accessibili (tag, ordine di lettura) o affiancati dai dati in HTML — la tabella Caratteristiche della scheda copre già i dati principali.
- **Lingua delle parti**: nomi commerciali delle macchine restano invariati; eventuali frasi in inglese dentro pagine IT vanno marcate con `lang="en"`.

### Decisioni aperte

1. **Versione WCAG**: 2.0 AA (richiesta) o 2.1 AA (riferimento attuale in Italia/UE, EN 301 549; include tutta la 2.0 + criteri mobile). Differenza pratica emersa finora: con 2.1 il bordo dei campi form (`--border-subtle`, 1,37:1) non basta per il criterio 1.4.11 → usare Gray 400 (3,43:1).
2. **Widget Ally sul front-end**: un widget di regolazione (overlay) non rende conforme il sito e il modulo di correzione automatica può mascherare problemi durante i test. Da decidere se tenerlo visibile al lancio; lo scanner Assistant resta utile in ogni caso per controllare i template.
