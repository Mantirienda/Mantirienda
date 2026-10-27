<?php /* Puente de Flores: pantallas informativas integradas */ ?>
<link rel="stylesheet" href="public/css/info_flores.css">
<section id="flores" class="flores-scope">


    <!-- Fondo botánico animado -->
    <div class="ambient" aria-hidden="true">
        <span class="blob b1"></span>
        <span class="blob b2"></span>
        <span class="blob b3"></span>
        <div class="petals" id="petals"></div>
    </div>

    <!-- Barra de progreso de scroll -->
    <div class="scroll-progress" id="scroll-progress" aria-hidden="true"></div>

    <div class="page-container">

        <header class="hero">
            <span class="badge reveal"><span class="dot"></span> Mantirienda</span>
            <h1 class="reveal">Puente <em>de</em> Flores</h1>
            <p class="reveal">Un paseo botánico por especies de todo el mundo. Tocá cualquier flor para abrir su ficha.</p>
            <div class="hero-line reveal" aria-hidden="true">
                <svg viewBox="0 0 200 20" preserveAspectRatio="none"><path d="M0 10 Q 25 0 50 10 T 100 10 T 150 10 T 200 10"/></svg>
            </div>
        </header>

        <!-- Filtros -->
        <section class="filters reveal" aria-label="Filtros">
            <div class="filter-group">
                <span class="filter-label">Zona</span>
                <div class="chips" id="zone-chips" role="group" aria-label="Filtrar por zona"></div>
            </div>
            <div class="filter-group">
                <span class="filter-label">Estación</span>
                <div class="chips" id="season-chips" role="group" aria-label="Filtrar por estación"></div>
            </div>

            <div class="active-filters" id="active-filters" aria-live="polite">
                <span class="active-label">Filtros activos:</span>
                <div class="active-list" id="active-list"></div>
                <button class="clear-all" id="clear-all" type="button">Limpiar todo ✕</button>
            </div>
        </section>

        <p class="results-count" id="results-count"></p>

        <main class="gallery-container" id="flower-gallery"></main>

        <div class="empty-state" id="empty-state" hidden>
            <span class="empty-icon">🌱</span>
            <p>No hay flores con esa combinación.</p>
            <button class="clear-all solid" type="button" id="empty-clear">Quitar filtros</button>
        </div>

        <footer class="site-footer">Mantirienda · Puente de Flores</footer>
    </div>

    <!-- Ficha de la flor -->
    <div class="modal-overlay" id="flower-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="modal-name">
        <div class="modal-card" id="modal-card">
            <button class="modal-close" id="modal-close" aria-label="Cerrar ficha">
                <svg viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18"/></svg>
            </button>

            <div class="modal-image-wrapper">
                <img id="modal-img" src="" alt="">
                <span class="modal-tag anim" style="--d:2" id="modal-zone-tag">Zona</span>
                <svg class="leaf-deco" viewBox="0 0 100 100" aria-hidden="true"><path d="M10 90 C 10 40, 40 10, 90 10 C 90 60, 60 90, 10 90 Z M10 90 L 70 30"/></svg>
            </div>

            <div class="modal-info">
                <span class="modal-subtitle anim" style="--d:1" id="modal-scientific"></span>
                <h2 class="modal-title anim" style="--d:2" id="modal-name"></h2>

                <div class="modal-grid">
                    <div class="info-box anim" style="--d:3">
                        <span class="box-icon">🧭</span>
                        <span class="box-label">Origen</span>
                        <span class="box-value" id="modal-origin"></span>
                    </div>
                    <div class="info-box anim" style="--d:4">
                        <span class="box-icon">🍃</span>
                        <span class="box-label">Estación</span>
                        <span class="box-value" id="modal-season"></span>
                    </div>
                    <div class="info-box anim" style="--d:5">
                        <span class="box-icon">☀️</span>
                        <span class="box-label">Luz</span>
                        <span class="box-value" id="modal-light"></span>
                    </div>
                    <div class="info-box anim" style="--d:6">
                        <span class="box-icon">✦</span>
                        <span class="box-label">Simbolismo</span>
                        <span class="box-value" id="modal-symbolism"></span>
                    </div>
                </div>

                <div class="modal-description anim" style="--d:7">
                    <h3>Descripción e historia</h3>
                    <p id="modal-description-text"></p>
                </div>

                <div class="modal-nav anim" style="--d:8">
                    <button type="button" id="modal-prev" aria-label="Flor anterior">← Anterior</button>
                    <span id="modal-pos"></span>
                    <button type="button" id="modal-next" aria-label="Flor siguiente">Siguiente →</button>
                </div>
            </div>
        </div>
    </div>

    </section>
<script src="public/js/info_flores.js"></script>
