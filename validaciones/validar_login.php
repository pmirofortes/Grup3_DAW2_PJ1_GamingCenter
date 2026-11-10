<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    // La sesión permitirá recordar quién ha iniciado sesión.
    session_start();
}

$error = "";
$mensaje = "";
$usuario = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recogemos los datos enviados y limpiamos los espacios sobrantes del usuario
    $usuario = trim($_POST["usuario"] ?? "");
    $password = $_POST["password"] ?? "";

    // Comprobamos los campos también en el servidor ya que el JavaScript se puede desactivar
    if ($usuario === "" || $password === "") {
        $error = "El usuario y la contraseña son obligatorios.";
    } elseif (strlen($usuario) < 3 || strlen($usuario) > 50) {
        $error = "El usuario debe tener entre 3 y 50 caracteres.";
    } else {
        // La consulta preparada evita insertar directamente el usuario en el SQL
        require_once __DIR__ . "/../conexion.php";

        try {
            $consulta = $conn->prepare(
                "SELECT id_gamemaster, username, password
                 FROM gamemasters
                 WHERE username = :usuario
                 LIMIT 1"
            );
            $consulta->execute(["usuario" => $usuario]);
            $gamemaster = $consulta->fetch(PDO::FETCH_ASSOC);

            // Comparamos la contraseña con su hash guardado, no con texto plano
            if ($gamemaster && password_verify($password, $gamemaster["password"])) {
                // Renovamos el identificador para proteger la sesión al iniciar correctamente
                session_regenerate_id(true);
                $_SESSION["id_gamemaster"] = $gamemaster["id_gamemaster"];
                $_SESSION["username"] = $gamemaster["username"];
                $mensaje = "Sesión iniciada correctamente.";
            } else {
                $error = "Usuario o contraseña incorrectos.";
            }
        } catch (PDOException $e) {
            // Guardamos el detalle para revisar el problema
            error_log("Error al validar el inicio de sesión: " . $e->getMessage());
            $error = "No se pudo validar el inicio de sesión. Inténtalo de nuevo más tarde.";
        }
    }
}