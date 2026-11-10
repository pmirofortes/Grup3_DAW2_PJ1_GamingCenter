<?php
include '../sql/conexion.php';

// consulta sql para obtener el numero de salas y plazas libres


?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>
</head>
<body>
    <navbar>
        <ul>
            <li>logo grand theft team: code city</li>
            <li>Salas libres <span></span></li>
            <li>Plazas libres <span></span></li>
            <li><?php echo date("H:i"); ?></li>
            <li><a href="logout.php">Cerrar sesión</a></li>
        </ul>
    </navbar>
</body>
</html>