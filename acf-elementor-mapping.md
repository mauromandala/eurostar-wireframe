# Mappatura ACF + Elementor Pro — Scheda macchina (`single-macchina.html`)

Riferimento tecnico per lo sviluppo WordPress dell'archetipo "Scheda macchina". Ogni blocco del wireframe deve avere una controparte 1:1 in un widget nativo di Elementor Pro pilotato da un campo ACF — nessuna soluzione custom dove esiste già un widget nativo, per mantenere coerente la struttura su tutte le istanze del CPT `macchina`.

## Principio guida

Dove il numero di elementi è **fisso e noto** (contenitori, stats, download, tab) si usano campi ACF singoli o checkbox, non repeater — mantiene la struttura predicibile e i template Elementor più semplici da mantenere. Il repeater è riservato ai soli casi in cui il contenuto è realmente variabile per macchina.

## Mappatura

| Blocco wireframe | Campo ACF | Widget Elementor Pro nativo | Note |
|---|---|---|---|
| H1 / tagline | Titolo macchina = post title nativo del CPT `macchina`; tagline = ACF Text | Heading / Text Editor con Dynamic Tag → ACF Field | Il titolo resta il post title, nessun ACF necessario |
| Tag "Adatta per" | Riempitrici: nessun campo ACF, tassonomia **Settore** assegnata al post. Tappatrici: ACF Text/Checkbox a scelte fisse (tipo di tappo — non esiste una mappatura a settore per questa tipologia nell'Excel). Sciacquatrici: campo assente, blocco omesso in pagina | Widget nativo Post Info (tipo "Terms") per le Riempitrici; Icon Box/Text per le Tappatrici | Il significato del campo cambia per tipologia, non è un'unica sorgente dati per tutte le macchine |
| Icone "Contenitori" | ACF Checkbox a scelte fisse (Vetro, Plastica, Lattina…) | Icon Box ripetuti in pagina, ciascuno con Display Conditions legata al valore checkbox | Set chiuso → basta la visibilità dinamica nativa, non serve loop |
| Galleria (main + thumbnail) | ACF Gallery field | Widget nativo Image Gallery, binding diretto al campo ACF Gallery via Dynamic Tag | Nessuno sviluppo custom richiesto |
| Stats strip (2 valori in testata) | 2 ACF Text field | 2x StatBlock/Counter con Dynamic Tag | Ridotto da 4 a 2 valori per allinearsi allo schema Excel; font ridotto sitewide sul componente StatBlock "sm" |
| Tabella "Caratteristiche principali" | ACF Group a schema fisso **per tipologia**: Riempitrici 5 campi (Tipo riempimento, Contenitori, Prodotto, Materiale a contatto, Velocità); Tappatrici 3 campi (Tipologia chiusura, Contenitori, Velocità); Sciacquatrici 2 campi (Contenitori, Bottiglie/ora) | Table widget nativo, o righe ripetute con Dynamic Tag | Sostituisce lo "Stats strip" a 4 campi generici della versione precedente — qui il numero e il nome dei campi sono fissi ma diversi per Categoria macchina, quindi conviene un field group ACF condizionale per tassonomia, non un unico group per tutto il CPT |
| Download center (PDF) | 1-2 ACF File field | Icon Box / Button con Dynamic Tag → URL del file | Formato scheda tecnica corretto da DWG a PDF; box "Dati chiave" e riferimento a persona specifica sono stati rimossi dal modulo preventivo |
| Modulo preventivo | — | Widget nativo Form | Nessun campo ACF "referente commerciale" nominativo: il form non preseleziona più una persona specifica |

## Blocchi rimossi (non più nel wireframe, non serve mapparli)

Il blocco "Come funziona" (griglia a 4 card) sulle pagine categoria, i blocchi "Progettata internamente" e "Focus macchina" sulla scheda macchina, e i tab "Dati tecnici"/"Contenitori" (residuo di design, mai funzionanti) sono stati rimossi perché non previsti dallo schema IMPOSTAZIONE CATALOGO MACCHINE — non richiedono field group ACF.

## Applicabilità

Questa logica di mappatura (fisso → campo singolo/checkbox + widget statico; variabile → repeater + Loop Grid; **condizionale per tipologia** dove lo schema lo richiede, come sulla tabella "Caratteristiche principali") va replicata per gli altri archetipi del prototipo (`single-servizio.html`, `single-news-articolo.html`, `single-news-editoriale.html`, `taxonomy-settore.html`, `taxonomy-macchina-*.html`) mano a mano che si definisce la struttura ACF di ciascuno. Per il CPT Macchina la struttura è già validata su tutte e 3 le tipologie coperte dallo schema (17 istanze reali: MEC LD + 16 nuove schede — Riempitrici, Tappatrici, Sciacquatrici); restano fuori Squadron e Linee complete, non coperte dallo schema Excel.
