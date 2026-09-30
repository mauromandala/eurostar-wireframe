#!/usr/bin/env python3
"""Genera il JSON Elementor della Home (IT). Le classi sono indicate con "@etichetta":
il PHP sul server le sostituisce con gli ID delle classi globali."""
import json, sys

SITE = "https://eurostar.demoengagemint.it"
T = lambda s: {"$$type": "string", "value": s}
H = lambda s: {"$$type": "escaped-html", "value": s}
NOLINK = {"$$type": "link", "value": []}


def cls(*labels, local=None):
    v = ["@" + l for l in labels]
    if local:
        v.append(local)
    return {"$$type": "classes", "value": v}


def url(u):
    return {"$$type": "link", "value": {"destination": {"$$type": "url", "value": SITE + u},
            "isTargetBlank": {"$$type": "boolean", "value": False}, "tag": T("a")}}


def page(pid, label):
    return {"$$type": "link", "value": {"destination": {"$$type": "query", "value": {
        "id": {"$$type": "number", "value": pid}, "label": T(label)}},
        "isTargetBlank": {"$$type": "boolean", "value": False}, "tag": T("a")}}


def box(eid, tag, labels, children, el="e-div-block", local=None, styles=None, cssid=None):
    s = {"classes": cls(*labels, local=local), "tag": T(tag), "link": NOLINK}
    if cssid:
        s["_cssid"] = T(cssid)
    e = {"id": eid, "elType": el, "settings": s, "elements": children, "isInner": False}
    if styles:
        e["styles"] = styles
    return e


def sec(eid, labels, children, local=None, styles=None):
    return box(eid, "section", labels, children, el="e-flexbox", local=local, styles=styles)


def w(eid, wtype, settings, styles=None):
    e = {"id": eid, "elType": "widget", "widgetType": wtype, "settings": settings, "elements": []}
    if styles:
        e["styles"] = styles
    return e


def head(eid, tag, labels, text, local=None, styles=None):
    return w(eid, "e-heading", {"classes": cls(*labels, local=local), "tag": T(tag), "title": H(text), "link": NOLINK}, styles)


def para(eid, tag, labels, text, local=None, styles=None):
    return w(eid, "e-paragraph", {"classes": cls(*labels, local=local), "paragraph": H(text), "tag": T(tag), "link": NOLINK}, styles)


def btn(eid, labels, text, link):
    return w(eid, "e-button", {"classes": cls(*labels), "text": H(text), "link": link, "tag": T("button")})


def sc(eid, code):
    return w(eid, "shortcode", {"shortcode": code})


def size(v, unit="px"):
    return {"$$type": "size", "value": {"size": v, "unit": unit}}


def dims(t, r, b, l):
    return {"$$type": "dimensions", "value": {"block-start": t, "inline-end": r, "block-end": b, "inline-start": l}}


def local_style(sid, variants):
    return {sid: {"id": sid, "type": "class", "label": "local", "variants": [
        {"meta": {"breakpoint": bp, "state": None}, "props": props, "custom_css": None} for bp, props in variants]}}


Z0 = size(0)
PADX = size("clamp(24px,3vw,40px)", "custom")

# --- Testi (IT) ---
H1 = "Dal 1996 progettiamo e realizziamo linee di imbottigliamento precise e affidabili, pensate per garantire continuità, efficienza e qualità in ogni fase del processo."

discover = [
    ("sciacquatura", "Sciacquatura", "Risciacquo e soffiaggio con acqua, aria filtrata o gas inerte, rotativo o lineare.", "/categoria-macchina/sciacquatrici/"),
    ("riempimento", "Riempimento", "Sistemi a gravità, leggero vuoto, leggera pressione, alto vuoto, isobarici, volumetrici, a flussimetri.", "/categoria-macchina/riempitrici/"),
    ("tappatura", "Tappatura", "Gestione delle chiusure in base al contenitore scelto.", "/categoria-macchina/tappatrici/"),
    ("linee", "Linee complete", "Soluzioni ingegnerizzate su misura per prodotto, contenitore e produttività.", "/categoria-macchina/linee-complete/"),
]

stats = [("tl", "1996", "Prodotta la prima macchina"), ("tr", "2.000+", "Macchine installate"),
         ("bl", "100+", "Paesi serviti"), ("br", "100%", "Progettato e costruito internamente")]

featured = [
    ("sciacquatura", "Sciacquatura", "Sciacquatrice MEC SI", "Sciacquatrice / soffiatrice rotativa",
     "Trattamento interno di bottiglie, lattine in alluminio e contenitori in PET/HDPE prima del riempimento, con risciacquo ad acqua o soffiatura ad aria filtrata: il ciclo automatico accompagna il contenitore dalla presa al rilascio.",
     "Schede tecniche", url("/categoria-macchina/sciacquatrici/")),
    ("riempimento", "Riempimento", "SKILLFILL", "Riempitrice isobarica elettropneumatica",
     "Riempitrice isobarica elettropneumatica idonea al riempimento di bottiglie in vetro e/o in PET con prodotti gassati e piatti, mantenendo la stessa pressione tra bottiglia e serbatoio per preservare la gasatura e limitare la formazione di schiuma.",
     "Schede tecniche", page(167, "SKILLFILL")),
    ("squadron", "Squadron", "ATHENA", "Uniblocco isobarico automatico lineare",
     "Uniblocco automatico per sciacquatura, riempimento isobarico e chiusura di bottiglie in vetro e lattine in alluminio, con sciacquatrice lineare a 6 ugelli, riempitrice isobarica elettropneumatica a 6 valvole e chiusura monotesta configurabile.",
     "Scopri Squadron", page(258, "Squadron")),
]

# --- Sezioni ---
hero = sec("hm-hero", ["es-home-hero"], [
    box("hm-hero-inner", "div", ["es-home-hero-inner"], [
        head("hm-h1", "h1", ["es-home-h1"], H1),
        para("hm-hero-lead", "p", ["es-home-hero-lead"], "Nel cuore della Bubble Valley piemontese, realizziamo internamente macchine e linee di imbottigliamento progettate sulle esigenze del tuo prodotto e del tuo processo."),
        box("hm-hero-btns", "div", ["es-home-btns"], [
            btn("hm-hero-btn-1", ["es-btn", "es-btn-primary-dark"], "Vedi le macchine", url("/macchine/")),
            btn("hm-hero-btn-2", ["es-btn", "es-btn-ghost-dark"], "Contattaci", page(260, "Contatti")),
        ]),
    ]),
    # Video di sfondo, velature, lettera "A" e pulsante pausa (proposta grafica, 30/09): snippet 6, [es_home_hero_video]
    sc("hm-hero-video", "[es_home_hero_video]"),
])

engineer = sec("hm-eng", ["es-home-engineer"], [
    box("hm-eng-wrap", "div", ["es-home-eng-wrap"], [
        # Bottiglia della proposta grafica (Libreria media 861, WebP con trasparenza); lente e posizione dallo script della Home
        box("hm-bottle", "div", ["es-home-bottle"], [
            w("hm-bottle-img", "e-image", {"image": {"$$type": "image", "value": {
                "src": {"$$type": "image-src", "value": {"id": {"$$type": "image-attachment-id", "value": 861}, "url": None}},
                "size": T("full")}}}),
        ]),
        head("hm-claim", "h2", ["es-home-claim"], "Progettiamo soluzioni."),
        box("hm-stats", "div", ["es-home-stats"], [
            box(f"hm-stat-{p}", "div", ["es-home-stat", f"es-home-stat-{p}"], [
                para(f"hm-stat-{p}-v", "span", ["es-home-stat-value"], v),
                para(f"hm-stat-{p}-l", "span", ["es-home-stat-label"], l),
            ]) for p, v, l in stats
        ]),
    ]),
    box("hm-discover", "div", ["es-home-discover"], [
        head("hm-discover-title", "h3", ["es-home-discover-title"], "Scopri le macchine per:"),
        box("hm-discover-grid", "div", ["es-home-discover-grid"], [
            box(f"hm-disc-{k}", "div", ["es-home-discover-item"], [
                box(f"hm-disc-{k}-ico", "div", ["es-home-discover-icon", f"es-ico-{k}"], []),
                head(f"hm-disc-{k}-h", "h4", ["es-home-discover-h"], t),
                para(f"hm-disc-{k}-p", "p", ["es-home-discover-text"], d),
                btn(f"hm-disc-{k}-a", ["es-link-arrow"], "Esplora", url(u)),
            ]) for k, t, d, u in discover
        ]),
    ]),
])

trust = sec("hm-trust", ["es-home-trust"], [
    box("hm-trust-inner", "div", ["es-home-trust-inner"], [
        para("hm-trust-label", "span", ["es-home-trust-label"], "Scelti da produttori in oltre 100 paesi"),
        sc("hm-trust-nastro", "[es_clienti_nastro]"),
    ]),
])

bivi = sec("hm-bivi", ["es-sec-range"], [
    box("hm-bivi-inner", "div", ["es-inner"], [
        para("hm-bivi-kicker", "p", ["es-kicker"], "Trova la tua soluzione"),
        box("hm-bivi-grid", "div", ["es-home-bivi-grid"], [
            box("hm-bivio-1", "div", ["es-home-bivio"], [
                para("hm-bivio-1-k", "span", ["es-home-bivio-kicker"], "Percorso 1"),
                head("hm-bivio-1-h", "h3", ["es-home-bivio-title"], "Esplora per tipologia di macchina"),
                para("hm-bivio-1-p", "p", ["es-home-bivio-text"], "Sciacquatrici, riempitrici e tappatrici progettate su misura: scegli la tecnologia adatta al tuo formato e alla tua produttività."),
                btn("hm-bivio-1-a", ["es-link-arrow"], "Vai al catalogo macchine", url("/macchine/")),
            ]),
            box("hm-bivio-2", "div", ["es-home-bivio"], [
                para("hm-bivio-2-k", "span", ["es-home-bivio-kicker"], "Percorso 2"),
                head("hm-bivio-2-h", "h3", ["es-home-bivio-title"], "Scegli in base al tuo settore"),
                para("hm-bivio-2-p", "p", ["es-home-bivio-text"], "Alimentare, chimico e farmaceutico: soluzioni ottimizzate per ogni processo produttivo."),
                btn("hm-bivio-2-a", ["es-link-arrow"], "Vedi soluzioni per settore", page(255, "Settori")),
            ]),
        ]),
    ]),
])

squadron = sec("hm-sq", ["es-home-sq"], [
    box("hm-sq-inner", "div", ["es-home-sq-inner"], [
        box("hm-sq-left", "div", ["es-home-sq-left"], [
            box("hm-sq-logo", "div", ["es-home-sq-logo"], []),
            para("hm-sq-title", "p", ["es-home-sq-title"], "Le nostre soluzioni per piccole e medie produzioni"),
        ]),
        btn("hm-sq-btn", ["es-btn", "es-btn-primary-dark"], "Scopri", page(258, "Squadron")),
    ]),
])


def diag_panel(eid, side, kicker, title, text, cta, link):
    return box(eid, "div", ["es-home-diag-panel", f"es-home-diag-{side}"], [
        para(eid + "-k", "p", ["es-kicker"], kicker),
        head(eid + "-h", "h2", ["es-home-diag-h2"], title),
        para(eid + "-p", "p", ["es-home-diag-text"], text),
        btn(eid + "-a", ["es-btn", "es-btn-primary", "es-home-diag-cta"], cta, link),
    ])


diag = sec("hm-diag", ["es-sec-body"], [
    box("hm-diag-1", "div", ["es-home-diag-wrap"], [
        box("hm-diag-1-grid", "div", ["es-home-diag"], [
            diag_panel("hm-diag-1-panel", "left", "Servizi &amp; post-vendita", "Al tuo fianco anche dopo l'installazione",
                       "Quando una linea si ferma, ogni minuto pesa sulla produzione. Il reparto tecnico Eurostar segue ogni macchina per tutto il suo ciclo di vita: <strong>assistenza da remoto, ricambi originali e interventi in loco</strong>, ovunque nel mondo la tua linea sia installata.",
                       "Scopri il servizio", page(256, "Servizi e post-vendita")),
            box("hm-diag-1-media", "div", ["es-home-diag-media", "es-home-diag-right"], []),
        ]),
    ]),
    box("hm-diag-2", "div", ["es-inner"], [
        box("hm-diag-2-grid", "div", ["es-home-diag"], [
            box("hm-diag-2-media", "div", ["es-home-diag-media", "es-home-diag-left"], []),
            diag_panel("hm-diag-2-panel", "right", "Innovazione &amp; tecnologia", "Ingegneria pensata per non fermarsi mai",
                       "Ogni linea nasce nel nostro ufficio tecnico e viene <strong>costruita e collaudata internamente</strong>, nello stesso stabilimento, dalla progettazione dei gruppi di riempimento fino al collaudo funzionale prima della spedizione.",
                       "Scopri Eurostar", page(254, "Chi siamo")),
        ]),
    ]),
])

quote = sec("hm-quote", ["es-sec-body"], [
    box("hm-quote-inner", "div", ["es-home-quote-inner"], [
        para("hm-quote-k", "span", ["es-home-quote-kicker"], "Soluzioni Eurostar"),
        para("hm-quote-lead", "p", ["es-home-quote-lead"], "Dal 1996 <span>progettiamo e realizziamo</span> linee di imbottigliamento precise e affidabili, pensate per garantire <span>continuità, efficienza e qualità</span> in ogni fase del processo."),
        para("hm-quote-q", "p", ["es-home-quote"], "\"La soluzione esiste sempre, basta trovarla.\""),
        box("hm-quote-author", "div", ["es-home-author"], [
            box("hm-quote-avatar", "div", ["es-home-avatar"], []),
            para("hm-quote-name", "p", ["es-home-author-text"], "<strong>Alessandro Castagno</strong> — Fondatore Eurostar"),
        ]),
        btn("hm-quote-btn", ["es-btn", "es-btn-primary"], "Vedi le macchine", url("/macchine/")),
    ]),
])

col_styles = {
    0: ("s-hm-feat-col-1", [("desktop", {"padding": dims(Z0, PADX, Z0, Z0), "border-width": Z0}), ("mobile_extra", {"padding": Z0})]),
    2: ("s-hm-feat-col-3", [("desktop", {"padding": dims(Z0, Z0, Z0, PADX)}), ("mobile_extra", {"padding": Z0})]),
}
feat_cols = []
for i, (k, label, name, sub, text, cta, link) in enumerate(featured):
    sid, variants = col_styles.get(i, (None, None))
    feat_cols.append(box(f"hm-feat-{k}", "div", ["es-home-feat-col"], [
        para(f"hm-feat-{k}-l", "span", ["es-home-feat-label"], label),
        box(f"hm-feat-{k}-media", "div", ["es-home-feat-media"], []),
        head(f"hm-feat-{k}-h", "h3", ["es-home-feat-h3"], name),
        para(f"hm-feat-{k}-s", "span", ["es-home-feat-sub"], sub),
        para(f"hm-feat-{k}-p", "p", ["es-home-feat-text"], text),
        btn(f"hm-feat-{k}-a", ["es-btn", "es-btn-primary-dark"], cta, link),
    ], local=sid, styles=local_style(sid, variants) if sid else None))

feat = sec("hm-feat", ["es-home-feat"], [
    box("hm-feat-inner", "div", ["es-inner"], [
        para("hm-feat-k", "p", ["es-kicker-dark"], "La nostra gamma"),
        head("hm-feat-h", "h2", ["es-home-feat-h2"], "Macchine in evidenza"),
        para("hm-feat-p", "p", ["es-home-feat-lead"], "Una selezione dalle nostre linee di sciacquatura, riempimento e chiusura, ognuna progettata internamente e calibrata sul prodotto che tratta."),
        box("hm-feat-grid", "div", ["es-home-feat-grid"], feat_cols),
    ]),
])

news = sec("hm-news", ["es-home-news"], [
    box("hm-news-inner", "div", ["es-inner"], [
        box("hm-news-head", "div", ["es-home-news-head"], [
            box("hm-news-titles", "div", ["es-home-news-titles"], [
                para("hm-news-k", "p", ["es-kicker"], "News"),
                head("hm-news-h", "h2", ["es-h2"], "Ultime dal blog Eurostar", local="s-hm-news-h",
                     styles=local_style("s-hm-news-h", [("desktop", {"margin": dims(size(16), Z0, Z0, Z0)})])),
            ]),
            box("hm-news-actions", "div", ["es-home-news-actions"], [
                btn("hm-news-all", ["es-link-arrow"], "Vedi tutte le news", page(259, "News")),
                sc("hm-news-nav", "[es_news_nav]"),
            ]),
        ]),
        sc("hm-news-track", "[es_news_home]"),
    ]),
])

cta = sec("hm-cta", ["es-home-cta"], [
    box("hm-cta-inner", "div", ["es-inner-narrow"], [
        head("hm-cta-h", "h2", ["es-home-cta-h2"], "Progettiamo insieme la tua prossima linea"),
        box("hm-cta-btns", "div", ["es-btn-row"], [
            btn("hm-cta-btn", ["es-btn", "es-btn-primary-dark"], "Contattaci", page(260, "Contatti")),
        ]),
    ]),
])

main = box("hm-main", "main", ["es-page"], [hero, engineer, trust, bivi, squadron, diag, quote, feat, news, cta],
           el="e-flexbox", cssid="content")
json.dump([main], open(sys.argv[1], "w"), ensure_ascii=False)
print("ok")
