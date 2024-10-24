<?php
require_once '../config.php';

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->connect();
    
    $query = "SELECT ingrediente_id, nombre FROM ingrediente WHERE is_active = 1 ORDER BY nombre";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    echo "<option value=''>Select Ingredient</option>";
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<option value='" . htmlspecialchars($row['ingrediente_id']) . "'>" . htmlspecialchars($row['nombre']) . "</option>";
    }
} catch(PDOException $e) {
    echo "<option value=''>Error loading ingredients</option>";
}
?>
