<?php require_once __DIR__ . '/../backend/cargar_salas.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salas</title>
    <link rel="icon" type="image/svg+xml" href="../assets/images/favicon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/styles/style.css?v=<?= filemtime(__DIR__ . '/../assets/styles/style.css') ?>">
</head>
<body class="pagina-salas">
    <?php include '../includes/header.php'; ?>

    <main class="salas">
        <!-- La búsqueda filtra al escribir (main.js). El tipo recarga la página -->
        <form id="filtro" class="filtros">
            <input type="search" id="buscador" class="buscador" name="q" placeholder="Buscar sala o módulo (7.1)" value="<?= htmlspecialchars($busqueda) ?>" autocomplete="off">
            <label class="pastilla"><input type="radio" name="tipo" value="" <?= $tipo === '' ? 'checked' : '' ?>> Todas</label>
            <?php foreach ($tipos as $valor => $texto) { ?>
                <label class="pastilla"><input type="radio" name="tipo" value="<?= $valor ?>" <?= $tipo === $valor ? 'checked' : '' ?>> <?= $texto ?></label>
            <?php } ?>
        </form>

        <!-- Mapa: cada sala es un enlace con su zona (SVG) y su pin -->
        <div class="mapa-hueco">
            <div class="mapa">
                <?php foreach ($salas as $id => $sala) { ?>
                    <a href="?tipo=<?= urlencode($tipo) ?>&q=<?= urlencode($busqueda) ?>&id_sala=<?= $id ?>"
                       class="sala <?= $id === $idSala ? 'activa' : '' ?> <?= $sala['apagada'] ? 'apagada' : '' ?>"
                       data-nombre="<?= htmlspecialchars($sala['nombre']) ?>"
                       data-tipo="<?= $sala['tipo'] ?>"
                       data-modulos="<?= implode(' ', array_column($sala['modulos'], 'modulo')) ?>">
                        <svg viewBox="0 0 1499 1049"><path d="<?= $sala['forma'] ?>"></path></svg>
                        <span class="pin" style="left: <?= $sala['pin'][0] ?>%; top: <?= $sala['pin'][1] ?>%">
                            <?= $sala['libres'] ?>/<?= count($sala['modulos']) ?>
                            <?php foreach ($sala['modulos'] as $modulo) { ?>
                                <span class="punto <?= $modulo['punto'] ?>"><?= $modulo['letra'] ?></span>
                            <?php } ?>
                        </span>
                    </a>
                <?php } ?>
            </div>
        </div>

        <!-- Panel de la sala abierta -->
        <section class="panel">
            <div class="panel-cabecera">
                <?php if ($salaAbierta) { ?>
                    <small>Sala <?= $idSala ?> · <?= $salaAbierta['tipo'] ?></small>
                    <h2><?= htmlspecialchars($salaAbierta['nombre']) ?></h2>
                    <p><?= $salaAbierta['libres'] ?> de <?= count($salaAbierta['modulos']) ?> módulos libres</p>
                <?php } else { ?>
                    <h2>Elige una sala</h2>
                    <p>Pulsa una sala del mapa para ver sus módulos.</p>
                <?php } ?>
            </div>

            <?php if ($aviso) { ?>
                <p class="aviso <?= $avisoError ? 'error' : '' ?>"><?= $aviso ?></p>
            <?php } ?>

            <?php if ($salaAbierta) { ?>
                <ul class="modulos">
                    <?php foreach ($salaAbierta['modulos'] as $modulo) { ?>
                        <li class="modulo">
                            <span class="punto <?= $modulo['punto'] ?>"><?= $modulo['letra'] ?></span>
                            <div class="modulo-info">
                                <b>Módulo <?= $modulo['modulo'] ?> · <?= $modulo['tipo_modulo'] ?></b>
                                <small><?= htmlspecialchars($modulo['detalle']) ?></small>
                            </div>

                            <?php if ($modulo['punto'] === 'libre') { ?>
                                <form action="../backend/ocupar_modulo.php" method="POST">
                                    <input type="hidden" name="id_modulo" value="<?= $modulo['id_modulo'] ?>">
                                    <input type="hidden" name="id_sala" value="<?= $idSala ?>">
                                    <?php if ($modulo['capacidad'] > 1) { ?>
                                        <!-- Empieza en 0 (vacío) y es obligatorio: hay que elegir cuántas personas son -->
                                        <select name="num_personas" required>
                                            <option value="">0</option>
                                            <?php for ($i = 1; $i <= $modulo['capacidad']; $i++) { ?>
                                                <option value="<?= $i ?>"><?= $i ?></option>
                                            <?php } ?>
                                        </select>
                                    <?php } else { ?>
                                        <input type="hidden" name="num_personas" value="1">
                                    <?php } ?>
                                    <button type="submit" class="btn-ocupar">Ocupar</button>
                                </form>
                            <?php } elseif ($modulo['punto'] === 'mio') { ?>
                                <form action="../backend/liberar_modulo.php" method="POST">
                                    <input type="hidden" name="id_modulo" value="<?= $modulo['id_modulo'] ?>">
                                    <input type="hidden" name="id_sala" value="<?= $idSala ?>">
                                    <button type="submit" class="btn-liberar">Liberar</button>
                                </form>
                            <?php } ?>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </section>
    </main>

    <script src="../assets/scripts/main.js?v=<?= filemtime(__DIR__ . '/../assets/scripts/main.js') ?>"></script>
</body>
</html>
