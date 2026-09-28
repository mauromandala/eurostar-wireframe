# Checklist setup WPML — Eurostar (IT/EN)

Checklist operativa per quando si passa dal wireframe allo sviluppo reale su WordPress + Elementor Pro + ACF Pro. Riferita alla struttura CPT/tassonomie/campi già mappata in `acf-elementor-mapping.md` — leggere quel file prima di questo. Due lingue confermate: **IT (default) / EN**.

Principio guida: **installare e configurare WPML all'inizio dello sviluppo, non alla fine.** Collegare field group ACF e template Elementor alla traduzione funziona molto peggio se fatto a posteriori su contenuto già popolato — in quel caso WPML spesso richiede di "risincronizzare" ogni post uno per uno.

---

## 1. Prima di installare

- [ ] Confermare con Novamira/hosting che la licenza WPML è attiva (Multilingual CMS, non solo Blog — serve CMS per i CPT) e i moduli **String Translation**, **Translation Management**, **ACF Multilingual** sono inclusi nel pacchetto acquistato
- [ ] Decidere la struttura URL: `/en/...` (sottocartella, consigliato — non richiede un secondo dominio/sottodominio) vs parametro `?lang=en` (sconsigliato, peggiore per SEO)
- [ ] Decidere se EN userà uno slug tradotto per ogni pagina (es. `/en/machines/` invece di `/en/macchine/`) o lo stesso slug IT — impatta il lavoro di traduzione slug-by-slug più avanti

## 2. Installazione e configurazione base

- [ ] Installare WPML Multilingual CMS + moduli: **WPML String Translation**, **WPML Translation Management**, **WPML CMS Nav** (per tradurre i menu), **ACF Multilingual** (modulo dedicato, separato dal core)
- [ ] Aggiungere IT e EN in *WPML → Languages*, IT come lingua di default
- [ ] Language switcher: attivarlo nell'header al posto del toggle statico IT/EN già presente nel wireframe (oggi è solo testo, non funzionale)
- [ ] Language switcher accessibile (WCAG 3.1.2, 4.1.2): ogni voce con nome completo per gli screen reader ("Italiano", "English" — anche se a schermo restano "IT"/"EN"), attributi `lang` e `hreflang` sulla voce ("it"/"en"), `aria-current="true"` sulla lingua attiva, niente bandiere senza testo. Nel wireframe oggi sono link `it`/`en` senza nessuno di questi attributi
- [ ] Verificare compatibilità nella pagina *WPML → Support*: deve risultare "compatibile" sia Elementor Pro sia ACF Pro (icona verde) prima di procedere oltre
- [ ] **Verificare se la compatibilità copre anche i widget atomic di Elementor Pro 4.x** (il nuovo sistema, non solo la struttura classic/legacy): se *WPML → Support* segnala compatibilità solo per classic, o ci sono issue note sugli atomic, decidere subito se costruire i template in classic invece di atomic — prima di iniziare la scheda pilota MEC LD, non dopo aver costruito le 21 schede macchina

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
