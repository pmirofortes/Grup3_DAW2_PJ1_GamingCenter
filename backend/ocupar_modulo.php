<?php

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../sql/conexion.php';

$idModulo = filter_input(INPUT_POST, 'id_modulo', FILTER_VALIDATE_INT);
$idSala   = filter_input(INPUT_POST, 'id_sala', FILTER_VALIDATE_INT);
$personas = filter_input(INPUT_POST, 'num_personas', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

// Volver a main con el panel de la sala abierto
$volver = '../views/main.php?' . ($idSala ? 'id_sala=' . $idSala . '&' : '');

if (!$idModulo) {
    header('Location: ' . $volver . 'error=modulo_invalido');
    exit();
}

// Si se salta el "required" del desplegable y llega 0 o vacío
if (!$personas) {
    header('Location: ' . $volver . 'error=sin_personas');
    exit();
}

// El formulario se puede trucar: comprobar que las personas caben en el módulo
$stmt = $conn->prepare("SELECT capacidad FROM modulos WHERE id_modulo = :id");
$stmt->execute([':id' => $idModulo]);
$capacidad = $stmt->fetchColumn();

if ($capacidad === false || $personas > $capacidad) {
    header('Location: ' . $volver . 'error=num_personas');
    exit();
}

try {
    $conn->beginTransaction();

    // Solo cambia el estado si el módulo sigue libre. Comprobar y ocupar en la misma
    // sentencia evita que dos gamemasters ocupen el mismo módulo a la vez.
    $sql = "UPDATE modulos SET estado = 'Ocupado' WHERE id_modulo = :id AND estado = 'Disponible'";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $idModulo]);

    if ($stmt->rowCount() === 0) {
        // No existe o ya estaba ocupado
        $conn->rollBack();
        header('Location: ' . $volver . 'error=modulo_ocupado');
        exit();
    }

    $sql = "INSERT INTO ocupaciones (fecha_inicio, num_personas, id_modulo, id_gamemaster)
            VALUES (NOW(), :num_personas, :id_modulo, :id_gamemaster)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':num_personas'  => $personas,
        ':id_modulo'     => $idModulo,
        ':id_gamemaster' => $idGamemaster,
    ]);

    $conn->commit();
    header('Location: ' . $volver . 'ok=modulo_ocupado');
    exit();

} catch (PDOException $e) {
    $conn->rollBack();
    header('Location: ' . $volver . 'error=bd');
    exit();
}
