# Mappatura ACF + Elementor Pro — Scheda macchina (`single-macchina.html`)

Riferimento tecnico per lo sviluppo WordPress dell'archetipo "Scheda macchina". Ogni blocco del wireframe deve avere una controparte 1:1 in un widget nativo di Elementor Pro pilotato da un campo ACF — nessuna soluzione custom dove esiste già un widget nativo, per mantenere coerente la struttura su tutte le istanze del CPT `macchina`.

## Principio guida

Dove il numero di elementi è **fisso e noto** (contenitori, stats, download, tab) si usano campi ACF singoli o checkbox, non repeater — mantiene la struttura predicibile e i template Elementor più semplici da mantenere. Il repeater è riservato ai soli casi in cui il contenuto è realmente variabile per macchina.

## Mappatura

| Blocco wireframe | Campo ACF | Widget Elementor Pro nativo | Note |
|---|---|---|---|
| H1 / tagline | Titolo macchina = post title nativo del CPT `macchina`; tagline = ACF Text | Heading / Text Editor con Dynamic Tag → ACF Field | Il titolo resta il post title, nessun ACF necessario |
| Tag "Adatta per" (settori) | Nessun campo ACF — tassonomia **Settore** assegnata al post | Widget nativo Post Info (tipo "Terms") o Loop Grid sulla tassonomia | Stesso dato usato in `taxonomy-settore.html`, due rappresentazioni |
| Icone "Contenitori" | ACF Checkbox a scelte fisse (Vetro, Plastica, Lattina…) | Icon Box ripetuti in pagina, ciascuno con Display Conditions legata al valore checkbox | Set chiuso → basta la visibilità dinamica nativa, non serve loop |
| Galleria (main + thumbnail) | ACF Gallery field | Widget nativo Image Gallery, binding diretto al campo ACF Gallery via Dynamic Tag | Nessuno sviluppo custom richiesto |
| Stats strip (4 valori) | 4 ACF Text field (o Group "Specifiche chiave") | 4x Icon Box / Counter con Dynamic Tag | Numero fisso e noto → campi singoli, non repeater |
| Box "In dettaglio" (focus) | ACF Repeater (sotto-campi: immagine, titolo, testo) | Loop Grid (Elementor Pro supporta i repeater ACF come sorgente dati per il Loop Builder) | Unico blocco realmente variabile in numero → repeater giustificato |
| Tabs (Caratteristiche / Dati tecnici / Contenitori) | ACF Text/Repeater per tab | Widget nativo Tabs | Contenuto dei singoli tab via Dynamic Tag |
| Download center (PDF/DWG) | 2 ACF File field | Icon Box / Button con Dynamic Tag → URL del file | |
| Sidebar "Dati chiave" + form richiesta | ACF Text field | Icon Box + widget nativo Form | Form gestibile nativamente, nessun dev custom |

## Applicabilità

Questa logica di mappatura (fisso → campo singolo/checkbox + widget statico; variabile → repeater + Loop Grid) va replicata per gli altri archetipi del prototipo (`single-servizio.html`, `single-news-articolo.html`, `single-news-editoriale.html`, `taxonomy-settore.html`, `taxonomy-macchina-*.html`) mano a mano che si definisce la struttura ACF di ciascuno.
