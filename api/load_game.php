<?php
header('Content-Type: application/json');
require_once 'db_config.php';

try {
    $stmt = $pdo->query("SELECT * FROM players ORDER BY last_saved DESC LIMIT 1");
    $player = $stmt->fetch();

    if ($player) {
        echo json_encode(['status' => 'success', 'player' => $player]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No hay partidas guardadas']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
