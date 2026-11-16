<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Comprueba si el usuario tiene guardado un ID válido en su sesión
$idGamemaster = filter_var(
    $_SESSION['id_gamemaster'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if ($idGamemaster === false) {
    header('Location: ../index.php');
    exit();
}
