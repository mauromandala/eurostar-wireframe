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
- [ ] Verificare compatibilità nella pagina *WPML → Support*: deve risultare "compatibile" sia Elementor Pro sia ACF Pro (icona verde) prima di procedere oltre

## 3. CPT `macchina` (tassonomia `Categoria macchina`)

- [ ] *WPML → Settings → Post Types Translation*: impostare CPT `macchina` su **"Translatable"** (non "Translate only using Translation Editor" a meno che si scelga fin da subito ATE — vedi punto 9)
- [ ] Stessa tassonomia `Categoria macchina` su **"Translatable"** nella stessa schermata
- [ ] Tradurre i 5-7 termini della tassonomia (Riempitrici, Tappatrici, Sciacquatrici/Soffiatrici, Sistemi movimentazione contenitori, Linee complete, Usate, Etichettatrici se riattivata) **prima** di iniziare a tradurre i singoli post macchina — altrimenti ogni post EN nasce senza categoria assegnata e va corretto a mano

## 4. CPT `settore`

- [ ] CPT `settore` su "Translatable"
- [ ] Nessuna tassonomia da tradurre qui (i 10 settori sono post singoli, non termini)

## 5. ACF Pro — modalità di sincronizzazione per campo

Il punto dove i progetti WPML+ACF sbagliano più spesso: ogni campo va impostato esplicitamente su **Translate**, **Copy** o **Copy once**, altrimenti WPML applica un default che quasi sempre è sbagliato. Configurare in *WPML → Settings → Custom Fields Translation*:

| Campo ACF | Dove | Modalità | Perché |
|---|---|---|---|
| Intro categoria/settore, "Le sfide del settore", descrizione tecnica macchina, tipo riempimento/chiusura, prodotto, materiale, velocità | term field / CPT `settore` / CPT `macchina` | **Translate** | Testo da tradurre riga per riga |
| Ordine di visualizzazione (numero) | term field / CPT `settore` | **Copy** | Stesso ordine in entrambe le lingue, non ha senso tradurlo |
| Contenitori, "Adatta per" (se ACF Select/Checkbox su valori fissi) | CPT `macchina` | **Copy** se le opzioni restano identificatori interni tradotti a livello di label; **Translate** se il valore salvato è testo libero | Verificare come sono implementati i field choices prima di scegliere — con choices fissi tradurre le *label* in String Translation (punto 7), non il campo |
| Relationship "Le nostre macchine" (settore → macchine correlate) | CPT `settore` | **Copy**, ma vedi nota sotto | I relationship field ACF salvano ID di post — WPML deve rimappare l'ID IT sull'equivalente EN. Serve che ogni macchina collegata abbia già la sua traduzione EN pubblicata, altrimenti la relazione punta al post IT anche nella pagina EN |
| Repeater "Esempi di layout" (Linee complete) — sub-campo immagine | CPT `settore`/term | **Copy** | Stessa immagine in entrambe le lingue |
| Repeater "Esempi di layout" — sub-campo didascalia | idem | **Translate** | Testo |
| Allegati (scheda tecnica PDF, layout CAD) | CPT `macchina` | **Copy**, salvo che Eurostar fornisca PDF separati in EN | Se in futuro arrivano PDF tradotti, passare a **Translate** solo per quel campo |
| Immagini/gallery macchina | CPT `macchina` | **Copy** | Stesse foto |

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

- [ ] Cambiare lingua da ogni tipologia di pagina (home, categoria macchina, scheda macchina, settore, pagina statica) e verificare che il language switcher porti alla pagina EN corrispondente, non alla home EN
- [ ] Verificare che i relationship field (settore → macchine correlate) mostrino i post EN e non quelli IT quando si è in lingua EN
- [ ] Verificare i form (Contatti, Lavora con noi) in EN: label, validazione, e-mail di conferma
- [ ] Controllare che nessuna pagina EN sia rimasta "non tradotta" e stia silenziosamente mostrando il fallback IT (capita spesso con pagine aggiunte dopo il primo giro di configurazione)
