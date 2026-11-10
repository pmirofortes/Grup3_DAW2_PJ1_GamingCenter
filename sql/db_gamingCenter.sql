DROP DATABASE IF EXISTS db_gamingCenter;
CREATE DATABASE db_gamingCenter;
USE db_gamingCenter;


-- Creación tablas

CREATE TABLE gamemasters (
    id_gamemaster INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    nom_real VARCHAR(100) NOT NULL,
    cognom_real VARCHAR(100) NOT NULL
);

CREATE TABLE salas (
    id_sala INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo VARCHAR(50) NOT NULL
);

CREATE TABLE modulos (
    id_modulo INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    capacidad INT NOT NULL,
    estado VARCHAR(20) NOT NULL,
    id_sala INT NOT NULL
);

CREATE TABLE ocupaciones (
    id_ocupacion INT AUTO_INCREMENT PRIMARY KEY,
    fecha_inicio DATETIME NOT NULL,
    fecha_fin DATETIME NULL,
    id_modulo INT NOT NULL,
    id_gamemaster INT NOT NULL
);


-- Relaciones

-- Una sala tiene muchos módulos 1 a N
ALTER TABLE modulos
ADD CONSTRAINT fk_modulo_sala
FOREIGN KEY (id_sala) REFERENCES salas(id_sala);

-- Un módulo tiene muchas ocupaciones 1 a N
ALTER TABLE ocupaciones
ADD CONSTRAINT fk_ocupacion_modulo
FOREIGN KEY (id_modulo) REFERENCES modulos(id_modulo);

-- Un gamemaster registra muchas ocupaciones 1 a N
ALTER TABLE ocupaciones
ADD CONSTRAINT fk_ocupacion_gamemaster
FOREIGN KEY (id_gamemaster) REFERENCES gamemasters(id_gamemaster);



USE db_gamingCenter;


-- ==========================================
-- GAMEMASTERS
-- ==========================================

INSERT INTO gamemasters
(username, password, nom_real, cognom_real)
VALUES
('admin', 'admin123', 'Carlos', 'Martínez'),
('ninjaMaster', 'ninja123', 'David', 'García'),
('pixelKing', 'pixel123', 'Laura', 'Fernández'),
('gamePro', 'game123', 'Javier', 'López'),
('shadowGM', 'shadow123', 'Sergio', 'Rodríguez'),
('levelMaster', 'level123', 'Ana', 'Martínez');


-- ==========================================
-- SALAS
-- 4 PC STANDARD
-- 3 SIMULADORES/CONSOLAS
-- 2 VIP
-- ==========================================

INSERT INTO salas
(nombre, tipo)
VALUES
('Downtown', 'PC Standard'),
('Little Haiti', 'PC Standard'),
('Little Havana', 'PC Standard'),
('Escobar International Airport', 'PC Standard'),

('Leaf Links', 'Simuladores/Consolas'),
('Starfish Island', 'Simuladores/Consolas'),
('Ocean Beach', 'Simuladores/Consolas'),

('Vice Point', 'VIP'),
('Prawn Island', 'VIP');


-- ==========================================
-- MÓDULOS
-- ==========================================

-- ------------------------------------------
-- SALA 1 - DOWNTOWN
-- PC STANDARD
-- ------------------------------------------

INSERT INTO modulos
(nombre, tipo, capacidad, estado, id_sala)
VALUES
('1.1', 'Solo', 1, 'Ocupado', 1),
('1.2', 'Duo', 2, 'Disponible', 1),
('1.3', 'Duo', 2, 'Disponible', 1),
('1.4', 'Squad', 5, 'Disponible', 1);


-- ------------------------------------------
-- SALA 2 - LITTLE HAITI
-- PC STANDARD
-- ------------------------------------------

INSERT INTO modulos
(nombre, tipo, capacidad, estado, id_sala)
VALUES
('2.1', 'Squad', 5, 'Disponible', 2),
('2.2', 'Duo', 2, 'Disponible', 2),
('2.3', 'Duo', 2, 'Disponible', 2),
('2.4', 'Solo', 1, 'Disponible', 2);


-- ------------------------------------------
-- SALA 3 - LITTLE HAVANA
-- PC STANDARD
-- ------------------------------------------

INSERT INTO modulos
(nombre, tipo, capacidad, estado, id_sala)
VALUES
('3.1', 'Squad', 5, 'Disponible', 3),
('3.2', 'Solo', 1, 'Ocupado', 3),
('3.3', 'Duo', 2, 'Disponible', 3),
('3.4', 'Duo', 2, 'Ocupado', 3),
('3.5', 'Solo', 1, 'Disponible', 3);


-- ------------------------------------------
-- SALA 4 - ESCOBAR INTERNATIONAL AIRPORT
-- PC STANDARD
-- ------------------------------------------

INSERT INTO modulos
(nombre, tipo, capacidad, estado, id_sala)
VALUES
('4.1', 'Squad', 5, 'Disponible', 4),
('4.2', 'Duo', 2, 'Disponible', 4),
('4.3', 'Duo', 2, 'Disponible', 4),
('4.4', 'Solo', 1, 'Disponible', 4),
('4.5', 'Solo', 1, 'Disponible', 4);


-- ------------------------------------------
-- SALA 5 - LEAF LINKS
-- SIMULADORES / CONSOLAS
-- ------------------------------------------

INSERT INTO modulos
(nombre, tipo, capacidad, estado, id_sala)
VALUES
('5.1', 'Party', 4, 'Ocupado', 5),
('5.2', 'Party', 4, 'Ocupado', 5),
('5.3', 'Duo', 2, 'Disponible', 5),
('5.4', 'Solo', 1, 'Disponible', 5),
('5.5', 'Solo', 1, 'Disponible', 5);


-- ------------------------------------------
-- SALA 6 - STARFISH ISLAND
-- SIMULADORES / CONSOLAS
-- ------------------------------------------

INSERT INTO modulos
(nombre, tipo, capacidad, estado, id_sala)
VALUES
('6.1', 'Party', 4, 'Ocupado', 6),
('6.2', 'Duo', 2, 'Disponible', 6),
('6.3', 'Duo', 2, 'Disponible', 6),
('6.4', 'Solo', 1, 'Disponible', 6),
('6.5', 'Solo', 1, 'Disponible', 6);


-- ------------------------------------------
-- SALA 7 - OCEAN BEACH
-- SIMULADORES / CONSOLAS
-- ------------------------------------------

INSERT INTO modulos
(nombre, tipo, capacidad, estado, id_sala)
VALUES
('7.1', 'Party', 4, 'Disponible', 7),
('7.2', 'Duo', 2, 'Disponible', 7),
('7.3', 'Duo', 2, 'Disponible', 7),
('7.4', 'Solo', 1, 'Disponible', 7),
('7.5', 'Solo', 1, 'Disponible', 7);


-- ------------------------------------------
-- SALA 8 - VICE POINT
-- VIP
-- ------------------------------------------

INSERT INTO modulos
(nombre, tipo, capacidad, estado, id_sala)
VALUES
('8.1', 'Party', 4, 'Ocupado', 8),
('8.2', 'Duo', 2, 'Disponible', 8),
('8.3', 'Solo', 1, 'Disponible', 8);


-- ------------------------------------------
-- SALA 9 - PRAWN ISLAND
-- VIP
-- ------------------------------------------

INSERT INTO modulos
(nombre, tipo, capacidad, estado, id_sala)
VALUES
('9.1', 'Squad', 5, 'Disponible', 9),
('9.2', 'Duo', 2, 'Disponible', 9),
('9.3', 'Duo', 2, 'Disponible', 9);


-- ==========================================
-- OCUPACIONES ACTUALES
-- ==========================================

INSERT INTO ocupaciones
(fecha_inicio, fecha_fin, id_modulo, id_gamemaster)
VALUES

-- Downtown 1.1 - Solo
('2026-11-09 17:40:00', NULL, 1, 1),

-- Little Havana 3.2 - Solo
('2026-11-09 18:05:00', NULL, 10, 3),

-- Little Havana 3.4 - Duo
('2026-11-09 18:20:00', NULL, 12, 2),

-- Leaf Links 5.1 - Party
('2026-11-09 17:15:00', NULL, 17, 4),

-- Leaf Links 5.2 - Party
('2026-11-09 18:10:00', NULL, 18, 5),

-- Starfish Island 6.1 - Party
('2026-11-09 16:50:00', NULL, 22, 6),

-- Vice Point 8.1 - Party
('2026-11-09 17:30:00', NULL, 32, 3);


-- ==========================================
-- OCUPACIONES HISTÓRICAS
-- ==========================================

INSERT INTO ocupaciones
(fecha_inicio, fecha_fin, id_modulo, id_gamemaster)
VALUES

('2026-11-01 10:00:00', '2026-11-01 12:00:00', 2, 1),
('2026-11-01 12:30:00', '2026-11-01 14:30:00', 5, 2),

('2026-11-02 09:00:00', '2026-11-02 11:00:00', 7, 3),
('2026-11-02 16:00:00', '2026-11-02 18:00:00', 21, 4),

('2026-11-03 10:00:00', '2026-11-03 13:00:00', 9, 5),
('2026-11-03 15:00:00', '2026-11-03 17:00:00', 14, 6),

('2026-11-04 11:00:00', '2026-11-04 13:00:00', 23, 1),
('2026-11-04 17:00:00', '2026-11-04 19:00:00', 28, 2),

('2026-11-05 10:00:00', '2026-11-05 12:00:00', 32, 3),
('2026-11-05 14:00:00', '2026-11-05 16:00:00', 35, 4);