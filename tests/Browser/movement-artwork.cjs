// Run with the local review server: node tests/Browser/movement-artwork.cjs
// Uses an existing Playwright installation; no build or server mutation.
const { chromium } = require('playwright');
const assert = require('node:assert/strict');
const path = require('node:path');
const fs = require('node:fs');
const url = process.env.MOVEMENT_PREVIEW_URL || 'http://127.0.0.1:8037/';
const output = process.env.MOVEMENT_SCREENSHOTS;
const keys = ['side-reach', 'cat-neutral', 'calf-stretch'];
const scrub = (page, value) => page.locator('[data-ms-progress]').evaluate((el, v) => {
  el.value = v; el.dispatchEvent(new Event('input', { bubbles: true }));
}, value);
const signature = (page, rect) => page.locator('canvas').evaluate((canvas, r) => {
  const pixels = canvas.getContext('2d').getImageData(...r).data;
  let hash = 2166136261, visible = 0;
  for (let i = 0; i < pixels.length; i++) { hash = Math.imul(hash ^ pixels[i], 16777619); if (i % 4 === 3 && pixels[i] > 128) visible++; }
  return { hash: hash >>> 0, visible };
}, rect);
const checkFocusAndChin = async (page, key, value) => {
  const result = await page.locator('canvas').evaluate((canvas, { key, value }) => {
    const ctx = canvas.getContext('2d');
    const area = key === 'side-reach' ? [370, 235, 130, 155] : key === 'cat-neutral' ? [495, 255, 170, 125] : [300, 525, 100, 120];
    const pixels = ctx.getImageData(0, 0, 1000, 780).data;
    let red = 0, inArea = 0, visible = 0;
    for (let i = 0; i < pixels.length; i += 4) {
      const [r, g, b, alpha] = pixels.subarray(i, i + 4);
      if (alpha < 180) continue;
      visible++;
      if (r > 100 && b > 75 && r > g * 1.65 && g < 150 && Math.abs(g - b) < 25) {
        red++;
        const x = (i / 4) % 1000, y = Math.floor(i / 4000);
        if (x >= area[0] && x < area[0] + area[2] && y >= area[1] && y < area[1] + area[3]) inArea++;
      }
    }
    const chin = [];
    if (key === 'cat-neutral') {
      const position = value / 1000;
      const amount = position < .12 || position > .93 ? 0 : Math.sin(Math.PI * (position - .12) / .81) ** 2;
      const angle = -.12 * (1 - amount);
      // Opaque skin landmarks in the original image, previously cut off by
      // the head mask. Project them into the stage's known source placement.
      for (const [x, y] of [[1220,345],[1230,355],[1225,360]]) {
        const px = 1190 + (x-1190)*Math.cos(angle) - (y-270)*Math.sin(angle);
        const py = 270 + (x-1190)*Math.sin(angle) + (y-270)*Math.cos(angle);
        chin.push(Array.from(ctx.getImageData(Math.round(96 + px*.53125), Math.round(148 + py*.53125), 1, 1).data));
      }
    }
    return { red, inArea, visible, chin };
  }, { key, value });
  assert(result.red > 300, `${key}: focus visibly red`);
  assert(result.inArea / result.red > .9, `${key}: red confined to the documented focus region (${result.inArea}/${result.red})`);
  assert(result.red / result.visible < .15, `${key}: illustration detail remains visible`);
  for (const [r, g, b, alpha] of result.chin) {
    assert(alpha > 220 && r > 155 && g > 85 && b > 45, 'Cat: original chin skin is present, with no mask cutout');
  }
  assert.match(await page.locator('#ms-pose-description').textContent(), /Movement focus:.*Red shading marks this area/);
};
(async () => {
  if (output) fs.mkdirSync(output, { recursive: true });
  const browser = await chromium.launch({ headless: true });
  try {
    const page = await browser.newPage({ viewport: { width: 1400, height: 1000 } });
    const errors = [];
    page.on('pageerror', error => errors.push(error.message));
    await page.goto(url + '?movement=cat-neutral#movement');
    await page.locator('canvas').waitFor();
    assert.equal(await page.locator('[data-ms-choice="cat-neutral"]').getAttribute('aria-pressed'), 'true');
    assert.equal(await page.locator('[data-ms-state]').textContent(), 'Paused');
    for (const [device, width] of [['desktop', 1400], ['mobile', 390]]) {
      await page.setViewportSize({ width, height: 1000 });
      for (const key of keys) {
        await page.locator(`[data-ms-choice="${key}"]`).click();
        await page.locator('[data-ms-play]:not([disabled])').waitFor();
        const frames = [], anchors = [];
        // Pixels containing visible feet/contact areas, not empty background.
        const contacts = key === 'side-reach' ? [[330, 650, 300, 85], [640, 470, 35, 30], [515, 410, 90, 55]] : key === 'cat-neutral' ? [[700, 575, 130, 55], [400, 550, 100, 70]] : [[285, 665, 335, 55], [695, 125, 16, 95]];
        for (const [phase, value] of [['start', 0], ['middle', 325], ['full', 525]]) {
          await scrub(page, value);
          await checkFocusAndChin(page, key, value);
          frames.push(await signature(page, [0, 0, 1000, 780]));
          anchors.push(await Promise.all(contacts.map(rect => signature(page, rect))));
          if (output) await page.locator('.ms-stage').screenshot({ path: path.join(output, `${device}-${key}-${phase}.png`) });
        }
        assert.equal(new Set(frames.map(f => f.hash)).size, 3, `${key}: three distinct poses`);
        for (let contact = 0; contact < contacts.length; contact++) {
          assert(anchors.every(frame => frame[contact].visible > 50), `${key}: contact check ${contact} includes artwork`);
          assert.equal(new Set(anchors.map(frame => frame[contact].hash)).size, 1, `${key}: contact ${contact} stays planted`);
        }
        await page.locator('[data-ms-step="0"]').click();
        assert.equal(await page.locator('[data-ms-progress]').inputValue(), '0');
        await page.locator('[data-ms-step="1"]').click();
        assert.equal(await page.locator('[data-ms-progress]').inputValue(), '525');
        assert.equal(await page.locator('[data-ms-step="1"]').getAttribute('aria-current'), 'step');
        await page.locator('[data-ms-step="2"]').click();
        assert.equal(await page.locator('[data-ms-progress]').inputValue(), '1000');
        assert.equal((await signature(page, [0, 0, 1000, 780])).hash, frames[0].hash, `${key}: returns to neutral`);
        assert(await page.locator('#ms-pose-description').textContent());
        console.log(`PASS ${device}: ${key}, three distinct poses, grounded contacts, red focus/chin, steps, neutral return`);
      }
      assert(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth), `${device}: no horizontal overflow`);
    }
    await page.setViewportSize({ width: 1400, height: 1000 });
    await page.locator('[data-ms-step="0"]').click();
    await page.locator('[data-ms-play]').click();
    await page.waitForFunction(() => Number(document.querySelector('[data-ms-progress]').value) > 25);
    await page.locator('[data-ms-play]').click();
    const paused = await page.locator('[data-ms-progress]').inputValue();
    await page.waitForTimeout(180);
    assert.equal(await page.locator('[data-ms-progress]').inputValue(), paused);
    await page.locator('[data-ms-replay]').click();
    assert.equal(await page.locator('[data-ms-state]').textContent(), 'Playing');
    await scrub(page, 990);
    await page.locator('[data-ms-play]').click();
    await page.waitForFunction(() => document.querySelector('[data-ms-state]').textContent === 'Complete');
    await page.waitForTimeout(180);
    assert.equal(await page.locator('[data-ms-progress]').inputValue(), '1000');
    await page.locator('[data-ms-play]').click();
    assert.equal(await page.locator('[data-ms-state]').textContent(), 'Playing');
    await page.emulateMedia({ reducedMotion: 'reduce' });
    await page.waitForFunction(() => document.querySelector('[data-ms-state]').textContent !== 'Playing');
    assert(await page.locator('[data-ms-motion-note]').isVisible());
    await page.locator('[data-ms-replay]').click();
    assert.equal(await page.locator('[data-ms-state]').textContent(), 'Paused');
    assert.equal(await page.locator('[data-ms-progress]').inputValue(), '0');
    await page.locator('[data-ms-play]').click();
    assert.equal(await page.locator('[data-ms-state]').textContent(), 'Playing');
    await page.locator('[data-ms-open]').click();
    assert(await page.locator('[data-ms-dialog]').isVisible());
    assert.equal(await page.locator('[data-ms-state]').textContent(), 'Paused');
    await page.keyboard.press('Escape');
    assert(await page.locator('[data-ms-open]').evaluate(el => el === document.activeElement));
    assert.deepEqual(errors, []);
    console.log('PASS play/pause/replay, scrub, completion without looping, reduced-motion change and explicit play, dialog/focus, no JS errors');
    await page.setViewportSize({ width: 390, height: 1000 });
    for (const key of keys) {
      await page.locator(`[data-ms-choice="${key}"]`).click();
      for (const [phase, value] of [['start', 0], ['middle', 325], ['full', 525]]) {
        await scrub(page, value);
        await checkFocusAndChin(page, key, value);
        assert.equal(await page.locator('[data-ms-state]').textContent(), 'Paused');
        if (output) await page.locator('.ms-stage').screenshot({ path: path.join(output, `reduced-mobile-${key}-${phase}.png`) });
      }
    }
    console.log('PASS reduced-motion mobile: red focus and chin at all nine static poses');
    const nojs = await browser.newPage({ javaScriptEnabled: false });
    await nojs.goto(url);
    assert(await nojs.locator('.ms-no-js').isVisible());
    assert.equal(await nojs.locator('.ms-no-js details').count(), 3);
    assert(await nojs.locator('[data-ms-pose] image').isVisible());
    assert.equal(await nojs.locator('[data-ms-focus-key-label]').textContent(), 'Illustrated movement guide');
    console.log('PASS no-JavaScript illustration and three written guides');
    const failed = await browser.newPage();
    await failed.route('**/movement-side-reach-rig.webp', route => route.abort());
    await failed.goto(url);
    await failed.locator('[data-ms-controls]:not([hidden])').waitFor();
    assert(await failed.locator('[data-ms-pose] image').isVisible());
    assert(await failed.locator('[data-ms-play]').isDisabled());
    assert.equal(await failed.locator('[data-ms-focus-key-label]').textContent(), 'Illustrated movement guide');
    assert(await failed.locator('[data-ms-progress]').isDisabled());
    await failed.locator('[data-ms-choice="cat-neutral"]').click();
    await failed.locator('[data-ms-play]:not([disabled])').waitFor();
    assert(await failed.locator('canvas').isVisible());
    console.log('PASS failed animation asset keeps illustration/written controls, another exercise still plays');
  } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode = 1; });
