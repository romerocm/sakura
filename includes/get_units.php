<?php
require_once '../config.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    $database = new Database();
    $db = $database->connect();
    
    // Log the connection status
    error_log("Database connection established");
    
    $query = "SELECT unidad_id, nombre FROM unidad_medida ORDER BY nombre";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    // Log the query results
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    error_log("Units query results: " . print_r($results, true));
    
    echo "<option value=''>Select Unit</option>";
    foreach($results as $row) {
        echo "<option value='" . htmlspecialchars($row['unidad_id']) . "'>" 
             . htmlspecialchars($row['nombre']) . "</option>";
    }
} catch(PDOException $e) {
    error_log("Database Error in get_units.php: " . $e->getMessage());
    echo "<option value=''>Error loading units: " . $e->getMessage() . "</option>";
}
?>
