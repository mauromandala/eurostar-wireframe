// node sonda-testo.js <url-wf> <url-st> <larghezza> "<testo>" [livelli] — antenati del primo elemento con quel testo, in entrambe le pagine.
const { chromium } = require(process.env.PW);
const [, , uw, us, w, txt, lv] = process.argv;
(async () => {
  const b = await chromium.launch({ executablePath: process.env.CHROME });
  const ctx = await b.newContext({ reducedMotion: 'reduce' });
  for (const u of [uw, us]) {
    const p = await ctx.newPage(); await p.setViewportSize({ width: +w, height: 900 });
    await p.goto(u, { waitUntil: 'networkidle' }); await p.evaluate(() => document.fonts.ready);
    const r = await p.evaluate(([txt, lv]) => {
      const main = document.querySelector('main#content') || document.querySelector('main'); const mr = main.getBoundingClientRect();
      const tw = document.createTreeWalker(main, NodeFilter.SHOW_TEXT); let n, el;
      while ((n = tw.nextNode())) if (n.textContent.trim().toLowerCase().startsWith(txt.toLowerCase())) { el = n.parentElement; break; }
      const out = [];
      for (let i = 0; el && i < +lv; i++, el = el.parentElement) { const b = el.getBoundingClientRect(); const c = getComputedStyle(el);
        out.push(`${el.tagName}.${String(el.className).replace(/elementor-element-\S+|e-con|e-atomic-element|elementor-element/g, '').trim().slice(0, 50)} x${(b.x - mr.x).toFixed(1)} y${(b.y - mr.y).toFixed(1)} ${b.width.toFixed(1)}×${b.height.toFixed(1)} | ${c.display} gtc:${c.gridTemplateColumns} gap:${c.gap} pad:${c.padding} m:${c.margin} fs:${c.fontSize}/${c.lineHeight} w:${c.width} bs:${c.boxSizing} bw:${c.borderWidth}`); }
      return out;
    }, [txt, lv || 5]);
    console.log('--', u); r.forEach((l) => console.log('  ' + l));
  }
  await b.close();
})();
