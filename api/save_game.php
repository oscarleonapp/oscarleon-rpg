<?php
header('Content-Type: application/json');
require_once 'db_config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'No data received']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO players (username, current_region, level, xp, hp, energy, gold) 
                           VALUES (:username, :region, :level, :xp, :hp, :energy, :gold) 
                           ON DUPLICATE KEY UPDATE 
                           level = VALUES(level), xp = VALUES(xp), hp = VALUES(hp), 
                           energy = VALUES(energy), gold = VALUES(gold), 
                           last_saved = CURRENT_TIMESTAMP");
    
    $stmt->execute([
        ':username' => $data['username'],
        ':region'   => $data['region'] ?? 'zacapa',
        ':level'    => $data['level'] ?? 1,
        ':xp'       => $data['xp'] ?? 0,
        ':hp'       => $data['hp'] ?? 100,
        ':energy'   => $data['energy'] ?? 80,
        ':gold'     => $data['gold'] ?? 0
    ]);

    // Obtener ID del jugador
    $stmt = $pdo->prepare("SELECT id FROM players WHERE username = ?");
    $stmt->execute([$data['username']]);
    $player = $stmt->fetch();
    $playerId = $player['id'];

    // Guardar Inventario
    if (isset($data['inventory'])) {
        // Limpiar inventario viejo
        $pdo->prepare("DELETE FROM inventory WHERE player_id = ?")->execute([$playerId]);
        
        // Insertar nuevo
        $stmtInv = $pdo->prepare("INSERT INTO inventory (player_id, item_id, quantity) VALUES (?, ?, ?)");
        foreach ($data['inventory'] as $itemId => $qty) {
            if ($qty > 0) {
                $stmtInv->execute([$playerId, $itemId, $qty]);
            }
        }
    }

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
