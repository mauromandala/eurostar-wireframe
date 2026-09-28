# Checklist setup WPML — Eurostar (IT/EN)

Checklist operativa per quando si passa dal wireframe allo sviluppo reale su WordPress + Elementor Pro + ACF Pro. Riferita alla struttura CPT/tassonomie/campi già mappata in `acf-elementor-mapping.md` — leggere quel file prima di questo. Due lingue confermate: **IT (default) / EN**.

Principio guida: **installare e configurare WPML all'inizio dello sviluppo, non alla fine.** Collegare field group ACF e template Elementor alla traduzione funziona molto peggio se fatto a posteriori su contenuto già popolato — in quel caso WPML spesso richiede di "risincronizzare" ogni post uno per uno.

---

> **Stato 28/09**: sezioni 1-2 fatte (salvo language switcher, che si costruisce con l'header). Setup eseguito via codice con gli stessi endpoint della procedura guidata di WPML, dopo export del DB in `/var/www/vhosts/eurostar.demoengagemint.it/es-backup/db-pre-wpml-setup-2026-09-28.sql` (fuori dalla webroot).

## 1. Prima di installare

- [x] Licenza WPML registrata sul sito. Installati e attivi: **WPML Multilingual CMS 5.0.2**, **WPML String Translation 5.0.2**, **ACF Multilingual 5.0.0**. Translation Management e la traduzione dei menu (ex CMS Nav) sono inclusi nel core da WPML 4.5
- [x] Struttura URL: **directory** — IT alla radice, EN sotto `/en/` (verificato: `/en/` risponde 200)
- [ ] Slug tradotti in EN o identici: la traduzione degli slug è attiva in WPML, la scelta dei singoli slug EN resta al referente SEO

## 2. Installazione e configurazione base

- [x] Procedura guidata completata: lingua originale **italiano**, traduzione **inglese**, modalità **"Traduci alcuni contenuti"** (niente traduzione automatica di tutto), chi traduce **"io stesso"**, editor **Advanced Translation Editor**. Condivisione dati con il supporto WPML non attivata. Contenuti esistenti assegnati all'italiano (35 macchine, 6 categorie, 10 settori, 2 linee, pagine)
- [ ] **WPML Media Translation** (traduzione dei testi alternativi): non installato — da aggiungere prima di tradurre le immagini
- [ ] La procedura ha attivato il selettore lingua di WPML **nel footer**: disattivarlo quando l'header con il selettore vero è pronto
- [x] Aggiungere IT e EN in *WPML → Languages*, IT come lingua di default
- [ ] Language switcher: attivarlo nell'header al posto del toggle statico IT/EN già presente nel wireframe (oggi è solo testo, non funzionale)
- [ ] Language switcher accessibile (WCAG 3.1.2, 4.1.2): ogni voce con nome completo per gli screen reader ("Italiano", "English" — anche se a schermo restano "IT"/"EN"), attributi `lang` e `hreflang` sulla voce ("it"/"en"), `aria-current="true"` sulla lingua attiva, niente bandiere senza testo. Nel wireframe oggi sono link `it`/`en` senza nessuno di questi attributi
- [ ] Verificare compatibilità nella pagina *WPML → Support*: deve risultare "compatibile" sia Elementor Pro sia ACF Pro (icona verde) prima di procedere oltre
- [ ] **Verificare se la compatibilità copre anche i widget atomic di Elementor Pro 4.x** (il nuovo sistema, non solo la struttura classic/legacy): se *WPML → Support* segnala compatibilità solo per classic, o ci sono issue note sugli atomic, decidere subito se costruire i template in classic invece di atomic — prima di iniziare la scheda pilota MEC LD, non dopo aver costruito le 21 schede macchina
  - **Esito sul codice (28/09)**: WPML 5.0.2 supporta esplicitamente gli atomic — `AutoConfig/Processors/AtomicWidgetProcessor.php` registra per la traduzione i controlli `text`/`textarea`/`link` di ogni widget atomic; `DynamicContent/Strings.php` gestisce i dynamic tag v4 (es. campi ACF); `V4/Hooks.php` rimappa sulle traduzioni i link interni e i component v4; `media/modules/AtomicStyles.php` traduce le immagini negli stili. Classi globali e variabili non servono (sono stili).
  - **Lato Elementor**: `e_atomic_elements` = beta, `e_opt_in_v4` = alpha, `e_pro_atomic_form` = dev. Il rischio è la maturità di Elementor v4, non WPML. Il form resta comunque il widget Form classic.
  - **Attenzione**: in Elementor 4.3.2 i testi atomic (titolo, paragrafo, testo pulsante) sono controlli `inline-editing`, che l'auto-config di WPML **non** intercetta. Li copre invece la configurazione Elementor che WPML scarica dal proprio server (opzione `WPML(api-pb-config/elementor)`): `e-heading`, `e-paragraph`, `e-button` (testi + link), `e-image` (alt + link), `e-flexbox`/`e-div-block` (link), `e-form` (nome, oggetto e testo email).
  - **Prova pratica (28/09) — superata**: pagina IT con `e-flexbox` > `e-heading` + `e-paragraph` + `e-button` con link interno a un'altra pagina → WPML registra le 3 stringhe → traduzione EN → la pagina `/en/…` mostra titolo, paragrafo e pulsante in inglese, `<html lang="en-US">`, e il pulsante punta alla **pagina di destinazione EN**. Verificato anche un widget classic (lista con icone) dentro un contenitore atomic: viene mostrato correttamente. Pagine di test nel cestino.
  - **Non ancora provato**: dynamic tag ACF dentro un widget atomic in un template Theme Builder (valori EN letti dal post tradotto) → primo controllo della scheda pilota MEC LD.
  - **Note operative**: (1) salvando via Novamira (non dall'editor Elementor) WPML non registra da solo le stringhe: dopo ogni scrittura richiamare `do_action('wpml_page_builder_register_strings', $post, $package)`; (2) le traduzioni vanno create dal flusso di WPML (Advanced Translation Editor), non inserendo a mano post e stringhe — la creazione manuale della prova ha generato avvisi `gzuncompress` di WPML allo spostamento nel cestino; (3) nelle pagine normali Hello avvolge già il contenuto in `<main id="content">`: il contenitore atomic della pagina non deve usare il tag `main` (sì nei template Theme Builder che sostituiscono il contenuto).
  - **Widget atomic disponibili** (Elementor 4.3.2): titolo, paragrafo, pulsante, immagine, SVG, YouTube, video, divisore, componente, flexbox, div block, elementi form (in sviluppo). **Senza equivalente atomic**: Loop Grid/Carousel, Taxonomy Filter, Nav Menu/Mega Menu, Search, galleria, breadcrumb, lista con icone, Form Pro → restano classic in ogni caso.
  - **Decisione atomic/classic (utente, 28/09): MISTO, atomic dove possibile** — struttura, testi, pulsanti e immagini in widget atomic con classi globali; widget classic di Elementor Pro solo dove non esiste l'equivalente atomic (elenco sopra). Aggiornamenti di Elementor da provare prima sullo staging, con backup.

> **Stato 28/09 — sezioni 3-5 fatte**: `macchina`, `posizione_lavoro` (decisione utente: annunci anche in EN), `categoria_macchina`, `settore`, `linea` impostati **Traducibili** (mostra solo contenuti tradotti). Campi ACF: ACF Multilingual 5.0 ha messo tutti i field group in modalità "Stesso contenuto in ogni lingua, tradotto" con preferenze per tipo di campo, coincidenti con la tabella sotto; eccezioni corrette passando a modalità **Esperto** i gruppi "Macchina — Riempitrici" (`tipologia_valvole` → Copia) e "Posizione di lavoro" (`rif` → Copia). Termini ancora da tradurre (servono i testi EN).

## 3. CPT `macchina` e tassonomie `categoria_macchina` / `linea`

- [ ] *WPML → Settings → Post Types Translation*: impostare CPT `macchina` su **"Translatable"** (non "Translate only using Translation Editor" a meno che si scelga fin da subito ATE — vedi punto 10)
- [ ] Tassonomia `categoria_macchina` su **"Translatable"** nella stessa schermata
- [ ] Tradurre i 6 termini di `categoria_macchina` (Sciacquatrici/Soffiatrici, Riempitrici, Tappatrici, Linee complete, Sistemi movimentazione contenitori, Usate) **prima** di iniziare a tradurre i singoli post macchina — altrimenti ogni post EN nasce senza categoria assegnata e va corretto a mano
- [ ] Tassonomia `linea` (Eurostar / Squadron): **"Translatable"** con termini tradotti a nome identico — sono nomi di brand, ma il termine EN deve esistere perché le query "Le nostre macchine" e i template filtrano per `linea` anche in lingua EN

## 4. Tassonomia `settore` (campi ACF sul termine)

- [ ] Tassonomia `settore` su "Translatable"
- [ ] Tradurre i 10 termini (con i loro campi ACF, vedi punto 5) **prima** delle macchine, per lo stesso motivo di `categoria_macchina`: "Adatta per" e "Le nostre macchine" leggono i settori assegnati al post

## 4b. CPT Posizione di lavoro

- [ ] CPT su "Translatable" — solo se le posizioni aperte vanno pubblicate anche in EN (da decidere col cliente: annunci di lavoro locali possono restare solo IT)

## 5. ACF Pro — modalità di sincronizzazione per campo

Il punto dove i progetti WPML+ACF sbagliano più spesso: ogni campo va impostato esplicitamente su **Translate**, **Copy** o **Copy once**, altrimenti WPML applica un default che quasi sempre è sbagliato. Configurare in *WPML → Settings → Custom Fields Translation*:

Nomi dei campi come in `acf-elementor-mapping.md`.

| Campo ACF | Dove | Modalità | Perché |
|---|---|---|---|
| `intro`, `descrizione` | termine `categoria_macchina` | **Translate** | Testo |
| `intro`, `sfide_1`, `sfide_2` | termine `settore` | **Translate** | Testo |
| `tipologia`, `descrizione`, `contenitori`, `prodotto`, `prodotto_breve`, `tecnologia_riempimento`, `tipologia_chiusura`, `cambio_formato`, `contenitori_ora` | CPT `macchina` | **Translate** | Testo libero (anche `contenitori_ora` contiene parole, es. "Fino a 850") |
| `ordine` | termini `categoria_macchina` e `settore` | **Copy** | Stesso ordine in entrambe le lingue |
| `tipologia_valvole` | CPT `macchina` | **Copy** | Sigle tecniche (S - PS - DPS…), identiche in EN |
| `contenitori_tipi` (checkbox Vetro/PET/HDPE/Lattina) | CPT `macchina` | **Copy** | Il valore salvato è un identificatore fisso: tradurre solo le *label* delle scelte in String Translation (punto 7) |
| `immagine` | termine `settore` | **Copy** | Stessa immagine |
| `layout` (repeater Linee complete) — sub-campo immagine | termine `categoria_macchina` | **Copy** | Stessa immagine |
| `layout` — sub-campi titolo e didascalia | idem | **Translate** | Testo |
| `scheda_tecnica` (PDF) | CPT `macchina` | **Copy**, salvo che Eurostar fornisca PDF separati in EN | Se arrivano PDF tradotti, passare a **Translate** solo per quel campo |
| `galleria` | CPT `macchina` | **Copy** | Stesse foto |
| Reparto, Sede, sezioni della scheda (repeater) | CPT Posizione di lavoro | **Translate** | Testo (solo se il CPT è tradotto, punto 4b) |
| Rif./Job ID, Tipo di contratto (select) | CPT Posizione di lavoro | **Copy** | Identificatore / scelta fissa: label del select in String Translation |

"Adatta per" e "Le nostre macchine" non sono campi ACF ma termini `settore` assegnati al post e query sulle tassonomie: non serve nessun relationship field da rimappare, basta che termini e macchine EN esistano (punti 3-4).

- [ ] Rifare questo giro di configurazione ogni volta che si aggiunge un nuovo field group — non è una configurazione "una tantum", va tenuta aggiornata insieme ad `acf-elementor-mapping.md`

## 6. Elementor Pro

- [ ] Verificare in *WPML → Support* che il modulo Elementor risulti attivo (si attiva da solo quando rileva Elementor, ma va controllato)
- [ ] Ogni pagina/template costruita in Elementor viene duplicata da WPML in una versione EN collegata: tradurre il **testo dentro i widget** (Heading, Text Editor, ecc.) tramite l'editor di traduzione WPML, non ricostruendo il layout da zero
- [ ] Occhio ai **Dynamic Tag** verso campi ACF: se il campo è impostato su "Translate" (punto 5), il Dynamic Tag nella pagina EN pesca automaticamente il valore EN — nessuna azione aggiuntiva nel widget stesso
- [ ] Global Widgets e Elementor Theme Builder templates (header, footer, template CPT `macchina`/`settore`, template archivio): tradurre **una sola volta a livello di template**, non per singola pagina che lo usa — altrimenti si duplica lavoro identico decine di volte

## 7. Stringhe del tema e del design system

> **Lezioni dalla prova EN (28/09)**: (1) String Translation registra le stringhe come **inglesi** se non si indica la lingua (impostazione "lingua delle stringhe" = en, da lasciare così per tema e plugin) → le stringhe di codice custom vanno registrate con sorgente italiana: `do_action('wpml_register_single_string', $contesto, $nome, $valore, false, 'it')`. (2) Le traduzioni vengono lette da un file `.mo` per contesto in `wp-content/languages/wpml/`: tradotte dall'interfaccia di String Translation si rigenera da solo; se inserite via codice va rigenerato (`WPML\ST\MO\File\Manager::add($contesto, 'en_US')`). (3) Le pagine EN si verificano nel browser: le richieste del server verso se stesso ricevono la versione IT.

Il wireframe ha molto testo che non passa da ACF o dal contenuto di Elementor: label di UI, testi statici nell'header/footer, microcopy dei form (es. "Adatta per", "Contenitori/ora", "Richiedi un preventivo", i messaggi di validazione). Questo testo va gestito con **WPML String Translation**, non con la traduzione dei post:

- [ ] Scansionare tema/plugin per stringhe non tradotte: *WPML → Theme and plugins localization → Scan*
- [ ] Tradurre le stringhe trovate in *WPML → String Translation*
- [ ] Attenzione particolare ai form (Contatti, Lavora con noi, richiesta preventivo): label dei campi, messaggi di errore/successo, testo dei bottoni — facile dimenticarli perché non sono "contenuto" in senso stretto

## 8. Menu e URL

- [ ] Tradurre le voci di menu con **WPML CMS Nav** (menu Macchine, Settori, footer)
- [ ] Se si è scelto lo slug tradotto (punto 1): tradurre lo slug di ogni CPT/tassonomia/pagina in *WPML → Translation Management*, non lasciarlo auto-generato — uno slug EN generato automaticamente dal titolo italiano è un problema SEO
- [ ] Verificare gli hreflang: WPML li genera automaticamente, ma va controllato con l'ispettore del browser su almeno una pagina di ogni tipo (macchina, settore, pagina statica) dopo il primo giro di traduzioni

## 9. SEO

- [ ] Il sito usa già Yoast SEO (attivo nel piano di sviluppo) — verificare il modulo **WPML SEO** attivo per sincronizzare automaticamente meta title/description/canonical tra le versioni linguistiche
- [ ] Tradurre meta title e meta description per ogni CPT, non lasciare quelli IT anche sulle pagine EN

## 10. Traduzione automatica (opzionale)

Se si vuole automatizzare parte del lavoro invece di tradurre tutto a mano:

- [ ] Attivare **Advanced Translation Editor (ATE)** in *WPML → Translation Management*
- [ ] Configurare la traduzione automatica (consuma crediti a pagamento, separati dalla licenza WPML base) — utile per un primo giro grezzo su testi lunghi (descrizioni tecniche, sfide del settore), da rivedere comunque a mano prima di pubblicare: sono contenuti tecnici, la macchina non sempre rende bene termini di settore (es. "leggera depressione", "aggraffatura")
- [ ] Non affidare a ATE la traduzione dei nomi macchina (MEC LD, GEMINI/F-IES, ecc.) — sono nomi commerciali, vanno esclusi dalla coda di traduzione automatica o marcati come "non tradurre"

## 11. Test prima del go-live

Accessibilità in EN (vedi sezione Accessibilità di `acf-elementor-mapping.md`):

- [ ] Pagine EN con `<html lang="en-US">` (o `en`), non `it-IT`
- [ ] Testi alternativi delle immagini tradotti (modulo **WPML Media Translation**: l'alt vive nella Libreria media, non nel post)
- [ ] Skip link di Hello: in EN deve leggere "Skip to content" (stringa del tema, segue la lingua attiva — verificare)
- [ ] Messaggi di errore dei form e `aria-label` custom (breadcrumb, selettore lingua, icone social) tradotti in EN — sono stringhe, passano da String Translation; widget Ally solo se riattivato al lancio
- [ ] `<title>` delle pagine EN tradotto (Yoast + WPML SEO)

Generali:

- [ ] Cambiare lingua da ogni tipologia di pagina (home, categoria macchina, scheda macchina, settore, pagina statica) e verificare che il language switcher porti alla pagina EN corrispondente, non alla home EN
- [ ] Verificare che "Le nostre macchine" dei settori e le griglie delle categorie mostrino le macchine EN e non quelle IT quando si è in lingua EN
- [ ] Verificare i form (Contatti, Lavora con noi) in EN: label, validazione, e-mail di conferma
- [ ] Controllare che nessuna pagina EN sia rimasta "non tradotta" e stia silenziosamente mostrando il fallback IT (capita spesso con pagine aggiunte dopo il primo giro di configurazione)
