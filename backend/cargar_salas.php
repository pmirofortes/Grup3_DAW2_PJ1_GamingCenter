<?php
// Prepara los datos de views/main.php:
// $salas, $tipos, $tipo, $busqueda, $idSala, $salaAbierta, $aviso, $avisoError

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../sql/conexion.php';
$zonas = require __DIR__ . '/../includes/zonas_mapa.php';

// Validamos los filtros antes de usarlos para no dejar valores fuera de las opciones.
$tipos = [
    'PC Standard'          => 'PC Standard',
    'Simuladores/Consolas' => 'Simuladores',
    'VIP'                  => 'VIP',
];
$tipo = $_GET['tipo'] ?? '';
if (!isset($tipos[$tipo])) {
    $tipo = ''; //solo se acepta si está en la lista permitida; si no, queda vacío.
}
$busqueda = trim($_GET['q'] ?? '');
//Es la sala que pulsaste en el mapa.
$idSala = (int) ($_GET['id_sala'] ?? 0);

// Una fila por módulo; los LEFT JOIN conservan también las salas sin módulos.
$sql = "SELECT s.id_sala, s.nombre AS sala, s.tipo AS tipo_sala,
               m.id_modulo, m.nombre AS modulo, m.tipo AS tipo_modulo, m.capacidad, m.estado,
               o.id_gamemaster AS ocupado_por, o.num_personas, o.fecha_inicio,
               g.nom_real AS ocupado_nombre
        FROM salas s
        LEFT JOIN modulos m ON m.id_sala = s.id_sala
        LEFT JOIN ocupaciones o ON o.id_modulo = m.id_modulo AND o.fecha_fin IS NULL
        LEFT JOIN gamemasters g ON g.id_gamemaster = o.id_gamemaster
        ORDER BY s.id_sala, m.id_modulo";
$filas = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// La consulta repite los datos de sala, así que los reunimos en una sola entrada.
$salas = [];
foreach ($filas as $fila) { //para cada fila, haz lo siguiente
    $id = $fila['id_sala'];//vale 1, el número de la sala

    if (!isset($salas[$id])) { //pregunta ¿ya tengo la sala 1 en la cajaa?
        $salas[$id] = [
            //así que la crea con su nombrie, tipo, modulos vacío, libres a 0, el pin y la zona del mapa
            'nombre'  => $fila['sala'],
            'tipo'    => $fila['tipo_sala'],
            'modulos' => [],
            'libres'  => 0,
            'pin'     => $zonas[$fila['sala']]['pin'] ?? [750, 525], // sin zona: centro del mapa
            'zona'    => $zonas[$fila['sala']]['zona'] ?? '',
        ];
    }

    //comprueba que la fila tenga módulo, si una sala no tuviera ninguno, continue salta a la siguiente fila 
    if ($fila['id_modulo'] === null) {
        continue;
    }

    // Círculo del módulo punto + letra y texto que sale en el panel detalle
    if ($fila['estado'] === 'Disponible') {
        $salas[$id]['libres']++;
        $fila['punto'] = 'libre';
        $fila['letra'] = $fila['capacidad'];
        $fila['detalle'] = $fila['capacidad'] . ($fila['capacidad'] == 1 ? ' plaza' : ' plazas') . ' · libre';
    } else {
        //$esMio compara quién lo ocupó con quién está conectado 1 == 1, es decir como tuyo
        $esMio = $fila['ocupado_por'] == $idGamemaster;
        $fila['punto'] = $esMio ? 'mio' : 'ocupado';
        $fila['letra'] = $esMio ? 'TÚ' : mb_substr($fila['ocupado_nombre'] ?? '·', 0, 1);
        $fila['detalle'] = 'Ocupado';

        //EJEMPLOOO: Ocupado por ti desde las 17:40 · 1/1 personas
        if ($fila['ocupado_por']) {
            $fila['detalle'] .= ' por ' . ($esMio ? 'ti' : $fila['ocupado_nombre'])
                . ' desde las ' . date('H:i', strtotime($fila['fecha_inicio']))
                . ' · ' . $fila['num_personas'] . '/' . $fila['capacidad'] . ' personas';
        }
    }

    $salas[$id]['modulos'][] = $fila;
}

// Se mantienen visibles para no mover el mapa, solo se oscurecen las que no coinciden.
foreach ($salas as $id => $sala) {

    //el texto aparece dentro del nombre de la sala (sin importar mayúsculas).
    $encontrada = $busqueda === '' || mb_stripos($sala['nombre'], $busqueda) !== false;
    foreach ($sala['modulos'] as $modulo) {
        if (str_starts_with($modulo['modulo'], $busqueda)) {
            $encontrada = true;
        }
    }
    $salas[$id]['apagada'] = !$encontrada || ($tipo !== '' && $sala['tipo'] !== $tipo);
}

//significa ,si no existe, usa null. Pasa cuando entras por primera vez, sin haber pulsado ninguna sala (id_sala valdria 0)
$salaAbierta = $salas[$idSala] ?? null;

// Mensaje después de ocupar o liberar 
$mensajes = [
    'modulo_ocupado'  => 'Módulo ocupado correctamente.',
    'modulo_liberado' => 'Módulo liberado correctamente.',
];
$errores = [
    'modulo_ocupado' => 'Ese módulo ya está ocupado.',
    'no_es_tuyo'     => 'Solo puedes liberar los módulos que has ocupado tú.',
    'num_personas'   => 'El número de personas no cabe en ese módulo.',
    'sin_personas'   => 'Indica cuántas personas van a ocupar el módulo.',
];
$aviso = $mensajes[$_GET['ok'] ?? ''] ?? '';
$avisoError = isset($_GET['error']);
if ($avisoError) {
    $aviso = $errores[$_GET['error']] ?? 'Ha ocurrido un error, inténtalo de nuevo.';
}
