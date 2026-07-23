# ISTRUZIONI DI PROGETTAZIONE WIREFRAME: NUOVO SITO B2B EUROSTAR (COMPLIANT, CRO-ORIENTED & MULTILINGUA)

## Obiettivo & Requisiti di Progetto
Generare wireframe strutturati per il nuovo sito web B2B di Eurostar sulla base dell'architettura dell'informazione approvata. L'intera alberatura del sito è stata condensata e ottimizzata in **6 Master Template modulari**.

I wireframe generati dovranno rispondere a tre requisiti cardine:
1. **Accessibilità (WCAG 2.2 Livello AA):** Navigabilità da tastiera, contrasti conformi, gerarchia semantica pulita e corretti attributi ARIA.
2. **CRO (Conversion Rate Optimization):** Focus su lead generation B2B ad alto valore aggiunto, gestione di macro-conversioni (richiesta commerciale) e micro-conversioni (download tecnici).
3. **Multilingua:** Layout flessibili che assorbono variazioni di lunghezza del testo (fino al +40% per lingue come il tedesco) senza compromettere il design.

---

## LINEE GUIDA GENERALI DI ACCESSIBILITÀ & MULTILINGUA

1. **Gerarchia Semantica & Layout Flessibili:**
   - Ogni pagina deve avere un solo ed unico `<h1>`.
   - La gerarchia dei titoli (`<h2>`, `<h3>`, `<h4>`) deve essere lineare e priva di salti.
   - Tutti i contenitori di testo (card, bottoni, tabelle) devono usare un layout flessibile (no altezze/larghezze fisse per i testi) per evitare sovrapposizioni o troncamenti quando si passa a lingue con parole più lunghe (es. Tedesco).

2. **Navigabilità da Tastiera & Selettore di Lingua:**
   - Tutti gli elementi interattivi (link, bottoni, campi di input, tab) devono essere raggiungibili tramite tasto `TAB` in modo logico.
   - Il **Selettore di Lingua** nell'header deve essere un elemento di navigazione nativo (non un'immagine), accessibile da tastiera. Usa i nomi delle lingue scritti nella loro grafia nativa (es. *English, Italiano, Deutsch*) anziché le bandiere nazionali.
   - Prevedi visivamente uno stato di `:focus-visible` chiaro e ad alto contrasto per chi naviga senza mouse.

3. **Contrasto Visivo & Testi:**
   - Il contrasto colore tra testo e sfondo deve essere di almeno **4.5:1** per i testi normali e **3:1** per i testi grandi.
   - I link all'interno dei testi devono essere chiaramente distinguibili (es. sottolineati), non affidarsi solo al cambio di colore.

---

### TEMPLATE 1: L'AMMIRAGLIA (LANDING PAGE)
**Pagine reali mappate su questo layout:** 
- `/` (Home Page)
- `/squadron` (Landing page indipendente del sub-brand)
- `/macchine/linee-complete` (Presentazione del ruolo di lead integrator)

**Focus CRO:** Storytelling ad alto impatto, riprova sociale immediata e instradamento rapido dei diversi profili di buyer (chi cerca la macchina vs chi parte dal settore).

**Struttura del Wireframe (Dall'alto in basso):**
1. **Hero Section:**
   - Titolo forte (H1 - proposta di valore chiara).
   - Sottotitolo (breve descrizione della tecnologia o del posizionamento).
   - Due CTA affiancate e differenziate visivamente: "Vedi le Macchine" (Primaria) / "Parla con un Tecnico" (Secondaria).
   - *Accessibilità/CRO:* Se è presente un video di sfondo in loop, deve esserci un pulsante visibile "Pausa/Play".
2. **Social Proof / Trust Bar:**
   - Loghi dei clienti principali o dei settori industriali serviti su una singola riga orizzontale a basso contrasto (con testi `alt` descrittivi).
3. **Key Metrics (I Numeri Chiave):**
   - Griglia a 3 o 4 colonne con numeri grandi e brevi etichette (es. "Anni di esperienza", "Installazioni globali", "BPH massimi di produzione").
4. **Bivi di Navigazione (Core Paths - Alta rilevanza CRO):**
   - Due grandi box affiancati per dividere i flussi degli utenti: "Esplora per tipologia di Macchina" vs "Scegli in base al tuo Settore".
   - Ciascun box deve contenere una CTA testuale o un bottone chiaro. L'intera card non deve essere un link generico.
5. **Sezione Focus Alternata:**
   - Layout a due colonne: Testo a sinistra (H2 + paragrafo descrittivo sull'innovazione, es. HMI 3D o AI) e immagine tecnica o diagramma a destra.
6. **CTA Finale di Chiusura:**
   - Banner a tutta larghezza con un titolo persuasivo e un bottone di contatto diretto.

---

### TEMPLATE 2: IL MODULARE MULTIUSO (CONTENUTO)
**Pagine reali mappate su questo layout:** 
- Area Azienda: `/chi-siamo`, `/stabilimento`, `/innovazione-e-sostenibilita`
- Area Team: `/team` (Sfrutta la griglia per i volti e i dettagli dei responsabili)
- Area Settori: `/settori` (Hub d'ingresso) e tutti i verticali (`/settori/vino`, `/settori/distillati-liquori`, `/settori/birra`, `/settori/olio-evo-aceto`, `/settori/succhi-rtd`, `/settori/acqua-bevande-gassate`)
- Area Servizi: `/servizi` (Hub), `/servizi/assistenza`, `/servizi/remote-view`
- Area Rete: `/rete-commerciale` (Variante con mappa attiva)

**Focus CRO:** Informare e rassicurare il buyer tecnico, guidandolo dolcemente verso la conversione tramite blocchi flessibili.

**Struttura del Wireframe (Dall'alto in basso):**
1. **Header Pagina Interna:**
   - Breadcrumb di navigazione in alto (strutturata come lista ordinata `<ol>` con `aria-label="Breadcrumb"`).
   - Titolo H1 pulito.
2. **Blocco Contenuto Alternato (Ripetibile / Modulare):**
   - Sezione A: Testo descrittivo a sinistra (H2 + paragrafo) e immagine/diagramma a destra.
   - Sezione B: Immagine a sinistra e testo descrittivo a destra (H2 + paragrafo).
3. **Griglia Modulare di Approfondimento (3 Colonne):**
   - Elemento flessibile che a seconda della pagina ospiterà: le schede del team (Foto, Nome, Ruolo, Lingue), i benefit specifici di un settore o le caratteristiche di un servizio.
   - *Accessibilità:* Se usi icone per illustrare i benefit, queste devono essere decorative (`aria-hidden="true"`).
4. **Sezione "Call Out" / Citazione:**
   - Box a larghezza intera con sfondo leggero per ospitare una testimonianza di un cliente o una certificazione (tag semantico `<blockquote>`).
5. **Blocco Mappa Interattiva (Variante attiva SOLO per `/rete-commerciale`):**
   - Un modulo a tutta larghezza per la visualizzazione geografica degli agenti.
   - *Accessibilità Critica:* La mappa deve essere affiancata da una vista alternativa a lista/tabella accessibile dei contatti per chi naviga da tastiera o screen reader.
6. **CTA Contestuale di Conversione:**
   - Modulo di chiusura specifico (es. "Richiedi una consulenza dedicata per il tuo impianto di [Nome Settore]").

---

### TEMPLATE 3: L'ARCHIVIO GRIGLIA (FILTRI E NAVIGAZIONE)
**Pagine reali mappate su questo layout:** 
- Area Macchine: `/macchine` (Catalogo generale), `/macchine/riempitrici`, `/macchine/sciacquatrici`, `/macchine/usate`, `/squadron/macchine`
- Area Referenze: `/referenze` (Hub dei casi studio)
- Area Blog: `/news` (Hub editoriale)

**Focus CRO:** Ridurre l'attrito nella ricerca. L'utente deve trovare la macchina o il caso studio di suo interesse nel minor numero di click possibile.

**Struttura del Wireframe (Dall'alto in basso):**
1. **Hero di Categoria:**
   - Titolo della categoria (H1) e un breve testo descrittivo (SEO).
2. **Barra dei Filtri / Tab di Navigazione:**
   - *Accessibilità Critica:* I pulsanti a tab per muoversi tra le macro-categorie devono essere implementati come un vero pattern di Tab accessibile (`role="tablist"`, `role="tab"`, `aria-selected="true/false"`). 
   - Se le macchine o le categorie sono poche, i filtri complessi a tendina spariscono e lavorano solo i tab orizzontali.
   - I menu a tendina per filtri secondari devono avere una `<label>` nativa o un attributo `aria-label` chiaro.
3. **Griglia dei Risultati (a 3 o 4 Colonne):**
   - Card standardizzate contenenti: Immagine in alto (con `alt` dinamico), Tag identificativo (es. "Birra", "Usato"), Titolo (H3), Estratto di testo (max 2-3 righe) e Link di atterraggio descrittivo (es. "Vedi scheda [Nome Macchina]" invece di un generico "Leggi di più").
4. **Paginazione:**
   - Pulsante "Mostra altri risultati" o paginazione numerica (usa `<nav aria-label="Paginazione">`).

---

### TEMPLATE 4: LA SCHEDA MACCHINA (DETTAGLIO TECNICO B2B)
**Pagine reali mappate su questo layout:** 
- `/macchine/*/[slug-prodotto]` (Tutte le pagine di dettaglio delle singole macchine prodotte o ricondizionate)

**Focus CRO:** Altissima densità informativa per il buyer tecnico. Chiara separazione tra la macro-conversione (richiesta preventivo) e la micro-conversione (download scheda tecnica in PDF per gli uffici acquisti).

**Struttura del Wireframe (Layout asimmetrico a due colonne - 65/35):**
1. **Intestazione Prodotto:**
   - Breadcrumb accessibile + Titolo del macchinario (H1) + Sottotitolo con macro-categoria tecnica (es. "Riempitrice isobarica automatica").
2. **Area Visuale & Gallery:**
   - Immagine principale ad alta risoluzione con dettagli costruttivi ed elementi di interfaccia in evidenza.
3. **Area Contenuto Principale (Colonna Sinistra - 65%):**
   - **Descrizione Tecnica:** Breve introduzione sul funzionamento e sul target ideale della macchina.
   - **Interfaccia a Tab per Specifiche (Specs):**
     * *Tab 1: Caratteristiche Principali* (Elenchi puntati).
     * *Tab 2: Dati Tecnici* (Tabella semantica delle performance: BPH, consumi, potenze).
     * *Tab 3: Contenitori Gestiti* (Dimensioni e materiali delle bottiglie).
     * *Accessibilità:* Il sistema a tab deve seguire le specifiche ARIA. La tabella tecnica deve usare i tag corretti `<table>`, `<th>` con `scope="col"` o `scope="row"` e un tag `<caption>` descrittivo.
   - **Download Center (Micro-conversione):** Link prominenti per scaricare la scheda tecnica in PDF o il layout CAD (specificando tipo di file e dimensione, es. PDF, 2.4 MB).
4. **Sidebar Commerciale (Colonna Destra - 35% - Sticky durante lo scroll):**
   - **Dati Chiave in Evidenza:** Box ad alto contrasto visivo che riassume i parametri vitali (es. Range BPH: 3.000 - 12.000 / Contenitori: Vetro e Lattine).
   - **Form di Richiesta Rapida / Preventivo (Macro-conversione):** Form compatto composto da 3-4 campi essenziali (Nome, Azienda, Email, Messaggio).
   - **Friction Killer & Umanizzazione:** Testo di rassicurazione ("Risposta entro 24h") + Foto e dettagli del commerciale di riferimento per quel settore/area con lingue parlate.

---

### TEMPLATE 5: IL DETTAGLIO EDITORIALE (STORIES & BLOG)
**Pagine reali mappate su questo layout:** 
- `/referenze/[slug]` (La singola scheda tecnica del caso studio)
- `/news/[slug]` (Il singolo articolo del blog o guida tecnica)

**Focus CRO:** Utilizzare la storia di successo o la guida come acceleratore di fiducia. Il caso studio deve dimostrare numericamente il superamento di una sfida produttiva e rimandare direttamente al catalogo prodotti.

**Struttura del Wireframe (Layout asimmetrico a due colonne - 70/30):**
1. **Intestazione Editoriale:**
   - Titolo principale dell'articolo o del caso studio (H1) + Metadati (Data, Settore, Paese d'installazione per i casi studio) + Grande immagine di copertina a tutta larghezza.
2. **Area Narrativa (Colonna Sinistra - 70%):**
   - Testo ad alta leggibilità strutturato con titoli interni (H2, H3), gallerie fotografiche ed elementi di citazione diretta del cliente (tag `<blockquote>`).
   - Sezione "Il Problema / La Soluzione" strutturata visivamente per evidenziare i dati di performance post-installazione (es. incremento effettivo del BPH).
3. **Sidebar Editoriale (Colonna Destra - 30%):**
   - **La Voce del Cliente:** Foto del cliente, nome, ruolo in azienda e recensione diretta con firma.
   - **Macchine Utilizzate nel Progetto (CRO Cross-Linking):** Card di collegamento rapido che rimandano alla specifica Scheda Macchina (Template 4).
4. **Sezione Correlati (In basso, a tutta larghezza):**
   - "Altri casi studio" o "Articoli consigliati" (3 card riprese dal Template Archivio).

---

### TEMPLATE 6: L'HUB DI CONVERSIONE (I FORM)
**Pagine reali mappate su questo layout:** 
- `/contatti` (Hub principale)
- `/contatti/lavora-con-noi` (Sezione carriere con elenco posizioni aperte nella barra info)
- `/contatti/diventa-partner` (Canale per agenti o distributori)
- `/servizi/ricambi` (Modulo avanzato richiesta componenti)

**Focus CRO:** Riduzione totale degli ostacoli visivi. Il form è lo strumento centrale, ottimizzato per raccogliere lead qualificati e instradarli istantaneamente al reparto corretto.

**Struttura del Wireframe (Layout a due colonne - 40/60):**
1. **Colonna Informazioni (Sinistra - 40%):**
   - Titolo della pagina (H1) + Testo di rassicurazione ("Rispondiamo in lingua... entro 24 ore") + Dati di contatto scritti in formato testuale (Indirizzo, telefoni diretti, email cliccabili) + Loghi di garanzia (es. "Assistenza H24").
2. **Colonna Modulo Dinamico (Destra - 60%):**
   - Modulo pulito con ampi spazi di digitazione e focus visivo definito.
   - *Accessibilità & CRO per i Form:*
     * Ogni campo deve avere una `<label>` visibile associata tramite l'attributo `for=""`.
     * I campi obbligatori devono essere chiaramente contrassegnati (asterisco rosso ad alto contrasto) sia visivamente che a codice (`required`).
     * **Logica condizionale (Fondamentale su `/servizi/ricambi`):** Se l'utente seleziona "Richiesta Ricambi" o accede direttamente a questo URL, il modulo mostra automaticamente i campi "Numero di matricola macchina" e l'upload per i file delle foto. Lo screen reader deve essere notificato dello svelamento di questi campi, posizionandoli subito dopo nel flusso logico del tasto `TAB`.
     * Gli errori di validazione del form devono presentare un testo di errore chiaro associato al campo tramite `aria-describedby`.
     * Pulsante di invio (CTA) ad alto contrasto e accessibile tramite tastiera.