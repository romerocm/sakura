<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
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
    echo json_encode($result);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
