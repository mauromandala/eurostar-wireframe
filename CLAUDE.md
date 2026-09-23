# Eurostar — Wireframe Hi-Fi

Prototipo statico HTML/CSS del nuovo sito Eurostar (bottling & packaging solutions), destinato a essere ricostruito in **WordPress + Elementor (Pro) + ACF (Pro)**. Non è il sito finale: è un wireframe ad alta fedeltà usato per validare struttura, contenuti e design system prima dello sviluppo.

- Sito pubblicato: https://mauromandala.github.io/eurostar-wireframe/ (GitHub Pages, serve da `main` root)
- Repo: https://github.com/mauromandala/eurostar-wireframe
- Indice di tutte le pagine: [index.html](index.html)

## Regole operative

- **Commit + push automatici**: dopo ogni modifica, fare `git commit` e `git push origin main` senza chiedere conferma all'utente. È una regola permanente per questo repo.
- Un'interruzione di tool call seguita da un messaggio utente è normale digitazione, non un evento speciale da gestire con cautela extra.
- Verificare sempre le modifiche visive nel browser (preview locale su `static` / porta 4173) prima di considerarle concluse, specialmente per hero, spaziature e responsive.
- Attenzione alla cache del browser di anteprima: dopo un edit, ricaricare con `force: true` o un query param per evitare di leggere contenuto stale.

## Struttura pagine

Tutte le pagine condividono header (utility bar + nav + mega-menu Macchine/Settori) e footer identici copia-incollati in ogni file — non c'è un sistema di include, quindi le modifiche a nav/footer vanno propagate manualmente (o con script) su tutti gli `.html`.

**Pagine uniche**: home, Chi siamo, Catalogo macchine, Settori (elenco), Servizi (overview), Referenze, Cataloghi (download), News (elenco), Contatti, Conferma invio form, 404, Squadron.

**Archetipi / template dinamici**:
- Categoria macchina × 7 (Riempitrici, Tappatrici, Etichettatrici, Sciacquatrici/Soffiatrici, Sistemi movimentazione contenitori, Linee complete, Usate)
- Scheda macchina: 21 istanze reali popolate (10 Riempitrici, 6 Tappatrici, 2 Sciacquatrici/Soffiatrici — MEC SI e Twist Rinser —, 3 Sistemi movimentazione contenitori) — dettaglio in [acf-elementor-mapping.md](acf-elementor-mapping.md)
- Scheda settore: tutti e 10 i settori di lancio popolati
- Scheda servizio (oggi: Assistenza tecnica) — Ricambi e Remote view da fare
- Articolo/guida tecnica e Caso studio editoriale (News)

## Design system

Token in `_ds/eurostar-design-system-8ffca847-51ad-4b4a-a5ee-a2536d8a406f/tokens/`:
- `colors.css` — palette **volutamente in scala di grigi** ("structure only, no brand color"): da sostituire con la palette brand reale in sviluppo, mantenendo la struttura semantica (surface/text/action)
- `typography.css` — Barlow (display/titoli, 700–900, maiuscolo) + Roboto (corpo, 300/400)
- `spacing.css` — scala fissa in px + spaziature fluide via `clamp()`, clip-path per i tagli diagonali del brand

Componenti riutilizzabili via `x-import`: `Button`, `StatBlock`, `SectionKicker`, `MachineCard`.

## Documentazione tecnica (handoff sviluppo)

- [handoff-wordpress-elementor.html](handoff-wordpress-elementor.html) — mappa token → Global Settings Elementor, componenti (nativo/Pro/custom), CPT + campi ACF, relazioni dinamiche, template Theme Builder, cosa resta fuori dal wireframe
- [riepilogo-testi.html](riepilogo-testi.html) — stato di inserimento dei testi definitivi dal documento cliente, sezione per sezione (fatto / testo pronto non impaginato / in attesa dal cliente)
- [acf-elementor-mapping.md](acf-elementor-mapping.md) — mappatura ACF/Elementor (file esistente, verificarne l'aggiornamento rispetto a `handoff-wordpress-elementor.html`)

## Decisioni di scope confermate con l'utente

- **A** — Etichettatrici resta rimossa da menu/footer/filtri finché non è chiarito con Eurostar se è una linea prodotto autonoma. Tappatrici invece è stata riattivata ovunque su indicazione esplicita del cliente/PM, con 6 modelli reali popolati.
- **B** — Superata: sia per le Macchine (21 istanze reali: 10 Riempitrici, 6 Tappatrici, 2 Sciacquatrici/Soffiatrici — MEC SI e Twist Rinser —, 3 Sistemi movimentazione contenitori) sia per i Settori (tutti e 10 quelli di lancio) sono stati popolati con contenuto reale, non solo un esempio per tipo. Resta valida solo per Servizio (un solo esempio, Assistenza tecnica, su 3 previsti).
- **C** — Squadron è una pagina/voce di menu a sé, creata da zero e collegata su tutto il sito.
- **D** — Superata (23/09): indirizzo, telefono ed email sono quelli forniti da Serena via mail, dato ufficiale confermato. Corretto un bug per cui in pagina Contatti, accanto al numero WhatsApp, veniva mostrato per errore il numero fisso invece del numero WhatsApp reale.

## Contenuti ancora mancanti dal cliente

Testimonianza cliente, loghi clienti per Referenze (Serena li recupera uno a uno nei prossimi giorni) — i case study completi restano sospesi a tempo indeterminato, resta solo la parete loghi, 3 articoli News, gamma esatta e velocità Squadron oltre EXACTA/ATHENA — sospesa a tempo indeterminato su indicazione del cliente (23/09), si procede al lancio solo con questi due modelli. Le 3 posizioni di esempio in Lavora con noi restano contenuto dimostrativo: confermato che non arriveranno altre posizioni reali, andranno sostituite con quelle davvero aperte al lancio o rimosse. Dettaglio completo in [riepilogo-testi.html](riepilogo-testi.html).
