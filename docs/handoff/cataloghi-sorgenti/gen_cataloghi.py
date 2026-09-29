#!/usr/bin/env python3
"""Genera il JSON Elementor della pagina Cataloghi (IT). Le classi sono indicate con "@etichetta":
il PHP sul server le sostituisce con gli ID delle classi globali."""
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
    return w(eid, "e-heading", {"classes": cls(*labels), "tag": T(tag), "title": H(text), "link": NOLINK}, local)


def para(eid, labels, text, local=None):
    return w(eid, "e-paragraph", {"classes": cls(*labels), "paragraph": H(text), "tag": T("p"), "link": NOLINK}, local)


def sc(eid, code):
    return w(eid, "shortcode", {"shortcode": code})


Z0 = size(0)
M = lambda sid, t, b: local_style(sid, {"margin": dims(size(t), Z0, size(b), Z0)})
HERO_Y = size("clamp(40px,7vh,72px)", "custom")

hero = box("ct-hero", "section", ["es-cat-hero"], [
    box("ct-hero-inner", "div", ["es-inner"], [
        sc("ct-breadcrumb", "[es_breadcrumb_archivio]"),
        para("ct-kicker", ["es-kicker-dark"], "Documentazione tecnica"),
        head("ct-titolo", "h1", ["es-display-2-white"], "Scarica i cataloghi Eurostar", M("s-ct-titolo", 18, 16)),
        para("ct-intro", ["es-lead-dark"], "Schede di gamma in PDF, aggiornate per categoria di macchina, pronte da consultare o condividere con il tuo team tecnico."),
    ]),
], el="e-flexbox", local=local_style("s-ct-hero", {"min-height": size("32vh", "custom"), "padding": dims(HERO_Y, SEZIONE_X, HERO_Y, SEZIONE_X)}))


def gruppo(eid, titolo, testo, codice):
    return box(eid, "div", ["es-catalog-group"], [
        head(eid + "-h", "h2", ["es-h3"], titolo, M("s-" + eid + "-h", 0, 4)),
        para(eid + "-p", ["es-body-sm"], testo, M("s-" + eid + "-p", 0, 20)),
        sc(eid + "-righe", codice),
    ])


elenco = box("ct-elenco", "section", ["es-sec-body"], [
    box("ct-gruppi", "div", ["es-inner-narrow", "es-catalog-groups"], [
        gruppo("ct-generale", "Catalogo generale", "La panoramica completa della gamma, in un unico documento.", '[es_cataloghi tipo="generale"]'),
        gruppo("ct-categorie", "Cataloghi per categoria", "Un documento dedicato per ogni tipologia di macchina.", "[es_cataloghi]"),
    ]),
], el="e-flexbox", cssid="cataloghi-elenco")

cta = box("ct-cta", "section", ["es-sec-cta-sm"], [
    box("ct-cta-inner", "div", ["es-inner-60ch"], [
        head("ct-cta-titolo", "h2", ["es-display-3-white"], "Non trovi quello che cerchi?"),
        para("ct-cta-testo", ["es-lead-muted-dark"], "Il nostro team tecnico ti invia la documentazione specifica per la tua configurazione.", M("s-ct-cta-testo", 0, 32)),
        box("ct-cta-btns", "div", ["es-btn-row"], [
            w("ct-cta-btn", "e-button", {"classes": cls("es-btn", "es-btn-primary-dark"), "text": H("Contattaci"), "link": page(260, "Contatti"), "tag": T("button")}),
        ]),
    ]),
], el="e-flexbox")

main = box("ct-main", "main", ["es-page"], [hero, elenco, cta], el="e-flexbox", cssid="content")
json.dump([main], open(sys.argv[1], "w"), ensure_ascii=False)
print("ok")
