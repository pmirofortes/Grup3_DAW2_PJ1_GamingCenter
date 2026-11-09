<?php
session_start();

// Aquí irá tu validación PHP (comprobar campos, PDO, password_verify, $_SESSION)
$error = "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Grand Theft Team: Code City · Panel de gamemasters</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Outfit:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>

<div class="container-fluid">
    <div class="row min-vh-100">

        <!-- COLUMNA IZQUIERDA: título + foto del equipo -->
        <div class="col-6 columna-izq">
            <h1 class="titulo">Grand Theft Team</h1>
            <h2 class="subtitulo">Code City</h2>
        </div>

        <!-- COLUMNA DERECHA: formulario -->
        <div class="col-6 columna-der">
            <div class="caja-login">
                <h2 class="titulo-login">Iniciar sesión</h2>
                <p class="texto-login">Acceso para gamemasters. Entra para ocupar y liberar módulos.</p>

                <?php if ($error != "") { ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php } ?>

                <form action="" method="POST" id="formLogin">
                    <label for="usuario">Usuario</label>
                    <input type="text" id="usuario" name="usuario" class="form-control mb-3">

                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control mb-4">

                    <button type="submit" class="btn-login">Iniciar sesión</button>
                </form>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="login.js"></script>
</body>
</html>