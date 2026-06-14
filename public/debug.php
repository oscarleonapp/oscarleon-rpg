<?php
// Reporte de errores para debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = '127.0.0.1';
$db   = 'qanil_rpg';
$user = 'root'; 
$pass = '';     
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $db_status = "Conectado a '$db'";
} catch (\PDOException $e) {
    // Si falla la conexión, mostramos el error pero no matamos la página
    $db_status = "Error DB: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crónicas de Q'anil - Debug</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap');
        body { font-family: 'Press Start 2P', cursive; background-color: #1a202c; color: #e2e8f0; }
    </style>
</head>
<body class="p-10">
    <h1 class="text-yellow-500 text-2xl mb-4">Modo Debug</h1>
    <div class="bg-gray-800 p-4 border-2 border-red-500">
        <p>Estado DB: <span class="text-blue-400"><?php echo $db_status; ?></span></p>
    </div>
    <div class="mt-4">
        <a href="index.php" class="text-green-500 underline">Recargar</a>
    </div>
</body>
</html>