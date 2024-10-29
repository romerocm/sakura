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
    
    error_log("Checking ingredients status for recipe ID: " . $_GET['recipe_id']);
    
    $query = "SELECT COUNT(*) as ingredient_count
              FROM receta_ingredientes
              WHERE receta_id = :recipe_id";
              
    $stmt = $db->prepare($query);
    $stmt->bindParam(':recipe_id', $_GET['recipe_id']);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'has_ingredients' => $result['ingredient_count'] > 0,
        'ingredient_count' => intval($result['ingredient_count'])
    ]);
    
} catch(PDOException $e) {
    error_log("Database Error in get_recipe_ingredients_status.php: " . $e->getMessage());
    echo json_encode([
        'error' => 'Database error occurred',
        'details' => $e->getMessage()
    ]);
}
?>