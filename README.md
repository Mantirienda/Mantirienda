# Mantirienda 🌸 — PHP + MySQL (MVC), para XAMPP

## Instalar
1. Copiá esta carpeta a `C:\xampp\htdocs\mantirienda` (reemplazá la versión de Node).
2. XAMPP: iniciá **Apache** y **MySQL**.
3. phpMyAdmin → **Importar** → `db/mantirienda.sql` (crea `mantirienda_db`).
   Si ya la tenías importada de antes, en la pestaña **SQL** ejecutá solo `db/migracion_reservas.sql` (agrega los campos nuevos de reservas).
4. Abrí **http://localhost/mantirienda/**
5. Mozo/admin: `http://localhost/mantirienda/crear-staff.php?nombre=Ana&email=ana@mail.com&clave=123456&rol=mozo` y después borrá `crear-staff.php`.

## Estructura
```
index.php           front controller (rutas: index.php?r=login)
config/config.php   base de datos, BASE_URL, modo local
app/core/           helpers (db, sesión, vistas)
app/models/         Usuario, Planta, Reserva, Puntos, Mesa
app/controllers/    Landing, Auth, Panel, Reserva, Puntos, Staff
app/views/          vistas PHP (partials, auth, panel, staff)
public/             css/, js/, img/  (estáticos)
db/mantirienda.sql  tablas + datos iniciales
storage/            log de mails en modo local
```
QR desde el celu: en `config/config.php` poné `BASE_URL` con la IP de tu PC (ej. `http://192.168.1.15/mantirienda/`).
