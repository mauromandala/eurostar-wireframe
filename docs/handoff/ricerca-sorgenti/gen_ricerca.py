#!/usr/bin/env python3
"""Genera il JSON Elementor del template "Risultati di ricerca" (IT). Classi come "@etichetta"
(sostituite con gli ID sul server). Uso: gen_ricerca.py ricerca.json"""
import json, sys

T = lambda s: {"$$type": "string", "value": s}
H = lambda s: {"$$type": "escaped-html", "value": s}
NOLINK = {"$$type": "link", "value": []}
SEZIONE_X = {"$$type": "global-size-variable", "value": "e-gv-806f882"}


def cls(*labels, local=None):
    v = ["@" + l for l in labels]
    if local:
        v.append(local)
    return {"$$type": "classes", "value": v}


def dyn(shortcode):
    return {"$$type": "dynamic", "value": {"name": "shortcode", "group": "site", "settings": {"shortcode": T(shortcode)}}}


def page(pid, label):
    return {"$$type": "link", "value": {"destination": {"$$type": "query", "value": {
        "id": {"$$type": "number", "value": pid}, "label": T(label)}},
        "isTargetBlank": {"$$type": "boolean", "value": False}, "tag": T("a")}}


def size(v, unit="px"):
    return {"$$type": "size", "value": {"size": v, "unit": unit}}


def dims(t, r, b, l):
    return {"$$type": "dimensions", "value": {"block-start": t, "inline-end": r, "block-end": b, "inline-start": l}}


def local_style(sid, props):
    return {sid: {"id": sid, "type": "class", "label": "local", "variants": [
        {"meta": {"breakpoint": "desktop", "state": None}, "props": props, "custom_css": None}]}}


def box(eid, tag, labels, children, el="e-div-block", local=None, cssid=None):
    s = {"classes": cls(*labels, local=local and next(iter(local))), "tag": T(tag), "link": NOLINK}
    if cssid:
        s["_cssid"] = T(cssid)
    e = {"id": eid, "elType": el, "settings": s, "elements": children, "isInner": False}
    if local:
        e["styles"] = local
    return e


def w(eid, wtype, settings, local=None):
    e = {"id": eid, "elType": "widget", "widgetType": wtype, "settings": settings, "elements": []}
    if local:
        settings["classes"]["value"].append(next(iter(local)))
        e["styles"] = local
    return e


def head(eid, tag, labels, text, local=None):
    return w(eid, "e-heading", {"classes": cls(*labels), "tag": T(tag), "title": text if isinstance(text, dict) else H(text), "link": NOLINK}, local)


def para(eid, labels, text, local=None):
    return w(eid, "e-paragraph", {"classes": cls(*labels), "paragraph": text if isinstance(text, dict) else H(text), "tag": T("p"), "link": NOLINK}, local)


def sc(eid, code):
    return w(eid, "shortcode", {"shortcode": code})


Z0 = size(0)
M = lambda sid, t, b: local_style(sid, {"margin": dims(size(t), Z0, size(b), Z0)})
HERO_Y = size("clamp(40px,7vh,72px)", "custom")

# Testata scura come Cataloghi/News: breadcrumb Home / Ricerca, kicker, H1 "Risultati per «…»", numero di risultati, campo per una nuova ricerca
hero = box("sr-hero", "section", ["es-cat-hero"], [
    box("sr-hero-inner", "div", ["es-inner"], [
        sc("sr-breadcrumb", "[es_breadcrumb_archivio]"),
        para("sr-kicker", ["es-kicker-dark"], "Ricerca nel sito"),
        head("sr-titolo", "h1", ["es-display-2-white"], dyn("[es_ricerca_titolo]"), M("s-sr-titolo", 18, 16)),
        para("sr-sommario", ["es-lead-dark"], dyn("[es_ricerca_sommario]")),
        sc("sr-form", "[es_ricerca_form]"),
    ]),
], el="e-flexbox", local=local_style("s-sr-hero", {"min-height": size("32vh", "custom"), "padding": dims(HERO_Y, SEZIONE_X, HERO_Y, SEZIONE_X)}), cssid="es-sr-hero")

# Macchine: stesso Collection Loop e stessa card del catalogo (Query ID es_ricerca_macchine); poi gli altri gruppi o lo stato vuoto
# Il loop viene copiato sul server dal template "Catalogo macchine" (512) cambiando solo il Query ID.
loop = {"__loop_da__": 512, "query_id": "es_ricerca_macchine", "prefisso": "sr"}

risultati = box("sr-risultati", "section", ["es-sec-body"], [
    box("sr-risultati-inner", "div", ["es-inner"], [
        box("sr-macchine", "div", [], [
            sc("sr-macchine-titolo", '[es_ricerca_gruppo tipo="macchine"]'),
            loop,
        ], local=local_style("s-sr-macchine", {"padding": dims(Z0, Z0, Z0, Z0)}), cssid="es-sr-macchine"),
        sc("sr-altri", "[es_ricerca_risultati]"),
    ]),
], el="e-flexbox", cssid="es-sr-risultati")

cta = box("sr-cta", "section", ["es-sec-cta-sm"], [
    box("sr-cta-inner", "div", ["es-inner-60ch"], [
        head("sr-cta-titolo", "h2", ["es-display-3-white"], "Non trovi quello che cerchi?"),
        para("sr-cta-testo", ["es-lead-muted-dark"], "Scrivici: il nostro team tecnico ti aiuta a trovare la soluzione giusta per il tuo prodotto.", M("s-sr-cta-testo", 0, 32)),
        box("sr-cta-btns", "div", ["es-btn-row"], [
            w("sr-cta-btn", "e-button", {"classes": cls("es-btn", "es-btn-primary-dark"), "text": H("Contattaci"), "link": page(260, "Contatti"), "tag": T("button")}),
        ]),
    ]),
], el="e-flexbox")

main = box("sr-main", "main", ["es-page"], [hero, risultati, cta], el="e-flexbox", cssid="content")
json.dump([main], open(sys.argv[1], "w"), ensure_ascii=False)
print("ok")
