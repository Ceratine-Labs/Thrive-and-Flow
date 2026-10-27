(() => {
  'use strict';

  const studio = document.querySelector('[data-movement-studio]');
  if (!studio) return;
  const find = (name) => studio.querySelector(`[data-ms-${name}]`);
  let movements;
  try { movements = JSON.parse(find('data').textContent); } catch { return; }

  const keys = Object.keys(movements);
  const choices = [...studio.querySelectorAll('[data-ms-choice]')];
  const stepButtons = [...studio.querySelectorAll('[data-ms-step]')];
  const pose = find('pose');
  const progress = find('progress');
  const play = find('play');
  const dialog = find('dialog');
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  const duration = 14000;
  let selected = keys[0];
  let position = 0;
  let playing = false;
  let frame = null;
  let lastTimestamp = null;
  let lastPhase = -1;
  let dialogTrigger = null;
  let artworkReady = false;

  const drawArtwork = window.MovementArtwork?.(pose, choices, () => render());

  function phaseAt(value) { return value < .25 ? 0 : value < .72 ? 1 : 2; }

  function render() {
    const movement = movements[selected];
    // A complete, gently eased excursion: setup → movement → return. No autoplay or looping.
    const amount = position < .12 || position > .93 ? 0 : Math.pow(Math.sin(Math.PI * (position - .12) / .81), 2);
    artworkReady = Boolean(drawArtwork?.(selected, amount));
    find('focus-key').classList.toggle('has-focus', artworkReady);
    find('focus-key-label').textContent = artworkReady ? 'Red shows movement focus' : 'Illustrated movement guide';
    play.disabled = !artworkReady;
    find('replay').disabled = !artworkReady;
    progress.disabled = !artworkReady;
    if (!playing) find('state').textContent = !artworkReady ? 'Illustration' : position >= 1 ? 'Complete' : 'Paused';
    if (!artworkReady) {
      const image = document.createElementNS('http://www.w3.org/2000/svg', 'image');
      image.setAttribute('href', choices.find(button => button.dataset.msChoice === selected).querySelector('img').src);
      image.setAttribute('width', '500');
      image.setAttribute('height', '390');
      pose.replaceChildren(image);
    }
    progress.value = Math.round(position * 1000);
    progress.style.setProperty('--ms-progress', `${position * 100}%`);
    const phase = phaseAt(position);
    progress.setAttribute('aria-valuetext', `${Math.round(position * 100)} percent · ${movement.steps[phase].title}`);
    if (phase !== lastPhase) {
      stepButtons.forEach((button, index) => {
        button.closest('li').classList.toggle('is-current', phase === index);
        if (phase === index) button.setAttribute('aria-current', 'step');
        else button.removeAttribute('aria-current');
      });
      lastPhase = phase;
    }
    const description = studio.querySelector('#ms-pose-description');
    description.textContent = `${movement.title}, ${movement.view.toLowerCase()}. ${movement.steps[phase].title}. ${movement.steps[phase].text} Movement focus: ${movement.focus}.${artworkReady ? ' Red shading marks this area.' : ''}`;
  }

  function setPlaying(next) {
    next = next && artworkReady;
    playing = next;
    if (frame !== null) cancelAnimationFrame(frame);
    frame = null;
    lastTimestamp = null;
    find('play-label').textContent = next ? 'Pause demo' : position >= 1 ? 'Play again' : 'Play demo';
    find('play-icon').textContent = next ? 'Ⅱ' : '▶';
    play.setAttribute('aria-label', next ? 'Pause movement demonstration' : 'Play movement demonstration');
    find('state').textContent = !artworkReady ? 'Illustration' : next ? 'Playing' : position >= 1 ? 'Complete' : 'Paused';
    if (next) frame = requestAnimationFrame(tick);
  }

  function tick(timestamp) {
    if (!playing) return;
    if (lastTimestamp !== null) position = Math.min(1, position + Math.min(timestamp - lastTimestamp, 100) / duration);
    lastTimestamp = timestamp;
    render();
    if (position >= 1) {
      setPlaying(false);
      announce(`${movements[selected].title} demonstration complete. Replay or choose another movement.`);
    } else frame = requestAnimationFrame(tick);
  }

  function announce(message) { find('announcement').textContent = message; }

  function updateMap(movement) {
    const highlight = find('highlight');
    if (movement.map === 'side') {
      highlight.innerHTML = '<path d="M75 60Q80 77 77 98" fill="none" stroke="#cb7a68" stroke-width="8" stroke-linecap="round"/><path d="M82 78H111" stroke="#cb7a68" stroke-width="1.5"/><circle cx="111" cy="78" r="3" fill="#cb7a68"/>';
    } else if (movement.map === 'back') {
      highlight.innerHTML = '<path d="M51 60Q46 78 50 104M69 60Q74 78 70 104" fill="none" stroke="#cb7a68" stroke-width="7" stroke-linecap="round"/><path d="M77 84H111" stroke="#cb7a68" stroke-width="1.5"/><circle cx="111" cy="84" r="3" fill="#cb7a68"/>';
    } else {
      highlight.innerHTML = '<path d="M43 148L41 165" fill="none" stroke="#cb7a68" stroke-width="9" stroke-linecap="round"/><path d="M48 156H105" stroke="#cb7a68" stroke-width="1.5"/><circle cx="105" cy="156" r="3" fill="#cb7a68"/>';
    }
    studio.querySelector('#ms-map-title').textContent = `Body focus: ${movement.focus}`;
    studio.querySelector('#ms-map-description').textContent = `Simplified ${movement.map === 'side' ? 'front' : 'back'} body map. Coral highlights ${movement.focus.toLowerCase()}. ${movement.focus_detail}`;
    find('focus').textContent = movement.focus;
    find('focus-detail').textContent = movement.focus_detail;
    find('map-view').textContent = movement.map_view;
  }

  function select(key) {
    if (!Object.hasOwn(movements, key)) return;
    setPlaying(false);
    selected = key;
    position = 0;
    lastPhase = -1;
    const movement = movements[key];
    choices.forEach((button) => {
      const active = button.dataset.msChoice === key;
      button.classList.toggle('is-selected', active);
      button.setAttribute('aria-pressed', String(active));
    });
    for (const field of ['title', 'category', 'equipment', 'view']) find(field).textContent = movement[field];
    find('number').textContent = String(keys.indexOf(key) + 1).padStart(2, '0');
    studio.querySelector('#ms-pose-title').textContent = `${movement.title} demonstration`;
    stepButtons.forEach((button, index) => {
      button.querySelector('strong').textContent = movement.steps[index].title;
      button.querySelector('span > span').textContent = movement.steps[index].text;
    });
    updateMap(movement);
    render();
    setPlaying(false);
  }

  choices.forEach((button) => {
    button.disabled = false;
    button.addEventListener('click', () => {
      select(button.dataset.msChoice);
      announce(`${movements[selected].title} selected. Demonstration paused at the starting position.`);
    });
  });

  stepButtons.forEach((button, index) => {
    button.disabled = false;
    button.addEventListener('click', () => {
      position = [0, .525, 1][index];
      setPlaying(false);
      render();
      announce(`Step ${index + 1}: ${movements[selected].steps[index].title}.`);
    });
  });

  play.addEventListener('click', () => {
    if (position >= 1) position = 0;
    setPlaying(!playing);
  });
  find('replay').addEventListener('click', () => {
    position = 0;
    render();
    // An explicit replay starts a single cycle; reduced-motion users keep direct control.
    setPlaying(!reducedMotion.matches);
    if (reducedMotion.matches) announce('Demo reset. Use the steps or slider to explore, or press play to request motion.');
  });
  progress.addEventListener('input', () => {
    position = Number(progress.value) / 1000;
    setPlaying(false);
    render();
  });

  function updateMotionPreference() {
    find('motion-note').hidden = !reducedMotion.matches;
    if (reducedMotion.matches) setPlaying(false);
  }
  if (reducedMotion.addEventListener) reducedMotion.addEventListener('change', updateMotionPreference);
  document.addEventListener('visibilitychange', () => { if (document.hidden) setPlaying(false); });

  function openGuide(event) {
    setPlaying(false);
    const movement = movements[selected];
    for (const field of ['title', 'category', 'equipment', 'adaptation']) find(`dialog-${field}`).textContent = movement[field];
    find('dialog-image').src = choices.find((button) => button.dataset.msChoice === selected).querySelector('img').src;
    find('dialog-image').alt = `Illustration of ${movement.title.toLowerCase()}`;
    find('dialog-focus').textContent = movement.focus;
    find('dialog-focus-detail').textContent = movement.focus_detail;
    find('dialog-focus').closest('.ms-dialog-focus').querySelector('.ms-focus-label').textContent = `Body focus · ${movement.map_view}`;
    const map = find('body-map').cloneNode(true);
    map.removeAttribute('role');
    map.removeAttribute('aria-labelledby');
    map.querySelectorAll('title, desc').forEach((element) => element.remove());
    find('dialog-map').replaceChildren(map);
    find('dialog-source').href = movement.source;
    const list = find('dialog-steps');
    list.replaceChildren();
    movement.steps.forEach((step) => {
      const item = document.createElement('li');
      const title = document.createElement('strong');
      title.textContent = step.title;
      item.append(title, document.createTextNode(step.text));
      list.append(item);
    });
    dialogTrigger = event.currentTarget;
    dialog.showModal();
    find('close').focus();
  }

  if (typeof dialog.showModal === 'function') {
    find('open').hidden = false;
    find('open').addEventListener('click', openGuide);
    find('close').addEventListener('click', () => dialog.close());
    dialog.addEventListener('close', () => { if (dialogTrigger) dialogTrigger.focus(); });
    dialog.addEventListener('click', (event) => {
      if (event.target !== dialog) return;
      const box = dialog.getBoundingClientRect();
      if (event.clientX < box.left || event.clientX > box.right || event.clientY < box.top || event.clientY > box.bottom) dialog.close();
    });
  }

  find('controls').hidden = false;
  const requestedMovement = new URLSearchParams(window.location.search).get('movement');
  select(Object.hasOwn(movements, requestedMovement) ? requestedMovement : selected);
  updateMotionPreference();
})();
