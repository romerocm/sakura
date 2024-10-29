<?php
require_once '../config.php';

try {
    $database = new Database();
    $db = $database->connect();
    
    error_log("Attempting to fetch recipes from receta_estandar");
    
    $query = "SELECT receta_id, nombre_receta, numero_porciones 
              FROM receta_estandar 
              ORDER BY nombre_receta";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Debug: Print results
    error_log("Found " . count($results) . " recipes");
    
    // Start with an empty select option
    echo "<option value=''>Select Recipe</option>";
    
    // Add each recipe as an option
    foreach($results as $row) {
        printf(
            "<option value='%s' data-porciones='%s'>%s</option>",
            htmlspecialchars($row['receta_id']),
            htmlspecialchars($row['numero_porciones']),
            htmlspecialchars($row['nombre_receta'])
        );
    }
    
} catch(PDOException $e) {
    error_log("Database Error in get_recipes.php: " . $e->getMessage());
    error_log("SQL State: " . $e->getCode());
    echo "<option value=''>Error loading recipes</option>";
}
?>