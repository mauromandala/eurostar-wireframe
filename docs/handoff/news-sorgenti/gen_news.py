#!/usr/bin/env python3
"""Trasforma "TESTI NEWS SITO EUROSTAR + Casi studio.docx" (convertito in testo) negli articoli da caricare.
Uso: gen_news.py testi-news-originale.txt news-it.json
Escluse le note redazionali (Nota, Suggerisco, IMG ALLEGATE, Troverai, [DA VERIFICARE]); "Fonte" e "Suggerisco di citare" diventano il campo fonte."""
import json, re, sys, html

MESI = {"Gennaio": 1, "Febbraio": 2, "Marzo": 3, "Aprile": 4, "Maggio": 5, "Giugno": 6, "Luglio": 7,
        "Agosto": 8, "Settembre": 9, "Ottobre": 10, "Novembre": 11, "Dicembre": 12}

# Date: quelle del documento; per i casi studio il mese della fonte; per i 6 articoli senza mese date provvisorie (decisione 29/09).
DATE = {
    "art1": "2026-05-20", "art2": "2026-01-22", "art3": "2026-03-12", "art4": "2026-05-12",
    "art5": "2026-02-16", "art6": "2026-03-24", "art7": "2025-12-18", "art8": "2026-01-24",
    "art9": "2026-07-08", "art10": "2026-06-15", "art11": "2025-12-04", "art12": "2026-04-14",
    "art13": "2026-02-10", "art14": "2026-08-26", "caso1": "2026-04-20", "caso2": "2026-09-15",
}
PROVVISORIE = {"art5", "art6", "art8", "art9", "art12", "art14"}

IMMAGINI = {
    "art1": "Blog_Robo Challenge ATPICA.avif", "art2": "Blog_Martigny.jpeg", "art3": "Blog_Enoliexpo Adriatica.jpeg",
    "art4": "Blog_Wine To Asia, Shenzhen.jpeg", "art5": "Blog_GT Vinea.jpeg", "art6": "Blog_Bambini delle Fate.jpeg",
    "art7": "Blog_Catering I Talenti.jpeg", "art8": "Blog_Open Days Itis.jpeg", "art9": "Blog_Apprendistato Gabriele.jpeg",
    "art10": "Blog_Borse di studio Atipica.jpeg", "art12": "Blog_POWERFILL su \"I Grandi Oli d'Italia\".png",
    "art14": "Blog_Cosmachine.jpeg",
}

# Sommario delle card: per i casi studio la "versione sintetica per carosello" del documento, per gli articoli la prima frase.
SLUG = {
    "caso1": "frantoio-bonamini-monoblocco-mec-av", "caso2": "castello-di-verrazzano-riempitrice-maxima",
    "art1": "robo-challenge-atpica", "art2": "agrovina-2026-squadron-olympia", "art3": "enoliexpo-adriatica-mec-ld",
    "art4": "wine-to-asia-2026-shenzhen", "art5": "eurostar-georgia-gt-vinea", "art6": "eurostar-i-bambini-delle-fate",
    "art7": "natale-eurostar-i-talenti-di-calamandrana", "art8": "open-day-itis-artom", "art9": "apprendistato-duale-gabriele",
    "art10": "borse-di-studio-atpica-2026", "art11": "brevetti-pionieri-30-anni-eurostar",
    "art12": "soluzioni-imbottigliamento-olio", "art13": "powerfill-riempitrice-a-flussimetri", "art14": "eurostar-cosmachine-portogallo",
}

NOTE = ("Nota", "Suggerisco", "IMG ALLEGATE", "Troverai", "Versioni sintetiche", "BLOG")


def esc(s):
    return html.escape(s, quote=False)


def first_sentence(s):
    m = re.match(r"(.+?[.!?])(\s|$)", s)
    return m.group(1) if m else s


def body_html(lines):
    """Paragrafi, titoli di sezione (H2), elenchi puntati (ul) e citazioni (blockquote con autore)."""
    out, ul = [], []
    def flush():
        if ul:
            out.append("<ul>" + "".join(f"<li>{esc(x)}</li>" for x in ul) + "</ul>")
            ul.clear()
    for ln in lines:
        if ln.startswith("•"):
            ul.append(ln.lstrip("• \t").strip())
            continue
        flush()
        q = re.match(r'^[«"“](.+?)[»"”]\s*(?:—\s*(.+))?$', ln)
        if q:
            cite = f"<cite>{esc(q.group(2))}</cite>" if q.group(2) else ""
            out.append(f"<blockquote><p>«{esc(q.group(1))}»</p>{cite}</blockquote>")
        elif len(ln) < 90 and not re.search(r"[.:;!?]$", ln):
            out.append(f"<h2>{esc(ln)}</h2>")
        else:
            out.append(f"<p>{esc(ln)}</p>")
    flush()
    return "\n".join(out)


def main(src, dst):
    raw = [l.rstrip("\n") for l in open(src, encoding="utf-8")]
    lines = [re.sub(r"^\t•\t", "• ", l).strip() for l in raw]
    # Blocchi: casi studio e articoli
    starts = [i for i, l in enumerate(lines) if re.match(r"^(CASO STUDIO \d|ARTICOLO \d+)", l)]
    starts.append(len(lines))
    sintesi = {}
    for i, l in enumerate(lines[:starts[0]]):
        if l.startswith("Frantoio Bonamini —"):
            sintesi["caso1"] = lines[i + 1]
        if l.startswith("Castello di Verrazzano —"):
            sintesi["caso2"] = lines[i + 1]
    items = []
    for a, b in zip(starts, starts[1:]):
        block = [l for l in lines[a:b] if l]
        head = block[0]
        key = ("caso" + re.search(r"CASO STUDIO (\d)", head).group(1)) if head.startswith("CASO") else ("art" + re.search(r"ARTICOLO (\d+)", head).group(1))
        rest = block[1:]
        fonte = ""
        keep = []
        for l in rest:
            if l.startswith("Fonte:"):
                fonte = l[len("Fonte:"):].strip()
                continue
            m = re.search(r"\((Caso pubblicato originariamente[^)]*)\)", l)
            if l.startswith("Suggerisco") and m:
                fonte = m.group(1)
                continue
            if l.startswith(NOTE):
                continue
            keep.append(l)
        item = {"key": key, "slug": SLUG[key], "data": DATE[key], "data_provvisoria": key in PROVVISORIE,
                "immagine": IMMAGINI.get(key), "fonte": fonte}
        if key.startswith("caso"):
            item["categoria"] = "Casi studio"
            # nel documento il titolo del caso 1 ha la F iniziale doppia ("FFrantoio")
            item["titolo"] = re.sub(r"^FF", "F", keep[0])
            scheda = keep[1]
            m = re.match(r"Settore:\s*(.+?)\s+Location:\s*(.+?)\s+Macchina Eurostar:\s*(.+)$", scheda)
            item["settore"], item["luogo"], item["macchina"] = m.group(1), m.group(2), m.group(3)
            body = keep[2:]
            # "La voce del cliente" (riquadro laterale del wireframe editoriale): l'ultima citazione del cliente,
            # tolta dal corpo per non ripeterla; le altre restano nella sezione "La testimonianza".
            cit = [i for i, l in enumerate(body) if l.startswith("«") and "Eurostar —" not in l.split("»")[-1] and "Castagno" not in l.split("»")[-1]]
            voce = body.pop(cit[-1])
            m = re.match(r"^«(.+)»\s*—\s*(.+?),\s*(.+)$", voce)
            item["voce_cliente"] = {"testo": m.group(1), "nome": m.group(2), "ruolo": m.group(3)[0].upper() + m.group(3)[1:]}
            item["sommario"] = sintesi[key]
            item["tempo_lettura"] = None
        else:
            meta_i = next(i for i, l in enumerate(keep) if l.startswith("Categoria:"))
            meta = keep[meta_i]
            cat = re.search(r"Categoria:\s*([^|]+)", meta).group(1).strip()
            item["categoria"] = re.split(r"\s+—\s+", cat)[0]
            t = re.search(r"(\d+)\s*min di lettura", meta)
            item["tempo_lettura"] = int(t.group(1)) if t else None
            item["titolo"] = keep[meta_i + 1]
            body = keep[meta_i + 2:]
            # [DA VERIFICARE] dentro un paragrafo: si toglie la frase della nota
            body = [re.sub(r"\s*\[DA VERIFICARE\].*$", "", l) for l in body]
            item["sommario"] = first_sentence(body[0])
        item["corpo"] = body_html(body)
        items.append(item)
    json.dump(items, open(dst, "w", encoding="utf-8"), ensure_ascii=False, indent=1)
    for it in items:
        print(it["key"], "|", it["data"], "P" if it["data_provvisoria"] else " ", "|", it["categoria"], "|", it["titolo"][:70], "|", it.get("tempo_lettura"), "|", (it["immagine"] or "-")[:30], "|", it["fonte"][:40])


main(sys.argv[1], sys.argv[2])
