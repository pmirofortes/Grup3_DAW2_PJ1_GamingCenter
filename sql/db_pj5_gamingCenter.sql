DROP DATABASE IF EXISTS db_pj5_gamingCenter;
CREATE DATABASE db_pj5_gamingCenter;
USE db_pj5_gamingCenter;


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