<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isset($_GET['recipe_id'])) {
    echo json_encode(['error' => 'Recipe ID is required']);
    exit;
}

try {
    $database = new Database();
    $db = $database->connect();
    
    // Get the latest cost record for the recipe
    $query = "SELECT costo_receta_id 
              FROM costos_receta 
              WHERE receta_id = :recipe_id 
              ORDER BY costo_receta_id DESC 
              LIMIT 1";
              
    $stmt = $db->prepare($query);
    $stmt->bindParam(':recipe_id', $_GET['recipe_id']);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo json_encode($result);
    } else {
        echo json_encode(['error' => 'No cost record found for this recipe']);
    }
    
} catch(PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>