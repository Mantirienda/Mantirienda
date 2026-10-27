<?php
/**
 * Identificador de plantas: recibe fotos, consulta PlantNet y cruza con la tabla `plantas`.
 * Config en config/config.php: PLANTNET_API_KEY y APP_SECRET.
 */
class IdentificarController {
  const MAX_FOTOS = 5; const MAX_BYTES_FOTO = 6291456; const LIMITE_HORA_ANON = 10; const LIMITE_HORA_USER = 30; const MAX_RESULTADOS = 5;
  const ORGANOS = ['auto', 'leaf', 'flower', 'fruit', 'bark', 'habit', 'other'];
  const MIMES_PERMITIDOS = ['image/jpeg', 'image/png', 'image/webp'];

  static function vista() {
    require_login();
    render('panel/identificador', ['title' => 'Identificador de plantas']);
  }

  static function responder(int $codigo, array $cuerpo): never {
    http_response_code($codigo);
    echo json_encode($cuerpo, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
  }

  static function armarMultipart(array $partes, string $limite): string
{
    $cuerpo = '';
    foreach ($partes as $p) {
        $cuerpo .= "--{$limite}\r\n";
        if (isset($p['archivo'])) {
            $cuerpo .= "Content-Disposition: form-data; name=\"{$p['nombre']}\"; filename=\"{$p['archivo']}\"\r\n";
            $cuerpo .= "Content-Type: {$p['mime']}\r\n\r\n{$p['contenido']}\r\n";
        } else {
            $cuerpo .= "Content-Disposition: form-data; name=\"{$p['nombre']}\"\r\n\r\n{$p['valor']}\r\n";
        }
    }
    return $cuerpo . "--{$limite}--\r\n";
}

static function escaparLike(string $s): string
{
    return addcslashes($s, '\\%_');
}

/** Busca en el catálogo: primero la misma especie, después el mismo género. */
static function buscarEnCatalogo(PDO $pdo, string $cientifico): ?array
{
    $sql = 'SELECT p.id, p.nombre_comun, p.nombre_cientifico, p.imagen_url, p.riego, p.luz,
                   p.temperatura_ideal, c.nombre AS categoria, e.nombre AS estacion
              FROM plantas p
              LEFT JOIN categorias c ON c.id = p.categoria_id
              LEFT JOIN estaciones e ON e.id = p.estacion_ideal_id
             WHERE p.nombre_cientifico LIKE ?
             ORDER BY p.nombre_comun
             LIMIT 1';
    $st = $pdo->prepare($sql);

    $intentos = [['especie', self::escaparLike($cientifico) . '%']];
    $genero = strtok($cientifico, ' ');
    if ($genero !== false && $genero !== '' && $genero !== $cientifico) {
        $intentos[] = ['genero', self::escaparLike($genero) . ' %'];
    }

    foreach ($intentos as [$tipo, $patron]) {
        $st->execute([$patron]);
        $fila = $st->fetch();
        if ($fila) {
            return [
                'id'                => (int) $fila['id'],
                'coincidencia'      => $tipo,
                'nombre_comun'      => $fila['nombre_comun'],
                'nombre_cientifico' => $fila['nombre_cientifico'],
                'imagen_url'        => $fila['imagen_url'],
                'categoria'         => $fila['categoria'],
                'estacion'          => $fila['estacion'],
                'riego'             => $fila['riego'],
                'luz'               => $fila['luz'],
                'temperatura_ideal' => $fila['temperatura_ideal'],
                'disponible'        => true,
            ];
        }
    }
    return null;
}

  static function api() {
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: no-store');
    if (!user()) self::responder(401, ['error' => 'Iniciá sesión para usar el identificador.']);


    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Allow: POST');
        self::responder(405, ['error' => 'Método no permitido.']);
    }

    $apiKey = PLANTNET_API_KEY;
    $secreto = APP_SECRET;
    if (!$apiKey || !$secreto) {
        error_log('[identificar] Faltan PLANTNET_API_KEY o APP_SECRET.');
        self::responder(500, ['error' => 'El servicio no está disponible por el momento.']);
    }

    $usuarioId = user() ? (int) user()['id'] : null;

    if (empty($_FILES['images']) || !is_array($_FILES['images']['tmp_name'])) {
        self::responder(400, ['error' => 'Agregá al menos una foto.']);
    }

    $cantidad = count($_FILES['images']['tmp_name']);
    if ($cantidad < 1 || $cantidad > self::MAX_FOTOS) {
        self::responder(400, ['error' => 'Podés enviar entre 1 y ' . self::MAX_FOTOS . ' fotos.']);
    }

    $organos = $_POST['organs'] ?? [];
    if (!is_array($organos)) {
        $organos = [];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $partes = [];
    for ($i = 0; $i < $cantidad; $i++) {
        $tmp = $_FILES['images']['tmp_name'][$i];
        if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK || !is_uploaded_file($tmp)) {
            self::responder(400, ['error' => 'No pudimos recibir una de las fotos. Probá de nuevo.']);
        }
        if ($_FILES['images']['size'][$i] > self::MAX_BYTES_FOTO) {
            self::responder(413, ['error' => 'Una de las fotos pesa demasiado (máximo 6 MB).']);
        }
        $mime = $finfo->file($tmp);
        if (!in_array($mime, self::MIMES_PERMITIDOS, true)) {
            self::responder(415, ['error' => 'Solo se aceptan fotos JPG, PNG o WebP.']);
        }
        $organo = in_array($organos[$i] ?? 'auto', self::ORGANOS, true) ? $organos[$i] : 'auto';

        $partes[] = ['nombre' => 'images', 'archivo' => "foto-" . ($i + 1) . ".jpg", 'mime' => $mime, 'contenido' => file_get_contents($tmp)];
        $partes[] = ['nombre' => 'organs', 'valor' => $organo];
    }

    /* ---------------- Límite de uso ---------------- */

    $ipHash = hash_hmac('sha256', $_SERVER['REMOTE_ADDR'] ?? 'desconocida', $secreto);

    try {
        $pdo = db();

        $st = $pdo->prepare(
            'SELECT COUNT(*) FROM identificaciones
              WHERE creado_en > (NOW() - INTERVAL 1 HOUR)
                AND ' . ($usuarioId ? 'usuario_id = ?' : 'ip_hash = ?')
        );
        $st->execute([$usuarioId ?? $ipHash]);
        $limite = $usuarioId ? self::LIMITE_HORA_USER : self::LIMITE_HORA_ANON;
        if ((int) $st->fetchColumn() >= $limite) {
            self::responder(429, ['error' => 'Hiciste muchas consultas seguidas. Probá de nuevo en un rato.']);
        }

        // Se registra antes de llamar al servicio externo para que los intentos fallidos también cuenten.
        $st = $pdo->prepare('INSERT INTO identificaciones (usuario_id, ip_hash, cantidad_fotos) VALUES (?, ?, ?)');
        $st->execute([$usuarioId, $ipHash, $cantidad]);
        $identificacionId = (int) $pdo->lastInsertId();
    } catch (Throwable $e) {
        error_log('[identificar] BD: ' . $e->getMessage());
        self::responder(500, ['error' => 'Ocurrió un problema interno. Intentá de nuevo más tarde.']);
    }

    /* ---------------- Consulta a PlantNet ---------------- */

    $limiteMultipart = '----mantirienda' . bin2hex(random_bytes(12));
    $url = 'https://my-api.plantnet.org/v2/identify/all?' . http_build_query([
        'api-key'                => $apiKey,
        'lang'                   => 'es',
        'nb-results'             => self::MAX_RESULTADOS,
        'include-related-images' => 'true',
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => self::armarMultipart($partes, $limiteMultipart),
        CURLOPT_HTTPHEADER     => ["Content-Type: multipart/form-data; boundary={$limiteMultipart}"],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT        => 30,
    ]);
    $respuesta = curl_exec($ch);
    $estado = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $errorCurl = curl_error($ch);
    curl_close($ch);

    if ($respuesta === false) {
        error_log('[identificar] cURL: ' . $errorCurl);
        self::responder(502, ['error' => 'No pudimos conectar con el servicio de identificación.']);
    }
    if ($estado === 404) {
        self::responder(200, ['identificacion_id' => $identificacionId, 'resultados' => []]);   // "Species not found"
    }
    if ($estado === 429) {
        error_log('[identificar] PlantNet: cuota diaria agotada.');
        self::responder(503, ['error' => 'El servicio está saturado. Probá de nuevo más tarde.']);
    }
    if ($estado !== 200) {
        error_log("[identificar] PlantNet HTTP {$estado}: " . substr((string) $respuesta, 0, 300));
        self::responder(502, ['error' => 'El servicio de identificación no respondió correctamente.']);
    }

    $datos = json_decode((string) $respuesta, true);
    if (!is_array($datos)) {
        self::responder(502, ['error' => 'Recibimos una respuesta inválida del servicio.']);
    }

    /* ---------------- Cruce con el catálogo ---------------- */

    $resultados = [];
    $plantaTopId = null;
    foreach (array_slice($datos['results'] ?? [], 0, self::MAX_RESULTADOS) as $n => $r) {
        $esp = $r['species'] ?? [];
        $cientifico = (string) ($esp['scientificNameWithoutAuthor'] ?? '');
        if ($cientifico === '') {
            continue;
        }
        $nombres = array_values(array_filter($esp['commonNames'] ?? [], 'is_string'));
        $imagen = $r['images'][0]['url']['s'] ?? ($r['images'][0]['url']['m'] ?? null);
        $catalogo = self::buscarEnCatalogo($pdo, $cientifico);

        if ($n === 0 && $catalogo) {
            $plantaTopId = $catalogo['id'];
        }
        $resultados[] = [
            'puntaje'       => round((float) ($r['score'] ?? 0), 4),
            'cientifico'    => $cientifico,
            'comun'         => $nombres[0] ?? null,
            'otros_nombres' => array_slice($nombres, 1, 2),
            'familia'       => $esp['family']['scientificNameWithoutAuthor'] ?? null,
            'imagen'        => is_string($imagen) && str_starts_with($imagen, 'https://') ? $imagen : null,
            'catalogo'      => $catalogo,
        ];
    }

    try {
        $top = $resultados[0] ?? null;
        $st = $pdo->prepare('UPDATE identificaciones SET especie_top = ?, puntaje_top = ?, planta_id = ? WHERE id = ?');
        $st->execute([$top['cientifico'] ?? null, $top['puntaje'] ?? null, $plantaTopId, $identificacionId]);
    } catch (Throwable $e) {
        error_log('[identificar] No se pudo actualizar el historial: ' . $e->getMessage());
    }

    self::responder(200, ['identificacion_id' => $identificacionId, 'resultados' => $resultados]);
  }
}
