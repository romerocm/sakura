<?php
require_once '../config.php';

try {
    $database = new Database();
    $db = $database->connect();
    
    $query = "SELECT unidad_id, nombre FROM unidad_medida ORDER BY nombre";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($results)) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'No units found']);
    } else {
        header('Content-Type: application/json');
        echo json_encode($results);
    }
} catch(PDOException $e) {
    error_log("Database Error in get_all_units.php: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}