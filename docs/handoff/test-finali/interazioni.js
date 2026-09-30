#!/usr/bin/env node
/*
 * Prove delle interazioni da tastiera sullo staging (solo tasti: Tab, Invio, Spazio, Esc, frecce).
 * Nessun form viene inviato. Uso: PW=… CHROME=… node interazioni.js
 */
const { chromium } = require(process.env.PW || 'playwright-core');
const ST = 'https://eurostar.demoengagemint.it/';
const esiti = [];
const ok = (nome, cond, det = '') => { esiti.push([cond ? 'OK ' : 'NO ', nome, det]); console.log((cond ? 'OK  ' : 'NO  ') + nome + (det ? '  — ' + det : '')); };

async function apri(ctx, url) {
  const p = await ctx.newPage();
  await p.goto(ST + url, { waitUntil: 'networkidle' });
  const r = p.getByRole('button', { name: /^(Rifiuta|Reject)/i }).first();
  if (await r.waitFor({ state: 'visible', timeout: 3000 }).then(() => true, () => false)) { await r.click(); await p.waitForTimeout(400); await p.reload({ waitUntil: 'networkidle' }); }
  return p;
}
const att = (p) => p.evaluate(() => { let e = document.activeElement; while (e && e.shadowRoot && e.shadowRoot.activeElement) e = e.shadowRoot.activeElement;
  return e ? { id: e.id, cls: String(e.className), txt: (e.getAttribute('aria-label') || e.innerText || '').trim().slice(0, 40), inMain: !!e.closest('main#content'), tag: e.tagName } : null; });
// Porta il focus sull'elemento con Tab (come un utente), partendo dall'inizio della pagina.
async function tabFino(p, pred, max = 150) {
  await p.evaluate(() => { document.activeElement && document.activeElement.blur(); window.scrollTo(0, 0); });
  for (let i = 0; i < max; i++) { await p.keyboard.press('Tab'); const a = await att(p); if (a && pred(a)) return a; }
  return null;
}

(async () => {
  const b = await chromium.launch({ executablePath: process.env.CHROME });
  const desk = await b.newContext({ viewport: { width: 1440, height: 900 } });
  const mob = await b.newContext({ viewport: { width: 375, height: 812 }, hasTouch: true });

  // 1. Salta al contenuto
  let p = await apri(desk, '');
  await p.evaluate(() => { document.activeElement && document.activeElement.blur(); window.scrollTo(0, 0); });
  await p.keyboard.press('Tab');
  let a = await att(p);
  const skipVis = await p.evaluate(() => { const r = document.activeElement.getBoundingClientRect(); return r.width > 20 && r.height > 10 && r.top >= 0; });
  ok('Primo Tab = "Vai al contenuto", visibile', /contenuto/i.test(a.txt) && skipVis, a.txt);
  await p.keyboard.press('Enter'); await p.waitForTimeout(300);
  await p.keyboard.press('Tab'); a = await att(p);
  ok('Invio sul link porta dentro il main', a.inMain, a.txt);

  // 2. Mega-menu Macchine
  a = await tabFino(p, (x) => x.id === 'es-mega-trigger');
  ok('Pulsante sottomenu Macchine raggiungibile', !!a, a && a.txt);
  await p.keyboard.press('Enter'); await p.waitForTimeout(400);
  let st = await p.evaluate(() => { const t = document.getElementById('es-mega-trigger'); const pn = document.getElementById(t.getAttribute('aria-controls')); const r = pn.getBoundingClientRect(); return { exp: t.getAttribute('aria-expanded'), vis: getComputedStyle(pn).visibility, h: r.height }; });
  ok('Invio apre il mega-menu (aria-expanded, pannello visibile)', st.exp === 'true' && st.vis === 'visible' && st.h > 50, JSON.stringify(st));
  await p.keyboard.press('Tab'); a = await att(p);
  const inPanel = await p.evaluate(() => { let e = document.activeElement; return !!e.closest('.es-mega'); });
  ok('Tab successivo entra nel pannello', inPanel, a.txt);
  await p.keyboard.press('Escape'); await p.waitForTimeout(300); a = await att(p);
  st = await p.evaluate(() => document.getElementById('es-mega-trigger').getAttribute('aria-expanded'));
  ok('Esc chiude e riporta il focus sul pulsante', st === 'false' && a.id === 'es-mega-trigger', a.txt);
  // Focus che esce dal pannello con Tab
  await p.keyboard.press('Enter'); await p.waitForTimeout(300);
  let uscito = null;
  for (let i = 0; i < 40; i++) { await p.keyboard.press('Tab'); const info = await p.evaluate(() => { const e = document.activeElement; const pn = document.querySelector('.es-mega.is-open'); return { dentro: !!e.closest('.es-mega'), aperto: !!pn, txt: (e.innerText || e.getAttribute('aria-label') || '').trim().slice(0, 30), coperto: pn ? (() => { const r = e.getBoundingClientRect(), q = pn.getBoundingClientRect(); return r.top < q.bottom && r.bottom > q.top && r.left < q.right && r.right > q.left; })() : false }; }); if (!info.dentro) { uscito = info; break; } }
  ok('Uscendo dal pannello con Tab il pannello si chiude', uscito && !uscito.aperto, uscito && JSON.stringify(uscito));
  // Maiusc+Tab dalla prima voce torna al pulsante (pannello ancora aperto)
  await p.focus('#es-mega-trigger'); await p.keyboard.press('Enter'); await p.waitForTimeout(300); await p.keyboard.press('Tab'); await p.keyboard.press('Shift+Tab');
  a = await att(p); st = await p.evaluate(() => document.getElementById('es-mega-trigger').getAttribute('aria-expanded'));
  ok('Maiusc+Tab dalla prima voce torna al pulsante', a.id === 'es-mega-trigger' && st === 'true', a.txt);
  await p.keyboard.press('Escape');

  // 3. Ricerca dall'header
  a = await tabFino(p, (x) => x.id === 'es-search-trigger');
  await p.keyboard.press('Enter'); await p.waitForTimeout(300); a = await att(p);
  ok('Invio sulla lente apre la ricerca con il focus nel campo', a.id === 'es-search-input', a.id);
  await p.keyboard.press('Escape'); await p.waitForTimeout(200); a = await att(p);
  ok('Esc chiude la ricerca e torna alla lente', a.id === 'es-search-trigger', a.id);
  await p.keyboard.press('Enter'); await p.waitForTimeout(200);
  await p.keyboard.type('riempitrice'); await Promise.all([p.waitForNavigation({ waitUntil: 'networkidle' }), p.keyboard.press('Enter')]);
  const h1 = await p.evaluate(() => document.querySelector('h1').textContent);
  ok('Invio nel campo apre i risultati', /riempitrice/i.test(h1) && p.url().includes('s=riempitrice'), h1);

  // 4. Torna su
  p = await apri(desk, 'servizi/');
  await p.evaluate(() => window.scrollTo(0, document.documentElement.scrollHeight));
  await p.focus('#es-back-to-top'); await p.keyboard.press('Enter'); await p.waitForTimeout(1200);
  a = await att(p); const y = await p.evaluate(() => scrollY);
  ok('"Torna su" riporta in cima', y < 5, 'scrollY ' + y);
  await p.keyboard.press('Tab'); a = await att(p);
  const yDopo = await p.evaluate(() => { const e = document.activeElement; return Math.round(e.getBoundingClientRect().top + scrollY); });
  ok('Dopo "Torna su" il Tab riparte dall\'inizio della pagina', yDopo < 300, `focus su "${a.txt}" a y ${yDopo}`);

  // 5. Filtri del catalogo
  p = await apri(desk, 'macchine/');
  a = await tabFino(p, (x) => /es-filter/.test(x.cls) && /riempitrici/i.test(x.txt));
  ok('Filtro "Riempitrici" raggiungibile', !!a);
  await p.keyboard.press('Enter'); await p.waitForTimeout(300);
  st = await p.evaluate(() => ({ pressed: document.activeElement.getAttribute('aria-pressed'), card: [...document.querySelectorAll('.es-mcard')].filter((c) => c.offsetParent).length, status: (document.querySelector('.es-catalog-status') || {}).textContent }));
  ok('Invio attiva il filtro (aria-pressed, card filtrate, annuncio)', st.pressed === 'true' && st.card > 0 && st.card < 21 && /\d/.test(st.status), JSON.stringify(st));
  await p.keyboard.press('Shift+Tab'); await p.keyboard.press('Shift+Tab'); a = await att(p);
  await p.keyboard.press('Space'); await p.waitForTimeout(300);
  st = await p.evaluate(() => ({ txt: document.activeElement.textContent, pressed: document.activeElement.getAttribute('aria-pressed'), card: [...document.querySelectorAll('.es-mcard')].filter((c) => c.offsetParent).length }));
  ok('Spazio su "Tutte" le mostra di nuovo', /tutte/i.test(st.txt) && st.pressed === 'true' && st.card === 21, JSON.stringify(st));

  // 6. News: filtro e "Mostra altri articoli"
  p = await apri(desk, 'news/');
  a = await tabFino(p, (x) => /es-news-more/.test(x.cls), 200);
  ok('"Mostra altri articoli" raggiungibile', !!a);
  const prima = await p.evaluate(() => [...document.querySelectorAll('#es-news-grid > li')].filter((l) => !l.hidden).length);
  await p.keyboard.press('Enter'); await p.waitForTimeout(300); a = await att(p);
  const dopo = await p.evaluate(() => [...document.querySelectorAll('#es-news-grid > li')].filter((l) => !l.hidden).length);
  const idx = await p.evaluate(() => [...document.querySelectorAll('#es-news-grid > li')].indexOf(document.activeElement.closest('li')));
  ok('Invio mostra gli altri e porta il focus sul primo nuovo', dopo > prima && idx === prima, `${prima}→${dopo}, focus su card ${idx + 1}`);

  // 7. Carosello News in Home
  p = await apri(desk, '');
  a = await tabFino(p, (x) => /successiv|next/i.test(x.txt), 200);
  ok('Freccia "successive" del carosello raggiungibile', !!a, a && a.txt);
  if (a) {
    const s0 = await p.evaluate(() => document.getElementById('es-news-track').scrollLeft);
    await p.keyboard.press('Enter'); await p.waitForTimeout(900);
    const s1 = await p.evaluate(() => ({ sl: document.getElementById('es-news-track').scrollLeft, cur: [...document.querySelectorAll('.es-news-dot')].findIndex((d) => d.getAttribute('aria-current') === 'true') }));
    ok('Invio scorre di una card e aggiorna il pallino', s1.sl > s0 && s1.cur === 1, JSON.stringify(s1));
    for (let i = 0; i < 6; i++) { await p.keyboard.press('Enter'); await p.waitForTimeout(700); }
    const fine = await p.evaluate(() => document.activeElement.getAttribute('aria-disabled'));
    ok('A fine carosello la freccia è aria-disabled', fine === 'true', fine);
  }

  // 8. Menu mobile (375)
  p = await apri(mob, '');
  a = await tabFino(p, (x) => x.id === 'es-nav-toggle');
  ok('Hamburger raggiungibile a 375', !!a, a && a.txt);
  await p.keyboard.press('Enter'); await p.waitForTimeout(400);
  st = await p.evaluate(() => { const t = document.getElementById('es-nav-toggle'); return { exp: t.getAttribute('aria-expanded'), label: t.getAttribute('aria-label') }; });
  ok('Invio apre il menu (aria-expanded, etichetta "Chiudi")', st.exp === 'true' && /chiudi/i.test(st.label), JSON.stringify(st));
  await p.keyboard.press('Tab'); a = await att(p);
  const inNav = await p.evaluate(() => !!document.activeElement.closest('#es-nav-links'));
  ok('Tab successivo entra nel menu', inNav, a.txt);
  let fuori = null;
  for (let i = 0; i < 30; i++) { await p.keyboard.press('Tab'); const inf = await p.evaluate(() => ({ dentro: !!document.activeElement.closest('#es-nav-links'), aperto: document.getElementById('es-nav-links').classList.contains('is-open'), txt: (document.activeElement.innerText || '').trim().slice(0, 30), dietro: (() => { const nl = document.getElementById('es-nav-links').getBoundingClientRect(); const r = document.activeElement.getBoundingClientRect(); return nl.height > 0 && r.top < nl.bottom && r.bottom > nl.top; })() })); if (!inf.dentro) { fuori = inf; break; } }
  ok('Uscendo dal menu con Tab il focus non finisce dietro il pannello aperto', !fuori || !fuori.aperto || !fuori.dietro, fuori && JSON.stringify(fuori));
  await p.focus('#es-nav-toggle'); await p.keyboard.press('Enter'); await p.waitForTimeout(300); await p.keyboard.press('Tab'); await p.keyboard.press('Tab');
  await p.keyboard.press('Escape'); await p.waitForTimeout(200);
  a = await att(p); st = await p.evaluate(() => document.getElementById('es-nav-toggle').getAttribute('aria-expanded'));
  ok('Esc chiude il menu e torna all\'hamburger', st === 'false' && a.id === 'es-nav-toggle', a.id);

  // 9. Form Contatti: ordine del Tab = ordine visivo, ogni campo con etichetta
  p = await apri(desk, 'contatti/');
  const campi = await p.evaluate(() => [...document.querySelectorAll('main form input:not([type=hidden]), main form select, main form textarea')].filter((e) => e.offsetParent && !e.closest('.elementor-field-type-honeypot') && e.tabIndex >= 0).map((e) => ({ n: e.name, lab: (e.labels && e.labels[0] ? e.labels[0].textContent.trim() : e.getAttribute('aria-label') || ''), req: e.required })));
  ok('Tutti i campi del form hanno un\'etichetta', campi.every((c) => c.lab), campi.filter((c) => !c.lab).map((c) => c.n).join(', '));
  a = await tabFino(p, (x) => x.tag === 'SELECT' && x.inMain);
  const ordine = [];
  for (let i = 0; i < campi.length; i++) { const nm = await p.evaluate(() => document.activeElement.name); ordine.push(nm); await p.keyboard.press('Tab'); }
  ok('Il Tab segue l\'ordine dei campi', JSON.stringify(ordine) === JSON.stringify(campi.map((c) => c.n)), ordine.join(' → '));
  const hp = await p.evaluate(() => { const h = document.querySelector('.elementor-field-type-honeypot input'); return h ? { tab: h.tabIndex, hidden: !h.offsetParent } : null; });
  ok('Il campo anti-spam non è raggiungibile col Tab', !hp || hp.tab < 0 || hp.hidden, JSON.stringify(hp));

  await b.close();
  const no = esiti.filter((e) => e[0].trim() === 'NO').length;
  console.log(`\n${esiti.length - no}/${esiti.length} prove superate`);
})().catch((e) => { console.error(e); process.exit(1); });
