/* Thrive & Flow — studio front-end behaviours (no framework, no build step) */
(function () {
  'use strict';

  var rm = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------------------------------------------------------- flash toast */
  var flash = document.querySelector('.flash');
  if (flash) {
    setTimeout(function () { flash.classList.add('gone'); }, 4600);
    flash.addEventListener('click', function () { flash.classList.add('gone'); });
  }

  /* ------------------------------------------------- accordions (wk, faq) */
  document.addEventListener('click', function (e) {
    var head = e.target.closest('.wk-head');
    if (head) {
      var wk = head.parentElement;
      wk.classList.toggle('open');
      head.setAttribute('aria-expanded', wk.classList.contains('open'));
    }
    var q = e.target.closest('.faq-q');
    if (q) q.parentElement.classList.toggle('open');
  });

  /* -------------------------------------------------- program filter bar */
  var grid = document.getElementById('pgrid');
  var fcount = document.getElementById('fcount');
  if (grid) {
    var state = { stage: grid.dataset.initialStage || 'all', focus: 'all' };
    document.querySelectorAll('.fchip').forEach(function (chip) {
      chip.addEventListener('click', function () {
        var group = chip.getAttribute('data-f');
        state[group] = chip.getAttribute('data-v');
        document.querySelectorAll('.fchip[data-f="' + group + '"]').forEach(function (c) {
          c.classList.toggle('on', c === chip);
          c.setAttribute('aria-pressed', String(c === chip));
        });
        var shown = 0;
        grid.querySelectorAll('[data-stage]').forEach(function (card) {
          var okStage = state.stage === 'all' || (card.getAttribute('data-stage') || '').indexOf(state.stage) > -1;
          var okKind = state.focus === 'all' || (card.getAttribute('data-kind') || '') === state.focus;
          var show = okStage && okKind;
          card.hidden = !show;
          if (show) shown++;
        });
        if (fcount) fcount.textContent = shown + (shown === 1 ? ' collection' : ' collections');
      });
    });
  }

  /* ------------------------------------------------------ pricing toggle */
  var mprice = document.getElementById('mprice');
  if (mprice) {
    var mper = document.getElementById('mper');
    var mform = document.getElementById('mform');
    document.querySelectorAll('[data-bill]').forEach(function (b) {
      b.addEventListener('click', function () {
        document.querySelectorAll('[data-bill]').forEach(function (x) { x.classList.toggle('on', x === b); });
        var yearly = b.getAttribute('data-bill') === 'y';
        mprice.textContent = yearly ? mprice.getAttribute('data-yearly') : mprice.getAttribute('data-monthly');
        mper.textContent = yearly ? mper.getAttribute('data-yearly') : mper.getAttribute('data-monthly');
        if (mform) mform.setAttribute('action', yearly ? mform.getAttribute('data-yearly') : mform.getAttribute('data-monthly'));
      });
    });
  }

  /* ------------------------------------------------------- player tabs */
  document.querySelectorAll('.vtab').forEach(function (tab) {
    tab.addEventListener('click', function () {
      document.querySelectorAll('.vtab').forEach(function (t) { t.classList.toggle('on', t === tab); });
      document.querySelectorAll('.vpane').forEach(function (p) {
        p.classList.toggle('on', p.id === 'vt-' + tab.getAttribute('data-vt'));
      });
    });
  });

  /* -------------------------------------------- admin: signups bar chart */
  var barsEl = document.getElementById('barsvg');
  var barsData = document.getElementById('bars-data');
  if (barsEl && barsData) {
    var data;
    try { data = JSON.parse(barsData.textContent); } catch (err) { data = []; }
    var W = 560, H = 210, mL = 34, mR = 12, mT = 16, mB = 30;
    var maxVal = Math.max(4, Math.max.apply(null, data.map(function (d) { return d.value; })));
    var top = Math.ceil(maxVal / 4) * 4;
    var iw = W - mL - mR, ih = H - mT - mB;
    var step = iw / Math.max(1, data.length);
    var bw = Math.min(38, step - 14);
    var NS = 'http://www.w3.org/2000/svg';
    var tip = document.getElementById('ctip');

    function mk(tag, attrs) {
      var el = document.createElementNS(NS, tag);
      for (var k in attrs) el.setAttribute(k, attrs[k]);
      return el;
    }

    [0, 0.25, 0.5, 0.75, 1].forEach(function (f) {
      var v = Math.round(top * f);
      var y = mT + ih - f * ih;
      barsEl.appendChild(mk('line', { x1: mL, x2: W - mR, y1: y, y2: y, stroke: '#EFE9E6', 'stroke-width': 1 }));
      var label = mk('text', { x: mL - 8, y: y + 3.5, 'text-anchor': 'end', 'font-size': 10, fill: '#98A0A8', 'font-family': 'Poppins,system-ui,sans-serif' });
      label.textContent = v;
      barsEl.appendChild(label);
    });

    data.forEach(function (d, i) {
      var x = mL + step * i + (step - bw) / 2;
      var h = top ? (d.value / top) * ih : 0;
      var y = mT + ih - h;
      var r = Math.min(4, bw / 2, h);
      var cx = mL + step * i + step / 2;

      if (d.value > 0) {
        barsEl.appendChild(mk('path', {
          d: 'M' + x + ' ' + (mT + ih) + 'V' + (y + r) + 'Q' + x + ' ' + y + ' ' + (x + r) + ' ' + y +
             'H' + (x + bw - r) + 'Q' + (x + bw) + ' ' + y + ' ' + (x + bw) + ' ' + (y + r) + 'V' + (mT + ih) + 'Z',
          fill: '#C5604F'
        }));
      }

      var xl = mk('text', { x: cx, y: H - 10, 'text-anchor': 'middle', 'font-size': 9.5, fill: '#98A0A8', 'font-family': 'Poppins,system-ui,sans-serif' });
      xl.textContent = d.label;
      barsEl.appendChild(xl);

      if (i === data.length - 1) {
        var vl = mk('text', { x: cx, y: y - 7, 'text-anchor': 'middle', 'font-size': 12, 'font-weight': 600, fill: '#2B333C', 'font-family': 'Poppins,system-ui,sans-serif' });
        vl.textContent = d.value;
        barsEl.appendChild(vl);
      }

      var hit = mk('rect', { x: mL + step * i, y: mT, width: step, height: ih, fill: 'transparent' });
      hit.addEventListener('mousemove', function () {
        if (!tip) return;
        var rect = barsEl.getBoundingClientRect();
        tip.innerHTML = 'Week of ' + d.label + ' · <b>' + d.value + '</b> new member' + (d.value === 1 ? '' : 's');
        tip.style.left = (cx / W) * rect.width + 'px';
        tip.style.top = (y / H) * rect.height + 'px';
        tip.hidden = false;
      });
      hit.addEventListener('mouseleave', function () { if (tip) tip.hidden = true; });
      barsEl.appendChild(hit);
    });
  }

  /* ------------------------------------------------ admin: KPI count-up */
  document.querySelectorAll('[data-count]').forEach(function (el) {
    var raw = el.getAttribute('data-count').replace(/[^\d]/g, '');
    var end = parseInt(raw, 10) || 0;
    var format = function (n) { return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' '); };
    if (rm || end === 0) { el.textContent = format(end); return; }
    var t0 = null;
    function stepFn(ts) {
      if (!t0) t0 = ts;
      var p = Math.min((ts - t0) / 800, 1);
      p = 1 - Math.pow(1 - p, 3);
      el.textContent = format(Math.round(end * p));
      if (p < 1) requestAnimationFrame(stepFn);
    }
    requestAnimationFrame(stepFn);
  });
})();
