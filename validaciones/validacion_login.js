// Estas comprobaciones muestran errores al momento; el servidor vuelve a validar al recibir el formulario
function validarUsuario() {
    // Quitamos espacios al principio y al final para que no cuenten como parte del usuario
    usuario = document.getElementById("usuario").value.trim();
    error = document.getElementById("errUsuario");

    if (usuario == "") {
        error.textContent = "El usuario es obligatorio";
        return false;
    } else if (usuario.length < 3) {
        error.textContent = "El usuario debe tener al menos 3 caracteres";
        return false;
    } else {
        error.textContent = "";
        return true;
    }
}

function validarPass() {
    // En la contraseña no quitamos espacios, también pueden formar parte de ella
    pass = document.getElementById("password").value;
    error = document.getElementById("errPass");

    if (pass == "") {
        error.textContent = "La contraseña es obligatoria";
        return false;
    } else {
        error.textContent = "";
        return true;
    }
}

function validarTodo() {
    // El formulario solo se envía cuando los dos campos son válidos
    usuarioValido = validarUsuario();
    passValido = validarPass();

    return usuarioValido && passValido;
}

// Revisamos cada campo mientras se escribe para mostrar los errores al momento.
document.getElementById("usuario").addEventListener("input", validarUsuario);
document.getElementById("password").addEventListener("input", validarPass);