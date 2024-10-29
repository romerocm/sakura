<?php
require_once '../config.php';

if (!isset($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No ingredient ID provided']);
    exit;
}

try {
    $database = new Database();
    $db = $database->connect();
    
    $query = "SELECT costo_unitario FROM ingrediente WHERE ingrediente_id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $_GET['id']);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    if ($result) {
        echo json_encode($result);
    } else {
        echo json_encode(['error' => 'Ingredient not found']);
    }
} catch(PDOException $e) {
    error_log("Database Error in get_ingredient_cost.php: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}