<?php 

    $servername = "mysql"; // Nombre del servicio MySQL dentro de Docker
    $dbport = 3306; // Puerto de MySQL
    $dbusername = "daw"; // Nombre de usuario 
    $dbpassword = "DAW2026"; // Contraseña 
    $dbname = "db_gamingCenter"; // Nombre de la base de datos 

try { 
     // Se crea una instancia de la clase PDO para establecer la conexión 
    $conn = new PDO(
        "mysql:host=$servername;port=$dbport;dbname=$dbname;charset=utf8mb4",$dbusername,$dbpassword
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

} catch (PDOException $e) { 

    // Se captura la excepción y se muestra el mensaje de error 
    echo "Error en la conexión a la base de datos: " . $e->getMessage(); 
    die(); 

}
