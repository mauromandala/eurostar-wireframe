---
type: sintesi-modifiche
date: 2026-09-16
fonte: "Modifiche sito post check cliente - 02.docx" (Serena, cartella Drive Paolo Scagliola/Eurostar/Serena/Modifiche 15-09/)
status: da validare prima di implementare
---

# Sintesi modifiche — "Modifiche sito post check cliente - 02.docx"

Legenda:
- ✅ **Già fatto** — non serve intervenire
- 🟢 **Pronto da implementare** — chiaro, nessun blocco, procedo appena confermi
- 🟡 **Bloccato da materiali/risposte mancanti** — non implementabile ora
- 🔴 **Contraddice una decisione già presa o è ambiguo** — da chiarire prima di toccare il sito

---

## Homepage

| Punto | Stato |
|---|---|
| Numero Paesi → "più di 60" o "60+" (non "circa") | ✅ Già così: `home.html` mostra già "60+ Paesi serviti" |
| Quarta statistica → "100% progettato e costruito internamente" | ✅ Già così: sostituita "8.000 interventi gestiti" con questo valore |
| Sezione "Macchine in evidenza": 1 riempitrice (CANFILL) + la sciacquatrice (MEC SI) + 1 Squadron | ✅ Già fatto — Can Fill, Sciacquatrice MEC SI ed Exacta EP ISO sono già le 3 card in evidenza |

## Azienda / Chi siamo — "I volti dietro le linee"

✅ **Già fatto per intero**: ho verificato `page-chi-siamo.html` e tutte le 8 persone (Alessandro Castagno, Elisabetta Dridini, Stefano Castagno, Giorgia Castagno, Maria Cristina Masoero, Giorgio Frova, Erika Ivaldi, Antonio Landolfi) sono già presenti con ruolo, lingue e bio identici a quelli del documento, nell'ordine richiesto.

🟡 **Bloccato**: lingue di Elisabetta Dridini ancora segnate "da confermare" (stesso punto che il documento segnala in rosso); foto del team allargato commerciali e dell'officina non fornite.

## Servizi

Buone notizie: gran parte di quello che negli handoff precedenti era segnato "da fare" (Ricambi, Remote view, tempistiche di processo) **ha già una bozza nel wireframe** — il documento in molti punti risponde esattamente alle domande che avevamo lasciato aperte in un box "Da confermare con Eurostar" già presente in `archive-servizi.html`.

| Punto | Stato |
|---|---|
| CTA rapida in cima alla pagina per chi è già cliente ("Vai all'assistenza") | 🟢 Pronto — il testo del documento stesso risolve l'ambiguità iniziale di Serena: il box in home rimanda alla sezione assistenza, la voce di menu/footer resta all'inizio della pagina servizi |
| Percorso "Come lavoriamo insieme": da 7 a 8 tappe, con step riorganizzati (nuovo "03 — Offerta e conferma d'ordine", sopralluogo assorbito in "02 — Analisi dell'impianto") | 🟢 Pronto — risposta diretta alle 3 domande nel box "Da confermare con Eurostar" già presente (sopralluogo in presenza/remoto, chi segue l'installazione nei mercati lontani, tempistiche indicative). Sostituisco l'intera sezione + rimuovo il box di domande, ora superato |
| Numeri strip "assistenza post-vendita": 2.000+ macchine, 60 Paesi, tracciata dal **1996**, ricambi in 48h | 🔴 **Da chiarire**: oggi il sito dice "Dal 2016" per il database matricole; il documento dice "dal 1996" (anno di fondazione). È un cambio di fatto, non solo di testo — confermi che il tracciamento risale davvero al 1996 e non al 2016? |
| Schede servizio: Assistenza tecnica, Ricambi originali, Remote view | 🟢 Pronto — testi già presenti ma con parole diverse, aggiorno alla formulazione esatta del cliente |
| Nuova scheda "Manutenzione programmata" | 🟢 Pronto — contenuto completo fornito, non esiste ancora sul sito |
| Nuovo blocco "Assistenza nella tua lingua" (IT/EN/FR/ES/PT) | 🟢 Pronto — contenuto completo fornito |
| Nuovo paragrafo "Garanzia" | 🟢 Pronto — contenuto completo fornito |
| Mappa interventi nel mondo (planisfero) e "Volti del servizio" | 🟡 Bloccato — materiali non forniti (già segnalato dal cliente stesso come "non ancora nel wireframe") |

## Lavora con noi

| Punto | Stato |
|---|---|
| 2 bullet aggiuntivi in "Cosa offriamo" per Area Manager (formazione in officina, trasferte internazionali) | 🟢 Pronto |
| Layout "stile Ferrero": posizioni aperte in riquadri cliccabili con pagina di dettaglio | 🟡 **Non è una modifica di testo**: è un ridisegno della sezione "Posizioni aperte" (oggi probabilmente un elenco semplice). Fattibile, ma è un intervento di struttura/layout più corposo — lo tratto come attività a parte, non come fix rapido insieme al resto |

## Contatti

| Punto | Stato |
|---|---|
| Nel form, campo "Indirizzo" → campo "Paese" | 🟢 Pronto |
| Pulsante WhatsApp sempre visibile in basso | ✅ Già presente (pulsante flottante su tutte le pagine; resta aperto solo il numero reale, già in elenco punti mancanti) |
| Riferimento WhatsApp accanto a telefono/email nella colonna contatti in alto a sinistra | ✅ Già presente su `page-contatti.html` |
| Domanda del cliente su dove arrivano le richieste dai form e cosa vede l'utente dopo l'invio | 🟡 **Non riguarda il wireframe**: è una domanda operativa sulla configurazione del form reale (routing email, pagina di conferma) da rispondere quando si imposta il form vero in WordPress, non un contenuto da cambiare nel prototipo statico |

## Settori

🔴 **Contraddice una decisione già presa — da chiarire prima di qualsiasi modifica.**

Il documento elenca 9 settori con **Vino & Liquori uniti in una sola voce** e "Farmaceutico e sanitario" al posto di "Chimico, farmaceutico e sanitario":

> Acqua, Bevande e succhi, Birra, Alimenti e condimenti, Olio alimentare, **Vino & Liquori**, Cosmetica e cura della persona, Detergenza e cura della casa, **Farmaceutico e sanitario**

Ma nella sessione di ieri Serena stessa aveva confermato — tramite l'Excel `SETTORI MACCHINE 14-09-26.xlsx`, usato come fonte per popolare tutto il sito — **10 settori con Vino e Liquori distinti**, chiudendo esplicitamente un conflitto aperto dalla settimana precedente. Ho già:
- creato le 10 pagine settore (inclusa `taxonomy-settore-liquori.html` separata da Vino)
- propagato i link su menu/footer/catalogo di tutte le 49 pagine del sito
- consegnato al project manager l'Excel di sintesi basato su questi 10 settori

Se questo documento è un ripensamento più recente del cliente, serve una conferma esplicita prima di disfare quel lavoro (tornare a 9 settori con Vino e Liquori uniti significherebbe riscrivere contenuti, unire due pagine, e correggere di nuovo l'Excel già condiviso col PM). **Non procedo su questo punto finché non mi dici come va interpretato.**

## Squadron

| Punto | Stato |
|---|---|
| Sezione "Perché Squadron" (Pensata per chi cresce in piccolo + La gamma + Quando passare a Eurostar) | 🟢 Pronto — contenuto completo fornito, sezione non ancora presente su `page-squadron.html` |
| Rimuovere dicitura "Form unico Eurostar" dal CTA, lasciare solo "Richiedi un preventivo" | 🟢 Pronto |

## Catalogo macchine — testi generali

| Punto | Stato |
|---|---|
| Testo introduttivo archivio macchine | 🟢 Pronto — nuovo testo fornito |
| "LA GAMMA {categoria}" → "LA NOSTRA GAMMA" (Riempitrici, Tappatrici, Sciacquatrici) | 🟢 Pronto — rinomina semplice su 3 pagine categoria |
| Intro categoria Riempitrici (piccola correzione: toglie "a flowmetro") | 🟢 Pronto |
| Rimuovere paragrafo elenco modelli sotto "La nostra gamma" (Riempitrici e Tappatrici) | 🟢 Pronto |
| Elenco macchine da mostrare in Riempitrici: include le 6 varianti MEC ISO come card separate (S/PS/SL/PSL/FS/DPS) | 🔴 **Legato al punto aperto già noto**: oggi esiste una sola card/pagina "MEC ISO". Se il cliente vuole 6 card distinte in catalogo, probabilmente vuole anche 6 pagine scheda separate — stesso punto aperto già segnalato nell'Excel consegnato al PM (nota "Note aperte per il PM", punto 1) |

## Scheda macchina — cambio di struttura importante (Riempitrici)

🔴 **Cambio di struttura, non solo di testo — impatta la documentazione ACF appena consegnata al PM.**

Sull'esempio MEC LD, il documento chiede:

1. **"Adatta per"** passa da tag-settore (Vino, Bevande e succhi, Acqua) a un singolo valore **tipo di prodotto** ("Liquidi piatti"). Questo cambia il significato del campo da "relazione a Settore" a "categoria prodotto" — è l'opposto di quanto documentato ieri in `acf-elementor-mapping.md` e nell'Excel per il PM.
2. **Velocità** passa da un valore specifico per macchina ("Fino a 10.000 bph" per MEC LD) a un **range unico di gamma** ("Da 1.000 a 25.000 contenitori/ora") — lo stesso range per (probabilmente) tutte le Riempitrici, non più un dato specifico per modello.
3. La tabella "Caratteristiche" cambia da 5 campi a 4, con nomi diversi: **Tecnologia di riempimento** (non più "Tipo riempimento"), Contenitori, **Prodotto da riempire** (non più "Prodotto"), **Campo di produzione della gamma** (non più "Velocità"). **Sparisce "Materiale a contatto prodotto"** — che è proprio il campo che avevo segnalato come ambiguo/duplicato nell'Excel per il PM: questa modifica lo risolve eliminandolo.
4. Header "Contenitori" aggiunge "Alluminio" all'elenco.
5. Nuova descrizione tecnica per MEC LD, più tecnica/precisa.

Se confermi questo schema, va applicato in modo coerente su **tutte e 10 le Riempitrici**, non solo su MEC LD, e va rifatta la sezione 3 di `acf-elementor-mapping.md` e la sezione "Materiale a contatto prodotto" nell'Excel appena mandato al PM (che a questo punto sparirebbe, risolvendo la nota che avevo lasciato lì). **Prima di procedere mi serve sapere**: il range di velocità "Da 1.000 a 25.000" è uguale per tutte le Riempitrici o restano eccezioni (es. CANFILL oggi è "Fino a 6.000 BPH", più basso della media)?

## Tappatrici — modifiche minori

| Punto | Stato |
|---|---|
| Elenco delle 6 tappatrici da mostrare in catalogo | ✅ Già fatto — coincide esattamente con le 6 schede già create |
| "ADATTA PER (TAPPI)" → aggiungere "Tappi in" davanti al valore | 🟢 Pronto |
| Unità di misura "BPH" → "contenitori/ora" (mantenendo "Fino a X", non un range) | 🟢 Pronto |
| Rimuovere "Tipologia chiusura" dalla tabella Caratteristiche | 🔴 **In conflitto con una modifica di ieri**: ieri, su tua richiesta, ho spostato "Tipologia chiusura" **nell'header** (StatBlock) proprio per eliminare la duplicazione con "Contenitori". Il documento ora chiede di toglierla anche dalla tabella sotto — se la togliamo da entrambi i posti, il dato sparisce del tutto dalla pagina. Probabilmente l'intento è "non ripeterla due volte", non "eliminarla": da confermare se resta solo in header (soluzione di ieri) o solo in tabella (come sui documenti Excel originali) |

## Note che non riguardano contenuti del wireframe

- Il commento finale del documento (su dove far atterrare il box home vs la voce di menu) è già integrato sopra, in "Servizi".
- La domanda sui form/moduli (Contatti) è operativa, non di contenuto — da rispondere in fase di setup WordPress reale.

---

## Riepilogo numerico

- ✅ **7 punti già fatti** (homepage stats e macchine in evidenza, intera sezione team, WhatsApp, elenco tappatrici) — più di quanto sembrasse a una prima lettura del documento
- 🟢 **~15 modifiche pronte da implementare subito**, senza bisogno di ulteriori chiarimenti
- 🟡 **5 punti bloccati** da materiali o risposte del cliente non ancora arrivate (incluso il ridisegno "stile Ferrero" di Lavora con noi, che tratto come attività a parte)
- 🔴 **5 punti da chiarire con te/Serena prima di toccare il sito**, perché contraddicono decisioni già prese o cambiano il significato di un campo dati già documentato e condiviso col PM

Prossimo passo consigliato: risolviamo prima i 5 punti rossi (specialmente Settori, che è il più costoso da disfare se sbagliato), poi procedo con tutto il verde in un solo giro di modifiche, verificando ogni pagina nel browser prima di committare.
