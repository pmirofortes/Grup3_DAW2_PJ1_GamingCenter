<?php

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../sql/conexion.php';

/** @var PDO $conn  (se crea en conexion.php) */

$sql = "SELECT COUNT(*) AS totalSalas FROM salas";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$totalSalas = $result['totalSalas'];

// Salas con sus módulos (LEFT JOIN para que salgan también las salas sin módulos)
// y el gamemaster de la ocupación abierta de cada módulo, si la hay
$sql = "SELECT s.id_sala, s.nombre AS sala, s.tipo AS tipo_sala,
               m.id_modulo, m.nombre AS modulo, m.tipo AS tipo_modulo, m.capacidad, m.estado,
               o.id_gamemaster AS ocupado_por, o.num_personas
        FROM salas s
        LEFT JOIN modulos m ON m.id_sala = s.id_sala
        LEFT JOIN ocupaciones o ON o.id_modulo = m.id_modulo AND o.fecha_fin IS NULL
        ORDER BY s.id_sala, m.id_modulo";
$stmt = $conn->prepare($sql);
$stmt->execute();
$filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agrupar los módulos por sala y contar los libres
$salas = [];
foreach ($filas as $fila) {
    $id = $fila['id_sala'];

    if (!isset($salas[$id])) {
        $salas[$id] = [
            'nombre'  => $fila['sala'],
            'tipo'    => $fila['tipo_sala'],
            'modulos' => [],
            'libres'  => 0,
        ];
    }

    if ($fila['id_modulo'] !== null) {
        $salas[$id]['modulos'][] = $fila;

        if ($fila['estado'] === 'Disponible') {
            $salas[$id]['libres']++;
        }
    }
}

// Filtro por tipo de sala (?tipo=...). Clave = valor en la BD, valor = texto del radio
$tipos = [
    'PC Standard'          => 'PC Standard',
    'Simuladores/Consolas' => 'Simuladores',
    'VIP'                  => 'VIP',
];
$tipo = $_GET['tipo'] ?? '';
if (!isset($tipos[$tipo])) {
    $tipo = ''; // Todas
}

// Búsqueda por nombre de sala (?q=...)
$busqueda = trim($_GET['q'] ?? '');

$salasFiltradas = [];
foreach ($salas as $id => $sala) {
    $coincideTipo = $tipo === '' || $sala['tipo'] === $tipo;
    $coincideNombre = $busqueda === '' || mb_stripos($sala['nombre'], $busqueda) !== false;

    if ($coincideTipo && $coincideNombre) {
        $salasFiltradas[$id] = $sala;
    }
}

// Sala abierta en el panel derecho (?id_sala=X)
$idSala = filter_input(INPUT_GET, 'id_sala', FILTER_VALIDATE_INT);
$salaAbierta = ($idSala && isset($salas[$idSala])) ? $salas[$idSala] : null;

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/styles/style.css?v=2.9">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <?php
    $mensajes = [
        'modulo_ocupado'   => 'Módulo ocupado correctamente.',
        'modulo_liberado'  => 'Módulo liberado correctamente.',
    ];
    $errores = [
        'modulo_ocupado'   => 'Ese módulo ya está ocupado.',
        'no_es_tuyo'       => 'Solo puedes liberar los módulos que has ocupado tú.',
        'num_personas'     => 'El número de personas no cabe en ese módulo.',
    ];

    if (isset($_GET['ok'], $mensajes[$_GET['ok']])) { ?>
        <p><?= $mensajes[$_GET['ok']] ?></p>
    <?php } elseif (isset($_GET['error'])) { ?>
        <p><?= $errores[$_GET['error']] ?? 'Ha ocurrido un error, inténtalo de nuevo.' ?></p>
    <?php } ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Filtro: el nombre se filtra en el navegador al escribir; el tipo se envía al cambiar de opción -->
            <form id="filtro">
                <input type="search" id="buscador" name="q" placeholder="Buscar sala..." value="<?= htmlspecialchars($busqueda) ?> " autocomplete="off">
                <label><input type="radio" name="tipo" value="" <?= $tipo === '' ? 'checked' : '' ?>> Todas</label>
                <?php foreach ($tipos as $valor => $texto) { ?>
                    <label><input type="radio" name="tipo" value="<?= $valor ?>" <?= $tipo === $valor ? 'checked' : '' ?>> <?= $texto ?></label>
                <?php } ?>
            </form>

            <!-- IZQUIERDA (66%): salas -->
            <div class="col-lg-8">
                <p>Número total de salas: <?= $totalSalas ?></p>

                <ul id="lista-salas">
                    <?php foreach ($salasFiltradas as $id => $sala) { ?>
                        <li data-nombre="<?= htmlspecialchars($sala['nombre']) ?>">
                            <a href="?tipo=<?= urlencode($tipo) ?>&q=<?= urlencode($busqueda) ?>&id_sala=<?= $id ?>">
                                <?= htmlspecialchars($sala['nombre']) ?> (<?= htmlspecialchars($sala['tipo']) ?>)
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>

            <!-- DERECHA (33%): panel de la sala abierta -->
            <?php if ($salaAbierta) { ?>
                <div class="col-lg-4">
                    <h2><?= htmlspecialchars($salaAbierta['nombre']) ?></h2>
                    <p><?= $salaAbierta['libres'] ?>/<?= count($salaAbierta['modulos']) ?> módulos libres</p>

                    <?php if (empty($salaAbierta['modulos'])) { ?>
                        <p>Esta sala no tiene módulos.</p>
                    <?php } else { ?>
                        <ul>
                            <?php foreach ($salaAbierta['modulos'] as $modulo) { ?>
                                <li>
                                    Módulo <?= htmlspecialchars($modulo['modulo']) ?> ·
                                    <?= htmlspecialchars($modulo['tipo_modulo']) ?> ·
                                    Capacidad: <?= $modulo['capacidad'] ?> ·
                                    <?= htmlspecialchars($modulo['estado']) ?>
                                    <?php if ($modulo['num_personas'] !== null) { ?>
                                        (<?= $modulo['num_personas'] ?> de <?= $modulo['capacidad'] ?> personas)
                                    <?php } ?>

                                    <?php if ($modulo['estado'] === 'Disponible') { ?>
                                        <form action="../backend/ocupar_modulo.php" method="POST">
                                            <input type="hidden" name="id_modulo" value="<?= $modulo['id_modulo'] ?>">
                                            <input type="hidden" name="id_sala" value="<?= $idSala ?>">

                                            <?php if ($modulo['capacidad'] > 1) { ?>
                                                <label>
                                                    Personas:
                                                    <select name="num_personas">
                                                        <?php for ($i = 1; $i <= $modulo['capacidad']; $i++) { ?>
                                                            <option value="<?= $i ?>"><?= $i ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </label>
                                            <?php } else { ?>
                                                <input type="hidden" name="num_personas" value="1">
                                            <?php } ?>

                                            <button type="submit">Ocupar</button>
                                        </form>
                                    <?php } elseif ($modulo['ocupado_por'] == $idGamemaster) { ?>
                                        <form action="../backend/liberar_modulo.php" method="POST">
                                            <input type="hidden" name="id_modulo" value="<?= $modulo['id_modulo'] ?>">
                                            <input type="hidden" name="id_sala" value="<?= $idSala ?>">
                                            <button type="submit">Liberar</button>
                                        </form>
                                    <?php } ?>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </div>
            <?php } ?>

        </div>
    </div>

    <script src="../assets/scripts/main.js?v=<?= filemtime(__DIR__ . '/../assets/scripts/main.js') ?>"></script>
</body>
</html>
