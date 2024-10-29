<?php
require_once '../config.php';

try {
    $database = new Database();
    $db = $database->connect();
    
    $query = "SELECT unidad_id, nombre FROM unidad_medida ORDER BY nombre";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($results)) {
        echo "<option value=''>No units available</option>";
    } else {
        echo "<option value=''>Select Unit</option>";
        foreach($results as $row) {
            echo "<option value='" . htmlspecialchars($row['unidad_id']) . "'>" 
                 . htmlspecialchars($row['nombre']) . "</option>";
        }
    }
} catch(PDOException $e) {
    error_log("Database Error in get_units.php: " . $e->getMessage());
    echo "<option value=''>Error loading units</option>";
}