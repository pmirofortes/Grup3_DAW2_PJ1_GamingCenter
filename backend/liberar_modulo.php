<?php

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../sql/conexion.php';

$idModulo = filter_input(INPUT_POST, 'id_modulo', FILTER_VALIDATE_INT);
$idSala   = filter_input(INPUT_POST, 'id_sala', FILTER_VALIDATE_INT);

// Volver a main con el panel de la sala abierto
$volver = '../views/main.php?' . ($idSala ? 'id_sala=' . $idSala . '&' : '');

if (!$idModulo) {
    header('Location: ' . $volver . 'error=modulo_invalido');
    exit();
}

try {
    $conn->beginTransaction();

    // Cerrar la ocupación abierta solo si es del gamemaster actual
    $sql = "UPDATE ocupaciones SET fecha_fin = NOW()
            WHERE id_modulo = :id_modulo AND id_gamemaster = :id_gamemaster AND fecha_fin IS NULL";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':id_modulo'     => $idModulo,
        ':id_gamemaster' => $idGamemaster,
    ]);

    if ($stmt->rowCount() === 0) {
        // No está ocupado o lo ocupó otro gamemaster
        $conn->rollBack();
        header('Location: ' . $volver . 'error=no_es_tuyo');
        exit();
    }

    $sql = "UPDATE modulos SET estado = 'Disponible' WHERE id_modulo = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $idModulo]);

    $conn->commit();
    header('Location: ' . $volver . 'ok=modulo_liberado');
    exit();

} catch (PDOException $e) {
    $conn->rollBack();
    header('Location: ' . $volver . 'error=bd');
    exit();
}
