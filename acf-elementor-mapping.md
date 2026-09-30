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
| Linee complete | `titolo` — Text (H1, diverso dal nome del termine), `claim` — Text (frase in grassetto nella hero), `descrizione` — Wysiwyg (testo sotto "Esempi di layout linea") + `layout` — Repeater (immagine, titolo, didascalia) | Template dedicato, layout via shortcode | Nessuna macchina sotto: pagina di categoria a contenuto proprio |
| Usate | `descrizione` — Wysiwyg (testo del riquadro "nessuna macchina disponibile") | Template dedicato: loop sulle macchine della categoria con stato vuoto | Parco variabile nel tempo, nessuna scheda popolata al lancio: quando ci saranno, compaiono come card |
| Tutte | `titolo_cta` — Text | Invito finale | Titolo della fascia scura in fondo (per categoria) |

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

Al lancio i post Squadron **non hanno pagina singola pubblica** (il wireframe non la prevede): il link della card porta all'ancora del modello in pagina Squadron. Raggruppamento nel "Resto della gamma": campo `gruppo_squadron` (field group "Macchina — Squadron", solo linea Squadron) — i modelli con lo stesso valore finiscono in un'unica card con quel titolo. Se in futuro arrivano i dati completi basta attivare il template singolo, il modello dati è già pronto. Gamma e velocità degli altri modelli: sospese su indicazione del cliente (23/09).

### 3.4 Template Theme Builder

- **Single macchina**: un template unico, blocchi con Display Conditions per categoria (righe tabella, pillole Adatta per, icone contenitori, Download se il file esiste). Condizione: `macchina` con `linea` = Eurostar.
- **Archivio categoria**: un template per termine standard + template dedicati per Linee complete e Usate.
- **Archivio settore**: un template unico.
- **Card**: in atomic il Loop Item è un **componente** dentro un Collection Loop (`e-collection-loop`), non un template separato. Una sola card macchina per tutte le categorie (la riga dati per categoria la sceglie lo shortcode `[es_card_stats]`, così funziona anche nel catalogo dove le categorie sono mescolate) + card compatta Squadron. Vedi "Card e archivi — stato" in fondo.
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

**Font in locale (GDPR, 28/09)**: attiva l'opzione di Elementor "Carica i Google Fonts in locale" (`elementor_local_google_fonts`). Barlow e Roboto sono serviti da `wp-content/uploads/elementor/google-fonts/` (CSS + woff2); verificato in browser: nessuna richiesta a `fonts.googleapis.com`/`fonts.gstatic.com`. Se al lancio si riattiva il widget Ally, ricontrollare che non carichi font esterni.

**Dove va il codice custom (28/09)**: JS e HTML → Custom Code di Elementor Pro (con condizioni per pagina); CSS globale → Custom CSS del Kit; PHP (agganci, filtri) → FluentSnippets (`easy-code-manager`, oggi vuoto). **Angie** (agente AI di Elementor) **disattivato**: nessuno snippet presente, e con Novamira già in uso sarebbe un secondo agente con esecuzione PHP da amministratore. Resta installato, riattivabile se serve.

Layout a due livelli, come nel wireframe (`tokens/spacing.css`): sezione esterna a tutta larghezza (sfondo da bordo a bordo) con padding laterale `--space-section-x` clamp(20px,6vw,80px) e verticale `--space-section-y` clamp(64px,10vh,140px) / `-lg` clamp(96px,16vh,190px); dentro, contenuto centrato a 1360px, o 1180px (`--space-content-max-narrow`) nelle sezioni strette. Il padding di sezione e la variante 1180 **non** vanno nel padding di default del Kit, che si applicherebbe anche ai container annidati: si impostano sui container di sezione nei template (classe/preset, dopo la verifica atomic/classic). **Non ancora fatti** (dipendono dalla verifica atomic/classic): variabili/classi globali v4, CSS hover `es-btn` (porting di `assets/es-hover.css` sul markup reale dei widget), header/footer, template.

**Catalogo importato (28/09)**: 35 post `macchina` in bozza — 21 Eurostar (2 Sciacquatrici, 10 Riempitrici, 6 Tappatrici, 3 Movimentazione) + 14 Squadron (categoria Riempitrici, linea Squadron). Fonte: Excel PM VER 25.09 (1), normalizzato come nel wireframe e confrontato campo per campo con le pagine del wireframe; valori riletti dal database dopo l'import, nessuna differenza. Ordine (`menu_order`) come colonna "Ordine" dell'Excel; Squadron nell'ordine delle righe Excel.

Scelte dell'import:
- Differenze Excel/wireframe risolte con il valore del wireframe (MEC SI contenitori, MAXIMA tecnologia, nome "Sistema Neck Handling"); MEC LP contenitori = "Bottiglie in vetro, PET, HDPE e alluminio" (l'Excel ha due versioni). ATHENA ed EXACTA prodotto = "Liquidi gassati e piatti" (decisione utente 28/09; il PDF ATHENA indica solo "Gassato").
- La riga "Tipologia di valvole isobariche: …" delle descrizioni (MEC ISO, SKILLFILL, DUALFILL, ATHENA, EXACTA) è nel campo `tipologia_valvole`, non più nella descrizione.
- Squadron: **14 post separati** (come Excel e pagine settore); il raggruppamento "Olympia A / SA", "Olympia AV A / SA", "Evox / Evox Plus / Evox CM" della pagina Squadron si risolve nel template. Nomi in maiuscolo come Excel. Contenitori (icone) di ATHENA/EXACTA ricavati dal testo; gli altri 12 modelli hanno solo nome e settori.
- Slug dai nomi file del wireframe (`/macchine/gemini-f/`, `/macchine/eagle-va/`, `/macchine/stelle-variabile/`…); Squadron dal nome (`/macchine/vol-l-grandi-formati/`).
- PDF nella Libreria media: TWIST RINSER (ID 157), ATHENA (158), EXACTA (159), collegati al campo `scheda_tecnica`. Le altre 18 schede elencate nell'Excel non sono mai arrivate: Download nascosto finché mancano.
- Nessuna immagine: foto e gallerie da caricare quando disponibili (con testo alternativo).

Punti aperti con Serena caricati con il valore dell'Excel: capacità ATHENA (850) ed EXACTA (600), valvole ATHENA/EXACTA (S - PS - DPS), Neck Handling "Cambio formato: Presa collo".

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
| Header — loghi su mobile stretto | Sotto ~355px il logo "30 anni" finisce sotto l'hamburger (a 320: logo fino a 288px, pulsante da 256) | **Loghi fluidi** (segnalato dall'utente 29/09): altezze `clamp()` nel Kit — logo 40px, divisore 28px, "30 anni" 32px da ~370px in su (come il wireframe), in proporzione sotto (a 320: 31 / 22 / 25px). Distanza dall'hamburger ≥ 18px a ogni larghezza, desktop invariato |
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

Requisito del cliente: sito accessibile. Riferimento deciso il 28/09: **WCAG 2.1 livello AA** (richiesta iniziale 2.0; la 2.1 la include e aggiunge i criteri per mobile/zoom — è il riferimento della EN 301 549).

### Già impostato sul sito (28/09)

- `<html lang="it-IT">` (lingua WordPress it_IT); viewport senza blocco dello zoom.
- Landmark del tema Hello: `header`, `main#content`, `footer`.
- **Ally** di Elementor (`pojo-accessibility` 4.1.4, abbonamento fino al 06/2027): **widget sul front-end spento durante lo sviluppo** (decisione 28/09, da riconsiderare al lancio). Si usa lo **scanner Assistant** per controllare i template; le correzioni automatiche proposte dall'Assistant **non vanno approvate** in sviluppo — i problemi si correggono nei template, altrimenti i test misurano le toppe e non il markup.
- **Skip link**: quello di Ally è **spento** perché nel codice del plugin ha `tabindex="-1"` fisso — verificato in browser: con Tab non si raggiunge mai (criterio 2.4.1). Riattivato lo skip link nativo di Hello "Vai al contenuto" → `#content`: primo elemento raggiunto con Tab, visibile al focus, con Invio porta nel contenuto (verificato). **Vincolo per i template Theme Builder**: quando un template Elementor sostituisce il contenuto del tema, il `<main id="content">` di Hello non c'è più → il container principale di ogni template single/archive/pagina va impostato con tag HTML `main` e ID `content`, altrimenti lo skip link punta al vuoto.
- Kit → Custom CSS: focus da tastiera visibile su tutti gli elementi interattivi (`outline: 2px solid currentColor; outline-offset: 2px`) e `prefers-reduced-motion` che annulla transizioni e animazioni.
- Contrasto palette verificato (rapporti calcolati): tutte le coppie testo/sfondo usate nel wireframe ≥ 4,5:1 (testo body 8,2:1, footer 72% su Navy 950 10,2:1, copyright 50% su Navy 950 5,3:1). Unico colore sotto soglia: **Gray 400** (3,4:1 su bianco), mai usato per testo nel wireframe → rinominato nel Kit "solo bordi/decorazioni, NON per testo".
- Breakpoint e specifiche responsive (sezione Responsive): nessuno scroll orizzontale fino a 375px — per il reflow a 320px (criterio 1.4.10, WCAG 2.1) i calcoli sul footer tornano, da provare sui template.

### Pattern del wireframe da mantenere nei template

Il wireframe è già costruito in modo accessibile; i template devono conservarlo, non semplificarlo:

| Pattern | Nel wireframe | Nei template |
|---|---|---|
| Un solo H1 per pagina, titoli in ordine | 59/59 pagine | H1 = titolo post/termine; kicker e label non vanno fatti con tag heading |
| Skip link | `.es-skip-link` su 58 pagine | Quello nativo di Hello ("Vai al contenuto"), già attivo; container principale del template = `<main id="content">` |
| Menu e mega-menu | `aria-expanded` + `aria-controls` sui caret, chiusura con Esc | Da riverificare sul widget menu scelto (atomic/classic), inclusa la navigazione da tastiera del pannello hamburger |
| Breadcrumb | `<ol>` con `aria-label="Breadcrumb"` | Idem (widget Breadcrumb di Yoast o Elementor) + `aria-current="page"` sull'ultima voce |
| Tab (Caratteristiche, filtri News) | `role="tablist"`/`role="tab"` + `aria-selected` | Idem; i filtri News devono annunciare il cambio di risultati |
| Tabelle caratteristiche | `<th scope="row">` | Tabella HTML vera, non griglia di div |
| Icone decorative (contenitori, frecce, social) | `aria-hidden="true"`; icone contenitori con `role="img"` + `aria-label` | Idem; le icone social hanno `aria-label` (LinkedIn, WhatsApp…) |
| Link download | Tipo e peso nel testo: "Scheda tecnica MEC LD (PDF, 2.4 MB)" | Idem, generato dal campo file ACF |
| Form | 14/14 campi con `<label for>`; focus 2px; bordo campi `--border-subtle` (1,37:1) | Elementor Form: label visibili (non solo placeholder), errori testuali associati al campo, campi obbligatori indicati anche a testo. **Bordo campi in Gray 400 (3,43:1)** per il criterio 1.4.11 di WCAG 2.1 — scostamento voluto dal wireframe |
| Galleria | Miniature `role="tab"` con `aria-label` | Galleria navigabile da tastiera, testo alternativo per ogni immagine |

### Regole di contenuto

- **Testo alternativo**: ogni immagine caricata (galleria macchina, immagine settore, layout Linee complete) va compilata con il testo alternativo nella Libreria media — ACF ed Elementor lo leggono da lì. Immagini puramente decorative: alt vuoto.
- **PDF schede tecniche**: sono documenti esterni; per WCAG vanno resi accessibili (tag, ordine di lettura) o affiancati dai dati in HTML — la tabella Caratteristiche della scheda copre già i dati principali.
- **Lingua delle parti**: nomi commerciali delle macchine restano invariati; eventuali frasi in inglese dentro pagine IT vanno marcate con `lang="en"`.

### Decisioni (28/09)

1. **WCAG 2.1 AA.** Conseguenze pratiche finora: bordo campi form in Gray 400; reflow a 320px da verificare sui template (criterio 1.4.10).
2. **Widget Ally spento in sviluppo**, da decidere al lancio: un widget di regolazione non rende conforme il sito, la conformità sta nel markup dei template.
3. **Skip link di Hello al posto di quello di Ally** (vedi sopra).

### A carico del referente SEO

- Titolo del sito (Impostazioni → Generali): oggi "Eurostar Handmade With Love", compare nel `<title>` di ogni pagina (criterio 2.4.2 "Titolo della pagina"). Da sostituire prima del lancio insieme alla configurazione Yoast.
- Slug delle macchine e basi URL (`/macchine/`, `/categoria-macchina/`, `/settori/`): impostati per lo sviluppo (vedi Stato implementazione), da confermare prima del lancio.

### Test consigliati per ogni template

Scanner Ally Assistant + navigazione completa da tastiera (Tab/Shift+Tab/Invio/Esc, menu e hamburger compresi) + zoom al 200% e larghezza 320px + screen reader (VoiceOver) sulla scheda pilota MEC LD.

---

## Replica del wireframe in Elementor

Obiettivo (utente, 28/09): il sito WordPress + Elementor è la **replica fedele del wireframe**, pagina per pagina e breakpoint per breakpoint. Si replica la **resa visiva e la semantica** (titoli, landmark, tabelle, attributi ARIA), non il DOM riga per riga: Elementor genera il proprio markup (container/wrapper), diverso tra widget atomic e classic. Scostamenti ammessi solo quelli decisi e documentati qui (footer responsive, hamburger fino a 1200px, bordo campi form in Gray 400, skip link di Hello).

### Componenti del design system

Nel wireframe 4 componenti sono generati da `_ds/…/_ds_bundle.js` (non sono scritti nell'HTML). Specifiche dal codice del bundle; token da `tokens/*.css` (il `readme.md` del design system descrive un prototipo precedente e ha alcuni valori superati, es. navy `#14213F`: **fanno fede i token**). In Elementor diventano classi globali (atomic) o stili salvati/preset di widget (classic), decisi dopo la verifica atomic/classic.

**Button** (124 usi) — `<a>` se ha link, altrimenti `<button>`.

| Proprietà | Valore |
|---|---|
| Base | inline-flex, centrato, gap 10px, Barlow 700, uppercase, letter-spacing .08em, nessun bordo, nessun raggio, nessun taglio d'angolo (`clip-path: none`) |
| Taglia `lg` (default, l'unica usata) | padding 16px 30px, 14px |
| Taglia `md` (prevista, non usata) | padding 11px 20px, 12px |
| Transizione | background, color, transform — 0.35s `cubic-bezier(0.22,1,0.36,1)` |

| Variante (usi) | Riposo | Hover (da `assets/es-hover.css`, solo mouse) |
|---|---|---|
| primary su chiaro (32) | fondo Blue 600 `#25387E`, testo bianco | riempimento bianco diagonale da sinistra, testo Blue 600, contorno 1px Blue 600 (`outline-offset:-1px`) |
| primary su scuro (50) | fondo bianco, testo Navy 800 | riempimento Blue 600 diagonale, testo bianco |
| ghost su chiaro (19) | fondo `rgba(0,0,0,.08)`, testo Navy 800 | riempimento Blue 600 diagonale, testo bianco |
| ghost su scuro (23) | fondo `rgba(255,255,255,.1)`, testo bianco | riempimento bianco diagonale, testo Navy 800 |

Riempimento diagonale: pseudo-elemento `::before` con `inset: 0 -30px`, `clip-path: polygon(0 0, calc(100% - 30px) 0, 100% 100%, 0 100%)`, da `translateX(-101%)` a `translateX(0)`; `isolation:isolate; overflow:hidden` sul pulsante. Solo `(hover:hover) and (pointer:fine)`; con riduci movimento nessuna transizione.

**StatBlock** (43 usi, sempre su chiaro) — valore sopra, etichetta sotto.

| Taglia | Valore | Etichetta |
|---|---|---|
| `lg` (12) | Barlow 900, clamp(32px,4vw,59px), line-height 0.9, Navy 800 | Roboto 400 12px, uppercase, ls .14em, Gray 500, margin-top 10px |
| `md` (31) | Barlow 900, clamp(17px,1.7vw,22px), lh 1.15 | 11px, ls .08em, margin-top 6px |
| `sm` (dentro MachineCard) | Barlow 900, clamp(11px,1.1vw,13px), lh 1.25 | 11px, ls .06em, margin-top 4px |
| Su scuro (non usata) | valore bianco | etichetta Blue 300 |

**SectionKicker** (81 usi) — `<p>` (non un titolo), Barlow 600 13px, uppercase, letter-spacing .26em, Blue 600 su chiaro (51) / Blue 300 su scuro (28); allineamento sinistra, centro (2) o destra.

**MachineCard** (83 usi) — intera card cliccabile (`<a>`), nessun hover nel wireframe.

| Parte | Specifica |
|---|---|
| Card | flex colonna, fondo Gray 100 `#EDEFF4`, nessun bordo/raggio/ombra |
| Immagine | riquadro `aspect-ratio: 4/3.2`, `overflow:hidden` (nel wireframe segnaposto tratteggiato → in WP immagine in evidenza, `object-fit: cover`) |
| Corpo | padding 20px 22px 0; nome H3 Barlow 800 clamp(22px,2vw,28px) uppercase Navy 800, margin 0 0 4px; sottotitolo (`tipologia`) Roboto 500 14px Blue 600 |
| Riga dati | flex, gap 24px, padding 18px 22px 22px, margin-top 14px, bordo superiore 1px `--border-subtle`; 2 StatBlock `sm` a larghezza uguale (i due valori dipendono dalla categoria, vedi sez. 1) |

Altri token usati dai template: ombra card in hover `0 18px 44px rgba(0,0,0,.12)` (card news, servizi, posizioni: `translateY(-6px)`), ombra pannello mega-menu `0 30px 60px rgba(0,0,0,.4)`, ombra nav `0 1px 0 rgba(0,0,0,.08)`, `--action-primary-hover` = Navy 800 (colore hover dei link).

### Comportamenti interattivi

Nel wireframe ci sono script inline per i comportamenti; `support.js` e `_ds_bundle.js` servono solo a disegnare i componenti del wireframe e **non vanno portati** in WordPress.

| Comportamento | Dove | Nel wireframe | In WordPress (proposta) |
|---|---|---|---|
| Menu hamburger | tutte | pannello a tutto schermo, chiusura con Esc, `aria-expanded` | Widget menu Elementor (soglia "tablet extra" 1200px); verificare Esc, focus e annuncio apertura |
| Mega-menu | tutte (desktop) | pannello a tutta larghezza sotto la nav, caret con `aria-expanded`/`aria-controls`, chiusura Esc e clic fuori, riposizionato al resize | Mega Menu di Elementor Pro (contenuto = template); se non replica la chiusura con Esc/clic fuori: piccolo script |
| Ricerca | tutte | pannello di ricerca aperto da pulsante nella barra alta | Widget Search di Elementor Pro in modalità pannello/overlay |
| Galleria scheda macchina | 21 schede | immagine principale + 4 miniature (`role="tab"`) | Widget galleria/carosello con miniature, alimentato dal campo `galleria`; navigazione da tastiera |
| Filtro catalogo | archivio macchine | tab per categoria che filtrano la griglia in pagina, messaggio "nessun risultato" | Loop Grid + Taxonomy Filter di Elementor Pro (Linee complete e Usate restano link) |
| Filtro News | archivio news | tab per categoria che filtrano in pagina | Loop Grid + Taxonomy Filter |
| Nastro loghi (marquee) | home | scorrimento continuo, pausa al passaggio del mouse, **pulsante pausa/play** | Carosello continuo o CSS + piccolo script; il pulsante pausa va mantenuto (WCAG 2.2.2) |
| Carosello news | home | traccia scorrevole con frecce | Loop Carousel di Elementor Pro |
| Torna su | tutte (footer) | pulsante `#es-back-to-top` | Pulsante con link a `#top` / piccolo script, rispetta riduci movimento |
| WhatsApp flottante | tutte | cerchio verde 52px fisso in basso a destra, `aria-label="Scrivici su WhatsApp"` | Pulsante nel template footer con posizione fissa |
| Form | Contatti, Lavora con noi, 5 posizioni | invio con validazione → `page-conferma.html` | Form di Elementor Pro, redirect alla pagina Conferma; servono destinatari e testo privacy dal cliente |
| Sidebar posizione | 5 posizioni | sticky (statica sotto 900px) | Impostazione sticky del container |
| Effetti hover | tutte | `es-hover.css` + regole inline (link, card, pillole) | Custom CSS del Kit / classi globali |

### Specifiche dettagliate dei comportamenti

Estratte dagli script e dal CSS del wireframe (28/09). Token di movimento: `--duration-fast` 0.2s, `--duration-base` 0.35s, `--ease-standard` `cubic-bezier(0.22,1,0.36,1)`. Nel wireframe **non ci sono** animazioni allo scroll (nessun reveal, nessun parallax, nessun video): l'unica animazione continua è il nastro loghi in home.

**Header — struttura** (tutte le pagine)

- Ordine: skip link → barra alta → pannelli mega-menu (fuori dalla nav, `position: fixed`) → nav.
- **Barra alta** (non fissa, scorre via): fondo Navy 950, padding 8px `clamp(20px,4vw,56px)`, testo caption. A sinistra punto 7px Blue 400 + "Rispondiamo entro 24 ore lavorative — assistenza tecnica IT/EN" (nascosto sotto i 560px → nel sito sotto i 767px); a destra, gap 16px: link "Catalogo" con icona, divisore 1×14px bianco 20%, selettore lingua (Barlow 700 12px, ls .14em, maiuscolo; lingua attiva bianca, l'altra bianco 50%, separatore "-" al 40%), divisore, pulsante ricerca.
- **Nav** (`position: sticky; top: 0; z-index: 50`): fondo Navy 800, padding 18px `clamp(20px,4vw,56px)`, flex con spazio tra. Logo: bianco 40px di altezza + divisore 1×28px bianco 30% + logo "30 anni" 32px, gap 16px. Voci: gap `clamp(16px,2.2vw,32px)`, ordine Azienda · Macchine ▾ · Settori ▾ · Servizi · Referenze · Squadron · News · **Contattaci**.
- **Voce di menu**: Barlow 700 13px, ls .14em, maiuscolo, bianco al 75% di opacità, padding-bottom 6px. Hover: bianco pieno + riga 2px bianca che cresce da sinistra (`scaleX` 0→1, 0.35s). Voce attiva (pagina corrente o sua sezione): opacità 1, peso 800, riga 2px **Blue 300** sempre visibile. Focus: contorno 2px bianco, offset 4px.
- **CTA "Contattaci"**: blocco Blue 600 a tutta altezza della nav, attaccato al bordo destro (margini negativi pari al padding della nav), padding 18px `clamp(20px,4vw,56px)` 18px 28px. Hover: riempimento bianco diagonale da sinistra, testo Blue 600 (da `es-hover.css`).

**Mega-menu** (Macchine, Settori — solo desktop)

- Si apre con il **caret** (pulsante 18×18px accanto alla voce, `aria-expanded`, `aria-controls`, `aria-haspopup`, `aria-label="Mostra sottomenu …"`); la voce stessa resta un link alla pagina. Caret ruota di 180° quando aperto.
- Pannello: `position: fixed`, a tutta larghezza, `top` = bordo inferiore della nav (ricalcolato al resize), fondo bianco, bordo superiore 1px, ombra `0 30px 60px rgba(0,0,0,.4)`, padding 44px `--space-section-x`, altezza massima `100vh − 100px` con scroll. Entrata: opacità 0→1 e `translateY(-12px)`→0 in 0.35s.
- Contenuto su griglia 1360px, colonne `1.1fr 1fr 1fr 1fr`, gap 56px: (1) titolo + testo + link "Vedi tutto…"; (2) e (3) liste di link con titolo H3 (14px), voci Roboto 14px con riga divisoria; (4) box in evidenza su fondo Gray 050 (immagine 4:3, titolo H4 15px, testo, link "Scopri…").
  - Macchine: Per tipologia (Sciacquatrici/Soffiatrici, Riempitrici, Tappatrici, Squadron, Sistemi movimentazione contenitori) · Per esigenza (Linee complete, Macchine usate, Ricambi e assistenza, Soluzioni per settore) · evidenza CANFILL.
  - Settori: Bevande (Vino, Birra, Liquori, Bevande e succhi, Acqua) · Altri settori (Olio alimentare, Alimenti e condimenti, Cosmetica…, Detergenza…, Chimico…) · evidenza Vino.
- Chiusura: nuovo clic sul caret, clic fuori dal pannello, **Esc** (il focus torna al caret). Un solo pannello aperto alla volta.
- Sotto la soglia hamburger: caret e pannelli nascosti (nel menu mobile restano solo le voci principali, come nel wireframe).

**Menu hamburger** (fino a 1200px nel sito; 1024 nel wireframe)

- Pulsante 44×44px, 3 linee 24×2px bianche (gap 5px); aperto → X (prima linea `translateY(7px) rotate(45deg)`, seconda sparisce, terza `translateY(-7px) rotate(-45deg)`), 0.35s. `aria-expanded` + `aria-label` "Apri il menu" / "Chiudi il menu".
- Pannello a tutto schermo, fondo Navy 950, padding 110px 28px 40px, entra da destra (`translateX(100%)`→0, 0.35s), scroll interno. Voci 16px, padding 18px 0, divisori 1px bianco 40%; CTA "Contattaci" in fondo, a larghezza piena, padding 16px 24px, margin-top 28px.
- Esc chiude e riporta il focus sul pulsante.

**Ricerca** (barra alta)

- Pulsante 26×26px con icona, `aria-expanded`, `aria-controls`, `aria-haspopup`, `aria-label="Apri la ricerca"`.
- Pannello sotto il pulsante, allineato a destra, 280px, fondo bianco, bordo 1px, ombra pannello, padding 16px; entrata opacità + `translateY(-8px)`→0 in 0.35s. All'apertura il focus va nel campo.
- Form `role="search"`: label nascosta "Cerca nel sito", campo (placeholder "Cerca macchine, settori…"), pulsante invio 34×34 Blue 600 (`aria-label="Avvia la ricerca"`).
- Chiusura: clic fuori, Esc (focus torna al pulsante). In WP la ricerca deve cercare anche nel CPT `macchina`.

**Galleria scheda macchina**

- Immagine principale 4:3 + 4 miniature quadrate in griglia (gap 10px), sotto 12px. Miniatura attiva: bordo e testo Blue 600; `role="tab"` + `aria-selected`; focus 2px Blue 600.
- Clic su miniatura → cambia l'immagine principale (nessuna transizione nel wireframe). In WP le immagini vengono dal campo `galleria` (max 4); testo alternativo di ogni immagine dalla Libreria media.

**Filtri catalogo e News**

- Riga di tab (Barlow 700 12px, ls .1em, maiuscolo, Gray 500, padding 12px 4px, gap 28px, a capo se non entrano); tab attiva: Navy 800 con bordo inferiore 2px Blue 600.
- Catalogo: Tutte · Sciacquatrici/Soffiatrici · Riempitrici · Tappatrici · *Linee complete* · Sistemi movimentazione contenitori · *Usate* (le due in corsivo sono link alle rispettive pagine, non filtri). News: Tutte · Novità · Fiere ed eventi · Casi studio.
- Il filtro nasconde/mostra le card in pagina, senza ricaricare; se non resta nulla compare "Nessuna macchina in questa categoria." (centrato, Body MD, Gray 500). Sotto la griglia: paginazione.
- Accessibilità, da migliorare rispetto al wireframe: i filtri sono marcati come `role="tab"` senza pannello associato e due "tab" sono link. Nel sito: pulsanti con `aria-pressed` (o il markup del Taxonomy Filter di Elementor) + annuncio del numero di risultati in una regione `aria-live`.

**Nastro loghi clienti** (home)

- Riga con etichetta a sinistra ("Scelti da produttori in oltre 100 paesi", eyebrow bianco 50%) e nastro a destra. I "loghi" sono **nomi dei clienti in testo** (29 nomi), non immagini.
- Scorrimento continuo verso sinistra, 34s lineare, infinito (lista duplicata con la copia `aria-hidden`, animazione `translateX(0 → -50%)`), bordi sfumati con maschera (trasparente → pieno all'8% e al 92%), gap `clamp(32px,5vw,64px)`.
- Pausa al passaggio del mouse e con il **pulsante pausa/play** (40×40 tondo, fondo bianco 10%, bordo bianco 40%, `aria-pressed`, `aria-label` "Metti in pausa…"/"Riprendi lo scorrimento dei loghi", icona che cambia). Con riduci movimento: fermo.

**Carosello News** (home)

- Traccia orizzontale con `scroll-snap` (x mandatory), barra di scorrimento nascosta, `role="region"` `aria-label="Ultime news"`, focalizzabile. Card: 1 per riga sotto i 760px, 3 per riga sopra (`calc(33.333% − 16px)`), gap 24px.
- Frecce precedente/successivo: tonde 44px, bordo `--border-subtle-strong`, fondo bianco (hover Gray 050); scorrono di una card (larghezza card + 24px), scorrimento morbido.
- Card news: immagine 16:10, padding 24px, riga categoria + data (spec label, Blue 600 / Gray 500), titolo H3, estratto Body MD; hover `translateY(-6px)` + ombra card.

**Torna su** (footer) — pulsante 44×26px, bordo bianco 40%, raggio 14px; scroll in cima morbido (istantaneo con riduci movimento).

**WhatsApp flottante** (tutte le 58 pagine) — link a `https://wa.me/393453450371`, nuova scheda, cerchio 52px `#25D366`, icona bianca, fisso a 20px da destra e dal basso, `z-index: 60`, ombra `0 6px 20px rgba(0,0,0,.25)`, `aria-label` e `title` "Scrivici su WhatsApp".

**Form**

- Contatti: 13 campi tutti obbligatori (`required`, asterisco nell'etichetta): Tipo di richiesta (select), Nome, Cognome, Azienda, Paese, Email, Telefono, Oggetto richiesta, Tipo di prodotto, Tipo di contenitore, Contenitori/ora indicativi, Messaggio (textarea), consenso "Ho letto e accetto la Privacy Policy" (il link oggi è `#`: manca la pagina Privacy).
- Campi: fondo bianco, padding 12px 14px, Body SM, testo Navy 800, focus 2px Blue 600; bordo **Gray 400** nel sito (WCAG 2.1, vedi Accessibilità).
- Invio → pagina Conferma. Posizioni di lavoro: campo nascosto con il titolo della posizione.
- **Sidebar candidatura** (5 posizioni): fondo Gray 050, padding `clamp(32px,3.6vw,44px)`, `position: sticky; top: 110px` (sotto la nav fissa), statica sotto i 900px.

### Inventario pagine → WordPress

59 file nel wireframe: 58 pagine del sito + `index.html` (indice del wireframe, non va replicato).

| Pagine wireframe | N. | In WordPress |
|---|---|---|
| `home.html` | 1 | Pagina "Home" (pagina iniziale) |
| `single-macchina*.html` | 21 | **Template Single** `macchina` (linea Eurostar), dati dai 21 post già importati |
| `taxonomy-macchina-sciacquatrici/riempitrici/tappatrici/movimentazione` | 4 | **Template Archivio** `categoria_macchina` (uno per le 4 categorie con macchine) |
| `taxonomy-macchina-linee-complete`, `-usate` | 2 | Template archivio dedicati (contenuto proprio, nessuna macchina sotto) |
| `taxonomy-settore*.html` | 10 | **Template Archivio** `settore` |
| `archive-macchine.html` | 1 | Archivio del CPT (`/macchine/`) con filtro per categoria |
| `archive-settori.html` | 1 | Pagina "Settori" (elenco dei 10 settori) |
| `page-squadron.html` | 1 | Pagina "Squadron" (card ATHENA/EXACTA + resto della gamma, raggruppato come nel wireframe) |
| `page-lavora-con-noi.html` | 1 | Pagina "Lavora con noi" (griglia posizioni + candidatura spontanea) |
| `single-posizione-lavoro-*.html` | 5 | **Template Single** `posizione_lavoro` (contenuti da inserire nei 5 post) |
| `archive-servizi.html` | 1 | Pagina "Servizi e post-vendita" |
| `single-servizio.html` | 1 | **Non si fa** (decisione utente 30/09): nessuna pagina del singolo servizio, i servizi restano le card della pagina Servizi così come sono |
| `archive-news.html`, `single-news-articolo.html`, `single-news-editoriale.html` | 3 | Articoli WordPress (`post`) + categorie news: archivio e 2 varianti di template single — modello dati da definire |
| `page-chi-siamo`, `page-contatti`, `page-referenze`, `page-cataloghi` | 4 | Pagine statiche |
| `page-conferma.html` | 1 | Pagina "Conferma" (destinazione dei form) |
| `404.html` | 1 | Template 404 |
| Header, footer | — | Template Theme Builder globali |
| — (non nel wireframe) | — | Template Theme Builder "Risultati di ricerca" (`search-results`), disegnato sul modello dell'archivio News |

### Ordine di costruzione (dopo WPML e verifica atomic/classic)

1. Classi/stili globali dei 4 componenti + CSS hover (`es-hover.css` riscritto sul markup reale).
2. ✅ Header (utility bar, nav, mega-menu, hamburger 1200px, ricerca, selettore lingua WPML) e footer (5/3/2 colonne, WhatsApp, torna su).
3. Scheda pilota MEC LD (template single `macchina`), confronto pixel con il wireframe → validazione.
4. ✅ Card (componenti atomic), archivio categoria, archivio settore, catalogo con filtro, template dedicati Linee complete e Usate.
5. Pagine: ✅ Squadron, Contatti, Conferma, Settori, Servizi, Chi siamo, Referenze, 404, Home, Cataloghi, Lavora con noi + 5 posizioni.
6. ✅ News: archivio, articolo, caso studio, 16 contenuti IT (EN da tradurre).
6b. ✅ Risultati di ricerca (IT + EN).
7. Test: ✅ confronto pixel a 375/768/1024/1280/1440, ✅ tastiera, ✅ zoom 200%; da fare scanner Ally, screen reader (vedi "Test finali — stato").

Metodo di confronto: wireframe (`http://localhost:4173`) e staging affiancati allo stesso viewport; screenshot + confronto degli stili calcolati per elemento (dimensioni, spaziature, colori, font).

**Novamira Design (DESIGN.md) non usato** (decisione 28/09): la specifica resta il wireframe + Kit Elementor + questa mappatura. Un design attivo in Novamira sarebbe una seconda fonte di verità da tenere allineata, e i suoi controlli "anti-slop" fissi segnalano come errore il trattino lungo (—), presente in molti testi approvati del wireframe.

---

## Scheda pilota MEC LD — stato (28/09, validata IT + EN)

- **Template Theme Builder** "Scheda macchina" (ID 237, tipo single-post, condizione `include/singular/macchina`). Costruzione mista: struttura, titoli, testi, pulsanti atomic con 28 classi globali `es-*` (il label diventa il nome della classe nell'HTML); dati per categoria via shortcode FluentSnippets (`1-eurostar-scheda-macchina-shortcode.php`: breadcrumb, adatta per, contenitori, descrizione+valvole, caratteristiche, download, galleria; etichette in WPML String Translation, contesto "Eurostar template"). Valori in evidenza atomic con condizioni "campo non vuoto". CSS componenti + hover diagonale pulsanti nel Custom CSS del Kit.
- **Verificato a 1440px contro il wireframe**: coordinate, dimensioni, font e colori coincidono al pixel (testata, corpo 65/35, tabella, riquadro laterale, invito finale). Scostamenti voluti: galleria senza miniature finché mancano le foto (testata −155px); sezione Download nascosta senza PDF. Responsive: 1 colonna sotto 900px, nessun overflow a 768/375/320px. Provate anche EAGLE C, Stelle universali, Twist Rinser, DUALFILL (righe tabella, adatta per, valvole, download corretti).
- **Correzioni Kit fatte**: preset titoli senza letter-spacing (il wireframe non lo usa); body Roboto 400 16px, `line-height: normal`.
- **Stato contenuti**: MEC LD **pubblicata** (staging non indicizzato); le altre 34 in bozza.
- **Attenzione FluentSnippets**: il codice di uno snippet PHP deve iniziare con `<?php` (l'intestazione chiude il blocco PHP); senza, il codice viene stampato in pagina — successo per ~2 minuti il 28/09, corretto.
- **Hover pulsanti** verificato in browser: riempimento bianco diagonale, testo e contorno 1px Blue 600 (come `es-hover.css`).
- **Invito finale**: campo ACF facoltativo `titolo_cta` (dati comuni, traducibile) + shortcode `[es_cta_titolo]` nel titolo atomic → "Configura la tua {nome}" oppure il testo del campo. Compilato per Neck Handling, Stelle universali, Stelle a geometria variabile con i testi del wireframe. Verificato su EAGLE C, Stelle, Twist Rinser, DUALFILL: un solo H1, tipologia, valori in evidenza e invito finale corretti.
- **Prova EN completa (28/09) — superata**, testi inglesi scritti da me, **da far rivedere** al cliente/traduttore:
  - Termini EN: Fillers, Wine, Beverages and juices, Water, Beer, Spirits, Food and condiments, Eurostar (slug `fillers`, `wine`… **provvisori, da confermare col referente SEO**).
  - MEC LD EN (ID 246, `/en/macchine/mec-ld/`) creata con duplicato WPML + `reset_duplicate_flag`, campi ACF in inglese, settori riordinati come in IT (il duplicato perde l'ordine per post).
  - Template EN (ID 248) = duplicato del 237 con le 20 stringhe del pacchetto tradotte; la cache condizioni contiene 237 e 248 e ogni lingua usa il proprio.
  - Verificato nel browser: `lang="en-US"`, breadcrumb "Home / Machines / Fillers / MEC LD" con link `/en/…`, "Suitable for" + pillole EN con link `/en/settori/…`, "Containers" + icone "Glass, PET, HDPE, Aluminium can", tabella e "containers/hour" in inglese, "Technical description", "Specifications", "Request a quote", "Contact us", "Configure your MEC LD", skip link "Skip to content", hreflang it/en/x-default. Pagina IT invariata.
  - Problemi risolti durante la prova: (1) le stringhe dello snippet erano registrate come **inglesi** (lingua predefinita delle stringhe in String Translation = en) → lingua di origine portata a IT e snippet corretto per registrarle con sorgente `it`; (2) WPML le traduce leggendo un file `.mo` per contesto (`wp-content/languages/wpml/eurostar template-en_US.mo`) che non si rigenera con `icl_add_string_translation` → rigenerato con `WPML\ST\MO\File\Manager::add()`. Traducendo dall'interfaccia di String Translation WPML lo rigenera da solo.
  - Le richieste che il server fa a se stesso (`wp_remote_get`) ricevono la versione IT anche su `/en/`: per verificare le pagine EN usare il browser.
  - Gli avvisi `gzuncompress()` che compaiono nelle esecuzioni Novamira durante le operazioni WPML sono innocui: WPML verifica di proposito se un campo è compresso (`@gzuncompress`) e ripiega sul dato non compresso; Novamira registra anche gli errori silenziati.
- **Accessibilità della scheda** (verificata in browser): un solo H1, titoli in ordine; landmark header/main#content/aside/footer; nessun link senza nome, nessuna immagine senza alt, icone SVG con nome, nessun ID duplicato, `th scope="row"`, breadcrumb con `aria-label` e `aria-current`; da tastiera: skip link per primo, poi header, breadcrumb, pillole, pulsanti, selettore lingua, tutti con focus visibile 2px.
- **Fatto il 29/09**: i 3 pulsanti puntano alla pagina Contatti (link di tipo pagina, ID 260): in EN WPML traduce l'ID da solo → `/en/contact/` (prima l'EN puntava a `/contatti/`). Singole Squadron reindirizzate alla pagina Squadron (vedi "Card e archivi — stato").
- **Resta da fare**: scanner Ally dall'admin.

---

## Header e footer — stato (28/09, validati IT + EN)

- **Template Theme Builder**: Header (ID 342, EN 426) e Footer (ID 344, EN 428), condizione `include/general` (tutto il sito). Hello usa il wrapper `<header>`/`<footer>` di Elementor Pro, quindi il contenitore atomic interno è un `div`.
- **Header** = contenitore atomic `es-header` + shortcode `[es_header]` (snippet FluentSnippets `2-eurostar-header-footer.php`): barra alta (supporto, Catalogo, selettore lingua WPML, ricerca), nav con logo, voci da menu WordPress, pulsanti mega-menu, CTA, hamburger. Header sticky: `.elementor-location-header{position:sticky}` + JS che imposta `top` = −altezza barra alta (resta visibile solo la nav, come nel wireframe).
- **Footer** atomic con 22 classi globali `es-footer-*` (griglia `1.4fr 1fr 1fr 1fr 1fr`; tablet 3 colonne, mobile 2 con `order`/`grid-column span 2`); link colonne da menu via `[es_menu_links location="…"]`, social `[es_social]`, torna su `[es_back_to_top]`, WhatsApp flottante `[es_whatsapp_float]`; contatti = pulsanti atomic (mailto/tel) stilizzati come testo.
- **Menu WordPress** (Aspetto → Menu, gestibili dal cliente): Principale, Mega Macchine, Mega Settori, Footer Azienda/Macchine/Settori + le 6 versioni EN collegate in WPML. Nel mega-menu ogni voce di primo livello è una colonna: con sottovoci → titolo + elenco; senza → titolo, descrizione (campo *Descrizione* della voce) e link (campo *Attributo title*); classe `es-mega-featured` → colonna in evidenza con riquadro immagine. Voci speciali con classe: `es-mega-macchine`, `es-mega-settori` (aprono il pannello), `es-navlink-cta` (Contattaci).
- **CSS** nel Custom CSS del Kit, blocco "header e footer" (valori del wireframe; hamburger e mega-menu nascosto fino a 1200px).
- **Verificato contro il wireframe a 1440px**: nav, voci, CTA, barra alta, mega-menu Macchine (posizioni colonne e testi, altezza 427px) e footer (colonne 80/391/621/852/1082, altezza 623px) coincidono al pixel. A 1200px footer identico. A 1024 il wireframe resta a 5 colonne compresse: il sito applica le 3 colonne della specifica; a 767/375/320 ordine Marchio → Macchine → Azienda·Settori → Contatti, nessun overflow.
- **Tastiera**: hamburger (aria-expanded, etichetta Apri/Chiudi, Esc chiude e riporta il focus), mega-menu (Invio apre, Esc chiude e torna al pulsante), ricerca (Invio apre e porta il focus nel campo, Esc chiude). **Miglioria rispetto al wireframe**: nel wireframe il focus non arriva al campo di ricerca perché `visibility` è ancora in transizione; sul sito il pannello diventa visibile subito (`visibility 0s` all'apertura) e l'input non eredita la `transition: all` di Hello.
- **EN**: termini EN completati (5 categorie, 4 settori, Squadron), 9 pagine EN (About us, Sectors, Services and after-sales, References, Squadron, News, Contact, Catalogues, Careers — **slug provvisori per il referente SEO**), menu EN, stringhe dello snippet (contesto "Eurostar template", versione `hf1`) e del footer tradotte. Verificato nel browser: testi, etichette ARIA e link EN corretti, IT invariato. **Testi EN scritti da me, da far rivedere.**
- **Selettore lingua del footer di WPML disattivato** (il selettore è nella barra alta).
- **Aperti**: colonna "in evidenza" CANFILL assente nel mega-menu EN finché la scheda CANFILL (bozza) non ha la traduzione → poi aggiungere la voce al menu "Mega Macchine (EN)"; URL social del cliente (ora `#`, tranne WhatsApp); foto per il riquadro in evidenza del mega-menu; il pulsante tondo del banner cookie compare sopra il WhatsApp (plugin cookie, non nel wireframe).
- **Lezioni**: (1) i contenitori atomic hanno anche la classe `.e-con` (`width:100%`): in una riga flex serve `width:auto` nella classe; (2) `.elementor img` batte le classi singole: altezze dei loghi con classe raddoppiata o `max-width:none` nelle classi globali delle immagini; (3) l'ability `elementor-edit-global-class` **sostituisce** tutte le proprietà della variante: passare sempre l'elenco completo; (4) `wpml_pb_finished_adding_string_translations` vuole 3 argomenti (`$post_en, $post_originale, $campi`).

---

## Card e archivi — stato (29/09, categoria e settore validati IT + EN)

- **Card = componenti atomic** (Elementor Pro 4.3: niente template Loop Item, la card è un componente dentro `e-collection-loop`):
  - **Card macchina** (componente 448): `<a>` con link dinamico al post, riquadro immagine 4:3.2 (immagine in evidenza solo se presente, altrimenti fondo Gray 150 pieno come la galleria vuota della scheda), nome H3, tipologia (nascosta se vuota), riga dati `[es_card_stats]`. **Una sola card per tutte le categorie** invece delle 4 varianti previste: la riga dati dipende dalla categoria (Sciacquatrici: Tipo contenitore + Contenitori/ora; Riempitrici: Prodotto + Contenitori/ora; Tappatrici: Tipo di chiusura + Contenitori/ora; Movimentazione: Contenitori + Cambio formato) e la sceglie lo shortcode, così la stessa card serve anche il catalogo, dove le categorie sono mescolate in un solo loop.
  - **Card Squadron compatta** (componente 450): "Squadron", nome, tipologia se presente.
  - Classi globali `es-mcard*`, `es-scard*`, griglie `es-mgrid` (auto-fit 260px, categoria), `es-mgrid-fill` (auto-fill 260px, settore), `es-sgrid` (auto-fill 220px); `es-loop`/`es-loop-item` tolgono il padding di 10px che i contenitori atomic hanno di default. Le griglie hanno `grid-auto-rows:auto`: il layout del loop di default ha righe tutte uguali (`1fr`), il wireframe no.
- **Query dei loop**: sorgente "Current Query" + Query ID `es_macchine_eurostar` / `es_macchine_squadron` (snippet `4-eurostar-archivi-catalogo.php`): filtro per linea, tutte le macchine (`nopaging`: con `posts_per_page = -1` impostato nell'hook la query esce con `LIMIT 0, -1` e non restituisce nulla), ordine `menu_order`.
- **Template archivio "Categoria macchina"** (490 / EN 496, condizione archivio `categoria_macchina`): hero scura con breadcrumb `[es_breadcrumb_archivio]`, kicker, H1 = nome termine, intro = campo `intro`; "La nostra gamma" + loop; nav "Vedi tutto il catalogo"; invito finale = nuovo campo del termine **`titolo_cta`** (`[es_term_cta]`). Linee complete e Usate avranno template dedicati (per ora prendono questo).
- **Template archivio "Settore"** (503 / EN 504, condizione archivio `settore`): hero, "Esigenze di processo" + "Le sfide del settore {nome}" (`[es_sfide_titolo]`) + paragrafi `[es_sfide]` (campi `sfide_1`/`sfide_2`, una riga vuota separa più paragrafi: Vino ne ha 4), "Le nostre macchine" + loop Eurostar, gruppo "Linea Squadron" (`[es_squadron_head]` + loop compatto, **nascosto se non ci sono modelli**), nav catalogo, invito finale (`titolo_cta` anche sul settore).
- **Contenuti caricati**: intro + `titolo_cta` di 5 categorie (Linee complete esclusa: avrà un template con contenuto proprio) e intro + sfide + `titolo_cta` dei 10 settori, dai testi del wireframe (erano vuoti). **34 macchine pubblicate** (erano in bozza; staging non indicizzato).
- **Squadron**: permalink dei modelli Squadron = ancora nella pagina Squadron della loro lingua (`/squadron/#athena`, filtro `post_type_link`); la singola risponde con **redirect 302** alla stessa ancora (temporaneo finché la pagina Squadron non ha le ancore; da portare a 301 al lancio). Le ancore `id="{slug}"` vanno messe nella pagina Squadron quando si costruisce.
- **Verifica al pixel** (wireframe e staging affiancati): categoria a 1440/1024/768/375 — sezioni, card (302×420/453/436 a 1440), colonne e testi coincidono; unico scarto ~0,4px per riga dovuto al bordo tratteggiato del segnaposto immagine del wireframe. Settore Vino a 1440/768/375: sfide, card compatte (246×122), intestazione Squadron coincidono. Nessuno scroll orizzontale fino a 320px (il wireframe sfora per il footer). Le 4 categorie e i 10 settori mostrano esattamente le macchine del wireframe.
- **Accessibilità**: un H1, H2 di sezione, card H3; loop con `role="list"`/`listitem`; card raggiungibili con Tab e focus visibile 2px; breadcrumb con `aria-current`; nav del catalogo con `aria-label`.
- **EN**: termini EN delle categorie con intro e `titolo_cta`, settore Wine completo (intro, sfide, invito) — **testi scritti da me, da far rivedere**; gli altri 9 settori EN sono vuoti (**decisione utente 30/09: le traduzioni EN di macchine e settori le fornisce chi se ne occupa; non scriverle, attendere i testi e poi caricarli**). Etichette card e archivi in String Translation (contesto "Eurostar template"). Le pagine EN mostrano solo MEC LD finché le altre macchine non sono tradotte; il gruppo Squadron EN è nascosto (nessun modello tradotto).
- **Lezioni**: (1) gli attributi dei widget atomic (es. `aria-label`) non entrano nel pacchetto WPML: aggiunto il campo con il filtro `wpml_elementor_widgets_to_translate` a priorità 1001 (dopo la config di WPML a 20) e svuotata la cache `wpml_elementor_auto_config`; valori dinamici negli attributi **non** sopravvivono al salvataggio di Elementor (diventano "Array"); (2) sul front-end EN WPML riconverte gli ID di `get_term()` nel termine tradotto: per leggere lo slug italiano serve una query diretta; (3) WPML traduce da solo gli ID dei link atomic di tipo pagina e gli ID dei componenti: usare link di tipo pagina, non URL; (4) dopo `make_duplicate` rigenerare la cache delle condizioni in una **richiesta separata**, altrimenti il duplicato può finire nella location "popup" e la pagina viene renderizzata due volte; (5) le classi globali escono come `.elementor .classe` (0,2,0): le regole del Kit che devono vincerle vanno raddoppiate.
- **Aperti**: testi EN (vedi sotto per quelli scritti da me).
- **Ordine nei settori — deciso (utente, 29/09)**: si usa il campo Ordine delle categorie (`menu_order`), non l'ordine per settore del wireframe (es. Acqua: MEC LD, MEC VOL, MEC ISO…), che il modello dati non memorizza. Un solo ordine da gestire.

---

## Catalogo macchine — stato (29/09, validato IT + EN)

- **Template archivio "Catalogo macchine"** (512 / EN 513, condizione archivio del CPT `macchina`, `/macchine/`): hero come le categorie ma più alta (`es-cat-hero-lg`: 46vh, padding `clamp(40px,8vh,90px)`), kicker "Catalogo", H1 e intro fissi (traducibili nel pacchetto WPML), barra filtri `[es_catalogo_filtri]`, loop con Query ID `es_catalogo` (tutte le Eurostar raggruppate per Ordine della categoria, poi `menu_order` — ordinamento in PHP sul filtro `the_posts`), `[es_catalogo_vuoto]`.
- **Filtri**: voci generate dai termini `categoria_macchina` nell'ordine del campo Ordine; Linee complete e Usate sono **link** alle loro pagine (etichetta "Usate" invece del nome del termine "Macchine usate", come nel wireframe), le altre **pulsanti con `aria-pressed`** (non `role="tab"` come nel wireframe: non ci sono pannelli associati). Il filtro nasconde/mostra le card in pagina (la card espone la categoria con `data-cat` sulla riga dati), mostra "Nessuna macchina in questa categoria." quando non resta nulla e annuncia il numero di risultati in una regione `role="status"` nascosta ("6 macchine").
- **Barra sticky** sotto l'header (nel wireframe è sticky a `top:0` e finisce sotto la nav): lo script imposta `top` = parte visibile dell'header, ricalcolato al resize; lo sticky sta sul wrapper del widget shortcode, non sulla barra.
- **Una sola riga che scorre in orizzontale** (decisione utente 29/09, al posto delle 2–5 righe del wireframe sotto ~1236px): sfumature bianche ai bordi quando c'è altro da vedere; al clic la riga scorre per mostrare la voce successiva (se voce cliccata e successiva non entrano insieme, ha la precedenza la cliccata: succede solo con "Sistemi movimentazione contenitori" a 375px); da tastiera la voce con il focus viene portata in vista; focus con `outline-offset:-3px` (con l'overflow il contorno esterno verrebbe tagliato); barra alta 51px e sticky anche su mobile. Sopra ~1236px identica al wireframe (voci a x 80/161/396/517/634/788/1104 a 1440).
- **Scostamenti voluti dal wireframe**: niente pulsante "Mostra altre macchine" (nel wireframe non fa nulla e il catalogo mostra già tutte le 21 macchine); il messaggio "nessun risultato" nel wireframe è visibile anche quando non dovrebbe (`hidden` sovrascritto), nel sito solo a filtro vuoto.
- **Verifica**: 1440 — hero 432px, barra a 550, card da 690, 21 card nell'ordine del wireframe; filtri Tappatrici 6, Movimentazione 3, Tutte 21; 768 e 375 card nelle stesse posizioni del wireframe; nessuno scroll orizzontale fino a 320px.

---

## Linee complete e Usate — stato (29/09, validati IT + EN)

- **Template dedicati** con condizione sul singolo termine (più specifica di quella generale della categoria, quindi vince): **Linee complete** 524 (termine 12) / EN 529 (termine 50), **Macchine usate** 525 (termine 14) / EN 532 (termine 52). I duplicati WPML dei template ereditano la condizione con l'ID del termine italiano: nell'EN va reimpostata sul termine tradotto.
- **Linee complete**: H1 dal nuovo campo `titolo` (`[es_term_titolo]`, se vuoto il nome del termine), intro, frase in evidenza dal nuovo campo `claim`, "Esempi di layout linea" + `descrizione` (`[es_term_descrizione]`) + griglia `[es_layout_linee]` dal repeater `layout` (immagini dalla Libreria media con `srcset`, testo alternativo della Libreria), nav catalogo, invito finale. Le due immagini dei layout reali del wireframe sono in Libreria (ID 516, 517; EN 527, 528 con testo alternativo inglese).
- **Usate**: Collection Loop sulle macchine Eurostar della categoria con **stato vuoto** (`e-collection-loop-empty-state`) = riquadro tratteggiato con `descrizione` + "Richiedi di essere avvisato" (→ Contatti). Quando verranno caricate macchine usate compariranno come card senza intervenire sul template (la riga dati per "usate" non è definita: la card mostrerà nome e tipologia, da decidere quali dati mostrare quando arriveranno).
- **Scostamento voluto**: tolta la nota "Altri esempi di layout in arrivo dal cliente." (appunto interno del wireframe); tutto sotto sale di 49px. La griglia dei layout ha minimo `min(320px,100%)`: a 320px il wireframe sfora, il sito no.
- **Verifica**: 1440 — Linee complete al pixel fino alla griglia (figure 628×972 a x 80/732, didascalie), Usate identica (riquadro 738×282 a x 351, invito a 1063, footer a 1528); 768/375 stesse coordinate del wireframe; nessuno scroll orizzontale a 320px.
- **EN** (testi scritti da me, **da far rivedere**): "Complete bottling lines up to 15,000 containers/hour", intro, claim, descrizione, titoli e didascalie dei layout, testo del riquadro Usate, "Line layout examples", "Available now", "Ask to be notified".
- **Lezione**: i duplicati WPML degli allegati (`make_duplicate`) non copiano `_wp_attached_file` e `_wp_attachment_metadata`: vanno copiati a mano, altrimenti l'immagine EN non viene stampata.

---

## Pagina Squadron — stato (29/09, validata IT + EN)

- **Pagina** "Squadron" (ID 258 / EN 360), modello di pagina **Elementor a larghezza piena** (`elementor_header_footer`: header e footer del Theme Builder, niente titolo né `<main>` di Hello, il `main#content` è il contenitore atomic della pagina). Testi fissi in widget atomic (pacchetto WPML della pagina), parti legate ai dati via snippet `5-eurostar-pagina-squadron.php`:
  - `[es_squadron_gamma]`: card estese dei modelli Squadron **con descrizione** (oggi ATHENA, EXACTA): riquadro immagine 4:3 (immagine in evidenza se presente), nome, tipologia, descrizione + valvole, tabella (Adatta per = settori nell'ordine del modello, Contenitori, Prodotto, Tecnologia, Campo di produzione), link "Scheda tecnica … (PDF, peso)".
  - `[es_squadron_resto]`: modelli senza descrizione, raggruppati con `gruppo_squadron` ("Olympia A / SA", "Olympia AV A / SA", "Evox / Evox Plus / Evox CM"), settori = unione dei modelli del gruppo.
  - `[es_squadron_settori]`: pillole dei settori di tutti i modelli Squadron, nell'**ordine del sito** (campo Ordine del settore: Vino, Birra, Liquori…) invece dell'ordine del wireframe (Acqua, Bevande…): stessa scelta dell'ordine unico già decisa per le macchine.
- **Ancore**: ogni card ha `id` = slug del modello; nei gruppi il primo modello è l'`id` della card, gli altri hanno uno `span` ancora. `scroll-margin-top: 110px` (la card si ferma sotto l'header sticky). Verificato: `/macchine/olympia-sa/` → redirect 302 → `/squadron/#olympia-sa` → card "Olympia A / SA" a 34px sotto l'header; tutte le 14 ancore presenti.
- **Breadcrumb** delle pagine: `[es_breadcrumb_archivio]` gestisce anche le pagine (Home / genitori / pagina).
- **Verifica al pixel**: a 1440, 768 e 375 le 9 sezioni hanno le stesse coordinate e altezze del wireframe (footer a 5980.8 a 1440); card ATHENA/EXACTA coincidenti (628×1027.9, nome, tabella, download). Nessuno scroll orizzontale a 320px (griglie con minimo `min(…px,100%)`).
- **EN**: 14 modelli Squadron EN creati (duplicati WPML, settori EN nell'ordine IT, `gruppo_squadron` copiato); ATHENA/EXACTA con tipologia, descrizione, contenitori, prodotto, tecnologia e capacità in inglese; nomi descrittivi tradotti ("WEIGHT FILLER", "VOLUMETRIC DOSER AND PNEUMATIC CAPPER", "VOL.L LARGE FORMATS"; slug invariati, quindi le ancore restano uguali). Testi della pagina tradotti nel pacchetto WPML. **Tutti i testi EN scritti da me, da far rivedere.** Le schede tecniche PDF sono in italiano anche nella versione EN.
- **Scostamenti**: ordine delle pillole (sopra); nessun segnaposto tratteggiato nel riquadro immagine (come le altre card).

---

## Contatti e Conferma — stato (29/09, validate IT + EN)

- **Contatti** (pagina 260 / EN 364, `/contatti/`, `/en/contact/`) e **Conferma** (nuova, 574 / EN 586, `/conferma/`, `/en/confirmation/` — slug EN provvisorio per il referente SEO), modello Elementor a larghezza piena. Contatti: colonne 40/60 (`es-hub*`; la colonna blu prosegue fino al bordo sinistro dello schermo con uno pseudo-elemento, come nel wireframe), kicker, H1, intro, recapiti `[es_contatti_info]` (snippet `6-eurostar-pagine.php`: indirizzo, telefono, email, WhatsApp in un solo punto), badge "Assistenza H24" / "Ricambi originali".
- **Form**: widget **Form classic di Elementor Pro** (il form atomic di Elementor 4.3 non ha redirect né reCAPTCHA), stilizzato nel Kit (`.es-contact-form`): 13 campi obbligatori del wireframe (select tipo, nome, cognome, azienda, paese, email, telefono, oggetto, prodotto, contenitore, contenitori/ora, messaggio, privacy) + **honeypot** anti-spam; etichette visibili collegate ai campi, asterisco rosso, validazione del browser; **bordo campi Gray 400** (decisione WCAG 1.4.11); pulsante con l'hover diagonale di `es-btn`. Azioni: **email** + **salvataggio invii** (Elementor → Invii) + **redirect** alla pagina Conferma della lingua. Oggetto email `[Sito] Richiesta contatto — {tipo}` (EN: `[Sito EN] …`), "Rispondi a" = email del visitatore.
- **Da completare prima del lancio**: (1) **destinatario** — sullo staging `mauromandala@gmail.com` per tutti i form (IT ed EN; decisione utente 29/09); al lancio `eurostarinfo@eurostar.it` (proposta del wireframe, da confermare con Eurostar); (2) **reCAPTCHA** — servono le chiavi (Elementor → Impostazioni → Integrazioni), per ora solo honeypot; (3) **pagina Privacy Policy** — esiste in bozza (ID 3): il link del consenso punta a `/privacy-policy/` e va in 404 finché non è pubblicata con il testo del cliente. Tolti dal wireframe: segnaposto reCAPTCHA, errore dimostrativo sul campo email, nota interna sotto il pulsante.
- **Responsive** (lacuna del wireframe): sotto 900px colonna informativa sopra il form; sotto 767px campi a tutta larghezza; nessuno scroll orizzontale a 320px.
- **Verifica al pixel a 1440**: colonna informativa identica (kicker, H1, intro, recapiti, badge alle stesse coordinate); campi a x 641.6/929.6, 272 o 560 × 55.2, stesse y fino a "Messaggio" (143px) e privacy; pulsante 180.7×49. Conferma identica (tutti gli elementi e footer a 987.4).
- **EN** (testi scritti da me, **da far rivedere**): intro, badge, etichette, opzioni del select ("I am a producer"…), segnaposto, consenso, messaggi, pagina Confirmation. Le risposte EN arrivano con i valori in inglese (es. tipo di richiesta).
- **Invio di prova (29/09, autorizzato)**: redirect a `/conferma/` riuscito, invio salvato in Elementor → Invii (n. 1, dati fittizi), email inviata (oggetto "[Sito] Richiesta contatto — Altro", Reply-To corretto) tramite il plugin Email Deliverability.
- **Mittente delle email (29/09, decisione utente)**: il plugin **Email Deliverability** (site-mailer) invia tutte le email del sito dal proprio servizio (`site-…@em0001.sitemailerservice.com`) e **impone il proprio nome mittente**, ignorando quello del form: con il campo vuoto usava "Email Deliverability". Impostato **Nome mittente = "Sito Eurostar"** (Email Deliverability → Impostazioni; vale per tutte le email di WordPress). Il plugin aggiunge anche il proprio "Rispondi a" (vuoto → email amministratore `supporto@ne-ws.it`) a quello del form (email del visitatore): rispondendo, togliere l'indirizzo in più. Per un indirizzo mittente del dominio del cliente serve verificare un dominio nel plugin (record DNS) — da fare al lancio.
- **Consenso nell'email**: la casella privacy ha l'etichetta "Consenso privacy" (EN "Privacy consent"), nascosta in pagina con `display:none`, così `[all-fields]` stampa "Consenso privacy: on" invece di una riga "on" senza nome.
- **Correzione 29/09 (segnalata dall'utente)**: in hover il testo di "Invia richiesta" restava bianco sul riempimento bianco: il widget genera `.elementor-XX .elementor-element-ID .elementor-button[type="submit"]:hover { color: #fff }` (specificità 0,6,0), che batteva la regola del Kit. Regola di hover del Kit rinforzata (`.es-contact-form` ripetuta + `[type]`): ora testo e contorno Blue 600 sul riempimento bianco, verificato con il mouse nel browser.
- **Lezioni**: l'opzione "Rispondi a" del form classic si popola solo nell'editor (il validatore accetta solo vuoto): impostata direttamente nei dati; le regole del widget form escono come `.elementor-XX .elementor-element-ID …` e `.elementor-widget-form …`: nel Kit servono selettori con classe ripetuta; se un salvataggio fallisce non cancellare il file caricato prima di aver verificato `success`.

---

## Pagina Settori — stato (29/09, validata IT + EN)

- **Pagina** "Settori" (ID 255 / EN 354 "Sectors", `/settori/`, `/en/sectors/` — slug EN provvisorio per il referente SEO), modello Elementor a larghezza piena, contenitore `main#content`. Testata chiara: breadcrumb `[es_breadcrumb_archivio tema="light"]` (nuovo attributo: senza, resta la variante scura degli archivi; `corrente="…"` sostituisce la voce finale), kicker "Cosa imbottigli?", H1, intro; griglia `[es_settori_griglia]`; invito finale "Non trovi il tuo prodotto? Progettiamo su misura." + "Contattaci" (link di tipo pagina → Contatti, in EN `/en/contact/`).
- **Nuove classi globali riutilizzabili** (stesso schema di testata in Servizi e Chi siamo): `es-sec-hero-light` (fondo bianco, padding `clamp(40px,7vh,80px)` / `clamp(24px,4vh,40px)`), `es-h1-hero` (Display 1 con tracking −0,02em, margini 16px; `max-width` in stile locale: 14ch qui, 26ch Servizi, 18ch Chi siamo), `es-sec-after-hero` (sezione subito sotto la testata: padding alto `clamp(24px,4vh,40px)`, basso come le sezioni).
- **`[es_settori_griglia]`** (snippet `6-eurostar-pagine.php`): una card per termine `settore` della lingua corrente, nell'**ordine del campo Ordine** (Vino, Birra, Liquori…) invece dell'ordine del wireframe (Acqua, Bevande e succhi, Vino…) — stessa scelta dell'ordine unico già decisa per macchine e pillole. Elenco `ul role="list"`, card = link al settore con nome in `span`; immagine = campo `immagine` del termine (nella traduzione, se vuoto, quella del termine italiano), `alt=""` perché il nome è già il testo del link; senza immagine fondo Gray 150 pieno (niente segnaposto tratteggiato, come le card macchina). CSS nel Kit, blocco "pagina Settori": 4 colonne, 2 sotto 900px, 1 sotto 767px (il 560 del wireframe portato al breakpoint Mobile, come da tabella dei comportamenti), rapporto 4:5, sfumatura scura in basso, zoom 1,06 dell'immagine in hover (disattivato con "riduci movimento").
- **Verifica al pixel**: a 1440, 768 e 375 breadcrumb, kicker, H1, intro, griglia, card (302×377,5 a 1440), nome, invito e pulsante hanno le stesse coordinate del wireframe (footer a 2288,4 a 1440). Nessuno scroll orizzontale a 320px (il wireframe sfora a 768 per il footer). Tastiera: dal breadcrumb Tab porta alla prima card con contorno 2px.
- **EN** (testi scritti da me, **da far rivedere**): "What do you bottle?", "Solutions for your sector", intro, "Can't find your product? We design to order.", "Contact us". Le card mostrano i 10 termini EN (i link usano ancora la base `/en/settori/…`: slug della tassonomia per il referente SEO).
- **Lezione**: `make_duplicate` su una traduzione già esistente sovrascrive **titolo e slug** con quelli italiani (qui "Settori"/`settori` sulla pagina EN): ripristinarli dopo il duplicato. Una pagina scritta con `elementor-set-content` non ha il pacchetto di stringhe WPML finché non viene salvata: `wp_update_post()` sulla pagina IT lo registra.
- **Aperti**: immagini dei 10 settori (campo `immagine` dei termini, da caricare quando arrivano le foto).

---

## Pagina Servizi — stato (29/09, validata IT + EN)

- **Pagina** "Servizi e post-vendita" (ID 256 / EN 356, `/servizi/`, `/en/services/` — slug e titolo EN "Services and after-sales services" per il referente SEO), modello Elementor a larghezza piena. Sezioni come il wireframe: testata chiara (breadcrumb `[es_breadcrumb_archivio tema="light" corrente="Servizi"]`: la voce finale è "Servizi" come nel wireframe, non il titolo della pagina), 4 numeri, "Il percorso in 8 tappe", transizione "Il percorso non finisce con la consegna", 4 card post-vendita (`section#assistenza`, ancora mantenuta), 2 riquadri informativi (lingue, garanzia), "Vi assistiamo in tutto il mondo" con la mappa, invito finale "Hai bisogno di assistenza o ricambi?" + "Contattaci" (→ Contatti) + "Oppure scrivi direttamente a …".
- **Testi tutti in widget atomic** (pacchetto WPML della pagina, modificabili dal cliente nell'editor). Classi globali nuove: `es-stats-lg`, `es-stat`, `es-stat-value-lg`, `es-stat-label-lg` (StatBlock "lg" del design system, riusabili in Home/Chi siamo), `es-grid-280`, `es-step`, `es-step-num`, `es-step-title`, `es-body`, `es-svc-grid` (2 colonne, 1 sotto 767px), `es-svc`, `es-svc-media`, `es-svc-body`, `es-spec-label`, `es-svc-title`, `es-svc-text`, `es-info-grid`, `es-info-box`, `es-info-label`, `es-body-sm`, `es-map`. Riusate `es-sec-hero-light`, `es-h1-hero`, `es-sec-body`, `es-sec-range`, `es-display-1`, `es-kicker`, `es-body-lg`, invito finale come Settori. Numeri delle tappe ed etichette delle card in `span` dentro il blocco, come nel wireframe (stessa riga di testo: coordinate identiche).
- **Recapiti post-vendita in un punto** (snippet `6-eurostar-pagine.php`, `es_recapiti()['aftersales']`): `[es_aftersales azione="Richiedi assistenza" telefono="1"]` stampa "azione · email · telefono" con **email e telefono cliccabili** (nel wireframe testo semplice; link Blue 600 sottolineati, CSS nel Kit, blocco "pagina Servizi"); l'azione è un attributo, quindi si traduce nel pacchetto WPML. `[es_aftersales_cta]` = "Oppure scrivi direttamente a …" (String Translation, contesto "Eurostar template").
- **Card post-vendita**: sollevamento di 6px con ombra in hover come nel wireframe (sono blocchi non cliccabili: effetto solo decorativo). Riquadri immagine 16:10 e mappa 21:9 con fondo Gray 150 pieno finché non arrivano le foto e il planisfero (grafica di Ilaria): andranno sostituiti con widget Immagine atomic.
- **Scostamenti voluti**: tolto il riquadro tratteggiato **vuoto** in fondo alla sezione assistenza (segnaposto senza contenuto del wireframe): da lì in giù tutto sale di 54px. Card post-vendita su **1 colonna sotto 767px** (lacuna: il wireframe resta a 2 colonne da 193px a 375 e sfora fino a 972px).
- **Verifica al pixel**: a 1440, 768 e 375 testata, numeri, tappe, transizione, card (628×658,6 a 1440), riquadri, mappa, invito e footer hanno le coordinate del wireframe (meno i 54px del riquadro tolto). Nessuno scroll orizzontale a 320px. Titoli: un H1, H2 di sezione e delle card (come nel wireframe), H3 delle tappe; link email/telefono con nome.
- **EN** (testi scritti da me, **da far rivedere**): tutti i 65 testi della pagina ("From needs analysis to production start-up and after-sales support", "The process in 8 steps", "After-sales support is not an extra service…", "We know your machine", "Or write directly to …", ecc.).

---

## Pagina Chi siamo — stato (29/09, validata IT + EN)

- **Pagina** "Chi siamo" (ID 254 / EN 352 "About us", `/chi-siamo/`, `/en/about-us/`), modello Elementor a larghezza piena, tutti i testi in widget atomic (pacchetto WPML). Sezioni come il wireframe: testata chiara (breadcrumb, "La nostra storia", H1), blocco testo + foto "Dalla progettazione al collaudo", "La nostra storia, in breve" (ultimo paragrafo con grassetto e corsivo inline, supportati da `e-paragraph`), blocco foto + testo "Sostenibilità", "Persone, formazione e territorio", team (8 schede: foto 1:1, nome H3, ruolo, lingue, descrizione) + "Il team allargato" (2 foto 16:9), invito "Vuoi conoscere lo stabilimento da vicino?" → Contatti.
- **Classi globali nuove**: `es-sec-block` (sezione bianca con padding ridotto `clamp(32px,5vh,56px)`), `es-grid-split` (2 colonne auto-fit 320px, gap `clamp(32px,5vw,72px)`, centrate), `es-h2` (preset H2 del design system), `es-stack` (colonna flex gap 20), `es-measure-56` (56ch), `es-media-4-3`, `es-media-1-1`, `es-media-16-9` (riquadri immagine Gray 150 in attesa delle foto: da sostituire con widget Immagine atomic), `es-team-grid` (4 colonne, 2 sotto 900px, 1 sotto 767px), `es-team-body`, `es-team-name`, `es-team-role`, `es-team-lang`.
- **Scostamenti voluti**: tolta la fascia "Testimonianza cliente — sezione oscurata in attesa di citazione reale" (segnaposto del wireframe): invito e footer salgono di 326,8px; da aggiungere quando ci sarà una citazione autorizzata. **H1 su mobile** (lacuna): a 38px "DELL'IMBOTTIGLIAMENTO" è più largo dello schermo sotto ~470px (nel wireframe la pagina sfora): sotto 767px il titolo usa `min(38px, calc(8.8vw - 4px))` (29px a 375, 24px a 320), il resto della pagina sale di ~25px a 375.
- **Verifica al pixel**: a 1440, 768 e 375 tutte le sezioni, i titoli, i paragrafi, le schede del team e le foto hanno le coordinate del wireframe (footer a 5728,1 sotto il main a 1440, meno la fascia tolta). Nessuno scroll orizzontale a 320px. Titoli: un H1, H2 di sezione, H3 per i nomi e "Il team allargato".
- **EN** (testi scritti da me, **da far rivedere**): tutti i 58 testi ("Bottling engineering since 1996", "From design to testing, under one roof", "The faces behind the lines", ruoli e descrizioni del team, "Want to see our plant up close?").
- **Foto**: elencate nel documento per la grafica (artifact "Immagini sito Eurostar", sezione Chi siamo; cartella Drive `Ilaria/Immagini sito/07-chi-siamo`).

---

## Pagina 404 — stato (29/09, validata IT + EN)

- **Template Theme Builder** "Pagina 404" (ID 650 / EN 652, tipo `error-404`, condizione `include/singular/not_found404`), contenitore `main#content`. Risponde con stato HTTP 404. "404" decorativo (`aria-hidden`), kicker "Errore 404", H1 "Pagina non trovata", testo, pulsanti "Torna alla home" (primario) e "Vai al catalogo macchine" (ghost), `nav` "Sezioni principali" con Azienda, Settori, Servizi, Contatti (link di tipo pagina, tradotti da WPML: in EN `/en/about-us/`, `/en/sectors/`…). "Torna alla home" è un link di tipo pagina alla Home (740, EN 742), come in Conferma (574/586); "Vai al catalogo macchine" resta un link URL (`/en/macchine/` nel pacchetto EN).
- **Classi nuove**: `es-sec-404`, `es-404-box`, `es-404-num`, `es-404-nav`, `es-link-u` (link testuale sottolineato, riusabile).
- **Colore del "404"**: il wireframe usa `var(--gray-200)`, token **non definito** nel design system (il numero esce nero); uso un grigio chiaro `#D3D7E2`, coerente con l'intento decorativo.
- **Verifica al pixel**: a 1440 tutti gli elementi e il footer (869,6) coincidono; a 375 scarto di 1,6px (altezza minima in `vh`). EN: "Page not found", "Back to home", "Go to the machine catalogue", "Company / Sectors / Services / Contact" (**da far rivedere**).

---

## Pagina Referenze — stato (29/09, validata IT + EN)

- **Pagina** "Referenze" (ID 257 / EN 358 "References", `/referenze/`, `/en/references/`). Testata scura come Squadron (`es-cat-hero-lg` con altezza minima 42vh), breadcrumb, kicker "I nostri clienti", H1, intro; "I nostri clienti nel mondo" + griglia clienti; invito "Parliamo del tuo prossimo progetto" (nuove classi `es-sec-cta-sm` fondo Navy 950 padding `clamp(56px,8vh,96px)`, `es-inner-60ch`, `es-display-3-white`).
- **Clienti da un'unica fonte** (servirà anche al nastro dei loghi in Home): pagina opzioni ACF **Clienti** (menu admin "Clienti", `post_id` `es_clienti`, gruppo campi "Clienti": repeater `clienti` con `nome` obbligatorio e `logo` facoltativo), caricati i 29 nomi del wireframe. Shortcode `[es_clienti]` (snippet `6-eurostar-pagine.php`): elenco `ul role="list"`, tessera con il logo (alt = nome) se caricato, altrimenti il nome. ACF Multilingual aggiungerebbe la lingua all'ID delle opzioni (`es_clienti_en`, elenco vuoto in inglese): filtro `acf/validate_post_id` che mantiene `es_clienti` per tutte le lingue, anche in salvataggio.
- **Tessere**: nel wireframe `min-height:88px` senza `border-box` = 122px reali; nel sito `min-height:122px` (CSS nel Kit, blocco "pagina Referenze").
- **Verifica al pixel**: 1440 e 375 identici (tessere 200×122 a 1440, invito a 1546,4, footer a 2073,7). Nessuno scroll orizzontale a 320px. EN (**da far rivedere**): "Our customers", "More than 2,000 lines installed worldwide", intro, "Who relies on Eurostar", "Our customers around the world", "Let's talk about your next project".
- **Da fare**: caricare i loghi dei clienti (cartella Drive `08-loghi-clienti`) nei campi Logo della pagina Clienti; foto di sfondo della testata.

---

## Pagina Home — stato (29/09, validata IT + EN)

- **Pagina** "Home" (ID 740 / EN 742, modello `elementor_header_footer`, `main#content`), **pagina iniziale** in Impostazioni → Lettura (WPML abbina la EN: `/` e `/en/`). Sorgenti in `docs/handoff/home-sorgenti/`: `gen_home.py` (JSON della pagina IT, classi come `"@etichetta"` sostituite con gli ID sul server), `home-classes.php` (84 classi), `kit-home.css` (blocco "Eurostar — Home" del Kit), `snip6-home.php` (shortcode), `home-en.json` (traduzioni del pacchetto).
- **Sezioni**: testata 80vh con H1, testo e due pulsanti; "ingegnere" (bottiglia, claim "Progettiamo soluzioni.", 4 numeri agli angoli sopra 900px, nel flusso sotto); "Scopri le macchine per:" con 4 icone (maschera SVG nel Kit, colore Blue 600) e link alle categorie; barra clienti `[es_clienti_nastro]`; bivio "Trova la tua soluzione"; fascia Squadron; due blocchi diagonali (taglio 56px, una colonna sotto 900px); citazione del fondatore; "Macchine in evidenza" (MEC SI → categoria Sciacquatrici, SKILLFILL → scheda, ATHENA → Squadron); News `[es_news_home]` + `[es_news_nav]`; invito finale.
- **News in Home**: carosello degli ultimi 6 articoli con categoria (esclusa "Senza categoria"); **la sezione resta nascosta finché non ci sono articoli** (`.es-home-news.es-home-news:not(:has(.es-news-track))`: la classe va raddoppiata, altrimenti la classe globale, caricata dopo, la batte).
- **Barra clienti sotto 767px**: etichetta sopra il nastro (colonna). Il wireframe a 375 tiene la riga, ma il nastro esce largo 0 e il pulsante pausa finisce fuori schermo (x 400): deviazione voluta.
- **Frecce dei link** (`→` sui link testuali, `↗` sui pulsanti dei blocchi diagonali): `::after` nel Kit, scritte come carattere e non come escape (`"\2192"` perde il backslash quando il CSS viene salvato nel Kit e la pagina mostra "2192"). Margine 0: lo spazio prima della freccia è quello a fine testo del pulsante. `line-height:1` sul `↗`, come lo `span` del wireframe (altrimenti il pulsante cresce di 3px).
- **Corretto in verifica**: colonna destra dei blocchi diagonali `width:auto` (il contenitore atomic resta al 100% e il margine -56px non la allarga fino a 696px); icone 54×54 e logo Squadron 122×42 (nel wireframe il bordo di 1px si aggiunge alla misura); foto dei blocchi diagonali sotto 900px `min-height:286px` (280 + bordo e padding del segnaposto).
- **Corretto dopo la segnalazione dell'utente (29/09)**, card "Scopri le macchine per": icona in alto a sinistra invece che al centro (Elementor usa il `::before` dei contenitori atomic per lo sfondo, con `position:absolute`: nel Kit `position:static`) e "Esplora →" centrato (`e-button` ha `text-align:center` di default e nella card il link è largo quanto la colonna: `text-align:start` in `es-link-arrow`). Il confronto per riquadri non li vedeva: ora si confronta anche la posizione del testo (Range) oltre al riquadro. Controllate con lo stesso metodo le altre 15 pagine: nessun altro caso.
- **Citazione del fondatore**: 6px di scarto nel centrare la frase: il wireframe non carica Roboto corsivo 600 e il browser inclina il 700 normale; il sito usa il corsivo vero (più stretto). Lasciato così.
- **Verifica al pixel**: 1440 e 768 identici in ogni elemento (News escluse: nel wireframe sono inventate, da noi nascoste); a 375 identico tranne la barra clienti (+31px, vedi sopra). Nessuno scroll orizzontale a 320px, IT ed EN. La citazione conserva gli `<span>` in Blue 600.
- **EN** (**da far rivedere**): H1 "Since 1996 we have been designing and building…", "We engineer solutions.", "Discover machines for:", "Find your solution", "Explore by machine type", "Choose by sector", "By your side after installation too", "Engineering built never to stop", "Featured machines", "Latest from the Eurostar blog", "Let's design your next line together"; link alle categorie EN (`rinsers-blowers`, `fillers`, `cappers`, `complete-lines`). Stringhe dello snippet ("Pause/Resume logo scrolling", "Previous/Next news", "Latest news") in String Translation, contesto "Eurostar template".
- **Da fare**: logo Squadron (ora riquadro con velatura bianca al 10%); foto (bottiglia, blocchi diagonali, macchine in evidenza, avatar del fondatore); SKILLFILL non ha la scheda EN, quindi in EN il pulsante porta alla scheda italiana.

---

## Pagina Cataloghi — stato (29/09, validata IT + EN)

- **Pagina** "Cataloghi" (ID 261 / EN 366 "Catalogues", `/cataloghi/`, `/en/catalogues/`). Testata scura (`es-cat-hero` + locale: altezza minima 32vh, padding verticale `clamp(40px,7vh,72px)`), breadcrumb, kicker "Documentazione tecnica", H1, intro; sezione `#cataloghi-elenco` con i gruppi "Catalogo generale" e "Cataloghi per categoria" (titoli e testi in widget atomic: `es-h3`, `es-body-sm`); invito "Non trovi quello che cerchi?" come Referenze. Sorgenti in `docs/handoff/cataloghi-sorgenti/`.
- **PDF dalla pagina opzioni ACF "Cataloghi"** (menu admin "Cataloghi", `post_id` `es_cataloghi`, gruppo "Cataloghi"): gruppo `generale` (titolo, titolo_en, descrizione, descrizione_en, file, file_en) e repeater `cataloghi` (categoria = termine `categoria_macchina`, descrizione, descrizione_en, file, file_en). Titolo e link "Vedi la categoria" delle righe per categoria vengono dal termine nella lingua corrente. Precompilata con i testi del wireframe (descrizioni EN mie, **da far rivedere**), **nessun PDF caricato**: il cliente carica i file da solo. Un solo elenco per tutte le lingue (filtro `acf/validate_post_id` esteso a `es_cataloghi`).
- **Shortcode** `[es_cataloghi tipo="generale"]` e `[es_cataloghi]` (snippet 6): `ul role="list"`, una riga **solo se c'è il PDF** (decisione utente 29/09); formato e peso letti dal file (`size_format`, "PDF, 1,9 MB" in IT, "1.9 MB" in EN). In inglese si scarica il PDF inglese se caricato, altrimenti quello italiano, con ", in Italian" nella riga e `hreflang="it"` sul link. Link "Scarica" con `download` e `type="application/pdf"`; nome accessibile completo con testo nascosto ("Scarica Riempitrici (PDF, 3,1 MB)", "Vedi la categoria: Riempitrici"). Senza righe il CSS del Kit nasconde il gruppo (`.es-catalog-group.es-catalog-group:not(:has(.es-catalog-row))`) e l'intera sezione (`#cataloghi-elenco:not(…)`): la pagina mostra testata e invito finale.
- **Classi nuove**: `es-catalog-groups` (colonna, 56px tra i gruppi), `es-catalog-group`. Righe stilate nel CSS del Kit (blocco "Eurostar — Cataloghi"); icona 46×46 (nel wireframe il bordo si somma ai 44px).
- **Sotto 767px** i link vanno a capo sotto il testo, allineati dopo l'icona: nel wireframe a 375 la colonna del testo resta larga 54–172px e la descrizione va su 7 righe (deviazione voluta).
- **Verifica al pixel** (con un PDF di prova collegato temporaneamente a tutte le righe, poi tolto): 1440 e 768 identici in ogni elemento, stili calcolati compresi (footer a 1734 / 1594); 375 identico in testata e titoli. Nessuno scroll orizzontale a 320px, IT ed EN. Stringhe dello snippet ("Scarica", "Vedi la categoria", "PDF, %s", "in italiano") in String Translation, contesto "Eurostar template". EN (**da far rivedere**): "Download the Eurostar catalogues", "Technical documentation", "General catalogue", "Catalogues by category", "Can't find what you're looking for?", "Download", "View the category".
- **Da fare**: caricare i PDF (italiani ed eventualmente inglesi) nella pagina opzioni "Cataloghi"; se serve, aggiungere la riga "Sistemi movimentazione contenitori" (non presente nel wireframe).

---

## Lavora con noi e posizioni — stato (29/09, validate IT + EN)

- **Pagina** "Lavora con noi" (ID 262 / EN 368 "Careers", `/lavora-con-noi/`, `/en/careers/`): testata scura (`es-cat-hero` + altezza minima 38vh), breadcrumb **Home / Azienda / Lavora con noi** (nuovi attributi `genitore="254" genitore_nome="Azienda"` di `[es_breadcrumb_archivio]`, snippet 4; in EN "Company"), kicker "Carriere", H1; intro (`es-sec-block`, tre paragrafi `es-body-lg`); "Posizioni aperte" (`es-sec-range`, `#posizioni-aperte`) con `[es_posizioni]`; "Candidatura spontanea" (`es-sec-cta`, `#candidatura`) con titolo e testo su 640px e riquadro bianco del form.
- **Posizioni**: 5 post `posizione_lavoro` (IT 766, 768, 770, 772, 774; EN 767…775) con i campi ACF esistenti (reparto, rif, sede, tipo_contratto, la_posizione, repeater attivita / requisiti / requisiti_preferenziali / cosa_offriamo), ordine = "Ordine" (menu_order). Contenuti del wireframe: Area Manager ed Elettricista/PLC reali (Serena), le altre 3 **scritte per il wireframe, da validare col cliente**. URL `/lavora-con-noi/<slug>/`, EN `/en/lavora-con-noi/<slug-en>/` (slug EN provvisori per il referente SEO).
- **Card** `[es_posizioni]` (snippet 6): `ul role="list"`, card `a.es-job-card` (titolo H3, reparto, "Rif. …", sede e contratto con icona e etichetta nascosta "Sede:"/"Contratto:" per i lettori di schermo, "Dettagli" + freccia cerchiata); griglia `minmax(min(340px,100%),1fr)` (nel wireframe 340px fissi: a 375 la card sfora). Senza posizioni pubblicate la sezione si nasconde (`#posizioni-aperte:not(:has(.es-job-card))`).
- **Template Theme Builder** "Scheda posizione" (785 / EN 789, `single-post`, condizione `include/singular/posizione_lavoro`): testata come la pagina (breadcrumb Home / Lavora con noi / posizione, kicker `[es_posizione_kicker]` = "Reparto · Rif. …" via tag dinamico shortcode, H1 = titolo del post, `[es_posizione_meta]`); corpo `es-job-detail-grid` (1.6fr / 1fr, gap 72px): "La posizione" + `[es_posizione_corpo]` (testo introduttivo e sezioni a elenco, quelle vuote non compaiono), riquadro `es-job-sidebar` (fisso durante lo scorrimento, top 110px) con "Candidati per questa posizione" e form; sotto la descrizione il link "Vedi tutte le posizioni aperte" (`es-job-back`, link di tipo pagina a 262). **Sotto 900px una colonna** (descrizione, form, link): nel wireframe a 768 il riquadro resta largo 232px con campi da 76px (lacuna).
- **Form di candidatura** (Elementor Pro, classi `es-contact-form es-job-form`): spontanea = Nome, Cognome, Email, Ambito di interesse, CV (PDF, obbligatorio, max 5 MB, **allegato all'email**), Messaggio, consenso privacy, honeypot; oggetto "[Sito] Candidatura spontanea". Posizione = stesso form senza "Ambito" + campo nascosto `posizione` = titolo del post (tag dinamico), oggetto "[Sito] Candidatura — {posizione}". Destinatario mauromandala@gmail.com (come gli altri form dello staging), "Rispondi a" = email del candidato, salvataggio in Invii, poi pagina Conferma (EN `/en/confirmation/`); in EN oggetto "[Sito EN] …". `email_reply_to` si scrive dopo `elementor-set-content` (il validatore rifiuta il valore perché le opzioni sono generate a runtime). **Non inviato nessun form di prova.**
- **Classi nuove**: `es-job-detail-grid`, `es-job-sidebar`, `es-job-back-row`, `es-job-back` (misure, bordo e colori nella classe: il pulsante atomic ha padding 12/24 e bordo 0 di base caricati dopo il Kit), `es-inner-640`, `es-form-box`. CSS del Kit blocco "Eurostar — Lavora con noi": card, testata, corpo, riempimento in hover e freccia del link di ritorno, campo CV, consenso.
- **Trappole trovate**: nel CSS personalizzato del Kit Elementor sostituisce la parola `selector` con `.elementor-kit-8` (anche dentro `::file-selector-button` e nei commenti): per il pulsante "Scegli file" si usa solo `::-webkit-file-upload-button`.
- **Verifica al pixel**: pagina elenco e scheda a 1440 identiche in ogni elemento e nella posizione del testo, form compreso (campo CV 49px, casella del consenso con margini predefiniti come nel wireframe, testo a 470); a 768 elenco identico, scheda in una colonna (vedi sopra); a 375 testata, intro e titoli identici, card entro 330px, campi a tutta larghezza. Nessuno scroll orizzontale a 320px, IT ed EN. Tolta la nota del wireframe sotto il pulsante ("Proposta da confermare con Eurostar…", annotazione per il cliente): footer 64px più in alto.
- **EN** (**da far rivedere**): pagina (Careers, Open positions, Speculative application, testi), 5 posizioni tradotte per intero, etichette del form (First name, Last name, Area of interest, Send application…), stringhe dello snippet (Ref. %s, Details, Permanent, Fixed-term, Main responsibilities, Requirements, Preferred qualifications, What we offer).
- **Da fare**: validare col cliente le 3 posizioni scritte per il wireframe; confermare destinatario reale delle candidature (nel wireframe eurostarinfo@eurostar.it) e se il CV va allegato o solo linkato; prova di invio quando autorizzata.

---

## News e casi studio — stato (30/09, validate IT; EN solo pagina)

- **Contenuti** dal documento "TESTI NEWS SITO EUROSTAR + Casi studio.docx" (convertito in `docs/handoff/news-sorgenti/testi-news-originale.txt`, trasformato da `gen_news.py` in `news-it.json`): 16 articoli WordPress (`post`, ID 823–838) — 2 casi studio (Frantoio Bonamini, Castello di Verrazzano) e 14 articoli. Escluse le note redazionali (Nota, Suggerisco, IMG ALLEGATE, Troverai, [DA VERIFICARE]: tolta la frase sulla sigla "VP-PP" nell'art. 3). Estratto delle card: per i casi studio la "versione sintetica per carosello", per gli articoli la prima frase. Titoli di sezione → H2, elenchi → `ul`, citazioni → `blockquote` con `cite`.
- **Categorie** (decisione utente 30/09, quelle del documento; ordine = term meta `ordine`): Casi studio, Fiere ed eventi, Prodotti e tecnologie, Mercati internazionali, Territorio e formazione, Sostenibilità e territorio, Chi siamo. Sulle card dei casi studio l'etichetta è "Caso studio" (singolare).
- **Date**: dal documento; casi studio = mese della fonte (aprile e settembre 2026); **provvisorie** (decisione utente, da correggere quando arriveranno le vere): GT Vinea 16/02/2026, Bambini delle Fate 24/03/2026, Open Day ITIS Artom 24/01/2026, Apprendistato Gabriele 08/07/2026, Soluzioni olio 14/04/2026, Cosmachine 26/08/2026.
- **Immagini** (cartella Drive `09-news`, 12 in Libreria ID 811–822, rinominate `news-*.jpg`; AVIF e PNG convertiti in JPEG; testo alternativo scritto guardando le immagini). POWERFILL "I Grandi Oli d'Italia" sull'art. 12 (decisione utente). **Mancano**: i 2 casi studio (il documento dice "IMG ALLEGATE" ma non ci sono), "Brevetti pionieri", POWERFILL (art. 13): riquadro Gray 150. "CFIA Rennes" senza articolo: non caricata. Locandine (GT Vinea, Cosmachine) e l'immagine verticale POWERFILL nel riquadro 16:7 vengono tagliate sopra e sotto.
- **Campi ACF**: gruppo "Articolo" (tutti i post: `tempo_lettura` — se vuoto calcolato a 200 parole/min —, `fonte`); gruppo "Caso studio" (categoria Casi studio: `cliente` per il breadcrumb, `settore`, `luogo`, `macchina_config`, `macchine` relazione → "Macchine nel progetto" (MEC AV 165, MAXIMA 163), `voce_cliente` testo/nome/ruolo/foto → "La voce del cliente" (l'ultima citazione del cliente, tolta dal corpo per non ripeterla), `numeri` valore/etichetta, vuoti per ora).
- **Pagina News** (259 / EN 362): testata scura (`es-cat-hero-lg` + 42vh), barra filtri `[es_news_filtri]` (stesse classi e comportamento del catalogo: sticky sotto l'header, scorre in orizzontale con sfumature; `aria-pressed`) — **corretto 30/09** (segnalazione utente): la barra si fermava 42px sotto la nav perché lo script calcolava la posizione prima che l'header ricevesse il suo spostamento (`top:-42px`, altezza della barra alta); ora la posizione è altezza header meno barra alta, ricalcolata anche al caricamento completo (76px a 1440, 80px a 375), griglia `[es_news_griglia]` (4 colonne da 300px `auto-fill`: con un filtro le card restano larghe uguali; estratto su 4 righe), 12 articoli + "Mostra altri articoli" (+12, focus sul primo aggiunto), conteggio per i lettori di schermo, `?categoria=slug` preseleziona il filtro. Gli archivi di categoria di WordPress (`/category/…`) rimandano all'archivio filtrato (302).
- **Template Theme Builder**: "Articolo" (849, `include/singular/post`) — testata chiara (breadcrumb Home / News / categoria, senza `aria-current` sulla categoria), H1 display-1 max 22ch, meta (categoria, data, minuti), immagine 16:7, corpo `[es_articolo_corpo]` (testo + fonte in piccolo), "Continua a leggere / Altri articoli" (ultimi 3 articoli, casi studio esclusi). "Caso studio" (851, `include/singular/in_category/66`) — breadcrumb Home / Referenze / cliente, H1 display-2, meta blu (data · Settore · Installazione), griglia 70/30 con colonna laterale `[es_caso_aside]` fissa (una colonna sotto 900px, lacuna del wireframe), "Altri casi studio".
- **Home**: il carosello News mostra gli ultimi 6 articoli con la stessa card dell'archivio (`es_news_card`). **Rivisto 30/09 (richiesta utente)**: niente spazio laterale nel carosello (si vedevano pezzi della card successiva e di quella precedente; restano 48/60px sopra e sotto per sollevamento e ombra), quindi niente più `scroll-padding`; **contatore a pallini** sotto le card (uno per posizione delle frecce: 4 con 3 card visibili, 6 su mobile; pulsanti 24×24 con `aria-current` e nome "News 1–3 di 6"), frecce con `aria-disabled` e attenuate all'inizio e alla fine, focus delle card all'interno del bordo.
- **Verifica al pixel** (1440, 768; 375/320 senza scroll orizzontale): archivio, articolo e caso studio coincidono col wireframe in testata, filtri, griglia, card, corpo (H2, paragrafi, citazioni), colonna laterale e correlati; le differenze di altezza dipendono solo dai testi reali, più lunghi. Frecce del carosello in Home provate: avanti/indietro di una card.
- **EN** (decisione utente: solo italiano per ora): pagina News EN con testata tradotta e "No articles published yet."; carosello nascosto nella Home EN; stringhe dello snippet già tradotte. Template EN da creare quando arriveranno gli articoli tradotti.
- **Corretto 30/09 (trovato verificando la ricerca)**: la regola del breadcrumb degli articoli (`li:last-child:not(:has(a))` → Navy 800) aveva specificità più alta della variante scura e colorava di Navy 800 la voce corrente in **tutte le testate scure** (News, Cataloghi, catalogo, categorie, settori, Lavora con noi…): contrasto insufficiente. Limitata a `.es-breadcrumb--light`; copia del Kit precedente in `es_kit_css_backup_breadcrumb`.
- **Da verificare con il cliente**: date vere dei 6 articoli; immagini dei casi studio e di "Brevetti pionieri"; testo per "CFIA Rennes"; sigla "VP-PP" (art. 3); "circa 60 Paesi" (art. 4) contro "oltre 100 paesi" del resto del sito. Articolo di prova "Hello world!" di WordPress spostato nel cestino (30/09).

---

## Effetto al passaggio del mouse sulle card — stato (30/09)

- **Richiesta utente**: card macchina (componente 448: catalogo, categorie, settori), card Squadron compatte (450) e card settore (`[es_settori_griglia]`) con lo stesso effetto delle card news: sollevamento di 6px e ombra `0 18px 44px rgba(0,0,0,.12)` in .35s; sulle card macchina anche zoom 1,05 dell'immagine in .6s (le card settore mantengono il loro zoom 1,06 del wireframe). Disattivato con "riduci movimento". Nel wireframe le card macchina non avevano effetti.
- CSS in fondo al Kit (blocco "Card macchina, Squadron e settore"), copia in `docs/handoff/kit-card-hover.css`; copia di sicurezza del Kit precedente nell'opzione `es_kit_css_backup_hover_card`. Nessun contenitore delle griglie taglia l'ombra (`overflow` visibile).
- **Nota per le verifiche**: nei Chrome recenti anche le regole normali hanno `cssRules` (vuoto, per il CSS annidato): per leggere i fogli di stile da JS si controlla `r instanceof CSSStyleRule`, altrimenti le regole vengono saltate.

---

## Snippet PHP nel repo — stato (30/09)

- I 6 snippet FluentSnippets dello staging sono copiati per intero in `wordpress/snippets/` (checksum MD5 verificate), con un `README.md` che spiega a quale template o pagina serve ciascuno e quali shortcode contiene. Prima nel repo c'erano solo i pezzi aggiunti in questi giorni (`docs/handoff/*-sorgenti/snip6-*.php`, restano come storia delle singole modifiche); gli snippet 1–5 non avevano copia.
- La fonte di verità resta lo staging: dopo ogni modifica a uno snippet va riesportato in `wordpress/snippets/` e committato (procedura nel README).

---

## Variabili globali — stato (29/09)

- **37 variabili Elementor v4** (editor → pannello Variabili; nel CSS escono come `--nome` in `:root` del file del Kit, quindi valgono su tutte le pagine):
  - colori: `navy-950` #000A23, `navy-900` #0D1633, `navy-800` #1C265A, `navy-600` #2E4491, `blue-600` #25387E, `blue-500` #3A56A8, `blue-400` #5B72AD, `blue-300` #9FB0D8, `gray-050` #F2F4FA, `gray-100` #EDEFF4, `gray-150` #E3E6EE, `gray-200` #D3D7E2, `gray-400` #888B8D, `gray-500` #4A4F5E, `gray-600` #3A3F4E, `gray-700` #2B3040, `bianco`, `bianco-82` (#FFFFFFD1), `bianco-72` (#FFFFFFB8), `bordo-sottile` (#1C265A29 = rgba(28,38,90,.16)), `hero-fondo` #767C9D (fondo testate scure finché mancano le foto);
  - font: `font-titoli` Barlow, `font-testo` Roboto;
  - misure: `sezione-x`, `sezione-y`, `sezione-compatta`, `testata-y`, `contenuto-max` 1360px, `contenuto-stretto` 1180px, `testo-display-1/2/3`, `testo-h2/h3/h4`, `testo-body-lg`, `testo-body-md` (valori del design system).
- **Classi globali collegate**: 97 classi, 225 valori (colori, font, misure) ora puntano alle variabili invece che a valori scritti. Rimasti scritti solo valori usati una o due volte (spaziature verticali di singole sezioni, velature delle testate, bianchi semitrasparenti del footer, `transparent`).
- **CSS personalizzato del Kit**: 161 colori e 50 font sostituiti con `var(--…)`; restano fissi solo `#25D366` (WhatsApp) e `#CD212A` (asterisco obbligatorio).
- **Copie di sicurezza** (opzioni WordPress): `es_classes_backup_20260929` (JSON delle 150 classi prima della conversione), `es_kit_css_backup_20260929` (CSS del Kit prima); mappa etichetta → ID variabile in `es_variabili_map`.
- **Verifica**: stessi valori calcolati (font, colori, padding) e stessa posizione del footer su Servizi 5928,5, Settori 2288,4, Chi siamo 5846,1, Referenze 2191,7, Squadron 5980,8, Conferma 987,4, catalogo (testata 432, card a 690).
- **Corretto durante la verifica** (preesistente): il pulsante "Invia richiesta" usciva `#3A56A8` (colore "In risalto" del Kit) perché `.elementor-widget-form .elementor-button[type=submit]` (0,3,0, caricata dopo il Kit) batteva la nostra regola; selettore rinforzato con `[type]`, ora Blue 600 come nel wireframe.
- **Regola per le pagine nuove**: nelle classi usare le variabili (`{"$$type":"global-color-variable"|"global-font-variable"|"global-size-variable","value":"e-gv-…"}`, ID in `es_variabili_map`), nel CSS del Kit `var(--nome)`. I colori globali del Kit (sistema classico, 18 colori e 21 stili di testo) restano per i widget classici (form) e non sono collegati alle variabili.
- **Non convertiti**: gli stili locali dentro le singole pagine/template (pochi: margini e qualche colore, es. ultimo paragrafo della storia in Chi siamo).

---

## Risultati di ricerca — stato (30/09, validati IT + EN)

- **Il wireframe non ha la pagina**: disegnata sul modello dell'archivio News. **Template Theme Builder** "Risultati di ricerca" (855 / EN 857, tipo `search-results`, condizione `include/archive/search`), contenitore `main#content`. Sorgenti in `docs/handoff/ricerca-sorgenti/`: `gen_ricerca.py` (JSON del template, classi come `"@etichetta"`; il Collection Loop viene copiato sul server dal template 512 cambiando solo il Query ID), `snip6-ricerca.php` (blocco "Ricerca" dello snippet 6), `kit-ricerca.css` (blocco "Eurostar — Ricerca" del Kit), `ricerca-en.json` (traduzioni).
- **Testata scura** come Cataloghi (`es-cat-hero` + altezza minima 32vh, `#es-sr-hero`): breadcrumb Home / Ricerca (nuovo ramo `is_search()` di `[es_breadcrumb_archivio]`, snippet 4), kicker "Ricerca nel sito", H1 `[es_ricerca_titolo]` = "Risultati per «…»" (senza parole: "Cerca nel sito"; va a capo anche con parole lunghissime), sommario `[es_ricerca_sommario]` ("21 risultati", "Nessun risultato"), `[es_ricerca_form]`: campo con etichetta visibile e pulsante "Cerca" per una nuova ricerca e, con più di un tipo di risultato, `nav` "Tipi di risultato" con i link ai gruppi e il loro numero (il gruppo si ferma 34px sotto l'header, come le ancore di Squadron).
- **Gruppi** (H2 con il numero dei risultati; 64px tra un gruppo e l'altro): **Macchine** = stesso Collection Loop e stessa card (componente 448) del catalogo, Query ID `es_ricerca_macchine` (le macchine trovate, nell'ordine dei risultati; gruppo nascosto se vuoto); poi `[es_ricerca_risultati]`: **Settori e categorie** (termini `settore` e `categoria_macchina`), **Pagine**, **News** (stessa card dell'archivio, titolo H3), **Posizioni aperte**. Righe = link con tipo, titolo H3 e circa 200 caratteri del testo attorno alla parola trovata, con le parole evidenziate (`mark`).
- **Motore** (snippet 6, `es_ricerca()`): indice per lingua dei contenuti pubblicati (macchine: titolo, tipologia, descrizione, prodotto, contenitori, tecnologia, categoria, settori, linea; termini: nome, intro, descrizione; pagine: testi dei widget letti da `_elementor_data`, perché molte pagine Elementor hanno `post_content` vuoto; news: titolo, estratto, testo, categoria; posizioni: tutti i campi ACF di testo). Ogni parola deve comparire; senza accenti né maiuscole; articoli e preposizioni IT/EN ignorati; parole di 5+ lettere trovate anche senza l'ultima lettera (riempitrice → Riempitrici, bottiglia → bottiglie). Ordine: più parole nel titolo prima, poi macchine Eurostar e poi Squadron nell'ordine del campo Ordine, news dalla più recente. Escluse Home, Conferma e "Sample Page". Indice in cache (transient `es_ricerca_{lingua}`, un giorno), svuotata quando si salva o cancella un contenuto, un termine o una pagina opzioni ACF.
- **Stato vuoto** (nessun risultato o nessuna parola): "Nessun risultato per «…»" + suggerimento, link alle categorie di macchine e ai settori (ordine del campo Ordine), pulsante "Vai al catalogo macchine". In fondo a tutte le ricerche l'invito "Non trovi quello che cerchi?" + "Contattaci" (link di tipo pagina → Contatti), come Cataloghi.
- **Verifica**: 1440 — card macchina 302×419,8 come il catalogo, 4 colonne; 768 due colonne; 375 e 320 una colonna, nessuno scroll orizzontale (anche con una parola di 40 lettere). Tastiera: breadcrumb → campo (contorno bianco 2px sul riquadro) → Cerca → link ai gruppi (contorno 2px), Invio porta al gruppo. Un H1, H2 per gruppo, H3 per card e righe. Il testo cercato è sempre codificato (prova con `<b>…</b>"&'`); richieste con `<img onerror>` bloccate già dal firewall del server (403). EN: `/en/?s=…` con testi, link e contatti inglesi (`/en/contact/`, `/en/macchine/`); trova solo i contenuti tradotti (oggi MEC LD e i 14 Squadron, nessuna news).
- **EN** (**da far rivedere**): "Site search", "Results for “…”", "Search the site", "Sectors and categories", "No results for “…”", "Where would you like to start?", "Go to the machine catalogue", "Can't find what you're looking for?"… (elenco in `ricerca-en.json`).
- **Per il referente SEO** (da annotare, non da decidere qui): titolo del documento "Risultati della ricerca per “…” – Eurostar Handmade With Love" (titolo e motto del sito) e indicizzazione delle pagine di ricerca.
- **Da decidere**: la pagina di esempio di WordPress "Sample Page" (ID 2) è pubblicata; esclusa dalla ricerca, ma raggiungibile a `/sample-page/`.

---

## Test finali — stato (30/09)

Script e istruzioni in `docs/handoff/test-finali/` (Playwright senza installazioni; report JSON nella stessa cartella).

### Confronto al pixel (24 pagine × 375/768/1024/1280/1440)

- **Nessuno scroll orizzontale** su nessuna pagina a nessuna larghezza; nessuna differenza di font o colore (tranne il "404" decorativo, voluto).
- **Corretto durante i test**:
  - **Card macchina** (catalogo, categorie, settori), a 1280: la colonna dati di destra era 7,8px più a destra. Il componente del wireframe (MachineCard del design system) ha `flex:1` **senza** `min-width:0`: la colonna con la parola più lunga ("CONTENITORI/ORA") si allarga. Tolto `min-width:0` da `.es-mcard-stat` → scarto 0 a tutte le larghezze.
  - **404 e Conferma**, a 1024 e 1280: sezione più bassa (752 contro 864px a 1024). Nel wireframe `min-height:64vh` vale per il solo contenuto (`content-box`) e il padding si somma. Classi `es-sec-404` / `es-sec-confirm` con `min-height: calc(64vh + 2 * clamp(90px,16vh,180px))` → identiche a tutte le larghezze (copie in `es_class_backup_es-sec-404` / `-confirm`).
  - **Consenso privacy** dei form (Contatti, candidature): casella con i margini predefiniti del browser come nel wireframe (4px a sinistra, testo a 26px), prima solo nel form candidature; aggiunto l'asterisco rosso " *" in fondo al testo del consenso, come nel wireframe (con l'asterisco il testo va a capo come nel wireframe: pulsante "Invia candidatura" a 1024 ora alla stessa y). Asterisco escluso dai lettori di schermo (la casella ha `required`).
- **Scostamenti rimasti, tutti già decisi o dovuti ai contenuti**: ordine di settori e pillole (campo Ordine); filtri del catalogo su una riga; galleria della scheda senza miniature e Download nascosto senza PDF; scheda macchina e posizione in una colonna sotto 900px (il wireframe resta a due colonne strette); Servizi, Chi siamo, Linee complete, Lavora con noi, Contatti (errore dimostrativo e reCAPTCHA) senza i segnaposto del wireframe; Cataloghi senza PDF; News, articolo e caso studio con i testi reali; barra clienti a 375 con l'etichetta sopra; citazione del fondatore (6px, corsivo vero); breadcrumb di Sciacquatrici a 375 che va a capo (nel wireframe sfiora il bordo); Referenze a 1440: un nome cliente va a capo in modo diverso (±8px locali).
- **Da segnalare al cliente**: il wireframe scrive "Sughero a fungo" nella card GEMINI/F-IES e "Sughero fungo" nella scheda; il sito usa un solo campo ("Sughero fungo").

### Tastiera

- **Scansione** di 23 pagine (IT, Home EN, ricerca) a 1440 e 375: primo Tab sempre "Vai al contenuto"; **nessun elemento nascosto raggiungibile col Tab**; focus visibile su tutti gli elementi (il campo della pagina di ricerca lo mostra sul riquadro che lo contiene).
- **Banner cookie** (plugin Cookiez, in shadow DOM): finché non si sceglie trattiene il focus nei suoi 4 pulsanti, come una finestra modale; dopo la scelta resta il pulsante tondo "Cookies" raggiungibile in fondo alla pagina.
- **Corretti nello script dell'header** (snippet 2, copia in `es_snip2_backup_tastiera`; blocco in `docs/handoff/test-finali/sorgenti/snip2-tastiera.js`):
  - **mega-menu**: il pannello sta dopo tutta la nav, quindi dopo Invio il Tab andava a "Settori" e le voci del pannello arrivavano solo dopo tutta la navigazione, con il pannello ancora aperto. Ora Tab dal pulsante aperto entra nella prima voce, Tab dall'ultima chiude ed esce verso la voce dopo il pulsante, Maiusc+Tab dalla prima torna al pulsante; il pannello si chiude quando il focus esce;
  - **menu mobile**: con Tab oltre l'ultima voce il focus finiva sul contenuto coperto dal pannello aperto; ora il menu si chiude quando il focus esce dall'header;
  - **"Torna su"**: scorreva in cima ma lasciava il focus in fondo (il Tab successivo andava a WhatsApp); ora porta il focus all'header (senza contorno), il Tab riparte da "Catalogo".
- **Prove di interazione** (`interazioni.js`, 29/29 superate): salta al contenuto, mega-menu (apertura, ingresso, uscita, Esc), ricerca (Invio apre con focus nel campo, Esc torna alla lente, Invio cerca), torna su, filtri del catalogo (Invio/Spazio, `aria-pressed`, annuncio "10 macchine"), "Mostra altri articoli" (focus sulla card 13), carosello Home (una card per pressione, pallino aggiornato, `aria-disabled` in fondo), hamburger a 375 (etichetta Apri/Chiudi, Esc), form Contatti (etichette su tutti i campi, ordine del Tab = ordine visivo, anti-spam non raggiungibile). Mouse sul mega-menu invariato.
- **Copia completa del CSS del Kit** in `wordpress/kit-custom-css.css` (esportata il 30/09 dopo le correzioni; la fonte di verità resta lo staging).

### Zoom 200% (30/09)

- Emulato come finestra 1280×900 al 200% = 640×450 px CSS a densità 2 (`zoom.js`, 23 pagine). **Nessuno scroll orizzontale, nessun testo tagliato o sovrapposto**; header fisso 80px su 450 (18%), 131px con la barra dei filtri (catalogo, News). Il menu hamburger occupa lo schermo e scorre (618px di voci in 450): tutte le voci raggiungibili.
- **Corretto**: durante il Tab l'elemento con il focus finiva sotto l'header fisso (7 volte in Home, 21 nel catalogo), perché il browser lo porta in vista ignorando lo spazio coperto. Lo script dell'header (snippet 2, copia in `es_snip2_backup_zoom`) imposta ora `scroll-padding-top` = altezza visibile dell'header (+ barra dei filtri sticky), ricalcolata al ridimensionamento e al caricamento, e `scroll-padding-bottom: 72px` (WhatsApp). Le card intere più alte dello spazio libero (card macchina, news, ~420px contro ~300) mostravano la parte bassa con il nome sotto l'header: con il focus da tastiera (`:focus-visible`) la pagina scorre per mostrarne l'inizio. Risultato: 0 focus coperti su tutte le pagine.
- Le ancore (`scroll-margin-top`) passano da 110 a 34px, perché ora lo spazio dell'header è nello `scroll-padding` (Kit, copia in `es_kit_css_backup_zoom`): Squadron `/macchine/olympia-sa/` → `#olympia-sa` e i gruppi della ricerca restano esattamente 34px sotto l'header a 1440, 640 e 375. Prove di interazione di nuovo 29/29.
- **Limite noto**: i pulsanti tondi fissi (cookie del plugin e WhatsApp) coprono il bordo destro dei campi dei form e restano sopra il menu aperto; nessuna voce o campo diventa irraggiungibile.

### Da fare

- Scanner Ally dall'admin (richiede l'accesso all'amministrazione).
- VoiceOver sulla scheda MEC LD.
- **Decisioni 30/09 (utente)**: il singolo servizio non si fa (restano le card della pagina Servizi); le 20 schede macchina EN e i 9 settori EN restano in attesa delle traduzioni di chi se ne occupa: quando arrivano si caricano con la procedura EN dei handoff 6/7 (duplicato WPML, campi ACF, ordine dei settori).
