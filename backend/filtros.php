<?php
require_once __DIR__ . '/../sql/conexion.php';

$idSala = filter_input(INPUT_GET, 'id_sala', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$idModulo = filter_input(INPUT_GET, 'id_modulo', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($idSala === false) {
    $idSala = null;
}
if ($idModulo === false) {
    $idModulo = null;
}

$busqueda = $_GET['buscar'] ?? '';
if (!is_string($busqueda)) {
    $busqueda = '';
}
$busqueda = trim($busqueda);

$periodos = [
    'todas' => 'Todas las fechas',
    'hoy' => 'Hoy',
    'semana' => 'Últimos 7 días',
    'mes' => 'Último mes',
];
$periodo = $_GET['periodo'] ?? 'todas';
if (!is_string($periodo) || !array_key_exists($periodo, $periodos)) {
    $periodo = 'todas';
}

$estados = [
    'todos' => 'Todos los estados',
    'completadas' => 'Completadas',
    'encurso' => 'En curso',
];
$estado = $_GET['estado'] ?? 'todos';
if (!is_string($estado) || !array_key_exists($estado, $estados)) {
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

    // Cada columna usa su propio parámetro para que la búsqueda funcione en todas ellas.
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

$sql = 'SELECT o.id_ocupacion, o.fecha_inicio, o.fecha_fin, o.num_personas,
               m.nombre AS modulo, m.tipo AS tipo_modulo, m.capacidad,
               s.nombre AS sala, g.nom_real, g.cognom_real,
               TIMESTAMPDIFF(MINUTE, o.fecha_inicio, IFNULL(o.fecha_fin, NOW())) AS duracion_minutos
        FROM ocupaciones o
        JOIN modulos m ON m.id_modulo = o.id_modulo
        JOIN salas s ON s.id_sala = m.id_sala
        JOIN gamemasters g ON g.id_gamemaster = o.id_gamemaster';

if ($condiciones) {
    $sql .= ' WHERE ' . implode(' AND ', $condiciones);
}

$sql .= ' ORDER BY o.fecha_inicio DESC, o.id_ocupacion DESC';
$stmt = $conn->prepare($sql);
$stmt->execute($parametros);
$ocupaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);