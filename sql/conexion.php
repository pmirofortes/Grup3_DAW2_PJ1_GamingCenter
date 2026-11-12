<?php

$servername = "db"; // Nombre del servidor
$dbusername = "nahuel"; // Nombre de usuario
$dbpassword = "95f97h0c"; // Contraseña
$dbname = "db_pj5_gamingCenter"; // Nombre de la base de datos

try {
    // Se crea una instancia de PDO para establecer la conexión
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $dbusername, $dbpassword);

    // Se configura PDO para lanzar excepciones en caso de error
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error en la conexión a la base de datos: " . $e->getMessage();
    die();
}