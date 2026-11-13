<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/../sql/conexion.php';

// Salas libres: salas con al menos un módulo disponible
// Plazas libres: capacidad de los módulos disponibles
$sql = "SELECT COUNT(DISTINCT s.id_sala) AS salas_total, COUNT(DISTINCT CASE WHEN m.estado = 'Disponible' THEN s.id_sala END) AS salas_libres, COALESCE(SUM(m.capacidad), 0) AS plazas_total, COALESCE(SUM(CASE WHEN m.estado = 'Disponible' THEN m.capacidad END), 0) AS plazas_libres
        FROM salas s
        LEFT JOIN modulos m ON m.id_sala = s.id_sala";
$stmt = $conn->prepare($sql);
$stmt->execute();
$resumen = $stmt->fetch(PDO::FETCH_ASSOC);

// Gamemaster conectado
$sql = "SELECT nom_real, cognom_real FROM gamemasters WHERE id_gamemaster = :id";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $idGamemaster]);
$gamemaster = $stmt->fetch(PDO::FETCH_ASSOC);

$nombre = $gamemaster['nom_real'] . ' ' . $gamemaster['cognom_real'];
?>


<header class="header">
    <a href="main.php" class="logo">
        <picture>
            <source media="(max-width: 767px)" srcset="../assets/images/logo_movil.svg">
            <img src="../assets/images/logo.svg" alt="Grand Theft Team: Code City">
        </picture>
    </a>

    <div class="contadores">
        <div class="contador">
            <small>Salas<span class="libres"> libres</span></small>
            <b><?= $resumen['salas_libres'] ?><span>/<?= $resumen['salas_total'] ?></span></b>
        </div>
        <div class="contador">
            <small>Plazas<span class="libres"> libres</span></small>
            <b><?= $resumen['plazas_libres'] ?><span>/<?= $resumen['plazas_total'] ?></span></b>
        </div>
    </div>

    <div class="usuario">
        <b><?= htmlspecialchars($nombre) ?></b>
        <small>Gamemaster</small>
    </div>

    <div class="acciones">
        <a href="historico.php" class="btn-header btn-icono" title="Histórico">
            <img src="../assets/images/historico.png" alt="Histórico">
        </a>
        <a href="../backend/logout.php" class="btn-header" title="Cerrar sesión">
            <img src="../assets/images/logout.png" alt="">
            <span>Cerrar sesión</span>
        </a>
    </div>
</header>
