const { chromium } = require(process.env.PW);
const [, , u, w, rm] = process.argv;
(async () => { const b = await chromium.launch({ executablePath: process.env.CHROME });
 const ctx = await b.newContext({ reducedMotion: rm || 'no-preference', viewport:{width:+w,height:812} }); const p = await ctx.newPage();
 await p.goto(u,{waitUntil:'networkidle'}); await p.evaluate(()=>document.fonts.ready); await p.waitForTimeout(800);
 console.log(rm, await p.evaluate(()=>{const W=innerWidth;const out=[];for(const e of document.querySelectorAll('body *')){const r=e.getBoundingClientRect();if(r.right>W+1&&r.width>0){let clipped=false;for(let a=e.parentElement;a;a=a.parentElement){const c=getComputedStyle(a);if(/(hidden|clip|auto|scroll)/.test(c.overflowX)&&a.getBoundingClientRect().right<=W+1){clipped=true;break}}if(!clipped)out.push(e.tagName+'.'+String(e.className).slice(0,50)+' right '+r.right.toFixed(0))}}return {sw:document.documentElement.scrollWidth,iw:W,over:out.slice(0,8)}}));
 await b.close(); })();
