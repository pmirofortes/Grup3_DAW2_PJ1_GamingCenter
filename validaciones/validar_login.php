<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    // La sesión permitirá recordar quién ha iniciado sesión
    session_start();
}

$error = "";
$mensaje = "";
$usuario = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recogemos los datos enviados y limpiamos los espacios sobrantes del usuario
    $usuario = trim($_POST["usuario"] ?? "");
    $password = $_POST["password"] ?? "";

    // En el inicio de sesión solo comprobamos que se hayan enviado ambos campos
    if ($usuario === "" || $password === "") {
        $error = "El usuario y la contraseña son obligatorios.";
    } else {
        // La consulta preparada evita insertar directamente el usuario en el SQL
        require_once __DIR__ . "/../sql/conexion.php";

        try {
            $consulta = $conn->prepare(
                "SELECT id_gamemaster, username, password
                 FROM gamemasters
                 WHERE username = :usuario
                 LIMIT 1"
            );
            $consulta->execute(["usuario" => $usuario]);
            $gamemaster = $consulta->fetch(PDO::FETCH_ASSOC);

            // Si la contraseña aún no está cifrada, se actualiza al verificarla
            $passwordStored = $gamemaster["password"] ?? "";
            $passwordIsHashed = password_get_info($passwordStored)["algo"] !== null;
            $passwordIsValid = $gamemaster && (
                ($passwordIsHashed && password_verify($password, $passwordStored))
                || (!$passwordIsHashed && hash_equals($passwordStored, $password))
            );

            if ($passwordIsValid) {
                if (!$passwordIsHashed) {
                    $actualizarPassword = $conn->prepare(
                        "UPDATE gamemasters SET password = :password WHERE id_gamemaster = :id"
                    );
                    $actualizarPassword->execute([
                        "password" => password_hash($password, PASSWORD_DEFAULT),
                        "id" => $gamemaster["id_gamemaster"],
                    ]);
                }

                // Renovamos el identificador para proteger la sesión al iniciar correctamente
                session_regenerate_id(true);
                $_SESSION["id_gamemaster"] = $gamemaster["id_gamemaster"];
                $_SESSION["username"] = $gamemaster["username"];
                $mensaje = "Sesión iniciada correctamente.";
                header("Location: ./views/main.php");
                exit();
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