<?php

$servername = 'localhost';
$dbname = 'db_pj5_gamingCenter';
$dbusername = 'root';
$dbpassword = '';

try {
    // PDO centraliza la conexión y permite usar consultas preparadas en el proyecto.
    $conn = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8mb4",
        $dbusername,
        $dbpassword
    );

    // Las consultas fallidas lanzan una excepción que se puede gestionar.
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error en la conexión a la base de datos: " . $e->getMessage();
    die();
}