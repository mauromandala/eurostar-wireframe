#!/usr/bin/env python3
"""Genera i JSON Elementor della pagina News (archivio) e dei template "Articolo" e "Caso studio" (IT).
Classi come "@etichetta" (sostituite con gli ID sul server). Uso: gen_news_pagine.py news-page.json articolo.json caso.json"""
import json, sys

T = lambda s: {"$$type": "string", "value": s}
H = lambda s: {"$$type": "escaped-html", "value": s}
NOLINK = {"$$type": "link", "value": []}
DYN_TITLE = {"$$type": "dynamic", "value": {"name": "post-title", "group": "post", "settings": []}}
NAVY = {"$$type": "global-color-variable", "value": "@@navy-800"}  # sostituito sul server con l'ID della variabile navy-800


def cls(*labels, local=None):
    v = ["@" + l for l in labels]
    if local:
        v.append(local)
    return {"$$type": "classes", "value": v}


def size(v, unit="px"):
    return {"$$type": "size", "value": {"size": v, "unit": unit}}


def dims(t, r, b, l):
    return {"$$type": "dimensions", "value": {"block-start": t, "inline-end": r, "block-end": b, "inline-start": l}}


Z0 = size(0)


def local(sid, props):
    return {sid: {"id": sid, "type": "class", "label": "local", "variants": [
        {"meta": {"breakpoint": "desktop", "state": None}, "props": props, "custom_css": None}]}}


def margin(sid, t, b, extra=None):
    p = {"margin": dims(size(t), Z0, size(b), Z0)}
    p.update(extra or {})
    return local(sid, p)


def box(eid, tag, labels, children, el="e-div-block", styles=None, cssid=None):
    s = {"classes": cls(*labels, local=styles and next(iter(styles))), "tag": T(tag), "link": NOLINK}
    if cssid:
        s["_cssid"] = T(cssid)
    e = {"id": eid, "elType": el, "settings": s, "elements": children, "isInner": False}
    if styles:
        e["styles"] = styles
    return e


def w(eid, wtype, settings, styles=None):
    if styles:
        settings["classes"]["value"].append(next(iter(styles)))
    e = {"id": eid, "elType": "widget", "widgetType": wtype, "settings": settings, "elements": []}
    if styles:
        e["styles"] = styles
    return e


def head(eid, tag, labels, text, styles=None):
    return w(eid, "e-heading", {"classes": cls(*labels), "tag": T(tag), "title": text if isinstance(text, dict) else H(text), "link": NOLINK}, styles)


def para(eid, labels, text, styles=None):
    return w(eid, "e-paragraph", {"classes": cls(*labels), "paragraph": H(text), "tag": T("p"), "link": NOLINK}, styles)


def sc(eid, code):
    return {"id": eid, "elType": "widget", "widgetType": "shortcode", "settings": {"shortcode": code}, "elements": []}


# --- Pagina News (archivio) ---
hero = box("nw-hero", "section", ["es-cat-hero-lg"], [
    box("nw-hero-inner", "div", ["es-inner"], [
        sc("nw-breadcrumb", "[es_breadcrumb_archivio]"),
        para("nw-kicker", ["es-kicker-dark"], "Editoriale"),
        head("nw-titolo", "h1", ["es-display-2-white"], "Novità, fiere ed eventi Eurostar", margin("s-nw-titolo", 18, 16)),
        para("nw-intro", ["es-lead-dark"], "Le novità sull'azienda e sulle macchine, le fiere e gli eventi a cui partecipiamo e i casi studio dei nostri clienti."),
    ]),
], el="e-flexbox", styles=local("s-nw-hero", {"min-height": size("42vh", "custom")}))

pagina = box("nw-main", "main", ["es-page"], [
    hero,
    sc("nw-filtri", "[es_news_filtri]"),
    box("nw-elenco", "section", ["es-sec-body"], [
        box("nw-elenco-inner", "div", ["es-inner"], [sc("nw-griglia", "[es_news_griglia]")]),
    ], el="e-flexbox"),
], el="e-flexbox", cssid="content")


def correlati(prefix, titolo, labels_h2, styles_h2):
    return box(prefix + "-rel", "section", ["es-sec-range"], [
        box(prefix + "-rel-inner", "div", ["es-inner"], [
            para(prefix + "-rel-kicker", ["es-kicker"], "Continua a leggere"),
            head(prefix + "-rel-titolo", "h2", labels_h2, titolo, styles_h2),
            sc(prefix + "-rel-card", "[es_articolo_correlati]"),
        ]),
    ], el="e-flexbox", cssid="es-correlati")


# --- Template "Articolo" ---
articolo = box("ar-main", "main", ["es-page"], [
    box("ar-head", "div", ["es-news-head"], [
        box("ar-head-inner", "div", ["es-inner-narrow"], [
            sc("ar-breadcrumb", '[es_breadcrumb_archivio tema="light"]'),
            head("ar-titolo", "h1", ["es-h1-article"], DYN_TITLE),
            sc("ar-meta", "[es_articolo_meta]"),
        ]),
    ]),
    box("ar-img", "div", ["es-img-band"], [sc("ar-img-sc", "[es_articolo_immagine]")]),
    box("ar-body", "section", ["es-sec-body"], [
        box("ar-body-inner", "article", ["es-inner-narrow"], [sc("ar-corpo", "[es_articolo_corpo]")]),
    ], el="e-flexbox"),
    correlati("ar", "Altri articoli", ["es-display-2"], margin("s-ar-rel-titolo", 16, 32)),
], el="e-flexbox", cssid="content")

# --- Template "Caso studio" ---
caso = box("cs-main", "main", ["es-page"], [
    box("cs-head", "div", ["es-case-head"], [
        box("cs-head-inner", "div", ["es-inner-narrow"], [
            sc("cs-breadcrumb", '[es_breadcrumb_archivio tema="light"]'),
            head("cs-titolo", "h1", ["es-h1-case"], DYN_TITLE),
            sc("cs-meta", "[es_caso_meta]"),
        ]),
    ]),
    box("cs-img", "div", ["es-img-band-case"], [sc("cs-img-sc", "[es_articolo_immagine]")]),
    box("cs-body", "section", ["es-sec-flush"], [
        box("cs-grid", "div", ["es-inner", "es-case-grid"], [
            box("cs-testo", "article", ["es-col-plain", "es-case-body"], [sc("cs-corpo", "[es_articolo_corpo]")]),
            box("cs-aside", "aside", ["es-aside"], [sc("cs-aside-sc", "[es_caso_aside]")]),
        ]),
    ], el="e-flexbox"),
    correlati("cs", "Altri casi studio", ["es-display-3-white"], margin("s-cs-rel-titolo", 16, 32, {"color": NAVY})),
], el="e-flexbox", cssid="content")

for data, path in ((pagina, sys.argv[1]), (articolo, sys.argv[2]), (caso, sys.argv[3])):
    json.dump([data], open(path, "w"), ensure_ascii=False)
print("ok")
