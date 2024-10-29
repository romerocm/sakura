<?php
require_once '../config.php';

try {
    $database = new Database();
    $db = $database->connect();
    
    // Add error logging
    error_log("Attempting to fetch recipes");
    
    $query = "SELECT receta_id, nombre_receta, numero_porciones, categoria_id 
              FROM receta_estandar 
              ORDER BY nombre_receta";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Log the results
    error_log("Recipes query results: " . print_r($results, true));
    
    if (empty($results)) {
        error_log("No recipes found in database");
        echo "<option value=''>No recipes available</option>";
    } else {
        echo "<option value=''>Select Recipe</option>";
        foreach($results as $row) {
            echo "<option value='" . htmlspecialchars($row['receta_id']) . "' 
                          data-porciones='" . htmlspecialchars($row['numero_porciones']) . "'>" 
                 . htmlspecialchars($row['nombre_receta']) . "</option>";
        }
    }
} catch(PDOException $e) {
    error_log("Database Error in get_recipes.php: " . $e->getMessage());
    error_log("SQL State: " . $e->getCode());
    echo "<option value=''>Error loading recipes</option>";
}
?>