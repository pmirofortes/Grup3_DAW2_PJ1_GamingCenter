<?php require_once __DIR__ . "/validaciones/validar_login.php"; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Grand Theft Team: Code City · Panel de gamemasters</title>
    <link rel="icon" type="image/svg+xml" href="assets/images/favicon.svg">
    <!-- Link de Bootsrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

   <!-- Link de la fuente --> 
    <link rel="stylesheet" href="./assets/styles/style.css?v=2.7">
</head>
<body class="pagina-login">
<!--Aqui es donde emepieza el boostrap para el responsive  -->
<div class="container-fluid">
    <div class="row min-vh-100">

        <!-- IZQUIERDA: Contiene las  4 imagenes y 3 bloques -->
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
                            <!-- Primer bloque -->
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
                                <!-- Segundo bloque -->
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
                                        <!-- Tercer bloque -->
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
            <h1 class="titulo_principal">
                <picture>
                    <source media="(max-width: 768px)" srcset="assets/images/logo_movil.svg">
                    <img src="assets/images/logo.svg" alt="Grand Theft Team: Code City">
                </picture>
            </h1>
        </div>

        <!-- DERECHA: congtiene lo del formulario -->
        <div class="col-12 col-md-6 zona_derecha">
            <div class="caja_formulario">
                <small class="texto_panel">Panel de gamemasters</small>
                <h2 class="titulo_iniciar_sesion">Iniciar sesión</h2>

                <!-- La validación en el navegador da respuesta rápida, PHP vuelve a comprobar al recibir el formulario -->
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

<!-- Si el servidor devuelve un error, lo mostramos en una ventana de SweetAlert -->
<?php if ($error !== "") { // Solo se pinta el script si $error tiene un mensaje ?>
<script>
    // Swal.fire abre la ventana emergente
    Swal.fire({
        icon: "error", // Dibujo de la ventana: aquí la X roja de error
        title: "No se pudo iniciar sesión", // Título en grande
        text: <?= json_encode($error) ?> // Mensaje que viene de PHP, pasado a JS de forma segura
    });
</script>
<?php } // Fin del if: si no hay error, no se escribe nada ?>

<script src="./validaciones/validacion_login.js" defer></script>
</body>
</html>