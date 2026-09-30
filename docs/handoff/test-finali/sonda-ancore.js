const { chromium } = require(process.env.PW);
(async () => { const b = await chromium.launch({ executablePath: process.env.CHROME });
 for (const [w, h] of [[1440, 900], [375, 812], [640, 450]]) { const ctx = await b.newContext({ viewport: { width: w, height: h }, reducedMotion: 'reduce' }); const p = await ctx.newPage();
  for (const [u, sel] of [['macchine/olympia-sa/', '#olympia-sa'], ['macchine/athena/', '#athena'], ['?s=vino#es-sr-termini', '#es-sr-termini']]) {
   await p.goto('https://eurostar.demoengagemint.it/' + u, { waitUntil: 'networkidle' }); await p.waitForTimeout(1200);
   const r = await p.evaluate((sel) => { const e = document.querySelector(sel); const card = e.closest('.es-sq-item,.es-sq-card') || e; const h = document.querySelector('.elementor-location-header').getBoundingClientRect().bottom; return { url: location.pathname + location.hash, gap: Math.round(card.getBoundingClientRect().top - h), pad: document.documentElement.style.scrollPaddingTop }; }, sel);
   console.log(w, JSON.stringify(r)); }
  await ctx.close(); }
 await b.close(); })();
