<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../backend/filtros.php';

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
                        <?php foreach ($periodos as $valor => $etiqueta) { ?>
                            <option value="<?= $valor ?>" <?= $periodo === $valor ? 'selected' : '' ?>><?= $etiqueta ?></option>
                        <?php } ?>
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
                        <?php foreach ($estados as $valor => $etiqueta) { ?>
                            <option value="<?= $valor ?>" <?= $estado === $valor ? 'selected' : '' ?>><?= $etiqueta ?></option>
                        <?php } ?>
                    </select>
                </div>
            </form>

            <?php if (empty($ocupacionesPorFecha)) { ?>
                <p class="historial-vacio mb-0">No hay reservas que coincidan con los filtros seleccionados.</p>
            <?php } else { ?>
                <div class="historial-listado">
                    <?php foreach ($ocupacionesPorFecha as $fecha => $reservas) { ?>
                        <?php $fechaFormateada = formatoFechaGrupo($fecha); ?>
                        <section class="historial-grupo" aria-label="Reservas del <?= htmlspecialchars($fechaFormateada, ENT_QUOTES, 'UTF-8') ?>">
                            <h2 class="historial-fecha"><?= htmlspecialchars($fechaFormateada, ENT_QUOTES, 'UTF-8') ?></h2>
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