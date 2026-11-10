<?php require_once __DIR__ . "/validaciones/validar_login.php"; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Grand Theft Team: Code City · Panel de gamemasters</title>
    <!-- Link de Bootsrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

   <!-- Link de la fuente --> 
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Outfit:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<!--Aqui es donde emepieza el boostrap para el responsive  -->
<div class="container-fluid">
    <div class="row min-vh-100">

        <!-- IZQUIERDA:  Contiene las  4 imagenes y 3 bloques -->
        <!--col-12 ocupa todo la pagina y luego se va a dividir en bloque divido en la mitad con el col-6 -->
        <div class="col-12 col-md-6 zona_izquierda">
            <div class="row g-2">

                <!-- La primera columna izquierda -->
                <div class="col-4">
                    <div class="row g-2">
                        <div class="col-12">
                            <!-- La primera imagen -->
                            <img src="./assets/images/palmeras.jpg" alt="Imagen 1" class="imagen_palmera">
                        </div>
                        <div class="col-12 ocupar_resto">
                            <!-- 1r bloque -->
                            <div class="bloque_grove">
                                <small>San Andreas · Sala 2</small>
                                <h3>Grove Street</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!--La otra columna -->
                <div class="col-8">
                    <div class="row g-2">
                        <div class="col-12">
                            <div class="bloque_starfish">
                                <!-- 2n bloque -->
                                <small>Vice City · Sala 8</small>
                                <h3>Starfish Island</h3>
                            </div>
                        </div>
                        <div class="col-12 ocupar_resto">
                            <!-- La segunda imagen -->
                            <img src="./assets/images/hotel.jpg" alt="Imagen 2" class="imagen_hotel">
                        </div>
                        <div class="col-12">
                            <!-- La tercera imagen -->
                            <img src="./assets/images/coche.jpg" alt="Imagen 3" class="imagen_coche">
                        </div>
                        <div class="col-12">
                            <div class="row g-2">
                                <div class="col-6">
                                    <!-- La cuarta imagen -->
                                    <img src="./assets/images/mueble.jpg" alt="Playa" class="imagen_mueble">
                                </div>
                                <div class="col-6">
                                    <div class="bloque_vinewood">
                                        <!-- 3r bloque -->
                                        <small>GTA V · Sala 9</small>
                                        <h3>Vinewood Hills</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Título encima -->
            <h1 class="titulo_principal">Grand Theft Team: Code City</h1>
        </div>

        <!-- DERECHA: congtiene lo del formulario -->
        <div class="col-12 col-md-6 zona_derecha">
            <div class="caja_formulario">
                <small class="texto_panel">Panel de gamemasters</small>
                <h2 class="titulo_iniciar_sesion">Iniciar sesión</h2>

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

                    <button type="submit" class="boton_login">Iniciar sesión</button>
                </form>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="./validaciones/validacion_login.js" defer></script>
</body>
</html>