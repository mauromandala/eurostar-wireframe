#!/usr/bin/env node
/*
 * Zoom 200% (finestra 1280×900 → 640×450 px CSS a densità 2) sullo staging. Per ogni pagina:
 *  - scroll orizzontale;
 *  - testo tagliato: elementi con testo e overflow nascosto il cui contenuto supera il riquadro (esclusi caroselli,
 *    righe di filtri che scorrono ed estratti troncati di proposito con line-clamp);
 *  - testo sovrapposto: righe di testo di elementi diversi che si sovrappongono;
 *  - spazio occupato da header e barre fisse (sticky/fixed) dopo aver scorso la pagina;
 *  - Tab fino in fondo: elementi con il focus coperti da header/barre fisse.
 * Uso: PW=… CHROME=… node zoom.js [pagine]   → zoom-200.json
 */
const path = require('path');
const fs = require('fs');
const { chromium } = require(process.env.PW || 'playwright-core');
const ST = 'https://eurostar.demoengagemint.it/';
const PAGINE = {
  home: '', catalogo: 'macchine/', riempitrici: 'categoria-macchina/riempitrici/', 'linee-complete': 'categoria-macchina/linee-complete/',
  usate: 'categoria-macchina/usate/', 'settore-vino': 'settori/vino/', 'mec-ld': 'macchine/mec-ld/', squadron: 'squadron/', settori: 'settori/',
  servizi: 'servizi/', 'chi-siamo': 'chi-siamo/', referenze: 'referenze/', contatti: 'contatti/', conferma: 'conferma/', 'p404': 'pagina-che-non-esiste-test/',
  cataloghi: 'cataloghi/', 'lavora-con-noi': 'lavora-con-noi/', posizione: 'lavora-con-noi/area-manager/', news: 'news/',
  articolo: 'eurostar-cosmachine-portogallo/', 'caso-studio': 'castello-di-verrazzano-riempitrice-maxima/', ricerca: '?s=vino', 'home-en': 'en/',
};

function analizza() {
  const W = innerWidth;
  const nomeEl = (e) => e.tagName.toLowerCase() + (typeof e.className === 'string' && e.className ? '.' + e.className.trim().split(/\s+/).filter((c) => /^es-|^elementor-(widget-|field|button)/.test(c)).slice(0, 2).join('.') : '');
  // Testo tagliato
  const tagliati = [];
  for (const e of document.querySelectorAll('body *')) {
    const cs = getComputedStyle(e);
    if (!/(hidden|clip)/.test(cs.overflowX + cs.overflowY) || !e.innerText || !e.innerText.trim()) continue;
    if (e.closest('.es-news-track,.es-filterbar-inner,.es-clienti-nastro,.es-logo-strip,[class*=marquee],.es-mega,#es-nav-links,.screen-reader-text,.es-sr-only,.skip-link') || cs.webkitLineClamp !== 'none' || e.matches('[class*=es-art-excerpt]')) continue;
    if (e.getBoundingClientRect().width < 2) continue;
    if (e.scrollWidth > e.clientWidth + 2 || e.scrollHeight > e.clientHeight + 2) tagliati.push({ el: nomeEl(e), testo: e.innerText.trim().slice(0, 40), sw: e.scrollWidth, cw: e.clientWidth, sh: e.scrollHeight, ch: e.clientHeight });
  }
  // Testo sovrapposto (righe di elementi diversi nel main)
  const main = document.querySelector('main#content') || document.body;
  const righe = [];
  const tw = document.createTreeWalker(main, NodeFilter.SHOW_TEXT); let n;
  while ((n = tw.nextNode())) {
    if (!n.textContent.trim()) continue;
    const el = n.parentElement; const cs = getComputedStyle(el);
    if (cs.visibility === 'hidden' || el.closest('[hidden],.screen-reader-text,[aria-hidden="true"]')) continue;
    const r = document.createRange(); r.selectNodeContents(n);
    for (const q of r.getClientRects()) if (q.width > 2 && q.height > 2) righe.push({ el, t: n.textContent.trim().slice(0, 30), x: q.left, y: q.top + scrollY, w: q.width, h: q.height });
  }
  const sovrapposti = [];
  for (let i = 0; i < righe.length; i++) for (let j = i + 1; j < righe.length; j++) {
    const a = righe[i], b = righe[j]; if (a.el === b.el || a.el.contains(b.el) || b.el.contains(a.el)) continue;
    const ox = Math.min(a.x + a.w, b.x + b.w) - Math.max(a.x, b.x), oy = Math.min(a.y + a.h, b.y + b.h) - Math.max(a.y, b.y);
    if (ox > 4 && oy > Math.min(a.h, b.h) * 0.4) sovrapposti.push(`"${a.t}" ↔ "${b.t}" @y${Math.round(a.y)}`);
  }
  return { sw: document.documentElement.scrollWidth, W, tagliati: tagliati.slice(0, 15), sovrapposti: sovrapposti.slice(0, 15) };
}

// Altezza occupata in alto e in basso da elementi fissi/sticky a pagina scorsa.
function fissi() {
  const H = innerHeight; let alto = 0, basso = 0; const el = [];
  for (const e of document.querySelectorAll('body *')) {
    const cs = getComputedStyle(e); if (!/(sticky|fixed)/.test(cs.position)) continue;
    const r = e.getBoundingClientRect(); if (r.height < 2 || r.width < 2 || r.bottom <= 0 || r.top >= H || cs.visibility === 'hidden') continue;
    if (e.parentElement && (() => { for (let a = e.parentElement; a; a = a.parentElement) { if (/(sticky|fixed)/.test(getComputedStyle(a).position)) return true; } return false; })()) continue;
    el.push(`${e.tagName.toLowerCase()}.${String(e.className).split(/\s+/).filter((c) => /^es-|^elementor-location|cookie|cc-/.test(c)).slice(0, 2).join('.') || e.id} ${Math.round(r.top)}–${Math.round(r.bottom)} w${Math.round(r.width)}`);
    if (r.width > innerWidth * 0.5) { if (r.top < H / 2) alto = Math.max(alto, r.bottom); else basso = Math.max(basso, H - r.top); }
  }
  return { alto: Math.round(alto), basso: Math.round(basso), H, el };
}

function focusCoperto(ricontrollo) {
  let e = document.activeElement; if (!e || e === document.body) return null;
  while (e.shadowRoot && e.shadowRoot.activeElement) e = e.shadowRoot.activeElement;
  window.__visti = window.__visti || new Set(); if (!ricontrollo) { if (window.__visti.has(e)) return { fine: true }; window.__visti.add(e); }
  const r = e.getBoundingClientRect(); if (r.width < 2 || r.height < 2) return { ok: true };
  const punti = [[r.left + r.width / 2, r.top + Math.min(r.height / 2, 10)], [r.left + 4, r.top + 4], [r.right - 4, r.bottom - 4]];
  let coperti = 0; let da = '';
  for (const [x, y] of punti) {
    if (y < 0 || y > innerHeight || x < 0 || x > innerWidth) continue;
    const h = document.elementFromPoint(x, y); if (!h || e.contains(h) || h.contains(e) || (e.getRootNode().host && e.getRootNode().host.contains(h))) continue;
    for (let a = h; a; a = a.parentElement) { const cs = getComputedStyle(a); if (/(sticky|fixed)/.test(cs.position)) { coperti++; da = a.tagName.toLowerCase() + '.' + String(a.className).split(/\s+/).slice(0, 2).join('.'); break; } }
  }
  return { ok: coperti < 2, nome: (e.getAttribute('aria-label') || e.innerText || e.name || '').trim().slice(0, 35), da };
}

(async () => {
  const filtro = process.argv[2] ? process.argv[2].split(',') : null;
  const b = await chromium.launch({ executablePath: process.env.CHROME });
  const ctx = await b.newContext({ viewport: { width: 640, height: 450 }, deviceScaleFactor: 2, reducedMotion: 'reduce' });
  const p = await ctx.newPage();
  const out = {};
  for (const [nome, url] of Object.entries(PAGINE)) {
    if (filtro && !filtro.includes(nome)) continue;
    await p.goto(ST + url, { waitUntil: 'networkidle', timeout: 60000 });
    const r = p.getByRole('button', { name: /^(Rifiuta|Reject)/i }).first();
    if (await r.waitFor({ state: 'visible', timeout: 2500 }).then(() => true, () => false)) { await r.click(); await p.waitForTimeout(400); await p.reload({ waitUntil: 'networkidle' }); }
    await p.evaluate(() => document.fonts.ready);
    const an = await p.evaluate(analizza);
    await p.evaluate(() => window.scrollTo(0, Math.min(1500, document.documentElement.scrollHeight / 2))); await p.waitForTimeout(500);
    const fx = await p.evaluate(fissi);
    await p.evaluate(() => { window.scrollTo(0, 0); document.activeElement && document.activeElement.blur(); window.__visti = new Set(); });
    const coperti = [];
    for (let i = 0; i < 300; i++) { await p.keyboard.press('Tab'); let f = await p.evaluate(focusCoperto, false); if (!f) continue; if (f.fine) break; if (!f.ok) { await p.waitForTimeout(600); f = await p.evaluate(focusCoperto, true); } if (!f.ok) coperti.push(f.nome + ' ← ' + f.da); }
    out[nome] = { ...an, fissi: fx, focusCoperti: coperti };
    console.log(`${nome}: sw ${an.sw}/${an.W}, tagliati ${an.tagliati.length}, sovrapposti ${an.sovrapposti.length}, fissi alto ${fx.alto}px basso ${fx.basso}px su ${fx.H}, focus coperti ${coperti.length}`);
  }
  await b.close();
  fs.writeFileSync(path.join(__dirname, 'zoom-200' + (filtro ? '-' + filtro.join('-') : '') + '.json'), JSON.stringify(out, null, 1));
})().catch((e) => { console.error(e); process.exit(1); });
