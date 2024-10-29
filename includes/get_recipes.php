<?php
require_once '../config.php';

try {
    $database = new Database();
    $db = $database->connect();
    
    $query = "SELECT receta_id, nombre_receta FROM receta_estandar ORDER BY nombre_receta";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($results)) {
        echo "<option value=''>No recipes available</option>";
    } else {
        echo "<option value=''>Select Recipe</option>";
        foreach($results as $row) {
            echo "<option value='" . htmlspecialchars($row['receta_id']) . "'>" 
                 . htmlspecialchars($row['nombre_receta']) . "</option>";
        }
    }
} catch(PDOException $e) {
    error_log("Database Error in get_recipes.php: " . $e->getMessage());
    echo "<option value=''>Error loading recipes</option>";
}