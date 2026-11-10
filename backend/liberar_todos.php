<?php

// SOLO PARA TEST: libera todos los módulos ocupados por el gamemaster actual

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../sql/conexion.php';

$idSala = filter_input(INPUT_POST, 'id_sala', FILTER_VALIDATE_INT);
$volver = '../views/main.php?' . ($idSala ? 'id_sala=' . $idSala . '&' : '');

try {
    $conn->beginTransaction();

    // Primero los módulos (necesitamos las ocupaciones abiertas para saber cuáles son)
    $sql = "UPDATE modulos m
            JOIN ocupaciones o ON o.id_modulo = m.id_modulo
            SET m.estado = 'Disponible'
            WHERE o.id_gamemaster = :id_gamemaster AND o.fecha_fin IS NULL";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id_gamemaster' => $idGamemaster]);

    $sql = "UPDATE ocupaciones SET fecha_fin = NOW()
            WHERE id_gamemaster = :id_gamemaster AND fecha_fin IS NULL";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id_gamemaster' => $idGamemaster]);

    $conn->commit();
    header('Location: ' . $volver . 'ok=todos_liberados');
    exit();

} catch (PDOException $e) {
    $conn->rollBack();
    header('Location: ' . $volver . 'error=bd');
    exit();
}
