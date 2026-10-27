<?php page_header('Identificador de plantas', 'Sacale una foto a cualquier planta y te decimos qué especie es.', 'identificador'); ?>
<link rel="stylesheet" href="public/css/identificador.css">
<main class="container-x py-10 ident-scope">
<main class="app" id="plant-id-app">
  <p class="lead">Sacale una foto o subí una imagen y te mostramos las especies más probables y si la tenemos en el vivero. Funciona mejor con hojas, flores o frutos bien enfocados.</p>

  <div class="pid-tabs" role="tablist" aria-label="Origen de la foto">
    <button class="pid-tab" role="tab" id="tab-upload" aria-selected="true" aria-controls="panel-upload">Subir foto</button>
    <button class="pid-tab" role="tab" id="tab-camera" aria-selected="false" aria-controls="panel-camera" tabindex="-1">Usar cámara</button>
  </div>

  <section class="pid-panel">
    <div id="panel-upload" role="tabpanel" aria-labelledby="tab-upload">
      <label class="dropzone" id="dropzone" for="file-input">
        <div>
          <strong>Elegí o arrastrá tus fotos</strong>
          <span>JPG, PNG o WebP. Hasta 5 fotos.</span>
        </div>
      </label>
      <input class="sr-only" id="file-input" type="file" accept="image/*" multiple>
    </div>

    <div id="panel-camera" role="tabpanel" aria-labelledby="tab-camera" hidden>
      <div class="camera">
        <div class="viewfinder">
          <video id="video" autoplay playsinline muted hidden></video>
          <p class="placeholder" id="cam-placeholder">La cámara está apagada.</p>
        </div>
        <div class="pid-row">
          <button class="pid-btn pid-btn-primary" id="cam-start" type="button">Encender cámara</button>
          <button class="pid-btn pid-btn-pollen" id="cam-snap" type="button" disabled>Sacar foto</button>
          <button class="pid-btn" id="cam-flip" type="button" disabled>Cambiar cámara</button>
          <button class="pid-btn" id="cam-stop" type="button" disabled>Apagar</button>
        </div>
      </div>
    </div>

    <ul class="tray" id="tray" aria-label="Fotos seleccionadas"></ul>

    <div class="actions">
      <button class="pid-btn pid-btn-primary" id="identify" type="button" disabled>Identificar planta</button>
      <button class="pid-btn" id="clear" type="button" disabled>Borrar todo</button>
      <p class="pid-hint" id="count-hint">Todavía no agregaste fotos.</p>
    </div>

    <div class="pid-status" id="status" role="status" aria-live="polite" hidden></div>
  </section>

  <section class="results" id="results" hidden aria-labelledby="results-title">
    <h2 id="results-title">Coincidencias</h2>
    <ol class="result-list" id="result-list"></ol>
    <p class="disclaimer">Son sugerencias automáticas, no un diagnóstico botánico. Nunca comas ni uses una planta, hongo o fruto por el solo resultado de esta herramienta.</p>
  </section>
</main>
</main>
<script>
(() => {
  'use strict';

  /* ================= CONFIGURACIÓN ================= */
  const CONFIG = {
    // Endpoint propio que habla con PlantNet y con la tabla `plantas`.
    endpoint: <?= json_encode(url('identificar_api')) ?>,

    // Página del vivero para una planta del catálogo (se le agrega el id).
    plantaUrl: 'vivero.html?planta=',

    maxPhotos: 5,
    maxSidePx: 1280,         // se redimensiona antes de enviar
    jpegQuality: 0.85
  };

  const ORGANS = [
    ['auto', 'Automático'], ['leaf', 'Hoja'], ['flower', 'Flor'],
    ['fruit', 'Fruto'], ['bark', 'Corteza'], ['habit', 'Planta entera'], ['other', 'Otro']
  ];

  /* ================= Referencias DOM ================= */
  const $ = (id) => document.getElementById(id);
  const el = {
    tabUpload: $('tab-upload'), tabCamera: $('tab-camera'),
    panelUpload: $('panel-upload'), panelCamera: $('panel-camera'),
    dropzone: $('dropzone'), fileInput: $('file-input'),
    video: $('video'), camPlaceholder: $('cam-placeholder'),
    camStart: $('cam-start'), camSnap: $('cam-snap'), camFlip: $('cam-flip'), camStop: $('cam-stop'),
    tray: $('tray'), identify: $('identify'), clear: $('clear'), countHint: $('count-hint'),
    status: $('status'), results: $('results'), resultList: $('result-list')
  };

  /** @type {{id:number, blob:Blob, url:string, organ:string}[]} */
  let photos = [];
  let nextId = 1;
  let stream = null;
  let facing = 'environment';
  let busy = false;

  /* ================= Utilidades ================= */
  function setStatus(msg, type = 'info', spinner = false) {
    if (!msg) { el.status.hidden = true; el.status.textContent = ''; return; }
    el.status.hidden = false;
    el.status.className = 'pid-status ' + type;
    el.status.textContent = '';
    if (spinner) {
      const s = document.createElement('span');
      s.className = 'pid-spinner'; s.setAttribute('aria-hidden', 'true');
      el.status.appendChild(s);
    }
    el.status.appendChild(document.createTextNode(msg));
  }

  function refreshUI() {
    const n = photos.length;
    el.identify.disabled = n === 0 || busy;
    el.clear.disabled = n === 0 || busy;
    el.countHint.textContent = n === 0
      ? 'Todavía no agregaste fotos.'
      : `${n} de ${CONFIG.maxPhotos} fotos. Podés sumar más ángulos para mejorar el resultado.`;
  }

  /** Redimensiona y comprime una imagen (File o canvas) a JPEG. */
  function compress(source) {
    return new Promise((resolve, reject) => {
      const draw = (img, w, h) => {
        const scale = Math.min(1, CONFIG.maxSidePx / Math.max(w, h));
        const cw = Math.round(w * scale), ch = Math.round(h * scale);
        const c = document.createElement('canvas');
        c.width = cw; c.height = ch;
        c.getContext('2d').drawImage(img, 0, 0, cw, ch);
        c.toBlob((b) => b ? resolve(b) : reject(new Error('No se pudo procesar la imagen.')),
          'image/jpeg', CONFIG.jpegQuality);
      };
      if (source instanceof HTMLVideoElement) {
        draw(source, source.videoWidth, source.videoHeight);
        return;
      }
      const url = URL.createObjectURL(source);
      const img = new Image();
      img.onload = () => { draw(img, img.naturalWidth, img.naturalHeight); URL.revokeObjectURL(url); };
      img.onerror = () => { URL.revokeObjectURL(url); reject(new Error('El archivo no es una imagen válida.')); };
      img.src = url;
    });
  }

  async function addPhoto(source) {
    if (photos.length >= CONFIG.maxPhotos) {
      setStatus(`Llegaste al máximo de ${CONFIG.maxPhotos} fotos. Borrá alguna para agregar otra.`, 'error');
      return;
    }
    try {
      const blob = await compress(source);
      photos.push({ id: nextId++, blob, url: URL.createObjectURL(blob), organ: 'auto' });
      renderTray();
      setStatus('');
    } catch (err) {
      setStatus(err.message, 'error');
    }
  }

  /* ================= Bandeja de fotos ================= */
  function renderTray() {
    el.tray.textContent = '';
    photos.forEach((p, i) => {
      const li = document.createElement('li');
      li.className = 'shot';

      const img = document.createElement('img');
      img.src = p.url; img.alt = `Foto ${i + 1}`;

      const bar = document.createElement('div');
      bar.className = 'shot-bar';

      const sel = document.createElement('select');
      sel.setAttribute('aria-label', `Parte de la planta en la foto ${i + 1}`);
      ORGANS.forEach(([v, label]) => {
        const o = document.createElement('option');
        o.value = v; o.textContent = label; o.selected = v === p.organ;
        sel.appendChild(o);
      });
      sel.addEventListener('change', () => { p.organ = sel.value; });

      const del = document.createElement('button');
      del.type = 'button'; del.textContent = 'Quitar';
      del.setAttribute('aria-label', `Quitar foto ${i + 1}`);
      del.addEventListener('click', () => removePhoto(p.id));

      bar.append(sel, del);
      li.append(img, bar);
      el.tray.appendChild(li);
    });
    refreshUI();
  }

  function removePhoto(id) {
    const p = photos.find((x) => x.id === id);
    if (p) URL.revokeObjectURL(p.url);
    photos = photos.filter((x) => x.id !== id);
    renderTray();
  }

  function clearAll() {
    photos.forEach((p) => URL.revokeObjectURL(p.url));
    photos = [];
    renderTray();
    el.results.hidden = true;
    el.resultList.textContent = '';
    setStatus('');
  }

  /* ================= Pestañas ================= */
  function selectTab(which) {
    const cam = which === 'camera';
    el.tabUpload.setAttribute('aria-selected', String(!cam));
    el.tabCamera.setAttribute('aria-selected', String(cam));
    el.tabUpload.tabIndex = cam ? -1 : 0;
    el.tabCamera.tabIndex = cam ? 0 : -1;
    el.panelUpload.hidden = cam;
    el.panelCamera.hidden = !cam;
    if (!cam) stopCamera();
  }
  el.tabUpload.addEventListener('click', () => selectTab('upload'));
  el.tabCamera.addEventListener('click', () => selectTab('camera'));
  [el.tabUpload, el.tabCamera].forEach((t) => t.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
      const goCam = t === el.tabUpload;
      selectTab(goCam ? 'camera' : 'upload');
      (goCam ? el.tabCamera : el.tabUpload).focus();
    }
  }));

  /* ================= Subida de archivos ================= */
  async function handleFiles(fileList) {
    const files = Array.from(fileList).filter((f) => f.type.startsWith('image/'));
    if (!files.length) { setStatus('Elegí archivos de imagen (JPG, PNG o WebP).', 'error'); return; }
    for (const f of files) await addPhoto(f);
  }
  el.fileInput.addEventListener('change', async () => {
    await handleFiles(el.fileInput.files);
    el.fileInput.value = '';
  });
  ['dragenter', 'dragover'].forEach((ev) => el.dropzone.addEventListener(ev, (e) => {
    e.preventDefault(); el.dropzone.classList.add('is-over');
  }));
  ['dragleave', 'drop'].forEach((ev) => el.dropzone.addEventListener(ev, (e) => {
    e.preventDefault(); el.dropzone.classList.remove('is-over');
  }));
  el.dropzone.addEventListener('drop', (e) => handleFiles(e.dataTransfer.files));

  /* ================= Cámara ================= */
  async function startCamera() {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      setStatus('Tu navegador no permite usar la cámara. Probá subir una foto.', 'error');
      return;
    }
    if (!window.isSecureContext) {
      setStatus('La cámara solo funciona en HTTPS o en localhost.', 'error');
      return;
    }
    stopCamera();
    try {
      stream = await navigator.mediaDevices.getUserMedia({
        video: { facingMode: { ideal: facing }, width: { ideal: 1920 }, height: { ideal: 1080 } },
        audio: false
      });
      el.video.srcObject = stream;
      el.video.hidden = false;
      el.camPlaceholder.hidden = true;
      el.camSnap.disabled = el.camStop.disabled = el.camFlip.disabled = false;
      el.camStart.textContent = 'Reiniciar cámara';
      setStatus('');
    } catch (err) {
      const msgs = {
        NotAllowedError: 'Bloqueaste el permiso de la cámara. Habilitalo en el candado de la barra de direcciones y volvé a intentar.',
        NotFoundError: 'No encontramos ninguna cámara en este dispositivo.',
        NotReadableError: 'La cámara está siendo usada por otra aplicación.'
      };
      setStatus(msgs[err.name] || 'No pudimos encender la cámara.', 'error');
    }
  }

  function stopCamera() {
    if (stream) stream.getTracks().forEach((t) => t.stop());
    stream = null;
    el.video.srcObject = null;
    el.video.hidden = true;
    el.camPlaceholder.hidden = false;
    el.camSnap.disabled = el.camStop.disabled = el.camFlip.disabled = true;
    el.camStart.textContent = 'Encender cámara';
  }

  el.camStart.addEventListener('click', startCamera);
  el.camStop.addEventListener('click', stopCamera);
  el.camFlip.addEventListener('click', () => {
    facing = facing === 'environment' ? 'user' : 'environment';
    startCamera();
  });
  el.camSnap.addEventListener('click', () => {
    if (el.video.videoWidth) addPhoto(el.video);
  });
  window.addEventListener('pagehide', stopCamera);
  document.addEventListener('visibilitychange', () => { if (document.hidden) stopCamera(); });

  /* ================= Identificación ================= */
  async function identify() {
    if (busy || !photos.length) return;

    busy = true; refreshUI();
    el.results.hidden = true;
    setStatus('Analizando tus fotos…', 'info', true);

    const form = new FormData();
    form.append('csrf', <?= json_encode(csrf_token()) ?>);
    photos.forEach((p, i) => {
      form.append('images[]', p.blob, `foto-${i + 1}.jpg`);
      form.append('organs[]', p.organ);
    });

    try {
      const res = await fetch(CONFIG.endpoint, { method: 'POST', body: form, credentials: 'same-origin' });
      let data = null;
      try { data = await res.json(); } catch (_) { /* respuesta sin JSON */ }
      if (!res.ok) throw new Error((data && data.error) || `El servicio respondió con un error (${res.status}).`);
      renderResults((data && data.resultados) || []);
    } catch (err) {
      const offline = err instanceof TypeError;
      setStatus(offline ? 'No pudimos conectar con el servicio. Revisá tu conexión e intentá de nuevo.' : err.message, 'error');
    } finally {
      busy = false; refreshUI();
    }
  }

  const fmtPrecio = (n) => new Intl.NumberFormat('es-AR', {
    style: 'currency', currency: 'ARS', maximumFractionDigits: 0
  }).format(n);

  function textEl(tag, className, text) {
    const e = document.createElement(tag);
    if (className) e.className = className;
    e.textContent = text;
    return e;
  }

  function renderCatalogo(c) {
    const box = document.createElement('div');
    box.className = 'catalogo' + (c.disponible ? '' : ' agotada');

    box.appendChild(textEl('p', 'catalogo-titulo',
      c.coincidencia === 'especie' ? 'La tenemos en el vivero' : 'Tenemos una planta del mismo género'));

    const fila = document.createElement('div');
    fila.className = 'catalogo-fila';
    fila.append(textEl('span', 'catalogo-nombre', c.nombre_comun));
    box.appendChild(fila);

    const cuidados = [['Riego', c.riego], ['Luz', c.luz], ['Temperatura', c.temperatura_ideal], ['Mejor época', c.estacion]]
      .filter(([, v]) => v);
    if (cuidados.length) {
      const dl = document.createElement('dl');
      dl.className = 'cuidados';
      cuidados.forEach(([k, v]) => dl.append(textEl('dt', '', k), textEl('dd', '', v)));
      box.appendChild(dl);
    }

    return box;
  }

  function renderResults(list) {
    el.resultList.textContent = '';
    if (!list.length) {
      setStatus('No encontramos coincidencias. Probá con una foto más cercana y enfocada de la hoja o la flor.', 'error');
      el.results.hidden = true;
      return;
    }
    setStatus('');
    list.forEach((r) => {
      const sci = r.cientifico || 'Especie sin nombre';
      const common = r.comun || sci;
      const pct = Math.round((r.puntaje || 0) * 100);

      const li = document.createElement('li');
      li.className = 'result';

      const img = document.createElement('img');
      img.className = 'result-img'; img.loading = 'lazy';
      img.alt = r.imagen ? `Imagen de referencia de ${sci}` : '';
      if (r.imagen) img.src = r.imagen; else img.style.visibility = 'hidden';

      const body = document.createElement('div');
      body.append(textEl('h3', '', common), textEl('p', 'sci', sci));

      const extra = [];
      if (r.familia) extra.push(`Familia ${r.familia}`);
      if (r.otros_nombres && r.otros_nombres.length) extra.push('También: ' + r.otros_nombres.join(', '));
      if (extra.length) body.appendChild(textEl('p', 'meta', extra.join('. ')));

      const meter = document.createElement('div'); meter.className = 'meter';
      const track = document.createElement('div'); track.className = 'meter-track';
      track.setAttribute('role', 'img'); track.setAttribute('aria-label', `Coincidencia del ${pct} por ciento`);
      const fill = document.createElement('div'); fill.className = 'meter-fill'; fill.style.width = pct + '%';
      track.appendChild(fill);
      meter.append(track, textEl('span', 'meter-label', pct + '%'));
      body.appendChild(meter);

      if (r.catalogo) body.appendChild(renderCatalogo(r.catalogo));

      const a = textEl('a', '', 'Ver más información');
      a.href = 'https://es.wikipedia.org/w/index.php?search=' + encodeURIComponent(sci);
      a.target = '_blank'; a.rel = 'noopener noreferrer';
      const wrap = document.createElement('p'); wrap.className = 'meta'; wrap.appendChild(a);
      body.appendChild(wrap);

      li.append(img, body);
      el.resultList.appendChild(li);
    });
    el.results.hidden = false;
    el.results.scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth', block: 'start' });
  }

  el.identify.addEventListener('click', identify);
  el.clear.addEventListener('click', clearAll);
  refreshUI();
})();
</script>
