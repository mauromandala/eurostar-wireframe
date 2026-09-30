# Test finali (30/09/2026)

Script Playwright che confrontano il wireframe (`http://localhost:4173`) con lo staging e provano la tastiera. Non installano nulla: usano un `playwright-core` già presente sul Mac e il Chromium headless in cache, passati con due variabili d'ambiente.

```bash
export PW="/percorso/node_modules/playwright-core"      # es. quello del tema marienklinik in ~/Local Sites
export CHROME=~/Library/Caches/ms-playwright/chromium_headless_shell-1223/chrome-headless-shell-mac-arm64/chrome-headless-shell
```

| Script | Cosa fa | Uscita |
|---|---|---|
| `confronto.js [larghezze] [pagine]` | 24 coppie wireframe ↔ staging: posizione di ogni riga di testo nel `main` (Range), abbinamento per testo, "salti" verticali > 1,5px, scarti orizzontali, font/colore diversi, scroll orizzontale | `report-<larghezze>.json` |
| `tastiera.js [larghezze] [pagine]` | 23 pagine: Tab fino in fondo; per ogni elemento focus visibile (outline/ombra), elemento visibile (niente link nascosti raggiungibili), primo Tab = "Vai al contenuto". Scende negli shadow DOM (banner cookie) | `tastiera-<larghezze>.json` |
| `interazioni.js` | 29 prove con i soli tasti: salta al contenuto, mega-menu, ricerca, torna su, filtri catalogo, News "Mostra altri", carosello Home, hamburger a 375, form Contatti (etichette, ordine, anti-spam). Nessun form inviato | console |
| `zoom.js [pagine]` | Zoom 200% (640×450 a densità 2): scroll orizzontale, testo tagliato o sovrapposto, spazio di header e barre fisse, elementi con il focus coperti da header/barre durante il Tab | `zoom-200.json` |
| `sonda-*.js` | Sonde puntuali usate per capire gli scarti (antenati di un testo, campi di un form, sezioni del main, elementi che sforano, posizione delle ancore sotto l'header) | console |

Avvertenze: dopo ogni modifica al CSS del Kit la prima pagina richiesta esce senza stili finché Elementor rigenera il file (due falsi allarmi il 30/09): aprire qualche pagina prima di lanciare i confronti. Il banner cookie va rifiutato e la pagina ricaricata, altrimenti il punto di partenza del Tab resta in fondo alla pagina.

`sorgenti/snip2-tastiera.js`: il blocco aggiunto allo script dell'header (snippet 2) dopo le prove.
