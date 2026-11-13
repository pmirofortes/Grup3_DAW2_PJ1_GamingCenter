<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/../sql/conexion.php';

// Contadores de la cabecera, cuántas salas y plazas hay en total y cuántas libres
// salas distintas que tienen módulos DISTINCT porque una sala tiene varios módulos
// plazas: suma de la capacidad de los módulos
$sql = "SELECT COUNT(DISTINCT id_sala) AS salas, SUM(capacidad) AS plazas FROM modulos";
$stmt = $conn->prepare($sql);
$stmt->execute();
$total = $stmt->fetch(PDO::FETCH_ASSOC);

// Repetimos el cálculo con los módulos disponibles para mostrar las plazas libres.
$sql = "SELECT COUNT(DISTINCT id_sala) AS salas, SUM(capacidad) AS plazas FROM modulos WHERE estado = 'Disponible'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$libres = $stmt->fetch(PDO::FETCH_ASSOC);

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
            <b><?= $libres['salas'] ?><span>/<?= $total['salas'] ?></span></b>
        </div>
        <div class="contador">
            <small>Plazas<span class="libres"> libres</span></small>
            <!-- (int): si no queda ningún módulo libre, SUM no tiene nada que sumar y hay que mostrar 0 -->
            <b><?= (int) $libres['plazas'] ?><span>/<?= $total['plazas'] ?></span></b>
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
