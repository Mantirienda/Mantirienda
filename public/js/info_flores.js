// ==========================================================
// Puente de Flores · Mantirienda
// ==========================================================
const floresData = [
    {
        id: 1,
        nombre: "Ceibo",
        cientifico: "Erythrina crista-galli",
        origen: "Sudamérica (Argentina, Uruguay, Brasil y Paraguay)",
        zona: "América del Sur",
        estaciones: ["Primavera", "Verano"],
        luz: "Sol pleno",
        simbolismo: "Patriotismo y Resiliencia",
        descripcion: "Flor nacional de Argentina y Uruguay. Sus flores de un rojo carmesí intenso poseen una estructura en forma de espada curva adaptada específicamente para la polinización por aves carpinteras y colibríes.",
        imagen: "public/img/flores/ceibo.avif"    
    },
    {
        id: 2,
        nombre: "Tulipanes",
        cientifico: "Tulipa gesneriana",
        origen: "Asia Central y Turquía",
        zona: "Asia",
        estaciones: ["Primavera"],
        luz: "Sol moderado a pleno",
        simbolismo: "Amor perfecto y Prosperidad",
        descripcion: "Bulbosas famosas por sus copas simétricas y colores vibrantes. Protagonistas de la histórica 'Tulipomanía' en los Países Bajos durante el siglo XVII.",
        imagen: "public/img/flores/tulipan.jpg"
    },
    {
        id: 3,
        nombre: "Peonías",
        cientifico: "Paeonia lactiflora",
        origen: "Asia Oriental, Europa y Oeste de Norteamérica",
        zona: "Asia / Europa",
        estaciones: ["Primavera", "Verano"],
        luz: "Sol directo o semisombra",
        simbolismo: "Riqueza, Honor y Romancero",
        descripcion: "Apreciadas por sus enormes y voluptuosas cabezas florales dobles llenas de pétalos sedosos, que emiten una fragancia dulce y embriagadora muy cotizada en alta jardinería.",
        imagen: "public/img/flores/peonia.webp"
    },
    {
        id: 4,
        nombre: "Plumerillo (Romerillo / Escobita)",
        cientifico: "Calliandra tweedii",
        origen: "Regiones tropicales y subtropicales de América",
        zona: "América del Sur",
        estaciones: ["Primavera", "Verano", "Otoño"],
        luz: "Sol pleno",
        simbolismo: "Hospitalidad y Atracción",
        descripcion: "Arbusto nativo muy apreciado por sus llamativas inflorescencias en forma de pompones o plumerillos rojos compuestos por largos estambres que fascinan a los colibríes.",
        imagen: "public/img/flores/plumerillo.jfif"
    },
    {
        id: 5,
        nombre: "Calceolaria",
        cientifico: "Calceolaria uniflora",
        origen: "Regiones andinas y sur de Sudamérica",
        zona: "América del Sur",
        estaciones: ["Primavera", "Verano"],
        luz: "Semisombra fresca",
        simbolismo: "Originalidad y Riqueza",
        descripcion: "Conocida popularmente como 'zapatitos de la Virgen' debido a la forma insólita y abalonada de sus pétalos inferiores que simulan diminutos zuecos o monederos bicolores.",
        imagen: "public/img/flores/calceolaria.jfif"
    },
    {
        id: 6,
        nombre: "Glandularia peruviana",
        cientifico: "Glandularia peruviana",
        origen: "Sudamérica templada y tropical",
        zona: "América del Sur",
        estaciones: ["Primavera", "Verano", "Otoño"],
        luz: "Sol pleno",
        simbolismo: "Resistencia y Unión",
        descripcion: "Hermosa planta rastrera nativa que forma densos tapices de flores escarlata o púrpuras intensas, sumamente resistente a las sequías y de floración prolongada.",
        imagen: "public/img/flores/glandularia.jpg"
    },
    {
        id: 7,
        nombre: "Verbena bonariensis",
        cientifico: "Verbena bonariensis",
        origen: "Sudamérica (Argentina, Brasil, Colombia)",
        zona: "América del Sur",
        estaciones: ["Primavera", "Verano", "Otoño"],
        luz: "Sol pleno",
        simbolismo: "Encanto y Protección",
        descripcion: "Planta herbácea alta y esbelta con ramilletes de flores lilas en la cima de tallos rígidos. Es un imán absoluto para mariposas y polinizadores en los jardines botánicos.",
        imagen: "public/img/flores/verbena.webp"
    },
    {
        id: 8,
        nombre: "Orquídea Phalaenopsis",
        cientifico: "Phalaenopsis amabilis",
        origen: "Sudeste Asiático",
        zona: "Asia",
        estaciones: ["Primavera", "Otoño"],
        luz: "Luz indirecta brillante",
        simbolismo: "Elegancia y Belleza",
        descripcion: "Célebre por sus deslumbrantes flores que semejan mariposas en vuelo. En la época victoriana, simbolizaban el lujo refinado y la distinción botánica.",
        imagen: "public/img/flores/orquidea.jfif"
    },
    {
        id: 9,
        nombre: "Rosa de Damasco",
        cientifico: "Rosa × damascena",
        origen: "Oriente Medio",
        zona: "Medio Oriente",
        estaciones: ["Primavera"],
        luz: "Sol directo (6h+)",
        simbolismo: "Amor y Pureza",
        descripcion: "Famosa por su fragancia profunda y cautivadora. Sus pétalos se han cosechado desde la antigüedad para la extracción de aceites esenciales en perfumería fina.",
        imagen: "public/img/flores/rosa.jfif"
    },
    {
        id: 10,
        nombre: "Tulipán Real",
        cientifico: "Tulipa gesneriana (Variedad de Exposición)",
        origen: "Asia Central / Turquía",
        zona: "Medio Oriente",
        estaciones: ["Primavera"],
        luz: "Sol moderado",
        simbolismo: "Prosperidad y Pasión",
        descripcion: "Especie de bulbo selecto con pétalos satinados de alta gama, símbolo clásico de la nobleza en los antiguos jardines imperiales otomanos y europeos.",
        imagen: "public/img/flores/tulipan_real.jfif"
    },
    {
        id: 11,
        nombre: "Flor Murciélago",
        cientifico: "Tacca chantrieri",
        origen: "Sudeste Asiático (Malasia y Tailandia)",
        zona: "Asia",
        estaciones: ["Verano", "Otoño"],
        luz: "Sombra parcial, alta humedad",
        simbolismo: "Misterio y Adaptación",
        descripcion: "Singulares brácteas oscuras que recuerdan a un murciélago en vuelo, acompañadas de apéndices filiformes en forma de bigotes de hasta 30 centímetros.",
        imagen: "public/img/flores/murcielago.jfif"
    },
    {
        id: 12,
        nombre: "Flor de Jade",
        cientifico: "Strongylodon macrobotrys",
        origen: "Selvas tropicales de Filipinas",
        zona: "Asia",
        estaciones: ["Primavera"],
        luz: "Luz brillante indirecta",
        simbolismo: "Rarísima Exquisitez",
        descripcion: "Racimos colgantes en forma de garras con un tono luminiscente turquesa o verde jade inigualable en el reino vegetal.",
        imagen: "public/img/flores/jade.avif"
    },
    {
        id: 13,
        nombre: "Ciclamen de Persia",
        cientifico: "Cyclamen persicum",
        origen: "Región del Mediterráneo hasta Oriente Medio",
        zona: "Europa / Medio Oriente",
        estaciones: ["Invierno", "Primavera"],
        luz: "Luz indirecta y ambiente fresco",
        simbolismo: "Sinceridad y Empatía",
        descripcion: "Planta tuberosa emblemática del invierno que despliega delicadas flores erguidas con pétalos revirados hacia arriba en tonos fucsia, rosados o blancos, acompañadas de hojas acorazonadas con patrones plateados.",
        imagen: "public/img/flores/ciclamen.jfif"
    },
    {
        id: 14,
        nombre: "Labios Ardientes",
        cientifico: "Psychotria elata",
        origen: "Bosques tropicales de Centro y Sudamérica",
        zona: "América Central",
        estaciones: ["Primavera", "Verano"],
        luz: "Luz filtrada de selva",
        simbolismo: "Pasión y Seducción",
        descripcion: "Brácteas carnosas que imitan a la perfección unos labios humanos pintados de rojo intenso, diseñadas evolutivamente para atraer a los colibríes.",
        imagen: "public/img/flores/labios.jpg"
    },
    {
        id: 15,
        nombre: "Cuna de Venus",
        cientifico: "Anguloa uniflora",
        origen: "Andes de Sudamérica (Colombia y Perú)",
        zona: "América del Sur",
        estaciones: ["Primavera"],
        luz: "Sombra luminosa de montaña",
        simbolismo: "Nacimiento y Refugio",
        descripcion: "Orquídea epífita extraordinaria cuyas grandes flores blancas y cerosas albergan una estructura interna similar a una pequeña cuna mecida por el viento.",
        imagen: "public/img/flores/venus.webp"
    },
    {
        id: 16,
        nombre: "Jengibre de Colmena",
        cientifico: "Zingiber spectabile",
        origen: "Sudeste Asiático",
        zona: "Asia",
        estaciones: ["Verano"],
        luz: "Sombra parcial tropical",
        simbolismo: "Estructura y Longevidad",
        descripcion: "Inflorescencia con aspecto de colmena arquitectónica construida con brácteas cerosas amarillas y naranjas capaces de retener agua de lluvia.",
        imagen: "public/img/flores/jengibre.jfif"
    },
    {
        id: 17,
        nombre: "Camelia Invernal",
        cientifico: "Camellia japonica",
        origen: "Asia Oriental (China, Japón y Corea)",
        zona: "Asia",
        estaciones: ["Invierno", "Primavera"],
        luz: "Semisombra sin sol directo",
        simbolismo: "Admiración y Perfección",
        descripcion: "Un clásico de los jardines botánicos durante los meses fríos. Destaca por su follaje verde oscuro brillante y sus impecables flores simétricas de pétalos cerosos en tonos rojo, rosa o blanco puro que desafían las heladas.",
        imagen: "public/img/flores/camelia.jfif"
    },
];

const ZONAS = [...new Set(floresData.map(f => f.zona))];
const ESTACIONES = ["Primavera", "Verano", "Otoño", "Invierno"].filter(e => floresData.some(f => f.estaciones.includes(e)));
const ICONOS_ESTACION = { Primavera: "🌸", Verano: "☀️", Otoño: "🍂", Invierno: "❄️" };

const filtros = { zona: null, estacion: null };
let visibles = [...floresData];
let indiceActual = 0;

const $ = id => document.getElementById(id);
const gallery = $("flower-gallery");
const modal = $("flower-modal");
const modalCard = $("modal-card");

// ---------- Pétalos de fondo ----------
(function crearPetalos() {
    const cont = $("petals");
    const n = window.innerWidth < 700 ? 8 : 16;
    for (let i = 0; i < n; i++) {
        const p = document.createElement("span");
        p.className = "petal";
        p.style.left = Math.random() * 100 + "%";
        p.style.animationDuration = 14 + Math.random() * 16 + "s";
        p.style.animationDelay = -Math.random() * 30 + "s";
        p.style.setProperty("--x", (Math.random() * 200 - 100) + "px");
        const s = 8 + Math.random() * 10;
        p.style.width = p.style.height = s + "px";
        if (i % 3 === 0) p.style.background = "#e7b8a1";
        cont.appendChild(p);
    }
})();

// ---------- Reveal on scroll ----------
const observer = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting) { e.target.classList.add("in"); observer.unobserve(e.target); }
    });
}, { threshold: 0.12 });
document.querySelectorAll(".reveal").forEach(el => observer.observe(el));

// ---------- Barra de progreso ----------
const progress = $("scroll-progress");
window.addEventListener("scroll", () => {
    const max = document.documentElement.scrollHeight - innerHeight;
    progress.style.transform = `scaleX(${max > 0 ? scrollY / max : 0})`;
}, { passive: true });

// ---------- Filtros ----------
function crearChips(contenedor, valores, tipo) {
    contenedor.innerHTML = "";
    valores.forEach(v => {
        const b = document.createElement("button");
        b.type = "button";
        b.className = "chip";
        b.dataset.tipo = tipo;
        b.dataset.valor = v;
        b.setAttribute("aria-pressed", "false");
        b.innerHTML = `${tipo === "estacion" ? ICONOS_ESTACION[v] + " " : ""}${v}<span class="x" aria-hidden="true">✕</span>`;
        b.addEventListener("click", () => {
            filtros[tipo] = filtros[tipo] === v ? null : v;
            b.classList.remove("pop"); void b.offsetWidth; b.classList.add("pop");
            aplicarFiltros();
        });
        contenedor.appendChild(b);
    });
}

function actualizarUIFiltros() {
    document.querySelectorAll(".chip").forEach(c => {
        const on = filtros[c.dataset.tipo] === c.dataset.valor;
        c.classList.toggle("active", on);
        c.setAttribute("aria-pressed", on);
    });
    const lista = $("active-list");
    lista.innerHTML = "";
    Object.entries(filtros).forEach(([tipo, valor]) => {
        if (!valor) return;
        const pill = document.createElement("span");
        pill.className = "pill";
        pill.innerHTML = `${valor}<button type="button" aria-label="Quitar filtro ${valor}">✕</button>`;
        pill.querySelector("button").onclick = () => { filtros[tipo] = null; aplicarFiltros(); };
        lista.appendChild(pill);
    });
    $("active-filters").classList.toggle("show", !!(filtros.zona || filtros.estacion));
}

function limpiarFiltros() { filtros.zona = null; filtros.estacion = null; aplicarFiltros(); }
$("clear-all").onclick = limpiarFiltros;
$("empty-clear").onclick = limpiarFiltros;

function aplicarFiltros() {
    actualizarUIFiltros();
    visibles = floresData.filter(f =>
        (!filtros.zona || f.zona === filtros.zona) &&
        (!filtros.estacion || f.estaciones.includes(filtros.estacion))
    );
    const actuales = gallery.querySelectorAll(".flower-card");
    actuales.forEach(c => c.classList.add("out"));
    setTimeout(renderGallery, actuales.length ? 280 : 0);
}

// ---------- Galería ----------
function renderGallery() {
    gallery.innerHTML = "";
    $("empty-state").hidden = visibles.length > 0;
    $("results-count").innerHTML = `<b>${visibles.length}</b> ${visibles.length === 1 ? "especie" : "especies"} en el puente`;

    visibles.forEach((flor, i) => {
        const card = document.createElement("article");
        card.className = "flower-card";
        card.tabIndex = 0;
        card.setAttribute("role", "button");
        card.setAttribute("aria-label", `Ver ficha de ${flor.nombre}`);
        card.innerHTML = `
            <div class="card-image-container">
                <img src="${flor.imagen}" alt="${flor.nombre}" loading="lazy">
            </div>
            <span class="card-badge">${flor.estaciones.map(e => ICONOS_ESTACION[e]).join(" ")} ${flor.estaciones.join(" · ")}</span>
            <span class="card-num">${String(flor.id).padStart(2, "0")}</span>
            <div class="card-content">
                <span class="card-subtitle">${flor.cientifico}</span>
                <h2 class="card-title">${flor.nombre}</h2>
                <div class="card-footer">
                    <span>📍 ${flor.zona}</span>
                    <span class="tap-indicator">Ver ficha →</span>
                </div>
            </div>`;
        card.addEventListener("click", () => openModal(i));
        card.addEventListener("keydown", e => {
            if (e.key === "Enter" || e.key === " ") { e.preventDefault(); openModal(i); }
        });
        // Efecto tilt suave solo con mouse
        card.addEventListener("pointermove", e => {
            if (e.pointerType !== "mouse") return;
            const r = card.getBoundingClientRect();
            const x = (e.clientX - r.left) / r.width - 0.5;
            const y = (e.clientY - r.top) / r.height - 0.5;
            card.style.transform = `translateY(-8px) perspective(900px) rotateY(${x * 6}deg) rotateX(${-y * 6}deg)`;
        });
        card.addEventListener("pointerleave", () => { card.style.transform = ""; });

        gallery.appendChild(card);
        card.style.transitionDelay = `${(i % 3) * 90}ms`;
        observer.observe(card);
        setTimeout(() => { card.style.transitionDelay = ""; }, 1200);
    });
}

// ---------- Modal ----------
function llenarModal(flor) {
    $("modal-img").src = flor.imagen;
    $("modal-img").alt = flor.nombre;
    $("modal-zone-tag").textContent = "📍 " + flor.zona;
    $("modal-scientific").textContent = flor.cientifico;
    $("modal-name").textContent = flor.nombre;
    $("modal-origin").textContent = flor.origen;
    $("modal-season").textContent = flor.estaciones.join(" / ");
    $("modal-light").textContent = flor.luz;
    $("modal-symbolism").textContent = flor.simbolismo;
    $("modal-description-text").textContent = flor.descripcion;
    $("modal-pos").textContent = `${indiceActual + 1} / ${visibles.length}`;
    const solo = visibles.length < 2;
    $("modal-prev").disabled = $("modal-next").disabled = solo;
    $("modal-prev").style.visibility = $("modal-next").style.visibility = solo ? "hidden" : "visible";
}

function openModal(i) {
    indiceActual = i;
    llenarModal(visibles[i]);
    modal.classList.add("active");
    modal.setAttribute("aria-hidden", "false");
    document.body.classList.add("modal-open");
    setTimeout(() => $("modal-close").focus({ preventScroll: true }), 300);
}

function closeModal() {
    modal.classList.remove("active");
    modal.setAttribute("aria-hidden", "true");
    document.body.classList.remove("modal-open");
}

function cambiar(delta) {
    if (visibles.length < 2) return;
    indiceActual = (indiceActual + delta + visibles.length) % visibles.length;
    modalCard.classList.add("swap");
    llenarModal(visibles[indiceActual]);
    void modalCard.offsetWidth; // reinicia las animaciones de entrada
    modalCard.classList.remove("swap");
}

$("modal-close").addEventListener("click", closeModal);
$("modal-prev").addEventListener("click", () => cambiar(-1));
$("modal-next").addEventListener("click", () => cambiar(1));
modal.addEventListener("click", e => { if (e.target === modal) closeModal(); });
document.addEventListener("keydown", e => {
    if (!modal.classList.contains("active")) return;
    if (e.key === "Escape") closeModal();
    if (e.key === "ArrowRight") cambiar(1);
    if (e.key === "ArrowLeft") cambiar(-1);
});

// Deslizar en táctil para cambiar de flor / bajar para cerrar
let toque = null;
modalCard.addEventListener("touchstart", e => { toque = { x: e.touches[0].clientX, y: e.touches[0].clientY }; }, { passive: true });
modalCard.addEventListener("touchend", e => {
    if (!toque) return;
    const dx = e.changedTouches[0].clientX - toque.x;
    const dy = e.changedTouches[0].clientY - toque.y;
    if (Math.abs(dx) > 60 && Math.abs(dx) > Math.abs(dy)) cambiar(dx < 0 ? 1 : -1);
    else if (dy > 110 && Math.abs(dy) > Math.abs(dx)) closeModal();
    toque = null;
});
modal.addEventListener("touchmove", e => e.preventDefault(), { passive: false });

// ---------- Inicio ----------
crearChips($("zone-chips"), ZONAS, "zona");
crearChips($("season-chips"), ESTACIONES, "estacion");
aplicarFiltros();
