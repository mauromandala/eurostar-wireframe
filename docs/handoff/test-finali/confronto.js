#!/usr/bin/env node
/*
 * Confronto al pixel wireframe (http://localhost:4173) ↔ staging, pagina per pagina e larghezza per larghezza.
 * Per ogni riga di testo dentro il <main> misura la posizione (Range.getClientRects, relativa al main),
 * abbina i testi uguali nell'ordine della pagina e segnala:
 *  - "salti": dove lo scarto verticale cambia di più di SOGLIA px rispetto al testo abbinato precedente
 *    (lì c'è una differenza di layout; gli scarti successivi la ereditano e non vengono ripetuti);
 *  - scarti orizzontali oltre SOGLIA; differenze di dimensione, peso o colore del font;
 *  - scroll orizzontale (scrollWidth > larghezza).
 * Uso: PW=<percorso di playwright-core> CHROME=<eseguibile chromium> node confronto.js [larghezze] [filtro-pagine]
 *   es. node confronto.js 1024,1280 home,news   → report in report-<larghezze>.json e .md accanto allo script
 */
const path = require('path');
const fs = require('fs');
const { chromium } = require(process.env.PW || 'playwright-core');

const WF = 'http://localhost:4173/';
const ST = 'https://eurostar.demoengagemint.it/';
const SOGLIA = 1.5;
const PAGINE = [
  ['home', 'home.html', ''],
  ['catalogo', 'archive-macchine.html', 'macchine/'],
  ['riempitrici', 'taxonomy-macchina-riempitrici.html', 'categoria-macchina/riempitrici/'],
  ['sciacquatrici', 'taxonomy-macchina-sciacquatrici.html', 'categoria-macchina/sciacquatrici/'],
  ['linee-complete', 'taxonomy-macchina-linee-complete.html', 'categoria-macchina/linee-complete/'],
  ['usate', 'taxonomy-macchina-usate.html', 'categoria-macchina/usate/'],
  ['settore-vino', 'taxonomy-settore.html', 'settori/vino/'],
  ['settore-birra', 'taxonomy-settore-birra.html', 'settori/birra/'],
  ['mec-ld', 'single-macchina.html', 'macchine/mec-ld/'],
  ['canfill', 'single-macchina-canfill.html', 'macchine/canfill/'],
  ['squadron', 'page-squadron.html', 'squadron/'],
  ['settori', 'archive-settori.html', 'settori/'],
  ['servizi', 'archive-servizi.html', 'servizi/'],
  ['chi-siamo', 'page-chi-siamo.html', 'chi-siamo/'],
  ['referenze', 'page-referenze.html', 'referenze/'],
  ['contatti', 'page-contatti.html', 'contatti/'],
  ['conferma', 'page-conferma.html', 'conferma/'],
  ['404', '404.html', 'pagina-che-non-esiste-test/'],
  ['cataloghi', 'page-cataloghi.html', 'cataloghi/'],
  ['lavora-con-noi', 'page-lavora-con-noi.html', 'lavora-con-noi/'],
  ['posizione', 'single-posizione-lavoro-area-manager.html', 'lavora-con-noi/area-manager/'],
  ['news', 'archive-news.html', 'news/'],
  ['articolo', 'single-news-articolo.html', 'eurostar-cosmachine-portogallo/'],
  ['caso-studio', 'single-news-editoriale.html', 'castello-di-verrazzano-riempitrice-maxima/'],
];

// Eseguita nella pagina: righe di testo visibili del main con posizione e stile.
function estrai() {
  const main = document.querySelector('main#content') || document.querySelector('main');
  const mr = main.getBoundingClientRect();
  const items = [];
  const w = document.createTreeWalker(main, NodeFilter.SHOW_TEXT);
  let n;
  while ((n = w.nextNode())) {
    const t = n.textContent.replace(/\s+/g, ' ').trim();
    if (!t) continue;
    const el = n.parentElement;
    if (el.closest('script,style,noscript,template,[hidden],[aria-hidden="true"] .screen-reader-text,.screen-reader-text,.es-sr-only,.sr-only,.visually-hidden')) continue;
    const cs = getComputedStyle(el);
    if (cs.visibility === 'hidden' || cs.display === 'none' || +cs.opacity === 0) continue;
    const r = document.createRange();
    r.selectNodeContents(n);
    const b = [...r.getClientRects()].find((q) => q.width > 1 && q.height > 1);
    if (!b) continue;
    items.push({ t: t.slice(0, 50).toLowerCase(), x: +(b.left - mr.left).toFixed(1), y: +(b.top - mr.top).toFixed(1),
      f: cs.fontSize + ' ' + cs.fontWeight + ' ' + cs.fontFamily.split(',')[0].replace(/"/g, ''), c: cs.color });
  }
  return { items, h: +mr.height.toFixed(1), sw: document.documentElement.scrollWidth };
}

async function misura(page, url, width) {
  await page.setViewportSize({ width, height: 900 });
  const res = await page.goto(url, { waitUntil: 'networkidle', timeout: 60000 }).catch((e) => ({ err: e.message }));
  if (res && res.err) return { err: res.err };
  await page.evaluate(() => document.fonts.ready);
  // Scorre fino in fondo e torna su: fa partire le animazioni di comparsa e il caricamento differito.
  await page.evaluate(async () => {
    for (let y = 0; y < document.documentElement.scrollHeight; y += 600) { window.scrollTo(0, y); await new Promise((r) => setTimeout(r, 40)); }
    window.scrollTo(0, 0); await new Promise((r) => setTimeout(r, 400));
  });
  return page.evaluate(estrai);
}

function confronta(a, b) {
  const pairs = [];
  let j = 0;
  for (const it of a.items) {
    for (let k = j; k < Math.min(b.items.length, j + 60); k++) {
      if (b.items[k].t === it.t) { pairs.push([it, b.items[k]]); j = k + 1; break; }
    }
  }
  const salti = [], dx = [], stile = [];
  let prev = 0;
  for (const [p, q] of pairs) {
    const dy = +(q.y - p.y).toFixed(1);
    if (Math.abs(dy - prev) > SOGLIA) salti.push({ t: p.t, wf: p.y, st: q.y, salto: +(dy - prev).toFixed(1) });
    prev = dy;
    if (Math.abs(q.x - p.x) > SOGLIA) dx.push({ t: p.t, wf: p.x, st: q.x });
    if (p.f !== q.f || p.c !== q.c) stile.push({ t: p.t, wf: p.f + ' ' + p.c, st: q.f + ' ' + q.c });
  }
  return { abbinati: pairs.length, wf: a.items.length, st: b.items.length, salti, dx, stile, hWf: a.h, hSt: b.h, swWf: a.sw, swSt: b.sw };
}

(async () => {
  const larghezze = (process.argv[2] || '1024,1280').split(',').map(Number);
  const filtro = process.argv[3] ? process.argv[3].split(',') : null;
  const browser = await chromium.launch({ executablePath: process.env.CHROME });
  const ctx = await browser.newContext({ reducedMotion: 'reduce' });
  const pa = await ctx.newPage(), pb = await ctx.newPage();
  const out = {};
  for (const [nome, wf, st] of PAGINE) {
    if (filtro && !filtro.includes(nome)) continue;
    out[nome] = {};
    for (const w of larghezze) {
      const [a, b] = await Promise.all([misura(pa, WF + wf, w), misura(pb, ST + st, w)]);
      out[nome][w] = a.err || b.err ? { err: a.err || b.err } : confronta(a, b);
      const r = out[nome][w];
      process.stdout.write(`${nome} ${w}: ${r.err ? 'ERRORE ' + r.err : `${r.abbinati}/${r.wf} testi, salti ${r.salti.length}, dx ${r.dx.length}, stile ${r.stile.length}, h ${r.hWf}→${r.hSt}, sw ${r.swSt}`}\n`);
    }
  }
  await browser.close();
  const base = path.join(__dirname, 'report-' + larghezze.join('-') + (filtro ? '-' + filtro.join('-') : ''));
  fs.writeFileSync(base + '.json', JSON.stringify(out, null, 1));
})().catch((e) => { console.error(e); process.exit(1); });
