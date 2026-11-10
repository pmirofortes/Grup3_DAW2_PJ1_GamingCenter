<?php
session_start();

// Ajusta la ruta de conexion.php según dónde esté guardado 
require_once '../sql/conexion.php';

// Obtener todas las salas y calcular su capacidad total sumando la capacidad de sus módulos
$sqlSalas = "SELECT s.id_sala, s.nombre, s.tipo,
                    COALESCE(
                        (SELECT SUM(m.capacidad)
                         FROM modulos m
                         WHERE m.id_sala = s.id_sala),
                        0
                    ) AS capacidad_total
             FROM salas s";
$stmtSalas = $conn->prepare($sqlSalas);
$stmtSalas->execute();
$salas = $stmtSalas->fetchAll(PDO::FETCH_ASSOC);

// Preparar la consulta para obtener los módulos de cada sala 
$sqlModulos = "SELECT m.*,
                      (SELECT o.id_gamemaster
                       FROM ocupaciones o
                       WHERE o.id_modulo = m.id_modulo AND o.fecha_fin IS NULL
                       LIMIT 1) AS gm_ocupando,
                      (SELECT g.username
                       FROM gamemasters g
                       WHERE g.id_gamemaster = (
                           SELECT o.id_gamemaster
                           FROM ocupaciones o
                           WHERE o.id_modulo = m.id_modulo AND o.fecha_fin IS NULL
                           LIMIT 1
                       )) AS nombre_gm
               FROM modulos m
               WHERE m.id_sala = ?";
$stmtModulos = $conn->prepare($sqlModulos);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gaming Center - Salas</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Anton y Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <!-- Barra Superior -->
    <nav class="navbar_gaming">
        <div class="container d-flex justify-content-between align-items-center">
            <span class="logo_navbar">gaming center</span>
            <div class="d-flex align-items-center gap-3">
                <a href="historico.php" class="btn_nav_secundario">Histórico</a>
                <span class="usuario_activo">
                    GM: <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'Invitado'); ?></strong>
                </span>
                <a href="../logout.php" class="btn_cerrar_sesion">Salir</a>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <main class="container py-4">
        <div class="encabezado_seccion mb-4">
            <small class="texto_panel text-light opacity-75">Panel de control en vivo</small>
            <h1 class="titulo_seccion">ESTADO DE LAS SALAS</h1>
        </div>

        <div class="row g-4">
            <?php foreach ($salas as $sala): ?>
                <?php
                // Asignar color de cabecera según el tipo de sala 
                $claseCabecera = 'cabecera_pc';
                if ($sala['tipo'] === 'Simuladores/Consolas') {
                    $claseCabecera = 'cabecera_consolas';
                } elseif ($sala['tipo'] === 'VIP') {
                    $claseCabecera = 'cabecera_vip';
                }

                // Ejecutar consulta de módulos para esta sala
                $stmtModulos->execute([$sala['id_sala']]);
                $modulos = $stmtModulos->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <div class="col-12 col-md-6 col-xl-4">
                    <div class="tarjeta_sala">
                        <!-- Cabecera  -->
                        <div class="cabecera_sala <?php echo $claseCabecera; ?>">
                            <div class="d-flex justify-content-between align-items-center">
                                <small><?php echo htmlspecialchars($sala['tipo']); ?></small>
                                <span class="badge_capacidad">Capacidad: <?php echo $sala['capacidad_total']; ?></span>
                            </div>
                            <h3><?php echo htmlspecialchars($sala['nombre']); ?></h3>
                        </div>

                        <!-- Lista de Módulos de la Sala -->
                        <div class="cuerpo_sala">
                            <?php foreach ($modulos as $mod): ?>
                                <?php $estaOcupado = ($mod['estado'] === 'Ocupado'); ?>

                                <div class="fila_modulo <?php echo $estaOcupado ? 'modulo_ocupado' : 'modulo_disponible'; ?>">
                                    <div>
                                        <div class="titulo_modulo">
                                            Módulo <?php echo htmlspecialchars($mod['nombre']); ?>
                                            <span class="tipo_modulo"><?php echo htmlspecialchars($mod['tipo']); ?></span>
                                        </div>
                                        <small class="info_modulo">
                                            <?php echo $mod['capacidad']; ?> <?php echo ($mod['capacidad'] == 1) ? 'lugar' : 'lugares'; ?>
                                            <?php if ($estaOcupado && !empty($mod['nombre_gm'])): ?>
                                                • Ocupado por: <strong><?php echo htmlspecialchars($mod['nombre_gm']); ?></strong>
                                            <?php endif; ?>
                                        </small>
                                    </div>

                                    <!-- Botones de Acción -->
                                    <div>
                                        <?php if (!$estaOcupado): ?>
                                            <form action="../controllers/ocupar.php" method="POST" class="d-flex align-items-center gap-1">
                                                <input type="hidden" name="id_modulo" value="<?php echo $mod['id_modulo']; ?>">
                                                <input type="number" name="num_jugadores" class="input_jugadores" min="1" max="<?php echo $mod['capacidad']; ?>" placeholder="Jug." required title="Número de jugadores (máx <?php echo $mod['capacidad']; ?>)">
                                                <button type="submit" class="btn_accion btn_ocupar">Ocupar</button>
                                            </form>
                                        <?php else: ?>
                                            <?php 
                                            // El enunciado pide que SOLO el gamemaster que lo ocupó pueda liberarlo
                                            $idGmActual = $_SESSION['id_gamemaster'] ?? 0;
                                            if ($mod['gm_ocupando'] == $idGmActual): 
                                            ?>
                                                <form action="../controllers/liberar.php" method="POST">
                                                    <input type="hidden" name="id_modulo" value="<?php echo $mod['id_modulo']; ?>">
                                                    <button type="submit" class="btn_accion btn_liberar">Liberar</button>
                                                </form>
                                            <?php else: ?>
                                                <span class="badge_bloqueado" title="Solo <?php echo htmlspecialchars($mod['nombre_gm'] ?? 'el GM que lo ocupó'); ?> puede liberarlo">Ocupado</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

</body>
</html>