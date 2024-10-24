<?php
require_once '../config.php';

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->connect();
    
    $query = "SELECT order_type_id, order_type FROM dim_order_type ORDER BY order_type";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    echo "<option value=''>Select Order Type</option>";
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<option value='" . htmlspecialchars($row['order_type_id']) . "'>" . htmlspecialchars($row['order_type']) . "</option>";
    }
} catch(PDOException $e) {
    echo "<option value=''>Error loading order types</option>";
}
?>
