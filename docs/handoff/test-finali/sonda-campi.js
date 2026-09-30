const { chromium } = require(process.env.PW);
const [, , uw, us, w] = process.argv;
(async () => { const b = await chromium.launch({ executablePath: process.env.CHROME });
 for (const u of [uw, us]) { const p = await b.newPage(); await p.setViewportSize({width:+w,height:900}); await p.goto(u,{waitUntil:'networkidle'}); await p.evaluate(()=>document.fonts.ready);
  console.log('--',u); console.log(await p.evaluate(()=>{const main=document.querySelector('main#content')||document.querySelector('main');const mr=main.getBoundingClientRect();
   return [...main.querySelectorAll('form label, form input:not([type=hidden]), form select, form textarea, form button')].filter(e=>e.getBoundingClientRect().width>1).map(e=>{const r=e.getBoundingClientRect();const c=getComputedStyle(e);return `${e.tagName} ${(e.textContent||e.name||e.type).trim().slice(0,28).padEnd(28)} x${(r.x-mr.x).toFixed(1)} y${(r.y-mr.y).toFixed(1)} ${r.width.toFixed(1)}×${r.height.toFixed(1)} mb:${c.marginBottom} fs:${c.fontSize} lh:${c.lineHeight}`}).join('\n')})); }
 await b.close(); })();
