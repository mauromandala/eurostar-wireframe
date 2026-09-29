#!/usr/bin/env python3
"""Genera i JSON Elementor di "Lavora con noi" (pagina elenco) e del template "Scheda posizione" (IT).
Le classi sono indicate con "@etichetta": il PHP sul server le sostituisce con gli ID delle classi globali.
Uso: gen_lavora.py lavora-it.json posizione-it.json"""
import json, sys

T = lambda s: {"$$type": "string", "value": s}
H = lambda s: {"$$type": "escaped-html", "value": s}
NOLINK = {"$$type": "link", "value": []}
PRIVACY = 'Ho letto e accetto la <a href="https://eurostar.demoengagemint.it/privacy-policy/">Privacy Policy</a>'
CONFERMA = "https://eurostar.demoengagemint.it/conferma/"


def cls(*labels, local=None):
    v = ["@" + l for l in labels]
    if local:
        v.append(local)
    return {"$$type": "classes", "value": v}


def dyn(name, group, settings=None):
    return {"$$type": "dynamic", "value": {"name": name, "group": group, "settings": settings or []}}


def page(pid, label):
    return {"$$type": "link", "value": {"destination": {"$$type": "query", "value": {
        "id": {"$$type": "number", "value": pid}, "label": T(label)}},
        "isTargetBlank": {"$$type": "boolean", "value": False}, "tag": T("a")}}


def size(v, unit="px"):
    return {"$$type": "size", "value": {"size": v, "unit": unit}}


def dims(t, r, b, l):
    return {"$$type": "dimensions", "value": {"block-start": t, "inline-end": r, "block-end": b, "inline-start": l}}


Z0 = size(0)


def local(sid, props):
    return {sid: {"id": sid, "type": "class", "label": "local", "variants": [
        {"meta": {"breakpoint": "desktop", "state": None}, "props": props, "custom_css": None}]}}


def margin(sid, t, b, extra=None):
    props = {"margin": dims(size(t), Z0, size(b), Z0)}
    props.update(extra or {})
    return local(sid, props)


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
    return w(eid, "e-paragraph", {"classes": cls(*labels), "paragraph": text if isinstance(text, dict) else H(text), "tag": T("p"), "link": NOLINK}, styles)


def sc(eid, code):
    return {"id": eid, "elType": "widget", "widgetType": "shortcode", "settings": {"shortcode": code}, "elements": []}


def field(fid, custom_id, ftype, label, width="100", required=True, **extra):
    f = {"_id": fid, "custom_id": custom_id, "field_type": ftype, "field_label": label, "width": width, "width_mobile": "100"}
    if required:
        f["required"] = "true"
    f.update(extra)
    return f


def form(eid, name, form_id, fields, subject):
    return {"id": eid, "elType": "widget", "widgetType": "form", "elements": [], "settings": {
        "form_name": name, "form_id": form_id, "_css_classes": "es-contact-form es-job-form",
        "form_fields": fields,
        "show_labels": "true", "mark_required": "yes",
        "column_gap": {"unit": "px", "size": 16}, "row_gap": {"unit": "px", "size": 20},
        "button_text": "Invia candidatura", "button_size": "lg", "button_align": "start", "button_width": "",
        "submit_actions": ["email", "save-to-database", "redirect"],
        "email_to": "mauromandala@gmail.com", "email_subject": subject, "email_content": "[all-fields]",
        "email_from": "email@eurostar.demoengagemint.it", "email_from_name": "Sito Eurostar",
        # email_reply_to = "email" si aggiunge dopo la scrittura (le opzioni del campo sono generate a runtime e il validatore lo rifiuta)
        "redirect_to": CONFERMA,
        "custom_messages": "yes",
        "success_message": "Candidatura inviata. Grazie per il tuo interesse.",
        "error_message": "Non è stato possibile inviare la candidatura. Riprova o scrivici a eurostarinfo@eurostar.it.",
        "invalid_message": "Controlla i campi evidenziati e riprova.",
        "required_field_message": "Questo campo è obbligatorio.",
    }}


def campi_comuni(prefix):
    return [
        field(prefix + "1", "nome", "text", "Nome", "50"),
        field(prefix + "2", "cognome", "text", "Cognome", "50"),
        field(prefix + "3", "email", "email", "Email"),
    ]


def campi_finali(prefix):
    return [
        field(prefix + "5", "cv", "upload", "Curriculum vitae (PDF)", file_sizes="5", file_types="pdf", attachment_type="attach"),
        field(prefix + "6", "messaggio", "textarea", "Messaggio", required=False, rows=4),
        field(prefix + "7", "privacy", "acceptance", "Consenso privacy", acceptance_text=PRIVACY),
        {"_id": prefix + "8", "custom_id": "es_hp", "field_type": "honeypot", "field_label": "", "width": "100"},
    ]


# --- Pagina "Lavora con noi" ---
hero = box("lv-hero", "section", ["es-cat-hero"], [
    box("lv-hero-inner", "div", ["es-inner"], [
        sc("lv-breadcrumb", '[es_breadcrumb_archivio genitore="254" genitore_nome="Azienda"]'),
        para("lv-kicker", ["es-kicker-dark"], "Carriere"),
        head("lv-titolo", "h1", ["es-display-2-white"], "Lavora con noi", margin("s-lv-titolo", 18, 16, {"max-width": size("16ch", "custom")})),
    ]),
], el="e-flexbox", styles=local("s-lv-hero", {"min-height": size("38vh", "custom")}))

intro = box("lv-intro", "section", ["es-sec-block"], [
    box("lv-intro-inner", "div", ["es-inner-narrow"], [
        para("lv-intro-1", ["es-body-lg"], "Da oltre 30 anni progettiamo e realizziamo soluzioni innovative per il settore del bottling e del packaging, portando l'eccellenza del Made in Italy sui mercati internazionali.", margin("s-lv-intro-1", 0, 20)),
        para("lv-intro-2", ["es-body-lg"], "Le persone sono il motore della nostra crescita: per questo siamo alla ricerca di professionisti competenti, appassionati e pronti a mettersi in gioco in un contesto dinamico e orientato all'innovazione.", margin("s-lv-intro-2", 0, 20)),
        para("lv-intro-3", ["es-body-lg"], "Scopri le opportunità aperte o inviaci la tua candidatura spontanea. Siamo sempre interessati a incontrare nuovi talenti che desiderano costruire il futuro insieme a noi."),
    ]),
], el="e-flexbox")

posizioni = box("lv-posizioni", "section", ["es-sec-range"], [
    box("lv-posizioni-inner", "div", ["es-inner"], [
        para("lv-pos-kicker", ["es-kicker"], "Opportunità"),
        head("lv-pos-titolo", "h2", ["es-display-1"], "Posizioni aperte", margin("s-lv-pos-titolo", 16, 40)),
        sc("lv-pos-card", "[es_posizioni]"),
    ]),
], el="e-flexbox", cssid="posizioni-aperte")

spontanea = box("lv-spontanea", "section", ["es-sec-cta"], [
    box("lv-spontanea-inner", "div", ["es-inner-640"], [
        head("lv-sp-titolo", "h2", ["es-display-2-white"], "Candidatura spontanea", margin("s-lv-sp-titolo", 0, 20)),
        para("lv-sp-testo", ["es-lead-muted-dark"], "Non trovi una posizione in linea con il tuo profilo? Inviaci comunque il tuo curriculum, raccontandoci le tue competenze e l'ambito in cui ti piacerebbe lavorare. Valuteremo il tuo profilo in relazione alle opportunità in azienda.", margin("s-lv-sp-testo", 0, 40)),
    ]),
    box("lv-sp-box", "div", ["es-form-box"], [
        form("lv-form", "Candidatura spontanea", "es_candidatura_spontanea",
             campi_comuni("s") + [field("s4", "ambito", "text", "Ambito di interesse", required=False, placeholder="Es. commerciale, tecnico, produzione…")] + campi_finali("s"),
             "[Sito] Candidatura spontanea"),
    ]),
], el="e-flexbox", cssid="candidatura")

pagina = box("lv-main", "main", ["es-page"], [hero, intro, posizioni, spontanea], el="e-flexbox", cssid="content")

# --- Template "Scheda posizione" ---
t_hero = box("sp-hero", "section", ["es-cat-hero"], [
    box("sp-hero-inner", "div", ["es-inner"], [
        sc("sp-breadcrumb", "[es_breadcrumb_archivio]"),
        para("sp-kicker", ["es-kicker-dark"], dyn("shortcode", "site", {"shortcode": T("[es_posizione_kicker]")})),
        head("sp-titolo", "h1", ["es-display-2-white"], dyn("post-title", "post"), margin("s-sp-titolo", 18, 0, {"max-width": size("20ch", "custom")})),
        sc("sp-meta", "[es_posizione_meta]"),
    ]),
], el="e-flexbox", styles=local("s-sp-hero", {"min-height": size("38vh", "custom")}))

campo_posizione = field("p0", "posizione", "hidden", "Posizione", required=False, field_value="",
                        __dynamic__={"field_value": '[elementor-tag id="e5a1c01" name="post-title" settings="%7B%7D"]'})

t_body = box("sp-body", "section", ["es-sec-body"], [
    box("sp-grid", "div", ["es-inner", "es-job-detail-grid"], [
        box("sp-descrizione", "div", ["es-col-plain"], [
            head("sp-desc-titolo", "h2", ["es-display-1"], "La posizione", margin("s-sp-desc-titolo", 0, 20, {"max-width": size("20ch", "custom")})),
            sc("sp-corpo", "[es_posizione_corpo]"),
        ]),
        box("sp-sidebar", "aside", ["es-job-sidebar"], [
            head("sp-form-titolo", "h2", ["es-h3"], "Candidati per questa posizione", margin("s-sp-form-titolo", 0, 28)),
            form("sp-form", "Candidatura posizione", "es_candidatura_posizione",
                 [campo_posizione] + campi_comuni("p") + campi_finali("p"),
                 '[Sito] Candidatura — [field id="posizione"]'),
        ]),
        box("sp-back", "div", ["es-job-back-row"], [
            w("sp-back-btn", "e-button", {"classes": cls("es-job-back"), "text": H("Vedi tutte le posizioni aperte"), "link": page(262, "Lavora con noi"), "tag": T("button")}),
        ]),
    ]),
], el="e-flexbox")

template = box("sp-main", "main", ["es-page"], [t_hero, t_body], el="e-flexbox", cssid="content")

json.dump([pagina], open(sys.argv[1], "w"), ensure_ascii=False)
json.dump([template], open(sys.argv[2], "w"), ensure_ascii=False)
print("ok")
