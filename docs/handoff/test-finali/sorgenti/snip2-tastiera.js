  // Tastiera nel mega-menu (test finali 30/09): il pannello sta dopo la nav, fuori dall'ordine del Tab. Con il pannello aperto
  // Tab dal pulsante entra nella prima voce, Tab dall'ultima voce chiude ed esce verso l'elemento dopo il pulsante,
  // Maiusc+Tab dalla prima voce torna al pulsante.
  function focusables(root){ return [].slice.call((root || document).querySelectorAll('a[href],button:not([disabled]),input:not([type=hidden]):not([disabled]),select,textarea,[tabindex]:not([tabindex="-1"])')).filter(function(el){ return el.offsetWidth || el.offsetHeight || el.getClientRects().length; }); }
  document.addEventListener('keydown', function(e){
    if (e.key !== 'Tab') return;
    var a = document.activeElement;
    if (a && a.classList && a.classList.contains('es-mega-caret') && a.getAttribute('aria-expanded') === 'true' && !e.shiftKey) {
      var f = focusables(document.getElementById(a.getAttribute('aria-controls'))); if (f.length) { e.preventDefault(); f[0].focus(); } return;
    }
    var pn = a && a.closest && a.closest('.es-mega.is-open'); if (!pn) return;
    var items = focusables(pn), caret = document.querySelector('.es-mega-caret[aria-controls="' + pn.id + '"]');
    if (e.shiftKey && a === items[0] && caret) { e.preventDefault(); caret.focus(); return; }
    if (!e.shiftKey && a === items[items.length - 1] && caret) { e.preventDefault(); var all = focusables(document).filter(function(el){ return !pn.contains(el); }); var next = all[all.indexOf(caret) + 1]; closeMega(pn, false); if (next) next.focus(); }
  });
  // Quando il focus esce: si chiudono il mega-menu (fuori da pannello e pulsante) e il menu mobile aperto (fuori dall'header),
  // così il focus non finisce su contenuti coperti dal pannello.
  document.addEventListener('focusin', function(e){
    document.querySelectorAll('.es-mega.is-open').forEach(function(pn){ var c = document.querySelector('.es-mega-caret[aria-controls="' + pn.id + '"]'); if (!pn.contains(e.target) && e.target !== c) closeMega(pn, false); });
    var nl = document.getElementById('es-nav-links'), bt = document.getElementById('es-nav-toggle');
    if (nl && bt && nl.classList.contains('is-open') && !(hdr && hdr.contains(e.target)) && !nl.contains(e.target)) { nl.classList.remove('is-open'); bt.setAttribute('aria-expanded','false'); bt.setAttribute('aria-label', bt.dataset.labelOpen); }
  });
