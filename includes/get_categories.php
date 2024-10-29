<?php
require_once '../config.php';

try {
    $database = new Database();
    $db = $database->connect();
    
    $query = "SELECT categoria_id, nombre FROM categoria ORDER BY nombre";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($results)) {
        echo "<option value=''>No categories available</option>";
    } else {
        echo "<option value=''>Select Category</option>";
        foreach($results as $row) {
            echo "<option value='" . htmlspecialchars($row['categoria_id']) . "'>" 
                 . htmlspecialchars($row['nombre']) . "</option>";
        }
    }
} catch(PDOException $e) {
    error_log("Database Error in get_categories.php: " . $e->getMessage());
    echo "<option value=''>Error loading categories</option>";
}