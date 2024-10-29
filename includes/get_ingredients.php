<?php
require_once '../config.php';

try {
    $database = new Database();
    $db = $database->connect();
    
    $query = "SELECT ingrediente_id, nombre FROM ingrediente WHERE is_active = 1 ORDER BY nombre";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($results)) {
        echo "<option value=''>No ingredients available</option>";
    } else {
        echo "<option value=''>Select Ingredient</option>";
        foreach($results as $row) {
            echo "<option value='" . htmlspecialchars($row['ingrediente_id']) . "'>" 
                 . htmlspecialchars($row['nombre']) . "</option>";
        }
    }
} catch(PDOException $e) {
    error_log("Database Error in get_ingredients.php: " . $e->getMessage());
    echo "<option value=''>Error loading ingredients</option>";
}