<?php
require_once '../config.php';

try {
    $database = new Database();
    $db = $database->connect();
    
    error_log("Attempting to fetch recipes from receta_estandar");
    
    // Debug: Print the SQL query
    $query = "SELECT receta_id, nombre_receta, numero_porciones 
              FROM receta_estandar 
              ORDER BY nombre_receta";
    error_log("SQL Query: " . $query);
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Debug: Print results
    error_log("Found " . count($results) . " recipes");
    error_log("Results: " . print_r($results, true));
    
    if (empty($results)) {
        echo "<option value=''>No recipes available</option>";
    } else {
        echo "<option value=''>Select Recipe</option>";
        foreach($results as $row) {
            echo "<option value='" . htmlspecialchars($row['receta_id']) . "'" . 
                 " data-porciones='" . htmlspecialchars($row['numero_porciones']) . "'>" . 
                 htmlspecialchars($row['nombre_receta']) . "</option>";
        }
    }
    
} catch(PDOException $e) {
    error_log("Database Error in get_recipes.php: " . $e->getMessage());
    error_log("SQL State: " . $e->getCode());
    echo "<option value=''>Error loading recipes: " . $e->getMessage() . "</option>";
}
?>