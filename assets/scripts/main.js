const filtro = document.getElementById('filtro');
const buscador = document.getElementById('buscador');
const radiosTipo = document.querySelectorAll('#filtro input[name="tipo"]');

// Filtra la lista de salas por nombre mientras se escribe en el buscador
function filtrarSalas() {
    const busqueda = buscador.value.trim().toLowerCase();
    const salas = document.querySelectorAll('#lista-salas li');

    for (const sala of salas) {
        const nombre = sala.dataset.nombre.toLowerCase();
        sala.classList.toggle('oculto', !nombre.includes(busqueda));
    }
}

// Envía el formulario del filtro al cambiar el tipo de sala
function enviarFiltro() {
    filtro.submit();
}

buscador.oninput = filtrarSalas;

for (const radio of radiosTipo) {
    radio.onchange = enviarFiltro;
}
