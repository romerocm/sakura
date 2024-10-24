<?php
require_once '../config.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    $database = new Database();
    $db = $database->connect();
    
    $query = "SELECT categoria_id, nombre FROM categoria ORDER BY nombre";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    // Debug: Print the query results
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    error_log("Categories query results: " . print_r($results, true));
    
    echo "<option value=''>Select Category</option>";
    foreach($results as $row) {
        echo "<option value='" . htmlspecialchars($row['categoria_id']) . "'>" 
             . htmlspecialchars($row['nombre']) . "</option>";
    }
} catch(PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    echo "<option value=''>Error loading categories: " . $e->getMessage() . "</option>";
}
?>
