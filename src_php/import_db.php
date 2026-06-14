<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Crear base de datos
    $pdo->exec("CREATE DATABASE IF NOT EXISTS qanil_rpg");
    $pdo->exec("USE qanil_rpg");
    
    // Leer el esquema
    $sql = file_get_contents(__DIR__ . '/../database/schema.sql');
    
    // Ejecutar el esquema
    $pdo->exec($sql);
    
    echo "Base de datos 'qanil_rpg' importada con éxito.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
