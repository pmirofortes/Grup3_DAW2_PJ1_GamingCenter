<?php require_once __DIR__ . "/validaciones/validar_login.php"; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Grand Theft Team: Code City · Panel de gamemasters</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Outfit:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<!--Aqui es donde emepieza el boostrap para el responsive  -->
<div class="container-fluid">
    <div class="row min-vh-100">

        <!-- IZQUIERDA: mosaico con 4 imágenes y 3 bloques -->
        <div class="col-12 col-md-6 columna-izq">
            <div class="row g-2">

                <!-- Mosaico columna A (estrecha) -->
                <div class="col-4">
                    <div class="row g-2">
                        <div class="col-12"><img src="img/palmeras.jpg" alt="Imagen 1" class="img-mosaico alta"></div>
                        <div class="col-12 grow">
                            <div class="bloque naranja">
                                <small>San Andreas · Sala 2</small>
                                <h3>Grove Street</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mosaico columna B (ancha) -->
                <div class="col-8">
                    <div class="row g-2">
                        <div class="col-12">
                            <div class="bloque rosa">
                                <small>Vice City · Sala 8</small>
                                <h3>Starfish Island</h3>
                            </div>
                        </div>
                        <div class="col-12 grow"><img src="img/hotel.jpg" alt="Imagen 2" class="img-mosaico grande"></div>
                        <div class="col-12"><img src="img/coche.jpg" alt="Imagen 3" class="img-mosaico media"></div>
                        <div class="col-12">
                            <div class="row g-2">
                                <div class="col-6"><img src="img/mueble.jpg" alt="Imagen 4" class="img-mosaico pequena"></div>
                                <div class="col-6">
                                    <div class="bloque naranja pequena">
                                        <small>GTA V · Sala 9</small>
                                        <h3>Vinewood Hills</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Título encima del mosaico -->
            <h1 class="titulo-gta">Grand Theft Team: Code City</h1>
        </div>

        <!-- DERECHA: formulario -->
        <div class="col-12 col-md-6 columna-der">
            <div class="caja-login">
                <small class="etiqueta">Panel de gamemasters</small>
                <h2 class="titulo-login">Iniciar sesión</h2>

                <?php if ($error !== "") { ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?></div>
                <?php } elseif ($mensaje !== "") { ?>
                    <div class="alert alert-success"><?= htmlspecialchars($mensaje, ENT_QUOTES, "UTF-8") ?></div>
                <?php } ?>

                <form action="" method="POST" id="formLogin" onsubmit="return validarTodo()">
                    <label for="usuario">Usuario</label>
                    <input type="text" id="usuario" name="usuario" class="form-control" placeholder="Usuario" value="<?= htmlspecialchars($usuario, ENT_QUOTES, "UTF-8") ?>" aria-describedby="errUsuario">
                    <small id="errUsuario" class="text-danger d-block mb-3" aria-live="polite"></small>

                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Contraseña" aria-describedby="errPass">
                    <small id="errPass" class="text-danger d-block mb-4" aria-live="polite"></small>

                    <button type="submit" class="btn-login">Iniciar sesión</button>
                </form>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="./validaciones/validacion_login.js" defer></script>
</body>
</html>