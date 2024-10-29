<?php
require_once '../config.php';

try {
    $database = new Database();
    $db = $database->connect();
    
    $query = "SELECT cr.costo_receta_id, r.nombre_receta 
              FROM costos_receta cr 
              JOIN receta_estandar r ON cr.receta_id = r.receta_id 
              ORDER BY r.nombre_receta";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($results)) {
        echo "<option value=''>No recipe costs available</option>";
    } else {
        echo "<option value=''>Select Recipe Cost</option>";
        foreach($results as $row) {
            echo "<option value='" . htmlspecialchars($row['costo_receta_id']) . "'>" 
                 . htmlspecialchars($row['nombre_receta']) . "</option>";
        }
    }
} catch(PDOException $e) {
    error_log("Database Error in get_recipe_costs.php: " . $e->getMessage());
    echo "<option value=''>Error loading recipe costs</option>";
}