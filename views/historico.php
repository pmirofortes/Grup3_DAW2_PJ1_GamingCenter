<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../sql/conexion.php';

$idSala = filter_input(INPUT_GET, 'id_sala', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$idModulo = filter_input(INPUT_GET, 'id_modulo', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$idSala = ($idSala === false || $idSala === null) ? null : $idSala;
$idModulo = ($idModulo === false || $idModulo === null) ? null : $idModulo;

$busqueda = $_GET['buscar'] ?? '';
if (!is_string($busqueda)) {
    $busqueda = '';
}
$busqueda = trim($busqueda);

$periodos = ['todas', 'hoy', 'semana', 'mes'];
$periodo = $_GET['periodo'] ?? 'todas';
if (!is_string($periodo) || !in_array($periodo, $periodos, true)) {
    $periodo = 'todas';
}

$estados = ['todos', 'completadas', 'encurso'];
$estado = $_GET['estado'] ?? 'todos';
if (!is_string($estado) || !in_array($estado, $estados, true)) {
    $estado = 'todos';
}

$stmt = $conn->query('SELECT id_sala, nombre FROM salas ORDER BY nombre');
$salas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->query(
    'SELECT m.id_modulo, m.nombre AS modulo, s.nombre AS sala
     FROM modulos m
     INNER JOIN salas s ON s.id_sala = m.id_sala
     ORDER BY s.nombre, m.nombre'
);
$modulos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$condiciones = [];
$parametros = [];
if ($idSala !== null) {
    $condiciones[] = 's.id_sala = :id_sala';
    $parametros[':id_sala'] = $idSala;
}
if ($idModulo !== null) {
    $condiciones[] = 'm.id_modulo = :id_modulo';
    $parametros[':id_modulo'] = $idModulo;
}
if ($busqueda !== '') {
    $condiciones[] = "(CONCAT('#R-', LPAD(o.id_ocupacion, 3, '0')) LIKE :buscar_id
                       OR s.nombre LIKE :buscar_sala
                       OR m.nombre LIKE :buscar_modulo
                       OR m.tipo LIKE :buscar_tipo
                       OR CONCAT(g.nom_real, ' ', g.cognom_real) LIKE :buscar_responsable)";
    $terminoBusqueda = '%' . $busqueda . '%';
    $parametros[':buscar_id'] = $terminoBusqueda;
    $parametros[':buscar_sala'] = $terminoBusqueda;
    $parametros[':buscar_modulo'] = $terminoBusqueda;
    $parametros[':buscar_tipo'] = $terminoBusqueda;
    $parametros[':buscar_responsable'] = $terminoBusqueda;
}
if ($periodo === 'hoy') {
    $condiciones[] = 'o.fecha_inicio >= CURRENT_DATE';
} elseif ($periodo === 'semana') {
    $condiciones[] = 'o.fecha_inicio >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)';
} elseif ($periodo === 'mes') {
    $condiciones[] = 'o.fecha_inicio >= DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH)';
}
if ($estado === 'completadas') {
    $condiciones[] = 'o.fecha_fin IS NOT NULL';
} elseif ($estado === 'encurso') {
    $condiciones[] = 'o.fecha_fin IS NULL';
}
$filtroSql = $condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '';

$sql = "SELECT o.id_ocupacion, o.fecha_inicio, o.fecha_fin, o.num_personas,
               m.nombre AS modulo, m.tipo AS tipo_modulo, m.capacidad,
               s.nombre AS sala, g.nom_real, g.cognom_real,
               TIMESTAMPDIFF(MINUTE, o.fecha_inicio, COALESCE(o.fecha_fin, NOW())) AS duracion_minutos
        FROM ocupaciones o
        INNER JOIN modulos m ON m.id_modulo = o.id_modulo
        INNER JOIN salas s ON s.id_sala = m.id_sala
        INNER JOIN gamemasters g ON g.id_gamemaster = o.id_gamemaster
        $filtroSql
        ORDER BY o.fecha_inicio DESC, o.id_ocupacion DESC";
$stmt = $conn->prepare($sql);
$stmt->execute($parametros);
$ocupaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

$ocupacionesPorFecha = [];
foreach ($ocupaciones as $ocupacion) {
    $fecha = substr($ocupacion['fecha_inicio'], 0, 10);
    $ocupacionesPorFecha[$fecha][] = $ocupacion;
}

function formatoDuracion($minutos)
{
    $minutos = (int) $minutos;
    $horas = intdiv($minutos, 60);
    $resto = $minutos % 60;

    if ($horas === 0) {
        return $resto . ' min';
    }

    return $horas . ' h' . ($resto > 0 ? ' ' . str_pad((string) $resto, 2, '0', STR_PAD_LEFT) . ' min' : '');
}

function formatoFechaGrupo($fecha)
{
    $meses = [
        1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
        'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre',
    ];
    $timestamp = strtotime($fecha);

    $dia = str_pad((string) date('j', $timestamp), 2, '0', STR_PAD_LEFT);
    return $dia . '. ' . $meses[(int) date('n', $timestamp)] . ' ' . date('Y', $timestamp);
}

$clasesSalas = [
    'Downtown' => 'downtown',
    'Little Haiti' => 'little-haiti',
    'Little Havana' => 'little-havana',
    'Escobar International Airport' => 'escobar-airport',
    'Leaf Links' => 'leaf-links',
    'Starfish Island' => 'starfish-island',
    'Ocean Beach' => 'ocean-beach',
    'Vice Point' => 'vice-point',
    'Prawn Island' => 'prawn-island',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi historial | Gaming Center</title>
    <link rel="icon" type="image/svg+xml" href="../assets/images/favicon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/styles/style.css?v=<?= filemtime(__DIR__ . '/../assets/styles/style.css') ?>">
</head>
<body class="historial-page">
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="container historial-main">
        <h1 class="historial-titulo">Mi historial</h1>
        <p class="historial-subtitulo">Todo lo que has reservado en el Gaming Center</p>

        <section class="card historial-panel" aria-label="Historial de reservas">
            <form method="get" action="historico.php" class="historial-filtros">
                <div>
                    <label class="visually-hidden" for="buscar">Buscar reservas</label>
                    <input class="form-control historial-filtro" type="search" id="buscar" name="buscar"
                           placeholder="Buscar reserva..." value="<?= htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div>
                    <label class="visually-hidden" for="periodo">Filtrar por fecha</label>
                    <select class="form-select historial-filtro" id="periodo" name="periodo" onchange="this.form.submit()">
                        <option value="todas" <?= $periodo === 'todas' ? 'selected' : '' ?>>Todas las fechas</option>
                        <option value="hoy" <?= $periodo === 'hoy' ? 'selected' : '' ?>>Hoy</option>
                        <option value="semana" <?= $periodo === 'semana' ? 'selected' : '' ?>>Últimos 7 días</option>
                        <option value="mes" <?= $periodo === 'mes' ? 'selected' : '' ?>>Último mes</option>
                    </select>
                </div>
                <div>
                    <label class="visually-hidden" for="id_sala">Filtrar por sala</label>
                    <select class="form-select historial-filtro" id="id_sala" name="id_sala" onchange="this.form.submit()">
                        <option value="">Todas las salas</option>
                        <?php foreach ($salas as $sala) { ?>
                            <option value="<?= (int) $sala['id_sala'] ?>" <?= $idSala === (int) $sala['id_sala'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sala['nombre'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <label class="visually-hidden" for="id_modulo">Filtrar por módulo</label>
                    <select class="form-select historial-filtro" id="id_modulo" name="id_modulo" onchange="this.form.submit()">
                        <option value="">Todos los módulos</option>
                        <?php foreach ($modulos as $modulo) { ?>
                            <option value="<?= (int) $modulo['id_modulo'] ?>" <?= $idModulo === (int) $modulo['id_modulo'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($modulo['modulo'] . ' · ' . $modulo['sala'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <label class="visually-hidden" for="estado">Filtrar por estado</label>
                    <select class="form-select historial-filtro" id="estado" name="estado" onchange="this.form.submit()">
                        <option value="todos" <?= $estado === 'todos' ? 'selected' : '' ?>>Todos los estados</option>
                        <option value="completadas" <?= $estado === 'completadas' ? 'selected' : '' ?>>Completadas</option>
                        <option value="encurso" <?= $estado === 'encurso' ? 'selected' : '' ?>>En curso</option>
                    </select>
                </div>
            </form>

            <?php if (empty($ocupacionesPorFecha)) { ?>
                <p class="historial-vacio mb-0">No hay reservas que coincidan con los filtros seleccionados.</p>
            <?php } else { ?>
                <div class="historial-listado">
                    <?php foreach ($ocupacionesPorFecha as $fecha => $reservas) { ?>
                        <section class="historial-grupo" aria-label="Reservas del <?= htmlspecialchars(formatoFechaGrupo($fecha), ENT_QUOTES, 'UTF-8') ?>">
                            <h2 class="historial-fecha"><?= htmlspecialchars(formatoFechaGrupo($fecha), ENT_QUOTES, 'UTF-8') ?></h2>
                            <div class="historial-reservas">
                                <?php foreach ($reservas as $reserva) {
                                    $enCurso = $reserva['fecha_fin'] === null;
                                    $horaInicio = date('H:i', strtotime($reserva['fecha_inicio']));
                                    $horaFin = $enCurso ? null : date('H:i', strtotime($reserva['fecha_fin']));
                                    $nombreResponsable = $reserva['nom_real'] . ' ' . $reserva['cognom_real'];
                                    $idReserva = '#R-' . str_pad((string) $reserva['id_ocupacion'], 3, '0', STR_PAD_LEFT);
                                    $claseSala = $clasesSalas[$reserva['sala']] ?? 'otra';
                                ?>
                                    <article class="historial-reserva" aria-label="Reserva <?= htmlspecialchars($idReserva, ENT_QUOTES, 'UTF-8') ?>">
                                        <div class="historial-reserva-id">
                                            <span class="visually-hidden">Reserva: </span>
                                            <span class="historial-id"><?= htmlspecialchars($idReserva, ENT_QUOTES, 'UTF-8') ?></span>
                                        </div>
                                        <div class="historial-reserva-horario">
                                            <span class="visually-hidden">Horario: </span>
                                            <span class="historial-horario"><?= htmlspecialchars($horaInicio . ($horaFin !== null ? ' – ' . $horaFin : ''), ENT_QUOTES, 'UTF-8') ?></span>
                                            <span class="historial-secundario"><?= htmlspecialchars(formatoDuracion($reserva['duracion_minutos']), ENT_QUOTES, 'UTF-8') ?></span>
                                        </div>
                                        <div class="historial-reserva-sala">
                                            <span class="visually-hidden">Sala: </span>
                                            <span class="historial-sala <?= htmlspecialchars($claseSala, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($reserva['sala'], ENT_QUOTES, 'UTF-8') ?></span>
                                        </div>
                                        <div class="historial-reserva-modulo">
                                            <span class="visually-hidden">Modalidad: </span>
                                            <span class="historial-modulo-tipo"><?= htmlspecialchars($reserva['tipo_modulo'], ENT_QUOTES, 'UTF-8') ?></span>
                                            <span class="historial-secundario"><?= (int) $reserva['num_personas'] ?> de <?= (int) $reserva['capacidad'] ?> plazas</span>
                                        </div>
                                        <div class="historial-reserva-responsable">
                                            <span class="visually-hidden">Responsable: </span>
                                            <?= htmlspecialchars($nombreResponsable, ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                        <div class="historial-reserva-estado">
                                            <span class="visually-hidden">Estado: </span>
                                            <span class="historial-estado <?= $enCurso ? 'encurso' : '' ?>"><?= $enCurso ? 'En curso' : 'Completada' ?></span>
                                        </div>
                                    </article>
                                <?php } ?>
                            </div>
                        </section>
                    <?php } ?>
                </div>
            <?php } ?>
        </section>
    </main>
</body>
</html>
