<?php
require_once '../config.php';

try {
    $database = new Database();
    $db = $database->connect();
    
    $query = "SELECT order_type_id, order_type FROM dim_order_type ORDER BY order_type";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($results)) {
        echo "<option value=''>No order types available</option>";
    } else {
        echo "<option value=''>Select Order Type</option>";
        foreach($results as $row) {
            echo "<option value='" . htmlspecialchars($row['order_type_id']) . "'>" 
                 . htmlspecialchars($row['order_type']) . "</option>";
        }
    }
} catch(PDOException $e) {
    error_log("Database Error in get_order_types.php: " . $e->getMessage());
    echo "<option value=''>Error loading order types</option>";
}