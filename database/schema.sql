-- Base de Datos para Crónicas de Q'anil
CREATE DATABASE IF NOT EXISTS qanil_rpg;
USE qanil_rpg;

-- Usuarios / Partidas Guardadas
CREATE TABLE IF NOT EXISTS players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    current_region VARCHAR(50) DEFAULT 'zacapa',
    level INT DEFAULT 1,
    xp INT DEFAULT 0,
    hp INT DEFAULT 100,
    max_hp INT DEFAULT 100,
    energy INT DEFAULT 80,
    max_energy INT DEFAULT 80,
    gold INT DEFAULT 0,
    reputation_score INT DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_saved TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Inventario del Jugador
CREATE TABLE IF NOT EXISTS inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT,
    item_id VARCHAR(50) NOT NULL,
    quantity INT DEFAULT 1,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE
);

-- Misiones Completadas
CREATE TABLE IF NOT EXISTS completed_quests (
    player_id INT,
    quest_id VARCHAR(50),
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (player_id, quest_id),
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE
);
