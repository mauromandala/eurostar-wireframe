#!/usr/bin/env node
/*
 * Scansione da tastiera dello staging: su ogni pagina preme Tab dall'inizio fino a tornare in cima (max 400)
 * e per ogni elemento che riceve il focus controlla:
 *  - focus visibile: contorno (outline) o ombra (box-shadow) presenti mentre ha il focus;
 *  - elemento davvero visibile: dimensioni > 2px, non visibility:hidden/opacity 0, dentro la finestra dopo lo scorrimento
 *    (scova link di menu chiusi, pannelli nascosti, testo solo per lettori di schermo raggiungibile col Tab);
 *  - primo elemento = link "salta al contenuto".
 * Uso: PW=… CHROME=… node tastiera.js [larghezze] [filtro-pagine]   → tastiera-<larghezze>.json
 */
const path = require('path');
const fs = require('fs');
const { chromium } = require(process.env.PW || 'playwright-core');

const ST = 'https://eurostar.demoengagemint.it/';
const PAGINE = {
  home: '', catalogo: 'macchine/', riempitrici: 'categoria-macchina/riempitrici/', 'linee-complete': 'categoria-macchina/linee-complete/',
  usate: 'categoria-macchina/usate/', 'settore-vino': 'settori/vino/', 'mec-ld': 'macchine/mec-ld/', squadron: 'squadron/', settori: 'settori/',
  servizi: 'servizi/', 'chi-siamo': 'chi-siamo/', referenze: 'referenze/', contatti: 'contatti/', conferma: 'conferma/', 404: 'pagina-che-non-esiste-test/',
  cataloghi: 'cataloghi/', 'lavora-con-noi': 'lavora-con-noi/', posizione: 'lavora-con-noi/area-manager/', news: 'news/',
  articolo: 'eurostar-cosmachine-portogallo/', 'caso-studio': 'castello-di-verrazzano-riempitrice-maxima/', ricerca: '?s=vino', 'home-en': 'en/',
};

function info() {
  let e = document.activeElement;
  if (!e || e === document.body) return null;
  // Dentro uno shadow DOM (banner cookie) activeElement è il contenitore: scende fino all'elemento vero.
  while (e.shadowRoot && e.shadowRoot.activeElement) e = e.shadowRoot.activeElement;
  window.__visti = window.__visti || new Set();
  const giaVisto = window.__visti.has(e);
  window.__visti.add(e);
  const r = e.getBoundingClientRect();
  const cs = getComputedStyle(e);
  let hidden = cs.visibility === 'hidden';
  for (let a = e; a; a = a.parentElement) { const c = getComputedStyle(a); if (+c.opacity === 0 || c.display === 'none') hidden = true; }
  const fuori = r.bottom < 0 || r.top > innerHeight || r.right < 0 || r.left > innerWidth;
  const outline = cs.outlineStyle !== 'none' && parseFloat(cs.outlineWidth) > 0;
  const ombra = cs.boxShadow && cs.boxShadow !== 'none';
  const nome = (e.getAttribute('aria-label') || e.innerText || e.value || e.name || e.id || '').replace(/\s+/g, ' ').trim().slice(0, 45);
  const sel = e.tagName.toLowerCase() + (e.id ? '#' + e.id : '') + (typeof e.className === 'string' && e.className ? '.' + e.className.trim().split(/\s+/).filter((c) => !/^elementor-element-|^e-con$|^e-atomic|^e-\w+-base$|^e-default/.test(c)).slice(0, 3).join('.') : '');
  return { giaVisto, ombraDom: e.getRootNode() !== document, sel, nome, w: Math.round(r.width), h: Math.round(r.height), invisibile: hidden || r.width < 3 || r.height < 3 || fuori, focus: outline || ombra, y: Math.round(r.top + scrollY) };
}

(async () => {
  const larghezze = (process.argv[2] || '1440,375').split(',').map(Number);
  const filtro = process.argv[3] ? process.argv[3].split(',') : null;
  const browser = await chromium.launch({ executablePath: process.env.CHROME });
  const out = {};
  for (const w of larghezze) {
    const ctx = await browser.newContext({ viewport: { width: w, height: w < 768 ? 812 : 900 }, reducedMotion: 'reduce' });
    const page = await ctx.newPage();
    for (const [nome, url] of Object.entries(PAGINE)) {
      if (filtro && !filtro.includes(nome)) continue;
      await page.goto(ST + url, { waitUntil: 'networkidle', timeout: 60000 });
      await page.evaluate(() => document.fonts.ready);
      // Banner cookie: chiuso rifiutando i non essenziali, così non entra nella sequenza di ogni pagina.
      const rifiuta = page.getByRole('button', { name: /^(Rifiuta|Reject|Decline)/i }).first();
      if (await rifiuta.waitFor({ state: 'visible', timeout: 4000 }).then(() => true, () => false)) { await rifiuta.click(); await page.waitForTimeout(500); await page.reload({ waitUntil: 'networkidle' }); }
      await page.evaluate(() => { document.activeElement && document.activeElement.blur(); window.scrollTo(0, 0); window.__visti = new Set(); });
      const seq = [];
      let primo = null;
      for (let i = 0; i < 400; i++) {
        await page.keyboard.press('Tab');
        const f = await page.evaluate(info);
        if (!f) { if (seq.length) break; else continue; }
        if (f.giaVisto) break;
        if (!primo) primo = f;
        seq.push(f);
      }
      const invisibili = seq.filter((f) => f.invisibile);
      const senzaFocus = seq.filter((f) => !f.invisibile && !f.focus);
      out[nome + '@' + w] = { n: seq.length, primo: seq[0], invisibili, senzaFocus };
      console.log(`${nome} ${w}: ${seq.length} tab, primo "${seq[0] && seq[0].nome}", invisibili ${invisibili.length}, senza focus visibile ${senzaFocus.length}`);
    }
    await ctx.close();
  }
  await browser.close();
  fs.writeFileSync(path.join(__dirname, 'tastiera-' + larghezze.join('-') + (filtro ? '-' + filtro.join('-') : '') + '.json'), JSON.stringify(out, null, 1));
})().catch((e) => { console.error(e); process.exit(1); });
