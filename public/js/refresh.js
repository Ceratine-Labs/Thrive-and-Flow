/* Public-site navigation and optional illustrated studio story. */
(function () {
  'use strict';
  var menu = document.querySelector('.nav-menu');
  var nav = document.getElementById('public-navigation');
  if (menu && nav) {
    function closeMenu() {
      nav.dataset.open = 'false'; menu.setAttribute('aria-expanded', 'false'); menu.setAttribute('aria-label', 'Open navigation');
    }
    menu.addEventListener('click', function () {
      var open = menu.getAttribute('aria-expanded') !== 'true';
      nav.dataset.open = String(open); menu.setAttribute('aria-expanded', String(open)); menu.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
    });
    nav.addEventListener('click', function (event) { if (event.target.closest('a')) closeMenu(); });
    document.addEventListener('keydown', function (event) { if (event.key === 'Escape' && menu.getAttribute('aria-expanded') === 'true') { closeMenu(); menu.focus(); } });
    document.addEventListener('click', function (event) { if (!event.target.closest('.site-nav')) closeMenu(); });
  }
  var scene = document.querySelector('[data-scene]');
  if (!scene) return;
  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
  var slides = [
    { title: 'Arrive just as you are.', copy: 'Roll out your mat. Find a comfortable seat. Take a moment to settle.', note: 'Let your shoulders soften.', small: 'There’s no rush here.', chapter: '01 / SETTLE IN', alt: 'An illustrated, light-filled prenatal studio. A mother sits comfortably on her mat.' },
    { title: 'Find a little more space.', copy: 'Explore a gentle movement and get to know the area it focuses on.', note: 'Curious about this movement?', small: 'Explore the step-by-step guide below.', chapter: '02 / FIND SPACE', alt: 'An illustrated pregnant woman explores a gentle seated side reach in a calm studio.' },
    { title: 'Let your day set the pace.', copy: 'A class, a stretch, or simply a pause. There’s more than one way to show up.', note: 'Your pace is a good pace.', small: 'Choose what feels right for today.', chapter: '03 / MAKE IT YOURS', alt: 'An illustrated pregnant woman uses support for a standing calf stretch.' }
  ];
  var index = 0, timer = null, paused = true;
  var toggle = scene.querySelector('[data-scene-pause]');
  var buttons = Array.from(scene.querySelectorAll('[data-scene-select]'));
  function show(next) {
    index = next; var data = slides[index]; scene.dataset.activeScene = String(index);
    scene.querySelector('[data-scene-title]').textContent = data.title;
    scene.querySelector('[data-scene-copy]').textContent = data.copy;
    var note = scene.querySelector('[data-scene-note]>span:last-child');
    note.replaceChildren(document.createTextNode(data.note));
    var small = document.createElement('small'); small.textContent = data.small; note.appendChild(small);
    scene.querySelector('.tf-scene-chapter').textContent = data.chapter;
    scene.querySelector('.tf-scene-art').setAttribute('aria-label', data.alt);
    buttons.forEach(function (button, i) { button.setAttribute('aria-pressed', String(i === index)); });
  }
  function updateTimer() { clearInterval(timer); timer = null; if (!paused && !document.hidden) timer = setInterval(function () { show((index + 1) % slides.length); }, 8500); }
  function setPaused(value) {
    paused = value; scene.dataset.paused = String(paused); toggle.setAttribute('aria-pressed', String(paused));
    toggle.textContent = paused ? 'Play scene ▷' : 'Pause scene Ⅱ'; updateTimer();
  }
  toggle.addEventListener('click', function () { setPaused(!paused); });
  buttons.forEach(function (button, i) { button.addEventListener('click', function () { show(i); updateTimer(); }); });
  reduced.addEventListener('change', function (event) { if (event.matches) setPaused(true); });
  document.addEventListener('visibilitychange', updateTimer);
  // Ambient storytelling can run by default; motion-sensitive visitors get a still scene.
  setPaused(reduced.matches);
})();
