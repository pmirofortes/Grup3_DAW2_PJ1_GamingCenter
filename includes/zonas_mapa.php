<?php

// Relaciona cada sala con su zona y la posición del pin sobre el mapa (1499 x 1049).
// La zona corresponde al id del polígono en zonas_mapa.svg, el pin usa [x, y].
return [
    'Downtown'                      => ['zona' => 'downtown', 'pin' => [561, 108]],
    'Little Haiti'                  => ['zona' => 'little-haiti', 'pin' => [462, 402]],
    'Little Havana'                 => ['zona' => 'little-havana', 'pin' => [432, 690]],
    'Ocean Beach'                   => ['zona' => 'ocean-beach', 'pin' => [1100, 761]],
    'Escobar International Airport' => ['zona' => 'escobar-international-airport', 'pin' => [232, 565]],
    'Vice Point'                    => ['zona' => 'vice-point', 'pin' => [1205, 312]],
    'Leaf Links'                    => ['zona' => 'leaf-links', 'pin' => [899, 476]],
    'Starfish Island'               => ['zona' => 'starfish-island', 'pin' => [706, 600]],
    'Prawn Island'                  => ['zona' => 'prawn-island', 'pin' => [866, 214]],
];
