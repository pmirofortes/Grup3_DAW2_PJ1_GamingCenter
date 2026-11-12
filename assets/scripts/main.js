const filtro = document.getElementById('filtro');
const buscador = document.getElementById('buscador');
const salas = document.querySelectorAll('.sala');

// Apaga en el mapa las salas que no coinciden con el tipo elegido o con lo que se escribe
// (el texto vale para el nombre de la sala o para el principio de un módulo, como 7.1)
function filtrarSalas() {
    const texto = buscador.value.trim().toLowerCase();
    const tipo = filtro.tipo.value;

    for (const sala of salas) {
        const coincideTipo = tipo === '' || sala.dataset.tipo === tipo;
        const coincideTexto = sala.dataset.nombre.toLowerCase().includes(texto)
            || sala.dataset.modulos.split(' ').some(modulo => modulo.startsWith(texto));

        sala.classList.toggle('apagada', !(coincideTipo && coincideTexto));
    }
}

buscador.oninput = filtrarSalas;

// Al cambiar el tipo se envía el formulario
for (const radio of filtro.tipo) {
    radio.onchange = () => filtro.submit();
}
