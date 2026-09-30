const { chromium } = require(process.env.PW);
const [, , uw, us, w] = process.argv;
(async () => { const b = await chromium.launch({ executablePath: process.env.CHROME });
 for (const u of [uw, us]) { const p = await b.newPage(); await p.setViewportSize({width:+w,height:900}); await p.goto(u,{waitUntil:'networkidle'}); await p.evaluate(()=>document.fonts.ready);
  console.log('--',u); console.log(await p.evaluate(()=>{const main=document.querySelector('main#content')||document.querySelector('main');const y0=main.getBoundingClientRect().y;
   const rows=[...main.querySelectorAll('section, main > *, form, footer')].slice(0,30).map(e=>{const r=e.getBoundingClientRect();return `${e.tagName}#${e.id}.${String(e.className).replace(/elementor-element-\S+|e-con|e-atomic-element|elementor-element|e-\w+-base|e-default-\w+/g,'').trim().slice(0,30)} y${(r.y-y0).toFixed(1)} h${r.height.toFixed(1)}`});
   const f=document.querySelector('footer, .elementor-location-footer');rows.push('FOOTER y'+(f.getBoundingClientRect().y-y0).toFixed(1));return rows.join('\n')})); }
 await b.close(); })();
